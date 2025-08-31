@extends('layouts.app')

@section('title', 'Payment Details - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payment Details</h1>
                    <p class="text-gray-600 dark:text-gray-400">View complete information about this payment transaction</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Payments
                    </a>
                    <button class="btn btn-outline" onclick="exportPayment()">
                        <i class="fas fa-download mr-2"></i>
                        Export Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Payment Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-credit-card text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Payment #{{ $payment->transaction_id ?? $payment->id }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">{{ ucfirst($payment->provider) }}</p>
                            <div class="flex items-center mt-2">
                                <span class="px-3 py-1 text-sm rounded-full
                                    @if($payment->STATUS === 'succeeded') bg-green-100 text-green-800
                                    @elseif($payment->STATUS === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($payment->STATUS === 'failed') bg-red-100 text-red-800
                                    @elseif($payment->STATUS === 'refunded') bg-gray-100 text-gray-800
                                    @elseif($payment->STATUS === 'canceled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($payment->STATUS) }}
                                </span>
                                <span class="ml-4 text-2xl font-bold text-gray-900 dark:text-white">
                                    ${{ number_format($payment->amount, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Information</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Transaction ID:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $payment->transaction_id ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($payment->provider) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">${{ number_format($payment->amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                    <span class="font-medium
                                        @if($payment->STATUS === 'succeeded') text-green-600
                                        @elseif($payment->STATUS === 'pending') text-yellow-600
                                        @elseif($payment->STATUS === 'failed') text-red-600
                                        @elseif($payment->STATUS === 'refunded') text-gray-600
                                        @elseif($payment->STATUS === 'canceled') text-red-600
                                        @else text-gray-600 @endif">
                                        {{ ucfirst($payment->STATUS) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Payment Date:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y H:i') }}
                                    </span>
                                </div>
                                @if($payment->updated_at != $payment->created_at)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Last Updated:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($payment->updated_at)->format('M d, Y H:i') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appointment Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Appointment Date:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($payment->appointment_date)->format('M d, Y') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Appointment Time:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($payment->appointment_time)->format('H:i') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Doctor:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">Dr. {{ $payment->doctor_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Patient:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $payment->first_name }} {{ $payment->last_name }}</span>
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
                                <span class="text-gray-700 dark:text-gray-300">{{ $payment->first_name }} {{ $payment->last_name }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ $payment->email }}</span>
                            </div>
                            @if($payment->phone)
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $payment->phone }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-3">
                            @if($payment->patient_dob)
                                <div class="flex items-center">
                                    <i class="fas fa-birthday-cake text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($payment->patient_dob)->format('M d, Y') }}</span>
                                </div>
                            @endif
                            @if($payment->patient_gender)
                                <div class="flex items-center">
                                    <i class="fas fa-venus-mars text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ ucfirst($payment->patient_gender) }}</span>
                                </div>
                            @endif
                            @if($payment->patient_blood_type)
                                <div class="flex items-center">
                                    <i class="fas fa-tint text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $payment->patient_blood_type }}</span>
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
                                <span class="text-gray-700 dark:text-gray-300">Dr. {{ $payment->doctor_name }}</span>
                            </div>
                            @if($payment->doctor_specialty)
                                <div class="flex items-center">
                                    <i class="fas fa-stethoscope text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $payment->doctor_specialty }}</span>
                                </div>
                            @endif
                            @if($payment->doctor_experience)
                                <div class="flex items-center">
                                    <i class="fas fa-briefcase text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $payment->doctor_experience }} years experience</span>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-3">
                            @if($payment->doctor_rating)
                                <div class="flex items-center">
                                    <i class="fas fa-star text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ number_format($payment->doctor_rating, 1) }} Rating</span>
                                </div>
                            @endif
                            @if($payment->consultation_fee)
                                <div class="flex items-center">
                                    <i class="fas fa-dollar-sign text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">${{ number_format($payment->consultation_fee, 2) }} consultation fee</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.appointments.show', $payment->appointment_id) }}" class="w-full btn btn-outline">
                            <i class="fas fa-calendar-check mr-2"></i>
                            View Appointment
                        </a>

                        <a href="{{ route('admin.doctors.show', $payment->doctor_id) }}" class="w-full btn btn-outline">
                            <i class="fas fa-user-md mr-2"></i>
                            View Doctor
                        </a>

                        <button class="w-full btn btn-outline" onclick="exportPayment()">
                            <i class="fas fa-download mr-2"></i>
                            Export Receipt
                        </button>
                    </div>
                </div>

                <!-- Payment Status Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Status</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Current Status:</span>
                            <span class="font-medium
                                @if($payment->STATUS === 'succeeded') text-green-600
                                @elseif($payment->STATUS === 'pending') text-yellow-600
                                @elseif($payment->STATUS === 'failed') text-red-600
                                @elseif($payment->STATUS === 'refunded') text-gray-600
                                @elseif($payment->STATUS === 'canceled') text-red-600
                                @else text-gray-600 @endif">
                                {{ ucfirst($payment->STATUS) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($payment->provider) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Amount Paid:</span>
                            <span class="font-medium text-gray-900 dark:text-white">${{ number_format($payment->amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Transaction Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Transaction Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Transaction ID:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->transaction_id ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Created:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Time:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($payment->created_at)->format('H:i') }}
                            </span>
                        </div>
                        @if($payment->updated_at != $payment->created_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Last Updated:</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($payment->updated_at)->format('M d, Y H:i') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportPayment() {
    // Create a simple receipt export
    const receiptData = {
        transactionId: '{{ $payment->transaction_id ?? $payment->id }}',
        amount: '${{ number_format($payment->amount, 2) }}',
        patient: '{{ $payment->first_name }} {{ $payment->last_name }}',
        doctor: 'Dr. {{ $payment->doctor_name }}',
        date: '{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y H:i') }}',
                    status: '{{ ucfirst($payment->STATUS) }}'
    };

    // Create receipt content
    const receipt = `
Payment Receipt
===============

Transaction ID: ${receiptData.transactionId}
Amount: ${receiptData.amount}
Patient: ${receiptData.patient}
Doctor: ${receiptData.doctor}
Date: ${receiptData.date}
Status: ${receiptData.status}

Thank you for your payment!
    `;

    // Create and download file
    const blob = new Blob([receipt], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `payment-receipt-${receiptData.transactionId}.txt`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
}
</script>
@endsection
