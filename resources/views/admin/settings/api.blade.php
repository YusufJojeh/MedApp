@extends('layouts.app')

@section('title', 'API Configuration')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">API Configuration</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    Configure API keys, webhooks, and third-party integrations for the system.
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

    <!-- API Settings -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">API Settings</h2>
        </div>

        <form method="POST" action="{{ route('admin.settings.api.update') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- API Enabled -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="api_enabled"
                               value="1"
                               {{ old('api_enabled', $api['api_enabled']) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Enable API</span>
                    </label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Enable or disable the API endpoints</p>
                </div>

                <!-- Rate Limit -->
                <div>
                    <label for="api_rate_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rate Limit (requests per minute)
                    </label>
                    <input type="number"
                           id="api_rate_limit"
                           name="api_rate_limit"
                           value="{{ old('api_rate_limit', $api['api_rate_limit']) }}"
                           min="1"
                           max="1000"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    @error('api_rate_limit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- API Timeout -->
                <div>
                    <label for="api_timeout" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        API Timeout (seconds)
                    </label>
                    <input type="number"
                           id="api_timeout"
                           name="api_timeout"
                           value="{{ old('api_timeout', $api['api_timeout']) }}"
                           min="1"
                           max="300"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    @error('api_timeout')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Webhook URL -->
                <div>
                    <label for="webhook_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Webhook URL
                    </label>
                    <input type="url"
                           id="webhook_url"
                           name="webhook_url"
                           value="{{ old('webhook_url', $api['webhook_url']) }}"
                           placeholder="https://your-domain.com/webhook"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    @error('webhook_url')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Webhook Secret -->
                <div>
                    <label for="webhook_secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Webhook Secret
                    </label>
                    <input type="text"
                           id="webhook_secret"
                           name="webhook_secret"
                           value="{{ old('webhook_secret', $api['webhook_secret']) }}"
                           placeholder="Your webhook secret key"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    @error('webhook_secret')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Save API Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Third-Party Integrations -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Third-Party Integrations</h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Stripe Configuration -->
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <div class="flex items-center mb-4">
                        <i class="fab fa-stripe text-2xl text-purple-600 mr-3"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Stripe Payment</h3>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Public Key
                            </label>
                            <input type="text"
                                   value="{{ $api['third_party_keys']['stripe_public_key'] ?? '' }}"
                                   placeholder="pk_test_..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Secret Key
                            </label>
                            <input type="password"
                                   value="{{ $api['third_party_keys']['stripe_secret_key'] ?? '' }}"
                                   placeholder="sk_test_..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   readonly>
                        </div>
                    </div>
                </div>

                <!-- Google Maps Configuration -->
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <div class="flex items-center mb-4">
                        <i class="fab fa-google text-2xl text-red-600 mr-3"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Google Maps</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            API Key
                        </label>
                        <input type="text"
                               value="{{ $api['third_party_keys']['google_maps_key'] ?? '' }}"
                               placeholder="AIza..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               readonly>
                    </div>
                </div>

                <!-- Twilio Configuration -->
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-sms text-2xl text-green-600 mr-3"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Twilio SMS</h3>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Account SID
                            </label>
                            <input type="text"
                                   value="{{ $api['third_party_keys']['twilio_sid'] ?? '' }}"
                                   placeholder="AC..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Auth Token
                            </label>
                            <input type="password"
                                   value="{{ $api['third_party_keys']['twilio_token'] ?? '' }}"
                                   placeholder="..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   readonly>
                        </div>
                    </div>
                </div>

                <!-- Email Configuration -->
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-envelope text-2xl text-blue-600 mr-3"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Email Service</h3>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Mail Driver
                            </label>
                            <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="smtp">SMTP</option>
                                <option value="mailgun">Mailgun</option>
                                <option value="ses">Amazon SES</option>
                                <option value="sendgrid">SendGrid</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- API Documentation -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">API Documentation</h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">API Endpoints</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Base URL: <code class="bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded">{{ url('/api/v1') }}</code>
                    </p>
                    <a href="{{ url('/api/documentation') }}"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-book mr-2"></i>
                        View Documentation
                    </a>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">API Testing</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Test your API endpoints and verify configurations.
                    </p>
                    <a href="{{ url('/api/test') }}"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-play mr-2"></i>
                        Test API
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
