<div class="space-y-6">
    <!-- Appointment Header -->
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-semibold text-text">Appointment Details</h4>
            <span class="px-3 py-1 text-sm rounded-full
                @if($appointment->STATUS === 'scheduled') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                @elseif($appointment->STATUS === 'confirmed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                @elseif($appointment->STATUS === 'completed') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                @elseif($appointment->STATUS === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                @endif">
                {{ ucfirst(str_replace('_', ' ', $appointment->STATUS)) }}
            </span>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h5 class="font-medium text-text mb-3">Patient Information</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-muted">Name</p>
                <p class="font-medium text-text">{{ $appointment->patient_name }}</p>
            </div>
            <div>
                <p class="text-sm text-muted">Phone</p>
                <p class="font-medium text-text">{{ $appointment->patient_phone }}</p>
            </div>
            <div>
                <p class="text-sm text-muted">Email</p>
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
        <div class="mt-3">
            <p class="text-sm text-muted">Address</p>
            <p class="font-medium text-text">{{ $appointment->address }}</p>
        </div>
        @endif
        @if($appointment->emergency_contact)
        <div class="mt-3">
            <p class="text-sm text-muted">Emergency Contact</p>
            <p class="font-medium text-text">{{ $appointment->emergency_contact }}</p>
        </div>
        @endif
    </div>

    <!-- Appointment Details -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h5 class="font-medium text-text mb-3">Appointment Details</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-muted">Date</p>
                <p class="font-medium text-text">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, M j, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-muted">Time</p>
                <p class="font-medium text-text">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
            </div>
            <div>
                <p class="text-sm text-muted">Type</p>
                <p class="font-medium text-text">{{ explode(' - ', $appointment->notes)[0] ?? 'General Consultation' }}</p>
            </div>
            <div>
                <p class="text-sm text-muted">Duration</p>
                <p class="font-medium text-text">30 minutes</p>
            </div>
        </div>
        @if($appointment->notes)
        <div class="mt-3">
            <p class="text-sm text-muted">Notes</p>
            <p class="font-medium text-text">{{ $appointment->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Payment Information -->
    @if($payment)
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h5 class="font-medium text-text mb-3">Payment Information</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-muted">Amount</p>
                <p class="font-medium text-text">${{ number_format($payment->amount, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-muted">Status</p>
                <span class="px-2 py-1 text-xs rounded-full
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
                <p class="text-sm text-muted">Provider</p>
                <p class="font-medium text-text">{{ ucfirst($payment->provider ?? 'N/A') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Patient History -->
    @if($patientHistory->count() > 0)
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h5 class="font-medium text-text mb-3">Recent Appointments</h5>
        <div class="space-y-2">
            @foreach($patientHistory as $history)
            <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-700 rounded">
                <div>
                    <p class="font-medium text-text">{{ \Carbon\Carbon::parse($history->appointment_date)->format('M j, Y') }}</p>
                    <p class="text-sm text-muted">{{ explode(' - ', $history->notes)[0] ?? 'General Consultation' }}</p>
                </div>
                <span class="px-2 py-1 text-xs rounded-full
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
    @endif

    <!-- Actions -->
    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        @if($appointment->STATUS === 'scheduled')
        <button class="btn btn-primary" onclick="updateStatus({{ $appointment->id }}, 'confirmed')">
            <i class="fas fa-check mr-2"></i>Confirm
        </button>
        <button class="btn btn-danger" onclick="updateStatus({{ $appointment->id }}, 'cancelled')">
            <i class="fas fa-times mr-2"></i>Cancel
        </button>
        @elseif($appointment->STATUS === 'confirmed')
        <button class="btn btn-success" onclick="updateStatus({{ $appointment->id }}, 'completed')">
            <i class="fas fa-check-double mr-2"></i>Mark Complete
        </button>
        @endif
        <button class="btn btn-outline" onclick="closeModal()">
            <i class="fas fa-times mr-2"></i>Close
        </button>
    </div>
</div>
