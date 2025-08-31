@extends('layouts.app')

@section('title', "Today's Schedule - Doctor Dashboard")

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Today's Schedule</h1>
                    <p class="text-muted mt-2">{{ now()->format('l, F j, Y') }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm text-green-600 font-medium">Active</span>
                    </div>
                    <button class="btn btn-outline" onclick="window.location.href='{{ route('doctor.appointments.index') }}'">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Today's Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card feature-card" data-aos="fade-up">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-day text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['total_today'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Total Today</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['completed'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Completed</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['pending'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Pending</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-times-circle text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['cancelled'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Cancelled</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Slots -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Morning Schedule -->
            <div class="card feature-card" data-aos="fade-up">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-sun text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-text">Morning</h3>
                    </div>

                    <div class="space-y-4">
                        @forelse($morningAppointments as $appointment)
                            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-text">{{ $appointment->patient_name }}</p>
                                    <p class="text-sm text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
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
                                    <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-900 dark:text-{{ $statusColor }}-200">
                                        {{ ucfirst(str_replace('_', ' ', $appointment->STATUS)) }}
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <button class="btn btn-sm btn-outline" onclick="viewAppointment({{ $appointment->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($appointment->STATUS === 'scheduled')
                                        <button class="btn btn-sm btn-primary" onclick="updateStatus({{ $appointment->id }}, 'confirmed')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-muted">
                                <i class="fas fa-calendar-times text-3xl mb-2"></i>
                                <p>No morning appointments</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Afternoon Schedule -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-cloud-sun text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-text">Afternoon</h3>
                    </div>

                    <div class="space-y-4">
                        @forelse($afternoonAppointments as $appointment)
                            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-text">{{ $appointment->patient_name }}</p>
                                    <p class="text-sm text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
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
                                    <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-900 dark:text-{{ $statusColor }}-200">
                                        {{ ucfirst(str_replace('_', ' ', $appointment->STATUS)) }}
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <button class="btn btn-sm btn-outline" onclick="viewAppointment({{ $appointment->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($appointment->STATUS === 'scheduled')
                                        <button class="btn btn-sm btn-primary" onclick="updateStatus({{ $appointment->id }}, 'confirmed')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-muted">
                                <i class="fas fa-calendar-times text-3xl mb-2"></i>
                                <p>No afternoon appointments</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Evening Schedule -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-moon text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-text">Evening</h3>
                    </div>

                    <div class="space-y-4">
                        @forelse($eveningAppointments as $appointment)
                            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-text">{{ $appointment->patient_name }}</p>
                                    <p class="text-sm text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
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
                                    <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-900 dark:text-{{ $statusColor }}-200">
                                        {{ ucfirst(str_replace('_', ' ', $appointment->STATUS)) }}
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <button class="btn btn-sm btn-outline" onclick="viewAppointment({{ $appointment->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($appointment->STATUS === 'scheduled')
                                        <button class="btn btn-sm btn-primary" onclick="updateStatus({{ $appointment->id }}, 'confirmed')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-muted">
                                <i class="fas fa-calendar-times text-3xl mb-2"></i>
                                <p>No evening appointments</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 flex flex-wrap gap-4">
            <button class="btn btn-outline" onclick="window.location.href='{{ route('doctor.appointments.upcoming') }}'">
                <i class="fas fa-calendar-alt mr-2"></i>
                View Upcoming
            </button>
            <button class="btn btn-outline" onclick="window.location.href='{{ route('doctor.appointments.calendar') }}'">
                <i class="fas fa-calendar mr-2"></i>
                Calendar View
            </button>
            <button class="btn btn-outline" onclick="exportTodaySchedule()">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </button>
        </div>
    </div>
</div>

<!-- Appointment Details Modal -->
<div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-text">Appointment Details</h3>
                    <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" onclick="closeModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="appointmentDetails">
                    <!-- Appointment details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // View appointment details
    function viewAppointment(appointmentId) {
        fetch(`{{ route('doctor.appointments.index') }}/${appointmentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('appointmentDetails').innerHTML = data.html;
                    document.getElementById('appointmentModal').classList.remove('hidden');
                } else {
                    showNotification('Error loading appointment details', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error loading appointment details', 'error');
            });
    }

    // Update appointment status
    function updateStatus(appointmentId, status) {
        if (!confirm(`Are you sure you want to mark this appointment as ${status}?`)) {
            return;
        }

        fetch(`{{ route('doctor.appointments.index') }}/${appointmentId}/status`, {
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
                showNotification('Appointment status updated successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification('Error updating appointment status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating appointment status', 'error');
        });
    }

    // Close modal
    function closeModal() {
        document.getElementById('appointmentModal').classList.add('hidden');
    }

    // Export today's schedule
    function exportTodaySchedule() {
        window.location.href = '{{ route("doctor.appointments.export") }}?type=today';
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Simple notification - you can enhance this with a proper notification system
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(`${type.toUpperCase()}: ${message}`);
    }
</script>
@endpush
