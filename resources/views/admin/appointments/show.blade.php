@extends('layouts.app')

@section('title', 'Appointment Details - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Appointment Details</h1>
                    <p class="text-gray-600 dark:text-gray-400">View complete information about this appointment</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Appointments
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Appointment Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-check text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Appointment #{{ $appointment->id }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">{{ $appointment->appointment_date }} at {{ $appointment->appointment_time }}</p>
                            <div class="flex items-center mt-2">
                                <span class="px-3 py-1 text-sm rounded-full
                                    @if($appointment->STATUS === 'completed') bg-green-100 text-green-800
                                    @elseif($appointment->STATUS === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($appointment->STATUS === 'cancelled') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($appointment->STATUS) }}
                                </span>
                                @if($appointment->payment_status)
                                    <span class="ml-2 px-3 py-1 text-sm rounded-full
                                        @if($appointment->payment_status === 'paid') bg-green-100 text-green-800
                                        @elseif($appointment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($appointment->payment_status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appointment Details</h3>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F d, Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-dollar-sign text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">${{ number_format($appointment->consultation_fee ?? 0, 2) }}</span>
                                </div>
                                @if($appointment->payment_method)
                                <div class="flex items-center">
                                    <i class="fas fa-credit-card text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $appointment->payment_method)) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status Information</h3>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">Status: <strong>{{ ucfirst($appointment->STATUS) }}</strong></span>
                                </div>
                                @if($appointment->payment_status)
                                <div class="flex items-center">
                                    <i class="fas fa-credit-card text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">Payment: <strong>{{ ucfirst($appointment->payment_status) }}</strong></span>
                                </div>
                                @endif
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-plus text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">Created: {{ \Carbon\Carbon::parse($appointment->created_at)->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-edit text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">Updated: {{ \Carbon\Carbon::parse($appointment->updated_at)->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Patient Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-user text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ $appointment->patient_name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ $appointment->patient_email ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ $appointment->patient_phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            @if($appointment->date_of_birth)
                            <div class="flex items-center">
                                <i class="fas fa-birthday-cake text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($appointment->date_of_birth)->format('M d, Y') }}</span>
                            </div>
                            @endif
                            @if($appointment->gender)
                            <div class="flex items-center">
                                <i class="fas fa-venus-mars text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ ucfirst($appointment->gender) }}</span>
                            </div>
                            @endif
                            @if($appointment->blood_type)
                            <div class="flex items-center">
                                <i class="fas fa-tint text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ $appointment->blood_type }}</span>
                            </div>
                            @endif
                            @if($appointment->address)
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ $appointment->address }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Doctor Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Doctor Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-user-md text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">Dr. {{ $appointment->doctor_name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-stethoscope text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ $appointment->specialty_name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ $appointment->doctor_email ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-clock text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ $appointment->experience_years ?? 'N/A' }} years experience</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-star text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ number_format($appointment->rating ?? 0, 1) }} rating</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-dollar-sign text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">${{ number_format($appointment->consultation_fee ?? 0, 2) }} fee</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical History Card -->
                @if($appointment->medical_history)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Medical History</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $appointment->medical_history }}</p>
                </div>
                @endif

                <!-- Notes Card -->
                @if($appointment->notes)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $appointment->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button class="w-full btn btn-outline" onclick="viewPatientHistory({{ $appointment->patient_id }})">
                            <i class="fas fa-history mr-2"></i>
                            View Patient History
                        </button>

                        <button class="w-full btn btn-outline" onclick="viewDoctorSchedule({{ $appointment->doctor_id }})">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            View Doctor Schedule
                        </button>

                        <button class="w-full btn btn-outline" onclick="exportAppointment()">
                            <i class="fas fa-download mr-2"></i>
                            Export Details
                        </button>
                    </div>
                </div>

                <!-- Payment Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                ${{ number_format($appointment->consultation_fee ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Method:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $appointment->payment_method ? ucfirst(str_replace('_', ' ', $appointment->payment_method)) : 'N/A' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <span class="font-medium
                                @if($appointment->payment_status === 'paid') text-green-600
                                @elseif($appointment->payment_status === 'pending') text-yellow-600
                                @else text-red-600 @endif">
                                {{ $appointment->payment_status ? ucfirst($appointment->payment_status) : 'N/A' }}
                            </span>
                        </div>
                        @if($appointment->transaction_id)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Transaction ID:</span>
                            <span class="font-medium text-gray-900 dark:text-white text-sm">
                                {{ $appointment->transaction_id }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Timeline Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appointment Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-green-500 rounded-full mt-2 mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Appointment Created</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($appointment->created_at)->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @if($appointment->STATUS === 'confirmed' || $appointment->STATUS === 'completed')
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mt-2 mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Appointment Confirmed</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($appointment->updated_at)->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($appointment->STATUS === 'completed')
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-green-500 rounded-full mt-2 mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Appointment Completed</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($appointment->updated_at)->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($appointment->STATUS === 'cancelled')
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-red-500 rounded-full mt-2 mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Appointment Cancelled</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($appointment->updated_at)->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // View patient history
    function viewPatientHistory(patientId) {
        window.location.href = `/admin/patients/${patientId}/history`;
    }

    // View doctor schedule
    function viewDoctorSchedule(doctorId) {
        window.location.href = `/admin/doctors/${doctorId}/schedule`;
    }

    // Export appointment details
    function exportAppointment() {
        const appointmentId = {{ $appointment->id }};
        window.location.href = `/admin/appointments/${appointmentId}/export`;
    }
</script>
@endpush
