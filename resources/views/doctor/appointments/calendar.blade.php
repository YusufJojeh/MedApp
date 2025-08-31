@extends('layouts.app')

@section('title', 'Calendar - Doctor Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Calendar</h1>
                    <p class="text-muted mt-2">View your appointments in calendar format</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-muted">{{ now()->format('F Y') }}</span>
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
        <!-- Calendar Controls -->
        <div class="card feature-card mb-8" data-aos="fade-up">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <button class="btn btn-outline" onclick="previousMonth()">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <h2 class="text-xl font-bold text-text" id="currentMonth">{{ now()->format('F Y') }}</h2>
                        <button class="btn btn-outline" onclick="nextMonth()">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="btn btn-outline" onclick="goToToday()">
                            <i class="fas fa-calendar-day mr-2"></i>
                            Today
                        </button>
                        <select id="viewType" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="month">Month</option>
                            <option value="week">Week</option>
                            <option value="day">Day</option>
                        </select>
                    </div>
                </div>

                <!-- Calendar Legend -->
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                        <span class="text-sm text-muted">Completed</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-blue-500 rounded"></div>
                        <span class="text-sm text-muted">Scheduled</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                        <span class="text-sm text-muted">Confirmed</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-red-500 rounded"></div>
                        <span class="text-sm text-muted">Cancelled</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="card feature-card" data-aos="fade-up">
            <div class="p-6">
                <!-- Week Days Header -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                    <div class="p-3 text-center font-medium text-text">Sun</div>
                    <div class="p-3 text-center font-medium text-text">Mon</div>
                    <div class="p-3 text-center font-medium text-text">Tue</div>
                    <div class="p-3 text-center font-medium text-text">Wed</div>
                    <div class="p-3 text-center font-medium text-text">Thu</div>
                    <div class="p-3 text-center font-medium text-text">Fri</div>
                    <div class="p-3 text-center font-medium text-text">Sat</div>
                </div>

                <!-- Calendar Days -->
                <div class="grid grid-cols-7 gap-1" id="calendarGrid">
                    <!-- Calendar days will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 flex flex-wrap gap-4">
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
                Past
            </button>
            <button class="btn btn-outline" onclick="exportCalendar()">
                <i class="fas fa-download mr-2"></i>
                Export
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

<!-- Day Appointments Modal -->
<div id="dayModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-text" id="dayModalTitle">Appointments for Today</h3>
                    <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" onclick="closeDayModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="dayAppointments">
                    <!-- Day appointments will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentDate = new Date();
    let appointments = @json($appointments ?? []);

    // Initialize calendar
    document.addEventListener('DOMContentLoaded', function() {
        renderCalendar();
        setupEventListeners();
    });

    // Setup event listeners
    function setupEventListeners() {
        document.getElementById('viewType').addEventListener('change', function() {
            renderCalendar();
        });
    }

    // Render calendar
    function renderCalendar() {
        const viewType = document.getElementById('viewType').value;
        const calendarGrid = document.getElementById('calendarGrid');

        if (viewType === 'month') {
            renderMonthView(calendarGrid);
        } else if (viewType === 'week') {
            renderWeekView(calendarGrid);
        } else if (viewType === 'day') {
            renderDayView(calendarGrid);
        }
    }

    // Render month view
    function renderMonthView(container) {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());

        container.innerHTML = '';

        for (let i = 0; i < 42; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);

            const dayElement = createDayElement(date, month);
            container.appendChild(dayElement);
        }
    }

    // Create day element
    function createDayElement(date, currentMonth) {
        const dayDiv = document.createElement('div');
        dayDiv.className = 'min-h-[120px] border border-gray-200 dark:border-gray-700 p-2 relative';

        const isCurrentMonth = date.getMonth() === currentMonth;
        const isToday = isSameDay(date, new Date());

        if (!isCurrentMonth) {
            dayDiv.classList.add('bg-gray-50', 'dark:bg-gray-900');
        }

        if (isToday) {
            dayDiv.classList.add('bg-gold/10', 'border-gold');
        }

        const dayNumber = document.createElement('div');
        dayNumber.className = `text-sm font-medium ${isCurrentMonth ? 'text-text' : 'text-muted'} ${isToday ? 'bg-gold text-white rounded-full w-6 h-6 flex items-center justify-center' : ''}`;
        dayNumber.textContent = date.getDate();
        dayDiv.appendChild(dayNumber);

        // Add appointments for this day
        const dayAppointments = getAppointmentsForDate(date);
        dayAppointments.forEach(appointment => {
            const appointmentElement = createAppointmentElement(appointment);
            dayDiv.appendChild(appointmentElement);
        });

        // Add click handler
        dayDiv.addEventListener('click', () => {
            if (isCurrentMonth) {
                showDayAppointments(date);
            }
        });

        return dayDiv;
    }

    // Create appointment element
    function createAppointmentElement(appointment) {
        const appointmentDiv = document.createElement('div');
        appointmentDiv.className = 'text-xs p-1 rounded mb-1 cursor-pointer text-white truncate';

        // Set color based on status
        switch(appointment.status) {
            case 'completed':
                appointmentDiv.classList.add('bg-green-500');
                break;
            case 'scheduled':
                appointmentDiv.classList.add('bg-blue-500');
                break;
            case 'confirmed':
                appointmentDiv.classList.add('bg-yellow-500');
                break;
            case 'cancelled':
                appointmentDiv.classList.add('bg-red-500');
                break;
            default:
                appointmentDiv.classList.add('bg-gray-500');
        }

        appointmentDiv.textContent = `${appointment.appointment_time} - ${appointment.patient.name}`;
        appointmentDiv.title = `${appointment.patient.name} - ${appointment.appointment_type}`;

        appointmentDiv.addEventListener('click', (e) => {
            e.stopPropagation();
            viewAppointment(appointment.id);
        });

        return appointmentDiv;
    }

    // Get appointments for a specific date
    function getAppointmentsForDate(date) {
        return appointments.filter(appointment => {
            const appointmentDate = new Date(appointment.appointment_date);
            return isSameDay(appointmentDate, date);
        });
    }

    // Check if two dates are the same day
    function isSameDay(date1, date2) {
        return date1.getFullYear() === date2.getFullYear() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getDate() === date2.getDate();
    }

    // Show day appointments
    function showDayAppointments(date) {
        const dayAppointments = getAppointmentsForDate(date);
        const modalTitle = document.getElementById('dayModalTitle');
        const container = document.getElementById('dayAppointments');

        modalTitle.textContent = `Appointments for ${date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}`;

        if (dayAppointments.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-muted">
                    <i class="fas fa-calendar-times text-4xl mb-4"></i>
                    <p>No appointments scheduled for this day</p>
                </div>
            `;
        } else {
            container.innerHTML = dayAppointments.map(appointment => `
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-text">${appointment.patient.name}</p>
                                <p class="text-sm text-muted">${appointment.patient.phone}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 text-xs rounded-full ${getStatusBadgeClass(appointment.status)}">
                            ${getStatusText(appointment.status)}
                        </span>
                    </div>
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-clock text-muted mr-2"></i>
                            <span class="text-text">${formatTime(appointment.appointment_time)}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-stethoscope text-muted mr-2"></i>
                            <span class="text-text">${appointment.appointment_type}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-dollar-sign text-muted mr-2"></i>
                            <span class="text-text">$${parseFloat(appointment.fees).toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="btn btn-sm btn-outline flex-1" onclick="viewAppointment(${appointment.id})">
                            <i class="fas fa-eye mr-1"></i>
                            View
                        </button>
                        ${appointment.status === 'scheduled' ? `
                            <button class="btn btn-sm btn-primary flex-1" onclick="updateStatus(${appointment.id}, 'confirmed')">
                                <i class="fas fa-check mr-1"></i>
                                Confirm
                            </button>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        }

        document.getElementById('dayModal').classList.remove('hidden');
    }

    // Navigation functions
    function previousMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateMonthDisplay();
        renderCalendar();
    }

    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateMonthDisplay();
        renderCalendar();
    }

    function goToToday() {
        currentDate = new Date();
        updateMonthDisplay();
        renderCalendar();
    }

    function updateMonthDisplay() {
        document.getElementById('currentMonth').textContent = currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
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

    // Close modals
    function closeModal() {
        document.getElementById('appointmentModal').classList.add('hidden');
    }

    function closeDayModal() {
        document.getElementById('dayModal').classList.add('hidden');
    }

    // Export calendar
    function exportCalendar() {
        const month = currentDate.getMonth() + 1;
        const year = currentDate.getFullYear();
        window.location.href = `{{ route('doctor.appointments.export') }}?type=calendar&month=${month}&year=${year}`;
    }

    // Utility functions
    function getStatusBadgeClass(status) {
        const classes = {
            'scheduled': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'confirmed': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'no-show': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
        };
        return classes[status] || classes['scheduled'];
    }

    function getStatusText(status) {
        const texts = {
            'scheduled': 'Scheduled',
            'confirmed': 'Confirmed',
            'completed': 'Completed',
            'cancelled': 'Cancelled',
            'no-show': 'No Show'
        };
        return texts[status] || 'Scheduled';
    }

    function formatTime(time) {
        return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Simple notification - you can enhance this with a proper notification system
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(`${type.toUpperCase()}: ${message}`);
    }
</script>
@endpush
