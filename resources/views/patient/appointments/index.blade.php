@extends('layouts.app')

@section('title', 'My Appointments - Patient Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Appointments</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Manage and track your medical appointments</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Book New Appointment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select id="statusFilter" class="form-select w-full" onchange="filterAppointments()">
                        <option value="">All Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                    <select id="dateFilter" class="form-select w-full" onchange="filterAppointments()">
                        <option value="">All Dates</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>

                <!-- Doctor Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Doctor</label>
                    <select id="doctorFilter" class="form-select w-full" onchange="filterAppointments()">
                        <option value="">All Doctors</option>
                        @foreach($doctors as $id => $name)
                            <option value="{{ $name }}" {{ request('doctor') == $name ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Clear Filters -->
                <div class="flex items-end">
                    <button onclick="clearFilters()" class="btn btn-outline w-full">
                        <i class="fas fa-times mr-2"></i>
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Appointments List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            @if($appointments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Appointment
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Doctor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date & Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Fee
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($appointments as $appointment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                    <i class="fas fa-calendar text-blue-600 dark:text-blue-400"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    #{{ $appointment->id }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $appointment->specialty_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $appointment->doctor_name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $appointment->specialty_name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($appointment->STATUS == 'scheduled') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($appointment->STATUS == 'confirmed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($appointment->STATUS == 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($appointment->STATUS == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                            @endif">
                                            {{ ucfirst($appointment->STATUS) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        ${{ number_format($appointment->consultation_fee, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="viewAppointment({{ $appointment->id }})"
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($appointment->STATUS == 'scheduled')
                                                <a href="{{ route('patient.appointments.create', ['reschedule' => $appointment->id]) }}"
                                                   class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="cancelAppointment({{ $appointment->id }})"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($appointments->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $appointments->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <i class="fas fa-calendar-times text-4xl"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No appointments found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Get started by booking your first appointment.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>
                            Book Appointment
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Appointment Details Modal -->
<div id="appointmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Appointment Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="appointmentDetails">
                <!-- Appointment details will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function filterAppointments() {
        const status = document.getElementById('statusFilter').value;
        const dateRange = document.getElementById('dateFilter').value;
        const doctor = document.getElementById('doctorFilter').value;

        let url = '{{ route("patient.appointments.index") }}?';
        if (status) url += `status=${status}&`;
        if (dateRange) url += `date_range=${dateRange}&`;
        if (doctor) url += `doctor=${doctor}&`;

        window.location.href = url;
    }

    function clearFilters() {
        window.location.href = '{{ route("patient.appointments.index") }}';
    }

    function viewAppointment(appointmentId) {
        fetch(`{{ route('patient.appointments.index') }}/${appointmentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.appointment) {
                    const appointment = data.appointment;
                    const detailsHtml = `
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">Appointment #${appointment.id}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">${appointment.specialty_name}</p>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Doctor</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">${appointment.doctor_name}</p>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Date & Time</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    ${new Date(appointment.appointment_date).toLocaleDateString()} at
                                    ${new Date('2000-01-01T' + appointment.appointment_time).toLocaleTimeString()}
                                </p>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Status</h5>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    ${appointment.STATUS === 'scheduled' ? 'bg-yellow-100 text-yellow-800' :
                                      appointment.STATUS === 'confirmed' ? 'bg-blue-100 text-blue-800' :
                                      appointment.STATUS === 'completed' ? 'bg-green-100 text-green-800' :
                                      appointment.STATUS === 'cancelled' ? 'bg-red-100 text-red-800' :
                                      'bg-gray-100 text-gray-800'}">
                                    ${appointment.STATUS.charAt(0).toUpperCase() + appointment.STATUS.slice(1)}
                                </span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Consultation Fee</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">$${parseFloat(appointment.consultation_fee).toFixed(2)}</p>
                            </div>
                            ${appointment.notes ? `
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Notes</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">${appointment.notes}</p>
                            </div>
                            ` : ''}
                        </div>
                    `;
                    document.getElementById('appointmentDetails').innerHTML = detailsHtml;
                    document.getElementById('appointmentModal').classList.remove('hidden');
                } else {
                    alert('Error loading appointment details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading appointment details');
            });
    }

    function closeModal() {
        document.getElementById('appointmentModal').classList.add('hidden');
    }

    function cancelAppointment(appointmentId) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            fetch(`{{ route('patient.appointments.index') }}/${appointmentId}/cancel`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error cancelling appointment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error cancelling appointment');
            });
        }
    }

    // Close modal when clicking outside
    document.getElementById('appointmentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endpush
