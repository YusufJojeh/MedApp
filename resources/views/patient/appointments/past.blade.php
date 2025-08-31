@extends('layouts.app')

@section('title', 'Past Appointments - Patient Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Past Appointments</h1>
                    <p class="text-muted mt-2">Your completed healthcare appointments</p>
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
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['total_completed'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Total Completed</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-star text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ $stats['total_rated'] ?? 0 }}</p>
                            <p class="text-sm text-muted">Appointments Rated</p>
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
                            <p class="text-sm text-muted">Doctors Visited</p>
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
            <button class="btn btn-outline" onclick="window.location.href='{{ route('patient.appointments.upcoming') }}'">
                <i class="fas fa-calendar-alt mr-2"></i>
                Upcoming Appointments
            </button>
            <button class="btn btn-outline" onclick="exportPast()">
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
                            <option value="last_week">Last Week</option>
                            <option value="last_month">Last Month</option>
                            <option value="last_3_months">Last 3 Months</option>
                            <option value="last_6_months">Last 6 Months</option>
                            <option value="last_year">Last Year</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Doctor</label>
                        <input type="text" id="doctorFilter" placeholder="Search doctor..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Rating</label>
                        <select id="ratingFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4+ Stars</option>
                            <option value="3">3+ Stars</option>
                            <option value="2">2+ Stars</option>
                            <option value="1">1+ Star</option>
                            <option value="unrated">Unrated</option>
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

        <!-- Past Appointments -->
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
                                        @if($appointment->review)
                                            <div class="flex items-center space-x-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star text-sm {{ $i <= $appointment->review->rating ? 'text-gold' : 'text-gray-300' }}"></i>
                                                @endfor
                                                <span class="text-sm text-muted">({{ $appointment->review->rating }}/5)</span>
                                            </div>
                                        @endif
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
                                            <p class="text-sm text-muted">Amount Paid</p>
                                            <p class="font-medium text-text">${{ number_format($appointment->fees, 2) }}</p>
                                        </div>
                                    </div>
                                    @if($appointment->symptoms)
                                        <div class="mb-4">
                                            <p class="text-sm text-muted">Symptoms/Notes</p>
                                            <p class="text-text">{{ $appointment->symptoms }}</p>
                                        </div>
                                    @endif
                                    @if($appointment->review && $appointment->review->comment)
                                        <div class="mb-4">
                                            <p class="text-sm text-muted">Your Review</p>
                                            <p class="text-text italic">"{{ $appointment->review->comment }}"</p>
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
                                @if(!$appointment->review)
                                    <button class="btn btn-sm btn-primary" onclick="rateAppointment({{ $appointment->id }})">
                                        <i class="fas fa-star mr-1"></i>
                                        Rate
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-outline" onclick="bookAgain({{ $appointment->doctor->id }})">
                                    <i class="fas fa-calendar-plus mr-1"></i>
                                    Book Again
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card feature-card">
                    <div class="p-12 text-center">
                        <i class="fas fa-history text-6xl text-muted mb-4"></i>
                        <h3 class="text-xl font-bold text-text mb-2">No Past Appointments</h3>
                        <p class="text-muted mb-6">You haven't completed any appointments yet</p>
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

<!-- Rating Modal -->
<div id="ratingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-text">Rate Your Experience</h3>
                <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" onclick="closeRatingModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="ratingForm">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-text mb-3">Rating</label>
                    <div class="flex items-center space-x-2" id="starRating">
                        <i class="fas fa-star text-2xl text-gray-300 cursor-pointer hover:text-gold" data-rating="1"></i>
                        <i class="fas fa-star text-2xl text-gray-300 cursor-pointer hover:text-gold" data-rating="2"></i>
                        <i class="fas fa-star text-2xl text-gray-300 cursor-pointer hover:text-gold" data-rating="3"></i>
                        <i class="fas fa-star text-2xl text-gray-300 cursor-pointer hover:text-gold" data-rating="4"></i>
                        <i class="fas fa-star text-2xl text-gray-300 cursor-pointer hover:text-gold" data-rating="5"></i>
                    </div>
                    <input type="hidden" id="ratingValue" name="rating" value="0">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-text mb-2">Comment</label>
                    <textarea id="ratingComment" name="comment" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                              placeholder="Share your experience..."></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="button" class="btn btn-outline flex-1" onclick="closeRatingModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary flex-1">Submit Rating</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentAppointmentId = null;

    // Filter appointments
    function filterAppointments() {
        const dateRange = document.getElementById('dateRangeFilter').value;
        const doctor = document.getElementById('doctorFilter').value;
        const rating = document.getElementById('ratingFilter').value;

        let url = '{{ route("patient.appointments.past") }}?';
        if (dateRange) url += `date_range=${dateRange}&`;
        if (doctor) url += `doctor=${encodeURIComponent(doctor)}&`;
        if (rating) url += `rating=${rating}&`;

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

    // Rate appointment
    function rateAppointment(appointmentId) {
        currentAppointmentId = appointmentId;
        document.getElementById('ratingModal').classList.remove('hidden');
        setupStarRating();
    }

    // Setup star rating
    function setupStarRating() {
        const stars = document.querySelectorAll('#starRating i');
        const ratingInput = document.getElementById('ratingValue');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                ratingInput.value = rating;

                // Update star display
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-gold');
                    } else {
                        s.classList.remove('text-gold');
                        s.classList.add('text-gray-300');
                    }
                });
            });

            star.addEventListener('mouseenter', function() {
                const rating = this.getAttribute('data-rating');
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('text-gold');
                    }
                });
            });

            star.addEventListener('mouseleave', function() {
                const currentRating = ratingInput.value;
                stars.forEach((s, index) => {
                    if (index < currentRating) {
                        s.classList.add('text-gold');
                    } else {
                        s.classList.remove('text-gold');
                        s.classList.add('text-gray-300');
                    }
                });
            });
        });
    }

    // Submit rating
    document.getElementById('ratingForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const rating = document.getElementById('ratingValue').value;
        const comment = document.getElementById('ratingComment').value;

        if (rating == 0) {
            showNotification('Please select a rating', 'error');
            return;
        }

        fetch(`{{ route('patient.appointments.index') }}/${currentAppointmentId}/rate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                rating: rating,
                comment: comment
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Rating submitted successfully', 'success');
                closeRatingModal();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification('Error submitting rating', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error submitting rating', 'error');
        });
    });

    // Book again with same doctor
    function bookAgain(doctorId) {
        window.location.href = `{{ route('patient.appointments.create') }}?doctor_id=${doctorId}`;
    }

    // Close modals
    function closeModal() {
        document.getElementById('appointmentModal').classList.add('hidden');
    }

    function closeRatingModal() {
        document.getElementById('ratingModal').classList.add('hidden');
        currentAppointmentId = null;
        document.getElementById('ratingForm').reset();
        document.getElementById('ratingValue').value = '0';

        // Reset stars
        const stars = document.querySelectorAll('#starRating i');
        stars.forEach(star => {
            star.classList.remove('text-gold');
            star.classList.add('text-gray-300');
        });
    }

    // Export past appointments
    function exportPast() {
        const dateRange = document.getElementById('dateRangeFilter').value;
        const doctor = document.getElementById('doctorFilter').value;
        const rating = document.getElementById('ratingFilter').value;

        let url = '{{ route("patient.appointments.export") }}?type=past';
        if (dateRange) url += `&date_range=${dateRange}`;
        if (doctor) url += `&doctor=${encodeURIComponent(doctor)}`;
        if (rating) url += `&rating=${rating}`;

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
        if (urlParams.get('rating')) {
            document.getElementById('ratingFilter').value = urlParams.get('rating');
        }
    });
</script>
@endpush
