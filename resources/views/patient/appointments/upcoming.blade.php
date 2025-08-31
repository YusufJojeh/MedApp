@extends('layouts.app')

@section('title', 'Upcoming Appointments - Patient Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Upcoming Appointments</h1>
                    <p class="text-muted mt-2">Your scheduled healthcare appointments</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="btn btn-primary" onclick="window.location.href='{{ route('patient.appointments.create') }}'">
                        <i class="fas fa-plus mr-2"></i>
                        Book New Appointment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['this_week'] ?? 0 }}</p>
                            <p class="text-sm text-muted">This Week</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-md text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['total_doctors'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Doctors Scheduled</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-4 mb-8">
            <button class="btn btn-outline" onclick="window.location.href='{{ route('patient.appointments.index') }}'">
                <i class="fas fa-list mr-2"></i>
                All Appointments
            </button>
            <button class="btn btn-outline" onclick="window.location.href='{{ route('patient.appointments.past') }}'">
                <i class="fas fa-history mr-2"></i>
                Past Appointments
            </button>
            <button class="btn btn-outline" onclick="exportUpcoming()">
                <i class="fas fa-download mr-2"></i>
                Export
            </button>
        </div>

        <!-- Filters -->
        <div class="card feature-card mb-8" data-aos="fade-up">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Date Range</label>
                        <select id="dateRangeFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Dates</option>
                            <option value="today">Today</option>
                            <option value="tomorrow">Tomorrow</option>
                            <option value="this_week">This Week</option>
                            <option value="next_week">Next Week</option>
                            <option value="this_month">This Month</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Doctor</label>
                        <input type="text" id="doctorFilter" placeholder="Search doctor..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Specialty</label>
                        <select id="specialtyFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Specialties</option>
                            @foreach($specialties as $specialty)
                                <option value="{{ $specialty->id }}">{{ $specialty->name_en }}</option>
                            @endforeach
                        </select>
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

        <!-- Upcoming Appointments -->
        <div class="space-y-6">
            @forelse($appointments as $appointment)
                <div class="card feature-card" data-aos="fade-up">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-md text-white text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4 mb-2">
                                        <h3 class="text-xl font-bold text-text">Dr. {{ $appointment->doctor->name }}</h3>
                                        <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $appointment->specialty_name }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-muted">Date & Time</p>
                                            <p class="font-medium text-text">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F j, Y') }}
                                                <br>
                                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-muted">Appointment Type</p>
                                            <p class="font-medium text-text">{{ ucfirst($appointment->appointment_type) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-muted">Consultation Fee</p>
                                            <p class="font-medium text-text">${{ number_format($appointment->fees, 2) }}</p>
                                        </div>
                                    </div>
                                    @if($appointment->symptoms)
                                        <div class="mb-4">
                                            <p class="text-sm text-muted">Symptoms/Notes</p>
                                            <p class="text-text">{{ $appointment->symptoms }}</p>
                                        </div>
                                    @endif
                                    <div class="flex items-center space-x-4">
                                        <span class="px-3 py-1 text-xs rounded-full {{ $appointment->status_badge_class }}">
                                            {{ $appointment->status_text }}
                                        </span>
                                        <span class="text-sm text-muted">
                                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col space-y-2">
                                <button class="btn btn-sm btn-outline" onclick="viewAppointment({{ $appointment->id }})">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </button>
                                @if($appointment->status === 'scheduled')
                                    <button class="btn btn-sm btn-danger" onclick="cancelAppointment({{ $appointment->id }})">
                                        <i class="fas fa-times mr-1"></i>
                                        Cancel
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-primary" onclick="rescheduleAppointment({{ $appointment->id }})">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Reschedule
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card feature-card">
                    <div class="p-12 text-center">
                        <i class="fas fa-calendar-times text-6xl text-muted mb-4"></i>
                        <h3 class="text-xl font-bold text-text mb-2">No Upcoming Appointments</h3>
                        <p class="text-muted mb-6">You don't have any upcoming appointments scheduled</p>
                        <button class="btn btn-primary" onclick="window.location.href='{{ route('patient.appointments.create') }}'">
                            <i class="fas fa-plus mr-2"></i>
                            Book Your First Appointment
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($appointments->hasPages())
            <div class="mt-8">
                {{ $appointments->links() }}
            </div>
        @endif
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
        const doctor = document.getElementById('doctorFilter').value;
        const specialty = document.getElementById('specialtyFilter').value;

        let url = '{{ route("patient.appointments.upcoming") }}?';
        if (dateRange) url += `date_range=${dateRange}&`;
        if (doctor) url += `doctor=${encodeURIComponent(doctor)}&`;
        if (specialty) url += `specialty=${specialty}&`;

        window.location.href = url;
    }

    // View appointment details
    function viewAppointment(appointmentId) {
        fetch(`{{ route('patient.appointments.index') }}/${appointmentId}`)
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

    // Cancel appointment
    function cancelAppointment(appointmentId) {
        if (!confirm('Are you sure you want to cancel this appointment?')) {
            return;
        }

        fetch(`{{ route('patient.appointments.index') }}/${appointmentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Appointment cancelled successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification('Error cancelling appointment', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error cancelling appointment', 'error');
        });
    }

    // Reschedule appointment
    function rescheduleAppointment(appointmentId) {
        window.location.href = `{{ route('patient.appointments.create') }}?reschedule=${appointmentId}`;
    }

    // Close modal
    function closeModal() {
        document.getElementById('appointmentModal').classList.add('hidden');
    }

    // Export upcoming appointments
    function exportUpcoming() {
        const dateRange = document.getElementById('dateRangeFilter').value;
        const doctor = document.getElementById('doctorFilter').value;
        const specialty = document.getElementById('specialtyFilter').value;

        let url = '{{ route("patient.appointments.export") }}?type=upcoming';
        if (dateRange) url += `&date_range=${dateRange}`;
        if (doctor) url += `&doctor=${encodeURIComponent(doctor)}`;
        if (specialty) url += `&specialty=${specialty}`;

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
        if (urlParams.get('doctor')) {
            document.getElementById('doctorFilter').value = urlParams.get('doctor');
        }
        if (urlParams.get('specialty')) {
            document.getElementById('specialtyFilter').value = urlParams.get('specialty');
        }
    });
</script>
@endpush
