@extends('layouts.app')

@section('title', 'System Configuration')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">System Configuration</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    Configure system-wide settings, notifications, and general application preferences.
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.settings.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Settings
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Application Settings</h2>
        </div>

        <form method="POST" action="{{ route('admin.settings.config.update') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Application Name -->
                <div>
                    <label for="app_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Application Name *
                    </label>
                    <input type="text"
                           id="app_name"
                           name="app_name"
                           value="{{ old('app_name', $config['app_name']) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    @error('app_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Application URL -->
                <div>
                    <label for="app_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Application URL *
                    </label>
                    <input type="url"
                           id="app_url"
                           name="app_url"
                           value="{{ old('app_url', $config['app_url']) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    @error('app_url')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Timezone -->
                <div>
                    <label for="app_timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Timezone *
                    </label>
                    <select id="app_timezone"
                            name="app_timezone"
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="UTC" {{ old('app_timezone', $config['app_timezone']) === 'UTC' ? 'selected' : '' }}>UTC</option>
                        <option value="Asia/Riyadh" {{ old('app_timezone', $config['app_timezone']) === 'Asia/Riyadh' ? 'selected' : '' }}>Asia/Riyadh</option>
                        <option value="Asia/Dubai" {{ old('app_timezone', $config['app_timezone']) === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai</option>
                        <option value="Europe/London" {{ old('app_timezone', $config['app_timezone']) === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                        <option value="America/New_York" {{ old('app_timezone', $config['app_timezone']) === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                    </select>
                    @error('app_timezone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Locale -->
                <div>
                    <label for="app_locale" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Default Locale *
                    </label>
                    <select id="app_locale"
                            name="app_locale"
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="en" {{ old('app_locale', $config['app_locale']) === 'en' ? 'selected' : '' }}>English</option>
                        <option value="ar" {{ old('app_locale', $config['app_locale']) === 'ar' ? 'selected' : '' }}>العربية</option>
                        <option value="fr" {{ old('app_locale', $config['app_locale']) === 'fr' ? 'selected' : '' }}>Français</option>
                        <option value="es" {{ old('app_locale', $config['app_locale']) === 'es' ? 'selected' : '' }}>Español</option>
                    </select>
                    @error('app_locale')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Email Configuration</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- From Address -->
                    <div>
                        <label for="mail_from_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            From Email Address *
                        </label>
                        <input type="email"
                               id="mail_from_address"
                               name="mail_from_address"
                               value="{{ old('mail_from_address', $config['mail_from_address']) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        @error('mail_from_address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- From Name -->
                    <div>
                        <label for="mail_from_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            From Name *
                        </label>
                        <input type="text"
                               id="mail_from_name"
                               name="mail_from_name"
                               value="{{ old('mail_from_name', $config['mail_from_name']) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        @error('mail_from_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Debug Mode</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $config['app_debug'] ? 'Enabled' : 'Disabled' }}
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Database</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ ucfirst($config['database_connection']) }}
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Cache Driver</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ ucfirst($config['cache_driver']) }}
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Session Driver</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ ucfirst($config['session_driver']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Save Configuration
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
