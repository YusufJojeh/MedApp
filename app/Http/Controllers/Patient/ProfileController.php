<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ProfileController extends Controller
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
     * Display the patient's profile
     */
    public function index()
    {
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        // Get user information
        $user = DB::table('users')->where('id', $patientId)->first();

        // Get wallet information
        $wallet = DB::table('wallets')
            ->where('user_id', $patientId)
            ->first();

        // Get recent appointments
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
            ->limit(5)
            ->get();

        // Get profile statistics
        $stats = $this->getProfileStats($patient->id);

        // Calculate profile completion percentage
        $profileCompletion = $this->calculateProfileCompletion($patient, $user);

        return view('patient.profile.index', compact('patient', 'user', 'wallet', 'recentAppointments', 'stats', 'profileCompletion'));
    }

    /**
     * Update patient's basic profile information
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NAME' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'blood_type' => 'nullable|string|max:5',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        DB::beginTransaction();
        try {
            DB::table('patients')->where('id', $patient->id)->update([
                'NAME' => $request->NAME,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'blood_type' => $request->blood_type,
                'address' => $request->address,
                'medical_history' => $request->medical_history,
                'emergency_contact' => $request->emergency_contact,
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update account details (email, phone)
     */
    public function updateAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();

        DB::beginTransaction();
        try {
            DB::table('users')->where('id', $userId)->update([
                'email' => $request->email,
                'phone' => $request->phone,
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Account details updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating account details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        DB::beginTransaction();
        try {
            DB::table('users')->where('id', $user->id)->update([
                'password' => Hash::make($request->new_password),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error changing password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload profile image
     */
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();

        try {
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $filename = 'profile_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/profiles', $filename);

                DB::table('users')->where('id', $userId)->update([
                    'profile_image' => $filename,
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Profile image uploaded successfully',
                    'image_url' => asset('storage/profiles/' . $filename)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file provided'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Get profile completion percentage
     */
    public function getProfileCompletion()
    {
        $userId = Auth::id();
        $patient = DB::table('patients')->where('user_id', $userId)->first();
        $user = DB::table('users')->where('id', $userId)->first();

        if (!$patient || !$user) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $fields = [
            'NAME' => !empty($patient->NAME),
            'date_of_birth' => !empty($patient->date_of_birth),
            'gender' => !empty($patient->gender),
            'address' => !empty($patient->address),
            'emergency_contact' => !empty($patient->emergency_contact),
            'profile_image' => !empty($user->profile_image),
            'phone' => !empty($user->phone),
        ];

        $completedFields = count(array_filter($fields));
        $totalFields = count($fields);
        $completionPercentage = ($completedFields / $totalFields) * 100;

        return response()->json([
            'completion_percentage' => round($completionPercentage, 2),
            'completed_fields' => $completedFields,
            'total_fields' => $totalFields,
            'missing_fields' => array_keys(array_filter($fields, function($value) { return !$value; }))
        ]);
    }

    /**
     * Export profile data
     */
    public function export()
    {
        $userId = Auth::id();
        $patient = DB::table('patients')->where('user_id', $userId)->first();
        $user = DB::table('users')->where('id', $userId)->first();

        if (!$patient || !$user) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        // Get appointment history
        $appointments = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name'
            )
            ->orderBy('appointments.appointment_date', 'desc')
            ->get();

        // Get payment history
        $payments = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'payments.*',
                'doctors.name as doctor_name',
                'appointments.appointment_date',
                'appointments.appointment_time'
            )
            ->orderBy('payments.created_at', 'desc')
            ->get();

        $profileData = [
            'user_info' => $user,
            'patient_info' => $patient,
            'appointments' => $appointments,
            'payments' => $payments,
            'export_date' => now()->toISOString()
        ];

        return response()->json($profileData);
    }

    /**
     * Get profile statistics for dashboard
     */
    private function getProfileStats($patientId)
    {
        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        return [
            'total_appointments' => DB::table('appointments')
                ->where('patient_id', $patientId)
                ->count(),
            'completed_appointments' => DB::table('appointments')
                ->where('patient_id', $patientId)
                ->where('STATUS', 'completed')
                ->count(),
            'upcoming_appointments' => DB::table('appointments')
                ->where('patient_id', $patientId)
                ->where('STATUS', 'scheduled')
                ->where('appointment_date', '>=', $currentDate->toDateString())
                ->count(),
            'total_spent' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.patient_id', $patientId)
                ->where('payments.STATUS', 'succeeded')
                ->sum('payments.amount'),
            'total_doctors' => DB::table('appointments')
                ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
                ->where('appointments.patient_id', $patientId)
                ->distinct('doctors.id')
                ->count('doctors.id'),
        ];
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($patient, $user)
    {
        $fields = [
            'name' => !empty($patient->NAME),
            'email' => !empty($user->email),
            'date_of_birth' => !empty($patient->date_of_birth),
            'gender' => !empty($patient->gender),
            'address' => !empty($patient->address),
            'emergency_contact' => !empty($patient->emergency_contact),
            'profile_image' => !empty($user->profile_image),
            'phone' => !empty($user->phone),
        ];

        $completedFields = count(array_filter($fields));
        $totalFields = count($fields);
        $completionPercentage = ($completedFields / $totalFields) * 100;

        return round($completionPercentage, 0);
    }
}
