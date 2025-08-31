@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">System Settings</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage system configuration and settings</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-clock mr-1"></i>
                        <span id="current-time"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Total Specialties</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_specialties'] }}</p>
                    </div>
                    <i class="fas fa-stethoscope text-blue-500 text-2xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Total Plans</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_plans'] }}</p>
                    </div>
                    <i class="fas fa-crown text-yellow-500 text-2xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Active Plans</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_plans'] }}</p>
                    </div>
                    <i class="fas fa-star text-green-500 text-2xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Total Features</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_features'] }}</p>
                    </div>
                    <i class="fas fa-list-check text-purple-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Settings Sections -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Specialties Management -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-stethoscope text-blue-500 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Specialties</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Manage medical specialties and their pricing. Add, edit, or remove specialties from the system.
                </p>
                <a href="{{ route('admin.settings.specialties') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-cog mr-2"></i>
                    Manage Specialties
                </a>
            </div>

            <!-- Plans Management -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-crown text-yellow-500 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Subscription Plans</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Configure subscription plans for doctors and patients. Set pricing, features, and availability.
                </p>
                <a href="{{ route('admin.settings.plans') }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-cog mr-2"></i>
                    Manage Plans
                </a>
            </div>

            <!-- System Configuration -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-cogs text-purple-500 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">System Config</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Configure system-wide settings, notifications, and general application preferences.
                </p>
                <a href="{{ route('admin.settings.config') }}"
                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-cog mr-2"></i>
                    System Settings
                </a>
            </div>

            <!-- Backup & Maintenance -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-database text-green-500 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Backup & Maintenance</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Manage database backups, system maintenance, and data integrity checks.
                </p>
                <a href="{{ route('admin.settings.maintenance') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-tools mr-2"></i>
                    Maintenance
                </a>
            </div>

            <!-- API Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-code text-indigo-500 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">API Configuration</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Configure API keys, webhooks, and third-party integrations for the system.
                </p>
                <a href="{{ route('admin.settings.api') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-key mr-2"></i>
                    API Settings
                </a>
            </div>

            <!-- Security Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-shield-alt text-red-500 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Security</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Configure security settings, password policies, and access controls.
                </p>
                <a href="{{ route('admin.settings.security') }}"
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-lock mr-2"></i>
                    Security Settings
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent System Activity</h3>
            </div>
            <div class="p-6">
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <i class="fas fa-chart-line text-4xl mb-4"></i>
                    <p>No recent activity to display</p>
                    <p class="text-sm">System activity logs will appear here</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update current time
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    document.getElementById('current-time').textContent = timeString;
}

// Update time every second
setInterval(updateTime, 1000);
updateTime();
</script>
@endsection
