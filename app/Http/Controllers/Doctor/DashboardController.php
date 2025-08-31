<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    /**
     * Display the doctor dashboard with statistics
     */
    public function index()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            // Redirect to profile setup or show error page
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Doctor profile not found'], 404);
            }
            
            // For web requests, redirect to profile setup
            return redirect()->route('doctor.profile.index')->with('error', 'Please complete your doctor profile first.');
        }

        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Appointment statistics
        $totalAppointments = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->count();

        $todayAppointments = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $currentDate->toDateString())
            ->count();

        $thisMonthAppointments = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->whereMonth('appointment_date', $currentDate->month)
            ->whereYear('appointment_date', $currentDate->year)
            ->count();

        $lastMonthAppointments = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->whereMonth('appointment_date', $lastMonth->month)
            ->whereYear('appointment_date', $lastMonth->year)
            ->count();

        // Revenue statistics
        $totalRevenue = DB::table('payments')
            ->where('doctor_id', $doctor->id)
            ->where('STATUS', 'succeeded')
            ->sum('net_amount');

        $thisMonthRevenue = DB::table('payments')
            ->where('doctor_id', $doctor->id)
            ->where('STATUS', 'succeeded')
            ->whereMonth('created_at', $currentDate->month)
            ->whereYear('created_at', $currentDate->year)
            ->sum('net_amount');

        $lastMonthRevenue = DB::table('payments')
            ->where('doctor_id', $doctor->id)
            ->where('STATUS', 'succeeded')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->sum('net_amount');

        // Patient statistics
        $totalPatients = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->distinct('patient_id')
            ->count('patient_id');

        $newPatientsThisMonth = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->whereMonth('created_at', $currentDate->month)
            ->whereYear('created_at', $currentDate->year)
            ->distinct('patient_id')
            ->count('patient_id');

        // Appointment status distribution
        $appointmentStatuses = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->select('STATUS', DB::raw('COUNT(*) as count'))
            ->groupBy('STATUS')
            ->get();

        // Recent appointments
        $recentAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone',
                'patients.email as patient_email'
            )
            ->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->limit(5)
            ->get();

        // Today's appointments
        $todayAppointmentsList = DB::table('appointments')
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

        // Monthly revenue data for chart
        $monthlyRevenue = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = DB::table('payments')
                ->where('doctor_id', $doctor->id)
                ->where('STATUS', 'succeeded')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('net_amount');

            $monthlyRevenue->push([
                'month' => $date->format('M'),
                'revenue' => $revenue
            ]);
        }

        // Calculate growth percentages
        $appointmentGrowth = $lastMonthAppointments > 0
            ? (($thisMonthAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100
            : 0;

        $revenueGrowth = $lastMonthRevenue > 0
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        // Wallet balance
        $wallet = DB::table('wallets')
            ->where('user_id', $doctorId)
            ->first();

        $walletBalance = $wallet ? $wallet->balance : 0.00;

        return view('doctor.dashboard', compact(
            'doctor',
            'totalAppointments',
            'todayAppointments',
            'thisMonthAppointments',
            'lastMonthAppointments',
            'totalRevenue',
            'thisMonthRevenue',
            'lastMonthRevenue',
            'totalPatients',
            'newPatientsThisMonth',
            'appointmentStatuses',
            'recentAppointments',
            'todayAppointmentsList',
            'monthlyRevenue',
            'appointmentGrowth',
            'revenueGrowth',
            'walletBalance'
        ));
    }

    /**
     * Get dashboard statistics for AJAX requests
     */
    public function getStats()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $currentDate = Carbon::now();

        $stats = [
            'total_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->count(),
            'today_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', $currentDate->toDateString())
                ->count(),
            'total_revenue' => DB::table('payments')
                ->where('doctor_id', $doctor->id)
                ->where('STATUS', 'succeeded')
                ->sum('net_amount'),
            'total_patients' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->distinct('patient_id')
                ->count('patient_id'),
            'wallet_balance' => DB::table('wallets')
                ->where('user_id', $doctorId)
                ->value('balance') ?? 0.00,
        ];

        return response()->json($stats);
    }

    /**
     * Get chart data for dashboard
     */
    public function getChartData()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $currentDate = Carbon::now();

        // Last 7 days appointments
        $dailyAppointments = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', $date->toDateString())
                ->count();

            $dailyAppointments[] = [
                'date' => $date->format('M d'),
                'count' => $count
            ];
        }

        // Last 6 months revenue
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = DB::table('payments')
                ->where('doctor_id', $doctor->id)
                ->where('STATUS', 'succeeded')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('net_amount');

            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Appointment status distribution
        $statusDistribution = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->select('STATUS', DB::raw('COUNT(*) as count'))
            ->groupBy('STATUS')
            ->get();

        return response()->json([
            'daily_appointments' => $dailyAppointments,
            'monthly_revenue' => $monthlyRevenue,
            'status_distribution' => $statusDistribution
        ]);
    }

    /**
     * Get today's schedule
     */
    public function getTodaySchedule()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $currentDate = Carbon::now();

        $schedule = DB::table('appointments')
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

        return response()->json($schedule);
    }

    /**
     * Get upcoming appointments
     */
    public function getUpcomingAppointments()
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
            ->limit(10)
            ->get();

        return response()->json($appointments);
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $activities = [];

        // Recent appointments
        $activities['appointments'] = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->select(
                'appointments.*',
                'patients.NAME as patient_name'
            )
            ->orderBy('appointments.created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent payments
        $activities['payments'] = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->select(
                'payments.*',
                'patients.NAME as patient_name'
            )
            ->orderBy('payments.created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($activities);
    }

    /**
     * Get doctor profile summary
     */
    public function getProfileSummary()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('doctors.user_id', $doctorId)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name',
                'specialties.name_ar as specialty_name_ar'
            )
            ->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        // Get working hours
        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $doctor->id)
            ->orderBy('day_of_week')
            ->get();

        // Get wallet balance
        $wallet = DB::table('wallets')
            ->where('user_id', $doctorId)
            ->first();

        $profile = [
            'doctor' => $doctor,
            'working_hours' => $workingHours,
            'wallet_balance' => $wallet ? $wallet->balance : 0.00,
        ];

        return response()->json($profile);
    }

    /**
     * Export dashboard data
     */
    public function exportData(Request $request)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $type = $request->get('type', 'appointments');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        switch ($type) {
            case 'appointments':
                $data = DB::table('appointments')
                    ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                    ->where('appointments.doctor_id', $doctor->id)
                    ->select(
                        'appointments.id',
                        'patients.NAME as patient_name',
                        'appointments.appointment_date',
                        'appointments.appointment_time',
                        'appointments.STATUS',
                        'appointments.notes',
                        'appointments.created_at'
                    )
                    ->when($startDate, function ($query) use ($startDate) {
                        return $query->whereDate('appointments.appointment_date', '>=', $startDate);
                    })
                    ->when($endDate, function ($query) use ($endDate) {
                        return $query->whereDate('appointments.appointment_date', '<=', $endDate);
                    })
                    ->get();
                break;

            case 'payments':
                $data = DB::table('payments')
                    ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                    ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                    ->where('appointments.doctor_id', $doctor->id)
                    ->select(
                        'payments.id',
                        'patients.NAME as patient_name',
                        'payments.amount',
                        'payments.currency',
                        'payments.STATUS',
                        'payments.created_at'
                    )
                    ->when($startDate, function ($query) use ($startDate) {
                        return $query->whereDate('payments.created_at', '>=', $startDate);
                    })
                    ->when($endDate, function ($query) use ($endDate) {
                        return $query->whereDate('payments.created_at', '<=', $endDate);
                    })
                    ->get();
                break;

            default:
                return response()->json(['error' => 'Invalid export type'], 400);
        }

        return response()->json($data);
    }
}
