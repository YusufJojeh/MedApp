@extends('layouts.app')

@section('title', 'Confirm Password')

@section('content')
<div class="min-h-screen bg-surface flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center" data-aos="fade-down" data-aos-delay="100">
            <div class="mx-auto h-16 w-16 bg-gradient-to-br from-gold to-gold-deep rounded-2xl flex items-center justify-center mb-6">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-text">Confirm Password</h2>
            <p class="text-muted mt-2">This is a secure area of the application</p>
        </div>

        <!-- Confirm Password Form -->
        <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
            <div class="p-8">
                <form method="POST" action="{{ route('password.confirm') }}" data-validate>
                    @csrf

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-text mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required autocomplete="current-password"
                                   class="w-full pl-10 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                   placeholder="Enter your password">
                            <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-muted"></i>
                            <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-muted hover:text-text">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full btn btn-primary btn-lg">
                        <i class="fas fa-check mr-2"></i>
                        Confirm
                    </button>
                </form>

                <!-- Security Notice -->
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Security Notice</h4>
                            <p class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                Please confirm your password before accessing this secure area. This helps protect your account.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Password toggle
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password');
        let isValid = true;

        // Password validation
        if (!password.value) {
            password.classList.add('border-red-500');
            isValid = false;
        } else {
            password.classList.remove('border-red-500');
        }

        if (!isValid) {
            e.preventDefault();
            showNotification('Please enter your password', 'error');
        }
    });
</script>
@endpush
