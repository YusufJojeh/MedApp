<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check for new device login and send security notification
            $this->checkNewDeviceLogin($user, $request);

            // Redirect based on role
            switch ($user->role) {
                case 'admin':
                    return redirect()->intended(route('admin.dashboard'));
                case 'doctor':
                    return redirect()->intended(route('doctor.dashboard'));
                case 'patient':
                    return redirect()->intended(route('patient.dashboard'));
                default:
                    return redirect()->intended(route('home'));
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Check for new device login and send security notification
     */
    private function checkNewDeviceLogin($user, Request $request)
    {
        $userAgent = $request->header('User-Agent');
        $ipAddress = $request->ip();
        
        // Check if this is a new device/IP combination
        $lastLogin = DB::table('user_login_history')
            ->where('user_id', $user->id)
            ->where('ip_address', $ipAddress)
            ->where('user_agent', $userAgent)
            ->first();

        if (!$lastLogin) {
            // This is a new device/IP, send security notification
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->createNotification(
                $user->id,
                'security',
                'New Login Detected',
                'A new login was detected from a new device or location. If this wasn\'t you, please contact support immediately.',
                [
                    'icon' => '🔒',
                    'color' => 'red',
                    'priority' => 'high',
                    'data' => [
                        'ip_address' => $ipAddress,
                        'user_agent' => $userAgent,
                        'timestamp' => now()
                    ]
                ]
            );
        }

        // Log this login attempt
        DB::table('user_login_history')->insert([
            'user_id' => $user->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'login_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
