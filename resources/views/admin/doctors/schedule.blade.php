@extends('layouts.app')

@section('title', 'Doctor Schedule - ' . $doctor->doctor_name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Doctor Schedule</h1>
                    <p class="text-gray-600 dark:text-gray-400">Viewing schedule for {{ $doctor->doctor_name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Doctors
                    </a>
                    <a href="{{ route('admin.doctors.show', $doctor->id) }}" class="btn btn-outline">
                        <i class="fas fa-user-md mr-2"></i>
                        View Doctor Details
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Doctor Information Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-user-md text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $doctor->doctor_name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">{{ $doctor->specialty_name }}</p>
                    <div class="flex items-center mt-2">
                        <span class="px-3 py-1 text-sm rounded-full {{ $doctor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($doctor->status) }}
                        </span>
                        <span class="ml-2 text-gray-600 dark:text-gray-400">
                            <i class="fas fa-star text-gold mr-1"></i>
                            {{ number_format($doctor->rating, 1) }} Rating
                        </span>
                        <span class="ml-4 text-gray-600 dark:text-gray-400">
                            <i class="fas fa-briefcase text-blue-500 mr-1"></i>
                            {{ $doctor->experience_years }} Years Experience
                        </span>
                        <span class="ml-4 text-gray-600 dark:text-gray-400">
                            <i class="fas fa-dollar-sign text-green-500 mr-1"></i>
                            ${{ number_format($doctor->consultation_fee, 2) }} Fee
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Total Appointments</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_appointments'] }}</p>
                    </div>
                    <i class="fas fa-calendar text-blue-500 text-2xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Today's Appointments</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['today_appointments'] }}</p>
                    </div>
                    <i class="fas fa-clock text-green-500 text-2xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Upcoming</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['upcoming_appointments'] }}</p>
                    </div>
                    <i class="fas fa-calendar-plus text-purple-500 text-2xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Completed</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['completed_appointments'] }}</p>
                    </div>
                    <i class="fas fa-check-circle text-yellow-500 text-2xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Cancelled</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['cancelled_appointments'] }}</p>
                    </div>
                    <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Appointments Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button onclick="showTab('today')" id="tab-today" class="tab-button active py-4 px-1 border-b-2 border-blue-500 text-blue-600 font-medium">
                        <i class="fas fa-clock mr-2"></i>
                        Today's Schedule
                    </button>
                    <button onclick="showTab('upcoming')" id="tab-upcoming" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Upcoming (7 days)
                    </button>
                    <button onclick="showTab('past')" id="tab-past" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                        <i class="fas fa-history mr-2"></i>
                        Past (7 days)
                    </button>
                </nav>
            </div>

            <!-- Today's Appointments -->
            <div id="tab-content-today" class="tab-content active p-6">
                @if($todayAppointments->count() > 0)
                    <div class="space-y-4">
                        @foreach($todayAppointments as $appointment)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-gray-900 dark:text-white font-semibold">{{ $appointment->patient_name }}</h3>
                                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $appointment->patient_email }}</p>
                                            <p class="text-gray-500 dark:text-gray-500 text-sm">{{ $appointment->patient_phone }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-gray-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</p>
                                        <span class="px-3 py-1 text-sm rounded-full
                                            @if($appointment->STATUS === 'completed') bg-green-100 text-green-800
                                            @elseif($appointment->STATUS === 'confirmed') bg-blue-100 text-blue-800
                                            @elseif($appointment->STATUS === 'scheduled') bg-yellow-100 text-yellow-800
                                            @elseif($appointment->STATUS === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($appointment->STATUS) }}
                                        </span>
                                        @if($appointment->payment_status)
                                            <div class="mt-1">
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    @if($appointment->payment_status === 'paid') bg-green-100 text-green-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($appointment->payment_status) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if($appointment->consultation_fee)
                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                                            <i class="fas fa-dollar-sign text-green-500 mr-1"></i>
                                            Fee: ${{ number_format($appointment->consultation_fee, 2) }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-gray-900 dark:text-white text-lg font-semibold mb-2">No Appointments Today</h3>
                        <p class="text-gray-600 dark:text-gray-400">This doctor has no appointments scheduled for today.</p>
                    </div>
                @endif
            </div>

            <!-- Upcoming Appointments -->
            <div id="tab-content-upcoming" class="tab-content hidden p-6">
                @if($upcomingAppointments->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingAppointments as $appointment)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-gray-900 dark:text-white font-semibold">{{ $appointment->patient_name }}</h3>
                                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $appointment->patient_email }}</p>
                                            <p class="text-gray-500 dark:text-gray-500 text-sm">{{ $appointment->patient_phone }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-gray-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d') }}</p>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</p>
                                        <span class="px-3 py-1 text-sm rounded-full
                                            @if($appointment->STATUS === 'completed') bg-green-100 text-green-800
                                            @elseif($appointment->STATUS === 'confirmed') bg-blue-100 text-blue-800
                                            @elseif($appointment->STATUS === 'scheduled') bg-yellow-100 text-yellow-800
                                            @elseif($appointment->STATUS === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($appointment->STATUS) }}
                                        </span>
                                    </div>
                                </div>
                                @if($appointment->consultation_fee)
                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                                            <i class="fas fa-dollar-sign text-green-500 mr-1"></i>
                                            Fee: ${{ number_format($appointment->consultation_fee, 2) }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-calendar-plus text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-gray-900 dark:text-white text-lg font-semibold mb-2">No Upcoming Appointments</h3>
                        <p class="text-gray-600 dark:text-gray-400">This doctor has no appointments scheduled for the next 7 days.</p>
                    </div>
                @endif
            </div>

            <!-- Past Appointments -->
            <div id="tab-content-past" class="tab-content hidden p-6">
                @if($pastAppointments->count() > 0)
                    <div class="space-y-4">
                        @foreach($pastAppointments as $appointment)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gray-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-gray-900 dark:text-white font-semibold">{{ $appointment->patient_name }}</h3>
                                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $appointment->patient_email }}</p>
                                            <p class="text-gray-500 dark:text-gray-500 text-sm">{{ $appointment->patient_phone }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-gray-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d') }}</p>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</p>
                                        <span class="px-3 py-1 text-sm rounded-full
                                            @if($appointment->STATUS === 'completed') bg-green-100 text-green-800
                                            @elseif($appointment->STATUS === 'confirmed') bg-blue-100 text-blue-800
                                            @elseif($appointment->STATUS === 'scheduled') bg-yellow-100 text-yellow-800
                                            @elseif($appointment->STATUS === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($appointment->STATUS) }}
                                        </span>
                                    </div>
                                </div>
                                @if($appointment->consultation_fee)
                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                                            <i class="fas fa-dollar-sign text-green-500 mr-1"></i>
                                            Fee: ${{ number_format($appointment->consultation_fee, 2) }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-gray-900 dark:text-white text-lg font-semibold mb-2">No Past Appointments</h3>
                        <p class="text-gray-600 dark:text-gray-400">This doctor has no appointments in the last 7 days.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.tab-button.active {
    @apply border-blue-500 text-blue-600;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}
</style>

<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
        content.classList.add('hidden');
    });

    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById(`tab-content-${tabName}`).classList.remove('hidden');
    document.getElementById(`tab-content-${tabName}`).classList.add('active');

    // Add active class to selected tab button
    document.getElementById(`tab-${tabName}`).classList.add('active', 'border-blue-500', 'text-blue-600');
    document.getElementById(`tab-${tabName}`).classList.remove('border-transparent', 'text-gray-500');
}
</script>
@endsection
