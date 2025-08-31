@extends('layouts.app')

@section('title', 'Upcoming Appointments - Doctor Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Upcoming Appointments</h1>
                    <p class="text-muted mt-2">Manage your future appointments and schedule</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-muted">Next 30 days</span>
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
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card feature-card" data-aos="fade-up">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['total_upcoming'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Total Upcoming</p>
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
                            <p class="text-2xl font-bold text-text">{{ $stats['confirmed'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Confirmed</p>
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
                            <p class="text-2xl font-bold text-text">{{ $stats['scheduled'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Scheduled</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['unique_patients'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Unique Patients</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card feature-card mb-8" data-aos="fade-up">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Date Range</label>
                        <select id="dateRangeFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="7">Next 7 days</option>
                            <option value="14">Next 14 days</option>
                            <option value="30" selected>Next 30 days</option>
                            <option value="60">Next 60 days</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Status</label>
                        <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Status</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="confirmed">Confirmed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Patient</label>
                        <input type="text" id="patientFilter" placeholder="Search patient..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                    </div>
                    <div class="flex items-end">
                        <button class="btn btn-primary w-full" onclick="filterAppointments()">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-4 mb-8">
            <button class="btn btn-outline" onclick="window.location.href='{{ route('doctor.appointments.today') }}'">
                <i class="fas fa-calendar-day mr-2"></i>
                Today's Schedule
            </button>
            <button class="btn btn-outline" onclick="window.location.href='{{ route('doctor.appointments.calendar') }}'">
                <i class="fas fa-calendar mr-2"></i>
                Calendar View
            </button>
            <button class="btn btn-outline" onclick="window.location.href='{{ route('doctor.appointments.past') }}'">
                <i class="fas fa-history mr-2"></i>
                Past Appointments
            </button>
            <button class="btn btn-outline" onclick="exportUpcoming()">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </button>
        </div>

        <!-- Appointments by Week -->
        <div class="space-y-8">
            @forelse($appointmentsByWeek as $week => $appointments)
                <div class="card feature-card" data-aos="fade-up">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-text">{{ $week }}</h3>
                            <span class="text-sm text-muted">{{ count($appointments) }} appointments</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($appointments as $appointment)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                                                <p class="font-medium text-text">{{ $appointment->patient_name }}</p>
                                <p class="text-sm text-muted">{{ $appointment->patient_phone }}</p>
                                            </div>
                                        </div>
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
                                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-900 dark:text-{{ $statusColor }}-200">
                                            {{ ucfirst(str_replace('_', ' ', $appointment->STATUS)) }}
                                        </span>
                                    </div>

                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-calendar text-muted mr-2"></i>
                                            <span class="text-text">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, M j, Y') }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-clock text-muted mr-2"></i>
                                            <span class="text-text">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-stethoscope text-muted mr-2"></i>
                                            <span class="text-text">{{ explode(' - ', $appointment->notes)[0] ?? 'General Consultation' }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-dollar-sign text-muted mr-2"></i>
                                            <span class="text-text">${{ number_format(200.00, 2) }}</span>
                                        </div>
                                    </div>

                                    <div class="flex space-x-2">
                                        <button class="btn btn-sm btn-outline flex-1" onclick="viewAppointment({{ $appointment->id }})">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </button>
                                        @if($appointment->STATUS === 'scheduled')
                                            <button class="btn btn-sm btn-primary flex-1" onclick="updateStatus({{ $appointment->id }}, 'confirmed')">
                                                <i class="fas fa-check mr-1"></i>
                                                Confirm
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="card feature-card" data-aos="fade-up">
                    <div class="p-12 text-center">
                        <i class="fas fa-calendar-times text-6xl text-muted mb-4"></i>
                        <h3 class="text-xl font-bold text-text mb-2">No Upcoming Appointments</h3>
                        <p class="text-muted mb-6">You don't have any upcoming appointments in the selected date range.</p>
                        <button class="btn btn-primary" onclick="window.location.href='{{ route('doctor.appointments.index') }}'">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            View All Appointments
                        </button>
                    </div>
                </div>
            @endforelse
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
    // Filter appointments
    function filterAppointments() {
        const dateRange = document.getElementById('dateRangeFilter').value;
        const status = document.getElementById('statusFilter').value;
        const patient = document.getElementById('patientFilter').value;

        let url = '{{ route("doctor.appointments.upcoming") }}?';
        if (dateRange) url += `date_range=${dateRange}&`;
        if (status) url += `status=${status}&`;
        if (patient) url += `patient=${patient}&`;

        window.location.href = url;
    }

    // View appointment details
    function viewAppointment(appointmentId) {
        fetch(`/doctor/appointments/${appointmentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('appointmentDetails').innerHTML = html;
                document.getElementById('appointmentModal').classList.remove('hidden');
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

    // Export upcoming appointments
    function exportUpcoming() {
        const dateRange = document.getElementById('dateRangeFilter').value;
        const status = document.getElementById('statusFilter').value;
        const patient = document.getElementById('patientFilter').value;

        let url = '{{ route("doctor.appointments.export") }}?type=upcoming';
        if (dateRange) url += `&date_range=${dateRange}`;
        if (status) url += `&status=${status}`;
        if (patient) url += `&patient=${patient}`;

        window.location.href = url;
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Simple notification - you can enhance this with a proper notification system
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(`${type.toUpperCase()}: ${message}`);
    }

    // Initialize filters on page load
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get('date_range')) {
            document.getElementById('dateRangeFilter').value = urlParams.get('date_range');
        }
        if (urlParams.get('status')) {
            document.getElementById('statusFilter').value = urlParams.get('status');
        }
        if (urlParams.get('patient')) {
            document.getElementById('patientFilter').value = urlParams.get('patient');
        }
    });
</script>
@endpush
