<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }
            return redirect()->route('login');
        }

        // Check if user has admin role
        if (Auth::user()->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin access required'
                ], 403);
            }

            // Redirect based on user role
            $role = Auth::user()->role;
            switch ($role) {
                case 'doctor':
                    return redirect()->route('doctor.dashboard');
                case 'patient':
                    return redirect()->route('patient.dashboard');
                default:
                    return redirect()->route('home');
            }
        }

        return $next($request);
    }
}
