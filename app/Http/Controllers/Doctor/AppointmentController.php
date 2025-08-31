<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Traits\ExportTrait;

class AppointmentController extends Controller
{
    use ExportTrait;
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    /**
     * Display a listing of appointments
     */
    public function index(Request $request)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $query = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone',
                'patients.email as patient_email'
            );

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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patients.NAME', 'like', "%{$search}%")
                  ->orWhere('patients.phone', 'like', "%{$search}%")
                  ->orWhere('patients.email', 'like', "%{$search}%");
            });
        }

        $appointments = $query->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->paginate(15);

        // Calculate statistics
        $stats = [
            'total_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->count(),

            'today_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', today())
                ->count(),

            'upcoming_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('appointment_date', '>', today())
                ->whereIn('STATUS', ['scheduled', 'confirmed'])
                ->count(),

            'total_patients' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->distinct('patient_id')
                ->count('patient_id')
        ];

        $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'];

        return view('doctor.appointments.index', compact('appointments', 'statuses', 'stats'));
    }

    /**
     * Display the specified appointment
     */
    public function show($id)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $appointment = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.id', $id)
            ->where('appointments.doctor_id', $doctor->id)
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone',
                'patients.email as patient_email',
                'patients.date_of_birth',
                'patients.gender',
                'patients.blood_type',
                'patients.address',
                'patients.medical_history',
                'patients.emergency_contact'
            )
            ->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        // Get related payment
        $payment = DB::table('payments')
            ->where('appointment_id', $id)
            ->first();

        // Get patient's appointment history with this doctor
        $patientHistory = DB::table('appointments')
            ->where('appointments.patient_id', $appointment->patient_id)
            ->where('appointments.doctor_id', $doctor->id)
            ->where('appointments.id', '!=', $id)
            ->orderBy('appointments.appointment_date', 'desc')
            ->limit(5)
            ->get();

        // If it's an AJAX request, return modal content
        if (request()->ajax()) {
            return view('doctor.appointments.show', compact('appointment', 'payment', 'patientHistory'));
        }

        // For direct page access, return full page layout
        return view('doctor.appointments.show-full', compact('appointment', 'payment', 'patientHistory'));
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:scheduled,confirmed,completed,cancelled,no_show',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $appointment = DB::table('appointments')
            ->where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->first();

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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment status updated successfully'
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
     * Get today's appointments
     */
    public function today()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $currentDate = Carbon::now();

        $appointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->whereDate('appointments.appointment_date', $currentDate->toDateString())
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone',
                'patients.email as patient_email'
            )
            ->orderBy('appointments.appointment_time', 'asc')
            ->get();

        // Group appointments by time periods
        $morningAppointments = $appointments->filter(function ($appointment) {
            $time = Carbon::parse($appointment->appointment_time);
            return $time->hour >= 6 && $time->hour < 12;
        });

        $afternoonAppointments = $appointments->filter(function ($appointment) {
            $time = Carbon::parse($appointment->appointment_time);
            return $time->hour >= 12 && $time->hour < 17;
        });

        $eveningAppointments = $appointments->filter(function ($appointment) {
            $time = Carbon::parse($appointment->appointment_time);
            return $time->hour >= 17 || $time->hour < 6;
        });

        // Calculate statistics for today's appointments
        $stats = [
            'total_today' => $appointments->count(),
            'completed' => $appointments->where('STATUS', 'completed')->count(),
            'pending' => $appointments->whereIn('STATUS', ['scheduled', 'confirmed'])->count(),
            'cancelled' => $appointments->where('STATUS', 'cancelled')->count()
        ];

        return view('doctor.appointments.today', compact('morningAppointments', 'afternoonAppointments', 'eveningAppointments', 'stats'));
    }

    /**
     * Get upcoming appointments
     */
    public function upcoming()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $currentDate = Carbon::now();

                $appointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->where('appointments.appointment_date', '>=', $currentDate->toDateString())
            ->where('appointments.STATUS', '!=', 'cancelled')
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone',
                'patients.email as patient_email'
            )
            ->orderBy('appointments.appointment_date', 'asc')
            ->orderBy('appointments.appointment_time', 'asc')
            ->get();

        // Group appointments by week
        $appointmentsByWeek = $appointments->groupBy(function ($appointment) {
            $date = Carbon::parse($appointment->appointment_date);
            $startOfWeek = $date->copy()->startOfWeek();
            $endOfWeek = $date->copy()->endOfWeek();
            return $startOfWeek->format('M j') . ' - ' . $endOfWeek->format('M j, Y');
        });

        // Calculate statistics for upcoming appointments
        $stats = [
            'total_upcoming' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('appointment_date', '>=', $currentDate->toDateString())
                ->where('STATUS', '!=', 'cancelled')
                ->count(),

            'confirmed' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('appointment_date', '>=', $currentDate->toDateString())
                ->where('STATUS', 'confirmed')
                ->count(),

            'scheduled' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('appointment_date', '>=', $currentDate->toDateString())
                ->where('STATUS', 'scheduled')
                ->count(),

            'unique_patients' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('appointment_date', '>=', $currentDate->toDateString())
                ->where('STATUS', '!=', 'cancelled')
                ->distinct('patient_id')
                ->count('patient_id')
        ];

        return view('doctor.appointments.upcoming', compact('appointmentsByWeek', 'stats'));
    }

    /**
     * Get past appointments
     */
    public function past()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $currentDate = Carbon::now();

        $appointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->where('appointments.appointment_date', '<', $currentDate->toDateString())
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone',
                'patients.email as patient_email'
            )
            ->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->get();

        // Calculate statistics for past appointments
        $stats = [
            'total_past' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('appointment_date', '<', $currentDate->toDateString())
                ->count(),

            'completed' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('appointment_date', '<', $currentDate->toDateString())
                ->where('STATUS', 'completed')
                ->count(),

            'cancelled' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('appointment_date', '<', $currentDate->toDateString())
                ->where('STATUS', 'cancelled')
                ->count(),

            'no_show' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('appointment_date', '<', $currentDate->toDateString())
                ->where('STATUS', 'no_show')
                ->count(),

            'total_earnings' => DB::table('payments')
                ->where('doctor_id', $doctor->id)
                ->where('STATUS', 'succeeded')
                ->sum('net_amount')
        ];

        return view('doctor.appointments.past', compact('appointments', 'stats'));
    }

    /**
     * Get calendar view
     */
    public function calendar(Request $request)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $appointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->whereBetween('appointments.appointment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone',
                'patients.email as patient_email'
            )
            ->orderBy('appointments.appointment_date', 'asc')
            ->orderBy('appointments.appointment_time', 'asc')
            ->get();

        // Transform appointments for JavaScript compatibility
        $appointments = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'appointment_date' => $appointment->appointment_date,
                'appointment_time' => $appointment->appointment_time,
                'status' => strtolower($appointment->STATUS),
                'notes' => $appointment->notes,
                'patient' => [
                    'name' => $appointment->patient_name,
                    'phone' => $appointment->patient_phone,
                    'email' => $appointment->patient_email
                ],
                'appointment_type' => explode(' - ', $appointment->notes)[0] ?? 'General Consultation',
                'fees' => 200.00
            ];
        });

        return view('doctor.appointments.calendar', compact('appointments', 'month', 'year'));
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $date = $request->date;
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        // Get doctor's working hours for this day
        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $doctor->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$workingHours) {
            return response()->json(['message' => 'Not available on this day']);
        }

        // Get booked appointments for this date
        $bookedSlots = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->where('appointment_date', $date)
            ->where('STATUS', '!=', 'cancelled')
            ->pluck('appointment_time')
            ->toArray();

        // Generate available time slots (30-minute intervals)
        $startTime = Carbon::parse($workingHours->start_time);
        $endTime = Carbon::parse($workingHours->end_time);
        $availableSlots = [];

        while ($startTime < $endTime) {
            $timeSlot = $startTime->format('H:i:s');
            if (!in_array($timeSlot, $bookedSlots)) {
                $availableSlots[] = [
                    'time' => $timeSlot,
                    'formatted_time' => $startTime->format('g:i A')
                ];
            }
            $startTime->addMinutes(30);
        }

        return response()->json($availableSlots);
    }

    /**
     * Get appointment statistics
     */
    public function getStats()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $stats = [
            'total_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->count(),
            'today_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', $currentDate->toDateString())
                ->count(),
            'this_month_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->whereMonth('appointment_date', $currentDate->month)
                ->whereYear('appointment_date', $currentDate->year)
                ->count(),
            'last_month_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->whereMonth('appointment_date', $lastMonth->month)
                ->whereYear('appointment_date', $lastMonth->year)
                ->count(),
            'scheduled_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('STATUS', 'scheduled')
                ->count(),
            'confirmed_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('STATUS', 'confirmed')
                ->count(),
            'completed_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('STATUS', 'completed')
                ->count(),
            'cancelled_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('STATUS', 'cancelled')
                ->count(),
        ];

        // Calculate growth
        $stats['appointment_growth'] = $stats['last_month_appointments'] > 0
            ? (($stats['this_month_appointments'] - $stats['last_month_appointments']) / $stats['last_month_appointments']) * 100
            : 0;

        return response()->json($stats);
    }

    /**
     * Export appointments data as XLSX
     */
    public function export(Request $request)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $type = $request->get('type', 'all'); // all, past, upcoming, today
        $dateRange = $request->get('date_range', 90);
        $status = $request->get('status', '');
        $patient = $request->get('patient', '');

        $query = DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->leftJoin('payments', 'appointments.id', '=', 'payments.appointment_id')
            ->where('appointments.doctor_id', $doctor->id)
            ->select(
                'appointments.id',
                'appointments.appointment_date',
                'appointments.appointment_time',
                'appointments.STATUS',
                'appointments.notes',
                'appointments.created_at',
                'appointments.updated_at',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone',
                'patients.email as patient_email',
                'patients.date_of_birth',
                'patients.gender',
                'patients.blood_type',
                'patients.emergency_contact',
                'patients.medical_history',
                'payments.amount as payment_amount',
                'payments.STATUS as payment_status',
                DB::raw('JSON_EXTRACT(payments.meta, "$.payment_method") as payment_method'),
                'payments.created_at as payment_date'
            );

        // Apply type filters
        $currentDate = Carbon::now();
        switch ($type) {
            case 'past':
                $query->where('appointments.appointment_date', '<', $currentDate->toDateString());
                break;
            case 'upcoming':
                $query->where('appointments.appointment_date', '>=', $currentDate->toDateString());
                break;
            case 'today':
                $query->whereDate('appointments.appointment_date', $currentDate->toDateString());
                break;
            default:
                // All appointments, apply date range filter
                if ($dateRange) {
                    $query->where('appointments.appointment_date', '>=', $currentDate->subDays($dateRange)->toDateString());
                }
                break;
        }

        // Apply status filter
        if ($status) {
            $query->where('appointments.STATUS', $status);
        }

        // Apply patient filter
        if ($patient) {
            $query->where('patients.NAME', 'like', '%' . $patient . '%');
        }

        $appointments = $query->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->get();

        // Generate filename
        $filename = 'appointments_' . $type . '_' . str_replace([' ', '/', '\\'], ['_', '-', '-'], $doctor->name) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Create CSV file (more reliable than XLSX)
        return $this->generateCSV($appointments, $filename, $doctor, $type);
    }

    /**
     * Generate CSV file using trait
     */
    private function generateCSV($appointments, $filename, $doctor, $type)
    {
        // Change filename to CSV
        $filename = str_replace('.xlsx', '.csv', $filename);

        // Headers
        $headers = [
            'Appointment ID',
            'Patient Name',
            'Patient Phone',
            'Patient Email',
            'Date of Birth',
            'Gender',
            'Blood Type',
            'Emergency Contact',
            'Medical History',
            'Appointment Date',
            'Appointment Time',
            'Status',
            'Appointment Type',
            'Notes',
            'Payment Amount',
            'Payment Status',
            'Payment Method',
            'Payment Date',
            'Created At',
            'Updated At'
        ];

        // Prepare data for CSV
        $csvData = [];
        foreach ($appointments as $appointment) {
            $csvData[] = [
                $appointment->id,
                $appointment->patient_name,
                $appointment->patient_phone,
                $appointment->patient_email,
                $this->formatDate($appointment->date_of_birth),
                $appointment->gender,
                $appointment->blood_type,
                $appointment->emergency_contact,
                $appointment->medical_history,
                $this->formatDate($appointment->appointment_date),
                $this->formatDateTime($appointment->appointment_time, 'g:i A'),
                $this->formatStatus($appointment->STATUS),
                $this->getAppointmentType($appointment->notes),
                $appointment->notes,
                $this->formatCurrency($appointment->payment_amount),
                $this->formatStatus($appointment->payment_status),
                str_replace('"', '', $appointment->payment_method),
                $this->formatDate($appointment->payment_date),
                $this->formatDateTime($appointment->created_at),
                $this->formatDateTime($appointment->updated_at)
            ];
        }

        // Get summary
        $summary = $this->getExportSummary($appointments, $type, $doctor->name);
        $summary['Doctor Name'] = $doctor->name;
        $summary['Completed Appointments'] = $appointments->where('STATUS', 'completed')->count();
        $summary['Cancelled Appointments'] = $appointments->where('STATUS', 'cancelled')->count();
        $summary['Total Revenue'] = $this->formatCurrency($appointments->where('payment_status', 'succeeded')->sum('payment_amount'));

        return $this->generateCSV($csvData, $filename, $headers, $summary);
    }



    /**
     * Get patient appointment history
     */
    public function getPatientHistory($patientId)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        // Get comprehensive patient information
        $patient = DB::table('patients')
            ->where('id', $patientId)
            ->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        // Get all appointments with payment information
        $appointments = DB::table('appointments')
            ->leftJoin('payments', 'appointments.id', '=', 'payments.appointment_id')
            ->where('appointments.doctor_id', $doctor->id)
            ->where('appointments.patient_id', $patientId)
            ->select(
                'appointments.*',
                'payments.amount as payment_amount',
                'payments.STATUS as payment_status',
                DB::raw('JSON_EXTRACT(payments.meta, "$.payment_method") as payment_method'),
                'payments.created_at as payment_date'
            )
            ->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->get();

        // Calculate patient statistics
        $stats = [
            'total_appointments' => $appointments->count(),
            'completed_appointments' => $appointments->where('STATUS', 'completed')->count(),
            'cancelled_appointments' => $appointments->where('STATUS', 'cancelled')->count(),
            'no_show_appointments' => $appointments->where('STATUS', 'no_show')->count(),
            'total_paid' => $appointments->where('payment_status', 'succeeded')->sum('payment_amount'),
            'pending_payments' => $appointments->where('payment_status', 'pending')->count(),
            'first_visit' => $appointments->last() ? Carbon::parse($appointments->last()->appointment_date)->format('M j, Y') : 'N/A',
            'last_visit' => $appointments->first() ? Carbon::parse($appointments->first()->appointment_date)->format('M j, Y') : 'N/A'
        ];

        return view('doctor.appointments.patient-history', compact('appointments', 'patient', 'stats'));
    }

    /**
     * Add appointment notes
     */
    public function addNotes(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $appointment = DB::table('appointments')
            ->where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        DB::table('appointments')->where('id', $id)->update([
            'notes' => $request->notes,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notes added successfully'
        ]);
    }
}
