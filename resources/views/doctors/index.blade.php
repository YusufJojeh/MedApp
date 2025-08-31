@extends('layouts.app')

@section('title', 'Find Doctors')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-text">Find Your Perfect Doctor</h1>
                <p class="text-muted mt-2">Connect with experienced healthcare professionals</p>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card feature-card mb-8" data-aos="fade-up" data-aos-delay="100">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-text mb-2">Search Doctors</label>
                        <div class="relative">
                            <input type="text" id="search" placeholder="Search by name, specialty, or location"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted"></i>
                        </div>
                    </div>
                    <div>
                        <label for="specialty" class="block text-sm font-medium text-text mb-2">Specialty</label>
                        <select id="specialty" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Specialties</option>
                            <option value="cardiology">Cardiology</option>
                            <option value="dermatology">Dermatology</option>
                            <option value="neurology">Neurology</option>
                            <option value="orthopedics">Orthopedics</option>
                            <option value="pediatrics">Pediatrics</option>
                            <option value="psychiatry">Psychiatry</option>
                        </select>
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-medium text-text mb-2">Location</label>
                        <select id="location" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Locations</option>
                            <option value="new-york">New York</option>
                            <option value="los-angeles">Los Angeles</option>
                            <option value="chicago">Chicago</option>
                            <option value="houston">Houston</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctors Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Doctor Card 1 -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user-md text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-text">Dr. Sarah Johnson</h3>
                            <p class="text-muted">Cardiology</p>
                            <div class="flex items-center mt-1">
                                <div class="flex text-gold">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-sm text-muted ml-2">(4.9)</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mb-4">Experienced cardiologist with over 15 years of practice. Specializes in heart disease prevention and treatment.</p>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-sm text-muted">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <span>New York, NY</span>
                        </div>
                        <div class="flex items-center text-sm text-muted">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Available Today</span>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="flex-1 btn btn-primary text-sm">
                            <i class="fas fa-calendar-plus mr-1"></i>
                            Book Appointment
                        </button>
                        <button class="btn btn-outline text-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Doctor Card 2 -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user-md text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-text">Dr. Michael Wilson</h3>
                            <p class="text-muted">Dermatology</p>
                            <div class="flex items-center mt-1">
                                <div class="flex text-gold">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <span class="text-sm text-muted ml-2">(4.7)</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mb-4">Board-certified dermatologist specializing in skin cancer detection and cosmetic dermatology procedures.</p>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-sm text-muted">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <span>Los Angeles, CA</span>
                        </div>
                        <div class="flex items-center text-sm text-muted">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Available Tomorrow</span>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="flex-1 btn btn-primary text-sm">
                            <i class="fas fa-calendar-plus mr-1"></i>
                            Book Appointment
                        </button>
                        <button class="btn btn-outline text-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Doctor Card 3 -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="400">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user-md text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-text">Dr. Emily Brown</h3>
                            <p class="text-muted">Neurology</p>
                            <div class="flex items-center mt-1">
                                <div class="flex text-gold">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-sm text-muted ml-2">(4.8)</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mb-4">Neurologist with expertise in stroke treatment, epilepsy, and movement disorders. Research-focused approach.</p>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-sm text-muted">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <span>Chicago, IL</span>
                        </div>
                        <div class="flex items-center text-sm text-muted">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Available Today</span>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="flex-1 btn btn-primary text-sm">
                            <i class="fas fa-calendar-plus mr-1"></i>
                            Book Appointment
                        </button>
                        <button class="btn btn-outline text-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Doctor Card 4 -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="500">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user-md text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-text">Dr. David Chen</h3>
                            <p class="text-muted">Orthopedics</p>
                            <div class="flex items-center mt-1">
                                <div class="flex text-gold">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <span class="text-sm text-muted ml-2">(4.6)</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mb-4">Orthopedic surgeon specializing in sports medicine and joint replacement surgeries. Minimally invasive techniques.</p>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-sm text-muted">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <span>Houston, TX</span>
                        </div>
                        <div class="flex items-center text-sm text-muted">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Available Next Week</span>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="flex-1 btn btn-primary text-sm">
                            <i class="fas fa-calendar-plus mr-1"></i>
                            Book Appointment
                        </button>
                        <button class="btn btn-outline text-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Doctor Card 5 -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="600">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user-md text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-text">Dr. Lisa Rodriguez</h3>
                            <p class="text-muted">Pediatrics</p>
                            <div class="flex items-center mt-1">
                                <div class="flex text-gold">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-sm text-muted ml-2">(4.9)</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mb-4">Pediatrician with 20+ years of experience. Specializes in child development and preventive care.</p>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-sm text-muted">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <span>Miami, FL</span>
                        </div>
                        <div class="flex items-center text-sm text-muted">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Available Today</span>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="flex-1 btn btn-primary text-sm">
                            <i class="fas fa-calendar-plus mr-1"></i>
                            Book Appointment
                        </button>
                        <button class="btn btn-outline text-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Doctor Card 6 -->
            <div class="card feature-card" data-aos="fade-up" data-aos-delay="700">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user-md text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-text">Dr. Robert Taylor</h3>
                            <p class="text-muted">Psychiatry</p>
                            <div class="flex items-center mt-1">
                                <div class="flex text-gold">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <span class="text-sm text-muted ml-2">(4.5)</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mb-4">Psychiatrist specializing in anxiety, depression, and trauma therapy. Uses evidence-based treatment approaches.</p>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-sm text-muted">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <span>Seattle, WA</span>
                        </div>
                        <div class="flex items-center text-sm text-muted">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Available Tomorrow</span>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="flex-1 btn btn-primary text-sm">
                            <i class="fas fa-calendar-plus mr-1"></i>
                            Book Appointment
                        </button>
                        <button class="btn btn-outline text-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Load More -->
        <div class="text-center mt-8" data-aos="fade-up" data-aos-delay="800">
            <button class="btn btn-outline btn-lg">
                <i class="fas fa-plus mr-2"></i>
                Load More Doctors
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Search functionality
    const searchInput = document.getElementById('search');
    const specialtySelect = document.getElementById('specialty');
    const locationSelect = document.getElementById('location');
    const doctorCards = document.querySelectorAll('.card');

    function filterDoctors() {
        const searchTerm = searchInput.value.toLowerCase();
        const specialty = specialtySelect.value.toLowerCase();
        const location = locationSelect.value.toLowerCase();

        doctorCards.forEach(card => {
            const doctorName = card.querySelector('h3').textContent.toLowerCase();
            const doctorSpecialty = card.querySelector('p').textContent.toLowerCase();
            const doctorLocation = card.querySelector('.fa-map-marker-alt').nextElementSibling.textContent.toLowerCase();

            const matchesSearch = doctorName.includes(searchTerm) || doctorSpecialty.includes(searchTerm);
            const matchesSpecialty = !specialty || doctorSpecialty.includes(specialty);
            const matchesLocation = !location || doctorLocation.includes(location);

            if (matchesSearch && matchesSpecialty && matchesLocation) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterDoctors);
    specialtySelect.addEventListener('change', filterDoctors);
    locationSelect.addEventListener('change', filterDoctors);

    // Book appointment buttons
    document.querySelectorAll('.btn-primary').forEach(button => {
        button.addEventListener('click', function() {
            const doctorName = this.closest('.card').querySelector('h3').textContent;
            showNotification(`Booking appointment with ${doctorName}...`, 'info');
        });
    });
</script>
@endpush
