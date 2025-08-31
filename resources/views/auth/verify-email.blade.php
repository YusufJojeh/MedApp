@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="min-h-screen bg-surface flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center" data-aos="fade-down" data-aos-delay="100">
            <div class="mx-auto h-16 w-16 bg-gradient-to-br from-gold to-gold-deep rounded-2xl flex items-center justify-center mb-6">
                <i class="fas fa-envelope-open text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-text">Verify Your Email</h2>
            <p class="text-muted mt-2">Check your email for a verification link</p>
        </div>

        <!-- Verification Form -->
        <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
            <div class="p-8">
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>
                        A new verification link has been sent to your email address.
                    </div>
                @endif

                <div class="text-center mb-6">
                    <p class="text-muted">
                        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                    </p>
                </div>

                <div class="space-y-4">
                    <!-- Resend Verification Email -->
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="w-full btn btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Resend Verification Email
                        </button>
                    </form>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full btn btn-outline">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Log Out
                        </button>
                    </form>
                </div>

                <!-- Email Instructions -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <h4 class="text-sm font-medium text-text mb-2">What to do next:</h4>
                    <ol class="text-xs text-muted space-y-1">
                        <li class="flex items-start">
                            <span class="bg-gold text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 mt-0.5">1</span>
                            Check your email inbox (and spam folder)
                        </li>
                        <li class="flex items-start">
                            <span class="bg-gold text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 mt-0.5">2</span>
                            Click the verification link in the email
                        </li>
                        <li class="flex items-start">
                            <span class="bg-gold text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 mt-0.5">3</span>
                            Return to this page and refresh
                        </li>
                    </ol>
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
    // Auto-refresh after 30 seconds
    setTimeout(() => {
        window.location.reload();
    }, 30000);

    // Show notification when resending email
    document.querySelector('form[action*="verification.send"]').addEventListener('submit', function() {
        showNotification('Verification email sent!', 'success');
    });
</script>
@endpush
