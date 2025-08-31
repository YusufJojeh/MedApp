<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Display admin profile
     */
    public function index()
    {
        $admin = Auth::user();

        // Get additional admin data
        $adminData = DB::table('admins')
            ->where('user_id', $admin->id)
            ->first();

        return view('admin.profile.index', compact('admin', 'adminData'));
    }

    /**
     * Show profile edit form
     */
    public function edit()
    {
        $admin = Auth::user();

        // Get additional admin data
        $adminData = DB::table('admins')
            ->where('user_id', $admin->id)
            ->first();

        return view('admin.profile.edit', compact('admin', 'adminData'));
    }

    /**
     * Update admin profile
     */
    public function update(Request $request)
    {
        $admin = Auth::user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Handle profile image upload
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($admin->profile_image) {
                    Storage::disk('public')->delete($admin->profile_image);
                }

                $file = $request->file('profile_image');
                $filename = 'admin_' . $admin->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $profileImagePath = $file->storeAs('profile-images', $filename, 'public');
            }

            // Update user table
            $userData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'updated_at' => now(),
            ];

            if ($profileImagePath) {
                $userData['profile_image'] = $profileImagePath;
            }

            DB::table('users')->where('id', $admin->id)->update($userData);

            // Update or create admin table record
            $adminData = [
                'phone' => $request->phone,
                'bio' => $request->bio,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'updated_at' => now(),
            ];

            DB::table('admins')->updateOrInsert(
                ['user_id' => $admin->id],
                $adminData
            );

            DB::commit();

            return redirect()->route('admin.profile.index')
                ->with('success', 'Profile updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }

    /**
     * Show password change form
     */
    public function changePassword()
    {
        return view('admin.profile.change-password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $admin = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        try {
            // Update password
            DB::table('users')->where('id', $admin->id)->update([
                'password' => Hash::make($request->new_password),
                'updated_at' => now(),
            ]);

            return redirect()->route('admin.profile.index')
                ->with('success', 'Password changed successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Error changing password: ' . $e->getMessage());
        }
    }

    /**
     * Show security settings
     */
    public function security()
    {
        $admin = Auth::user();

        $securityData = DB::table('admin_security_settings')
            ->where('user_id', $admin->id)
            ->first();

        return view('admin.profile.security', compact('admin', 'securityData'));
    }

    /**
     * Update security settings
     */
    public function updateSecurity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'two_factor_enabled' => 'boolean',
            'login_notifications' => 'boolean',
            'session_timeout' => 'integer|min:5|max:1440',
            'ip_whitelist' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $admin = Auth::user();

        try {
            DB::table('admin_security_settings')->updateOrInsert(
                ['user_id' => $admin->id],
                [
                    'two_factor_enabled' => $request->boolean('two_factor_enabled'),
                    'login_notifications' => $request->boolean('login_notifications'),
                    'session_timeout' => $request->session_timeout ?? 120,
                    'ip_whitelist' => $request->ip_whitelist,
                    'updated_at' => now(),
                ]
            );

            return redirect()->route('admin.profile.security')
                ->with('success', 'Security settings updated successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating security settings: ' . $e->getMessage());
        }
    }

    /**
     * Show activity log
     */
    public function activity()
    {
        $admin = Auth::user();

        $activities = DB::table('admin_activity_logs')
            ->where('user_id', $admin->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.profile.activity', compact('admin', 'activities'));
    }

    /**
     * Delete profile image
     */
    public function deleteImage()
    {
        $admin = Auth::user();

        try {
            if ($admin->profile_image) {
                Storage::disk('public')->delete($admin->profile_image);

                DB::table('users')->where('id', $admin->id)->update([
                    'profile_image' => null,
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile image deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting profile image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get admin statistics
     */
    public function getStats()
    {
        $admin = Auth::user();

        $stats = [
            'total_logins' => DB::table('admin_activity_logs')
                ->where('user_id', $admin->id)
                ->where('action', 'login')
                ->count(),
            'last_login' => DB::table('admin_activity_logs')
                ->where('user_id', $admin->id)
                ->where('action', 'login')
                ->orderBy('created_at', 'desc')
                ->value('created_at'),
            'total_actions' => DB::table('admin_activity_logs')
                ->where('user_id', $admin->id)
                ->count(),
            'profile_completion' => $this->calculateProfileCompletion($admin),
        ];

        return response()->json($stats);
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($admin)
    {
        $fields = [
            'first_name', 'last_name', 'email', 'profile_image',
            'phone', 'bio', 'date_of_birth', 'gender', 'address'
        ];

        $completed = 0;
        $total = count($fields);

        foreach ($fields as $field) {
            if (!empty($admin->$field)) {
                $completed++;
            }
        }

        return round(($completed / $total) * 100);
    }
}
