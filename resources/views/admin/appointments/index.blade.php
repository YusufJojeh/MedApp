@extends('layouts.app')

@section('title', 'Appointment Management - Admin Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Appointment Management</h1>
                    <p class="text-muted mt-2">View all medical appointments (Read-only)</p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Removed Add Appointment button - admin can only view -->
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
                            <i class="fas fa-calendar-check text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['total_appointments']) }}</p>
                            <p class="text-sm text-muted">Total Appointments</p>
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
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['completed_appointments']) }}</p>
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
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['pending_appointments']) }}</p>
                            <p class="text-sm text-muted">Pending</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-dollar-sign text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">${{ number_format($stats['total_revenue'], 2) }}</p>
                            <p class="text-sm text-muted">Total Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-4 mb-8">
            <button class="btn btn-outline" onclick="exportAppointments()">
                <i class="fas fa-download mr-2"></i>
                Export Appointments
            </button>
            <button class="btn btn-outline" onclick="viewCalendar()">
                <i class="fas fa-calendar-alt mr-2"></i>
                Calendar View
            </button>
        </div>

        <!-- Filters -->
        <div class="card feature-card mb-8" data-aos="fade-up">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Search</label>
                        <input type="text" id="searchInput" placeholder="Search appointments..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Doctor</label>
                        <select id="doctorFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Doctors</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                            @endforeach
                        </select>
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
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Status</label>
                        <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Date Range</label>
                        <select id="dateFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button class="btn btn-primary flex-1" onclick="filterAppointments()">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                        <button class="btn btn-outline flex-1" onclick="clearFilters()">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </button>
                    </div>
                </div>
            </div>
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
                                <th class="text-left py-3 px-4 font-medium text-text">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-gold focus:ring-gold">
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-text">Patient</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Doctor</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Date & Time</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Status</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Payment</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appointment)
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-4 px-4">
                                        <input type="checkbox" class="appointment-checkbox rounded border-gray-300 text-gold focus:ring-gold" value="{{ $appointment->id }}">
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
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
                                            <p class="font-medium text-text">{{ $appointment->doctor_name }}</p>
                                            <p class="text-sm text-muted">{{ $appointment->specialty_name }}</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div>
                                            <p class="text-sm text-text">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M j, Y') }}</p>
                                            <p class="text-xs text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-3 py-1 text-xs rounded-full {{ $appointment->status_badge_class }}">
                                            {{ ucfirst($appointment->STATUS) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div>
                                            <p class="text-sm text-text">${{ number_format($appointment->consultation_fee ?? 0, 2) }}</p>
                                            <p class="text-xs text-muted">{{ $appointment->payment_status ?? 'Pending' }}</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline">
                                                <i class="fas fa-eye"></i>
                                                View Details
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-muted">
                                        <i class="fas fa-calendar-times text-4xl mb-4"></i>
                                        <p>No appointments found</p>
                                        <!-- Admin can only view appointments -->
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




@endsection

@push('scripts')
<script>


    // Filter appointments
    function filterAppointments() {
        const search = document.getElementById('searchInput').value;
        const doctor = document.getElementById('doctorFilter').value;
        const specialty = document.getElementById('specialtyFilter').value;
        const status = document.getElementById('statusFilter').value;
        const dateRange = document.getElementById('dateFilter').value;

        let url = '{{ route("admin.appointments.index") }}?';
        if (search) url += `search=${encodeURIComponent(search)}&`;
        if (doctor) url += `doctor=${doctor}&`;
        if (specialty) url += `specialty=${specialty}&`;
        if (status) url += `status=${status}&`;
        if (dateRange) url += `date_range=${dateRange}&`;

        window.location.href = url;
    }

    // Clear filters
    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('doctorFilter').value = '';
        document.getElementById('specialtyFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('dateFilter').value = '';
        window.location.href = '{{ route("admin.appointments.index") }}';
    }









    // Export appointments
    function exportAppointments() {
        const search = document.getElementById('searchInput').value;
        const doctor = document.getElementById('doctorFilter').value;
        const specialty = document.getElementById('specialtyFilter').value;
        const status = document.getElementById('statusFilter').value;
        const dateRange = document.getElementById('dateFilter').value;

        let url = '{{ route("admin.appointments.export") }}?';
        if (search) url += `search=${encodeURIComponent(search)}&`;
        if (doctor) url += `doctor=${doctor}&`;
        if (specialty) url += `specialty=${specialty}&`;
        if (status) url += `status=${status}&`;
        if (dateRange) url += `date_range=${dateRange}&`;

        window.location.href = url;
    }



    // View calendar
    function viewCalendar() {
        showNotification('Calendar view coming soon', 'info');
    }

    // Select all appointments
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.appointment-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Show notification
    function showNotification(message, type = 'info') {
        // Simple notification - you can enhance this with a proper notification system
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(`${type.toUpperCase()}: ${message}`);
    }

    // Initialize filters on page load
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get('search')) {
            document.getElementById('searchInput').value = urlParams.get('search');
        }
        if (urlParams.get('doctor')) {
            document.getElementById('doctorFilter').value = urlParams.get('doctor');
        }
        if (urlParams.get('specialty')) {
            document.getElementById('specialtyFilter').value = urlParams.get('specialty');
        }
        if (urlParams.get('status')) {
            document.getElementById('statusFilter').value = urlParams.get('status');
        }
        if (urlParams.get('date_range')) {
            document.getElementById('dateFilter').value = urlParams.get('date_range');
        }
    });
</script>
@endpush
