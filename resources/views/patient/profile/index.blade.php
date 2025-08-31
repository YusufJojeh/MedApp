@extends('layouts.app')

@section('title', 'My Profile - Patient Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">My Profile</h1>
                    <p class="text-muted mt-2">Manage your personal information and preferences</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Information -->
            <div class="lg:col-span-2">
                <div class="card feature-card" data-aos="fade-up">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Personal Information</h3>

                        <form id="profileForm" class="space-y-6">
                            @csrf

                            <!-- Profile Picture -->
                            <div class="flex items-center space-x-6">
                                <div class="w-24 h-24 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-2xl"></i>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline" onclick="uploadImage()">
                                        <i class="fas fa-camera mr-2"></i>
                                        Upload Photo
                                    </button>
                                    <p class="text-sm text-muted mt-2">JPG, PNG or GIF. Max size 2MB.</p>
                                </div>
                            </div>

                            <!-- Name -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">First Name</label>
                                    <input type="text" name="first_name" value="{{ $user->first_name ?? '' }}" required
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">Last Name</label>
                                    <input type="text" name="last_name" value="{{ $user->last_name ?? '' }}" required
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">Email</label>
                                    <input type="email" name="email" value="{{ $user->email ?? '' }}" required
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">Phone</label>
                                    <input type="tel" name="phone" value="{{ $user->phone ?? '' }}" required
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                </div>
                            </div>

                            <!-- Address -->
                            <div>
                                <label class="block text-sm font-medium text-text mb-2">Address</label>
                                <textarea name="address" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">{{ $user->address ?? '' }}</textarea>
                            </div>

                            <!-- Medical Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">Date of Birth</label>
                                    <input type="date" name="date_of_birth" value="{{ $patient->date_of_birth ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">Blood Type</label>
                                    <select name="blood_type" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                        <option value="">Select Blood Type</option>
                                        <option value="A+" {{ $patient->blood_type == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ $patient->blood_type == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ $patient->blood_type == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ $patient->blood_type == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="AB+" {{ $patient->blood_type == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ $patient->blood_type == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        <option value="O+" {{ $patient->blood_type == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ $patient->blood_type == 'O-' ? 'selected' : '' }}>O-</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Allergies -->
                            <div>
                                <label class="block text-sm font-medium text-text mb-2">Allergies</label>
                                <textarea name="allergies" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                          placeholder="List any allergies...">{{ $patient->allergies ?? '' }}</textarea>
                            </div>

                            <!-- Medical History -->
                            <div>
                                <label class="block text-sm font-medium text-text mb-2">Medical History</label>
                                <textarea name="medical_history" rows="4"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                          placeholder="Any relevant medical history...">{{ $patient->medical_history ?? '' }}</textarea>
                            </div>

                            <!-- Emergency Contact -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">Emergency Contact Name</label>
                                    <input type="text" name="emergency_contact_name" value="{{ $patient->emergency_contact_name ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">Emergency Contact Phone</label>
                                    <input type="tel" name="emergency_contact_phone" value="{{ $patient->emergency_contact_phone ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex space-x-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>
                                    Save Changes
                                </button>
                                <button type="button" class="btn btn-outline" onclick="resetForm()">
                                    <i class="fas fa-undo mr-2"></i>
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Profile Completion -->
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-4">Profile Completion</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-muted">Profile Completion</span>
                                    <span class="text-sm font-medium text-text">{{ $profileCompletion }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-gold h-2 rounded-full" style="width: {{ $profileCompletion }}%"></div>
                                </div>
                            </div>
                            <div class="text-sm text-muted">
                                Complete your profile to get better healthcare recommendations
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Security -->
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-4">Account Security</h3>
                        <div class="space-y-4">
                            <button class="btn btn-outline w-full" onclick="changePassword()">
                                <i class="fas fa-key mr-2"></i>
                                Change Password
                            </button>
                            <button class="btn btn-outline w-full" onclick="enableTwoFactor()">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Two-Factor Authentication
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-4">Quick Stats</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Total Appointments</span>
                                <span class="text-text font-medium">{{ $stats['total_appointments'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Doctors Visited</span>
                                <span class="text-text font-medium">{{ $stats['total_doctors'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Member Since</span>
                                <span class="text-text font-medium">{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-text">Change Password</h3>
                <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" onclick="closePasswordModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="passwordForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Current Password</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">New Password</label>
                        <input type="password" name="new_password" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Confirm New Password</label>
                        <input type="password" name="confirm_password" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                    </div>
                </div>
                <div class="flex space-x-3 mt-6">
                    <button type="button" class="btn btn-outline flex-1" onclick="closePasswordModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary flex-1">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Submit profile form
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("patient.profile.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Profile updated successfully', 'success');
            } else {
                showNotification(data.message || 'Error updating profile', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating profile', 'error');
        });
    });

    // Upload image
    function uploadImage() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('image', file);

                fetch('{{ route("patient.profile.image") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Profile image updated successfully', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showNotification(data.message || 'Error uploading image', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error uploading image', 'error');
                });
            }
        };
        input.click();
    }

    // Reset form
    function resetForm() {
        if (confirm('Are you sure you want to reset the form? All changes will be lost.')) {
            document.getElementById('profileForm').reset();
        }
    }

    // Change password
    function changePassword() {
        document.getElementById('passwordModal').classList.remove('hidden');
    }

    // Close password modal
    function closePasswordModal() {
        document.getElementById('passwordModal').classList.add('hidden');
        document.getElementById('passwordForm').reset();
    }

    // Submit password form
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const newPassword = formData.get('new_password');
        const confirmPassword = formData.get('confirm_password');

        if (newPassword !== confirmPassword) {
            showNotification('New passwords do not match', 'error');
            return;
        }

        fetch('{{ route("patient.profile.password") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Password changed successfully', 'success');
                closePasswordModal();
            } else {
                showNotification(data.message || 'Error changing password', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error changing password', 'error');
        });
    });

    // Enable two-factor authentication
    function enableTwoFactor() {
        showNotification('Two-factor authentication feature coming soon', 'info');
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Simple notification - you can enhance this with a proper notification system
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(`${type.toUpperCase()}: ${message}`);
    }
</script>
@endpush
