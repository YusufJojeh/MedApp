<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Traits\ExportTrait;

class PatientManagementController extends Controller
{
    use ExportTrait;
    /**
     * Display a listing of all patients
     */
    public function index(Request $request)
    {
        $query = DB::table('users')
            ->join('patients', 'users.id', '=', 'patients.user_id')
            ->select(
                'users.*',
                'patients.NAME as patient_name',
                'patients.date_of_birth',
                'patients.gender',
                'patients.blood_type',
                'patients.address',
                'patients.medical_history',
                'patients.emergency_contact',
                'patients.status as patient_status'
            );

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.username', 'like', "%{$search}%")
                  ->orWhere('patients.NAME', 'like', "%{$search}%")
                  ->orWhere('patients.phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('users.status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->where('patients.gender', $request->gender);
        }

        if ($request->filled('blood_type')) {
            $query->where('patients.blood_type', $request->blood_type);
        }

        if ($request->filled('age_range')) {
            $ageRange = $request->age_range;
            $today = Carbon::today();

            switch ($ageRange) {
                case '0-18':
                    $query->where('patients.date_of_birth', '>=', $today->subYears(18));
                    break;
                case '19-30':
                    $query->whereBetween('patients.date_of_birth', [
                        $today->subYears(30),
                        $today->subYears(19)
                    ]);
                    break;
                case '31-50':
                    $query->whereBetween('patients.date_of_birth', [
                        $today->subYears(50),
                        $today->subYears(31)
                    ]);
                    break;
                case '51+':
                    $query->where('patients.date_of_birth', '<=', $today->subYears(51));
                    break;
            }
        }

        $patients = $query->orderBy('users.created_at', 'desc')->paginate(15);

        // Add computed properties to each patient
        $patients->getCollection()->transform(function ($patient) {
            // Calculate age
            if ($patient->date_of_birth) {
                $patient->age = Carbon::parse($patient->date_of_birth)->age;
            } else {
                $patient->age = 'Unknown';
            }

            // Status badge classes
            $patient->status_badge_class = match($patient->status) {
                'active' => 'bg-green-500/20 text-green-400',
                'inactive' => 'bg-gray-500/20 text-gray-400',
                'suspended' => 'bg-red-500/20 text-red-400',
                default => 'bg-yellow-500/20 text-yellow-400'
            };

            $patient->status_text = ucfirst($patient->status ?? 'pending');
            $patient->is_active = ($patient->status === 'active');

            return $patient;
        });

        // Get statistics for the stats cards
        $stats = [
            'total_patients' => DB::table('users')->where('role', 'patient')->count(),
            'active_patients' => DB::table('users')->where('role', 'patient')->where('status', 'active')->count(),
            'new_patients_this_month' => DB::table('users')
                ->where('role', 'patient')
                ->whereMonth('created_at', now()->month)
                ->count(),
            'avg_age' => DB::table('patients')
                ->whereNotNull('date_of_birth')
                ->selectRaw('AVG(TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())) as avg_age')
                ->first()->avg_age ?? 0,
        ];

        $genders = ['male', 'female', 'other'];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $ageRanges = ['0-18', '19-30', '31-50', '51+'];
        $statuses = ['active', 'inactive', 'suspended'];

        return view('admin.patients.index', compact('patients', 'genders', 'bloodTypes', 'ageRanges', 'statuses', 'stats'));
    }

    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        $genders = ['male', 'female', 'other'];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $statuses = ['active', 'inactive', 'suspended'];

        return view('admin.patients.create', compact('genders', 'bloodTypes', 'statuses'));
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'blood_type' => 'nullable|string|max:5',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Create user
            $userId = DB::table('users')->insertGetId([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'patient',
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'status' => $request->status,
                'profile_image' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create patient record
            DB::table('patients')->insert([
                'user_id' => $userId,
                'NAME' => $request->first_name . ' ' . $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'blood_type' => $request->blood_type,
                'address' => $request->address,
                'medical_history' => $request->medical_history,
                'emergency_contact' => $request->emergency_contact,
                'status' => $request->status === 'active' ? 'active' : 'inactive',
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Patient created successfully',
                'patient_id' => $userId
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating patient: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified patient
     */
    public function show($id)
    {
        $patient = DB::table('users')
            ->join('patients', 'users.id', '=', 'patients.user_id')
            ->where('users.id', $id)
            ->where('users.role', 'patient')
            ->select(
                'users.*',
                'patients.NAME as patient_name',
                'patients.date_of_birth',
                'patients.gender',
                'patients.blood_type',
                'patients.address',
                'patients.medical_history',
                'patients.emergency_contact',
                'patients.status as patient_status'
            )
            ->first();

        if (!$patient) {
            abort(404, 'Patient not found');
        }

        // Calculate age
        if ($patient->date_of_birth) {
            $patient->age = Carbon::parse($patient->date_of_birth)->age;
        } else {
            $patient->age = 'Unknown';
        }

        // Get patient's appointment history
        $appointments = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', DB::table('patients')->where('user_id', $id)->value('id'))
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name'
            )
            ->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->get();

        // Get patient's payment history
        $payments = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->where('payments.user_id', $id)
            ->select(
                'payments.*',
                'doctors.name as doctor_name',
                'appointments.appointment_date',
                'appointments.appointment_time'
            )
            ->orderBy('payments.created_at', 'desc')
            ->get();

        // Calculate patient statistics
        $stats = [
            'total_appointments' => $appointments->count(),
            'completed_appointments' => $appointments->where('STATUS', 'completed')->count(),
            'upcoming_appointments' => $appointments->where('STATUS', 'confirmed')->count(),
            'total_spent' => $payments->where('STATUS', 'completed')->sum('amount'),
            'last_appointment' => $appointments->first(),
            'next_appointment' => $appointments->where('appointment_date', '>=', now()->toDateString())->first(),
        ];

        return view('admin.patients.show', compact('patient', 'appointments', 'payments', 'stats'));
    }

    /**
     * Show the form for editing the specified patient
     */
    public function edit($id)
    {
        $patient = DB::table('users')
            ->join('patients', 'users.id', '=', 'patients.user_id')
            ->where('users.id', $id)
            ->where('users.role', 'patient')
            ->select(
                'users.*',
                'patients.NAME as patient_name',
                'patients.date_of_birth',
                'patients.gender',
                'patients.blood_type',
                'patients.address',
                'patients.medical_history',
                'patients.emergency_contact',
                'patients.status as patient_status'
            )
            ->first();

        if (!$patient) {
            abort(404, 'Patient not found');
        }

        $genders = ['male', 'female', 'other'];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $statuses = ['active', 'inactive', 'suspended'];

        return view('admin.patients.edit', compact('patient', 'genders', 'bloodTypes', 'statuses'));
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, $id)
    {
        $patient = DB::table('users')->where('id', $id)->where('role', 'patient')->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|string|min:8|confirmed',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'blood_type' => 'nullable|string|max:5',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Update user
            $userData = [
                'username' => $request->username,
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'status' => $request->status,
                'updated_at' => now(),
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            DB::table('users')->where('id', $id)->update($userData);

            // Update patient record
            $patientData = [
                'NAME' => $request->first_name . ' ' . $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'blood_type' => $request->blood_type,
                'address' => $request->address,
                'medical_history' => $request->medical_history,
                'emergency_contact' => $request->emergency_contact,
                'status' => $request->status === 'active' ? 'active' : 'inactive',
            ];

            $patientId = DB::table('patients')->where('user_id', $id)->value('id');

            if ($patientId) {
                DB::table('patients')->where('id', $patientId)->update($patientData);
            } else {
                DB::table('patients')->insert(array_merge($patientData, [
                    'user_id' => $id,
                    'created_at' => now(),
                ]));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Patient updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating patient: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified patient
     */
    public function destroy($id)
    {
        $patient = DB::table('users')->where('id', $id)->where('role', 'patient')->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Delete patient record
            DB::table('patients')->where('user_id', $id)->delete();

            // Delete user
            DB::table('users')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Patient deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting patient: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate a patient
     */
    public function activate($id)
    {
        DB::table('users')->where('id', $id)->where('role', 'patient')->update([
            'status' => 'active',
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Patient activated successfully'
        ]);
    }

    /**
     * Deactivate a patient
     */
    public function deactivate($id)
    {
        DB::table('users')->where('id', $id)->where('role', 'patient')->update([
            'status' => 'inactive',
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Patient deactivated successfully'
        ]);
    }

    /**
     * Bulk actions on patients
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'patient_ids' => 'required|array',
            'patient_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $patientIds = $request->patient_ids;
        $action = $request->action;

        DB::beginTransaction();
        try {
            switch ($action) {
                case 'activate':
                    DB::table('users')->whereIn('id', $patientIds)->where('role', 'patient')->update([
                        'status' => 'active',
                        'updated_at' => now()
                    ]);
                    break;

                case 'deactivate':
                    DB::table('users')->whereIn('id', $patientIds)->where('role', 'patient')->update([
                        'status' => 'inactive',
                        'updated_at' => now()
                    ]);
                    break;

                case 'delete':
                    // Delete patient records first
                    DB::table('patients')->whereIn('user_id', $patientIds)->delete();

                    // Delete users
                    DB::table('users')->whereIn('id', $patientIds)->where('role', 'patient')->delete();
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
     * Export patients data as CSV
     */
    public function export(Request $request)
    {
        $query = DB::table('users')
            ->join('patients', 'users.id', '=', 'patients.user_id')
            ->select(
                'users.id',
                'users.username',
                'users.email',
                'users.first_name',
                'users.last_name',
                'users.status',
                'users.last_login',
                'users.created_at',
                'users.updated_at',
                'patients.NAME as patient_name',
                'patients.phone',
                'patients.date_of_birth',
                'patients.gender',
                'patients.blood_type',
                'patients.address',
                'patients.emergency_contact',
                'patients.medical_history',
                'patients.status as patient_status'
            );

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.username', 'like', "%{$search}%")
                  ->orWhere('patients.NAME', 'like', "%{$search}%")
                  ->orWhere('patients.phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('users.status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->where('patients.gender', $request->gender);
        }

        if ($request->filled('blood_type')) {
            $query->where('patients.blood_type', $request->blood_type);
        }

        if ($request->filled('age_range')) {
            $ageRange = $request->age_range;
            $today = Carbon::today();

            switch ($ageRange) {
                case '0-18':
                    $query->where('patients.date_of_birth', '>=', $today->subYears(18));
                    break;
                case '19-30':
                    $query->whereBetween('patients.date_of_birth', [
                        $today->subYears(30),
                        $today->subYears(19)
                    ]);
                    break;
                case '31-50':
                    $query->whereBetween('patients.date_of_birth', [
                        $today->subYears(50),
                        $today->subYears(31)
                    ]);
                    break;
                case '51+':
                    $query->where('patients.date_of_birth', '<=', $today->subYears(51));
                    break;
            }
        }

        $patients = $query->orderBy('users.created_at', 'desc')->get();

        // Prepare CSV data
        $csvData = [];
        foreach ($patients as $patient) {
            $csvData[] = [
                $patient->id,
                $patient->username,
                $patient->email,
                $patient->first_name . ' ' . $patient->last_name,
                $this->formatStatus($patient->status),
                $patient->patient_name,
                $patient->phone,
                $this->formatDate($patient->date_of_birth),
                $this->calculateAge($patient->date_of_birth),
                $patient->gender,
                $patient->blood_type,
                $patient->address,
                $patient->emergency_contact,
                $patient->medical_history,
                $this->formatStatus($patient->patient_status),
                $this->formatDateTime($patient->last_login),
                $this->formatDateTime($patient->created_at),
                $this->formatDateTime($patient->updated_at)
            ];
        }

        // Headers
        $headers = [
            'User ID',
            'Username',
            'Email',
            'Full Name',
            'Status',
            'Patient Name',
            'Phone',
            'Date of Birth',
            'Age',
            'Gender',
            'Blood Type',
            'Address',
            'Emergency Contact',
            'Medical History',
            'Patient Status',
            'Last Login',
            'Created At',
            'Updated At'
        ];

        // Generate filename
        $filename = $this->generateExportFilename('patients', 'admin_management');

        // Get summary
        $summary = $this->getExportSummary($patients, 'patients', 'Admin');
        $summary['Active Patients'] = $patients->where('status', 'active')->count();
        $summary['Average Age'] = number_format($patients->whereNotNull('date_of_birth')->avg(function($p) {
            return $this->calculateAge($p->date_of_birth);
        }), 1) . ' years';
        $summary['Male Patients'] = $patients->where('gender', 'male')->count();
        $summary['Female Patients'] = $patients->where('gender', 'female')->count();

        return $this->generateCSV($csvData, $filename, $headers, $summary);
    }

    /**
     * Get patient statistics
     */
    public function getStats()
    {
        $stats = [
            'total_patients' => DB::table('users')->where('role', 'patient')->count(),
            'active_patients' => DB::table('users')->where('role', 'patient')->where('status', 'active')->count(),
            'new_patients_this_month' => DB::table('users')
                ->where('role', 'patient')
                ->whereMonth('created_at', now()->month)
                ->count(),
            'avg_age' => DB::table('patients')
                ->whereNotNull('date_of_birth')
                ->selectRaw('AVG(TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())) as avg_age')
                ->first()->avg_age ?? 0,
        ];

        return response()->json($stats);
    }

    /**
     * Get patient demographics
     */
    public function getDemographics()
    {
        $demographics = [
            'gender_distribution' => DB::table('patients')
                ->selectRaw('gender, COUNT(*) as count')
                ->whereNotNull('gender')
                ->groupBy('gender')
                ->get(),

            'age_distribution' => [
                '0-18' => DB::table('patients')
                    ->whereNotNull('date_of_birth')
                    ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 18')
                    ->count(),
                '19-30' => DB::table('patients')
                    ->whereNotNull('date_of_birth')
                    ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 19 AND 30')
                    ->count(),
                '31-50' => DB::table('patients')
                    ->whereNotNull('date_of_birth')
                    ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 31 AND 50')
                    ->count(),
                '51+' => DB::table('patients')
                    ->whereNotNull('date_of_birth')
                    ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 50')
                    ->count(),
            ],

            'blood_type_distribution' => DB::table('patients')
                ->selectRaw('blood_type, COUNT(*) as count')
                ->whereNotNull('blood_type')
                ->groupBy('blood_type')
                ->get(),
        ];

        return response()->json($demographics);
    }
}
