@extends('layouts.app')

@section('title', 'Appointments - Doctor Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Appointments</h1>
                    <p class="text-muted mt-2">Manage your patient appointments and schedule</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-muted">Today:</span>
                        <span class="text-sm font-medium text-text">{{ now()->format('l, F j, Y') }}</span>
                    </div>
                    <button class="btn btn-primary" onclick="window.location.href='{{ route('doctor.appointments.today') }}'">
                        <i class="fas fa-calendar-day mr-2"></i>
                        Today's Schedule
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card feature-card" data-aos="fade-up">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-check text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['total_appointments'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Total Appointments</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['today_appointments'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Today's Appointments</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['upcoming_appointments'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Upcoming</p>
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
                            <p class="text-2xl font-bold text-text">{{ $stats['total_patients'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Total Patients</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card feature-card mb-8" data-aos="fade-up">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Status</label>
                        <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Status</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="no-show">No Show</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Date Range</label>
                        <select id="dateFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Dates</option>
                            <option value="today">Today</option>
                            <option value="tomorrow">Tomorrow</option>
                            <option value="this_week">This Week</option>
                            <option value="next_week">Next Week</option>
                            <option value="this_month">This Month</option>
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
            <button class="btn btn-outline" onclick="window.location.href='{{ route('doctor.appointments.upcoming') }}'">
                <i class="fas fa-calendar-alt mr-2"></i>
                Upcoming
            </button>
            <button class="btn btn-outline" onclick="window.location.href='{{ route('doctor.appointments.past') }}'">
                <i class="fas fa-history mr-2"></i>
                Past Appointments
            </button>
            <button class="btn btn-outline" onclick="window.location.href='{{ route('doctor.appointments.calendar') }}'">
                <i class="fas fa-calendar mr-2"></i>
                Calendar View
            </button>
            <button class="btn btn-outline" onclick="exportAppointments()">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </button>
        </div>

        <!-- Appointments Table -->
        <div class="card feature-card" data-aos="fade-up">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-text">All Appointments</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-muted">Showing {{ $appointments->count() }} appointments</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-medium text-text">Patient</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Date & Time</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Type</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Status</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Fees</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appointment)
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-text">{{ $appointment->patient_name }}</p>
                                                <p class="text-sm text-muted">{{ $appointment->patient_phone }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div>
                                            <p class="font-medium text-text">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M j, Y') }}</p>
                                            <p class="text-sm text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ ucfirst(explode(' - ', $appointment->notes)[0] ?? 'Consultation') }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
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
                                    </td>
                                    <td class="py-4 px-4">
                                        <p class="font-medium text-text">${{ number_format(200.00, 2) }}</p>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-2">
                                            <button class="btn btn-sm btn-outline" onclick="viewAppointment({{ $appointment->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($appointment->STATUS === 'scheduled')
                                                <button class="btn btn-sm btn-primary" onclick="updateStatus({{ $appointment->id }}, 'confirmed')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="updateStatus({{ $appointment->id }}, 'cancelled')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-muted">
                                        <i class="fas fa-calendar-times text-4xl mb-4"></i>
                                        <p>No appointments found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($appointments->hasPages())
                    <div class="mt-6">
                        {{ $appointments->links() }}
                    </div>
                @endif
            </div>
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
        const status = document.getElementById('statusFilter').value;
        const dateRange = document.getElementById('dateFilter').value;
        const patient = document.getElementById('patientFilter').value;

        let url = '{{ route("doctor.appointments.index") }}?';
        if (status) url += `status=${status}&`;
        if (dateRange) url += `date_range=${dateRange}&`;
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

    // Export appointments
    function exportAppointments() {
        const status = document.getElementById('statusFilter').value;
        const dateRange = document.getElementById('dateFilter').value;
        const patient = document.getElementById('patientFilter').value;

        let url = '{{ route("doctor.appointments.export") }}?';
        if (status) url += `status=${status}&`;
        if (dateRange) url += `date_range=${dateRange}&`;
        if (patient) url += `patient=${patient}&`;

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

        if (urlParams.get('status')) {
            document.getElementById('statusFilter').value = urlParams.get('status');
        }
        if (urlParams.get('date_range')) {
            document.getElementById('dateFilter').value = urlParams.get('date_range');
        }
        if (urlParams.get('patient')) {
            document.getElementById('patientFilter').value = urlParams.get('patient');
        }
    });
</script>
@endpush
