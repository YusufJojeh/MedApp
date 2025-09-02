<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     */
    public function showRegistrationForm()
    {
        $specialties = DB::table('specialties')->select('id', 'name_en', 'name_ar')->get();
        $plans = DB::table('plans')
            ->where('audience', 'doctors')
            ->where('is_popular', true)
            ->select('id', 'name', 'price', 'currency', 'billing_cycle')
            ->get();

        return view('auth.register', compact('specialties', 'plans'));
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:doctor,patient',
            'phone' => 'required|string|max:20',
            'terms' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // Check if registration is enabled for the selected role
        if (!$this->isRegistrationEnabled($request->role)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration is currently disabled for ' . $request->role . 's.'
                ], 403);
            }
            return back()->withErrors(['role' => 'Registration is currently disabled for ' . $request->role . 's.'])->withInput();
        }

        DB::beginTransaction();
        try {
            // Split full name into first and last name
            $nameParts = explode(' ', trim($request->name), 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            // Generate username from email
            $username = strtolower(explode('@', $request->email)[0]);

            // Create user
            $userId = DB::table('users')->insertGetId([
                'username' => $username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $request->phone,
                'status' => 'active',
                'profile_image' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create role-specific records
            if ($request->role === 'doctor') {
                $this->createDoctorProfile($userId, $request);
            } elseif ($request->role === 'patient') {
                $this->createPatientProfile($userId, $request);
            }

            // Send welcome email
            // $this->sendWelcomeEmail($request->email, $firstName, $request->role);

            // Log registration activity
            // $this->logRegistrationActivity($userId, $request);

            // Send notification for new user registration
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->userRegistered($userId);

            DB::commit();

            // Auto-login the user
            Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ]);

            $user = DB::table('users')->where('id', $userId)->first();
            $userData = $this->getUserRoleData($user);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful! Welcome to Medical Booking System.',
                    'user' => $userData,
                    'redirect_url' => $this->getRedirectUrl($request->role)
                ]);
            }

            // For web requests, redirect to appropriate dashboard
            return redirect()->intended($this->getRedirectUrl($request->role))
                ->with('success', 'Registration successful! Welcome to Medical Booking System.');

        } catch (\Exception $e) {
            DB::rollback();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error during registration: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['general' => 'Error during registration: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Create doctor profile.
     */
    protected function createDoctorProfile($userId, Request $request)
    {
        // Split full name into first and last name
        $nameParts = explode(' ', trim($request->name), 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        // Create doctor record
        DB::table('doctors')->insert([
            'user_id' => $userId,
            'name' => $firstName . ' ' . $lastName,
            'specialty_id' => 1, // Default specialty
            'description' => '',
            'experience_years' => 0,
            'education' => '',
            'languages' => 'English',
            'consultation_fee' => 100.00,
            'is_active' => true,
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

        // Create default working hours (Sunday to Thursday, 9 AM to 5 PM)
        $workingDays = [0, 1, 2, 3, 4]; // Sunday to Thursday
        foreach ($workingDays as $day) {
            DB::table('working_hours')->insert([
                'doctor_id' => $userId,
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Create patient profile.
     */
    protected function createPatientProfile($userId, Request $request)
    {
        // Split full name into first and last name
        $nameParts = explode(' ', trim($request->name), 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        DB::table('patients')->insert([
            'user_id' => $userId,
            'NAME' => $firstName . ' ' . $lastName,
            'phone' => $request->phone,
            'email' => $request->email,
            'date_of_birth' => null,
            'gender' => null,
            'blood_type' => null,
            'address' => null,
            'medical_history' => null,
            'emergency_contact' => null,
            'status' => 'active',
            'created_at' => now(),
        ]);
    }

    /**
     * Send welcome email.
     */
    protected function sendWelcomeEmail($email, $firstName, $role)
    {
        // You can implement your email service here
        // For now, we'll just log the email
        \Log::info("Welcome email sent to {$email} for {$role} registration");

        // Example email implementation:
        /*
        Mail::send('emails.welcome', [
            'name' => $firstName,
            'role' => $role
        ], function ($message) use ($email, $firstName) {
            $message->to($email, $firstName)
                    ->subject('Welcome to Medical Booking System');
        });
        */
    }

    /**
     * Log registration activity.
     */
    protected function logRegistrationActivity($userId, Request $request)
    {
        DB::table('registration_activities')->insert([
            'user_id' => $userId,
            'role' => $request->role,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now()
        ]);
    }

    /**
     * Check if registration is enabled for the role.
     */
    protected function isRegistrationEnabled($role)
    {
        // You can implement role-based registration control here
        $enabledRoles = ['doctor', 'patient'];
        return in_array($role, $enabledRoles);
    }

    /**
     * Get user role-specific data.
     */
    protected function getUserRoleData($user)
    {
        $userData = [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->role,
            'status' => $user->status,
            'profile_image' => $user->profile_image,
            'last_login' => $user->last_login,
        ];

        // Add role-specific data
        switch ($user->role) {
            case 'doctor':
                $doctor = DB::table('doctors')
                    ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
                    ->where('doctors.user_id', $user->id)
                    ->select(
                        'doctors.*',
                        'specialties.name_en as specialty_name',
                        'specialties.name_ar as specialty_name_ar'
                    )
                    ->first();

                if ($doctor) {
                    $userData['doctor'] = $doctor;
                }
                break;

            case 'patient':
                $patient = DB::table('patients')
                    ->where('patients.user_id', $user->id)
                    ->first();

                if ($patient) {
                    $userData['patient'] = $patient;
                }
                break;
        }

        return $userData;
    }

    /**
     * Get redirect URL based on user role.
     */
    protected function getRedirectUrl($role)
    {
        switch ($role) {
            case 'doctor':
                return route('doctor.dashboard');
            case 'patient':
                return route('patient.dashboard');
            default:
                return route('patient.dashboard');
        }
    }

    /**
     * Check username availability.
     */
    public function checkUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $username = $request->username;
        $exists = DB::table('users')->where('username', $username)->exists();

        return response()->json([
            'success' => true,
            'available' => !$exists,
            'message' => $exists ? 'Username is already taken.' : 'Username is available.'
        ]);
    }

    /**
     * Check email availability.
     */
    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        $exists = DB::table('users')->where('email', $email)->exists();

        return response()->json([
            'success' => true,
            'available' => !$exists,
            'message' => $exists ? 'Email is already registered.' : 'Email is available.'
        ]);
    }

    /**
     * Get registration requirements for a role.
     */
    public function getRegistrationRequirements(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:doctor,patient'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $role = $request->role;
        $requirements = [];

        switch ($role) {
            case 'doctor':
                $requirements = [
                    'specialties' => DB::table('specialties')->select('id', 'name_en', 'name_ar')->get(),
                    'required_fields' => [
                        'username', 'email', 'password', 'first_name', 'last_name',
                        'specialty_id', 'consultation_fee', 'experience_years'
                    ],
                    'optional_fields' => [
                        'phone', 'education', 'languages', 'description'
                    ]
                ];
                break;

            case 'patient':
                $requirements = [
                    'required_fields' => [
                        'username', 'email', 'password', 'first_name', 'last_name',
                        'date_of_birth', 'gender'
                    ],
                    'optional_fields' => [
                        'phone', 'blood_type', 'address', 'medical_history', 'emergency_contact'
                    ]
                ];
                break;
        }

        return response()->json([
            'success' => true,
            'requirements' => $requirements
        ]);
    }

    /**
     * Verify email address.
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        $token = $request->token;

        // Check if verification token exists and is valid
        $verification = DB::table('email_verifications')
            ->where('email', $email)
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification token.'
            ], 400);
        }

        // Mark email as verified
        DB::table('users')
            ->where('email', $email)
            ->update([
                'email_verified_at' => now(),
                'updated_at' => now()
            ]);

        // Delete verification token
        DB::table('email_verifications')
            ->where('email', $email)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully!'
        ]);
    }

    /**
     * Resend verification email.
     */
    public function resendVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        $user = DB::table('users')->where('email', $email)->first();

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified.'
            ], 400);
        }

        // Generate new verification token
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addHours(24);

        // Store verification token
        DB::table('email_verifications')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
                'created_at' => now(),
                'expires_at' => $expiresAt
            ]
        );

        // Send verification email
        $this->sendVerificationEmail($email, $user->first_name, $token);

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent successfully!'
        ]);
    }

    /**
     * Send verification email.
     */
    protected function sendVerificationEmail($email, $firstName, $token)
    {
        // You can implement your email service here
        \Log::info("Verification email sent to {$email} with token: {$token}");

        // Example email implementation:
        /*
        Mail::send('emails.verify', [
            'name' => $firstName,
            'token' => $token
        ], function ($message) use ($email, $firstName) {
            $message->to($email, $firstName)
                    ->subject('Verify Your Email Address');
        });
        */
    }

    /**
     * Get registration statistics.
     */
    public function getRegistrationStats()
    {
        $stats = [
            'total_registrations' => DB::table('users')->count(),
            'doctors_registered' => DB::table('users')->where('role', 'doctor')->count(),
            'patients_registered' => DB::table('users')->where('role', 'patient')->count(),
            'this_month_registrations' => DB::table('users')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'verified_users' => DB::table('users')
                ->whereNotNull('email_verified_at')
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
