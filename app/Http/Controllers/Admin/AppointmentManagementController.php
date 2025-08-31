<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Traits\ExportTrait;

class AppointmentManagementController extends Controller
{
    use ExportTrait;
    /**
     * Display a listing of all appointments
     */
    public function index(Request $request)
    {
        $query = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone',
                'patients.email as patient_email',
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name'
            );

        // Apply filters
        if ($request->filled('status')) {
            $query->where('appointments.STATUS', $request->status);
        }

        if ($request->filled('doctor')) {
            $query->where('appointments.doctor_id', $request->doctor);
        }

        if ($request->filled('specialty')) {
            $query->where('doctors.specialty_id', $request->specialty);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('appointments.appointment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('appointments.appointment_date', '<=', $request->date_to);
        }

        // Handle date range filter
        if ($request->filled('date_range')) {
            $today = Carbon::now();
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('appointments.appointment_date', $today->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('appointments.appointment_date', [
                        $today->startOfWeek()->toDateString(),
                        $today->endOfWeek()->toDateString()
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('appointments.appointment_date', $today->month)
                          ->whereYear('appointments.appointment_date', $today->year);
                    break;
                case 'year':
                    $query->whereYear('appointments.appointment_date', $today->year);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patients.NAME', 'like', "%{$search}%")
                  ->orWhere('doctors.name', 'like', "%{$search}%")
                  ->orWhere('specialties.name_en', 'like', "%{$search}%");
            });
        }

        $appointments = $query->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->paginate(15);

        // Add computed properties to appointments
        $appointments->getCollection()->transform(function ($appointment) {
            // Add status badge class
            $appointment->status_badge_class = $this->getStatusBadgeClass($appointment->STATUS);

            // Add payment status
            $appointment->payment_status = $this->getPaymentStatus($appointment->id);

            return $appointment;
        });

        $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $doctors = DB::table('doctors')->select('id', 'name')->get();
        $specialties = DB::table('specialties')->select('id', 'name_en')->get();

        // Calculate statistics
        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $stats = [
            'total_appointments' => DB::table('appointments')->count(),
            'completed_appointments' => DB::table('appointments')
                ->where('STATUS', 'completed')
                ->count(),
            'pending_appointments' => DB::table('appointments')
                ->whereIn('STATUS', ['scheduled', 'confirmed'])
                ->count(),
            'total_revenue' => DB::table('payments')
                ->where('STATUS', 'succeeded')
                ->sum('amount'),
            'this_month_appointments' => DB::table('appointments')
                ->whereMonth('appointment_date', $currentDate->month)
                ->whereYear('appointment_date', $currentDate->year)
                ->count(),
            'last_month_appointments' => DB::table('appointments')
                ->whereMonth('appointment_date', $lastMonth->month)
                ->whereYear('appointment_date', $lastMonth->year)
                ->count(),
        ];

        // Calculate growth
        $stats['appointment_growth'] = $stats['last_month_appointments'] > 0
            ? (($stats['this_month_appointments'] - $stats['last_month_appointments']) / $stats['last_month_appointments']) * 100
            : 0;

        return view('admin.appointments.index', compact('appointments', 'statuses', 'doctors', 'specialties', 'stats'));
    }

    /**
     * Show the form for creating a new appointment
     */
    public function create()
    {
        $patients = DB::table('patients')->select('id', 'NAME', 'phone', 'email')->get();
        $doctors = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->select('doctors.id', 'doctors.name', 'specialties.name_en as specialty_name')
            ->where('doctors.is_active', true)
            ->get();
        $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'];

        return view('admin.appointments.create', compact('patients', 'doctors', 'statuses'));
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
            return response()->json([
                'error' => 'Time slot is already booked for this doctor'
            ], 400);
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);

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
        $appointment = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
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
                'patients.emergency_contact',
                'doctors.name as doctor_name',
                'doctors.consultation_fee',
                'doctors.experience_years',
                'doctors.education',
                'doctors.languages',
                'doctors.description',
                'specialties.name_en as specialty_name'
            )
            ->where('appointments.id', $id)
            ->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        // Get related payment
        $payment = DB::table('payments')
            ->where('appointment_id', $id)
            ->first();

        // Get patient's appointment history
        $patientHistory = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $appointment->patient_id)
            ->where('appointments.id', '!=', $id)
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name'
            )
            ->orderBy('appointments.appointment_date', 'desc')
            ->limit(5)
            ->get();

        return view('admin.appointments.show', compact('appointment', 'payment', 'patientHistory'));
    }

    /**
     * Show the form for editing the specified appointment
     */
    public function edit($id)
    {
        $appointment = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'doctors.name as doctor_name'
            )
            ->where('appointments.id', $id)
            ->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        $patients = DB::table('patients')->select('id', 'NAME', 'phone', 'email')->get();
        $doctors = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->select('doctors.id', 'doctors.name', 'specialties.name_en as specialty_name')
            ->where('doctors.is_active', true)
            ->get();
        $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'];

        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors', 'statuses'));
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $appointment = DB::table('appointments')->where('id', $id)->first();

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
            return response()->json([
                'error' => 'Time slot is already booked for this doctor'
            ], 400);
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
                'updated_at' => now(),
            ]);

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
        $appointment = DB::table('appointments')->where('id', $id)->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Delete related payment if exists
            DB::table('payments')->where('appointment_id', $id)->delete();

            // Delete appointment
            DB::table('appointments')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions on appointments
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:confirm,complete,cancel,delete',
            'appointment_ids' => 'required|array',
            'appointment_ids.*' => 'exists:appointments,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $appointmentIds = $request->appointment_ids;
        $action = $request->action;

        DB::beginTransaction();
        try {
            switch ($action) {
                case 'confirm':
                    DB::table('appointments')->whereIn('id', $appointmentIds)->update([
                        'STATUS' => 'confirmed',
                        'updated_at' => now()
                    ]);
                    break;

                case 'complete':
                    DB::table('appointments')->whereIn('id', $appointmentIds)->update([
                        'STATUS' => 'completed',
                        'updated_at' => now()
                    ]);
                    break;

                case 'cancel':
                    DB::table('appointments')->whereIn('id', $appointmentIds)->update([
                        'STATUS' => 'cancelled',
                        'updated_at' => now()
                    ]);
                    break;

                case 'delete':
                    // Delete related payments first
                    DB::table('payments')->whereIn('appointment_id', $appointmentIds)->delete();

                    // Delete appointments
                    DB::table('appointments')->whereIn('id', $appointmentIds)->delete();
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bulk action completed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error performing bulk action: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get appointment statistics
     */
    public function getStats()
    {
        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $stats = [
            'total_appointments' => DB::table('appointments')->count(),
            'today_appointments' => DB::table('appointments')
                ->whereDate('appointment_date', $currentDate->toDateString())
                ->count(),
            'this_month_appointments' => DB::table('appointments')
                ->whereMonth('appointment_date', $currentDate->month)
                ->whereYear('appointment_date', $currentDate->year)
                ->count(),
            'last_month_appointments' => DB::table('appointments')
                ->whereMonth('appointment_date', $lastMonth->month)
                ->whereYear('appointment_date', $lastMonth->year)
                ->count(),
            'scheduled_appointments' => DB::table('appointments')
                ->where('STATUS', 'scheduled')
                ->count(),
            'confirmed_appointments' => DB::table('appointments')
                ->where('STATUS', 'confirmed')
                ->count(),
            'completed_appointments' => DB::table('appointments')
                ->where('STATUS', 'completed')
                ->count(),
            'cancelled_appointments' => DB::table('appointments')
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
     * Get appointment chart data
     */
    public function getChartData()
    {
        $currentDate = Carbon::now();

        // Last 7 days appointments
        $dailyAppointments = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = DB::table('appointments')
                ->whereDate('appointment_date', $date->toDateString())
                ->count();

            $dailyAppointments[] = [
                'date' => $date->format('M d'),
                'count' => $count
            ];
        }

        // Appointment status distribution
        $statusDistribution = DB::table('appointments')
            ->select('STATUS', DB::raw('COUNT(*) as count'))
            ->groupBy('STATUS')
            ->get();

        // Top doctors by appointments
        $topDoctors = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->select('doctors.name', DB::raw('COUNT(appointments.id) as appointment_count'))
            ->groupBy('doctors.id', 'doctors.name')
            ->orderBy('appointment_count', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'daily_appointments' => $dailyAppointments,
            'status_distribution' => $statusDistribution,
            'top_doctors' => $topDoctors
        ]);
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
            return response()->json(['message' => 'Doctor not available on this day']);
        }

        // Get booked appointments for this date
        $bookedSlots = DB::table('appointments')
            ->where('doctor_id', $doctorId)
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
     * Export appointments data as CSV
     */
    public function export(Request $request)
    {
        $query = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->leftJoin('payments', 'appointments.id', '=', 'payments.appointment_id')
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
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name',
                'specialties.name_ar as specialty_name_ar',
                'payments.amount as payment_amount',
                'payments.STATUS as payment_status',
                DB::raw('JSON_EXTRACT(payments.meta, "$.payment_method") as payment_method'),
                'payments.created_at as payment_date'
            );

        // Apply filters
        if ($request->filled('status')) {
            $query->where('appointments.STATUS', $request->status);
        }

        if ($request->filled('doctor_id')) {
            $query->where('appointments.doctor_id', $request->doctor_id);
        }

        if ($request->filled('specialty')) {
            $query->where('doctors.specialty_id', $request->specialty);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('appointments.appointment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('appointments.appointment_date', '<=', $request->date_to);
        }

        // Handle date range filter
        if ($request->filled('date_range')) {
            $today = Carbon::now();
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('appointments.appointment_date', $today->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('appointments.appointment_date', [
                        $today->startOfWeek()->toDateString(),
                        $today->endOfWeek()->toDateString()
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('appointments.appointment_date', $today->month)
                          ->whereYear('appointments.appointment_date', $today->year);
                    break;
                case 'year':
                    $query->whereYear('appointments.appointment_date', $today->year);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patients.NAME', 'like', "%{$search}%")
                  ->orWhere('doctors.name', 'like', "%{$search}%")
                  ->orWhere('specialties.name_en', 'like', "%{$search}%");
            });
        }

        $appointments = $query->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->get();

        // Prepare CSV data
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
                $appointment->doctor_name,
                $appointment->specialty_name,
                $appointment->specialty_name_ar,
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
            'Doctor Name',
            'Specialty (English)',
            'Specialty (Arabic)',
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

        // Generate filename
        $filename = $this->generateExportFilename('appointments', 'admin_management');

        // Get summary
        $summary = $this->getExportSummary($appointments, 'appointments', 'Admin');
        $summary['Completed Appointments'] = $appointments->where('STATUS', 'completed')->count();
        $summary['Cancelled Appointments'] = $appointments->where('STATUS', 'cancelled')->count();
        $summary['No Show Appointments'] = $appointments->where('STATUS', 'no_show')->count();
        $summary['Total Revenue'] = $this->formatCurrency($appointments->where('payment_status', 'succeeded')->sum('payment_amount'));

        return $this->generateCSV($csvData, $filename, $headers, $summary);
    }

    /**
     * Get status badge class for appointment status
     */
    private function getStatusBadgeClass($status)
    {
        switch ($status) {
            case 'scheduled':
                return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
            case 'confirmed':
                return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
            case 'completed':
                return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300';
            case 'cancelled':
                return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
            case 'no_show':
                return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
            default:
                return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
        }
    }

    /**
     * Get payment status for appointment
     */
    private function getPaymentStatus($appointmentId)
    {
        $payment = DB::table('payments')
            ->where('appointment_id', $appointmentId)
            ->first();

        if (!$payment) {
            return 'Pending';
        }

        switch ($payment->STATUS) {
            case 'succeeded':
                return 'Paid';
            case 'pending':
                return 'Pending';
            case 'failed':
                return 'Failed';
            case 'refunded':
                return 'Refunded';
            default:
                return 'Unknown';
        }
    }
}
