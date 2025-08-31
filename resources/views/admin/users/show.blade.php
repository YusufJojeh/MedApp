@extends('layouts.app')

@section('title', 'User Details - Admin Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">User Details</h1>
                    <p class="text-muted mt-2">View user information and activities</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- User Information -->
            <div class="lg:col-span-2">
                <div class="card feature-card">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-text mb-6">User Information</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Full Name</label>
                                <p class="text-text font-semibold">{{ $user->first_name }} {{ $user->last_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Username</label>
                                <p class="text-text font-semibold">{{ $user->username }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Email</label>
                                <p class="text-text font-semibold">{{ $user->email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Phone</label>
                                <p class="text-text font-semibold">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Role</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->role_badge_class ?? 'bg-gray-500/20 text-gray-400' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Status</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->status_badge_class ?? 'bg-gray-500/20 text-gray-400' }}">
                                    {{ $user->status_text ?? 'Unknown' }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Joined</label>
                                <p class="text-text font-semibold">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Last Updated</label>
                                <p class="text-text font-semibold">{{ $user->updated_at ? $user->updated_at->format('M d, Y H:i') : 'N/A' }}</p>
                            </div>
                        </div>

                        @if($user->role === 'doctor')
                        <div class="mt-8">
                            <h3 class="text-xl font-bold text-text mb-4">Doctor Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-muted mb-2">Specialty</label>
                                    <p class="text-text font-semibold">{{ $user->specialty_name ?? 'Not specified' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-muted mb-2">Consultation Fee</label>
                                    <p class="text-text font-semibold">${{ number_format($user->consultation_fee ?? 0, 2) }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-muted mb-2">Experience Years</label>
                                    <p class="text-text font-semibold">{{ $user->experience_years ?? 'Not specified' }} years</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-muted mb-2">Rating</label>
                                    <p class="text-text font-semibold">{{ $user->rating ?? 'No ratings' }}</p>
                                </div>

                                @if($user->education)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-muted mb-2">Education</label>
                                    <p class="text-text">{{ $user->education }}</p>
                                </div>
                                @endif

                                @if($user->languages)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-muted mb-2">Languages</label>
                                    <p class="text-text">{{ $user->languages }}</p>
                                </div>
                                @endif

                                @if($user->description)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-muted mb-2">Description</label>
                                    <p class="text-text">{{ $user->description }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($user->role === 'patient')
                        <div class="mt-8">
                            <h3 class="text-xl font-bold text-text mb-4">Patient Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-muted mb-2">Date of Birth</label>
                                    <p class="text-text font-semibold">{{ $user->date_of_birth ?? 'Not provided' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-muted mb-2">Gender</label>
                                    <p class="text-text font-semibold">{{ ucfirst($user->gender ?? 'Not specified') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-muted mb-2">Blood Type</label>
                                    <p class="text-text font-semibold">{{ $user->blood_type ?? 'Not specified' }}</p>
                                </div>

                                @if($user->address)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-muted mb-2">Address</label>
                                    <p class="text-text">{{ $user->address }}</p>
                                </div>
                                @endif

                                @if($user->emergency_contact)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-muted mb-2">Emergency Contact</label>
                                    <p class="text-text">{{ $user->emergency_contact }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- User Activities -->
            <div class="lg:col-span-1">
                <div class="card feature-card">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-text mb-6">Recent Activities</h2>

                        @if(isset($activities['appointments']) && $activities['appointments']->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-text mb-3">Recent Appointments</h3>
                            <div class="space-y-3">
                                @foreach($activities['appointments']->take(5) as $appointment)
                                <div class="bg-surface-light rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-text">
                                                @if($user->role === 'doctor')
                                                    {{ $appointment->patient_name }}
                                                @else
                                                    Dr. {{ $appointment->doctor_name }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-muted">{{ $appointment->appointment_date }} at {{ $appointment->appointment_time }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($appointment->STATUS === 'confirmed') bg-green-500/20 text-green-400
                                            @elseif($appointment->STATUS === 'completed') bg-blue-500/20 text-blue-400
                                            @elseif($appointment->STATUS === 'cancelled') bg-red-500/20 text-red-400
                                            @else bg-yellow-500/20 text-yellow-400 @endif">
                                            {{ ucfirst($appointment->STATUS) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(isset($activities['payments']) && $activities['payments']->count() > 0)
                        <div>
                            <h3 class="text-lg font-semibold text-text mb-3">Recent Payments</h3>
                            <div class="space-y-3">
                                @foreach($activities['payments']->take(5) as $payment)
                                <div class="bg-surface-light rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-text">${{ number_format($payment->amount, 2) }}</p>
                                            <p class="text-xs text-muted">{{ $payment->created_at->format('M d, Y') }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($payment->status === 'completed') bg-green-500/20 text-green-400
                                            @elseif($payment->status === 'pending') bg-yellow-500/20 text-yellow-400
                                            @else bg-red-500/20 text-red-400 @endif">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if((!isset($activities['appointments']) || $activities['appointments']->count() === 0) &&
                            (!isset($activities['payments']) || $activities['payments']->count() === 0))
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-muted mb-4"></i>
                            <p class="text-muted">No recent activities found</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
