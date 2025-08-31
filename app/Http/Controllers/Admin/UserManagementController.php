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

class UserManagementController extends Controller
{
    use ExportTrait;
    /**
     * Display a listing of all users
     */
    public function index(Request $request)
    {
        $query = DB::table('users')
            ->select('users.*',
                DB::raw('CASE WHEN doctors.id IS NOT NULL THEN doctors.name ELSE NULL END as doctor_name'),
                DB::raw('CASE WHEN patients.id IS NOT NULL THEN patients.NAME ELSE NULL END as patient_name')
            )
            ->leftJoin('doctors', 'users.id', '=', 'doctors.user_id')
            ->leftJoin('patients', 'users.id', '=', 'patients.user_id');

        // Apply filters
        if ($request->filled('role')) {
            $query->where('users.role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('users.status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.username', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('users.created_at', 'desc')->paginate(15);

        // Add computed properties to each user
        $users->getCollection()->transform(function ($user) {
            // Role badge classes
            $user->role_badge_class = match($user->role) {
                'admin' => 'bg-red-500/20 text-red-400',
                'doctor' => 'bg-green-500/20 text-green-400',
                'patient' => 'bg-blue-500/20 text-blue-400',
                default => 'bg-gray-500/20 text-gray-400'
            };

            // Status badge classes and text
            $user->status_badge_class = match($user->status) {
                'active' => 'bg-green-500/20 text-green-400',
                'inactive' => 'bg-gray-500/20 text-gray-400',
                'suspended' => 'bg-red-500/20 text-red-400',
                default => 'bg-yellow-500/20 text-yellow-400'
            };

            $user->status_text = ucfirst($user->status ?? 'pending');
            $user->is_active = ($user->status === 'active');

            return $user;
        });

        // Get statistics for the stats cards
        $stats = [
            'total_users' => DB::table('users')->count(),
            'total_doctors' => DB::table('users')->where('role', 'doctor')->count(),
            'total_patients' => DB::table('users')->where('role', 'patient')->count(),
            'total_admins' => DB::table('users')->where('role', 'admin')->count(),
        ];

        $roles = ['admin', 'doctor', 'patient'];
        $statuses = ['active', 'inactive', 'suspended'];

        return view('admin.users.index', compact('users', 'roles', 'statuses', 'stats'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = ['admin', 'doctor', 'patient'];
        $specialties = DB::table('specialties')->get();

        return view('admin.users.create', compact('roles', 'specialties'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,doctor,patient',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'specialty_id' => 'required_if:role,doctor|exists:specialties,id',
            'consultation_fee' => 'nullable|numeric|min:0',
            'experience_years' => 'nullable|integer|min:0',
            'education' => 'nullable|string',
            'languages' => 'nullable|string',
            'description' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
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
                'role' => $request->role,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'status' => $request->status,
                'profile_image' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create role-specific records
            if ($request->role === 'doctor') {
                DB::table('doctors')->insert([
                    'user_id' => $userId,
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'specialty_id' => $request->specialty_id,
                    'description' => $request->description,
                    'experience_years' => $request->experience_years,
                    'education' => $request->education,
                    'languages' => $request->languages,
                    'consultation_fee' => $request->consultation_fee,
                    'is_active' => $request->status === 'active',
                    'is_featured' => false,
                    'rating' => 0.00,
                    'total_reviews' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create wallet for doctor
                DB::table('wallets')->insert([
                    'user_id' => $userId,
                    'balance' => 0.00,
                    'currency' => 'SAR',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($request->role === 'patient') {
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
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user_id' => $userId
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = DB::table('users')
            ->select('users.*',
                DB::raw('CASE WHEN doctors.id IS NOT NULL THEN doctors.name ELSE NULL END as doctor_name'),
                DB::raw('CASE WHEN patients.id IS NOT NULL THEN patients.NAME ELSE NULL END as patient_name'),
                'doctors.specialty_id',
                DB::raw('specialties.name_en as specialty_name'),
                'doctors.consultation_fee',
                'doctors.experience_years',
                'doctors.education',
                'doctors.languages',
                'doctors.description',
                'doctors.rating',
                'doctors.total_reviews',
                'patients.date_of_birth',
                'patients.gender',
                'patients.blood_type',
                'patients.address',
                'patients.medical_history',
                'patients.emergency_contact'
            )
            ->leftJoin('doctors', 'users.id', '=', 'doctors.user_id')
            ->leftJoin('patients', 'users.id', '=', 'patients.user_id')
            ->leftJoin('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('users.id', $id)
            ->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Get user activities
        $activities = [];

        if ($user->role === 'doctor') {
            $activities['appointments'] = DB::table('appointments')
                ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                ->where('appointments.doctor_id', DB::table('doctors')->where('user_id', $id)->value('id'))
                ->select('appointments.*', 'patients.NAME as patient_name')
                ->orderBy('appointments.created_at', 'desc')
                ->limit(10)
                ->get();
        }

        if ($user->role === 'patient') {
            $activities['appointments'] = DB::table('appointments')
                ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
                ->where('appointments.patient_id', DB::table('patients')->where('user_id', $id)->value('id'))
                ->select('appointments.*', 'doctors.name as doctor_name')
                ->orderBy('appointments.created_at', 'desc')
                ->limit(10)
                ->get();
        }

        $activities['payments'] = DB::table('payments')
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Check if request expects JSON (AJAX request)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'user' => $user,
                'activities' => $activities
            ]);
        }

        return view('admin.users.show', compact('user', 'activities'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        $user = DB::table('users')
            ->select('users.*',
                'doctors.specialty_id',
                DB::raw('specialties.name_en as specialty_name'),
                'doctors.consultation_fee',
                'doctors.experience_years',
                'doctors.education',
                'doctors.languages',
                'doctors.description',
                'patients.date_of_birth',
                'patients.gender',
                'patients.blood_type',
                'patients.address',
                'patients.medical_history',
                'patients.emergency_contact'
            )
            ->leftJoin('doctors', 'users.id', '=', 'doctors.user_id')
            ->leftJoin('patients', 'users.id', '=', 'patients.user_id')
            ->leftJoin('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('users.id', $id)
            ->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $roles = ['admin', 'doctor', 'patient'];
        $specialties = DB::table('specialties')->get();

        // Check if request expects JSON (AJAX request)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'user' => $user,
                'roles' => $roles,
                'specialties' => $specialties
            ]);
        }

        return view('admin.users.edit', compact('user', 'roles', 'specialties'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,doctor,patient',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'specialty_id' => 'required_if:role,doctor|exists:specialties,id',
            'consultation_fee' => 'nullable|numeric|min:0',
            'experience_years' => 'nullable|integer|min:0',
            'education' => 'nullable|string',
            'languages' => 'nullable|string',
            'description' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
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
                'role' => $request->role,
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

            // Update role-specific records
            if ($request->role === 'doctor') {
                $doctorData = [
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'specialty_id' => $request->specialty_id,
                    'description' => $request->description,
                    'experience_years' => $request->experience_years,
                    'education' => $request->education,
                    'languages' => $request->languages,
                    'consultation_fee' => $request->consultation_fee,
                    'is_active' => $request->status === 'active',
                    'updated_at' => now(),
                ];

                $doctorId = DB::table('doctors')->where('user_id', $id)->value('id');

                if ($doctorId) {
                    DB::table('doctors')->where('id', $doctorId)->update($doctorData);
                } else {
                    DB::table('doctors')->insert(array_merge($doctorData, [
                        'user_id' => $id,
                        'is_featured' => false,
                        'rating' => 0.00,
                        'total_reviews' => 0,
                        'created_at' => now(),
                    ]));
                }
            }

            if ($request->role === 'patient') {
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
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Delete role-specific records first
            if ($user->role === 'doctor') {
                DB::table('doctors')->where('user_id', $id)->delete();
                DB::table('wallets')->where('user_id', $id)->delete();
            }

            if ($user->role === 'patient') {
                DB::table('patients')->where('user_id', $id)->delete();
            }

            // Delete user
            DB::table('users')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions on users
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,suspend,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userIds = $request->user_ids;
        $action = $request->action;

        DB::beginTransaction();
        try {
            switch ($action) {
                case 'activate':
                    DB::table('users')->whereIn('id', $userIds)->update([
                        'status' => 'active',
                        'updated_at' => now()
                    ]);
                    break;

                case 'deactivate':
                    DB::table('users')->whereIn('id', $userIds)->update([
                        'status' => 'inactive',
                        'updated_at' => now()
                    ]);
                    break;

                case 'suspend':
                    DB::table('users')->whereIn('id', $userIds)->update([
                        'status' => 'suspended',
                        'updated_at' => now()
                    ]);
                    break;

                case 'delete':
                    // Delete role-specific records first
                    DB::table('doctors')->whereIn('user_id', $userIds)->delete();
                    DB::table('patients')->whereIn('user_id', $userIds)->delete();
                    DB::table('wallets')->whereIn('user_id', $userIds)->delete();

                    // Delete users
                    DB::table('users')->whereIn('id', $userIds)->delete();
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
     * Activate a user
     */
    public function activate($id)
    {
        DB::table('users')->where('id', $id)->update([
            'status' => 'active',
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User activated successfully'
        ]);
    }

    /**
     * Deactivate a user
     */
    public function deactivate($id)
    {
        DB::table('users')->where('id', $id)->update([
            'status' => 'inactive',
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User deactivated successfully'
        ]);
    }

    /**
     * Export users data as CSV
     */
    public function export(Request $request)
    {
        $query = DB::table('users')
            ->select('users.*',
                DB::raw('CASE WHEN doctors.id IS NOT NULL THEN doctors.name ELSE NULL END as doctor_name'),
                DB::raw('CASE WHEN patients.id IS NOT NULL THEN patients.NAME ELSE NULL END as patient_name'),
                DB::raw('CASE WHEN doctors.id IS NOT NULL THEN doctors.specialty_id ELSE NULL END as specialty_id'),
                DB::raw('CASE WHEN doctors.id IS NOT NULL THEN doctors.consultation_fee ELSE NULL END as consultation_fee'),
                DB::raw('CASE WHEN doctors.id IS NOT NULL THEN doctors.rating ELSE NULL END as rating'),
                DB::raw('CASE WHEN doctors.id IS NOT NULL THEN doctors.total_reviews ELSE NULL END as total_reviews')
            )
            ->leftJoin('doctors', 'users.id', '=', 'doctors.user_id')
            ->leftJoin('patients', 'users.id', '=', 'patients.user_id');

        // Apply filters
        if ($request->filled('role')) {
            $query->where('users.role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('users.status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.username', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('users.created_at', 'desc')->get();

        // Prepare CSV data
        $csvData = [];
        foreach ($users as $user) {
            $csvData[] = [
                $user->id,
                $user->username,
                $user->email,
                $user->first_name . ' ' . $user->last_name,
                $this->formatStatus($user->role),
                $this->formatStatus($user->status),
                $user->doctor_name,
                $user->patient_name,
                $this->getSpecialtyInfo($user->specialty_id),
                $this->formatCurrency($user->consultation_fee),
                $user->rating,
                $user->total_reviews,
                $this->formatDateTime($user->last_login),
                $this->formatDateTime($user->created_at),
                $this->formatDateTime($user->updated_at)
            ];
        }

        // Headers
        $headers = [
            'User ID',
            'Username',
            'Email',
            'Full Name',
            'Role',
            'Status',
            'Doctor Name',
            'Patient Name',
            'Specialty',
            'Consultation Fee',
            'Rating',
            'Total Reviews',
            'Last Login',
            'Created At',
            'Updated At'
        ];

        // Generate filename
        $filename = $this->generateExportFilename('users', 'admin_management');

        // Get summary
        $summary = $this->getExportSummary($users, 'users', 'Admin');
        $summary['Total Admins'] = $users->where('role', 'admin')->count();
        $summary['Total Doctors'] = $users->where('role', 'doctor')->count();
        $summary['Total Patients'] = $users->where('role', 'patient')->count();
        $summary['Active Users'] = $users->where('status', 'active')->count();

        return $this->generateCSV($csvData, $filename, $headers, $summary);
    }

    // ============================================================================
    // DOCTOR MANAGEMENT METHODS
    // ============================================================================

    /**
     * Display a listing of all doctors
     */
    public function doctors(Request $request)
    {
        $query = DB::table('users')
            ->join('doctors', 'users.id', '=', 'doctors.user_id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->select('users.*', 'doctors.*', 'specialties.name_en as specialty_name');

        // Apply filters
        if ($request->filled('specialty')) {
            $query->where('doctors.specialty_id', $request->specialty);
        }

        if ($request->filled('status')) {
            $query->where('users.status', $request->status);
        }

        if ($request->filled('verified')) {
            $query->where('doctors.is_verified', $request->verified);
        }

        if ($request->filled('rating')) {
            $rating = $request->rating;
            $query->where('doctors.rating', '>=', $rating);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.username', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('doctors.name', 'like', "%{$search}%")
                  ->orWhere('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%");
            });
        }

        $doctors = $query->orderBy('users.created_at', 'desc')->paginate(15);
        $specialties = DB::table('specialties')->get();

        // Get statistics for the stats cards
        $stats = [
            'total_doctors' => DB::table('users')->where('role', 'doctor')->count(),
            'active_doctors' => DB::table('users')->where('role', 'doctor')->where('status', 'active')->count(),
            'pending_doctors' => DB::table('doctors')->where('is_verified', false)->count(),
            'avg_rating' => DB::table('doctors')->avg('rating') ?? 0,
        ];

        return view('admin.doctors.index', compact('doctors', 'specialties', 'stats'));
    }

    /**
     * Show the form for creating a new doctor
     */
    public function createDoctor()
    {
        $specialties = DB::table('specialties')->get();
        return view('admin.doctors.create', compact('specialties'));
    }

    /**
     * Store a newly created doctor
     */
    public function storeDoctor(Request $request)
    {
        // This method can reuse the existing store method with role validation
        $request->merge(['role' => 'doctor']);
        return $this->store($request);
    }

    /**
     * Display the specified doctor
     */
    public function showDoctor($id)
    {
        $doctor = DB::table('users')
            ->join('doctors', 'users.id', '=', 'doctors.user_id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('users.id', $id)
            ->select('users.*', 'doctors.*', 'specialties.name_en as specialty_name')
            ->first();

        if (!$doctor) {
            abort(404, 'Doctor not found');
        }

        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified doctor
     */
    public function editDoctor($id)
    {
        $doctor = DB::table('users')
            ->join('doctors', 'users.id', '=', 'doctors.user_id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('users.id', $id)
            ->select('users.*', 'doctors.*', 'specialties.name_en as specialty_name')
            ->first();

        if (!$doctor) {
            abort(404, 'Doctor not found');
        }

        $specialties = DB::table('specialties')->get();
        return view('admin.doctors.edit', compact('doctor', 'specialties'));
    }

    /**
     * Update the specified doctor
     */
    public function updateDoctor(Request $request, $id)
    {
        // This method can reuse the existing update method
        return $this->update($request, $id);
    }

    /**
     * Remove the specified doctor
     */
    public function destroyDoctor($id)
    {
        // This method can reuse the existing destroy method
        return $this->destroy($id);
    }

    /**
     * Verify a doctor
     */
    public function verifyDoctor($id)
    {
        DB::table('doctors')->where('user_id', $id)->update([
            'is_verified' => true,
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Doctor verified successfully'
        ]);
    }

    /**
     * Unverify a doctor
     */
    public function unverifyDoctor($id)
    {
        DB::table('doctors')->where('user_id', $id)->update([
            'is_verified' => false,
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Doctor unverified successfully'
        ]);
    }

    /**
     * Activate a doctor (activate user account)
     */
    public function activateDoctor($id)
    {
        DB::table('users')->where('id', $id)->update([
            'status' => 'active',
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Doctor activated successfully'
        ]);
    }

    /**
     * Deactivate a doctor (deactivate user account)
     */
    public function deactivateDoctor($id)
    {
        DB::table('users')->where('id', $id)->update([
            'status' => 'inactive',
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Doctor deactivated successfully'
        ]);
    }

    /**
     * Bulk actions on doctors
     */
    public function bulkActionDoctors(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:verify,unverify,activate,deactivate,delete',
            'doctor_ids' => 'required|array',
            'doctor_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctorIds = $request->doctor_ids;
        $action = $request->action;

        DB::beginTransaction();
        try {
            switch ($action) {
                case 'verify':
                    DB::table('doctors')->whereIn('user_id', $doctorIds)->update([
                        'is_verified' => true,
                        'updated_at' => now()
                    ]);
                    break;

                case 'unverify':
                    DB::table('doctors')->whereIn('user_id', $doctorIds)->update([
                        'is_verified' => false,
                        'updated_at' => now()
                    ]);
                    break;

                case 'activate':
                    DB::table('users')->whereIn('id', $doctorIds)->update([
                        'status' => 'active',
                        'updated_at' => now()
                    ]);
                    break;

                case 'deactivate':
                    DB::table('users')->whereIn('id', $doctorIds)->update([
                        'status' => 'inactive',
                        'updated_at' => now()
                    ]);
                    break;

                case 'delete':
                    DB::table('doctors')->whereIn('user_id', $doctorIds)->delete();
                    DB::table('users')->whereIn('id', $doctorIds)->delete();
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
     * Export doctors data as CSV
     */
    public function exportDoctors(Request $request)
    {
        $query = DB::table('users')
            ->join('doctors', 'users.id', '=', 'doctors.user_id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
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
                'doctors.name as doctor_name',
                'doctors.description',
                'doctors.experience_years',
                'doctors.education',
                'doctors.languages',
                'doctors.consultation_fee',
                'doctors.is_active',
                'doctors.is_featured',
                'doctors.is_verified',
                'doctors.rating',
                'doctors.total_reviews',
                'specialties.name_en as specialty_name',
                'specialties.name_ar as specialty_name_ar'
            );

        // Apply filters
        if ($request->filled('specialty')) {
            $query->where('doctors.specialty_id', $request->specialty);
        }

        if ($request->filled('status')) {
            $query->where('users.status', $request->status);
        }

        if ($request->filled('verified')) {
            $query->where('doctors.is_verified', $request->verified);
        }

        if ($request->filled('rating')) {
            $rating = $request->rating;
            $query->where('doctors.rating', '>=', $rating);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.username', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('doctors.name', 'like', "%{$search}%")
                  ->orWhere('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%");
            });
        }

        $doctors = $query->orderBy('users.created_at', 'desc')->get();

        // Prepare CSV data
        $csvData = [];
        foreach ($doctors as $doctor) {
            $csvData[] = [
                $doctor->id,
                $doctor->username,
                $doctor->email,
                $doctor->first_name . ' ' . $doctor->last_name,
                $this->formatStatus($doctor->status),
                $doctor->doctor_name,
                $doctor->specialty_name,
                $doctor->specialty_name_ar,
                $doctor->description,
                $doctor->experience_years . ' years',
                $doctor->education,
                $doctor->languages,
                $this->formatCurrency($doctor->consultation_fee),
                $doctor->is_active ? 'Yes' : 'No',
                $doctor->is_featured ? 'Yes' : 'No',
                $doctor->is_verified ? 'Yes' : 'No',
                $doctor->rating,
                $doctor->total_reviews,
                $this->formatDateTime($doctor->last_login),
                $this->formatDateTime($doctor->created_at),
                $this->formatDateTime($doctor->updated_at)
            ];
        }

        // Headers
        $headers = [
            'User ID',
            'Username',
            'Email',
            'Full Name',
            'Status',
            'Doctor Name',
            'Specialty (English)',
            'Specialty (Arabic)',
            'Description',
            'Experience',
            'Education',
            'Languages',
            'Consultation Fee',
            'Active',
            'Featured',
            'Verified',
            'Rating',
            'Total Reviews',
            'Last Login',
            'Created At',
            'Updated At'
        ];

        // Generate filename
        $filename = $this->generateExportFilename('doctors', 'admin_management');

        // Get summary
        $summary = $this->getExportSummary($doctors, 'doctors', 'Admin');
        $summary['Active Doctors'] = $doctors->where('status', 'active')->count();
        $summary['Verified Doctors'] = $doctors->where('is_verified', 1)->count();
        $summary['Featured Doctors'] = $doctors->where('is_featured', 1)->count();
        $summary['Average Rating'] = number_format($doctors->avg('rating'), 1);

        return $this->generateCSV($csvData, $filename, $headers, $summary);
    }

    /**
     * Show doctor schedule
     */
    public function doctorSchedule($id)
    {
        // Get doctor information - try both users.id and doctors.id
        $doctor = DB::table('users')
            ->join('doctors', 'users.id', '=', 'doctors.user_id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where(function($query) use ($id) {
                $query->where('users.id', $id)
                      ->orWhere('doctors.id', $id);
            })
            ->where('users.role', 'doctor')
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.status',
                'doctors.id as doctor_id',
                'doctors.name as doctor_name',
                'doctors.consultation_fee',
                'doctors.experience_years',
                'doctors.rating',
                'specialties.name_en as specialty_name'
            )
            ->first();

        if (!$doctor) {
            abort(404, 'Doctor not found');
        }

        // Use the correct doctor ID for appointments
        $doctorId = $doctor->doctor_id ?? $doctor->id;

        // Get today's appointments
        $todayAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $doctorId)
            ->where('appointments.appointment_date', now()->toDateString())
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone',
                'patients.email as patient_email'
            )
            ->orderBy('appointments.appointment_time')
            ->get();

        // Get upcoming appointments (next 7 days)
        $upcomingAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $doctorId)
            ->where('appointments.appointment_date', '>', now()->toDateString())
            ->where('appointments.appointment_date', '<=', now()->addDays(7)->toDateString())
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone',
                'patients.email as patient_email'
            )
            ->orderBy('appointments.appointment_date')
            ->orderBy('appointments.appointment_time')
            ->get();

        // Get past appointments (last 7 days)
        $pastAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $doctorId)
            ->where('appointments.appointment_date', '<', now()->toDateString())
            ->where('appointments.appointment_date', '>=', now()->subDays(7)->toDateString())
            ->select(
                'appointments.*',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone',
                'patients.email as patient_email'
            )
            ->orderBy('appointments.appointment_date', 'desc')
            ->orderBy('appointments.appointment_time', 'desc')
            ->get();

        // Get appointment statistics
        $stats = [
            'total_appointments' => DB::table('appointments')->where('doctor_id', $doctorId)->count(),
            'today_appointments' => $todayAppointments->count(),
            'upcoming_appointments' => $upcomingAppointments->count(),
            'completed_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctorId)
                ->where('STATUS', 'completed')
                ->count(),
            'cancelled_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctorId)
                ->where('STATUS', 'cancelled')
                ->count(),
        ];

        return view('admin.doctors.schedule', compact(
            'doctor',
            'todayAppointments',
            'upcomingAppointments',
            'pastAppointments',
            'stats'
        ));
    }
}
