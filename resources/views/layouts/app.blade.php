<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">
    <meta name="description" content="Hawraa Ahmad Balwi's Medical Booking System - The most advanced healthcare platform in the world">
    <meta name="keywords" content="medical booking, healthcare, doctor appointments, telemedicine, Hawraa Ahmad Balwi">
    <meta name="author" content="Hawraa Ahmad Balwi">

    <title>@yield('title', config('app.name', 'Medical Booking System')) - Hawraa Ahmad Balwi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    @if(app()->getLocale() === 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @endif

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <style>
        /* ===================== DARK THEME - GLASSMORPHISM ===================== */
        :root {
            /* Dark Theme Color Palette */
            --bg: #0f172a; /* slate-900 */
            --bg-secondary: #1e293b; /* slate-800 */
            --bg-tertiary: #334155; /* slate-700 */
            --surface: #1e293b; /* slate-800 */
            --surface-secondary: #334155; /* slate-700 */
            --text: #f8fafc; /* slate-50 */
            --text-secondary: #cbd5e1; /* slate-300 */
            --text-muted: #94a3b8; /* slate-400 */
            --text-dim: #64748b; /* slate-500 */

            /* Accent Colors - Purple & Pink */
            --primary: #8b5cf6; /* violet-500 */
            --primary-light: #a78bfa; /* violet-400 */
            --primary-dark: #7c3aed; /* violet-600 */
            --secondary: #ec4899; /* pink-500 */
            --secondary-light: #f472b6; /* pink-400 */
            --secondary-dark: #db2777; /* pink-600 */

            /* Glassmorphism */
            --glass: rgba(255, 255, 255, 0.05);
            --glass-strong: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-border-strong: rgba(255, 255, 255, 0.2);

            /* Shadows & Effects */
            --shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --shadow-lg: 0 50px 100px -20px rgba(0, 0, 0, 0.4);
            --radius: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;
            --speed: 300ms;

            /* Gradients */
            --gradient-primary: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            --gradient-secondary: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary-light) 100%);
            --gradient-dark: linear-gradient(135deg, var(--bg) 0%, var(--bg-secondary) 100%);
            --gradient-glass: linear-gradient(135deg, var(--glass) 0%, var(--glass-strong) 100%);

            /* Extended Color Palette */
            --success: #10b981; /* emerald-500 */
            --warning: #f59e0b; /* amber-500 */
            --error: #ef4444; /* red-500 */
            --info: #3b82f6; /* blue-500 */

            /* Animation */
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
            /* Typography */
            --font-family-primary: 'Inter', 'Segoe UI', system-ui, sans-serif;
            --font-size-base: 1rem;
            --line-height-base: 1.6;
        }

        /* Dark Theme Variables */
        [data-theme="dark"] {
            --bg: #0F0F0F;
            --surface: #1A1A1A;
            --text: #FFFFFF;
            --muted: #A0A0A0;
            --glass: rgba(26,26,26,.58);
            --glass-strong: rgba(26,26,26,.72);
            --border: rgba(255,255,255,0.08);
        }

        /* Custom Utility Classes */
        .text-primary { color: var(--primary); }
        .text-secondary { color: var(--secondary); }
        .text-muted { color: var(--text-muted); }
        .text-dim { color: var(--text-dim); }
        .text-white { color: var(--text); }

        .bg-primary { background-color: var(--primary); }
        .bg-secondary { background-color: var(--secondary); }
        .bg-surface { background-color: var(--surface); }
        .bg-glass { background-color: var(--glass); }
        .bg-dark { background-color: var(--bg); }

        .from-primary { --tw-gradient-from: var(--primary); }
        .to-secondary { --tw-gradient-to: var(--secondary); }
        .from-primary-light { --tw-gradient-from: var(--primary-light); }
        .to-secondary-light { --tw-gradient-to: var(--secondary-light); }

        .border-glass { border-color: var(--glass-border); }
        .border-glass-strong { border-color: var(--glass-border-strong); }
        .ring-primary { --tw-ring-color: var(--primary); }

        /* Glassmorphism Components */
        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow);
            border-radius: var(--radius);
        }

        .glass-card-strong {
            background: var(--glass-strong);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border-strong);
            box-shadow: var(--shadow-lg);
            border-radius: var(--radius-lg);
        }

        .card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--glass-border);
            transition: var(--transition);
        }

        .feature-card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--glass-border);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gradient-primary);
            opacity: 0;
            transition: var(--transition);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            outline: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: var(--text);
            border: 2px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }

        .btn-outline:hover {
            background: var(--gold);
            color: white;
            transform: translateY(-2px);
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.125rem;
        }

        /* Form Styles */
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            background: var(--surface);
            color: var(--text);
            transition: var(--transition);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Animations */
        .animate-in {
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .theme-toggle {
                top: 15px;
                right: 15px;
                width: 40px;
                height: 40px;
            }
        }

        /* Form Styles */
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface);
            color: var(--text);
            transition: all var(--speed) ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px var(--ring);
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-slate-900 text-white font-sans antialiased">
    <!-- Floating Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-20 left-10 w-72 h-72 bg-gradient-to-r from-purple-500/10 to-pink-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-gradient-to-r from-blue-500/10 to-cyan-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-gradient-to-r from-emerald-500/10 to-teal-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Navigation -->
    <nav class="backdrop-blur-xl bg-white/5 border-b border-white/10 fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="h-10 w-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-heartbeat text-white text-xl"></i>
                        </a>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-xl font-bold text-white">MediBook</h1>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <!-- Public Navigation -->
                        @guest
                        <a href="{{ route('home') }}" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">Home</a>
                        <a href="{{ route('services') }}" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">Services</a>
                        <a href="{{ route('about') }}" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">About</a>
                        <a href="{{ route('contact') }}" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">Contact</a>
                        @endguest

                        <!-- Authenticated User Navigation -->
                        @auth
                            @if(auth()->user()->role === 'admin')
                                <!-- Admin Navigation -->
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('admin.dashboard') }}" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                        <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                                    </a>
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                            <i class="fas fa-users mr-1"></i>Users
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-48 backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg py-1 z-50">
                                            <a href="{{ route('admin.patients.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-user-injured mr-2"></i>Patients
                                            </a>
                                            <a href="{{ route('admin.doctors.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-user-md mr-2"></i>Doctors
                                            </a>
                                            <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-users-cog mr-2"></i>All Users
                                            </a>
                                        </div>
                                    </div>
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                            <i class="fas fa-calendar-alt mr-1"></i>Appointments
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-48 backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg py-1 z-50">
                                            <a href="{{ route('admin.appointments.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-list mr-2"></i>All Appointments
                                            </a>
                                            <a href="{{ route('admin.appointments.create') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-plus mr-2"></i>Create Appointment
                                            </a>
                                        </div>
                                    </div>
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                            <i class="fas fa-chart-bar mr-1"></i>Reports
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-48 backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg py-1 z-50">
                                            <a href="{{ route('admin.appointments.stats') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-chart-line mr-2"></i>Appointments Stats
                                            </a>
                                            <a href="{{ route('admin.payments.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-credit-card mr-2"></i>Payments
                                            </a>
                                            <a href="{{ route('admin.payments.stats') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-dollar-sign mr-2"></i>Revenue Stats
                                            </a>
                                        </div>
                                    </div>
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                            <i class="fas fa-cog mr-1"></i>Settings
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-48 backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg py-1 z-50">
                                            <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-cogs mr-2"></i>General
                                            </a>
                                            <a href="{{ route('admin.settings.specialties') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-stethoscope mr-2"></i>Specialties
                                            </a>
                                            <a href="{{ route('admin.settings.system') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-server mr-2"></i>System
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            @elseif(auth()->user()->role === 'doctor')
                                <!-- Doctor Navigation -->
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('doctor.dashboard') }}" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                        <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                                    </a>
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                            <i class="fas fa-calendar-alt mr-1"></i>Appointments
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-48 backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg py-1 z-50">
                                            <a href="{{ route('doctor.appointments.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-list mr-2"></i>All Appointments
                                            </a>
                                            <a href="{{ route('doctor.appointments.today') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-calendar-day mr-2"></i>Today's Schedule
                                            </a>
                                            <a href="{{ route('doctor.appointments.upcoming') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-calendar-week mr-2"></i>Upcoming
                                            </a>
                                        </div>
                                    </div>
                                    <a href="{{ route('doctor.patients.index') }}" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                        <i class="fas fa-user-injured mr-1"></i>Patients
                                    </a>
                                    <a href="{{ route('doctor.schedule.index') }}" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                        <i class="fas fa-clock mr-1"></i>Schedule
                                    </a>
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                            <i class="fas fa-chart-bar mr-1"></i>Reports
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-48 backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg py-1 z-50">
                                            <a href="{{ route('doctor.reports.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-chart-line mr-2"></i>Overview
                                            </a>
                                            <a href="{{ route('doctor.reviews.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-star mr-2"></i>Reviews
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            @elseif(auth()->user()->role === 'patient')
                                <!-- Patient Navigation -->
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('patient.dashboard') }}" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                        <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                                    </a>
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                            <i class="fas fa-calendar-alt mr-1"></i>Appointments
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-48 backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg py-1 z-50">
                                            <a href="{{ route('patient.appointments.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-list mr-2"></i>All Appointments
                                            </a>
                                            <a href="{{ route('patient.appointments.create') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-plus mr-2"></i>Book Appointment
                                            </a>
                                            <a href="{{ route('patient.appointments.upcoming') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-calendar-week mr-2"></i>Upcoming
                                            </a>
                                            <a href="{{ route('patient.appointments.past') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-history mr-2"></i>Past
                                            </a>
                                        </div>
                                    </div>
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                            <i class="fas fa-user-md mr-1"></i>Doctors
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-48 backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg py-1 z-50">
                                            <a href="{{ route('patient.doctors.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-search mr-2"></i>Find Doctors
                                            </a>
                                            <a href="{{ route('patient.doctors.top-rated') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-star mr-2"></i>Top Rated
                                            </a>
                                            <a href="{{ route('patient.doctors.favorites') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-heart mr-2"></i>Favorites
                                            </a>
                                        </div>
                                    </div>
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                            <i class="fas fa-wallet mr-1"></i>Wallet
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-48 backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg py-1 z-50">
                                            <a href="{{ route('patient.wallet.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-home mr-2"></i>Overview
                                            </a>
                                            <a href="{{ route('patient.wallet.transactions') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-exchange-alt mr-2"></i>Transactions
                                            </a>
                                            <a href="{{ route('patient.wallet.payment-history') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-history mr-2"></i>Payment History
                                            </a>
                                            <a href="{{ route('patient.wallet.payment-methods') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                                <i class="fas fa-credit-card mr-2"></i>Payment Methods
                                            </a>
                                        </div>
                                    </div>
                                    <a href="{{ route('ai.assistant') }}" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                        <i class="fas fa-robot mr-1"></i>AI Assistant
                                    </a>
                                </div>
                            @endif

                            <!-- Notifications Dropdown -->
                            <div class="relative ml-4" x-data="{ open: false }" id="notificationsDropdown">
                                <button @click="open = !open" class="flex items-center text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors relative">
                                    <i class="fas fa-bell text-xl"></i>
                                    <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg py-1 z-50 max-h-96 overflow-y-auto">
                                    <div class="px-4 py-2 border-b border-white/10">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium text-white">Notifications</h3>
                                            <a href="{{ route('notifications.index') }}" class="text-xs text-blue-400 hover:text-blue-300">View All</a>
                                        </div>
                                    </div>
                                    <div id="notificationsList" class="divide-y divide-white/10">
                                        <!-- Notifications will be loaded here -->
                                    </div>
                                    <div class="px-4 py-2 border-t border-white/10">
                                        <a href="{{ route('notifications.settings') }}" class="text-xs text-gray-400 hover:text-white block text-center">
                                            <i class="fas fa-cog mr-1"></i>Notification Settings
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- User Profile Dropdown -->
                            <div class="relative ml-4" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <span>{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down ml-1"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 backdrop-blur-xl bg-white/10 border border-white/20 rounded-xl shadow-lg py-1 z-50">
                                    @if(auth()->user()->role === 'admin')
                                                                                    <a href="{{ route('admin.profile.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                            <i class="fas fa-user-cog mr-2"></i>Profile
                                        </a>
                                    @elseif(auth()->user()->role === 'doctor')
                                        <a href="{{ route('doctor.profile.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                            <i class="fas fa-user-md mr-2"></i>Profile
                                        </a>
                                    @elseif(auth()->user()->role === 'patient')
                                        <a href="{{ route('patient.profile.index') }}" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                            <i class="fas fa-user-injured mr-2"></i>Profile
                                        </a>
                                    @endif
                                    <div class="border-t border-white/10 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- Guest Navigation -->
                            <a href="{{ route('login') }}" class="backdrop-blur-xl bg-white/10 border border-white/20 text-white px-4 py-2 rounded-xl text-sm font-medium transition-all hover:bg-white/20">Login</a>
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-xl text-sm font-medium transition-all hover:scale-105">Sign Up</a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-white/80 hover:text-white" id="mobile-menu-button">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 backdrop-blur-xl bg-white/5 border-t border-white/10">
                @guest
                <a href="{{ route('home') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Home</a>
                <a href="{{ route('services') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Services</a>
                <a href="{{ route('about') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">About</a>
                <a href="{{ route('contact') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Contact</a>
                @endguest

                @auth
                    @if(auth()->user()->role === 'admin')
                        <!-- Admin Mobile Menu -->
                        <a href="{{ route('admin.dashboard') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <div class="border-t border-white/10 my-2"></div>
                        <p class="px-3 py-2 text-xs text-white/60 uppercase tracking-wider">Users</p>
                        <a href="{{ route('admin.patients.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-user-injured mr-2"></i>Patients
                        </a>
                        <a href="{{ route('admin.doctors.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-user-md mr-2"></i>Doctors
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-users-cog mr-2"></i>All Users
                        </a>
                        <div class="border-t border-white/10 my-2"></div>
                        <p class="px-3 py-2 text-xs text-white/60 uppercase tracking-wider">Appointments</p>
                        <a href="{{ route('admin.appointments.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-list mr-2"></i>All Appointments
                        </a>
                        <a href="{{ route('admin.appointments.create') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-plus mr-2"></i>Create Appointment
                        </a>
                        <div class="border-t border-white/10 my-2"></div>
                        <p class="px-3 py-2 text-xs text-white/60 uppercase tracking-wider">Reports</p>
                        <a href="{{ route('admin.appointments.stats') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-chart-line mr-2"></i>Appointments Stats
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-credit-card mr-2"></i>Payments
                        </a>
                        <a href="{{ route('admin.payments.stats') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-dollar-sign mr-2"></i>Revenue Stats
                        </a>
                        <div class="border-t border-white/10 my-2"></div>
                        <p class="px-3 py-2 text-xs text-white/60 uppercase tracking-wider">Settings</p>
                        <a href="{{ route('admin.settings.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-cogs mr-2"></i>General
                        </a>
                        <a href="{{ route('admin.settings.specialties') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-stethoscope mr-2"></i>Specialties
                        </a>
                        <a href="{{ route('admin.settings.system') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-server mr-2"></i>System
                        </a>

                    @elseif(auth()->user()->role === 'doctor')
                        <!-- Doctor Mobile Menu -->
                        <a href="{{ route('doctor.dashboard') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <div class="border-t border-white/10 my-2"></div>
                        <p class="px-3 py-2 text-xs text-white/60 uppercase tracking-wider">Appointments</p>
                        <a href="{{ route('doctor.appointments.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-list mr-2"></i>All Appointments
                        </a>
                        <a href="{{ route('doctor.appointments.today') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-calendar-day mr-2"></i>Today's Schedule
                        </a>
                        <a href="{{ route('doctor.appointments.upcoming') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-calendar-week mr-2"></i>Upcoming
                        </a>
                        <div class="border-t border-white/10 my-2"></div>
                        <a href="{{ route('doctor.patients.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-user-injured mr-2"></i>Patients
                        </a>
                        <a href="{{ route('doctor.schedule.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-clock mr-2"></i>Schedule
                        </a>
                        <div class="border-t border-white/10 my-2"></div>
                        <p class="px-3 py-2 text-xs text-white/60 uppercase tracking-wider">Reports</p>
                        <a href="{{ route('doctor.reports.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-chart-line mr-2"></i>Overview
                        </a>
                        <a href="{{ route('doctor.reviews.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-star mr-2"></i>Reviews
                        </a>

                    @elseif(auth()->user()->role === 'patient')
                        <!-- Patient Mobile Menu -->
                        <a href="{{ route('patient.dashboard') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <div class="border-t border-white/10 my-2"></div>
                        <p class="px-3 py-2 text-xs text-white/60 uppercase tracking-wider">Appointments</p>
                        <a href="{{ route('patient.appointments.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-list mr-2"></i>All Appointments
                        </a>
                        <a href="{{ route('patient.appointments.create') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-plus mr-2"></i>Book Appointment
                        </a>
                        <a href="{{ route('patient.appointments.upcoming') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-calendar-week mr-2"></i>Upcoming
                        </a>
                        <a href="{{ route('patient.appointments.past') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-history mr-2"></i>Past
                        </a>
                        <div class="border-t border-white/10 my-2"></div>
                        <p class="px-3 py-2 text-xs text-white/60 uppercase tracking-wider">Doctors</p>
                        <a href="{{ route('patient.doctors.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-search mr-2"></i>Find Doctors
                        </a>
                        <a href="{{ route('patient.doctors.top-rated') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-star mr-2"></i>Top Rated
                        </a>
                        <a href="{{ route('patient.doctors.favorites') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-heart mr-2"></i>Favorites
                        </a>
                        <div class="border-t border-white/10 my-2"></div>
                        <p class="px-3 py-2 text-xs text-white/60 uppercase tracking-wider">Wallet</p>
                        <a href="{{ route('patient.wallet.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-home mr-2"></i>Overview
                        </a>
                        <a href="{{ route('patient.wallet.transactions') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-exchange-alt mr-2"></i>Transactions
                        </a>
                        <a href="{{ route('patient.wallet.payment-history') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-history mr-2"></i>Payment History
                        </a>
                        <a href="{{ route('patient.wallet.payment-methods') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-credit-card mr-2"></i>Payment Methods
                        </a>
                        <div class="border-t border-white/10 my-2"></div>
                        <a href="{{ route('ai.assistant') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-robot mr-2"></i>AI Assistant
                        </a>
                    @endif

                    <div class="border-t border-white/10 my-2"></div>
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.profile.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-user-cog mr-2"></i>Profile
                        </a>
                    @elseif(auth()->user()->role === 'doctor')
                        <a href="{{ route('doctor.profile.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-user-md mr-2"></i>Profile
                        </a>
                    @elseif(auth()->user()->role === 'patient')
                        <a href="{{ route('patient.profile.index') }}" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-user-injured mr-2"></i>Profile
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-white/80 hover:text-white block w-full text-left px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="backdrop-blur-xl bg-white/10 border border-white/20 text-white px-4 py-2 rounded-xl text-sm font-medium block text-center">Login</a>
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-xl text-sm font-medium block text-center mt-2">Sign Up</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>



    <!-- AOS Animation Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Main JavaScript -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true
        });

        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('nav');
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(255, 255, 255, 0.1)';
                navbar.style.backdropFilter = 'blur(20px)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.05)';
                navbar.style.backdropFilter = 'blur(20px)';
            }
        });

        // Notification System
        window.showNotification = function(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;

            const colors = {
                success: 'bg-green-500 text-white',
                error: 'bg-red-500 text-white',
                warning: 'bg-yellow-500 text-white',
                info: 'bg-blue-500 text-white'
            };

            notification.className += ` ${colors[type]}`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : type === 'warning' ? 'exclamation-triangle' : 'info'} mr-2"></i>
                    <span>${message}</span>
                    <button class="ml-4 hover:opacity-75" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        };

        // Load Notifications
        function loadNotifications() {
            if (document.getElementById('notificationsDropdown')) {
                fetch('/notifications/recent')
                    .then(response => response.json())
                    .then(data => {
                        const notificationsList = document.getElementById('notificationsList');
                        const badge = document.getElementById('notificationBadge');

                        if (data.notifications && data.notifications.length > 0) {
                            notificationsList.innerHTML = data.notifications.map(notification => `
                                <div class="px-4 py-3 hover:bg-white/5 transition-colors ${notification.is_unread ? 'bg-blue-500/10' : ''}">
                                    <div class="flex items-start space-x-3">
                                        <div class="text-lg">${notification.icon}</div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-white">${notification.title}</p>
                                            <p class="text-xs text-gray-300 mt-1">${notification.message}</p>
                                            <p class="text-xs text-gray-400 mt-1">${notification.time_ago}</p>
                                        </div>
                                        ${notification.is_unread ? '<div class="w-2 h-2 bg-blue-500 rounded-full"></div>' : ''}
                                    </div>
                                </div>
                            `).join('');

                            // Update badge
                            const unreadCount = data.notifications.filter(n => n.is_unread).length;
                            if (unreadCount > 0) {
                                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                                badge.classList.remove('hidden');
                            } else {
                                badge.classList.add('hidden');
                            }
                        } else {
                            notificationsList.innerHTML = `
                                <div class="px-4 py-8 text-center">
                                    <div class="text-4xl mb-2">🔔</div>
                                    <p class="text-sm text-gray-400">No notifications</p>
                                </div>
                            `;
                            badge.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                    });
            }
        }

        // Load notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifications();

            // Refresh notifications every 30 seconds
            setInterval(loadNotifications, 30000);
        });

        // AI Assistant Modal
        window.showAIModal = function() {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 glass-card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-text">AI Health Assistant</h3>
                        <button class="text-gray-500 hover:text-gray-700" onclick="this.closest('.fixed').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center">
                                <i class="fas fa-robot text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-text">Hello! I'm your AI health assistant. How can I help you today?</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <button class="w-full text-left p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-search mr-2 text-gold"></i>
                                Check my symptoms
                            </button>
                            <button class="w-full text-left p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-lightbulb mr-2 text-gold"></i>
                                Get health tips
                            </button>
                            <button class="w-full text-left p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-pills mr-2 text-gold"></i>
                                Medication reminder
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            // Close modal when clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        };
    </script>

    @stack('scripts')
</body>
</html>
