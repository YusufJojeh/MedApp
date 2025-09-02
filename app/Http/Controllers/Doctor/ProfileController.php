<?php

namespace App\Http\Controllers\Doctor;

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
        $this->middleware('doctor');
    }

    /**
     * Display the doctor's profile
     */
    public function index()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->where('doctors.user_id', $doctorId)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name',
                'specialties.name_ar as specialty_name_ar',
                'users.profile_image'
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

        // Get wallet information
        $wallet = DB::table('wallets')
            ->where('user_id', $doctorId)
            ->first();

        // Get user information
        $user = DB::table('users')
            ->where('id', $doctorId)
            ->first();

        // Get specialties for dropdown
        $specialties = DB::table('specialties')
            ->select('id', 'name_en', 'name_ar')
            ->get();

        return view('doctor.profile.index', compact('doctor', 'workingHours', 'wallet', 'user', 'specialties'));
    }

    /**
     * Update doctor's basic profile information
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'specialty_id' => 'required|exists:specialties,id',
            'description' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'education' => 'nullable|string|max:255',
            'languages' => 'nullable|string|max:255',
            'consultation_fee' => 'required|numeric|min:0',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Handle profile image upload
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $fileName = 'doctor_' . $doctorId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $profileImagePath = 'doctors/profile_images/' . $fileName;

                // Store the file
                $file->storeAs('public/' . dirname($profileImagePath), $fileName);
            }

            $updateData = [
                'name' => $request->name,
                'specialty_id' => $request->specialty_id,
                'description' => $request->description,
                'experience_years' => $request->experience_years,
                'education' => $request->education,
                'languages' => $request->languages,
                'consultation_fee' => $request->consultation_fee,
                'updated_at' => now(),
            ];

            // Update doctor information
            DB::table('doctors')->where('id', $doctor->id)->update($updateData);

            // Update profile image in users table if provided
            if ($profileImagePath) {
                DB::table('users')->where('id', $doctorId)->update([
                    'profile_image' => $profileImagePath,
                    'updated_at' => now(),
                ]);
            }

            // Send notification for profile update
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->profileUpdated($doctorId);

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
     * Update user account information
     */
    public function updateAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
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
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Account information updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating account: ' . $e->getMessage()
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

        // Check current password
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
     * Update working hours
     */
    public function updateWorkingHours(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'working_hours' => 'required|array',
            'working_hours.*.day_of_week' => 'required|integer|between:0,6',
            'working_hours.*.is_available' => 'required|boolean',
            'working_hours.*.start_time' => 'required_if:working_hours.*.is_available,true|date_format:H:i:s',
            'working_hours.*.end_time' => 'required_if:working_hours.*.is_available,true|date_format:H:i:s|after:working_hours.*.start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        DB::beginTransaction();
        try {
            foreach ($request->working_hours as $workingHour) {
                DB::table('working_hours')
                    ->where('doctor_id', $doctor->id)
                    ->where('day_of_week', $workingHour['day_of_week'])
                    ->update([
                        'is_available' => $workingHour['is_available'],
                        'start_time' => $workingHour['is_available'] ? $workingHour['start_time'] : null,
                        'end_time' => $workingHour['is_available'] ? $workingHour['end_time'] : null,
                        'updated_at' => now(),
                    ]);
            }

            // Send notification for schedule update
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->scheduleUpdated($doctor->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Working hours updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating working hours: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get working hours
     */
    public function getWorkingHours()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $doctor->id)
            ->orderBy('day_of_week')
            ->get();

        return response()->json($workingHours);
    }

    /**
     * Update profile image
     */
    public function updateProfileImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();

        try {
            // Handle file upload (you can implement your file storage logic here)
            $imagePath = 'profile_images/' . time() . '.' . $request->profile_image->getClientOriginalExtension();
            // $request->profile_image->storeAs('public', $imagePath);

            DB::table('users')->where('id', $userId)->update([
                'profile_image' => $imagePath,
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile image updated successfully',
                'image_path' => $imagePath
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove profile image
     */
    public function removeProfileImage()
    {
        $userId = Auth::id();

        try {
            // Get current profile image
            $user = DB::table('users')->where('id', $userId)->first();

            if ($user && $user->profile_image) {
                // Delete the file from storage
                $filePath = storage_path('app/public/' . $user->profile_image);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // Update database to remove profile image
                DB::table('users')->where('id', $userId)->update([
                    'profile_image' => null,
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile image removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing profile image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wallet information
     */
    public function getWallet()
    {
        $userId = Auth::id();

        $wallet = DB::table('wallets')
            ->where('user_id', $userId)
            ->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        // Get recent transactions
        $transactions = DB::table('wallet_transactions')
            ->where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'wallet' => $wallet,
            'transactions' => $transactions
        ]);
    }

    /**
     * Request wallet withdrawal
     */
    public function requestWithdrawal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'bank_account' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        if ($request->amount > $wallet->balance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Create withdrawal request
            DB::table('wallet_transactions')->insert([
                'wallet_id' => $wallet->id,
                'type' => 'withdrawal',
                'amount' => $request->amount,
                'description' => 'Withdrawal request',
                'status' => 'pending',
                'metadata' => json_encode([
                    'bank_account' => $request->bank_account,
                    'notes' => $request->notes
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update wallet balance
            DB::table('wallets')->where('id', $wallet->id)->update([
                'balance' => $wallet->balance - $request->amount,
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error submitting withdrawal request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get profile statistics
     */
    public function getProfileStats()
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
            'completed_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('STATUS', 'completed')
                ->count(),
            'total_patients' => DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->distinct('patient_id')
                ->count('patient_id'),
            'total_revenue' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.doctor_id', $doctor->id)
                ->where('payments.STATUS', 'succeeded')
                ->sum('payments.amount'),
            'average_rating' => $doctor->rating ?? 0,
            'total_reviews' => $doctor->total_reviews ?? 0,
        ];

        return response()->json($stats);
    }

    /**
     * Toggle doctor availability
     */
    public function toggleAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        DB::table('doctors')->where('id', $doctor->id)->update([
            'is_active' => $request->is_active,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Availability updated successfully'
        ]);
    }

    /**
     * Get doctor's reviews
     */
    public function getReviews()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')->where('user_id', $doctorId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        // Note: You'll need to create a reviews table for this functionality
        // For now, we'll return empty array
        $reviews = [];

        return response()->json($reviews);
    }

    /**
     * Export profile data
     */
    public function exportProfile()
    {
        $doctorId = Auth::user()->id;
        $doctor = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('doctors.user_id', $doctorId)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name'
            )
            ->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $user = DB::table('users')->where('id', $doctorId)->first();
        $workingHours = DB::table('working_hours')->where('doctor_id', $doctor->id)->get();
        $wallet = DB::table('wallets')->where('user_id', $doctorId)->first();

        $profileData = [
            'user' => $user,
            'doctor' => $doctor,
            'working_hours' => $workingHours,
            'wallet' => $wallet,
        ];

        return response()->json($profileData);
    }
}
