@extends('layouts.app')

@section('title', 'Register - Medical Booking System')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-text">Join MediBook</h1>
                <p class="text-muted mt-2">Create your account to get started</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-center">
            <div class="card feature-card w-full max-w-lg" data-aos="fade-up">
                <div class="p-6">
            <form class="space-y-6" method="POST" action="{{ route('register.submit') }}">
                @csrf

                <!-- Full Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-text mb-2">
                        Full Name
                    </label>
                    <input id="name" name="name" type="text" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                           placeholder="Enter your full name"
                           value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-text mb-2">
                        Email Address
                    </label>
                    <input id="email" name="email" type="email" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                           placeholder="Enter your email"
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-text mb-2">
                        Phone Number
                    </label>
                    <input id="phone" name="phone" type="tel" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                           placeholder="Enter your phone number"
                           value="{{ old('phone') }}">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Selection -->
                <div>
                    <label class="block text-sm font-medium text-text mb-2">
                        I am a
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative">
                            <input type="radio" name="role" value="patient" class="sr-only peer" {{ old('role') == 'patient' ? 'checked' : '' }}>
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-gray-700 peer-checked:border-gold peer-checked:bg-gold/10">
                                <i class="fas fa-user-injured text-2xl text-muted mb-2"></i>
                                <div class="text-text font-medium">Patient</div>
                                <div class="text-muted text-xs">Book appointments</div>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="role" value="doctor" class="sr-only peer" {{ old('role') == 'doctor' ? 'checked' : '' }}>
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-gray-700 peer-checked:border-gold peer-checked:bg-gold/10">
                                <i class="fas fa-user-md text-2xl text-muted mb-2"></i>
                                <div class="text-text font-medium">Doctor</div>
                                <div class="text-muted text-xs">Manage patients</div>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-text mb-2">
                        Password
                    </label>
                    <input id="password" name="password" type="password" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                           placeholder="Create a strong password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-text mb-2">
                        Confirm Password
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                           placeholder="Confirm your password">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" required
                               class="h-4 w-4 text-gold focus:ring-gold border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="text-text">
                            I agree to the
                            <a href="#" class="text-gold hover:text-gold-deep">Terms of Service</a>
                            and
                            <a href="{{ route('privacy') }}" class="text-gold hover:text-gold-deep">Privacy Policy</a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="btn btn-primary w-full">
                        Create Account
                    </button>
                </div>

            </form>
        </div>

        <!-- Sign In Link -->
        <div class="text-center mt-6">
            <p class="text-text">
                Already have an account?
                <a href="{{ route('login') }}" class="text-gold hover:text-gold-deep font-medium">
                    Sign in here
                </a>
            </p>
        </div>
                </div>
            </div>
        </div>
    </div>
@endsection
