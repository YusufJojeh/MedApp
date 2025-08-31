@extends('layouts.app')

@section('title', 'Appointment Details - Doctor Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Appointment Details</h1>
                    <p class="text-muted mt-2">View and manage appointment information</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="btn btn-outline" onclick="window.location.href='{{ route('doctor.appointments.index') }}'">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Appointments
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6">
            <!-- Appointment Header -->
            <div class="card feature-card">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-text">Appointment #{{ $appointment->id }}</h2>
                        <span class="px-4 py-2 text-sm rounded-full font-medium
                            @if($appointment->STATUS === 'scheduled') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                            @elseif($appointment->STATUS === 'confirmed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($appointment->STATUS === 'completed') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                            @elseif($appointment->STATUS === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $appointment->STATUS)) }}
                        </span>
                    </div>

                    <!-- Quick Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <p class="text-sm text-muted">Date</p>
                            <p class="font-medium text-text">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, M j, Y') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <p class="text-sm text-muted">Time</p>
                            <p class="font-medium text-text">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <p class="text-sm text-muted">Type</p>
                            <p class="font-medium text-text">{{ explode(' - ', $appointment->notes)[0] ?? 'General Consultation' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="card feature-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-text mb-4">Patient Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-muted">Full Name</p>
                            <p class="font-medium text-text text-lg">{{ $appointment->patient_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Phone Number</p>
                            <p class="font-medium text-text">{{ $appointment->patient_phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Email Address</p>
                            <p class="font-medium text-text">{{ $appointment->patient_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Date of Birth</p>
                            <p class="font-medium text-text">{{ $appointment->date_of_birth ? \Carbon\Carbon::parse($appointment->date_of_birth)->format('M j, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Gender</p>
                            <p class="font-medium text-text">{{ ucfirst($appointment->gender ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Blood Type</p>
                            <p class="font-medium text-text">{{ $appointment->blood_type ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($appointment->address)
                    <div class="mt-6">
                        <p class="text-sm text-muted">Address</p>
                        <p class="font-medium text-text">{{ $appointment->address }}</p>
                    </div>
                    @endif
                    @if($appointment->emergency_contact)
                    <div class="mt-4">
                        <p class="text-sm text-muted">Emergency Contact</p>
                        <p class="font-medium text-text">{{ $appointment->emergency_contact }}</p>
                    </div>
                    @endif
                    @if($appointment->medical_history)
                    <div class="mt-4">
                        <p class="text-sm text-muted">Medical History</p>
                        <p class="font-medium text-text">{{ $appointment->medical_history }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Appointment Details -->
            <div class="card feature-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-text mb-4">Appointment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-muted">Appointment Date</p>
                            <p class="font-medium text-text">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, M j, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Appointment Time</p>
                            <p class="font-medium text-text">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Consultation Type</p>
                            <p class="font-medium text-text">{{ explode(' - ', $appointment->notes)[0] ?? 'General Consultation' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Duration</p>
                            <p class="font-medium text-text">30 minutes</p>
                        </div>
                    </div>
                    @if($appointment->notes)
                    <div class="mt-6">
                        <p class="text-sm text-muted">Notes</p>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mt-2">
                            <p class="font-medium text-text">{{ $appointment->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            @if($payment)
            <div class="card feature-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-text mb-4">Payment Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-muted">Amount</p>
                            <p class="font-medium text-text text-lg">${{ number_format($payment->amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Payment Status</p>
                            <span class="px-3 py-1 text-sm rounded-full font-medium
                                @if($payment->STATUS === 'succeeded') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($payment->STATUS === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @endif">
                                {{ ucfirst($payment->STATUS) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Payment Date</p>
                            <p class="font-medium text-text">{{ \Carbon\Carbon::parse($payment->created_at)->format('M j, Y g:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Payment Provider</p>
                            <p class="font-medium text-text">{{ ucfirst($payment->provider ?? 'N/A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Patient History -->
            @if($patientHistory->count() > 0)
            <div class="card feature-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-text mb-4">Recent Appointments</h3>
                    <div class="space-y-3">
                        @foreach($patientHistory as $history)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div>
                                <p class="font-medium text-text">{{ \Carbon\Carbon::parse($history->appointment_date)->format('M j, Y') }}</p>
                                <p class="text-sm text-muted">{{ explode(' - ', $history->notes)[0] ?? 'General Consultation' }}</p>
                            </div>
                            <span class="px-3 py-1 text-sm rounded-full font-medium
                                @if($history->STATUS === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($history->STATUS === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $history->STATUS)) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="card feature-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-text mb-4">Actions</h3>
                    <div class="flex flex-wrap gap-4">
                        @if($appointment->STATUS === 'scheduled')
                        <button class="btn btn-primary" onclick="updateStatus({{ $appointment->id }}, 'confirmed')">
                            <i class="fas fa-check mr-2"></i>Confirm Appointment
                        </button>
                        <button class="btn btn-danger" onclick="updateStatus({{ $appointment->id }}, 'cancelled')">
                            <i class="fas fa-times mr-2"></i>Cancel Appointment
                        </button>
                        @elseif($appointment->STATUS === 'confirmed')
                        <button class="btn btn-success" onclick="updateStatus({{ $appointment->id }}, 'completed')">
                            <i class="fas fa-check-double mr-2"></i>Mark as Complete
                        </button>
                        @endif
                        <button class="btn btn-outline" onclick="window.location.href='{{ route('doctor.appointments.index') }}'">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Update appointment status
    function updateStatus(appointmentId, status) {
        if (!confirm(`Are you sure you want to mark this appointment as ${status}?`)) {
            return;
        }

        fetch(`/doctor/appointments/${appointmentId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Appointment status updated successfully');
                window.location.reload();
            } else {
                alert('Error updating appointment status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating appointment status');
        });
    }
</script>
@endpush
@endsection
