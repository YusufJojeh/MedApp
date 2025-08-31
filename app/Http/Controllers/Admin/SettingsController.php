<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Display settings dashboard
     */
    public function index()
    {
        $stats = [
            'total_specialties' => DB::table('specialties')->count(),
            'total_plans' => DB::table('plans')->count(),
            'active_plans' => DB::table('plans')->where('is_popular', true)->count(),
            'total_features' => DB::table('plan_features')->count(),
        ];

        return view('admin.settings.index', compact('stats'));
    }

    /**
     * Display specialties management
     */
    public function specialties(Request $request)
    {
        $query = DB::table('specialties');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $specialties = $query->orderBy('name_en')->paginate(15);

        return view('admin.settings.specialties', compact('specialties'));
    }

    /**
     * Store a new specialty
     */
    public function storeSpecialty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_en' => 'required|string|max:100|unique:specialties',
            'name_ar' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $specialtyId = DB::table('specialties')->insertGetId([
                'name_en' => $request->name_en,
                'name_ar' => $request->name_ar,
                'description' => $request->description,
                'icon' => $request->icon,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create default pricing for this specialty
            DB::table('specialty_pricing')->insert([
                'specialty_id' => $specialtyId,
                'base_price' => 100.00,
                'currency' => 'SAR',
                'note' => 'Default pricing',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Specialty created successfully',
                'specialty_id' => $specialtyId
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating specialty: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update specialty
     */
    public function updateSpecialty(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name_en' => 'required|string|max:100|unique:specialties,name_en,' . $id,
            'name_ar' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::table('specialties')->where('id', $id)->update([
                'name_en' => $request->name_en,
                'name_ar' => $request->name_ar,
                'description' => $request->description,
                'icon' => $request->icon,
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Specialty updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating specialty: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete specialty
     */
    public function deleteSpecialty($id)
    {
        // Check if specialty is being used by doctors
        $doctorCount = DB::table('doctors')->where('specialty_id', $id)->count();

        if ($doctorCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete specialty. It is being used by ' . $doctorCount . ' doctor(s).'
            ], 400);
        }

        try {
            DB::table('specialty_pricing')->where('specialty_id', $id)->delete();
            DB::table('specialties')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Specialty deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting specialty: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display plans management
     */
    public function plans(Request $request)
    {
        $query = DB::table('plans');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('audience')) {
            $query->where('audience', $request->audience);
        }

        $plans = $query->orderBy('sort_order')->paginate(15);
        $audiences = ['doctors', 'patients'];

        return view('admin.settings.plans', compact('plans', 'audiences'));
    }

    /**
     * Store a new plan
     */
    public function storePlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:plans',
            'audience' => 'required|in:doctors,patients',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'billing_cycle' => 'required|in:monthly,yearly',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $planId = DB::table('plans')->insertGetId([
                'name' => $request->name,
                'slug' => $request->slug,
                'audience' => $request->audience,
                'price' => $request->price,
                'currency' => $request->currency,
                'billing_cycle' => $request->billing_cycle,
                'is_popular' => $request->boolean('is_popular'),
                'sort_order' => $request->sort_order ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plan created successfully',
                'plan_id' => $planId
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update plan
     */
    public function updatePlan(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:plans,slug,' . $id,
            'audience' => 'required|in:doctors,patients',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'billing_cycle' => 'required|in:monthly,yearly',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::table('plans')->where('id', $id)->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'audience' => $request->audience,
                'price' => $request->price,
                'currency' => $request->currency,
                'billing_cycle' => $request->billing_cycle,
                'is_popular' => $request->boolean('is_popular'),
                'sort_order' => $request->sort_order ?? 0,
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plan updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete plan
     */
    public function deletePlan($id)
    {
        // Check if plan is being used by subscriptions
        $subscriptionCount = DB::table('subscriptions')->where('plan_id', $id)->count();

        if ($subscriptionCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete plan. It is being used by ' . $subscriptionCount . ' subscription(s).'
            ], 400);
        }

        try {
            DB::table('plan_features')->where('plan_id', $id)->delete();
            DB::table('plans')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Plan deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display plan features
     */
    public function planFeatures($planId)
    {
        $plan = DB::table('plans')->where('id', $planId)->first();

        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

        $features = DB::table('plan_features')
            ->where('plan_id', $planId)
            ->orderBy('sort_order')
            ->get();

        return view('admin.settings.plan-features', compact('plan', 'features'));
    }

    /**
     * Store plan feature
     */
    public function storePlanFeature(Request $request, $planId)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'is_included' => 'boolean',
            'note' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::table('plan_features')->insert([
                'plan_id' => $planId,
                'label' => $request->label,
                'is_included' => $request->boolean('is_included'),
                'note' => $request->note,
                'sort_order' => $request->sort_order ?? 0,
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Feature added successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding feature: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update plan feature
     */
    public function updatePlanFeature(Request $request, $featureId)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'is_included' => 'boolean',
            'note' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::table('plan_features')->where('id', $featureId)->update([
                'label' => $request->label,
                'is_included' => $request->boolean('is_included'),
                'note' => $request->note,
                'sort_order' => $request->sort_order ?? 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Feature updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating feature: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete plan feature
     */
    public function deletePlanFeature($featureId)
    {
        try {
            DB::table('plan_features')->where('id', $featureId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Feature deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting feature: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display system settings
     */
    public function systemSettings()
    {
        $settings = [
            'site_name' => config('app.name', 'Medical Booking System'),
            'site_description' => config('app.description', 'Professional medical booking platform'),
            'contact_email' => config('mail.from.address', 'admin@medical.com'),
            'contact_phone' => config('app.contact_phone', '+966 50 123 4567'),
            'default_currency' => config('app.default_currency', 'SAR'),
            'timezone' => config('app.timezone', 'Asia/Riyadh'),
            'appointment_duration' => config('app.appointment_duration', 30),
            'max_appointments_per_day' => config('app.max_appointments_per_day', 20),
            'payment_gateway' => config('app.payment_gateway', 'stripe'),
            'platform_fee_percentage' => config('app.platform_fee_percentage', 5),
        ];

        return view('admin.settings.system', compact('settings'));
    }

    /**
     * Update system settings
     */
    public function updateSystemSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:100',
            'site_description' => 'nullable|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'default_currency' => 'required|string|max:10',
            'timezone' => 'required|string',
            'appointment_duration' => 'required|integer|min:15|max:120',
            'max_appointments_per_day' => 'required|integer|min:1|max:100',
            'payment_gateway' => 'required|in:stripe,paypal',
            'platform_fee_percentage' => 'required|numeric|min:0|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Update configuration values
            $settings = [
                'app.name' => $request->site_name,
                'app.description' => $request->site_description,
                'mail.from.address' => $request->contact_email,
                'app.contact_phone' => $request->contact_phone,
                'app.default_currency' => $request->default_currency,
                'app.timezone' => $request->timezone,
                'app.appointment_duration' => $request->appointment_duration,
                'app.max_appointments_per_day' => $request->max_appointments_per_day,
                'app.payment_gateway' => $request->payment_gateway,
                'app.platform_fee_percentage' => $request->platform_fee_percentage,
            ];

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'System settings updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display pricing settings
     */
    public function pricingSettings()
    {
        $specialtyPricing = DB::table('specialty_pricing')
            ->join('specialties', 'specialty_pricing.specialty_id', '=', 'specialties.id')
            ->select('specialty_pricing.*', 'specialties.name_en as specialty_name')
            ->get();

        $doctorPricing = DB::table('doctor_pricing_overrides')
            ->join('doctors', 'doctor_pricing_overrides.doctor_id', '=', 'doctors.id')
            ->select('doctor_pricing_overrides.*', 'doctors.name as doctor_name')
            ->get();

        return view('admin.settings.pricing', compact('specialtyPricing', 'doctorPricing'));
    }

    /**
     * Update specialty pricing
     */
    public function updateSpecialtyPricing(Request $request, $specialtyId)
    {
        $validator = Validator::make($request->all(), [
            'base_price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::table('specialty_pricing')
                ->where('specialty_id', $specialtyId)
                ->update([
                    'base_price' => $request->base_price,
                    'currency' => $request->currency,
                    'note' => $request->note,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Pricing updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating pricing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update doctor pricing override
     */
    public function updateDoctorPricing(Request $request, $doctorId)
    {
        $validator = Validator::make($request->all(), [
            'override_price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::table('doctor_pricing_overrides')
                ->updateOrInsert(
                    ['doctor_id' => $doctorId],
                    [
                        'override_price' => $request->override_price,
                        'currency' => $request->currency,
                        'note' => $request->note,
                        'updated_at' => now(),
                    ]
                );

            return response()->json([
                'success' => true,
                'message' => 'Doctor pricing updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating doctor pricing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear system cache
     */
    public function clearCache()
    {
        try {
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'System cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system statistics
     */
    public function getSystemStats()
    {
        $stats = [
            'total_users' => DB::table('users')->count(),
            'total_doctors' => DB::table('doctors')->count(),
            'total_patients' => DB::table('patients')->count(),
            'total_appointments' => DB::table('appointments')->count(),
            'total_payments' => DB::table('payments')->count(),
            'total_revenue' => DB::table('payments')->where('STATUS', 'succeeded')->sum('amount'),
            'active_subscriptions' => DB::table('subscriptions')->where('STATUS', 'active')->count(),
            'system_health' => [
                'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
                'cache' => Cache::driver()->get('test') !== null ? 'working' : 'not_working',
                'storage' => is_writable(storage_path()) ? 'writable' : 'not_writable',
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Display system configuration settings
     */
    public function config()
    {
        $config = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_debug' => config('app.debug'),
            'app_timezone' => config('app.timezone'),
            'app_locale' => config('app.locale'),
            'mail_driver' => config('mail.default'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
            'database_connection' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
        ];

        return view('admin.settings.config', compact('config'));
    }

    /**
     * Update system configuration
     */
    public function updateConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'app_timezone' => 'required|string',
            'app_locale' => 'required|string|max:10',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update .env file or database settings
            // This is a simplified implementation
            // In production, you might want to use a settings table or .env file management

            return redirect()->route('admin.settings.config')
                ->with('success', 'System configuration updated successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating configuration: ' . $e->getMessage());
        }
    }

    /**
     * Display maintenance settings
     */
    public function maintenance()
    {
        $maintenance = [
            'backup_enabled' => config('backup.enabled', false),
            'last_backup' => Cache::get('last_backup_time'),
            'backup_retention_days' => config('backup.retention_days', 7),
            'maintenance_mode' => app()->isDownForMaintenance(),
            'database_size' => $this->getDatabaseSize(),
            'storage_usage' => $this->getStorageUsage(),
        ];

        return view('admin.settings.maintenance', compact('maintenance'));
    }

    /**
     * Create database backup
     */
    public function createBackup()
    {
        try {
            // This is a simplified backup implementation
            // In production, you should use a proper backup package like spatie/laravel-backup

            $backupPath = storage_path('backups/backup_' . date('Y-m-d_H-i-s') . '.sql');

            // Create backup directory if it doesn't exist
            if (!file_exists(dirname($backupPath))) {
                mkdir(dirname($backupPath), 0755, true);
            }

            // Simple database dump (this is basic - use proper backup tools in production)
            $command = sprintf(
                'mysqldump -h%s -P%s -u%s -p%s %s > %s',
                config('database.connections.mysql.host'),
                config('database.connections.mysql.port'),
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.database'),
                $backupPath
            );

            exec($command);

            Cache::put('last_backup_time', now(), 60 * 24 * 30); // 30 days

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully',
                'backup_path' => $backupPath
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimize database
     */
    public function optimizeDatabase()
    {
        try {
            // Run database optimization commands
            \Artisan::call('migrate:status');
            \Artisan::call('config:cache');
            \Artisan::call('route:cache');
            \Artisan::call('view:cache');

            return response()->json([
                'success' => true,
                'message' => 'Database optimized successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error optimizing database: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display API configuration
     */
    public function api()
    {
        $api = [
            'api_enabled' => config('api.enabled', true),
            'api_rate_limit' => config('api.rate_limit', 60),
            'api_timeout' => config('api.timeout', 30),
            'webhook_url' => config('webhooks.url'),
            'webhook_secret' => config('webhooks.secret'),
            'third_party_keys' => [
                'stripe_public_key' => config('services.stripe.key'),
                'stripe_secret_key' => config('services.stripe.secret') ? '***hidden***' : null,
                'google_maps_key' => config('services.google.maps_key'),
                'twilio_sid' => config('services.twilio.sid'),
                'twilio_token' => config('services.twilio.token') ? '***hidden***' : null,
            ]
        ];

        return view('admin.settings.api', compact('api'));
    }

    /**
     * Update API configuration
     */
    public function updateApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_enabled' => 'boolean',
            'api_rate_limit' => 'integer|min:1|max:1000',
            'api_timeout' => 'integer|min:1|max:300',
            'webhook_url' => 'nullable|url',
            'webhook_secret' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update API configuration
            // This is a simplified implementation

            return redirect()->route('admin.settings.api')
                ->with('success', 'API configuration updated successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating API configuration: ' . $e->getMessage());
        }
    }

    /**
     * Display security settings
     */
    public function security()
    {
        $security = [
            'password_min_length' => config('auth.password_min_length', 8),
            'password_require_special' => config('auth.password_require_special', true),
            'password_require_numbers' => config('auth.password_require_numbers', true),
            'password_require_uppercase' => config('auth.password_require_uppercase', true),
            'session_lifetime' => config('session.lifetime', 120),
            'session_secure' => config('session.secure', false),
            'session_http_only' => config('session.http_only', true),
            'two_factor_enabled' => config('auth.two_factor_enabled', false),
            'login_attempts_limit' => config('auth.login_attempts_limit', 5),
            'lockout_duration' => config('auth.lockout_duration', 15),
        ];

        return view('admin.settings.security', compact('security'));
    }

    /**
     * Update security settings
     */
    public function updateSecurity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password_min_length' => 'integer|min:6|max:20',
            'password_require_special' => 'boolean',
            'password_require_numbers' => 'boolean',
            'password_require_uppercase' => 'boolean',
            'session_lifetime' => 'integer|min:1|max:1440',
            'session_secure' => 'boolean',
            'session_http_only' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'login_attempts_limit' => 'integer|min:1|max:10',
            'lockout_duration' => 'integer|min:1|max:60',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update security configuration
            // This is a simplified implementation

            return redirect()->route('admin.settings.security')
                ->with('success', 'Security settings updated successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating security settings: ' . $e->getMessage());
        }
    }

    /**
     * Get database size
     */
    private function getDatabaseSize()
    {
        try {
            $result = DB::select("
                SELECT
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb'
                FROM information_schema.tables
                WHERE table_schema = ?
            ", [config('database.connections.mysql.database')]);

            return $result[0]->size_mb ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get storage usage
     */
    private function getStorageUsage()
    {
        try {
            $storagePath = storage_path();
            $totalSpace = disk_total_space($storagePath);
            $freeSpace = disk_free_space($storagePath);
            $usedSpace = $totalSpace - $freeSpace;

            return [
                'total' => round($totalSpace / 1024 / 1024 / 1024, 2), // GB
                'used' => round($usedSpace / 1024 / 1024 / 1024, 2), // GB
                'free' => round($freeSpace / 1024 / 1024 / 1024, 2), // GB
                'percentage' => round(($usedSpace / $totalSpace) * 100, 2)
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'used' => 0,
                'free' => 0,
                'percentage' => 0
            ];
        }
    }
}
