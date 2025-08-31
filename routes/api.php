<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

// Controllers
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\AiBookingController;
use App\Http\Controllers\AiProxyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\PaymentManagementController;
use App\Http\Controllers\Admin\AppointmentManagementController;
use App\Http\Controllers\Admin\SettingsController;

// Doctor Controllers
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\ProfileController as DoctorProfileController;
use App\Http\Controllers\Doctor\WalletController as DoctorWalletController;

// Patient Controllers
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\DoctorController as PatientDoctorController;
use App\Http\Controllers\Patient\ProfileController as PatientProfileController;
use App\Http\Controllers\Patient\WalletController as PatientWalletController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================================================
// PUBLIC API ROUTES (No authentication required)
// ============================================================================

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// Public data endpoints
Route::get('/specialties', [DoctorController::class, 'getSpecialties']);
Route::get('/featured-doctors', [DoctorController::class, 'getFeaturedDoctors']);
Route::get('/public-stats', [DoctorController::class, 'getPublicStats']);

// ============================================================================
// AUTHENTICATION API ROUTES
// ============================================================================

// Guest routes (not authenticated)
Route::middleware('guest')->group(function () {
    // Login
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/login/username', [LoginController::class, 'loginWithUsername']);

    // Registration
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/check-username', [RegisterController::class, 'checkUsername']);
    Route::post('/check-email', [RegisterController::class, 'checkEmail']);
    Route::get('/registration-requirements', [RegisterController::class, 'getRegistrationRequirements']);

    // Password reset
    Route::post('/forgot-password', [LoginController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [LoginController::class, 'resetPassword']);

    // Email verification
    Route::get('/verify-email/{token}', [RegisterController::class, 'verifyEmail']);
    Route::post('/resend-verification', [RegisterController::class, 'resendVerification']);
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout']);

    // Auth checks
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    Route::get('/check-auth', [LoginController::class, 'checkAuth']);
    Route::post('/refresh', [LoginController::class, 'refresh']);
    Route::get('/login-history', [LoginController::class, 'getLoginHistory']);

    // Password confirmation
    Route::post('/confirm-password', [LoginController::class, 'resetPassword']);
});

// Web authenticated routes (for frontend use)
Route::middleware('web')->group(function () {
    Route::get('/user/profile', function (Request $request) {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'role' => $user->role
            ]
        ]);
    });
});

// ============================================================================
// AI ASSISTANT API ROUTES
// ============================================================================

// AI Assistant routes (public for testing)
Route::prefix('ai')->group(function () {
    // Test route
    Route::get('/test', function() {
        return response()->json(['status' => 'ok', 'message' => 'AI routes working']);
    })->name('test');

    // Proxy routes to Flask service (public for testing)
    Route::get('/proxy/health', [AiProxyController::class, 'health'])->name('proxy.health');
    Route::post('/proxy/process', [AiProxyController::class, 'process'])->name('proxy.process');
});

// AI booking route (requires Sanctum authentication)
Route::middleware('auth:sanctum')->prefix('ai')->group(function () {
    Route::post('/proxy/book-appointment', [AiProxyController::class, 'bookAppointment'])->name('proxy.book-appointment');
});

// AI Assistant routes (authenticated)
Route::middleware('auth:sanctum')->prefix('ai')->group(function () {
    Route::get('/health', [AiAssistantController::class, 'health']);
    Route::post('/chat', [AiAssistantController::class, 'chat']);
    Route::post('/voice', [AiAssistantController::class, 'processVoiceInput']);
    Route::post('/book-appointment', [AiAssistantController::class, 'bookAppointment']);
    Route::post('/medical-advice', [AiAssistantController::class, 'getMedicalAdvice']);
    Route::post('/medication-info', [AiAssistantController::class, 'getMedicationInfo']);
    Route::get('/history', [AiAssistantController::class, 'getHistory']);
    Route::delete('/history', [AiAssistantController::class, 'clearHistory']);
    Route::get('/suggestions', [AiAssistantController::class, 'getSuggestions']);
});

// ============================================================================
// ADMIN API ROUTES
// ============================================================================

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/stats', [AdminDashboardController::class, 'getStats']);
    Route::get('/chart-data', [AdminDashboardController::class, 'getChartData']);
    Route::get('/recent-activities', [AdminDashboardController::class, 'getRecentActivities']);
    Route::get('/export-data', [AdminDashboardController::class, 'exportData']);
    Route::get('/system-health', [AdminDashboardController::class, 'systemHealth']);

    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', [UserManagementController::class, 'index']);
        Route::post('/', [UserManagementController::class, 'store']);
        Route::get('/{id}', [UserManagementController::class, 'show']);
        Route::put('/{id}', [UserManagementController::class, 'update']);
        Route::delete('/{id}', [UserManagementController::class, 'destroy']);
        Route::post('/bulk-action', [UserManagementController::class, 'bulkAction']);
        Route::get('/export', [UserManagementController::class, 'export']);
    });

    // Payment Management
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentManagementController::class, 'index']);
        Route::get('/{id}', [PaymentManagementController::class, 'show']);
        Route::put('/{id}/status', [PaymentManagementController::class, 'updateStatus']);
        Route::post('/{id}/refund', [PaymentManagementController::class, 'processRefund']);
        Route::get('/stats', [PaymentManagementController::class, 'getStats']);
        Route::get('/chart-data', [PaymentManagementController::class, 'getChartData']);
        Route::get('/export', [PaymentManagementController::class, 'export']);
        Route::get('/webhooks', [PaymentManagementController::class, 'getWebhooks']);
        Route::post('/webhooks/{id}/retry', [PaymentManagementController::class, 'retryWebhook']);
        Route::get('/financial-report', [PaymentManagementController::class, 'getFinancialReport']);
    });

    // Appointment Management
    Route::prefix('appointments')->group(function () {
        Route::get('/', [AppointmentManagementController::class, 'index']);
        Route::post('/', [AppointmentManagementController::class, 'store']);
        Route::get('/{id}', [AppointmentManagementController::class, 'show']);
        Route::put('/{id}', [AppointmentManagementController::class, 'update']);
        Route::delete('/{id}', [AppointmentManagementController::class, 'destroy']);
        Route::post('/bulk-action', [AppointmentManagementController::class, 'bulkAction']);
        Route::get('/stats', [AppointmentManagementController::class, 'getStats']);
        Route::get('/chart-data', [AppointmentManagementController::class, 'getChartData']);
        Route::get('/available-slots', [AppointmentManagementController::class, 'getAvailableSlots']);
        Route::get('/export', [AppointmentManagementController::class, 'export']);
    });

    // Settings Management
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index']);

        // Specialties
        Route::get('/specialties', [SettingsController::class, 'specialties']);
        Route::post('/specialties', [SettingsController::class, 'storeSpecialty']);
        Route::put('/specialties/{id}', [SettingsController::class, 'updateSpecialty']);
        Route::delete('/specialties/{id}', [SettingsController::class, 'deleteSpecialty']);

        // Plans
        Route::get('/plans', [SettingsController::class, 'plans']);
        Route::post('/plans', [SettingsController::class, 'storePlan']);
        Route::put('/plans/{id}', [SettingsController::class, 'updatePlan']);
        Route::delete('/plans/{id}', [SettingsController::class, 'deletePlan']);

        // Plan Features
        Route::get('/plans/{planId}/features', [SettingsController::class, 'planFeatures']);
        Route::post('/plans/{planId}/features', [SettingsController::class, 'storePlanFeature']);
        Route::put('/features/{featureId}', [SettingsController::class, 'updatePlanFeature']);
        Route::delete('/features/{featureId}', [SettingsController::class, 'deletePlanFeature']);

        // System Settings
        Route::get('/system', [SettingsController::class, 'systemSettings']);
        Route::put('/system', [SettingsController::class, 'updateSystemSettings']);

        // Pricing Settings
        Route::get('/pricing', [SettingsController::class, 'pricingSettings']);
        Route::put('/pricing/specialty/{specialtyId}', [SettingsController::class, 'updateSpecialtyPricing']);
        Route::put('/pricing/doctor/{doctorId}', [SettingsController::class, 'updateDoctorPricing']);

        // System Operations
        Route::post('/clear-cache', [SettingsController::class, 'clearCache']);
        Route::get('/system-stats', [SettingsController::class, 'getSystemStats']);
    });
});

// ============================================================================
// DOCTOR API ROUTES
// ============================================================================

Route::middleware(['auth:sanctum', 'doctor'])->prefix('doctor')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DoctorDashboardController::class, 'index']);
    Route::get('/stats', [DoctorDashboardController::class, 'getStats']);
    Route::get('/chart-data', [DoctorDashboardController::class, 'getChartData']);
    Route::get('/today-schedule', [DoctorDashboardController::class, 'getTodaySchedule']);
    Route::get('/upcoming-appointments', [DoctorDashboardController::class, 'getUpcomingAppointments']);
    Route::get('/recent-activities', [DoctorDashboardController::class, 'getRecentActivities']);
    Route::get('/profile-summary', [DoctorDashboardController::class, 'getProfileSummary']);
    Route::get('/export-data', [DoctorDashboardController::class, 'exportData']);

    // Appointments
    Route::prefix('appointments')->group(function () {
        Route::get('/', [DoctorAppointmentController::class, 'index']);
        Route::get('/{id}', [DoctorAppointmentController::class, 'show']);
        Route::put('/{id}/status', [DoctorAppointmentController::class, 'updateStatus']);
        Route::get('/today', [DoctorAppointmentController::class, 'today']);
        Route::get('/upcoming', [DoctorAppointmentController::class, 'upcoming']);
        Route::get('/past', [DoctorAppointmentController::class, 'past']);
        Route::get('/calendar', [DoctorAppointmentController::class, 'calendar']);
        Route::get('/available-slots', [DoctorAppointmentController::class, 'getAvailableSlots']);
        Route::get('/stats', [DoctorAppointmentController::class, 'getStats']);
        Route::get('/export', [DoctorAppointmentController::class, 'export']);
        Route::get('/patient/{patientId}/history', [DoctorAppointmentController::class, 'getPatientHistory']);
        Route::post('/{id}/notes', [DoctorAppointmentController::class, 'addNotes']);
    });

    // Patients
    Route::prefix('patients')->group(function () {
        Route::get('/', [PatientController::class, 'index']);
        Route::get('/{id}', [PatientController::class, 'show']);
        Route::get('/{patientId}/appointment-history', [PatientController::class, 'getAppointmentHistory']);
        Route::get('/{patientId}/payment-history', [PatientController::class, 'getPaymentHistory']);
        Route::get('/{patientId}/stats', [PatientController::class, 'getPatientStats']);
        Route::get('/recent', [PatientController::class, 'getRecentPatients']);
        Route::get('/top', [PatientController::class, 'getTopPatients']);
        Route::get('/search', [PatientController::class, 'search']);
        Route::get('/demographics', [PatientController::class, 'getDemographics']);
        Route::get('/export', [PatientController::class, 'export']);
        Route::get('/{patientId}/medical-history', [PatientController::class, 'getMedicalHistory']);
        Route::post('/{patientId}/notes', [PatientController::class, 'addNotes']);
        Route::get('/dashboard-stats', [PatientController::class, 'getDashboardStats']);
    });

    // Profile Management
    Route::prefix('profile')->group(function () {
        Route::get('/', [DoctorProfileController::class, 'index']);
        Route::put('/update', [DoctorProfileController::class, 'updateProfile']);
        Route::put('/account', [DoctorProfileController::class, 'updateAccount']);
        Route::put('/password', [DoctorProfileController::class, 'changePassword']);
        Route::put('/working-hours', [DoctorProfileController::class, 'updateWorkingHours']);
        Route::get('/working-hours', [DoctorProfileController::class, 'getWorkingHours']);
        Route::post('/image', [DoctorProfileController::class, 'updateProfileImage']);
        Route::get('/stats', [DoctorProfileController::class, 'getProfileStats']);
        Route::post('/availability', [DoctorProfileController::class, 'toggleAvailability']);
        Route::get('/reviews', [DoctorProfileController::class, 'getReviews']);
        Route::get('/export', [DoctorProfileController::class, 'exportProfile']);
    });

    // Wallet Management
    Route::prefix('wallet')->group(function () {
        Route::get('/', [DoctorWalletController::class, 'index']);
        Route::get('/balance', [DoctorWalletController::class, 'getWallet']);
        Route::get('/transactions', [DoctorWalletController::class, 'getTransactions']);
        Route::get('/stats', [DoctorWalletController::class, 'getStats']);
        Route::post('/withdrawal', [DoctorWalletController::class, 'requestWithdrawal']);
        Route::get('/earnings', [DoctorWalletController::class, 'getEarnings']);
        Route::get('/earnings-stats', [DoctorWalletController::class, 'getEarningsStats']);
        Route::get('/monthly-earnings', [DoctorWalletController::class, 'getMonthlyEarnings']);
        Route::get('/withdrawals', [DoctorWalletController::class, 'getWithdrawals']);
        Route::delete('/withdrawals/{id}', [DoctorWalletController::class, 'cancelWithdrawal']);
        Route::get('/transactions/{id}', [DoctorWalletController::class, 'getTransaction']);
        Route::get('/export', [DoctorWalletController::class, 'export']);
    });
});

// ============================================================================
// PATIENT API ROUTES
// ============================================================================

Route::middleware(['auth:sanctum', 'patient'])->prefix('patient')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PatientDashboardController::class, 'index']);
    Route::get('/stats', [PatientDashboardController::class, 'getStats']);
    Route::get('/chart-data', [PatientDashboardController::class, 'getChartData']);
    Route::get('/today-appointments', [PatientDashboardController::class, 'getTodayAppointments']);
    Route::get('/upcoming-appointments', [PatientDashboardController::class, 'getUpcomingAppointments']);
    Route::get('/recent-activities', [PatientDashboardController::class, 'getRecentActivities']);
    Route::get('/profile-summary', [PatientDashboardController::class, 'getProfileSummary']);
    Route::get('/favorite-doctors', [PatientDashboardController::class, 'getFavoriteDoctors']);
    Route::get('/health-summary', [PatientDashboardController::class, 'getHealthSummary']);
    Route::get('/export-data', [PatientDashboardController::class, 'exportData']);

    // Appointments
    Route::prefix('appointments')->group(function () {
        Route::get('/', [PatientAppointmentController::class, 'index']);
        Route::get('/{id}', [PatientAppointmentController::class, 'show']);
        Route::post('/', [PatientAppointmentController::class, 'store']);
        Route::delete('/{id}', [PatientAppointmentController::class, 'cancel']);
        Route::get('/available-doctors', [PatientAppointmentController::class, 'getAvailableDoctors']);
        Route::get('/available-slots', [PatientAppointmentController::class, 'getAvailableSlots']);
        Route::get('/upcoming', [PatientAppointmentController::class, 'upcoming']);
        Route::get('/past', [PatientAppointmentController::class, 'past']);
        Route::get('/stats', [PatientAppointmentController::class, 'getStats']);
        Route::get('/export', [PatientAppointmentController::class, 'export']);
        Route::post('/{id}/rate', [PatientAppointmentController::class, 'rate']);
    });

    // Doctor Browsing
    Route::prefix('doctors')->group(function () {
        Route::get('/', [PatientDoctorController::class, 'index']);
        Route::get('/{id}', [PatientDoctorController::class, 'show']);
        Route::get('/search', [PatientDoctorController::class, 'search']);
        Route::get('/available', [PatientDoctorController::class, 'getAvailableDoctors']);
        Route::get('/{doctorId}/working-hours', [PatientDoctorController::class, 'getWorkingHours']);
        Route::get('/{doctorId}/available-slots', [PatientDoctorController::class, 'getAvailableSlots']);
        Route::get('/{doctorId}/ratings', [PatientDoctorController::class, 'getRatings']);
        Route::get('/{doctorId}/stats', [PatientDoctorController::class, 'getStats']);
        Route::get('/top-rated', [PatientDoctorController::class, 'getTopRated']);
        Route::get('/specialty/{specialtyId}', [PatientDoctorController::class, 'getBySpecialty']);
        Route::get('/favorites', [PatientDoctorController::class, 'getFavoriteDoctors']);
        Route::get('/recently-visited', [PatientDoctorController::class, 'getRecentlyVisited']);
    });

    // Profile Management
    Route::prefix('profile')->group(function () {
        Route::get('/', [PatientProfileController::class, 'index']);
        Route::put('/update', [PatientProfileController::class, 'updateProfile']);
        Route::put('/account', [PatientProfileController::class, 'updateAccount']);
        Route::put('/password', [PatientProfileController::class, 'changePassword']);
        Route::post('/image', [PatientProfileController::class, 'uploadImage']);
        Route::get('/completion', [PatientProfileController::class, 'getProfileCompletion']);
        Route::get('/export', [PatientProfileController::class, 'export']);
    });

    // Wallet Management
    Route::prefix('wallet')->group(function () {
        Route::get('/', [PatientWalletController::class, 'index']);
        Route::get('/balance', [PatientWalletController::class, 'getWallet']);
        Route::get('/transactions', [PatientWalletController::class, 'getTransactions']);
        Route::get('/stats', [PatientWalletController::class, 'getStats']);
        Route::post('/add-funds', [PatientWalletController::class, 'addFunds']);
        Route::get('/payment-history', [PatientWalletController::class, 'getPaymentHistory']);
        Route::get('/payment-stats', [PatientWalletController::class, 'getPaymentStats']);
        Route::get('/monthly-spending', [PatientWalletController::class, 'getMonthlySpending']);
        Route::get('/transactions/{id}', [PatientWalletController::class, 'getTransaction']);
        Route::get('/payments/{id}', [PatientWalletController::class, 'getPayment']);
        Route::post('/payments/{paymentId}/refund', [PatientWalletController::class, 'requestRefund']);
        Route::get('/export', [PatientWalletController::class, 'export']);
    });
});

// ============================================================================
// GENERAL API ROUTES (All authenticated users)
// ============================================================================

Route::middleware('auth:sanctum')->group(function () {
    // Appointments
    Route::prefix('appointments')->group(function () {
        Route::get('/', [AppointmentController::class, 'index']);
        Route::post('/', [AppointmentController::class, 'store']);
        Route::get('/{id}', [AppointmentController::class, 'show']);
        Route::put('/{id}', [AppointmentController::class, 'update']);
        Route::delete('/{id}', [AppointmentController::class, 'destroy']);
        Route::get('/{id}/available-slots', [AppointmentController::class, 'getAvailableSlots']);
        Route::put('/{id}/status', [AppointmentController::class, 'updateStatus']);
        Route::get('/stats', [AppointmentController::class, 'getStats']);
        Route::get('/export', [AppointmentController::class, 'export']);
    });

    // Doctors
    Route::prefix('doctors')->group(function () {
        Route::get('/', [DoctorController::class, 'index']);
        Route::post('/', [DoctorController::class, 'store']);
        Route::get('/{id}', [DoctorController::class, 'show']);
        Route::put('/{id}', [DoctorController::class, 'update']);
        Route::delete('/{id}', [DoctorController::class, 'destroy']);
        Route::get('/{id}/working-hours', [DoctorController::class, 'getWorkingHours']);
        Route::put('/{id}/working-hours', [DoctorController::class, 'updateWorkingHours']);
        Route::get('/{id}/reviews', [DoctorController::class, 'getReviews']);
        Route::get('/search', [DoctorController::class, 'search']);
        Route::get('/{id}/stats', [DoctorController::class, 'getStats']);
        Route::get('/export', [DoctorController::class, 'export']);
    });

    // Payments
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/', [PaymentController::class, 'store']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::put('/{id}', [PaymentController::class, 'update']);
        Route::post('/{id}/process', [PaymentController::class, 'processPayment']);
        Route::post('/{id}/refund', [PaymentController::class, 'processRefund']);
        Route::get('/stats', [PaymentController::class, 'getStats']);
        Route::get('/export', [PaymentController::class, 'export']);
    });
});

// ============================================================================
// AI SERVICE PROXY ROUTES (No authentication required for health check)
// ============================================================================

// AI Service proxy routes
Route::prefix('ai')->group(function () {
    Route::get('/health', function () {
        try {
            $response = Http::timeout(5)->get('http://127.0.0.1:5005/health');
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Flask service unavailable',
                'message' => $e->getMessage(),
                'status' => 'unhealthy'
            ], 503);
        }
    });

    // Helper function to detect doctor-related queries
function isDoctorRelatedQuery($text) {
    $doctorKeywords = ['doctor', 'specialist', 'find', 'look for', 'search for', 'need a', 'want a', 'see a'];
    $textLower = strtolower($text);
    return collect($doctorKeywords)->contains(function($keyword) use ($textLower) {
        return strpos($textLower, $keyword) !== false;
    });
}

Route::post('/process', function (Request $request) {
        try {
            // Get the text from the request
            $text = $request->input('text');

            if (!$text) {
                return response()->json([
                    'error' => 'Missing text parameter',
                    'message' => 'Please provide a text message to process'
                ], 400);
            }

            // Call Flask service
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('http://127.0.0.1:5006/process', ['text' => $text]);

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Flask service error',
                    'message' => 'AI service is temporarily unavailable'
                ], 503);
            }

            $aiData = $response->json();

                        // Add database-specific enhancements
            $enhancedData = $aiData;

            // If it's an appointment booking intent or doctor search, add available doctors
            if (($aiData['intent']['intent'] ?? '') === 'book_appointment' ||
                ($aiData['intent']['intent'] ?? '') === 'search_doctors' ||
                isDoctorRelatedQuery($text)) {

                // Get specialty hint for filtering
                $specialtyHint = $aiData['intent']['specialty_hint'] ?? null;
                $textLower = strtolower($text);

                // Enhanced specialty detection for common terms
                $specialtyMapping = [
                    'eye' => 'ophthalmology',
                    'ophthalmologist' => 'ophthalmology',
                    'vision' => 'ophthalmology',
                    'heart' => 'cardiology',
                    'cardiologist' => 'cardiology',
                    'cardiac' => 'cardiology',
                    'brain' => 'neurology',
                    'neurologist' => 'neurology',
                    'child' => 'pediatrics',
                    'pediatrician' => 'pediatrics',
                    'pregnancy' => 'obstetrics',
                    'gynecologist' => 'obstetrics',
                    'obstetrician' => 'obstetrics',
                    'teeth' => 'dentistry',
                    'dentist' => 'dentistry',
                    'dental' => 'dentistry',
                    'skin' => 'dermatology',
                    'dermatologist' => 'dermatology'
                ];

                // Determine specialty to filter by
                $targetSpecialty = null;
                if ($specialtyHint) {
                    $targetSpecialty = $specialtyHint;
                } else {
                    foreach ($specialtyMapping as $keyword => $specialty) {
                        if (strpos($textLower, $keyword) !== false) {
                            $targetSpecialty = $specialty;
                            break;
                        }
                    }
                }

                $query = \DB::table('doctors')
                    ->join('users', 'doctors.user_id', '=', 'users.id')
                    ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
                    ->where('doctors.is_active', true)
                    ->where('users.role', 'doctor')
                    ->where('users.status', 'active')
                    ->select(
                        'doctors.id',
                        'doctors.name',
                        'doctors.consultation_fee',
                        'doctors.rating',
                        'doctors.experience_years',
                        'doctors.description',
                        'specialties.name_en as specialty'
                    );

                // Filter by specialty if detected
                if ($targetSpecialty) {
                    $query->where('specialties.name_en', 'like', '%' . $targetSpecialty . '%');
                }

                $doctors = $query->orderBy('doctors.rating', 'desc')
                    ->orderBy('doctors.experience_years', 'desc')
                    ->limit(5)
                    ->get();

                $enhancedData['available_doctors'] = $doctors;

                // Override intent if it's a doctor-related query
                if (isDoctorRelatedQuery($text) && ($aiData['intent']['intent'] ?? '') === 'general_inquiry') {
                    $enhancedData['intent']['intent'] = 'search_doctors';
                    $enhancedData['intent']['confidence'] = 0.90;
                }

                // Enhance the response message for doctor queries
                if (($aiData['intent']['intent'] ?? '') === 'book_appointment') {
                    if ($targetSpecialty) {
                        $specialtyDisplay = ucfirst($targetSpecialty);
                        $enhancedData['response_message'] = "I found some excellent {$specialtyDisplay} specialists for your appointment. Here are the available options:";
                    } else {
                        $enhancedData['response_message'] = "I found some excellent doctors for your appointment. Here are the available options:";
                    }
                } else {
                    if ($targetSpecialty) {
                        $specialtyDisplay = ucfirst($targetSpecialty);
                        $enhancedData['response_message'] = "I found some excellent {$specialtyDisplay} specialists for you. Here are the available options:";
                    } else {
                        $enhancedData['response_message'] = "I found some excellent doctors for you. Here are the available options:";
                    }
                }

                // Add formatted doctor list for better display
                $formattedDoctors = [];
                $doctorCount = $doctors->count();

                foreach ($doctors as $index => $doctor) {
                    // Clean up doctor name (remove duplicate "Dr.")
                    $cleanName = str_replace('Dr. Dr.', 'Dr.', $doctor->name);
                    $cleanName = str_replace('Dr. Dr. ', 'Dr. ', $cleanName);

                    // Format rating display
                    $ratingDisplay = $doctor->rating > 0 ? number_format($doctor->rating, 1) : 'New';
                    $ratingText = $doctor->rating > 0 ? "Rating: {$ratingDisplay}/5" : "New Doctor";

                    // Format consultation fee
                    $feeDisplay = $doctor->consultation_fee > 0 ? "\$$" . number_format($doctor->consultation_fee, 0) : "Contact for pricing";

                    $formattedDoctors[] = [
                        'id' => $doctor->id,
                        'name' => $cleanName,
                        'specialty' => $doctor->specialty,
                        'rating' => $doctor->rating,
                        'experience_years' => $doctor->experience_years,
                        'consultation_fee' => $doctor->consultation_fee,
                        'description' => $doctor->description,
                        'display_text' => "{$cleanName} - {$doctor->specialty} ({$ratingText}, Experience: {$doctor->experience_years} years, Fee: {$feeDisplay})",
                        'short_display' => "{$cleanName} - {$doctor->specialty}",
                        'list_number' => $index + 1
                    ];
                }
                $enhancedData['formatted_doctors'] = $formattedDoctors;
                $enhancedData['doctor_count'] = $doctorCount;

                // Add helpful suggestions based on intent
                if (($aiData['intent']['intent'] ?? '') === 'book_appointment') {
                    $enhancedData['suggestions'] = [
                        "Select a doctor from the list above to proceed with booking",
                        "Check doctor availability and consultation fees",
                        "Choose your preferred appointment date and time",
                        "Review doctor profiles and experience before booking"
                    ];
                } else {
                    $enhancedData['suggestions'] = [
                        "Select a doctor from the list above to book an appointment",
                        "Ask about specific specialties or conditions",
                        "Check consultation fees and availability",
                        "View doctor profiles and experience"
                    ];
                }
            }

            // If it's a medical inquiry or general health question, add relevant health tips
            if (($aiData['intent']['intent'] ?? '') === 'medical_inquiry' ||
                ($aiData['intent']['intent'] ?? '') === 'general_inquiry') {

                // Get specialty-specific tips if available
                $specialtyHint = $aiData['intent']['specialty_hint'] ?? null;
                $category = $specialtyHint ? strtolower($specialtyHint) : 'general';

                $healthTips = \DB::table('health_tips')
                    ->where('category', $category)
                    ->where('is_active', true)
                    ->select('tip', 'category')
                    ->limit(3)
                    ->get();

                // Fallback to general tips if no specialty-specific tips found
                if ($healthTips->count() === 0) {
                    $healthTips = \DB::table('health_tips')
                        ->where('category', 'general')
                        ->where('is_active', true)
                        ->select('tip', 'category')
                        ->limit(3)
                        ->get();
                }

                if ($healthTips->count() > 0) {
                    $enhancedData['database_health_tips'] = $healthTips;
                    $enhancedData['health_tips_message'] = "Here are some helpful health tips for you:";
                }
            }

            // Save conversation to database if user is authenticated
            if (auth()->check()) {
                $user = auth()->user();
                \DB::table('ai_conversations')->insert([
                    'user_id' => $user->id,
                    'user_message' => $text,
                    'ai_response' => json_encode($enhancedData),
                    'intent' => $enhancedData['intent']['intent'] ?? 'general',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Add response metadata
            $enhancedData['response_metadata'] = [
                'timestamp' => now()->toISOString(),
                'processing_time' => microtime(true) - LARAVEL_START,
                'version' => '1.0.0',
                'features' => [
                    'ai_intent_detection' => true,
                    'doctor_search' => isset($enhancedData['available_doctors']),
                    'health_tips' => isset($enhancedData['database_health_tips']),
                    'formatted_response' => true
                ]
            ];

            return response()->json($enhancedData);

        } catch (\Exception $e) {
            \Log::error('AI Process Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Processing error',
                'message' => 'Unable to process your request at this time'
            ], 500);
        }
    });
});

// AI Booking routes moved to web routes for better session handling

// ============================================================================
// WEBHOOK ROUTES (No authentication required)
// ============================================================================

// Payment webhooks
Route::prefix('webhooks')->group(function () {
    Route::post('/stripe', function (Request $request) {
        // Handle Stripe webhook
        return response()->json(['status' => 'received']);
    });

    Route::post('/paypal', function (Request $request) {
        // Handle PayPal webhook
        return response()->json(['status' => 'received']);
    });
});

// ============================================================================
// FALLBACK ROUTES
// ============================================================================

// Handle 404 errors for API
Route::fallback(function () {
    return response()->json([
        'error' => 'Endpoint not found',
        'message' => 'The requested API endpoint does not exist',
        'status' => 404
    ], 404);
});
