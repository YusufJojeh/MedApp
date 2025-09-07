<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    /**
     * Display reports dashboard
     */
    public function index()
    {
        $doctor = Auth::user()->doctor;
        
        // Get basic stats
        $stats = [
            'total_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->count(),
            'completed_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('status', 'completed')
                ->count(),
            'total_revenue' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('status', 'completed')
                ->sum('total_amount'),
            'total_patients' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->distinct('patient_id')
                ->count('patient_id')
        ];

        return view('doctor.reports.index', compact('stats'));
    }

    /**
     * Get appointment reports
     */
    public function appointments()
    {
        $doctor = Auth::user()->doctor;
        
        $appointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('users', 'patients.user_id', '=', 'users.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->select(
                'appointments.*',
                'users.first_name',
                'users.last_name'
            )
            ->orderBy('appointments.appointment_date', 'desc')
            ->paginate(20);

        return view('doctor.reports.appointments', compact('appointments'));
    }

    /**
     * Get revenue reports
     */
    public function revenue()
    {
        $doctor = Auth::user()->doctor;
        
        // Monthly revenue for the last 12 months
        $monthlyRevenue = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->where('status', 'completed')
            ->where('appointment_date', '>=', now()->subMonths(12))
            ->selectRaw('
                DATE_FORMAT(appointment_date, "%Y-%m") as month,
                SUM(total_amount) as revenue,
                COUNT(*) as appointments
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('doctor.reports.revenue', compact('monthlyRevenue'));
    }

    /**
     * Get patient reports
     */
    public function patients()
    {
        $doctor = Auth::user()->doctor;
        
        $patients = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('users', 'patients.user_id', '=', 'users.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->selectRaw('
                patients.id,
                users.first_name,
                users.last_name,
                users.email,
                COUNT(appointments.id) as total_appointments,
                MAX(appointments.appointment_date) as last_appointment,
                SUM(appointments.total_amount) as total_spent
            ')
            ->groupBy('patients.id', 'users.first_name', 'users.last_name', 'users.email')
            ->orderBy('total_appointments', 'desc')
            ->paginate(20);

        return view('doctor.reports.patients', compact('patients'));
    }
}
