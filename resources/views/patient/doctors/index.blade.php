@extends('layouts.app')

@section('title', 'Find Doctors - Patient Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Find Doctors</h1>
                    <p class="text-muted mt-2">Browse and book appointments with healthcare professionals</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="btn btn-outline" onclick="window.location.href='{{ route('patient.appointments.index') }}'">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Appointments
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search and Filters -->
        <div class="card feature-card mb-8" data-aos="fade-up">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Search</label>
                        <input type="text" id="searchInput" placeholder="Search doctors..."
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
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Rating</label>
                        <select id="ratingFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Ratings</option>
                            <option value="4">4+ Stars</option>
                            <option value="3">3+ Stars</option>
                            <option value="2">2+ Stars</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="btn btn-primary w-full" onclick="filterDoctors()">
                            <i class="fas fa-search mr-2"></i>
                            Search
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-4 mb-8">
            <button class="btn btn-outline" onclick="window.location.href='{{ route('patient.doctors.top-rated') }}'">
                <i class="fas fa-star mr-2"></i>
                Top Rated
            </button>
            <button class="btn btn-outline" onclick="window.location.href='{{ route('patient.doctors.favorites') }}'">
                <i class="fas fa-heart mr-2"></i>
                My Favorites
            </button>
            <button class="btn btn-outline" onclick="window.location.href='{{ route('patient.doctors.recently-visited') }}'">
                <i class="fas fa-history mr-2"></i>
                Recently Visited
            </button>
        </div>

        <!-- Doctors Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="doctorsGrid">
            @forelse($doctors as $doctor)
                <div class="card feature-card doctor-card" data-aos="fade-up">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-user-md text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-text">Dr. {{ $doctor->name }}</h3>
                                    <p class="text-sm text-muted">{{ $doctor->specialty_name }}</p>
                                </div>
                            </div>
                            <button class="text-gray-400 hover:text-red-500 transition-colors favorite-btn"
                                    data-doctor-id="{{ $doctor->id }}"
                                    onclick="toggleFavorite({{ $doctor->id }})">
                                <i class="fas fa-heart {{ $doctor->is_favorite ? 'text-red-500' : '' }}"></i>
                            </button>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-star text-gold mr-2"></i>
                                <span class="text-text">{{ number_format($doctor->average_rating, 1) }}/5</span>
                                <span class="text-muted ml-1">({{ $doctor->total_reviews ?? 0 }} reviews)</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-briefcase text-muted mr-2"></i>
                                <span class="text-text">{{ $doctor->experience_years }} years experience</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-dollar-sign text-muted mr-2"></i>
                                <span class="text-text">${{ number_format($doctor->consultation_fee, 2) }} consultation</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-clock text-muted mr-2"></i>
                                <span class="text-text {{ $doctor->is_available ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $doctor->is_available ? 'Available' : 'Not Available' }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-2 mb-6">
                            <p class="text-sm text-muted line-clamp-3">{{ Str::limit($doctor->description, 120) }}</p>
                        </div>

                        <div class="flex space-x-2">
                            <button class="btn btn-outline flex-1" onclick="viewDoctor({{ $doctor->id }})">
                                <i class="fas fa-eye mr-1"></i>
                                View Profile
                            </button>
                            @if($doctor->is_available)
                                <button class="btn btn-primary flex-1" onclick="bookAppointment({{ $doctor->id }})">
                                    <i class="fas fa-calendar-plus mr-1"></i>
                                    Book Now
                                </button>
                            @else
                                <button class="btn btn-outline flex-1" disabled>
                                    <i class="fas fa-clock mr-1"></i>
                                    Unavailable
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="card feature-card">
                        <div class="p-12 text-center">
                            <i class="fas fa-user-md text-6xl text-muted mb-4"></i>
                            <h3 class="text-xl font-bold text-text mb-2">No Doctors Found</h3>
                            <p class="text-muted mb-6">Try adjusting your search criteria</p>
                            <button class="btn btn-primary" onclick="clearFilters()">
                                <i class="fas fa-refresh mr-2"></i>
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($doctors->hasPages())
            <div class="mt-8">
                {{ $doctors->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Doctor Details Modal -->
<div id="doctorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-text">Doctor Profile</h3>
                    <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" onclick="closeDoctorModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="doctorDetails">
                    <!-- Doctor details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Filter doctors
    function filterDoctors() {
        const search = document.getElementById('searchInput').value;
        const specialty = document.getElementById('specialtyFilter').value;
        const rating = document.getElementById('ratingFilter').value;

        let url = '{{ route("patient.doctors.index") }}?';
        if (search) url += `search=${encodeURIComponent(search)}&`;
        if (specialty) url += `specialty=${specialty}&`;
        if (rating) url += `rating=${rating}&`;

        window.location.href = url;
    }

    // Clear filters
    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('specialtyFilter').value = '';
        document.getElementById('ratingFilter').value = '';
        window.location.href = '{{ route("patient.doctors.index") }}';
    }

    // View doctor details
    function viewDoctor(doctorId) {
        fetch(`{{ route('patient.doctors.index') }}/${doctorId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('doctorDetails').innerHTML = data.html;
                    document.getElementById('doctorModal').classList.remove('hidden');
                } else {
                    showNotification('Error loading doctor details', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error loading doctor details', 'error');
            });
    }

    // Book appointment
    function bookAppointment(doctorId) {
        window.location.href = `{{ route('patient.appointments.create') }}?doctor_id=${doctorId}`;
    }

    // Toggle favorite
    function toggleFavorite(doctorId) {
        fetch(`{{ route('patient.doctors.index') }}/${doctorId}/favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const btn = document.querySelector(`[data-doctor-id="${doctorId}"]`);
                const icon = btn.querySelector('i');

                if (data.is_favorite) {
                    icon.classList.add('text-red-500');
                    showNotification('Added to favorites', 'success');
                } else {
                    icon.classList.remove('text-red-500');
                    showNotification('Removed from favorites', 'success');
                }
            } else {
                showNotification('Error updating favorites', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating favorites', 'error');
        });
    }

    // Close doctor modal
    function closeDoctorModal() {
        document.getElementById('doctorModal').classList.add('hidden');
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

        if (urlParams.get('search')) {
            document.getElementById('searchInput').value = urlParams.get('search');
        }
        if (urlParams.get('specialty')) {
            document.getElementById('specialtyFilter').value = urlParams.get('specialty');
        }
        if (urlParams.get('rating')) {
            document.getElementById('ratingFilter').value = urlParams.get('rating');
        }
    });
</script>
@endpush
