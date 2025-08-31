<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\ExportTrait;

class DashboardController extends Controller
{
    use ExportTrait;
    /**
     * Display the admin dashboard with statistics
     */
    public function index()
    {
        // Get current date and last month for comparisons
        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // User statistics
        $totalUsers = DB::table('users')->count();
        $totalDoctors = DB::table('doctors')->count();
        $totalPatients = DB::table('patients')->count();
        $activeUsers = DB::table('users')->where('status', 'active')->count();

        // Appointment statistics
        $totalAppointments = DB::table('appointments')->count();
        $todayAppointments = DB::table('appointments')
            ->whereDate('appointment_date', $currentDate->toDateString())
            ->count();
        $thisMonthAppointments = DB::table('appointments')
            ->whereMonth('appointment_date', $currentDate->month)
            ->whereYear('appointment_date', $currentDate->year)
            ->count();
        $lastMonthAppointments = DB::table('appointments')
            ->whereMonth('appointment_date', $lastMonth->month)
            ->whereYear('appointment_date', $lastMonth->year)
            ->count();

        // Payment statistics
        $totalRevenue = DB::table('payments')
            ->where('STATUS', 'succeeded')
            ->sum('amount');
        $thisMonthRevenue = DB::table('payments')
            ->where('STATUS', 'succeeded')
            ->whereMonth('created_at', $currentDate->month)
            ->whereYear('created_at', $currentDate->year)
            ->sum('amount');
        $lastMonthRevenue = DB::table('payments')
            ->where('STATUS', 'succeeded')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->sum('amount');

        // Subscription statistics
        $activeSubscriptions = DB::table('subscriptions')
            ->where('STATUS', 'active')
            ->count();
        $totalSubscriptions = DB::table('subscriptions')->count();

        // Recent activities
        $recentAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->select('appointments.*', 'patients.NAME as patient_name', 'doctors.name as doctor_name')
            ->orderBy('appointments.created_at', 'desc')
            ->limit(5)
            ->get();

        $recentPayments = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->select('payments.*', 'users.first_name', 'users.last_name')
            ->orderBy('payments.created_at', 'desc')
            ->limit(5)
            ->get();

        // Top performing doctors
        $topDoctors = DB::table('doctors')
            ->join('appointments', 'doctors.id', '=', 'appointments.doctor_id')
            ->select('doctors.name', 'doctors.specialty_id', DB::raw('COUNT(appointments.id) as appointment_count'))
            ->groupBy('doctors.id', 'doctors.name', 'doctors.specialty_id')
            ->orderBy('appointment_count', 'desc')
            ->limit(5)
            ->get();

        // Appointment status distribution
        $appointmentStatuses = DB::table('appointments')
            ->select('STATUS', DB::raw('COUNT(*) as count'))
            ->groupBy('STATUS')
            ->get();

        // Monthly revenue chart data (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = DB::table('payments')
                ->where('STATUS', 'succeeded')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('amount');

            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Calculate growth percentages
        $appointmentGrowth = $lastMonthAppointments > 0
            ? (($thisMonthAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100
            : 0;

        $revenueGrowth = $lastMonthRevenue > 0
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        // Calculate user growth (simplified - you can enhance this)
        $userGrowth = 5.2; // Placeholder - you can calculate actual user growth
        $doctorGrowth = 3.8; // Placeholder - you can calculate actual doctor growth

        // Prepare stats array for the view
        $stats = [
            'total_users' => $totalUsers,
            'total_doctors' => $totalDoctors,
            'total_patients' => $totalPatients,
            'total_appointments' => $totalAppointments,
            'total_revenue' => $totalRevenue,
            'user_growth' => $userGrowth,
            'doctor_growth' => $doctorGrowth,
            'appointment_growth' => round($appointmentGrowth, 1),
            'revenue_growth' => round($revenueGrowth, 1),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'totalUsers',
            'totalDoctors',
            'totalPatients',
            'activeUsers',
            'totalAppointments',
            'todayAppointments',
            'thisMonthAppointments',
            'lastMonthAppointments',
            'totalRevenue',
            'thisMonthRevenue',
            'lastMonthRevenue',
            'activeSubscriptions',
            'totalSubscriptions',
            'recentAppointments',
            'recentPayments',
            'topDoctors',
            'appointmentStatuses',
            'monthlyRevenue',
            'appointmentGrowth',
            'revenueGrowth'
        ));
    }

    /**
     * Get dashboard statistics for AJAX requests
     */
    public function getStats()
    {
        $currentDate = Carbon::now();

        $stats = [
            'total_users' => DB::table('users')->count(),
            'total_doctors' => DB::table('doctors')->count(),
            'total_patients' => DB::table('patients')->count(),
            'today_appointments' => DB::table('appointments')
                ->whereDate('appointment_date', $currentDate->toDateString())
                ->count(),
            'total_revenue' => DB::table('payments')
                ->where('STATUS', 'succeeded')
                ->sum('amount'),
            'active_subscriptions' => DB::table('subscriptions')
                ->where('STATUS', 'active')
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get chart data for dashboard
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

        // Last 6 months revenue
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = DB::table('payments')
                ->where('STATUS', 'succeeded')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('amount');

            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Appointment status distribution
        $statusDistribution = DB::table('appointments')
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
     * Get recent activities for dashboard
     */
    public function getRecentActivities()
    {
        $recentAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'doctors.name as doctor_name'
            )
            ->orderBy('appointments.created_at', 'desc')
            ->limit(10)
            ->get();

        $recentPayments = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->select('payments.*', 'users.first_name', 'users.last_name')
            ->orderBy('payments.created_at', 'desc')
            ->limit(10)
            ->get();

        $recentUsers = DB::table('users')
            ->select('id', 'username', 'email', 'role', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'appointments' => $recentAppointments,
            'payments' => $recentPayments,
            'users' => $recentUsers
        ]);
    }

    /**
     * Export dashboard data as CSV
     */
    public function exportData(Request $request)
    {
        $type = $request->get('type', 'appointments');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        switch ($type) {
            case 'appointments':
                $data = DB::table('appointments')
                    ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                    ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
                    ->leftJoin('payments', 'appointments.id', '=', 'payments.appointment_id')
                    ->select(
                        'appointments.id',
                        'patients.NAME as patient_name',
                        'patients.phone as patient_phone',
                        'patients.email as patient_email',
                        'doctors.name as doctor_name',
                        'appointments.appointment_date',
                        'appointments.appointment_time',
                        'appointments.STATUS',
                        'appointments.notes',
                        'payments.amount as payment_amount',
                        'payments.STATUS as payment_status',
                        DB::raw('JSON_EXTRACT(payments.meta, "$.payment_method") as payment_method'),
                        'appointments.created_at'
                    )
                    ->when($startDate, function ($query) use ($startDate) {
                        return $query->whereDate('appointments.appointment_date', '>=', $startDate);
                    })
                    ->when($endDate, function ($query) use ($endDate) {
                        return $query->whereDate('appointments.appointment_date', '<=', $endDate);
                    })
                    ->orderBy('appointments.created_at', 'desc')
                    ->get();

                // Prepare CSV data
                $csvData = [];
                foreach ($data as $appointment) {
                    $csvData[] = [
                        $appointment->id,
                        $appointment->patient_name,
                        $appointment->patient_phone,
                        $appointment->patient_email,
                        $appointment->doctor_name,
                        $this->formatDate($appointment->appointment_date),
                        $this->formatDateTime($appointment->appointment_time, 'g:i A'),
                        $this->formatStatus($appointment->STATUS),
                        $this->getAppointmentType($appointment->notes),
                        $appointment->notes,
                        $this->formatCurrency($appointment->payment_amount),
                        $this->formatStatus($appointment->payment_status),
                        str_replace('"', '', $appointment->payment_method),
                        $this->formatDateTime($appointment->created_at)
                    ];
                }

                $headers = [
                    'Appointment ID',
                    'Patient Name',
                    'Patient Phone',
                    'Patient Email',
                    'Doctor Name',
                    'Appointment Date',
                    'Appointment Time',
                    'Status',
                    'Appointment Type',
                    'Notes',
                    'Payment Amount',
                    'Payment Status',
                    'Payment Method',
                    'Created At'
                ];
                break;

            case 'payments':
                $data = DB::table('payments')
                    ->join('users', 'payments.user_id', '=', 'users.id')
                    ->leftJoin('doctors', 'payments.doctor_id', '=', 'doctors.id')
                    ->leftJoin('appointments', 'payments.appointment_id', '=', 'appointments.id')
                    ->select(
                        'payments.id',
                        'users.first_name',
                        'users.last_name',
                        'users.email',
                        'doctors.name as doctor_name',
                        'appointments.appointment_date',
                        'payments.amount',
                        'payments.currency',
                        'payments.STATUS',
                        DB::raw('JSON_EXTRACT(payments.meta, "$.payment_method") as payment_method'),
                        'payments.platform_fee',
                        'payments.net_amount',
                        'payments.created_at'
                    )
                    ->when($startDate, function ($query) use ($startDate) {
                        return $query->whereDate('payments.created_at', '>=', $startDate);
                    })
                    ->when($endDate, function ($query) use ($endDate) {
                        return $query->whereDate('payments.created_at', '<=', $endDate);
                    })
                    ->orderBy('payments.created_at', 'desc')
                    ->get();

                // Prepare CSV data
                $csvData = [];
                foreach ($data as $payment) {
                    $csvData[] = [
                        $payment->id,
                        $payment->first_name . ' ' . $payment->last_name,
                        $payment->email,
                        $payment->doctor_name,
                        $this->formatDate($payment->appointment_date),
                        $this->formatCurrency($payment->amount, $payment->currency),
                        $this->formatStatus($payment->STATUS),
                        str_replace('"', '', $payment->payment_method),
                        $this->formatCurrency($payment->platform_fee, $payment->currency),
                        $this->formatCurrency($payment->net_amount, $payment->currency),
                        $this->formatDateTime($payment->created_at)
                    ];
                }

                $headers = [
                    'Payment ID',
                    'Customer Name',
                    'Customer Email',
                    'Doctor Name',
                    'Appointment Date',
                    'Amount',
                    'Status',
                    'Payment Method',
                    'Platform Fee',
                    'Net Amount',
                    'Created At'
                ];
                break;

            case 'users':
                $data = DB::table('users')
                    ->leftJoin('doctors', 'users.id', '=', 'doctors.user_id')
                    ->leftJoin('patients', 'users.id', '=', 'patients.user_id')
                    ->select(
                        'users.id',
                        'users.username',
                        'users.email',
                        'users.first_name',
                        'users.last_name',
                        'users.role',
                        'users.status',
                        'doctors.name as doctor_name',
                        'patients.NAME as patient_name',
                        'users.last_login',
                        'users.created_at'
                    )
                    ->when($startDate, function ($query) use ($startDate) {
                        return $query->whereDate('users.created_at', '>=', $startDate);
                    })
                    ->when($endDate, function ($query) use ($endDate) {
                        return $query->whereDate('users.created_at', '<=', $endDate);
                    })
                    ->orderBy('users.created_at', 'desc')
                    ->get();

                // Prepare CSV data
                $csvData = [];
                foreach ($data as $user) {
                    $csvData[] = [
                        $user->id,
                        $user->username,
                        $user->email,
                        $user->first_name . ' ' . $user->last_name,
                        $this->formatStatus($user->role),
                        $this->formatStatus($user->status),
                        $user->doctor_name,
                        $user->patient_name,
                        $this->formatDateTime($user->last_login),
                        $this->formatDateTime($user->created_at)
                    ];
                }

                $headers = [
                    'User ID',
                    'Username',
                    'Email',
                    'Full Name',
                    'Role',
                    'Status',
                    'Doctor Name',
                    'Patient Name',
                    'Last Login',
                    'Created At'
                ];
                break;

            default:
                return response()->json(['error' => 'Invalid export type'], 400);
        }

        // Generate filename
        $filename = $this->generateExportFilename($type, 'admin_dashboard');

        // Get summary
        $summary = $this->getExportSummary($data, $type, 'Admin');
        if ($startDate) $summary['Start Date'] = $this->formatDate($startDate);
        if ($endDate) $summary['End Date'] = $this->formatDate($endDate);

        return $this->generateCSV($csvData, $filename, $headers, $summary);
    }

    /**
     * System health check
     */
    public function systemHealth()
    {
        $health = [
            'database' => [
                'status' => 'healthy',
                'connection' => DB::connection()->getPdo() ? 'connected' : 'disconnected'
            ],
            'storage' => [
                'status' => 'healthy',
                'writable' => is_writable(storage_path())
            ],
            'cache' => [
                'status' => 'healthy',
                'driver' => config('cache.default')
            ],
            'queue' => [
                'status' => 'healthy',
                'driver' => config('queue.default')
            ]
        ];

        return response()->json($health);
    }
}
