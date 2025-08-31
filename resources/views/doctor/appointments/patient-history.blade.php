<div class="space-y-6">
    <!-- Patient Information Header -->
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-text">{{ $patient->NAME }}</h4>
                    <p class="text-sm text-muted">{{ $patient->phone }}</p>
                    <p class="text-sm text-muted">{{ $patient->email }}</p>
                    <p class="text-sm text-muted">Patient ID: {{ $patient->id }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-muted">First Visit: {{ $stats['first_visit'] }}</p>
                <p class="text-sm text-muted">Last Visit: {{ $stats['last_visit'] }}</p>
            </div>
        </div>
    </div>

    <!-- Patient Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-calendar text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_appointments'] }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400">Total Visits</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $stats['completed_appointments'] }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400">Completed</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-dollar-sign text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-purple-600 dark:text-purple-400">${{ number_format($stats['total_paid'], 0) }}</p>
                    <p class="text-xs text-purple-600 dark:text-purple-400">Total Paid</p>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-user-times text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['no_show_appointments'] }}</p>
                    <p class="text-xs text-yellow-600 dark:text-yellow-400">No Shows</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Medical Information -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
        <h5 class="font-medium text-text mb-3">Medical Information</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-muted">Date of Birth:</p>
                <p class="text-text">{{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('M j, Y') : 'Not provided' }}</p>
            </div>
            <div>
                <p class="text-muted">Gender:</p>
                <p class="text-text capitalize">{{ $patient->gender ?? 'Not provided' }}</p>
            </div>
            <div>
                <p class="text-muted">Blood Type:</p>
                <p class="text-text">{{ $patient->blood_type ?? 'Not provided' }}</p>
            </div>
            <div>
                <p class="text-muted">Emergency Contact:</p>
                <p class="text-text">{{ $patient->emergency_contact ?? 'Not provided' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-muted">Medical History:</p>
                <p class="text-text">{{ $patient->medical_history ?? 'No medical history recorded' }}</p>
            </div>
        </div>
    </div>

    <!-- Appointment History -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-medium text-text">Appointment History ({{ $appointments->count() }} appointments)</h5>
            <div class="text-sm text-muted">
                @if($stats['cancelled_appointments'] > 0)
                    <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded text-xs mr-2">
                        {{ $stats['cancelled_appointments'] }} Cancelled
                    </span>
                @endif
                @if($stats['pending_payments'] > 0)
                    <span class="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">
                        {{ $stats['pending_payments'] }} Pending Payments
                    </span>
                @endif
            </div>
        </div>

        @if($appointments->count() > 0)
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @foreach($appointments as $appointment)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-text">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, M j, Y') }}
                                    </p>
                                    <p class="text-sm text-muted">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                @php
                                    $statusColor = match($appointment->STATUS) {
                                        'scheduled' => 'blue',
                                        'confirmed' => 'green',
                                        'completed' => 'purple',
                                        'cancelled' => 'red',
                                        'no_show' => 'yellow',
                                        default => 'gray'
                                    };
                                @endphp
                                <span class="px-3 py-1 text-xs rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-900 dark:text-{{ $statusColor }}-200">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->STATUS)) }}
                                </span>
                                @if($appointment->payment_status)
                                    @php
                                        $paymentColor = match($appointment->payment_status) {
                                            'succeeded' => 'green',
                                            'pending' => 'yellow',
                                            'failed' => 'red',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <div class="mt-1">
                                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $paymentColor }}-100 text-{{ $paymentColor }}-800 dark:bg-{{ $paymentColor }}-900 dark:text-{{ $paymentColor }}-200">
                                            {{ ucfirst($appointment->payment_status) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <i class="fas fa-stethoscope text-muted mr-2 w-4"></i>
                                    <span class="text-text">{{ explode(' - ', $appointment->notes)[0] ?? 'General Consultation' }}</span>
                                </div>

                                @if($appointment->notes)
                                    <div class="flex items-start">
                                        <i class="fas fa-file-alt text-muted mr-2 mt-1 w-4"></i>
                                        <span class="text-text text-xs">{{ $appointment->notes }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-muted">Fee:</span>
                                    <span class="text-text font-medium">
                                        @if($appointment->payment_amount)
                                            ${{ number_format($appointment->payment_amount, 2) }}
                                        @else
                                            $200.00
                                        @endif
                                    </span>
                                </div>

                                @if($appointment->payment_method)
                                    <div class="flex items-center justify-between">
                                        <span class="text-muted">Payment Method:</span>
                                        <span class="text-text text-xs capitalize">{{ str_replace('_', ' ', $appointment->payment_method) }}</span>
                                    </div>
                                @endif

                                @if($appointment->payment_date)
                                    <div class="flex items-center justify-between">
                                        <span class="text-muted">Payment Date:</span>
                                        <span class="text-text text-xs">{{ \Carbon\Carbon::parse($appointment->payment_date)->format('M j, Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($appointment->created_at)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-muted">
                                    Appointment created: {{ \Carbon\Carbon::parse($appointment->created_at)->format('M j, Y g:i A') }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-muted">
                <i class="fas fa-calendar-times text-4xl mb-4"></i>
                <p>No appointment history found for this patient</p>
            </div>
        @endif
    </div>
</div>
