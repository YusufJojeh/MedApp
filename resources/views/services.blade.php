@extends('layouts.app')

@section('title', 'Our Services - Medical Booking System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 w-72 h-72 bg-gold/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center" data-aos="fade-up">
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6">
                    Our <span class="text-gold">Services</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    Comprehensive healthcare solutions designed to meet your every medical need.
                    From routine checkups to specialized care, we've got you covered.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Services -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Primary Care -->
            <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 hover:border-gold/30 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-user-md text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Primary Care</h3>
                <p class="text-gray-300 mb-6">
                    Comprehensive health checkups, preventive care, and ongoing health management
                    with experienced primary care physicians.
                </p>
                <ul class="text-gray-300 space-y-2 mb-6">
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Annual physical exams
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Vaccination services
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Chronic disease management
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Health screenings
                    </li>
                </ul>
                <a href="{{ route('doctors.index') }}?specialty=primary-care" class="btn btn-primary w-full">
                    Find Primary Care Doctor
                </a>
            </div>

            <!-- Specialized Care -->
            <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 hover:border-gold/30 transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-stethoscope text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Specialized Care</h3>
                <p class="text-gray-300 mb-6">
                    Expert care from specialists in cardiology, dermatology, orthopedics,
                    and more than 25 medical specialties.
                </p>
                <ul class="text-gray-300 space-y-2 mb-6">
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Cardiology & heart health
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Dermatology & skin care
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Orthopedics & sports medicine
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Neurology & brain health
                    </li>
                </ul>
                <a href="{{ route('doctors.index') }}" class="btn btn-primary w-full">
                    Browse Specialists
                </a>
            </div>

            <!-- Telemedicine -->
            <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 hover:border-gold/30 transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-video text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Telemedicine</h3>
                <p class="text-gray-300 mb-6">
                    Virtual consultations from the comfort of your home.
                    Connect with doctors instantly via secure video calls.
                </p>
                <ul class="text-gray-300 space-y-2 mb-6">
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Secure video consultations
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        24/7 availability
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Prescription services
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Follow-up care
                    </li>
                </ul>
                <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary w-full">
                    Book Virtual Visit
                </a>
            </div>

            <!-- Emergency Care -->
            <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 hover:border-gold/30 transition-all duration-300" data-aos="fade-up" data-aos-delay="400">
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-ambulance text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Emergency Care</h3>
                <p class="text-gray-300 mb-6">
                    Immediate medical attention when you need it most.
                    Connect with emergency care specialists instantly.
                </p>
                <ul class="text-gray-300 space-y-2 mb-6">
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Immediate consultation
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Emergency referrals
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Urgent care coordination
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Hospital connections
                    </li>
                </ul>
                <a href="tel:911" class="btn btn-danger w-full">
                    <i class="fas fa-phone mr-2"></i>
                    Emergency: 911
                </a>
            </div>

            <!-- Mental Health -->
            <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 hover:border-gold/30 transition-all duration-300" data-aos="fade-up" data-aos-delay="500">
                <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-brain text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Mental Health</h3>
                <p class="text-gray-300 mb-6">
                    Professional mental health support from licensed therapists
                    and psychiatrists in a safe, confidential environment.
                </p>
                <ul class="text-gray-300 space-y-2 mb-6">
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Individual therapy
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Psychiatric evaluation
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Anxiety & depression care
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Stress management
                    </li>
                </ul>
                <a href="{{ route('doctors.index') }}?specialty=psychiatry" class="btn btn-primary w-full">
                    Find Mental Health Expert
                </a>
            </div>

            <!-- Preventive Care -->
            <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 hover:border-gold/30 transition-all duration-300" data-aos="fade-up" data-aos-delay="600">
                <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Preventive Care</h3>
                <p class="text-gray-300 mb-6">
                    Stay healthy with comprehensive preventive care including
                    screenings, vaccinations, and wellness programs.
                </p>
                <ul class="text-gray-300 space-y-2 mb-6">
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Health screenings
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Vaccination programs
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Wellness coaching
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-gold mr-2"></i>
                        Lifestyle counseling
                    </li>
                </ul>
                <a href="{{ route('doctors.index') }}?specialty=preventive" class="btn btn-primary w-full">
                    Schedule Wellness Visit
                </a>
            </div>
        </div>
    </div>

    <!-- AI Health Assistant -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="backdrop-blur-xl bg-gradient-to-r from-gold/20 to-gold-deep/20 rounded-3xl p-12 border border-gold/30" data-aos="fade-up">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-white mb-6">AI Health Assistant</h2>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        Experience the future of healthcare with our intelligent AI assistant.
                        Get instant health insights, symptom analysis, and personalized recommendations
                        powered by advanced artificial intelligence.
                    </p>
                    <ul class="text-gray-300 space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-robot text-gold mr-3 text-xl"></i>
                            Symptom analysis and preliminary diagnosis
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-robot text-gold mr-3 text-xl"></i>
                            Medication information and interactions
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-robot text-gold mr-3 text-xl"></i>
                            Health tips and lifestyle recommendations
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-robot text-gold mr-3 text-xl"></i>
                            Appointment scheduling assistance
                        </li>
                    </ul>
                    <a href="/ai" class="btn btn-primary btn-lg">
                        <i class="fas fa-comments mr-2"></i>
                        Chat with AI Assistant
                    </a>
                </div>
                <div class="text-center">
                    <div class="w-64 h-64 bg-gradient-to-br from-gold/20 to-gold-deep/20 rounded-full flex items-center justify-center mx-auto">
                        <i class="fas fa-robot text-gold text-8xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Specialties Grid -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-white mb-4">Medical Specialties</h2>
            <p class="text-gray-300 max-w-2xl mx-auto">
                Access to over 25 medical specialties with board-certified experts
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @php
                $specialties = [
                    ['name' => 'Cardiology', 'icon' => 'fa-heart', 'color' => 'from-red-500 to-red-600'],
                    ['name' => 'Dermatology', 'icon' => 'fa-user', 'color' => 'from-pink-500 to-pink-600'],
                    ['name' => 'Orthopedics', 'icon' => 'fa-bone', 'color' => 'from-blue-500 to-blue-600'],
                    ['name' => 'Neurology', 'icon' => 'fa-brain', 'color' => 'from-purple-500 to-purple-600'],
                    ['name' => 'Pediatrics', 'icon' => 'fa-baby', 'color' => 'from-green-500 to-green-600'],
                    ['name' => 'Oncology', 'icon' => 'fa-microscope', 'color' => 'from-yellow-500 to-yellow-600'],
                    ['name' => 'Psychiatry', 'icon' => 'fa-comments', 'color' => 'from-indigo-500 to-indigo-600'],
                    ['name' => 'Gynecology', 'icon' => 'fa-female', 'color' => 'from-pink-500 to-pink-600'],
                    ['name' => 'Urology', 'icon' => 'fa-tint', 'color' => 'from-blue-500 to-blue-600'],
                    ['name' => 'Ophthalmology', 'icon' => 'fa-eye', 'color' => 'from-green-500 to-green-600'],
                    ['name' => 'ENT', 'icon' => 'fa-ear', 'color' => 'from-purple-500 to-purple-600'],
                    ['name' => 'Gastroenterology', 'icon' => 'fa-stomach', 'color' => 'from-orange-500 to-orange-600'],
                ];
            @endphp

            @foreach($specialties as $specialty)
                <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-4 border border-white/10 hover:border-gold/30 transition-all duration-300 text-center group cursor-pointer" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <div class="w-12 h-12 bg-gradient-to-br {{ $specialty['color'] }} rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas {{ $specialty['icon'] }} text-white"></i>
                    </div>
                    <h4 class="text-sm font-medium text-white">{{ $specialty['name'] }}</h4>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Why Choose Us -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-12 border border-white/10" data-aos="fade-up">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-white mb-4">Why Choose MedBook?</h2>
                <p class="text-gray-300 max-w-2xl mx-auto">
                    Experience healthcare reimagined with our innovative platform
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-gold to-gold-deep rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-2">24/7 Availability</h4>
                    <p class="text-gray-300 text-sm">
                        Access healthcare anytime, anywhere with our round-the-clock platform.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-certificate text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-2">Verified Doctors</h4>
                    <p class="text-gray-300 text-sm">
                        All our doctors are board-certified and thoroughly vetted for quality.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-2">Secure & Private</h4>
                    <p class="text-gray-300 text-sm">
                        Your health information is protected with bank-level security.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dollar-sign text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-2">Transparent Pricing</h4>
                    <p class="text-gray-300 text-sm">
                        No hidden fees. Clear, upfront pricing for all services.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="backdrop-blur-xl bg-gradient-to-r from-gold/20 to-gold-deep/20 rounded-3xl p-12 border border-gold/30 text-center" data-aos="fade-up">
            <h2 class="text-4xl font-bold text-white mb-4">Ready to Get Started?</h2>
            <p class="text-gray-300 max-w-2xl mx-auto mb-8">
                Join thousands of patients who trust MedBook for their healthcare needs.
                Book your first appointment today and experience the future of healthcare.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('doctors.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-search mr-2"></i>
                    Find a Doctor
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline btn-lg">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Account
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
