<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the patient's appointments
     */
    public function index(Request $request)
    {
        // Get authenticated user's patient record
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patient.profile.create')
                ->with('error', 'Please complete your patient profile first.');
        }

        // Build query with filters
        $query = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'doctors.consultation_fee',
                'specialties.name_en as specialty_name'
            );

        // Apply filters
        if ($request->filled('status')) {
            $query->where('appointments.STATUS', $request->status);
        }

        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('appointments.appointment_date', Carbon::today());
                    break;
                case 'week':
                    $query->whereBetween('appointments.appointment_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('appointments.appointment_date', Carbon::now()->month);
                    break;
            }
        }

        if ($request->filled('doctor')) {
            $query->where('doctors.name', 'like', '%' . $request->doctor . '%');
        }

        // Get paginated results
        $appointments = $query->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->paginate(10);

        // Get filter options
        $doctors = DB::table('doctors')
            ->where('is_active', true)
            ->pluck('name', 'id');

        return view('patient.appointments.index', compact('appointments', 'doctors'));
    }

    /**
     * Show the form for creating a new appointment
     */
    public function create(Request $request)
    {
        // Get authenticated user's patient record
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patient.profile.create')
                ->with('error', 'Please complete your patient profile first.');
        }

        // Handle AJAX request for available slots
        if ($request->has('doctor_id') && $request->has('date')) {
            return $this->getAvailableSlots($request);
        }

        // Handle reschedule parameter
        $rescheduleAppointment = null;
        if ($request->has('reschedule')) {
            $rescheduleAppointment = DB::table('appointments')
                ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
                ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
                ->where('appointments.id', $request->reschedule)
                ->where('appointments.patient_id', $patient->id)
                ->select(
                    'appointments.*',
                    'doctors.name as doctor_name',
                    'doctors.specialty_id',
                    'specialties.name_en as specialty_name'
                )
                ->first();

            if (!$rescheduleAppointment) {
                return redirect()->route('patient.appointments.index')
                    ->with('error', 'Appointment not found or you do not have permission to reschedule it.');
            }
        }

        // Handle followup parameter
        $followupAppointment = null;
        if ($request->has('followup')) {
            $followupAppointment = DB::table('appointments')
                ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
                ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
                ->where('appointments.id', $request->followup)
                ->where('appointments.patient_id', $patient->id)
                ->select(
                    'appointments.*',
                    'doctors.name as doctor_name',
                    'doctors.specialty_id',
                    'specialties.name_en as specialty_name'
                )
                ->first();

            if (!$followupAppointment) {
                return redirect()->route('patient.appointments.index')
                    ->with('error', 'Appointment not found or you do not have permission to book follow-up.');
            }
        }

        // Get specialties and doctors
        $specialties = DB::table('specialties')->get();
        $doctors = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('doctors.is_active', true)
            ->select('doctors.*', 'specialties.name_en as specialty_name')
            ->get();

        // Set appointment type based on context
        $appointmentType = null;
        if ($rescheduleAppointment) {
            $appointmentType = 'consultation'; // Default for reschedule
        } elseif ($followupAppointment) {
            $appointmentType = 'follow_up';
        }

        return view('patient.appointments.create', compact(
            'specialties',
            'doctors',
            'rescheduleAppointment',
            'followupAppointment',
            'appointmentType'
        ));
    }

    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i:s',
            'notes' => 'nullable|string|max:1000',
            'reschedule_appointment_id' => 'nullable|exists:appointments,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if slot is available
        $isSlotAvailable = !DB::table('appointments')
            ->where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('STATUS', '!=', 'cancelled')
            ->exists();

        if (!$isSlotAvailable) {
            return response()->json(['error' => 'This time slot is not available'], 422);
        }

        // If rescheduling, cancel the old appointment
        if ($request->filled('reschedule_appointment_id')) {
            DB::table('appointments')
                ->where('id', $request->reschedule_appointment_id)
                ->where('patient_id', $patient->id)
                ->update(['STATUS' => 'cancelled']);
        }

        // Create new appointment
        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'STATUS' => 'scheduled',
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Send notification
        $notificationService = app(\App\Services\NotificationService::class);
        $notificationService->appointmentBooked($appointmentId);

        return response()->json([
            'success' => true,
            'message' => $request->filled('reschedule_appointment_id') ? 'Appointment rescheduled successfully' : 'Appointment booked successfully',
            'appointment_id' => $appointmentId
        ]);
    }

    /**
     * Display the specified appointment
     */
    public function show($id)
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $appointment = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.id', $id)
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'doctors.consultation_fee',
                'specialties.name_en as specialty_name'
            )
            ->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        return response()->json(['appointment' => $appointment]);
    }

    /**
     * Cancel an appointment
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $appointment = DB::table('appointments')
            ->where('id', $id)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        if ($appointment->STATUS === 'cancelled') {
            return response()->json(['error' => 'Appointment is already cancelled'], 422);
        }

        DB::table('appointments')
            ->where('id', $id)
            ->update(['STATUS' => 'cancelled']);

        // Send notification
        $notificationService = app(\App\Services\NotificationService::class);
        $notificationService->appointmentCancelled($id, 'patient');

        return response()->json(['success' => true, 'message' => 'Appointment cancelled successfully']);
    }

    /**
     * Get available time slots for a doctor on a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dayOfWeek = Carbon::parse($request->date)->dayOfWeek;

        // Get doctor's working hours for this day
        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $request->doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$workingHours) {
            return response()->json(['slots' => []]);
        }

        // Generate time slots
        $startTime = Carbon::parse($workingHours->start_time);
        $endTime = Carbon::parse($workingHours->end_time);
        $slots = [];

        while ($startTime < $endTime) {
            $timeSlot = $startTime->format('H:i:s');

            // Check if slot is available
            $isBooked = DB::table('appointments')
                ->where('doctor_id', $request->doctor_id)
                ->where('appointment_date', $request->date)
                ->where('appointment_time', $timeSlot)
                ->where('STATUS', '!=', 'cancelled')
                ->exists();

            if (!$isBooked) {
                $slots[] = [
                    'time' => $timeSlot,
                    'formatted_time' => $startTime->format('g:i A'),
                    'available' => true
                ];
            }

            $startTime->addMinutes(30);
        }

        return response()->json(['slots' => $slots]);
    }

    /**
     * Get upcoming appointments
     */
    public function upcoming()
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patient.profile.create')
                ->with('error', 'Please complete your patient profile first.');
        }

        $appointments = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $patient->id)
            ->where('appointments.appointment_date', '>=', Carbon::today())
            ->where('appointments.STATUS', '!=', 'cancelled')
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name'
            )
            ->orderBy('appointments.appointment_date', 'asc')
            ->orderBy('appointments.appointment_time', 'asc')
            ->paginate(10);

        return view('patient.appointments.upcoming', compact('appointments'));
    }

    /**
     * Get past appointments
     */
    public function past()
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patient.profile.create')
                ->with('error', 'Please complete your patient profile first.');
        }

        $appointments = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $patient->id)
            ->where('appointments.appointment_date', '<', Carbon::today())
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name'
            )
            ->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->paginate(10);

        return view('patient.appointments.past', compact('appointments'));
    }
}
