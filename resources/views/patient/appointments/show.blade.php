@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text mb-2">Appointment Details</h1>
                    <p class="text-muted">View your appointment information and status</p>
                </div>
                <a href="{{ route('patient.appointments.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Appointments
                </a>
            </div>
        </div>

        <!-- Appointment Status Badge -->
        <div class="mb-6">
            @php
                $statusColors = [
                    'scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                    'no-show' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                ];
                $statusColor = $statusColors[$appointment->STATUS] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                <i class="fas fa-circle mr-2"></i>
                {{ ucfirst($appointment->STATUS) }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Appointment Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Appointment Details Card -->
                <div class="card feature-card" data-aos="fade-up">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-text mb-4">Appointment Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Date</label>
                                <p class="text-text font-semibold">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F j, Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Time</label>
                                <p class="text-text font-semibold">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Duration</label>
                                <p class="text-text font-semibold">30 minutes</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Consultation Fee</label>
                                <p class="text-text font-semibold">${{ $appointment->consultation_fee }}</p>
                            </div>
                            @if($appointment->notes)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-muted mb-2">Notes</label>
                                <p class="text-text">{{ $appointment->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Doctor Information Card -->
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-text mb-4">Doctor Information</h2>
                        <div class="flex items-start space-x-4">
                            <div class="w-16 h-16 bg-gold rounded-full flex items-center justify-center">
                                <i class="fas fa-user-md text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-text mb-2">{{ $appointment->doctor_name }}</h3>
                                <p class="text-muted mb-3">{{ $appointment->specialty_name }}</p>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-muted">Experience:</span>
                                        <span class="text-text font-medium">{{ $appointment->experience_years }} years</span>
                                    </div>
                                    <div>
                                        <span class="text-muted">Rating:</span>
                                        <span class="text-text font-medium">{{ $appointment->rating }}/5 ⭐</span>
                                    </div>
                                    <div>
                                        <span class="text-muted">Education:</span>
                                        <span class="text-text font-medium">{{ $appointment->education ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted">Languages:</span>
                                        <span class="text-text font-medium">{{ $appointment->languages ?? 'English' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information Card -->
                @if($payment)
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-text mb-4">Payment Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Payment Status</label>
                                @php
                                    $paymentStatusColors = [
                                        'succeeded' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'failed' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                    ];
                                    $paymentStatusColor = $paymentStatusColors[$payment->STATUS] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $paymentStatusColor }}">
                                    {{ ucfirst($payment->STATUS) }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Amount Paid</label>
                                <p class="text-text font-semibold">${{ $payment->amount }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Payment Method</label>
                                <p class="text-text font-semibold">{{ ucfirst($payment->payment_method ?? 'N/A') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-muted mb-2">Payment Date</label>
                                <p class="text-text font-semibold">{{ \Carbon\Carbon::parse($payment->created_at)->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Doctor's Working Hours -->
                @if($workingHours->count() > 0)
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-text mb-4">Doctor's Working Hours</h3>
                        <div class="space-y-3">
                            @php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            @endphp
                            @foreach($workingHours as $hour)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted">{{ $days[$hour->day_of_week - 1] ?? 'Unknown' }}</span>
                                    @if($hour->is_available)
                                        <span class="text-sm text-text font-medium">
                                            {{ \Carbon\Carbon::parse($hour->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($hour->end_time)->format('g:i A') }}
                                        </span>
                                    @else
                                        <span class="text-sm text-red-500">Not Available</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Actions Card -->
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-text mb-4">Actions</h3>
                        <div class="space-y-3">
                            @if($appointment->STATUS === 'scheduled')
                                <button class="w-full btn btn-outline text-red-600 border-red-600 hover:bg-red-600 hover:text-white" onclick="cancelAppointment()">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancel Appointment
                                </button>
                                <button class="w-full btn btn-outline" onclick="rescheduleAppointment()">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Reschedule
                                </button>
                            @endif

                            @if($appointment->STATUS === 'completed')
                                <button class="w-full btn btn-primary" onclick="bookFollowUp()">
                                    <i class="fas fa-plus mr-2"></i>
                                    Book Follow-up
                                </button>
                            @endif

                            <button class="w-full btn btn-outline" onclick="downloadReceipt()">
                                <i class="fas fa-download mr-2"></i>
                                Download Receipt
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Contact -->
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-text mb-4">Need Help?</h3>
                        <p class="text-sm text-muted mb-4">Contact our support team for assistance</p>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-phone text-gold mr-2"></i>
                                <span class="text-text">+1 (555) 123-4567</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-envelope text-gold mr-2"></i>
                                <span class="text-text">support@medicalbooking.com</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Appointment Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-text">Cancel Appointment</h3>
                <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" onclick="closeCancelModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <p class="text-muted mb-6">Are you sure you want to cancel this appointment? This action cannot be undone.</p>
            <div class="flex space-x-3">
                <button class="btn btn-outline flex-1" onclick="closeCancelModal()">Keep Appointment</button>
                <button class="btn btn-danger flex-1" onclick="confirmCancel()">Cancel Appointment</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function cancelAppointment() {
        document.getElementById('cancelModal').classList.remove('hidden');
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }

    function confirmCancel() {
        fetch(`{{ route('patient.appointments.cancel', $appointment->id) }}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Appointment cancelled successfully', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("patient.appointments.index") }}';
                }, 1500);
            } else {
                showNotification(data.message || 'Error cancelling appointment', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error cancelling appointment', 'error');
        });

        closeCancelModal();
    }

    function rescheduleAppointment() {
        window.location.href = '{{ route("patient.appointments.create") }}?reschedule={{ $appointment->id }}';
    }

    function bookFollowUp() {
        window.location.href = '{{ route("patient.appointments.create") }}?followup={{ $appointment->id }}';
    }

    function downloadReceipt() {
        // Implement receipt download functionality
        showNotification('Receipt download feature coming soon', 'info');
    }

    function showNotification(message, type) {
        // Simple notification function - you can enhance this
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' :
            type === 'info' ? 'bg-blue-500' : 'bg-gray-500'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
</script>
@endpush
