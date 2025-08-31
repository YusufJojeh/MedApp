@extends('layouts.app')

@section('title', 'Create User - Admin Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Create New User</h1>
                    <p class="text-muted mt-2">Add a new user to the system</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card feature-card">
            <div class="p-6">
                <form id="userForm" method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                    @csrf

                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-text mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-muted mb-2">First Name *</label>
                                <input type="text" id="first_name" name="first_name" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter first name">
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-muted mb-2">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter last name">
                            </div>

                            <div>
                                <label for="username" class="block text-sm font-medium text-muted mb-2">Username *</label>
                                <input type="text" id="username" name="username" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter username">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-muted mb-2">Email *</label>
                                <input type="email" id="email" name="email" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter email address">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-muted mb-2">Phone</label>
                                <input type="tel" id="phone" name="phone"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter phone number">
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-muted mb-2">Password *</label>
                                <input type="password" id="password" name="password" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter password">
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-muted mb-2">Confirm Password *</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Confirm password">
                            </div>

                            <div>
                                <label for="role" class="block text-sm font-medium text-muted mb-2">Role *</label>
                                <select id="role" name="role" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text">
                                    <option value="">Select role</option>
                                    <option value="admin">Admin</option>
                                    <option value="doctor">Doctor</option>
                                    <option value="patient">Patient</option>
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-muted mb-2">Status *</label>
                                <select id="status" name="status" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Doctor Information (Conditional) -->
                    <div id="doctorFields" class="hidden">
                        <h3 class="text-lg font-semibold text-text mb-4">Doctor Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="specialty_id" class="block text-sm font-medium text-muted mb-2">Specialty *</label>
                                <select id="specialty_id" name="specialty_id"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text">
                                    <option value="">Select specialty</option>
                                    @foreach($specialties as $specialty)
                                        <option value="{{ $specialty->id }}">{{ $specialty->name_en }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="consultation_fee" class="block text-sm font-medium text-muted mb-2">Consultation Fee</label>
                                <input type="number" id="consultation_fee" name="consultation_fee" step="0.01" min="0"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter consultation fee">
                            </div>

                            <div>
                                <label for="experience_years" class="block text-sm font-medium text-muted mb-2">Experience Years</label>
                                <input type="number" id="experience_years" name="experience_years" min="0"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter years of experience">
                            </div>

                            <div>
                                <label for="languages" class="block text-sm font-medium text-muted mb-2">Languages</label>
                                <input type="text" id="languages" name="languages"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="e.g., English, Arabic, French">
                            </div>

                            <div class="md:col-span-2">
                                <label for="education" class="block text-sm font-medium text-muted mb-2">Education</label>
                                <textarea id="education" name="education" rows="3"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter education details"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-muted mb-2">Description</label>
                                <textarea id="description" name="description" rows="3"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter professional description"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Information (Conditional) -->
                    <div id="patientFields" class="hidden">
                        <h3 class="text-lg font-semibold text-text mb-4">Patient Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-muted mb-2">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text">
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium text-muted mb-2">Gender</label>
                                <select id="gender" name="gender"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text">
                                    <option value="">Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label for="blood_type" class="block text-sm font-medium text-muted mb-2">Blood Type</label>
                                <select id="blood_type" name="blood_type"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text">
                                    <option value="">Select blood type</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-muted mb-2">Address</label>
                                <textarea id="address" name="address" rows="2"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter address"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label for="emergency_contact" class="block text-sm font-medium text-muted mb-2">Emergency Contact</label>
                                <input type="text" id="emergency_contact" name="emergency_contact"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter emergency contact information">
                            </div>

                            <div class="md:col-span-2">
                                <label for="medical_history" class="block text-sm font-medium text-muted mb-2">Medical History</label>
                                <textarea id="medical_history" name="medical_history" rows="3"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-gold focus:border-transparent bg-white text-text"
                                    placeholder="Enter medical history"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide role-specific fields based on selected role
    document.getElementById('role').addEventListener('change', function() {
        const role = this.value;
        const doctorFields = document.getElementById('doctorFields');
        const patientFields = document.getElementById('patientFields');

        // Hide all role-specific fields
        doctorFields.classList.add('hidden');
        patientFields.classList.add('hidden');

        // Show relevant fields based on role
        if (role === 'doctor') {
            doctorFields.classList.remove('hidden');
        } else if (role === 'patient') {
            patientFields.classList.remove('hidden');
        }
    });

    // Form validation
    document.getElementById('userForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;

        if (password !== passwordConfirmation) {
            e.preventDefault();
            alert('Password confirmation does not match');
            return false;
        }

        const role = document.getElementById('role').value;
        if (role === 'doctor') {
            const specialtyId = document.getElementById('specialty_id').value;
            if (!specialtyId) {
                e.preventDefault();
                alert('Please select a specialty for the doctor');
                return false;
            }
        }
    });
</script>
@endpush
@endsection
