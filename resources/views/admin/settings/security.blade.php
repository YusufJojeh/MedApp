@extends('layouts.app')

@section('title', 'Security Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Security Settings</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    Configure security settings, password policies, and access controls.
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

    <!-- Password Policy -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Password Policy</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Configure password requirements and complexity rules</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.security.update') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Minimum Length -->
                <div>
                    <label for="password_min_length" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Minimum Password Length
                    </label>
                    <input type="number"
                           id="password_min_length"
                           name="password_min_length"
                           value="{{ old('password_min_length', $security['password_min_length']) }}"
                           min="6"
                           max="20"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    @error('password_min_length')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Require Special Characters -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="password_require_special"
                               value="1"
                               {{ old('password_require_special', $security['password_require_special']) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Require Special Characters</span>
                    </label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Passwords must contain at least one special character</p>
                </div>

                <!-- Require Numbers -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="password_require_numbers"
                               value="1"
                               {{ old('password_require_numbers', $security['password_require_numbers']) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Require Numbers</span>
                    </label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Passwords must contain at least one number</p>
                </div>

                <!-- Require Uppercase -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="password_require_uppercase"
                               value="1"
                               {{ old('password_require_uppercase', $security['password_require_uppercase']) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Require Uppercase Letters</span>
                    </label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Passwords must contain at least one uppercase letter</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Save Password Policy
                </button>
            </div>
        </form>
    </div>

    <!-- Session Management -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Session Management</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Configure session security and timeout settings</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.security.update') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Session Lifetime -->
                <div>
                    <label for="session_lifetime" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Session Lifetime (minutes)
                    </label>
                    <input type="number"
                           id="session_lifetime"
                           name="session_lifetime"
                           value="{{ old('session_lifetime', $security['session_lifetime']) }}"
                           min="1"
                           max="1440"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    @error('session_lifetime')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Secure Sessions -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="session_secure"
                               value="1"
                               {{ old('session_secure', $security['session_secure']) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Secure Sessions (HTTPS Only)</span>
                    </label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Require HTTPS for session cookies</p>
                </div>

                <!-- HTTP Only Cookies -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="session_http_only"
                               value="1"
                               {{ old('session_http_only', $security['session_http_only']) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">HTTP Only Cookies</span>
                    </label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Prevent JavaScript access to session cookies</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Save Session Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Authentication Security -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Authentication Security</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Configure login security and account protection</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.security.update') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Two-Factor Authentication -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="two_factor_enabled"
                               value="1"
                               {{ old('two_factor_enabled', $security['two_factor_enabled']) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Enable Two-Factor Authentication</span>
                    </label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Require 2FA for all user accounts</p>
                </div>

                <!-- Login Attempts Limit -->
                <div>
                    <label for="login_attempts_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Login Attempts Limit
                    </label>
                    <input type="number"
                           id="login_attempts_limit"
                           name="login_attempts_limit"
                           value="{{ old('login_attempts_limit', $security['login_attempts_limit']) }}"
                           min="1"
                           max="10"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    @error('login_attempts_limit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lockout Duration -->
                <div>
                    <label for="lockout_duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Lockout Duration (minutes)
                    </label>
                    <input type="number"
                           id="lockout_duration"
                           name="lockout_duration"
                           value="{{ old('lockout_duration', $security['lockout_duration']) }}"
                           min="1"
                           max="60"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    @error('lockout_duration')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Save Authentication Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Security Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Security Status</h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg">
                            <i class="fas fa-shield-alt text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">SSL Certificate</p>
                            <p class="text-lg font-semibold text-green-900 dark:text-green-100">Valid</p>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                            <i class="fas fa-lock text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Password Policy</p>
                            <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">Active</p>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-800 rounded-lg">
                            <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Session Timeout</p>
                            <p class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">{{ $security['session_lifetime'] }}m</p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-lg">
                            <i class="fas fa-mobile-alt text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-purple-800 dark:text-purple-200">2FA Status</p>
                            <p class="text-lg font-semibold text-purple-900 dark:text-purple-100">
                                {{ $security['two_factor_enabled'] ? 'Enabled' : 'Disabled' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
