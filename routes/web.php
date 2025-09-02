<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PatientManagementController as PatientManagementController;
use App\Http\Controllers\Admin\UserManagementController as UserManagementController;
use App\Http\Controllers\Admin\AppointmentManagementController as AppointmentManagementController;
use App\Http\Controllers\Admin\PaymentManagementController as PaymentManagementController;
use App\Http\Controllers\Admin\SettingsController as SettingsController;
use App\Http\Controllers\Admin\ProfileController as ProfileController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\PatientController as DoctorPatientController;
use App\Http\Controllers\Doctor\ScheduleController as DoctorScheduleController;
use App\Http\Controllers\Doctor\ProfileController as DoctorProfileController;
use App\Http\Controllers\Doctor\ReviewController as DoctorReviewController;
use App\Http\Controllers\Doctor\ReportController as DoctorReportController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\DoctorController as PatientDoctorController;
use App\Http\Controllers\Patient\ProfileController as PatientProfileController;
use App\Http\Controllers\Patient\WalletController as PatientWalletController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AiProxyController;
use App\Http\Controllers\NotificationController;

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');
Route::get('/faq', function () {
    return view('faq');
})->name('faq');
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/services', function () {
    return view('services');
})->name('services');

// AI Assistant routes
Route::get('/ai', function () {
    return view('ai-assistant.index', [
        'conversationHistory' => collect([]), // Empty collection for now
        'user' => auth()->user()
    ]);
})->name('ai.assistant');

Route::get('/ai/index', function () {
    return view('ai-assistant.index', [
        'conversationHistory' => collect([]), // Empty collection for now
        'user' => auth()->user()
    ]);
})->name('ai.index');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.submit');
});

// ============================================================================
// NOTIFICATION ROUTES (for authenticated users)
// ============================================================================

Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    Route::get('/recent', [NotificationController::class, 'getRecent'])->name('recent');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/{id}/unread', [NotificationController::class, 'markAsUnread'])->name('mark-unread');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/delete-read', [NotificationController::class, 'deleteRead'])->name('delete-read');
    Route::get('/settings', [NotificationController::class, 'showSettingsPage'])->name('settings');
    Route::get('/settings/api', [NotificationController::class, 'getSettings'])->name('settings.api');
    Route::post('/settings', [NotificationController::class, 'updateSettings'])->name('update-settings');
    Route::post('/test', [NotificationController::class, 'testNotification'])->name('test');
    Route::get('/stats', [NotificationController::class, 'getStats'])->name('stats');
    Route::get('/test-page', function () {
        return view('notifications.test');
    })->name('test-page');
});

// ============================================================================
// ADMIN ROUTES
// ============================================================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [AdminDashboardController::class, 'getStats'])->name('stats');
    Route::get('/chart-data', [AdminDashboardController::class, 'getChartData'])->name('chart-data');
    Route::get('/recent-activities', [AdminDashboardController::class, 'getRecentActivities'])->name('recent-activities');

    // Patients Management
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('/', [PatientManagementController::class, 'index'])->name('index');
        Route::get('/create', [PatientManagementController::class, 'create'])->name('create');
        Route::post('/', [PatientManagementController::class, 'store'])->name('store');
        Route::get('/{id}', [PatientManagementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PatientManagementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PatientManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [PatientManagementController::class, 'destroy'])->name('destroy');
        Route::get('/search', [PatientManagementController::class, 'search'])->name('search');
        Route::get('/export', [PatientManagementController::class, 'export'])->name('export');
    });

    // Users Management (General Users)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{id}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [UserManagementController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/{id}/activate', [UserManagementController::class, 'activate'])->name('activate');
        Route::post('/{id}/deactivate', [UserManagementController::class, 'deactivate'])->name('deactivate');
        Route::get('/export', [UserManagementController::class, 'export'])->name('export');
    });

    // Doctors Management (via UserManagementController)
    Route::prefix('doctors')->name('doctors.')->group(function () {
        Route::get('/', [UserManagementController::class, 'doctors'])->name('index');
        Route::get('/create', [UserManagementController::class, 'createDoctor'])->name('create');
        Route::post('/', [UserManagementController::class, 'storeDoctor'])->name('store');
        Route::get('/{id}', [UserManagementController::class, 'showDoctor'])->name('show');
        Route::get('/{id}/edit', [UserManagementController::class, 'editDoctor'])->name('edit');
        Route::put('/{id}', [UserManagementController::class, 'updateDoctor'])->name('update');
        Route::delete('/{id}', [UserManagementController::class, 'destroyDoctor'])->name('destroy');
        Route::post('/{id}/verify', [UserManagementController::class, 'verifyDoctor'])->name('verify');
        Route::post('/{id}/unverify', [UserManagementController::class, 'unverifyDoctor'])->name('unverify');
        Route::post('/{id}/activate', [UserManagementController::class, 'activateDoctor'])->name('activate');
        Route::post('/{id}/deactivate', [UserManagementController::class, 'deactivateDoctor'])->name('deactivate');
        Route::post('/bulk-action', [UserManagementController::class, 'bulkActionDoctors'])->name('bulk-action');
        Route::get('/export', [UserManagementController::class, 'exportDoctors'])->name('export');
        Route::get('/{id}/schedule', [UserManagementController::class, 'doctorSchedule'])->name('schedule');
    });

    // Appointments Management
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [AppointmentManagementController::class, 'index'])->name('index');
        Route::get('/create', [AppointmentManagementController::class, 'create'])->name('create');
        Route::post('/', [AppointmentManagementController::class, 'store'])->name('store');
        Route::get('/{id}', [AppointmentManagementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AppointmentManagementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AppointmentManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [AppointmentManagementController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [AppointmentManagementController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/stats', [AppointmentManagementController::class, 'getStats'])->name('stats');
        Route::get('/chart-data', [AppointmentManagementController::class, 'getChartData'])->name('chart-data');
        Route::get('/available-slots', [AppointmentManagementController::class, 'getAvailableSlots'])->name('available-slots');
        Route::get('/export', [AppointmentManagementController::class, 'export'])->name('export');
    });

    // Payments Management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentManagementController::class, 'index'])->name('index');
        Route::get('/{id}', [PaymentManagementController::class, 'show'])->name('show');
        Route::put('/{id}/status', [PaymentManagementController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/refund', [PaymentManagementController::class, 'processRefund'])->name('refund');
        Route::get('/stats', [PaymentManagementController::class, 'getStats'])->name('stats');
        Route::get('/chart-data', [PaymentManagementController::class, 'getChartData'])->name('chart-data');
        Route::get('/export', [PaymentManagementController::class, 'export'])->name('export');
        Route::get('/webhooks', [PaymentManagementController::class, 'getWebhooks'])->name('webhooks');
        Route::post('/webhooks/{id}/retry', [PaymentManagementController::class, 'retryWebhook'])->name('retry-webhook');
        Route::get('/financial-report', [PaymentManagementController::class, 'getFinancialReport'])->name('financial-report');
    });

    // Settings Management
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');

        // Specialties Management
        Route::get('/specialties', [SettingsController::class, 'specialties'])->name('specialties');
        Route::post('/specialties', [SettingsController::class, 'storeSpecialty'])->name('specialties.store');
        Route::put('/specialties/{id}', [SettingsController::class, 'updateSpecialty'])->name('specialties.update');
        Route::delete('/specialties/{id}', [SettingsController::class, 'deleteSpecialty'])->name('specialties.delete');

        // Plans Management
        Route::get('/plans', [SettingsController::class, 'plans'])->name('plans');
        Route::post('/plans', [SettingsController::class, 'storePlan'])->name('plans.store');
        Route::put('/plans/{id}', [SettingsController::class, 'updatePlan'])->name('plans.update');
        Route::delete('/plans/{id}', [SettingsController::class, 'deletePlan'])->name('plans.delete');

        // Plan Features
        Route::get('/plans/{planId}/features', [SettingsController::class, 'planFeatures'])->name('plans.features');
        Route::post('/plans/{planId}/features', [SettingsController::class, 'storePlanFeature'])->name('plans.features.store');
        Route::put('/plans/features/{featureId}', [SettingsController::class, 'updatePlanFeature'])->name('plans.features.update');
        Route::delete('/plans/features/{featureId}', [SettingsController::class, 'deletePlanFeature'])->name('plans.features.delete');

        // System Settings
        Route::get('/system', [SettingsController::class, 'systemSettings'])->name('system');
        Route::put('/system', [SettingsController::class, 'updateSystemSettings'])->name('system.update');

        // Pricing Settings
        Route::get('/pricing', [SettingsController::class, 'pricingSettings'])->name('pricing');
        Route::put('/pricing/specialty/{specialtyId}', [SettingsController::class, 'updateSpecialtyPricing'])->name('pricing.specialty');
        Route::put('/pricing/doctor/{doctorId}', [SettingsController::class, 'updateDoctorPricing'])->name('pricing.doctor');

        // System Maintenance
        Route::post('/cache/clear', [SettingsController::class, 'clearCache'])->name('cache.clear');
        Route::get('/system/stats', [SettingsController::class, 'getSystemStats'])->name('system.stats');
        Route::get('/config', [SettingsController::class, 'config'])->name('config');
        Route::put('/config', [SettingsController::class, 'updateConfig'])->name('config.update');
        Route::get('/maintenance', [SettingsController::class, 'maintenance'])->name('maintenance');
        Route::post('/backup', [SettingsController::class, 'createBackup'])->name('backup');
        Route::post('/database/optimize', [SettingsController::class, 'optimizeDatabase'])->name('database.optimize');

        // API Settings
        Route::get('/api', [SettingsController::class, 'api'])->name('api');
        Route::put('/api', [SettingsController::class, 'updateApi'])->name('api.update');

        // Security Settings
        Route::get('/security', [SettingsController::class, 'security'])->name('security');
        Route::put('/security', [SettingsController::class, 'updateSecurity'])->name('security.update');
    });

    // Admin Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
        Route::put('/change-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::get('/security', [ProfileController::class, 'security'])->name('security');
        Route::put('/security', [ProfileController::class, 'updateSecurity'])->name('update-security');
        Route::get('/activity', [ProfileController::class, 'activity'])->name('activity');
        Route::delete('/image', [ProfileController::class, 'deleteImage'])->name('delete-image');
        Route::get('/stats', [ProfileController::class, 'getStats'])->name('stats');
    });
});

// ============================================================================
// DOCTOR ROUTES
// ============================================================================

Route::middleware(['auth', 'doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    // Dashboard
    Route::get('/', [DoctorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [DoctorDashboardController::class, 'getStats'])->name('stats');
    Route::get('/chart-data', [DoctorDashboardController::class, 'getChartData'])->name('chart-data');
    Route::get('/today-appointments', [DoctorDashboardController::class, 'getTodayAppointments'])->name('today-appointments');
    Route::get('/upcoming-appointments', [DoctorDashboardController::class, 'getUpcomingAppointments'])->name('upcoming-appointments');
    Route::get('/recent-activities', [DoctorDashboardController::class, 'getRecentActivities'])->name('recent-activities');

    // Appointments
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [DoctorAppointmentController::class, 'index'])->name('index');
        Route::get('/{id}', [DoctorAppointmentController::class, 'show'])->name('show');
        Route::put('/{id}/status', [DoctorAppointmentController::class, 'updateStatus'])->name('update-status');
        Route::get('/today', [DoctorAppointmentController::class, 'today'])->name('today');
        Route::get('/upcoming', [DoctorAppointmentController::class, 'upcoming'])->name('upcoming');
        Route::get('/past', [DoctorAppointmentController::class, 'past'])->name('past');
        Route::get('/stats', [DoctorAppointmentController::class, 'getStats'])->name('stats');
    });

    // Patients
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('/', [DoctorPatientController::class, 'index'])->name('index');
        Route::get('/{id}', [DoctorPatientController::class, 'show'])->name('show');
        Route::get('/search', [DoctorPatientController::class, 'search'])->name('search');
    });

    // Schedule
    Route::prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/', [DoctorScheduleController::class, 'index'])->name('index');
        Route::get('/working-hours', [DoctorScheduleController::class, 'getWorkingHours'])->name('working-hours');
        Route::put('/working-hours', [DoctorScheduleController::class, 'updateWorkingHours'])->name('update-working-hours');
        Route::get('/availability', [DoctorScheduleController::class, 'getAvailability'])->name('availability');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [DoctorProfileController::class, 'index'])->name('index');
        Route::put('/update', [DoctorProfileController::class, 'updateProfile'])->name('update');
        Route::put('/password', [DoctorProfileController::class, 'changePassword'])->name('password');
        Route::post('/image', [DoctorProfileController::class, 'uploadImage'])->name('image');
    });

    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [DoctorReviewController::class, 'index'])->name('index');
        Route::get('/{id}', [DoctorReviewController::class, 'show'])->name('show');
        Route::get('/stats', [DoctorReviewController::class, 'getStats'])->name('stats');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [DoctorReportController::class, 'index'])->name('index');
        Route::get('/appointments', [DoctorReportController::class, 'appointments'])->name('appointments');
        Route::get('/revenue', [DoctorReportController::class, 'revenue'])->name('revenue');
        Route::get('/patients', [DoctorReportController::class, 'patients'])->name('patients');
    });
});

// ============================================================================
// PATIENT ROUTES
// ============================================================================

Route::middleware(['auth', 'patient'])->prefix('patient')->name('patient.')->group(function () {
    // Dashboard
    Route::get('/', [PatientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [PatientDashboardController::class, 'getStats'])->name('stats');
    Route::get('/chart-data', [PatientDashboardController::class, 'getChartData'])->name('chart-data');
    Route::get('/today-appointments', [PatientDashboardController::class, 'getTodayAppointments'])->name('today-appointments');
    Route::get('/upcoming-appointments', [PatientDashboardController::class, 'getUpcomingAppointments'])->name('upcoming-appointments');
    Route::get('/recent-activities', [PatientDashboardController::class, 'getRecentActivities'])->name('recent-activities');
    Route::get('/profile-summary', [PatientDashboardController::class, 'getProfileSummary'])->name('profile-summary');
    Route::get('/favorite-doctors', [PatientDashboardController::class, 'getFavoriteDoctors'])->name('favorite-doctors');
    Route::get('/health-summary', [PatientDashboardController::class, 'getHealthSummary'])->name('health-summary');
    Route::get('/export-data', [PatientDashboardController::class, 'exportData'])->name('export-data');

    // Appointments - NEW CLEAN IMPLEMENTATION
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [PatientAppointmentController::class, 'index'])->name('index');
        Route::get('/create', [PatientAppointmentController::class, 'create'])->name('create');
        Route::post('/', [PatientAppointmentController::class, 'store'])->name('store');
        Route::get('/{id}', [PatientAppointmentController::class, 'show'])->name('show');
        Route::delete('/{id}', [PatientAppointmentController::class, 'cancel'])->name('cancel');
        Route::get('/upcoming', [PatientAppointmentController::class, 'upcoming'])->name('upcoming');
        Route::get('/past', [PatientAppointmentController::class, 'past'])->name('past');
        Route::get('/stats', [PatientAppointmentController::class, 'getStats'])->name('stats');
        Route::get('/export', [PatientAppointmentController::class, 'export'])->name('export');
        Route::post('/{id}/rate', [PatientAppointmentController::class, 'rate'])->name('rate');

        // API endpoints for appointment creation
        Route::get('/available-doctors', [PatientAppointmentController::class, 'getAvailableDoctors'])->name('available-doctors');
    });

    // Doctor Browsing
    Route::prefix('doctors')->name('doctors.')->group(function () {
        Route::get('/', [PatientDoctorController::class, 'index'])->name('index');
        Route::get('/{id}', [PatientDoctorController::class, 'show'])->name('show');
        Route::get('/search', [PatientDoctorController::class, 'search'])->name('search');
        Route::get('/available', [PatientDoctorController::class, 'getAvailableDoctors'])->name('available');
        Route::get('/{doctorId}/working-hours', [PatientDoctorController::class, 'getWorkingHours'])->name('working-hours');
        Route::get('/{doctorId}/available-slots', [PatientDoctorController::class, 'getAvailableSlots'])->name('doctor-available-slots');
        Route::get('/{doctorId}/ratings', [PatientDoctorController::class, 'getRatings'])->name('ratings');
        Route::get('/{doctorId}/stats', [PatientDoctorController::class, 'getStats'])->name('stats');
        Route::get('/top-rated', [PatientDoctorController::class, 'getTopRated'])->name('top-rated');
        Route::get('/specialty/{specialtyId}', [PatientDoctorController::class, 'getBySpecialty'])->name('by-specialty');
        Route::get('/favorites', [PatientDoctorController::class, 'getFavoriteDoctors'])->name('favorites');
        Route::get('/recently-visited', [PatientDoctorController::class, 'getRecentlyVisited'])->name('recently-visited');
    });

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [PatientProfileController::class, 'index'])->name('index');
        Route::put('/update', [PatientProfileController::class, 'updateProfile'])->name('update');
        Route::put('/account', [PatientProfileController::class, 'updateAccount'])->name('account');
        Route::put('/password', [PatientProfileController::class, 'changePassword'])->name('password');
        Route::post('/image', [PatientProfileController::class, 'uploadImage'])->name('image');
        Route::get('/completion', [PatientProfileController::class, 'getProfileCompletion'])->name('completion');
        Route::get('/export', [PatientProfileController::class, 'export'])->name('export');
    });

    // Wallet Management
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [PatientWalletController::class, 'index'])->name('index');
        Route::get('/balance', [PatientWalletController::class, 'getWallet'])->name('balance');
        Route::get('/transactions', [PatientWalletController::class, 'getTransactions'])->name('transactions');
        Route::get('/stats', [PatientWalletController::class, 'getStats'])->name('stats');
        Route::post('/add-funds', [PatientWalletController::class, 'addFunds'])->name('add-funds');
Route::post('/withdraw-funds', [PatientWalletController::class, 'withdrawFunds'])->name('withdraw-funds');
Route::get('/payment-history', [PatientWalletController::class, 'getPaymentHistory'])->name('payment-history');
        Route::get('/payment-stats', [PatientWalletController::class, 'getPaymentStats'])->name('payment-stats');
        Route::get('/monthly-spending', [PatientWalletController::class, 'getMonthlySpending'])->name('monthly-spending');
        Route::get('/transactions/{id}', [PatientWalletController::class, 'getTransaction'])->name('transaction');
        Route::get('/payments/{id}', [PatientWalletController::class, 'getPayment'])->name('payment');
        Route::post('/payments/{paymentId}/refund', [PatientWalletController::class, 'requestRefund'])->name('refund');
        Route::get('/export', [PatientWalletController::class, 'export'])->name('export');

        // Payment Methods Routes
        Route::get('/payment-methods', [PatientWalletController::class, 'paymentMethods'])->name('payment-methods');
        Route::post('/payment-methods', [PatientWalletController::class, 'storePaymentMethod'])->name('payment-methods.store');
        Route::get('/payment-methods/{id}', [PatientWalletController::class, 'getPaymentMethod'])->name('payment-methods.show');
        Route::put('/payment-methods/{id}', [PatientWalletController::class, 'updatePaymentMethod'])->name('payment-methods.update');
        Route::post('/payment-methods/{id}/default', [PatientWalletController::class, 'setDefaultPaymentMethod'])->name('payment-methods.default');
        Route::delete('/payment-methods/{id}', [PatientWalletController::class, 'deletePaymentMethod'])->name('payment-methods.destroy');
        Route::post('/process-payment', [PatientWalletController::class, 'processPayment'])->name('process-payment');
    });
});

// ============================================================================
// AI ASSISTANT ROUTES
// ============================================================================

Route::middleware(['auth'])->prefix('ai')->name('ai.')->group(function () {
    Route::post('/proxy', [AiProxyController::class, 'process'])->name('proxy');
    Route::post('/book-appointment', [AiProxyController::class, 'bookAppointment'])->name('book-appointment');
    Route::get('/user-profile', [AiProxyController::class, 'getUserProfile'])->name('user-profile');
});

// ============================================================================
// GENERAL PROTECTED ROUTES (All authenticated users)
// ============================================================================

Route::middleware(['auth'])->group(function () {
    // Appointments (General)
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [AppointmentController::class, 'index'])->name('index');
        Route::get('/create', [AppointmentController::class, 'create'])->name('create');
        Route::post('/', [AppointmentController::class, 'store'])->name('store');
        Route::get('/{id}', [AppointmentController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AppointmentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AppointmentController::class, 'update'])->name('update');
        Route::delete('/{id}', [AppointmentController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('general-available-slots');
        Route::put('/{id}/status', [AppointmentController::class, 'updateStatus'])->name('update-status');
        Route::get('/stats', [AppointmentController::class, 'getStats'])->name('stats');
        Route::get('/export', [AppointmentController::class, 'export'])->name('export');
    });

    // Doctors (General)
    Route::prefix('doctors')->name('doctors.')->group(function () {
        Route::get('/', [DoctorController::class, 'index'])->name('index');
        Route::get('/create', [DoctorController::class, 'create'])->name('create');
        Route::post('/', [DoctorController::class, 'store'])->name('store');
        Route::get('/{id}', [DoctorController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [DoctorController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DoctorController::class, 'update'])->name('update');
        Route::delete('/{id}', [DoctorController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/working-hours', [DoctorController::class, 'getWorkingHours'])->name('working-hours');
        Route::put('/{id}/working-hours', [DoctorController::class, 'updateWorkingHours'])->name('update-working-hours');
        Route::get('/{id}/reviews', [DoctorController::class, 'getReviews'])->name('reviews');
        Route::get('/search', [DoctorController::class, 'search'])->name('search');
        Route::get('/{id}/stats', [DoctorController::class, 'getStats'])->name('stats');
        Route::get('/export', [DoctorController::class, 'export'])->name('export');
    });

    // Payments (General)
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/create', [PaymentController::class, 'create'])->name('create');
        Route::post('/', [PaymentController::class, 'store'])->name('store');
        Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PaymentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PaymentController::class, 'update'])->name('update');
        Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
        Route::get('/search', [PaymentController::class, 'search'])->name('search');
        Route::get('/export', [PaymentController::class, 'export'])->name('export');
    });
});

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// ============================================================================
// FALLBACK ROUTE
// ============================================================================

Route::fallback(function () {
    return view('errors.404');
});
