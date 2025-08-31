@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Edit Profile</h1>
                    <p class="text-muted mt-2">Update your personal information and preferences</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-muted">Welcome back,</p>
                        <p class="font-semibold text-text">{{ auth()->user()->name }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Navigation -->
            <div class="lg:col-span-1">
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <div class="w-24 h-24 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-user text-white text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-text">{{ auth()->user()->name }}</h3>
                            <p class="text-muted">{{ auth()->user()->email }}</p>
                            <span class="inline-block px-3 py-1 bg-gold/10 text-gold rounded-full text-sm font-medium mt-2">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                        </div>

                        <nav class="space-y-2">
                            <a href="#personal-info" class="flex items-center p-3 bg-gold/10 text-gold rounded-lg font-medium">
                                <i class="fas fa-user-edit mr-3"></i>
                                Personal Information
                            </a>
                            <a href="#security" class="flex items-center p-3 text-muted hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                                <i class="fas fa-shield-alt mr-3"></i>
                                Security Settings
                            </a>
                            <a href="#notifications" class="flex items-center p-3 text-muted hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                                <i class="fas fa-bell mr-3"></i>
                                Notifications
                            </a>
                            <a href="#preferences" class="flex items-center p-3 text-muted hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                                <i class="fas fa-cog mr-3"></i>
                                Preferences
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Personal Information -->
                <div id="personal-info" class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Personal Information</h3>

                        <form method="POST" action="{{ route('profile.update') }}" data-validate>
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-text mb-2">Full Name</label>
                                    <input type="text" id="name" name="name" value="{{ auth()->user()->name }}"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                           required>
                                    @error('name')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-text mb-2">Email Address</label>
                                    <input type="email" id="email" name="email" value="{{ auth()->user()->email }}"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                           required>
                                    @error('email')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-text mb-2">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" value="{{ auth()->user()->phone ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                    @error('phone')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-text mb-2">Date of Birth</label>
                                    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ auth()->user()->date_of_birth ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                    @error('date_of_birth')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-text mb-2">Address</label>
                                    <textarea id="address" name="address" rows="3"
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">{{ auth()->user()->address ?? '' }}</textarea>
                                    @error('address')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Settings -->
                <div id="security" class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Security Settings</h3>

                        <form method="POST" action="{{ route('password.update') }}" data-validate>
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-text mb-2">Current Password</label>
                                    <input type="password" id="current_password" name="current_password"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                           required>
                                    @error('current_password')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-text mb-2">New Password</label>
                                    <input type="password" id="password" name="password"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                           required>
                                    @error('password')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-text mb-2">Confirm New Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                           required>
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key mr-2"></i>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notifications -->
                <div id="notifications" class="card feature-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Notification Preferences</h3>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-text">Email Notifications</h4>
                                    <p class="text-sm text-muted">Receive notifications via email</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gold/20 dark:peer-focus:ring-gold/20 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-gold"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-text">SMS Notifications</h4>
                                    <p class="text-sm text-muted">Receive notifications via SMS</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gold/20 dark:peer-focus:ring-gold/20 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-gold"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-text">Appointment Reminders</h4>
                                    <p class="text-sm text-muted">Get reminded about upcoming appointments</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gold/20 dark:peer-focus:ring-gold/20 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-gold"></div>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Save Preferences
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Preferences -->
                <div id="preferences" class="card feature-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">General Preferences</h3>

                        <div class="space-y-6">
                            <div>
                                <label for="language" class="block text-sm font-medium text-text mb-2">Language</label>
                                <select id="language" name="language"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                    <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                                    <option value="ar" {{ app()->getLocale() === 'ar' ? 'selected' : '' }}>العربية</option>
                                </select>
                            </div>

                            <div>
                                <label for="timezone" class="block text-sm font-medium text-text mb-2">Timezone</label>
                                <select id="timezone" name="timezone"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                    <option value="UTC">UTC</option>
                                    <option value="America/New_York">Eastern Time</option>
                                    <option value="America/Chicago">Central Time</option>
                                    <option value="America/Denver">Mountain Time</option>
                                    <option value="America/Los_Angeles">Pacific Time</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-text mb-2">Theme Preference</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="theme" value="light" class="mr-2" checked>
                                        <span class="text-text">Light</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="theme" value="dark" class="mr-2">
                                        <span class="text-text">Dark</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="theme" value="auto" class="mr-2">
                                        <span class="text-text">Auto</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Save Preferences
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Profile navigation
    document.querySelectorAll('nav a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            // Remove active class from all links
            document.querySelectorAll('nav a').forEach(l => {
                l.classList.remove('bg-gold/10', 'text-gold');
                l.classList.add('text-muted');
            });

            // Add active class to clicked link
            this.classList.remove('text-muted');
            this.classList.add('bg-gold/10', 'text-gold');

            // Scroll to section
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>
@endpush
