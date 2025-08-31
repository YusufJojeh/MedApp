@extends('layouts.app')

@section('title', 'Admin Dashboard - MediBook')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Admin Dashboard</h1>
                    <p class="text-muted mt-2">Welcome back, {{ auth()->user()->first_name }}! Here's what's happening today.</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-muted text-sm">Current Time</p>
                        <p class="text-text font-semibold" id="currentTime">{{ now()->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-gold to-gold-deep rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="card feature-card" data-aos="fade-up">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['total_users']) }}</p>
                            <p class="text-sm text-muted">Total Users</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Doctors -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-md text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['total_doctors']) }}</p>
                            <p class="text-sm text-muted">Active Doctors</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Appointments -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-check text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['total_appointments']) }}</p>
                            <p class="text-sm text-muted">Total Appointments</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
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
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                <i class="fas fa-users mr-2"></i>
                Users
            </a>
            <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline">
                <i class="fas fa-user-md mr-2"></i>
                Doctors
            </a>
            <a href="{{ route('admin.patients.index') }}" class="btn btn-outline">
                <i class="fas fa-user-injured mr-2"></i>
                Patients
            </a>
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-outline">
                <i class="fas fa-calendar mr-2"></i>
                Appointments
            </a>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline">
                <i class="fas fa-credit-card mr-2"></i>
                Payments
            </a>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline">
                <i class="fas fa-cog mr-2"></i>
                Settings
            </a>
        </div>

        <!-- Charts and Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Chart -->
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">Monthly Revenue</h3>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-xs bg-white/10 rounded-lg text-white/70 hover:bg-white/20" onclick="updateChart('revenue', '6')">6M</button>
                        <button class="px-3 py-1 text-xs bg-white/10 rounded-lg text-white/70 hover:bg-white/20" onclick="updateChart('revenue', '12')">12M</button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Appointments Chart -->
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">Appointment Status</h3>
                    <div class="text-sm text-white/60">Total: {{ number_format($stats['total_appointments']) }}</div>
                </div>
                <div class="h-64">
                    <canvas id="appointmentChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities and System Info -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Appointments -->
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">Recent Appointments</h3>
                    <a href="#" class="text-purple-400 hover:text-purple-300 text-sm">View All</a>
                </div>
                <div class="space-y-4 max-h-80 overflow-y-auto">
                    @forelse($recentAppointments as $appointment)
                    <div class="flex items-center justify-between p-4 backdrop-blur-xl bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all">
                        <div class="flex-1">
                            <p class="text-white font-medium truncate">{{ $appointment->patient_name }}</p>
                            <p class="text-white/60 text-sm truncate">with Dr. {{ $appointment->doctor_name }}</p>
                            <p class="text-white/40 text-xs">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full ml-2 flex-shrink-0
                            @if($appointment->STATUS === 'completed') bg-green-500/20 text-green-400
                            @elseif($appointment->STATUS === 'pending') bg-yellow-500/20 text-yellow-400
                            @elseif($appointment->STATUS === 'cancelled') bg-red-500/20 text-red-400
                            @else bg-blue-500/20 text-blue-400 @endif">
                            {{ ucfirst($appointment->STATUS) }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-4xl text-white/20 mb-4"></i>
                        <p class="text-white/60">No recent appointments</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">Recent Payments</h3>
                    <a href="#" class="text-purple-400 hover:text-purple-300 text-sm">View All</a>
                </div>
                <div class="space-y-4 max-h-80 overflow-y-auto">
                    @forelse($recentPayments as $payment)
                    <div class="flex items-center justify-between p-4 backdrop-blur-xl bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all">
                        <div class="flex-1">
                            <p class="text-white font-medium truncate">{{ $payment->first_name }} {{ $payment->last_name }}</p>
                            <p class="text-white/60 text-sm">${{ number_format($payment->amount, 2) }}</p>
                            <p class="text-white/40 text-xs">{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full ml-2 flex-shrink-0
                            @if($payment->STATUS === 'succeeded') bg-green-500/20 text-green-400
                            @elseif($payment->STATUS === 'pending') bg-yellow-500/20 text-yellow-400
                            @else bg-red-500/20 text-red-400 @endif">
                            {{ ucfirst($payment->STATUS) }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-credit-card text-4xl text-white/20 mb-4"></i>
                        <p class="text-white/60">No recent payments</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- System Information -->
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">System Info</h3>
                    <i class="fas fa-server text-white/40"></i>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div>
                            <p class="text-white font-medium">Active Subscriptions</p>
                            <p class="text-white/60 text-sm">{{ number_format($activeSubscriptions) }} / {{ number_format($totalSubscriptions) }}</p>
                        </div>
                        <div class="text-right">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div>
                            <p class="text-white font-medium">System Health</p>
                            <p class="text-green-400 text-sm">All Systems Operational</p>
                        </div>
                        <div class="text-right">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-heartbeat text-white"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div>
                            <p class="text-white font-medium">Database</p>
                            <p class="text-white/60 text-sm">MySQL - Connected</p>
                        </div>
                        <div class="text-right">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-database text-white"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div>
                            <p class="text-white font-medium">AI Service</p>
                            <p class="text-green-400 text-sm">Flask NLP - Online</p>
                        </div>
                        <div class="text-right">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-robot text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        }) + ' ' + now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('currentTime').textContent = timeString;
    }

    updateTime();
    setInterval(updateTime, 1000);

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json(collect($monthlyRevenue)->pluck('month')),
            datasets: [{
                label: 'Revenue',
                data: @json(collect($monthlyRevenue)->pluck('revenue')),
                borderColor: 'rgb(139, 92, 246)',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(139, 92, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6
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
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.8)',
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.8)'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Appointment Status Chart
    const appointmentCtx = document.getElementById('appointmentChart').getContext('2d');
    const appointmentChart = new Chart(appointmentCtx, {
        type: 'doughnut',
        data: {
            labels: @json(collect($appointmentStatuses)->pluck('STATUS')),
            datasets: [{
                data: @json(collect($appointmentStatuses)->pluck('count')),
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(168, 85, 247, 0.8)'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(255, 255, 255, 0.8)',
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });

    // Add hover effects to cards
    const cards = document.querySelectorAll('.backdrop-blur-xl');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

// Function to update charts (placeholder for future functionality)
function updateChart(type, period) {
    console.log(`Updating ${type} chart for ${period} months`);
    // You can implement AJAX calls here to fetch new data
}
</script>
@endpush
@endsection
