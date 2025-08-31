@extends('layouts.app')

@section('title', 'Login - Medical Booking System')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-text">Welcome Back</h1>
                <p class="text-muted mt-2">Sign in to your account</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-center">
            <div class="card feature-card w-full max-w-md" data-aos="fade-up">
                <div class="p-6">

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4 mb-6">
                <ul class="text-red-400 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4 mb-6">
                <p class="text-red-400 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4 mb-6">
                <p class="text-green-400 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-text mb-2">Email Address</label>
                <input type="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                       id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-text mb-2">Password</label>
                <input type="password" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                       id="password" name="password" required>
                @error('password')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" class="rounded border-gray-300 text-gold focus:ring-gold" id="remember" name="remember">
                <label class="ml-2 text-sm text-text" for="remember">
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-full">
                Sign In
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-text">Don't have an account? <a href="{{ route('register') }}" class="text-gold hover:text-gold-deep">Register here</a></p>
            <p class="text-text mt-2">
                <a href="{{ route('home') }}" class="text-gold hover:text-gold-deep">Back to Home</a>
            </p>
        </div>
                </div>
            </div>
        </div>
    </div>
@endsection
