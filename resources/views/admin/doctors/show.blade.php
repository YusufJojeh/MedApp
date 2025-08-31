@extends('layouts.app')

@section('title', 'Doctor Details - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Doctor Details</h1>
                    <p class="text-gray-600 dark:text-gray-400">View complete information about this doctor</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Doctors
                    </a>
                    <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Doctor
                    </a>
                    @if($doctor->status == 'active')
                        <button class="btn btn-warning" onclick="deactivateDoctor({{ $doctor->id }})">
                            <i class="fas fa-pause mr-2"></i>
                            Deactivate
                        </button>
                    @else
                        <button class="btn btn-success" onclick="activateDoctor({{ $doctor->id }})">
                            <i class="fas fa-play mr-2"></i>
                            Activate
                        </button>
                    @endif
                    <button class="btn btn-danger" onclick="deleteDoctor({{ $doctor->id }})">
                        <i class="fas fa-trash mr-2"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user-md text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Dr. {{ $doctor->name }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">{{ $doctor->specialty_name }}</p>
                            <div class="flex items-center mt-2">
                                <span class="px-3 py-1 text-sm rounded-full {{ $doctor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($doctor->status) }}
                                </span>
                                @if($doctor->is_verified)
                                    <span class="ml-2 px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                                        Verified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $doctor->email }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $doctor->phone }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">Joined {{ \Carbon\Carbon::parse($doctor->created_at)->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Professional Information</h3>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-stethoscope text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $doctor->specialty_name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $doctor->experience_years }} years experience</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-dollar-sign text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700 dark:text-gray-300">${{ number_format($doctor->consultation_fee, 2) }} consultation fee</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Professional Details</h3>

                    @if($doctor->education)
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Education</h4>
                        <p class="text-gray-700 dark:text-gray-300">{{ $doctor->education }}</p>
                    </div>
                    @endif

                    @if($doctor->languages)
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Languages</h4>
                        <p class="text-gray-700 dark:text-gray-300">{{ $doctor->languages }}</p>
                    </div>
                    @endif

                    @if($doctor->description)
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Bio</h4>
                        <p class="text-gray-700 dark:text-gray-300">{{ $doctor->description }}</p>
                    </div>
                    @endif
                </div>

                <!-- Statistics Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-lg {{ $i <= $doctor->rating ? 'text-gold' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($doctor->rating, 1) }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Average Rating</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $doctor->total_reviews ?? 0 }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Reviews</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($doctor->consultation_fee, 0) }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Consultation Fee</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        @if($doctor->status == 'active')
                            <button class="w-full btn btn-warning" onclick="deactivateDoctor({{ $doctor->id }})">
                                <i class="fas fa-pause mr-2"></i>
                                Deactivate Account
                            </button>
                        @else
                            <button class="w-full btn btn-success" onclick="activateDoctor({{ $doctor->id }})">
                                <i class="fas fa-play mr-2"></i>
                                Activate Account
                            </button>
                        @endif

                        <button class="w-full btn btn-outline" onclick="viewAppointments({{ $doctor->id }})">
                            <i class="fas fa-calendar-check mr-2"></i>
                            View Appointments
                        </button>

                        <button class="w-full btn btn-outline" onclick="viewReviews({{ $doctor->id }})">
                            <i class="fas fa-star mr-2"></i>
                            View Reviews
                        </button>

                        <button class="w-full btn btn-danger" onclick="deleteDoctor({{ $doctor->id }})">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Doctor
                        </button>
                    </div>
                </div>

                <!-- Account Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Account Status:</span>
                            <span class="font-medium {{ $doctor->status === 'active' ? 'text-green-600' : 'text-red-600' }}">
                                {{ ucfirst($doctor->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Verification:</span>
                            <span class="font-medium {{ $doctor->is_verified ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $doctor->is_verified ? 'Verified' : 'Unverified' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Member Since:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($doctor->created_at)->format('M d, Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Last Updated:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($doctor->updated_at)->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" id="confirmTitle">Confirm Action</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6" id="confirmMessage">Are you sure you want to perform this action?</p>
            <div class="flex justify-end space-x-3">
                <button class="btn btn-outline" onclick="closeConfirmModal()">Cancel</button>
                <button class="btn btn-danger" id="confirmAction">Confirm</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Activate doctor
    function activateDoctor(doctorId) {
        showConfirmModal(
            'Activate Doctor',
            'Are you sure you want to activate this doctor?',
            () => performAction(`/admin/doctors/${doctorId}/activate`, 'Doctor activated successfully')
        );
    }

    // Deactivate doctor
    function deactivateDoctor(doctorId) {
        showConfirmModal(
            'Deactivate Doctor',
            'Are you sure you want to deactivate this doctor?',
            () => performAction(`/admin/doctors/${doctorId}/deactivate`, 'Doctor deactivated successfully')
        );
    }

    // Delete doctor
    function deleteDoctor(doctorId) {
        showConfirmModal(
            'Delete Doctor',
            'Are you sure you want to delete this doctor? This action cannot be undone.',
            () => performAction(`/admin/doctors/${doctorId}`, 'Doctor deleted successfully', 'DELETE')
        );
    }

    // View appointments
    function viewAppointments(doctorId) {
        // Redirect to appointments page filtered by doctor
        window.location.href = `/admin/appointments?doctor=${doctorId}`;
    }

    // View reviews
    function viewReviews(doctorId) {
        // Redirect to reviews page filtered by doctor
        window.location.href = `/admin/reviews?doctor=${doctorId}`;
    }

    // Show confirmation modal
    function showConfirmModal(title, message, onConfirm) {
        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmAction').onclick = () => {
            closeConfirmModal();
            onConfirm();
        };
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    // Close confirmation modal
    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    // Perform action
    function performAction(url, successMessage, method = 'POST') {
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(successMessage, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message || 'Error performing action', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error performing action', 'error');
        });
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Simple notification - you can enhance this with a proper notification system
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(`${type.toUpperCase()}: ${message}`);
    }
</script>
@endpush
