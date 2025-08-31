<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Medical Booking System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-primary-600">🏥 Medical Booking</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/test-tailwind" class="nav-link">Test Tailwind</a>
                    <a href="/dashboard" class="nav-link active">Dashboard</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="px-4 py-6 sm:px-0">
            <div class="border-4 border-dashed border-gray-200 rounded-lg p-8 text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">🎉 Database Setup Complete!</h2>
                <p class="text-lg text-gray-600 mb-6">Your medical booking system database has been successfully configured.</p>

                <!-- Success Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card">
                        <div class="stat-number">15</div>
                        <div class="stat-label">Tables Created</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">14</div>
                        <div class="stat-label">Users Created</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">10</div>
                        <div class="stat-label">Medical Specialties</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">6</div>
                        <div class="stat-label">Sample Appointments</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="/test-tailwind" class="btn btn-primary">
                        🎨 Test Tailwind CSS
                    </a>
                    <button class="btn btn-outline">
                        📊 View Database
                    </button>
                    <button class="btn btn-success">
                        🚀 Start Development
                    </button>
                </div>
            </div>
        </div>

        <!-- Database Summary -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Users Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">👥 Users Created</h3>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Admins</span>
                            <span class="badge badge-info">2</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Doctors</span>
                            <span class="badge badge-primary">6</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Patients</span>
                            <span class="badge badge-success">6</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Data Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">🏥 Medical Data</h3>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Specialties</span>
                            <span class="badge badge-info">10</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Appointments</span>
                            <span class="badge badge-warning">6</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Subscription Plans</span>
                            <span class="badge badge-primary">4</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="mt-8 card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">🔧 Next Steps</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-semibold text-blue-900 mb-2">1. Create Models</h4>
                        <p class="text-sm text-blue-700">Create Laravel Eloquent models for all database tables</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <h4 class="font-semibold text-green-900 mb-2">2. Build Controllers</h4>
                        <p class="text-sm text-green-700">Create API controllers for CRUD operations</p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <h4 class="font-semibold text-purple-900 mb-2">3. Authentication</h4>
                        <p class="text-sm text-purple-700">Implement user authentication and authorization</p>
                    </div>
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <h4 class="font-semibold text-yellow-900 mb-2">4. Frontend</h4>
                        <p class="text-sm text-yellow-700">Build the user interface with Tailwind CSS</p>
                    </div>
                    <div class="p-4 bg-red-50 rounded-lg">
                        <h4 class="font-semibold text-red-900 mb-2">5. Testing</h4>
                        <p class="text-sm text-red-700">Write tests for all functionality</p>
                    </div>
                    <div class="p-4 bg-indigo-50 rounded-lg">
                        <h4 class="font-semibold text-indigo-900 mb-2">6. Deployment</h4>
                        <p class="text-sm text-indigo-700">Deploy to production environment</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
