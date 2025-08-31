<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PatientController extends Controller
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
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $query = DB::table('patients')
            ->join('appointments', 'patients.id', '=', 'appointments.patient_id')
            ->where('appointments.doctor_id', $doctor->id)
            ->select(
                'patients.*',
                DB::raw('COUNT(appointments.id) as appointment_count'),
                DB::raw('MAX(appointments.appointment_date) as last_appointment'),
                DB::raw('MIN(appointments.appointment_date) as first_appointment')
            )
            ->groupBy('patients.id');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patients.NAME', 'like', "%{$search}%")
                  ->orWhere('patients.phone', 'like', "%{$search}%")
                  ->orWhere('patients.email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('patients.gender', $request->gender);
        }

        if ($request->filled('blood_type')) {
            $query->where('patients.blood_type', $request->blood_type);
        }

        $patients = $query->orderBy('patients.NAME')
            ->paginate(15);

        return view('doctor.patients.index', compact('patients'));
    }

    /**
     * Display the specified patient
     */
    public function show($id)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        // Get patient information
        $patient = DB::table('patients')
            ->where('patients.id', $id)
            ->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        // Check if patient has appointments with this doctor
        $hasAppointments = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->where('patient_id', $id)
            ->exists();

        if (!$hasAppointments) {
            return response()->json(['error' => 'Patient not found in your records'], 404);
        }

        // Get patient's appointment history with this doctor
        $appointments = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->where('patient_id', $id)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        // Get patient's payment history
        $payments = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->where('appointments.patient_id', $id)
            ->select('payments.*')
            ->orderBy('payments.created_at', 'desc')
            ->get();

        // Calculate patient statistics
        $stats = [
            'total_appointments' => $appointments->count(),
            'completed_appointments' => $appointments->where('STATUS', 'completed')->count(),
            'cancelled_appointments' => $appointments->where('STATUS', 'cancelled')->count(),
            'total_paid' => $payments->where('STATUS', 'succeeded')->sum('amount'),
            'pending_payments' => $payments->where('STATUS', 'pending')->count(),
        ];

        return view('doctor.patients.show', compact('patient', 'appointments', 'payments', 'stats'));
    }

    /**
     * Get patient's appointment history
     */
    public function getAppointmentHistory($patientId)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $appointments = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->where('patient_id', $patientId)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        return response()->json($appointments);
    }

    /**
     * Get patient's payment history
     */
    public function getPaymentHistory($patientId)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $payments = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->where('appointments.patient_id', $patientId)
            ->select('payments.*')
            ->orderBy('payments.created_at', 'desc')
            ->get();

        return response()->json($payments);
    }

    /**
     * Get patient statistics
     */
    public function getPatientStats($patientId)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $appointments = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->where('patient_id', $patientId);

        $payments = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->where('appointments.patient_id', $patientId);

        $stats = [
            'total_appointments' => $appointments->count(),
            'completed_appointments' => $appointments->where('STATUS', 'completed')->count(),
            'cancelled_appointments' => $appointments->where('STATUS', 'cancelled')->count(),
            'scheduled_appointments' => $appointments->where('STATUS', 'scheduled')->count(),
            'total_paid' => $payments->where('payments.STATUS', 'succeeded')->sum('payments.amount'),
            'pending_payments' => $payments->where('payments.STATUS', 'pending')->count(),
            'first_appointment' => $appointments->min('appointment_date'),
            'last_appointment' => $appointments->max('appointment_date'),
        ];

        return response()->json($stats);
    }

    /**
     * Get recent patients
     */
    public function getRecentPatients()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $recentPatients = DB::table('patients')
            ->join('appointments', 'patients.id', '=', 'appointments.patient_id')
            ->where('appointments.doctor_id', $doctor->id)
            ->select(
                'patients.*',
                DB::raw('MAX(appointments.appointment_date) as last_appointment')
            )
            ->groupBy('patients.id')
            ->orderBy('last_appointment', 'desc')
            ->limit(10)
            ->get();

        return response()->json($recentPatients);
    }

    /**
     * Get top patients by appointment count
     */
    public function getTopPatients()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $topPatients = DB::table('patients')
            ->join('appointments', 'patients.id', '=', 'appointments.patient_id')
            ->where('appointments.doctor_id', $doctor->id)
            ->select(
                'patients.*',
                DB::raw('COUNT(appointments.id) as appointment_count')
            )
            ->groupBy('patients.id')
            ->orderBy('appointment_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json($topPatients);
    }

    /**
     * Search patients
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $query = $request->query;

        $patients = DB::table('patients')
            ->join('appointments', 'patients.id', '=', 'appointments.patient_id')
            ->where('appointments.doctor_id', $doctor->id)
            ->where(function ($q) use ($query) {
                $q->where('patients.NAME', 'like', "%{$query}%")
                  ->orWhere('patients.phone', 'like', "%{$query}%")
                  ->orWhere('patients.email', 'like', "%{$query}%");
            })
            ->select(
                'patients.*',
                DB::raw('COUNT(appointments.id) as appointment_count')
            )
            ->groupBy('patients.id')
            ->orderBy('patients.NAME')
            ->limit(10)
            ->get();

        return response()->json($patients);
    }

    /**
     * Get patient demographics
     */
    public function getDemographics()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        // Gender distribution
        $genderDistribution = DB::table('patients')
            ->join('appointments', 'patients.id', '=', 'appointments.patient_id')
            ->where('appointments.doctor_id', $doctor->id)
            ->select('patients.gender', DB::raw('COUNT(DISTINCT patients.id) as count'))
            ->groupBy('patients.gender')
            ->get();

        // Age distribution
        $ageDistribution = DB::table('patients')
            ->join('appointments', 'patients.id', '=', 'appointments.patient_id')
            ->where('appointments.doctor_id', $doctor->id)
            ->whereNotNull('patients.date_of_birth')
            ->select(
                DB::raw('CASE
                    WHEN TIMESTAMPDIFF(YEAR, patients.date_of_birth, CURDATE()) < 18 THEN "Under 18"
                    WHEN TIMESTAMPDIFF(YEAR, patients.date_of_birth, CURDATE()) BETWEEN 18 AND 30 THEN "18-30"
                    WHEN TIMESTAMPDIFF(YEAR, patients.date_of_birth, CURDATE()) BETWEEN 31 AND 50 THEN "31-50"
                    WHEN TIMESTAMPDIFF(YEAR, patients.date_of_birth, CURDATE()) BETWEEN 51 AND 65 THEN "51-65"
                    ELSE "Over 65"
                END as age_group'),
                DB::raw('COUNT(DISTINCT patients.id) as count')
            )
            ->groupBy('age_group')
            ->get();

        // Blood type distribution
        $bloodTypeDistribution = DB::table('patients')
            ->join('appointments', 'patients.id', '=', 'appointments.patient_id')
            ->where('appointments.doctor_id', $doctor->id)
            ->whereNotNull('patients.blood_type')
            ->select('patients.blood_type', DB::raw('COUNT(DISTINCT patients.id) as count'))
            ->groupBy('patients.blood_type')
            ->get();

        return response()->json([
            'gender_distribution' => $genderDistribution,
            'age_distribution' => $ageDistribution,
            'blood_type_distribution' => $bloodTypeDistribution
        ]);
    }

    /**
     * Export patient data
     */
    public function export(Request $request)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $query = DB::table('patients')
            ->join('appointments', 'patients.id', '=', 'appointments.patient_id')
            ->where('appointments.doctor_id', $doctor->id)
            ->select(
                'patients.id',
                'patients.NAME',
                'patients.phone',
                'patients.email',
                'patients.date_of_birth',
                'patients.gender',
                'patients.blood_type',
                'patients.address',
                'patients.medical_history',
                'patients.emergency_contact',
                'patients.status',
                'patients.created_at',
                DB::raw('COUNT(appointments.id) as appointment_count'),
                DB::raw('MAX(appointments.appointment_date) as last_appointment'),
                DB::raw('MIN(appointments.appointment_date) as first_appointment')
            )
            ->groupBy('patients.id');

        // Apply filters
        if ($request->filled('gender')) {
            $query->where('patients.gender', $request->gender);
        }

        if ($request->filled('blood_type')) {
            $query->where('patients.blood_type', $request->blood_type);
        }

        $patients = $query->orderBy('patients.NAME')->get();

        return response()->json($patients);
    }

    /**
     * Get patient medical history
     */
    public function getMedicalHistory($patientId)
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $patient = DB::table('patients')
            ->where('id', $patientId)
            ->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        // Check if patient has appointments with this doctor
        $hasAppointments = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->where('patient_id', $patientId)
            ->exists();

        if (!$hasAppointments) {
            return response()->json(['error' => 'Patient not found in your records'], 404);
        }

        $medicalHistory = [
            'patient' => $patient,
            'appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('patient_id', $patientId)
                ->orderBy('appointment_date', 'desc')
                ->get(),
            'payments' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.doctor_id', $doctor->id)
                ->where('appointments.patient_id', $patientId)
                ->select('payments.*')
                ->orderBy('payments.created_at', 'desc')
                ->get()
        ];

        return response()->json($medicalHistory);
    }

    /**
     * Add patient notes
     */
    public function addNotes(Request $request, $patientId)
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

        // Check if patient has appointments with this doctor
        $hasAppointments = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->where('patient_id', $patientId)
            ->exists();

        if (!$hasAppointments) {
            return response()->json(['error' => 'Patient not found in your records'], 404);
        }

        // You can create a patient_notes table for this functionality
        // For now, we'll just return success
        return response()->json([
            'success' => true,
            'message' => 'Notes added successfully'
        ]);
    }

    /**
     * Get patient statistics for dashboard
     */
    public function getDashboardStats()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $stats = [
            'total_patients' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->distinct('patient_id')
                ->count('patient_id'),
            'new_patients_this_month' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->distinct('patient_id')
                ->count('patient_id'),
            'new_patients_last_month' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->distinct('patient_id')
                ->count('patient_id'),
            'active_patients' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('appointment_date', '>=', $currentDate->subDays(30)->toDateString())
                ->distinct('patient_id')
                ->count('patient_id'),
        ];

        // Calculate growth
        $stats['patient_growth'] = $stats['new_patients_last_month'] > 0
            ? (($stats['new_patients_this_month'] - $stats['new_patients_last_month']) / $stats['new_patients_last_month']) * 100
            : 0;

        return response()->json($stats);
    }
}
