@extends('layouts.app')

@section('title', 'Doctor Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Doctor Dashboard</h1>
                    <p class="text-muted mt-2">Manage your appointments and patients</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-muted">Welcome back,</p>
                        <p class="font-semibold text-text">Dr. {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-md text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Today's Appointments -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white mr-4">
                            <i class="fas fa-calendar-day text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Today's Appointments</p>
                            <p class="text-2xl font-bold text-text">{{ $todayAppointments }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Patients -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white mr-4">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Total Patients</p>
                            <p class="text-2xl font-bold text-text">{{ $totalPatients }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Today -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white mr-4">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Completed Today</p>
                            <p class="text-2xl font-bold text-text">
                                {{ $appointmentStatuses->where('STATUS', 'completed')->first()->count ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="400">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-deep rounded-xl flex items-center justify-center text-white mr-4">
                            <i class="fas fa-star text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Average Rating</p>
                            <p class="text-2xl font-bold text-text">{{ number_format($doctor->rating ?? 0, 1) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Today's Appointments -->
            <div class="lg:col-span-3">
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-text">Today's Schedule</h3>
                            <div class="flex space-x-2">
                                <button class="btn btn-outline text-sm" onclick="previousDay()">Previous</button>
                                <button class="btn btn-primary text-sm" onclick="nextDay()">Next</button>
                            </div>
                        </div>
                        <div class="space-y-4" id="appointmentsList">
                            @if($todayAppointmentsList->count() > 0)
                                @foreach($todayAppointmentsList as $appointment)
                                    @php
                                        $statusColor = match($appointment->STATUS) {
                                            'confirmed' => 'green',
                                            'completed' => 'blue',
                                            'scheduled' => 'yellow',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <div class="flex items-center p-4 bg-{{ $statusColor }}-50 dark:bg-{{ $statusColor }}-900/20 rounded-lg border-l-4 border-{{ $statusColor }}-500">
                                        <div class="w-12 h-12 bg-{{ $statusColor }}-100 dark:bg-{{ $statusColor }}-900 rounded-full flex items-center justify-center mr-4">
                                            <i class="fas fa-clock text-{{ $statusColor }}-600 dark:text-{{ $statusColor }}-400"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="font-medium text-text">{{ $appointment->patient_name }}</p>
                                                    <p class="text-sm text-muted">{{ explode(' - ', $appointment->notes)[0] ?? 'General Consultation' }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-medium text-text">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                                                    <span class="px-2 py-1 bg-{{ $statusColor }}-100 dark:bg-{{ $statusColor }}-900 text-{{ $statusColor }}-800 dark:text-{{ $statusColor }}-200 rounded-full text-xs font-medium capitalize">{{ str_replace('_', ' ', $appointment->STATUS) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-calendar-times text-4xl text-muted mb-4"></i>
                                    <p class="text-muted">No appointments scheduled for today</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <!-- Revenue and Growth -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Chart -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="700">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-text mb-6">Monthly Revenue</h3>
                    <div class="h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Growth Stats -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="800">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-text mb-6">Growth Overview</h3>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted">Appointments Growth</p>
                                <p class="text-2xl font-bold text-text">{{ number_format($appointmentGrowth, 1) }}%</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-blue-600 dark:text-blue-400"></i>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted">Revenue Growth</p>
                                <p class="text-2xl font-bold text-text">{{ number_format($revenueGrowth, 1) }}%</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-green-600 dark:text-green-400"></i>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted">Wallet Balance</p>
                                <p class="text-2xl font-bold text-text">${{ number_format($walletBalance, 2) }}</p>
                            </div>
                            <div class="w-12 h-12 bg-gold/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-wallet text-gold"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="card feature-card" data-aos="fade-up" data-aos-delay="900">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-text">Recent Appointments</h3>
                    <a href="{{ route('doctor.appointments.index') }}" class="btn btn-outline text-sm">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-medium text-text">Patient</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Date</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Time</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Type</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Status</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAppointments as $appointment)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="py-3 px-4">
                                        <div>
                                            <p class="font-medium text-text">{{ $appointment->patient_name }}</p>
                                            <p class="text-sm text-muted">{{ $appointment->patient_phone }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-text">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</td>
                                    <td class="py-3 px-4 text-text">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</td>
                                    <td class="py-3 px-4 text-text">{{ explode(' - ', $appointment->notes)[0] ?? 'General Consultation' }}</td>
                                    <td class="py-3 px-4">
                                        @php
                                            $tableStatusColor = match($appointment->STATUS) {
                                                'confirmed' => 'green',
                                                'completed' => 'blue',
                                                'scheduled' => 'yellow',
                                                default => 'gray'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 bg-{{ $tableStatusColor }}-100 dark:bg-{{ $tableStatusColor }}-900 text-{{ $tableStatusColor }}-800 dark:text-{{ $tableStatusColor }}-200 rounded-full text-xs font-medium capitalize">{{ str_replace('_', ' ', $appointment->STATUS) }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <a href="{{ route('doctor.appointments.show', $appointment->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-muted">
                                        <i class="fas fa-calendar-times text-4xl mb-4"></i>
                                        <p>No recent appointments</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($monthlyRevenue->pluck('month')),
        datasets: [{
            label: 'Monthly Revenue',
            data: @json($monthlyRevenue->pluck('revenue')),
            borderColor: '#f59e0b',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Navigation functions
function previousDay() {
    // Implement previous day navigation
    console.log('Previous day clicked');
}

function nextDay() {
    // Implement next day navigation
    console.log('Next day clicked');
}

// Auto-refresh dashboard data every 5 minutes
setInterval(function() {
    fetch('/doctor/stats')
        .then(response => response.json())
        .then(data => {
            // Update stats if needed
            console.log('Dashboard data refreshed');
        })
        .catch(error => console.error('Error refreshing dashboard:', error));
}, 300000);
</script>
@endpush
