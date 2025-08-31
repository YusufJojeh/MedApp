@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black flex items-center justify-center px-4">
    <div class="max-w-2xl w-full">
        <!-- 404 Error Card -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 text-center">
            <!-- Error Icon -->
            <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-exclamation-triangle text-white text-3xl"></i>
            </div>

            <!-- Error Message -->
            <h1 class="text-6xl font-bold text-white mb-4">404</h1>
            <h2 class="text-2xl font-semibold text-white mb-4">Page Not Found</h2>
            <p class="text-gray-300 mb-8 text-lg">
                The page you're looking for doesn't exist or has been moved.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}"
                   class="bg-gradient-to-r from-gold to-gold-deep text-white px-8 py-3 rounded-xl font-semibold hover:from-gold-deep hover:to-gold transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-home mr-2"></i>
                    Go Home
                </a>
                <button onclick="history.back()"
                        class="bg-white/10 text-white px-8 py-3 rounded-xl font-semibold hover:bg-white/20 transition-all duration-300 border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Go Back
                </button>
            </div>

            <!-- Search Suggestion -->
            <div class="mt-8 p-6 bg-white/5 rounded-2xl border border-white/10">
                <h3 class="text-white font-semibold mb-3">Looking for something specific?</h3>
                <div class="flex gap-2">
                    <a href="{{ route('services') }}" class="text-gold hover:text-gold-2 transition-colors">
                        <i class="fas fa-stethoscope mr-1"></i>Our Services
                    </a>
                    <span class="text-gray-400">•</span>
                    <a href="/ai" class="text-gold hover:text-gold-2 transition-colors">
                        <i class="fas fa-robot mr-1"></i>AI Assistant
                    </a>
                    <span class="text-gray-400">•</span>
                    <a href="{{ route('doctors.index') }}" class="text-gold hover:text-gold-2 transition-colors">
                        <i class="fas fa-user-md mr-1"></i>Find Doctors
                    </a>
                </div>
            </div>
        </div>

        <!-- Floating Elements -->
        <div class="absolute top-20 left-20 w-32 h-32 bg-gradient-to-br from-gold/20 to-transparent rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-br from-blue-500/20 to-transparent rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-br from-purple-500/10 to-transparent rounded-full blur-3xl"></div>
    </div>
</div>
@endsection
