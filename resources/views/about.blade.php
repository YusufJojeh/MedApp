@extends('layouts.app')

@section('title', 'About Us - Medical Booking System')

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
                    About <span class="text-gold">MedBook</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    Revolutionizing healthcare access through innovative technology and compassionate care. 
                    We connect patients with world-class medical professionals seamlessly.
                </p>
            </div>
        </div>
    </div>

    <!-- Mission & Vision -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Mission -->
            <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10" data-aos="fade-right">
                <div class="w-16 h-16 bg-gradient-to-br from-gold to-gold-deep rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-bullseye text-white text-2xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-white mb-4">Our Mission</h3>
                <p class="text-gray-300 leading-relaxed">
                    To democratize healthcare access by providing a seamless platform that connects patients 
                    with qualified medical professionals, ensuring quality care is accessible to everyone, 
                    everywhere, at any time.
                </p>
            </div>

            <!-- Vision -->
            <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10" data-aos="fade-left">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-eye text-white text-2xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-white mb-4">Our Vision</h3>
                <p class="text-gray-300 leading-relaxed">
                    To become the world's leading healthcare platform, transforming how people access and 
                    experience medical care through cutting-edge technology and unwavering commitment to 
                    patient well-being.
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-6 border border-white/10 text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="text-4xl font-bold text-gold mb-2">50K+</div>
                <div class="text-gray-300">Happy Patients</div>
            </div>
            <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-6 border border-white/10 text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="text-4xl font-bold text-gold mb-2">500+</div>
                <div class="text-gray-300">Expert Doctors</div>
            </div>
            <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-6 border border-white/10 text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="text-4xl font-bold text-gold mb-2">25+</div>
                <div class="text-gray-300">Specialties</div>
            </div>
            <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-6 border border-white/10 text-center" data-aos="fade-up" data-aos-delay="400">
                <div class="text-4xl font-bold text-gold mb-2">99.9%</div>
                <div class="text-gray-300">Uptime</div>
            </div>
        </div>
    </div>

    <!-- Story Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-12 border border-white/10" data-aos="fade-up">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-white mb-4">Our Story</h2>
                <p class="text-gray-300 max-w-2xl mx-auto">
                    From a simple idea to a revolutionary healthcare platform
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-lightbulb text-white text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-2">The Beginning</h4>
                    <p class="text-gray-300">
                        Founded in 2024, MedBook emerged from a vision to bridge the gap between patients 
                        and healthcare providers through innovative technology.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-rocket text-white text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-2">Rapid Growth</h4>
                    <p class="text-gray-300">
                        Within months, we've connected thousands of patients with qualified doctors, 
                        building trust and transforming healthcare delivery.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-globe text-white text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-2">Global Impact</h4>
                    <p class="text-gray-300">
                        Today, we're expanding globally, bringing quality healthcare to communities 
                        worldwide with our innovative platform.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Values Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-white mb-4">Our Core Values</h2>
            <p class="text-gray-300 max-w-2xl mx-auto">
                The principles that guide everything we do
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-6 border border-white/10 text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-heart text-white text-xl"></i>
                </div>
                <h4 class="text-lg font-bold text-white mb-2">Compassion</h4>
                <p class="text-gray-300 text-sm">
                    We care deeply about every patient's well-being and experience.
                </p>
            </div>

            <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-6 border border-white/10 text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-white text-xl"></i>
                </div>
                <h4 class="text-lg font-bold text-white mb-2">Trust</h4>
                <p class="text-gray-300 text-sm">
                    Building lasting relationships through transparency and reliability.
                </p>
            </div>

            <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-6 border border-white/10 text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lightbulb text-white text-xl"></i>
                </div>
                <h4 class="text-lg font-bold text-white mb-2">Innovation</h4>
                <p class="text-gray-300 text-sm">
                    Continuously improving through cutting-edge technology and solutions.
                </p>
            </div>

            <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-6 border border-white/10 text-center" data-aos="fade-up" data-aos-delay="400">
                <div class="w-16 h-16 bg-gradient-to-br from-gold to-gold-deep rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-white text-xl"></i>
                </div>
                <h4 class="text-lg font-bold text-white mb-2">Excellence</h4>
                <p class="text-gray-300 text-sm">
                    Striving for the highest standards in everything we deliver.
                </p>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-12 border border-white/10" data-aos="fade-up">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-white mb-4">Meet Our Team</h2>
                <p class="text-gray-300 max-w-2xl mx-auto">
                    The passionate individuals behind MedBook's success
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-tie text-white text-4xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-2">Dr. Sarah Johnson</h4>
                    <p class="text-gold mb-2">Chief Medical Officer</p>
                    <p class="text-gray-300 text-sm">
                        Leading our medical standards and ensuring quality care delivery.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-laptop-code text-white text-4xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-2">Michael Chen</h4>
                    <p class="text-gold mb-2">Chief Technology Officer</p>
                    <p class="text-gray-300 text-sm">
                        Driving innovation and building cutting-edge healthcare technology.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-white text-4xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-2">Emily Rodriguez</h4>
                    <p class="text-gold mb-2">Chief Executive Officer</p>
                    <p class="text-gray-300 text-sm">
                        Visionary leader guiding MedBook's mission and global expansion.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="backdrop-blur-xl bg-gradient-to-r from-gold/20 to-gold-deep/20 rounded-3xl p-12 border border-gold/30 text-center" data-aos="fade-up">
            <h2 class="text-4xl font-bold text-white mb-4">Join the Healthcare Revolution</h2>
            <p class="text-gray-300 max-w-2xl mx-auto mb-8">
                Experience the future of healthcare today. Book your appointment with world-class doctors 
                and take control of your health journey.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('doctors.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-search mr-2"></i>
                    Find a Doctor
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline btn-lg">
                    <i class="fas fa-user-plus mr-2"></i>
                    Get Started
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
