@extends('layouts.app')

@section('title', 'Patient Details - Admin Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Patient Details</h1>
                    <p class="text-muted mt-2">View patient information and activities</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.patients.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Patients
                    </a>
                    <a href="{{ route('admin.patients.edit', $patient->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Patient
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Patient Information -->
            <div class="lg:col-span-2">
                <div class="card feature-card">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-text mb-6">Patient Information</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Full Name</label>
                                <p class="text-text font-semibold">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Username</label>
                                <p class="text-text font-semibold">{{ $patient->username }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Email</label>
                                <p class="text-text font-semibold">{{ $patient->email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Phone</label>
                                <p class="text-text font-semibold">{{ $patient->phone ?? 'Not provided' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Age</label>
                                <p class="text-text font-semibold">{{ $patient->age }} years</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Gender</label>
                                <p class="text-text font-semibold">{{ ucfirst($patient->gender ?? 'Not specified') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Blood Type</label>
                                <p class="text-text font-semibold">{{ $patient->blood_type ?? 'Not specified' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Status</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $patient->status_badge_class ?? 'bg-gray-500/20 text-gray-400' }}">
                                    {{ ucfirst($patient->status) }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Joined</label>
                                <p class="text-text font-semibold">{{ $patient->created_at ? $patient->created_at->format('M d, Y') : 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Last Updated</label>
                                <p class="text-text font-semibold">{{ $patient->updated_at ? $patient->updated_at->format('M d, Y H:i') : 'N/A' }}</p>
                            </div>
                        </div>

                        @if($patient->address)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-muted mb-2">Address</label>
                            <p class="text-text">{{ $patient->address }}</p>
                        </div>
                        @endif

                        @if($patient->medical_history)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-muted mb-2">Medical History</label>
                            <p class="text-text">{{ $patient->medical_history }}</p>
                        </div>
                        @endif

                        @if($patient->emergency_contact)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-muted mb-2">Emergency Contact</label>
                            <p class="text-text">{{ $patient->emergency_contact }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Patient Statistics -->
                <div class="card feature-card mt-8">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Patient Statistics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">{{ $stats['total_appointments'] }}</div>
                                <div class="text-sm text-muted">Total Appointments</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600">{{ $stats['completed_appointments'] }}</div>
                                <div class="text-sm text-muted">Completed</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-yellow-600">{{ $stats['upcoming_appointments'] }}</div>
                                <div class="text-sm text-muted">Upcoming</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600">${{ number_format($stats['total_spent'], 2) }}</div>
                                <div class="text-sm text-muted">Total Spent</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Activities -->
            <div class="lg:col-span-1">
                <div class="card feature-card">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-text mb-6">Recent Activities</h2>

                        @if($appointments->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-text mb-3">Recent Appointments</h3>
                            <div class="space-y-3">
                                @foreach($appointments->take(5) as $appointment)
                                <div class="bg-surface-light rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-text">
                                                Dr. {{ $appointment->doctor_name }}
                                            </p>
                                            <p class="text-xs text-muted">{{ $appointment->specialty_name }}</p>
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

                        @if($payments->count() > 0)
                        <div>
                            <h3 class="text-lg font-semibold text-text mb-3">Recent Payments</h3>
                            <div class="space-y-3">
                                @foreach($payments->take(5) as $payment)
                                <div class="bg-surface-light rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-text">
                                                ${{ number_format($payment->amount, 2) }}
                                            </p>
                                            <p class="text-xs text-muted">Dr. {{ $payment->doctor_name }}</p>
                                            <p class="text-xs text-muted">{{ $payment->created_at->format('M d, Y') }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($payment->STATUS === 'completed') bg-green-500/20 text-green-400
                                            @elseif($payment->STATUS === 'pending') bg-yellow-500/20 text-yellow-400
                                            @else bg-red-500/20 text-red-400 @endif">
                                            {{ ucfirst($payment->STATUS) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($appointments->count() === 0 && $payments->count() === 0)
                        <div class="text-center text-muted py-8">
                            <i class="fas fa-info-circle text-4xl mb-4"></i>
                            <p>No recent activities</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment History -->
        @if($appointments->count() > 0)
        <div class="card feature-card mt-8">
            <div class="p-6">
                <h3 class="text-xl font-bold text-text mb-6">Appointment History</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-medium text-muted">Date & Time</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Doctor</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Specialty</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Status</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-3 px-4">
                                    <div>
                                        <div class="text-text font-medium">{{ $appointment->appointment_date }}</div>
                                        <div class="text-sm text-muted">{{ $appointment->appointment_time }}</div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-text">{{ $appointment->doctor_name }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-text">{{ $appointment->specialty_name }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($appointment->STATUS === 'confirmed') bg-green-500/20 text-green-400
                                        @elseif($appointment->STATUS === 'completed') bg-blue-500/20 text-blue-400
                                        @elseif($appointment->STATUS === 'cancelled') bg-red-500/20 text-red-400
                                        @else bg-yellow-500/20 text-yellow-400 @endif">
                                        {{ ucfirst($appointment->STATUS) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-text">${{ number_format($appointment->consultation_fee ?? 0, 2) }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Payment History -->
        @if($payments->count() > 0)
        <div class="card feature-card mt-8">
            <div class="p-6">
                <h3 class="text-xl font-bold text-text mb-6">Payment History</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-medium text-muted">Date</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Doctor</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Amount</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Method</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-3 px-4">
                                    <div class="text-text">{{ $payment->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-text">{{ $payment->doctor_name }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-text font-medium">${{ number_format($payment->amount, 2) }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-text">{{ ucfirst($payment->provider ?? 'N/A') }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($payment->STATUS === 'completed') bg-green-500/20 text-green-400
                                        @elseif($payment->STATUS === 'pending') bg-yellow-500/20 text-yellow-400
                                        @else bg-red-500/20 text-red-400 @endif">
                                        {{ ucfirst($payment->STATUS) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
