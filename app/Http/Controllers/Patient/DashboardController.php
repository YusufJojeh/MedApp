<?php

namespace App\Http\Controllers\Patient;

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
        $this->middleware('patient');
    }

    /**
     * Display the patient dashboard with statistics
     */
    public function index()
    {
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Appointment statistics
        $totalAppointments = DB::table('appointments')
            ->where('patient_id', $patient->id)
            ->count();

        $todayAppointments = DB::table('appointments')
            ->where('patient_id', $patient->id)
            ->whereDate('appointment_date', $currentDate->toDateString())
            ->count();

        $thisMonthAppointments = DB::table('appointments')
            ->where('patient_id', $patient->id)
            ->whereMonth('appointment_date', $currentDate->month)
            ->whereYear('appointment_date', $currentDate->year)
            ->count();

        $lastMonthAppointments = DB::table('appointments')
            ->where('patient_id', $patient->id)
            ->whereMonth('appointment_date', $lastMonth->month)
            ->whereYear('appointment_date', $lastMonth->year)
            ->count();

        // Payment statistics
        $totalSpent = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->where('appointments.patient_id', $patient->id)
            ->where('payments.STATUS', 'succeeded')
            ->sum('payments.amount');

        $thisMonthSpent = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->where('appointments.patient_id', $patient->id)
            ->where('payments.STATUS', 'succeeded')
            ->whereMonth('payments.created_at', $currentDate->month)
            ->whereYear('payments.created_at', $currentDate->year)
            ->sum('payments.amount');

        $lastMonthSpent = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->where('appointments.patient_id', $patient->id)
            ->where('payments.STATUS', 'succeeded')
            ->whereMonth('payments.created_at', $lastMonth->month)
            ->whereYear('payments.created_at', $lastMonth->year)
            ->sum('payments.amount');

        // Doctor statistics
        $totalDoctors = DB::table('appointments')
            ->where('patient_id', $patient->id)
            ->distinct('doctor_id')
            ->count('doctor_id');

        $favoriteDoctors = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'doctors.id',
                'doctors.name',
                'doctors.specialty_id',
                DB::raw('COUNT(appointments.id) as appointment_count')
            )
            ->groupBy('doctors.id', 'doctors.name', 'doctors.specialty_id')
            ->orderBy('appointment_count', 'desc')
            ->limit(5)
            ->get();

        // Appointment status distribution
        $appointmentStatuses = DB::table('appointments')
            ->where('patient_id', $patient->id)
            ->select('STATUS', DB::raw('COUNT(*) as count'))
            ->groupBy('STATUS')
            ->get();

        // Recent appointments
        $recentAppointments = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name'
            )
            ->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->limit(5)
            ->get();

        // Today's appointments
        $todayAppointmentsList = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $patient->id)
            ->whereDate('appointments.appointment_date', $currentDate->toDateString())
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name'
            )
            ->orderBy('appointments.appointment_time', 'asc')
            ->get();

        // Upcoming appointments
        $upcomingAppointments = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $patient->id)
            ->where('appointments.appointment_date', '>=', $currentDate->toDateString())
            ->where('appointments.STATUS', '!=', 'cancelled')
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name'
            )
            ->orderBy('appointments.appointment_date', 'asc')
            ->orderBy('appointments.appointment_time', 'asc')
            ->limit(5)
            ->get();

        // Monthly spending chart data (last 6 months)
        $monthlySpending = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $spending = DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.patient_id', $patient->id)
                ->where('payments.STATUS', 'succeeded')
                ->whereMonth('payments.created_at', $month->month)
                ->whereYear('payments.created_at', $month->year)
                ->sum('payments.amount');

            $monthlySpending[] = [
                'month' => $month->format('M Y'),
                'spending' => $spending
            ];
        }

        // Calculate growth percentages
        $appointmentGrowth = $lastMonthAppointments > 0
            ? (($thisMonthAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100
            : 0;

        $spendingGrowth = $lastMonthSpent > 0
            ? (($thisMonthSpent - $lastMonthSpent) / $lastMonthSpent) * 100
            : 0;

        // Wallet balance
        $wallet = DB::table('wallets')
            ->where('user_id', $patientId)
            ->first();

        $walletBalance = $wallet ? $wallet->balance : 0.00;

        // Recent payments
        $recentPayments = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'payments.*',
                'doctors.name as doctor_name'
            )
            ->orderBy('payments.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('patient.dashboard', compact(
            'patient',
            'totalAppointments',
            'todayAppointments',
            'thisMonthAppointments',
            'lastMonthAppointments',
            'totalSpent',
            'thisMonthSpent',
            'lastMonthSpent',
            'totalDoctors',
            'favoriteDoctors',
            'appointmentStatuses',
            'recentAppointments',
            'todayAppointmentsList',
            'upcomingAppointments',
            'monthlySpending',
            'appointmentGrowth',
            'spendingGrowth',
            'walletBalance',
            'recentPayments'
        ));
    }

    /**
     * Get dashboard statistics for AJAX requests
     */
    public function getStats()
    {
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $currentDate = Carbon::now();

        $stats = [
            'total_appointments' => DB::table('appointments')
                ->where('patient_id', $patient->id)
                ->count(),
            'today_appointments' => DB::table('appointments')
                ->where('patient_id', $patient->id)
                ->whereDate('appointment_date', $currentDate->toDateString())
                ->count(),
            'total_spent' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.patient_id', $patient->id)
                ->where('payments.STATUS', 'succeeded')
                ->sum('payments.amount'),
            'total_doctors' => DB::table('appointments')
                ->where('patient_id', $patient->id)
                ->distinct('doctor_id')
                ->count('doctor_id'),
            'wallet_balance' => DB::table('wallets')
                ->where('user_id', $patientId)
                ->value('balance') ?? 0.00,
        ];

        return response()->json($stats);
    }

    /**
     * Get chart data for dashboard
     */
    public function getChartData()
    {
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $currentDate = Carbon::now();

        // Last 7 days appointments
        $dailyAppointments = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = DB::table('appointments')
                ->where('patient_id', $patient->id)
                ->whereDate('appointment_date', $date->toDateString())
                ->count();

            $dailyAppointments[] = [
                'date' => $date->format('M d'),
                'count' => $count
            ];
        }

        // Last 6 months spending
        $monthlySpending = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $spending = DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.patient_id', $patient->id)
                ->where('payments.STATUS', 'succeeded')
                ->whereMonth('payments.created_at', $month->month)
                ->whereYear('payments.created_at', $month->year)
                ->sum('payments.amount');

            $monthlySpending[] = [
                'month' => $month->format('M Y'),
                'spending' => $spending
            ];
        }

        // Appointment status distribution
        $statusDistribution = DB::table('appointments')
            ->where('patient_id', $patient->id)
            ->select('STATUS', DB::raw('COUNT(*) as count'))
            ->groupBy('STATUS')
            ->get();

        return response()->json([
            'daily_appointments' => $dailyAppointments,
            'monthly_spending' => $monthlySpending,
            'status_distribution' => $statusDistribution
        ]);
    }

    /**
     * Get today's appointments
     */
    public function getTodayAppointments()
    {
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $currentDate = Carbon::now();

        $appointments = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $patient->id)
            ->whereDate('appointments.appointment_date', $currentDate->toDateString())
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name'
            )
            ->orderBy('appointments.appointment_time', 'asc')
            ->get();

        return response()->json($appointments);
    }

    /**
     * Get upcoming appointments
     */
    public function getUpcomingAppointments()
    {
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $currentDate = Carbon::now();

        $appointments = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $patient->id)
            ->where('appointments.appointment_date', '>=', $currentDate->toDateString())
            ->where('appointments.STATUS', '!=', 'cancelled')
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name'
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
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $activities = [];

        // Recent appointments
        $activities['appointments'] = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'appointments.*',
                'doctors.name as doctor_name'
            )
            ->orderBy('appointments.created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent payments
        $activities['payments'] = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'payments.*',
                'doctors.name as doctor_name'
            )
            ->orderBy('payments.created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($activities);
    }

    /**
     * Get patient profile summary
     */
    public function getProfileSummary()
    {
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        // Get user information
        $user = DB::table('users')->where('id', $patientId)->first();

        // Get wallet balance
        $wallet = DB::table('wallets')
            ->where('user_id', $patientId)
            ->first();

        $profile = [
            'patient' => $patient,
            'user' => $user,
            'wallet_balance' => $wallet ? $wallet->balance : 0.00,
        ];

        return response()->json($profile);
    }

    /**
     * Get favorite doctors
     */
    public function getFavoriteDoctors()
    {
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $favoriteDoctors = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'doctors.id',
                'doctors.name',
                'doctors.specialty_id',
                'doctors.consultation_fee',
                'doctors.rating',
                'specialties.name_en as specialty_name',
                DB::raw('COUNT(appointments.id) as appointment_count')
            )
            ->groupBy('doctors.id', 'doctors.name', 'doctors.specialty_id', 'doctors.consultation_fee', 'doctors.rating', 'specialties.name_en')
            ->orderBy('appointment_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json($favoriteDoctors);
    }

    /**
     * Get health summary
     */
    public function getHealthSummary()
    {
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $currentDate = Carbon::now();
        $lastYear = Carbon::now()->subYear();

        $healthSummary = [
            'total_appointments' => DB::table('appointments')
                ->where('patient_id', $patient->id)
                ->count(),
            'completed_appointments' => DB::table('appointments')
                ->where('patient_id', $patient->id)
                ->where('STATUS', 'completed')
                ->count(),
            'this_year_appointments' => DB::table('appointments')
                ->where('patient_id', $patient->id)
                ->whereYear('appointment_date', $currentDate->year)
                ->count(),
            'last_year_appointments' => DB::table('appointments')
                ->where('patient_id', $patient->id)
                ->whereYear('appointment_date', $lastYear->year)
                ->count(),
            'total_doctors_visited' => DB::table('appointments')
                ->where('patient_id', $patient->id)
                ->distinct('doctor_id')
                ->count('doctor_id'),
            'total_specialties' => DB::table('appointments')
                ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
                ->where('appointments.patient_id', $patient->id)
                ->distinct('doctors.specialty_id')
                ->count('doctors.specialty_id'),
        ];

        return response()->json($healthSummary);
    }

    /**
     * Export dashboard data
     */
    public function exportData(Request $request)
    {
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $type = $request->get('type', 'appointments');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        switch ($type) {
            case 'appointments':
                $data = DB::table('appointments')
                    ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
                    ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
                    ->where('appointments.patient_id', $patient->id)
                    ->select(
                        'appointments.id',
                        'doctors.name as doctor_name',
                        'specialties.name_en as specialty_name',
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
                    ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
                    ->where('appointments.patient_id', $patient->id)
                    ->select(
                        'payments.id',
                        'doctors.name as doctor_name',
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
