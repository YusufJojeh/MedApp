@extends('layouts.app')

@section('title', 'Patient Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Patient Dashboard</h1>
                    <p class="text-muted mt-2">Manage your health and appointments</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/ai" class="btn btn-primary">
                        <i class="fas fa-robot mr-2"></i>
                        AI Assistant
                    </a>
                    <div class="text-right">
                        <p class="text-sm text-muted">Welcome back,</p>
                        <p class="font-semibold text-text">{{ auth()->user()->name }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-injured text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Appointments -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white mr-4">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Total Appointments</p>
                            <p class="text-2xl font-bold text-text">{{ $totalAppointments }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Appointments -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white mr-4">
                            <i class="fas fa-calendar-day text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Today's Appointments</p>
                            <p class="text-2xl font-bold text-text">{{ $todayAppointments }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white mr-4">
                            <i class="fas fa-dollar-sign text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Total Spent</p>
                            <p class="text-2xl font-bold text-text">${{ number_format($totalSpent, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wallet Balance -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="400">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-deep rounded-xl flex items-center justify-center text-white mr-4">
                            <i class="fas fa-wallet text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Wallet Balance</p>
                            <p class="text-2xl font-bold text-text">${{ number_format($walletBalance, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Upcoming Appointments -->
            <div class="lg:col-span-2">
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-text">Upcoming Appointments</h3>
                            <a href="{{ route('patient.appointments.index') }}" class="text-gold hover:text-gold-deep font-medium">View All</a>
                        </div>
                        <div class="space-y-4">
                            @forelse($upcomingAppointments as $appointment)
                                <div class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-l-4 border-blue-500">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-user-md text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-text">{{ $appointment->doctor_name }}</p>
                                                <p class="text-sm text-muted">{{ $appointment->specialty_name }}</p>
                                                <p class="text-xs text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-xs font-medium">{{ ucfirst($appointment->STATUS) }}</span>
                                                <div class="mt-2 space-x-2">
                                                    <a href="{{ route('patient.appointments.show', $appointment->id) }}" class="text-gold hover:text-gold-deep text-sm">
                                                        <i class="fas fa-eye mr-1"></i>View
                                                    </a>
                                                    @if($appointment->STATUS !== 'cancelled')
                                                        <button class="text-red-600 hover:text-red-700 text-sm" onclick="cancelAppointment({{ $appointment->id }})">
                                                            <i class="fas fa-times mr-1"></i>Cancel
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-muted">No upcoming appointments</p>
                                    <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary mt-4">
                                        <i class="fas fa-plus mr-2"></i>Book Appointment
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="600">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-text mb-4">Quick Actions</h3>
                    <div class="space-y-4">
                        <a href="{{ route('patient.appointments.create') }}" class="flex items-center p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl text-white hover:transform hover:scale-105 transition-all">
                            <i class="fas fa-calendar-plus text-2xl mr-3"></i>
                            <div>
                                <p class="font-medium">Book Appointment</p>
                                <p class="text-sm opacity-90">Schedule with doctor</p>
                            </div>
                        </a>
                        <a href="{{ route('ai.assistant') }}" class="flex items-center p-4 bg-gradient-to-br from-green-500 to-green-600 rounded-xl text-white hover:transform hover:scale-105 transition-all">
                            <i class="fas fa-robot text-2xl mr-3"></i>
                            <div>
                                <p class="font-medium">AI Assistant</p>
                                <p class="text-sm opacity-90">Get health insights</p>
                            </div>
                        </a>
                        <a href="{{ route('patient.appointments.index') }}" class="flex items-center p-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl text-white hover:transform hover:scale-105 transition-all">
                            <i class="fas fa-file-medical text-2xl mr-3"></i>
                            <div>
                                <p class="font-medium">Medical Records</p>
                                <p class="text-sm opacity-90">View your history</p>
                            </div>
                        </a>
                        <a href="{{ route('patient.wallet.index') }}" class="flex items-center p-4 bg-gradient-to-br from-gold to-gold-deep rounded-xl text-white hover:transform hover:scale-105 transition-all">
                            <i class="fas fa-wallet text-2xl mr-3"></i>
                            <div>
                                <p class="font-medium">Wallet</p>
                                <p class="text-sm opacity-90">Manage payments</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Health Metrics -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="700">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-text mb-4">Health Metrics</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-heartbeat text-red-600 dark:text-red-400 text-sm"></i>
                                </div>
                                <span class="text-text">Blood Pressure</span>
                            </div>
                            <span class="font-medium text-text">120/80</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-weight text-blue-600 dark:text-blue-400 text-sm"></i>
                                </div>
                                <span class="text-text">Weight</span>
                            </div>
                            <span class="font-medium text-text">70 kg</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-thermometer-half text-green-600 dark:text-green-400 text-sm"></i>
                                </div>
                                <span class="text-text">Temperature</span>
                            </div>
                            <span class="font-medium text-text">36.8°C</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-tint text-purple-600 dark:text-purple-400 text-sm"></i>
                                </div>
                                <span class="text-text">Blood Sugar</span>
                            </div>
                            <span class="font-medium text-text">95 mg/dL</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Medical Records -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="800">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-text">Recent Medical Records</h3>
                        <a href="{{ route('patient.appointments.index') }}" class="text-gold hover:text-gold-deep font-medium">View All</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($recentAppointments as $appointment)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="font-medium text-text">{{ $appointment->specialty_name }}</p>
                                    <span class="text-xs text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</span>
                                </div>
                                <p class="text-sm text-muted mb-2">{{ $appointment->doctor_name }}</p>
                                <p class="text-xs text-muted">{{ $appointment->notes ?: 'Appointment completed successfully.' }}</p>
                                <div class="mt-2">
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-xs font-medium">{{ ucfirst($appointment->STATUS) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-file-medical text-gray-400"></i>
                                </div>
                                <p class="text-muted text-sm">No recent appointments</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Health Assistant -->
        <div class="card feature-card" data-aos="fade-up" data-aos-delay="900">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-text">AI Health Assistant</h3>
                    <div class="w-8 h-8 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center">
                        <i class="fas fa-robot text-white text-sm"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gold/10 to-gold-deep/10 rounded-lg p-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center">
                            <i class="fas fa-robot text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-text mb-2">Hello! I'm your AI health assistant. How can I help you today?</p>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 bg-gold text-white rounded-full text-sm hover:bg-gold-deep transition-colors">
                                    Check Symptoms
                                </button>
                                <button class="px-3 py-1 bg-gold text-white rounded-full text-sm hover:bg-gold-deep transition-colors">
                                    Health Tips
                                </button>
                                <button class="px-3 py-1 bg-gold text-white rounded-full text-sm hover:bg-gold-deep transition-colors">
                                    Medication Reminder
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function cancelAppointment(appointmentId) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            fetch(`/patient/appointments/${appointmentId}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Appointment cancelled successfully!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification(data.message || 'Error cancelling appointment', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error cancelling appointment', 'error');
            });
        }
    }

    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;

        // Add to page
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
</script>
@endpush
