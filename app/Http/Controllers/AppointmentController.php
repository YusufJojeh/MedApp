<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of appointments
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'patients.NAME as patient_name',
                'specialties.name_en as specialty_name'
            );

        // Apply role-based filtering
        if ($user->role === 'doctor') {
            $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
            if ($doctor) {
                $query->where('appointments.doctor_id', $doctor->id);
            }
        } elseif ($user->role === 'patient') {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('appointments.patient_id', $patient->id);
            }
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('appointments.STATUS', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('appointments.appointment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('appointments.appointment_date', '<=', $request->date_to);
        }

        if ($request->filled('doctor_id')) {
            $query->where('appointments.doctor_id', $request->doctor_id);
        }

        if ($request->filled('specialty_id')) {
            $query->where('doctors.specialty_id', $request->specialty_id);
        }

        $appointments = $query->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->paginate(15);

        $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $specialties = DB::table('specialties')->select('id', 'name_en')->get();

        return view('appointments.index', compact('appointments', 'statuses', 'specialties'));
    }

    /**
     * Show the form for creating a new appointment
     */
    public function create()
    {
        $doctors = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('doctors.is_active', true)
            ->select('doctors.*', 'specialties.name_en as specialty_name')
            ->get();

        $patients = DB::table('patients')
            ->join('users', 'patients.user_id', '=', 'users.id')
            ->select('patients.*', 'users.first_name', 'users.last_name')
            ->get();

        return view('appointments.create', compact('doctors', 'patients'));
    }

    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i:s',
            'STATUS' => 'required|in:scheduled,confirmed,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check for scheduling conflicts
        $conflict = DB::table('appointments')
            ->where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('STATUS', '!=', 'cancelled')
            ->exists();

        if ($conflict) {
            return response()->json(['error' => 'Time slot is already booked for this doctor'], 400);
        }

        // Check doctor availability
        $dayOfWeek = Carbon::parse($request->appointment_date)->dayOfWeek;
        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $request->doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$workingHours) {
            return response()->json(['error' => 'Doctor is not available on this day'], 400);
        }

        if ($request->appointment_time < $workingHours->start_time || $request->appointment_time > $workingHours->end_time) {
            return response()->json(['error' => 'Appointment time is outside doctor\'s working hours'], 400);
        }

        DB::beginTransaction();
        try {
            $appointmentId = DB::table('appointments')->insertGetId([
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'STATUS' => $request->STATUS,
                'notes' => $request->notes,
                'consultation_fee' => $request->consultation_fee,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create payment record if consultation fee is provided
            if ($request->consultation_fee && $request->consultation_fee > 0) {
                DB::table('payments')->insert([
                    'appointment_id' => $appointmentId,
                    'amount' => $request->consultation_fee,
                    'payment_method' => 'pending',
                    'STATUS' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully',
                'appointment_id' => $appointmentId
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified appointment
     */
    public function show($id)
    {
        $user = Auth::user();
        $query = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.id', $id)
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'doctors.consultation_fee',
                'patients.NAME as patient_name',
                'specialties.name_en as specialty_name'
            );

        // Apply role-based access
        if ($user->role === 'doctor') {
            $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
            if ($doctor) {
                $query->where('appointments.doctor_id', $doctor->id);
            }
        } elseif ($user->role === 'patient') {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('appointments.patient_id', $patient->id);
            }
        }

        $appointment = $query->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        // Get payment information
        $payment = DB::table('payments')
            ->where('appointment_id', $id)
            ->first();

        return view('appointments.show', compact('appointment', 'payment'));
    }

    /**
     * Show the form for editing the specified appointment
     */
    public function edit($id)
    {
        $user = Auth::user();
        $query = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.id', $id)
            ->select('appointments.*', 'doctors.name as doctor_name', 'patients.NAME as patient_name');

        // Apply role-based access
        if ($user->role === 'doctor') {
            $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
            if ($doctor) {
                $query->where('appointments.doctor_id', $doctor->id);
            }
        } elseif ($user->role === 'patient') {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('appointments.patient_id', $patient->id);
            }
        }

        $appointment = $query->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        $doctors = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('doctors.is_active', true)
            ->select('doctors.*', 'specialties.name_en as specialty_name')
            ->get();

        $patients = DB::table('patients')
            ->join('users', 'patients.user_id', '=', 'users.id')
            ->select('patients.*', 'users.first_name', 'users.last_name')
            ->get();

        return view('appointments.edit', compact('appointment', 'doctors', 'patients'));
    }

    /**
     * Update the specified appointment
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i:s',
            'STATUS' => 'required|in:scheduled,confirmed,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $query = DB::table('appointments')->where('id', $id);

        // Apply role-based access
        if ($user->role === 'doctor') {
            $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
            if ($doctor) {
                $query->where('doctor_id', $doctor->id);
            }
        } elseif ($user->role === 'patient') {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('patient_id', $patient->id);
            }
        }

        $appointment = $query->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        // Check for scheduling conflicts (excluding current appointment)
        $conflict = DB::table('appointments')
            ->where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('STATUS', '!=', 'cancelled')
            ->where('id', '!=', $id)
            ->exists();

        if ($conflict) {
            return response()->json(['error' => 'Time slot is already booked for this doctor'], 400);
        }

        DB::beginTransaction();
        try {
            DB::table('appointments')->where('id', $id)->update([
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'STATUS' => $request->STATUS,
                'notes' => $request->notes,
                'consultation_fee' => $request->consultation_fee,
                'updated_at' => now(),
            ]);

            // Update payment if consultation fee changed
            if ($request->consultation_fee && $request->consultation_fee > 0) {
                $payment = DB::table('payments')->where('appointment_id', $id)->first();

                if ($payment) {
                    DB::table('payments')->where('id', $payment->id)->update([
                        'amount' => $request->consultation_fee,
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('payments')->insert([
                        'appointment_id' => $id,
                        'amount' => $request->consultation_fee,
                        'payment_method' => 'pending',
                        'STATUS' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified appointment
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $query = DB::table('appointments')->where('id', $id);

        // Apply role-based access
        if ($user->role === 'doctor') {
            $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
            if ($doctor) {
                $query->where('doctor_id', $doctor->id);
            }
        } elseif ($user->role === 'patient') {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('patient_id', $patient->id);
            }
        }

        $appointment = $query->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Cancel the appointment instead of deleting
            DB::table('appointments')->where('id', $id)->update([
                'STATUS' => 'cancelled',
                'updated_at' => now(),
            ]);

            // Update payment status if exists
            DB::table('payments')
                ->where('appointment_id', $id)
                ->update([
                    'STATUS' => 'cancelled',
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available time slots for a doctor
     */
    public function getAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctorId = $request->doctor_id;
        $date = $request->date;
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        // Get doctor's working hours for this day
        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$workingHours) {
            return response()->json(['slots' => []]);
        }

        // Get booked appointments for this date
        $bookedSlots = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('STATUS', '!=', 'cancelled')
            ->pluck('appointment_time')
            ->toArray();

        // Generate available time slots
        $slots = [];
        $startTime = Carbon::parse($workingHours->start_time);
        $endTime = Carbon::parse($workingHours->end_time);
        $interval = 30; // 30 minutes interval

        while ($startTime < $endTime) {
            $timeSlot = $startTime->format('H:i:s');

            if (!in_array($timeSlot, $bookedSlots)) {
                $slots[] = [
                    'time' => $timeSlot,
                    'display_time' => $startTime->format('g:i A'),
                    'available' => true
                ];
            } else {
                $slots[] = [
                    'time' => $timeSlot,
                    'display_time' => $startTime->format('g:i A'),
                    'available' => false
                ];
            }

            $startTime->addMinutes($interval);
        }

        return response()->json(['slots' => $slots]);
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:scheduled,confirmed,completed,cancelled,no_show',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $query = DB::table('appointments')->where('id', $id);

        // Apply role-based access
        if ($user->role === 'doctor') {
            $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
            if ($doctor) {
                $query->where('doctor_id', $doctor->id);
            }
        } elseif ($user->role === 'patient') {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('patient_id', $patient->id);
            }
        }

        $appointment = $query->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        DB::beginTransaction();
        try {
            DB::table('appointments')->where('id', $id)->update([
                'STATUS' => $request->status,
                'notes' => $request->notes ?? $appointment->notes,
                'updated_at' => now(),
            ]);

            // Update payment status if appointment is completed
            if ($request->status === 'completed') {
                DB::table('payments')
                    ->where('appointment_id', $id)
                    ->update([
                        'STATUS' => 'succeeded',
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment status updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating appointment status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get appointment statistics
     */
    public function getStats()
    {
        $user = Auth::user();
        $query = DB::table('appointments');

        // Apply role-based filtering
        if ($user->role === 'doctor') {
            $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
            if ($doctor) {
                $query->where('doctor_id', $doctor->id);
            }
        } elseif ($user->role === 'patient') {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('patient_id', $patient->id);
            }
        }

        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $stats = [
            'total_appointments' => $query->count(),
            'today_appointments' => $query->whereDate('appointment_date', $currentDate->toDateString())->count(),
            'this_month_appointments' => $query->whereMonth('appointment_date', $currentDate->month)
                ->whereYear('appointment_date', $currentDate->year)->count(),
            'last_month_appointments' => $query->whereMonth('appointment_date', $lastMonth->month)
                ->whereYear('appointment_date', $lastMonth->year)->count(),
            'completed_appointments' => $query->where('STATUS', 'completed')->count(),
            'cancelled_appointments' => $query->where('STATUS', 'cancelled')->count(),
            'pending_appointments' => $query->whereIn('STATUS', ['scheduled', 'confirmed'])->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Export appointments
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $query = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'patients.NAME as patient_name',
                'specialties.name_en as specialty_name'
            );

        // Apply role-based filtering
        if ($user->role === 'doctor') {
            $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
            if ($doctor) {
                $query->where('appointments.doctor_id', $doctor->id);
            }
        } elseif ($user->role === 'patient') {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('appointments.patient_id', $patient->id);
            }
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('appointments.STATUS', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('appointments.appointment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('appointments.appointment_date', '<=', $request->date_to);
        }

        $appointments = $query->orderBy('appointments.appointment_date', 'desc')->get();

        return response()->json($appointments);
    }
}
