@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
    </div>

    <!-- Floating Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-gradient-to-r from-blue-500/20 to-cyan-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-gradient-to-r from-emerald-500/20 to-teal-500/20 rounded-full blur-3xl animate-pulse delay-2000"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 container mx-auto px-4 text-center">
        <div class="max-w-6xl mx-auto">
            <!-- Trust Badge -->
            <div class="mb-8 flex justify-center">
                <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-full px-6 py-3 flex items-center space-x-3">
                    <div class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse"></div>
                    <span class="text-white/90 text-sm font-medium">Trusted by 50,000+ Patients Worldwide</span>
                </div>
            </div>

            <!-- Main Headline -->
            <h1 class="text-6xl md:text-8xl font-bold text-white mb-6 leading-tight">
                <span class="bg-gradient-to-r from-white via-purple-200 to-white bg-clip-text text-transparent">
                    World-Class
                </span>
                <br>
                <span class="bg-gradient-to-r from-purple-400 via-pink-400 to-purple-400 bg-clip-text text-transparent">
                    Healthcare
                </span>
            </h1>

            <!-- Subheadline -->
            <p class="text-xl md:text-2xl text-white/80 mb-8 max-w-3xl mx-auto leading-relaxed">
                Experience the future of medical care with AI-powered diagnostics,
                world-renowned specialists, and seamless booking in under 60 seconds.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                <a href="{{ route('doctors.index') }}"
                   class="group relative px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl text-white font-semibold text-lg transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/25">
                    <span class="relative z-10">Book Appointment Now</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-700 to-pink-700 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>

                <a href="{{ route('services') }}"
                   class="group px-8 py-4 backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl text-white font-semibold text-lg transition-all duration-300 hover:bg-white/20 hover:scale-105">
                    Explore Services
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                </a>
            </div>

            <!-- Trust Indicators -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
                    <div class="text-3xl font-bold text-white mb-2">50K+</div>
                    <div class="text-white/70 text-sm">Happy Patients</div>
                </div>
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
                    <div class="text-3xl font-bold text-white mb-2">500+</div>
                    <div class="text-white/70 text-sm">Expert Doctors</div>
                </div>
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
                    <div class="text-3xl font-bold text-white mb-2">24/7</div>
                    <div class="text-white/70 text-sm">AI Support</div>
                </div>
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
                    <div class="text-3xl font-bold text-white mb-2">99.9%</div>
                    <div class="text-white/70 text-sm">Uptime</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2">
        <div class="animate-bounce">
            <i class="fas fa-chevron-down text-white/50 text-2xl"></i>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-20 bg-gradient-to-b from-slate-900 to-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%23ffffff" fill-opacity="0.02"%3E%3Cpath d="M20 20c0 11.046-8.954 20-20 20s-20-8.954-20-20 8.954-20 20-20 20 8.954 20 20z"/%3E%3C/g%3E%3C/svg%3E')]"></div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-5xl md:text-6xl font-bold text-white mb-6">
                Why Choose
                <span class="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                    Excellence
                </span>
            </h2>
            <p class="text-xl text-white/70 max-w-3xl mx-auto">
                We're not just another medical platform. We're the future of healthcare,
                combining cutting-edge technology with human expertise.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- AI-Powered Diagnostics -->
            <div class="group backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8 transition-all duration-500 hover:bg-white/10 hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-brain text-2xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">AI-Powered Diagnostics</h3>
                <p class="text-white/70 leading-relaxed">
                    Get instant preliminary assessments with our advanced AI system.
                    Our technology analyzes symptoms with 95% accuracy, providing
                    you with immediate insights while connecting you to specialists.
                </p>
            </div>

            <!-- World-Class Specialists -->
            <div class="group backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8 transition-all duration-500 hover:bg-white/10 hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-md text-2xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">World-Class Specialists</h3>
                <p class="text-white/70 leading-relaxed">
                    Access to 500+ verified specialists from top medical institutions.
                    Each doctor is thoroughly vetted with an average of 15+ years
                    experience and 4.8+ star ratings.
                </p>
            </div>

            <!-- Seamless Experience -->
            <div class="group backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8 transition-all duration-500 hover:bg-white/10 hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-magic text-2xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Seamless Experience</h3>
                <p class="text-white/70 leading-relaxed">
                    Book appointments in under 60 seconds. Our intuitive platform
                    handles everything from scheduling to payments, ensuring
                    your focus stays on your health.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Social Proof Section -->
<section class="py-20 bg-slate-800 relative overflow-hidden">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-5xl md:text-6xl font-bold text-white mb-6">
                Trusted by
                <span class="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                    Patients Worldwide
                </span>
            </h2>
        </div>

        <!-- Testimonials Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <!-- Testimonial 1 -->
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8">
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <p class="text-white/80 mb-6 leading-relaxed">
                    "The AI diagnostic feature is incredible! It helped me understand my symptoms
                    before even seeing the doctor. The whole experience was seamless and professional."
                </p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold">S</span>
                    </div>
                    <div>
                        <div class="text-white font-semibold">Sarah Johnson</div>
                        <div class="text-white/60 text-sm">Verified Patient</div>
                    </div>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8">
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <p class="text-white/80 mb-6 leading-relaxed">
                    "Found an excellent cardiologist within minutes. The booking process was
                    incredibly smooth, and the doctor was available the next day. Highly recommend!"
                </p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold">M</span>
                    </div>
                    <div>
                        <div class="text-white font-semibold">Michael Chen</div>
                        <div class="text-white/60 text-sm">Verified Patient</div>
                    </div>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8">
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <p class="text-white/80 mb-6 leading-relaxed">
                    "The 24/7 AI assistant is a game-changer. I had questions at 2 AM and got
                    immediate, helpful responses. This platform truly cares about patient care."
                </p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold">E</span>
                    </div>
                    <div>
                        <div class="text-white font-semibold">Emily Rodriguez</div>
                        <div class="text-white/60 text-sm">Verified Patient</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-4xl font-bold text-white mb-2">4.9/5</div>
                <div class="text-white/70">Average Rating</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-white mb-2">98%</div>
                <div class="text-white/70">Satisfaction Rate</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-white mb-2">60s</div>
                <div class="text-white/70">Average Booking Time</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-white mb-2">24/7</div>
                <div class="text-white/70">AI Support Available</div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-gradient-to-b from-slate-800 to-slate-900 relative overflow-hidden">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-5xl md:text-6xl font-bold text-white mb-6">
                Revolutionary
                <span class="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                    Features
                </span>
            </h2>
            <p class="text-xl text-white/70 max-w-3xl mx-auto">
                Experience healthcare reimagined with our cutting-edge features designed
                to put you in control of your health journey.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- Feature List -->
            <div class="space-y-8">
                <div class="flex items-start space-x-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-robot text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-3">AI Health Assistant</h3>
                        <p class="text-white/70 leading-relaxed">
                            Get instant health insights, symptom analysis, and preliminary
                            assessments powered by advanced artificial intelligence.
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-3">Smart Scheduling</h3>
                        <p class="text-white/70 leading-relaxed">
                            Intelligent appointment matching based on your symptoms,
                            location, and doctor availability for optimal care.
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-3">Secure & Private</h3>
                        <p class="text-white/70 leading-relaxed">
                            Bank-level security with end-to-end encryption ensuring
                            your health data remains completely confidential.
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-mobile-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-3">Mobile-First Design</h3>
                        <p class="text-white/70 leading-relaxed">
                            Seamless experience across all devices with our responsive
                            design optimized for mobile healthcare management.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Feature Visual -->
            <div class="relative">
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-gradient-to-r from-purple-500 to-pink-500 rounded-3xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-heartbeat text-white text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Your Health Journey</h3>
                        <p class="text-white/70 mb-6">
                            Track your appointments, health records, and AI insights
                            all in one secure, beautiful interface.
                        </p>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-4">
                                <div class="text-2xl font-bold text-white">Real-time</div>
                                <div class="text-white/60 text-sm">Updates</div>
                            </div>
                            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-4">
                                <div class="text-2xl font-bold text-white">Smart</div>
                                <div class="text-white/60 text-sm">Reminders</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-purple-900 via-pink-900 to-purple-900 relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            <h2 class="text-5xl md:text-6xl font-bold text-white mb-6">
                Ready to Experience
                <span class="bg-gradient-to-r from-white to-purple-200 bg-clip-text text-transparent">
                    World-Class Healthcare?
                </span>
            </h2>
            <p class="text-xl text-white/80 mb-8 leading-relaxed">
                Join 50,000+ patients who trust us with their health.
                Book your first appointment in under 60 seconds and experience
                the future of medical care today.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <a href="{{ route('doctors.index') }}"
                   class="group px-8 py-4 bg-white text-purple-900 font-bold text-lg rounded-2xl transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                    Start Your Journey
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                </a>

                <a href="{{ route('about') }}"
                   class="group px-8 py-4 backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl text-white font-bold text-lg transition-all duration-300 hover:bg-white/20">
                    Learn More
                </a>
            </div>

            <!-- Trust Badges -->
            <div class="flex flex-wrap justify-center items-center gap-6 text-white/60">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-shield-alt"></i>
                    <span>HIPAA Compliant</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-lock"></i>
                    <span>256-bit Encryption</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-certificate"></i>
                    <span>ISO 27001 Certified</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-slate-900 py-16 relative overflow-hidden">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-4 gap-8 mb-12">
            <!-- Company Info -->
            <div class="md:col-span-2">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-heartbeat text-white text-xl"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-white">MediBook</div>
                        <div class="text-white/60 text-sm">World-Class Healthcare</div>
                    </div>
                </div>
                <p class="text-white/70 mb-6 leading-relaxed">
                    Revolutionizing healthcare through technology and human expertise.
                    Join us in creating a healthier future for everyone.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white/70 hover:text-white hover:bg-white/20 transition-colors">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white/70 hover:text-white hover:bg-white/20 transition-colors">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white/70 hover:text-white hover:bg-white/20 transition-colors">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white/70 hover:text-white hover:bg-white/20 transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-white font-bold text-lg mb-6">Quick Links</h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('doctors.index') }}" class="text-white/70 hover:text-white transition-colors">Find Doctors</a></li>
                    <li><a href="{{ route('services') }}" class="text-white/70 hover:text-white transition-colors">Our Services</a></li>
                    <li><a href="{{ route('about') }}" class="text-white/70 hover:text-white transition-colors">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="text-white/70 hover:text-white transition-colors">Contact</a></li>
                    <li><a href="#" class="text-white/70 hover:text-white transition-colors">FAQ</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-white font-bold text-lg mb-6">Support</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-white/70 hover:text-white transition-colors">Help Center</a></li>
                    <li><a href="#" class="text-white/70 hover:text-white transition-colors">Live Chat</a></li>
                    <li><a href="#" class="text-white/70 hover:text-white transition-colors">Emergency</a></li>
                    <li><a href="#" class="text-white/70 hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="text-white/70 hover:text-white transition-colors">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center">
            <div class="text-white/60 text-sm mb-4 md:mb-0">
                © 2024 MediBook. All rights reserved. Designed with ❤️ by
                <span class="text-white font-semibold">Hawraa Ahmad Balwi</span>
            </div>
            <div class="flex items-center space-x-6 text-white/60 text-sm">
                <span>Made with cutting-edge technology</span>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-heart text-red-400"></i>
                    <span>For better healthcare</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- AI Assistant Modal Trigger -->
<div id="ai-assistant-trigger" class="fixed bottom-6 right-6 z-50">
    <button class="group w-16 h-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full shadow-2xl hover:scale-110 transition-all duration-300 flex items-center justify-center">
        <i class="fas fa-robot text-white text-xl group-hover:rotate-12 transition-transform duration-300"></i>
    </button>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // AI Assistant Modal Trigger
    const aiTrigger = document.getElementById('ai-assistant-trigger');
    if (aiTrigger) {
        aiTrigger.addEventListener('click', function() {
            // Trigger AI assistant modal
            if (typeof window.openAiAssistant === 'function') {
                window.openAiAssistant();
            } else {
                            // Fallback to direct route
            window.location.href = '/ai';
            }
        });
    }

    // Parallax effect for floating elements
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.absolute');

        parallaxElements.forEach(element => {
            const speed = 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.backdrop-blur-xl').forEach(el => {
        observer.observe(el);
    });
});
</script>
@endpush
