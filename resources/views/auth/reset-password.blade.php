@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="min-h-screen bg-surface flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center" data-aos="fade-down" data-aos-delay="100">
            <div class="mx-auto h-16 w-16 bg-gradient-to-br from-gold to-gold-deep rounded-2xl flex items-center justify-center mb-6">
                <i class="fas fa-lock text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-text">Reset Password</h2>
            <p class="text-muted mt-2">Enter your new password</p>
        </div>

        <!-- Reset Password Form -->
        <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
            <div class="p-8">
                <form method="POST" action="{{ route('password.store') }}" data-validate>
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-text mb-2">Email Address</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}" required autocomplete="username"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                   placeholder="Enter your email address">
                            <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-muted"></i>
                        </div>
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-text mb-2">New Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required autocomplete="new-password"
                                   class="w-full pl-10 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                   placeholder="Enter your new password">
                            <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-muted"></i>
                            <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-muted hover:text-text">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-text mb-2">Confirm New Password</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                   class="w-full pl-10 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                   placeholder="Confirm your new password">
                            <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-muted"></i>
                            <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-muted hover:text-text">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Requirements -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <h4 class="text-sm font-medium text-text mb-2">Password Requirements:</h4>
                        <ul class="text-xs text-muted space-y-1">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2" id="req-length"></i>
                                At least 8 characters
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2" id="req-uppercase"></i>
                                One uppercase letter
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2" id="req-lowercase"></i>
                                One lowercase letter
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2" id="req-number"></i>
                                One number
                            </li>
                        </ul>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full btn btn-primary btn-lg">
                        <i class="fas fa-save mr-2"></i>
                        Reset Password
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
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Password toggles
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');

    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });

    toggleConfirmPassword.addEventListener('click', function() {
        const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmation.setAttribute('type', type);

        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });

    // Password validation
    password.addEventListener('input', function() {
        const value = this.value;

        // Check requirements
        const reqLength = document.getElementById('req-length');
        const reqUppercase = document.getElementById('req-uppercase');
        const reqLowercase = document.getElementById('req-lowercase');
        const reqNumber = document.getElementById('req-number');

        // Length check
        if (value.length >= 8) {
            reqLength.className = 'fas fa-check text-green-500 mr-2';
        } else {
            reqLength.className = 'fas fa-times text-red-500 mr-2';
        }

        // Uppercase check
        if (/[A-Z]/.test(value)) {
            reqUppercase.className = 'fas fa-check text-green-500 mr-2';
        } else {
            reqUppercase.className = 'fas fa-times text-red-500 mr-2';
        }

        // Lowercase check
        if (/[a-z]/.test(value)) {
            reqLowercase.className = 'fas fa-check text-green-500 mr-2';
        } else {
            reqLowercase.className = 'fas fa-times text-red-500 mr-2';
        }

        // Number check
        if (/\d/.test(value)) {
            reqNumber.className = 'fas fa-check text-green-500 mr-2';
        } else {
            reqNumber.className = 'fas fa-times text-red-500 mr-2';
        }
    });

    // Password confirmation validation
    passwordConfirmation.addEventListener('input', function() {
        if (password.value !== this.value) {
            this.classList.add('border-red-500');
        } else {
            this.classList.remove('border-red-500');
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        let isValid = true;

        // Email validation
        if (!email.value || !isValidEmail(email.value)) {
            email.classList.add('border-red-500');
            isValid = false;
        } else {
            email.classList.remove('border-red-500');
        }

        // Password validation
        if (!password.value || password.value.length < 8) {
            password.classList.add('border-red-500');
            isValid = false;
        } else {
            password.classList.remove('border-red-500');
        }

        // Password confirmation validation
        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.classList.add('border-red-500');
            isValid = false;
        } else {
            passwordConfirmation.classList.remove('border-red-500');
        }

        if (!isValid) {
            e.preventDefault();
            showNotification('Please fill in all required fields correctly', 'error');
        }
    });

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
</script>
@endpush
