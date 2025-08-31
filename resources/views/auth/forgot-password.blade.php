@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="min-h-screen bg-surface flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center" data-aos="fade-down" data-aos-delay="100">
            <div class="mx-auto h-16 w-16 bg-gradient-to-br from-gold to-gold-deep rounded-2xl flex items-center justify-center mb-6">
                <i class="fas fa-key text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-text">Forgot Password</h2>
            <p class="text-muted mt-2">Enter your email to reset your password</p>
        </div>

        <!-- Forgot Password Form -->
        <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
            <div class="p-8">
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" data-validate>
                    @csrf

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-text mb-2">Email Address</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                   placeholder="Enter your email address">
                            <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-muted"></i>
                        </div>
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Send Reset Link
                    </button>
                </form>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-gold hover:text-gold-deep font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Login
                    </a>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="text-center" data-aos="fade-up" data-aos-delay="300">
            <p class="text-muted text-sm">
                Need help?
                <a href="#" class="text-gold hover:text-gold-deep font-medium transition-colors">Contact Support</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const email = document.getElementById('email');
        let isValid = true;

        // Email validation
        if (!email.value || !isValidEmail(email.value)) {
            email.classList.add('border-red-500');
            isValid = false;
        } else {
            email.classList.remove('border-red-500');
        }

        if (!isValid) {
            e.preventDefault();
            showNotification('Please enter a valid email address', 'error');
        }
    });

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
</script>
@endpush
