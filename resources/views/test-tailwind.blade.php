<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind CSS Test - Medical Booking</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">🏥 Medical Booking System</h1>
                <p class="text-lg text-gray-600">Tailwind CSS Test Page</p>
            </div>

            <!-- Test Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Primary Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">Primary Colors</h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-2">
                            <div class="h-8 bg-primary-500 rounded"></div>
                            <div class="h-8 bg-primary-600 rounded"></div>
                            <div class="h-8 bg-primary-700 rounded"></div>
                        </div>
                    </div>
                </div>

                <!-- Medical Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">Medical Colors</h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-2">
                            <div class="h-8 bg-medical-500 rounded"></div>
                            <div class="h-8 bg-medical-600 rounded"></div>
                            <div class="h-8 bg-medical-700 rounded"></div>
                        </div>
                    </div>
                </div>

                <!-- Status Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">Status Colors</h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-2">
                            <div class="h-8 bg-success-500 rounded"></div>
                            <div class="h-8 bg-warning-500 rounded"></div>
                            <div class="h-8 bg-danger-500 rounded"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons Test -->
            <div class="card mb-8">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Button Styles</h3>
                </div>
                <div class="card-body">
                    <div class="flex flex-wrap gap-4">
                        <button class="btn btn-primary">Primary Button</button>
                        <button class="btn btn-secondary">Secondary Button</button>
                        <button class="btn btn-success">Success Button</button>
                        <button class="btn btn-warning">Warning Button</button>
                        <button class="btn btn-danger">Danger Button</button>
                        <button class="btn btn-outline">Outline Button</button>
                    </div>
                </div>
            </div>

            <!-- Badges Test -->
            <div class="card mb-8">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Status Badges</h3>
                </div>
                <div class="card-body">
                    <div class="flex flex-wrap gap-4">
                        <span class="badge badge-success">Success</span>
                        <span class="badge badge-warning">Warning</span>
                        <span class="badge badge-danger">Danger</span>
                        <span class="badge badge-info">Info</span>
                    </div>
                </div>
            </div>

            <!-- Form Test -->
            <div class="card mb-8">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Form Elements</h3>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-input" placeholder="Enter your email">
                        </div>
                        <div>
                            <label class="form-label">Password</label>
                            <input type="password" class="form-input" placeholder="Enter your password">
                        </div>
                        <div>
                            <label class="form-label">Message</label>
                            <textarea class="form-input" rows="3" placeholder="Enter your message"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Test -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="stat-card">
                    <div class="stat-number">1,234</div>
                    <div class="stat-label">Total Patients</div>
                    <div class="stat-change positive">+12% from last month</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">567</div>
                    <div class="stat-label">Appointments</div>
                    <div class="stat-change positive">+8% from last month</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">89</div>
                    <div class="stat-label">Doctors</div>
                    <div class="stat-change negative">-2% from last month</div>
                </div>
            </div>

            <!-- Success Message -->
            <div class="bg-success-50 border border-success-200 rounded-lg p-6 text-center">
                <div class="text-success-800">
                    <h3 class="text-lg font-semibold mb-2">✅ Tailwind CSS is Working!</h3>
                    <p class="text-success-600">All custom styles and components are properly configured.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
