@extends('layouts.app')
@section('title', 'Security Settings')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Security Settings</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Manage your account security preferences and settings</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Security Settings Form -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Security Preferences</h3>

                <form action="{{ route('admin.profile.update-security') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <!-- Two-Factor Authentication -->
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Two-Factor Authentication</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Add an extra layer of security to your account</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="two_factor_enabled"
                                       value="1"
                                       {{ old('two_factor_enabled', $securityData->two_factor_enabled ?? false) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Login Notifications -->
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Login Notifications</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Get notified of new login attempts</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="login_notifications"
                                       value="1"
                                       {{ old('login_notifications', $securityData->login_notifications ?? false) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Session Timeout -->
                        <div>
                            <label for="session_timeout" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Session Timeout (minutes)
                            </label>
                            <select id="session_timeout"
                                    name="session_timeout"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="30" {{ old('session_timeout', $securityData->session_timeout ?? 30) == 30 ? 'selected' : '' }}>30 minutes</option>
                                <option value="60" {{ old('session_timeout', $securityData->session_timeout ?? 30) == 60 ? 'selected' : '' }}>1 hour</option>
                                <option value="120" {{ old('session_timeout', $securityData->session_timeout ?? 30) == 120 ? 'selected' : '' }}>2 hours</option>
                                <option value="240" {{ old('session_timeout', $securityData->session_timeout ?? 30) == 240 ? 'selected' : '' }}>4 hours</option>
                                <option value="480" {{ old('session_timeout', $securityData->session_timeout ?? 30) == 480 ? 'selected' : '' }}>8 hours</option>
                            </select>
                        </div>

                        <!-- IP Whitelist -->
                        <div>
                            <label for="ip_whitelist" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                IP Whitelist
                            </label>
                            <textarea id="ip_whitelist"
                                      name="ip_whitelist"
                                      rows="3"
                                      placeholder="Enter IP addresses (one per line)"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">{{ old('ip_whitelist', $securityData->ip_whitelist ?? '') }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Leave empty to allow all IP addresses
                            </p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700 mt-8">
                        <a href="{{ route('admin.profile.index') }}"
                           class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Update Security Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Status -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Security Status</h3>

                <div class="space-y-4">
                    <!-- Account Security Score -->
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Account Security Score</h4>
                                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">85%</p>
                            </div>
                            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center">
                                <i class="fas fa-shield-alt text-2xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Security Checklist -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Security Checklist</h4>

                        <div class="flex items-center space-x-3">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Strong password set</span>
                        </div>

                        <div class="flex items-center space-x-3">
                            <i class="fas fa-times-circle text-red-500"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Two-factor authentication</span>
                        </div>

                        <div class="flex items-center space-x-3">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Login notifications enabled</span>
                        </div>

                        <div class="flex items-center space-x-3">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Session timeout configured</span>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Recent Login Activity</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Last login</span>
                                <span class="text-gray-900 dark:text-white">{{ now()->subHours(2)->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">IP Address</span>
                                <span class="text-gray-900 dark:text-white">127.0.0.1</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Device</span>
                                <span class="text-gray-900 dark:text-white">Chrome on Windows</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
