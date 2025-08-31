@extends('layouts.app')

@section('title', 'Edit Patient - Admin Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Edit Patient</h1>
                    <p class="text-muted mt-2">Update patient information</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.patients.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Patients
                    </a>
                    <a href="{{ route('admin.patients.show', $patient->id) }}" class="btn btn-outline">
                        <i class="fas fa-eye mr-2"></i>
                        View Patient
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card feature-card">
            <div class="p-6">
                <form id="editPatientForm" method="POST" action="{{ route('admin.patients.update', $patient->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Personal Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-text mb-4">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-muted mb-2">First Name *</label>
                                <input type="text" id="first_name" name="first_name" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('first_name', $patient->first_name) }}">
                                @error('first_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-muted mb-2">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('last_name', $patient->last_name) }}">
                                @error('last_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="username" class="block text-sm font-medium text-muted mb-2">Username *</label>
                                <input type="text" id="username" name="username" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('username', $patient->username) }}">
                                @error('username')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-muted mb-2">Email *</label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('email', $patient->email) }}">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-muted mb-2">Phone</label>
                                <input type="tel" id="phone" name="phone"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('phone', $patient->phone) }}">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-muted mb-2">Status *</label>
                                <select id="status" name="status" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                    <option value="">Select Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ old('status', $patient->status) == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-text mb-4">Medical Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-muted mb-2">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('date_of_birth', $patient->date_of_birth) }}">
                                @error('date_of_birth')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium text-muted mb-2">Gender</label>
                                <select id="gender" name="gender"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                    <option value="">Select Gender</option>
                                    @foreach($genders as $gender)
                                        <option value="{{ $gender }}" {{ old('gender', $patient->gender) == $gender ? 'selected' : '' }}>
                                            {{ ucfirst($gender) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gender')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="blood_type" class="block text-sm font-medium text-muted mb-2">Blood Type</label>
                                <select id="blood_type" name="blood_type"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                    <option value="">Select Blood Type</option>
                                    @foreach($bloodTypes as $bloodType)
                                        <option value="{{ $bloodType }}" {{ old('blood_type', $patient->blood_type) == $bloodType ? 'selected' : '' }}>
                                            {{ $bloodType }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('blood_type')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-text mb-4">Additional Information</h3>
                        <div class="space-y-6">
                            <div>
                                <label for="address" class="block text-sm font-medium text-muted mb-2">Address</label>
                                <textarea id="address" name="address" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                          placeholder="Enter patient's address">{{ old('address', $patient->address) }}</textarea>
                                @error('address')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="medical_history" class="block text-sm font-medium text-muted mb-2">Medical History</label>
                                <textarea id="medical_history" name="medical_history" rows="4"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                          placeholder="Enter patient's medical history">{{ old('medical_history', $patient->medical_history) }}</textarea>
                                @error('medical_history')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="emergency_contact" class="block text-sm font-medium text-muted mb-2">Emergency Contact</label>
                                <textarea id="emergency_contact" name="emergency_contact" rows="2"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                          placeholder="Enter emergency contact information">{{ old('emergency_contact', $patient->emergency_contact) }}</textarea>
                                @error('emergency_contact')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Account Security -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-text mb-4">Account Security</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-muted mb-2">New Password (leave blank to keep current)</label>
                                <input type="password" id="password" name="password"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-muted mb-2">Confirm New Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.patients.show', $patient->id) }}" class="btn btn-outline">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Update Patient
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-gold"></div>
                <span class="text-gray-900 dark:text-white">Updating patient...</span>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('editPatientForm').addEventListener('submit', function(e) {
    e.preventDefault();

    document.getElementById('loadingModal').classList.remove('hidden');

    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loadingModal').classList.add('hidden');

        if (data.success) {
            alert('Patient updated successfully!');
            setTimeout(() => {
                window.location.href = '{{ route("admin.patients.show", $patient->id) }}';
            }, 1500);
        } else {
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.classList.add('border-red-500');
                        const errorDiv = document.createElement('p');
                        errorDiv.className = 'text-red-500 text-sm mt-1';
                        errorDiv.textContent = data.errors[field][0];
                        input.parentNode.appendChild(errorDiv);
                    }
                });
                alert('Please fix the validation errors.');
            } else {
                alert(data.message || 'Error updating patient');
            }
        }
    })
    .catch(error => {
        document.getElementById('loadingModal').classList.add('hidden');
        console.error('Error:', error);
        alert('Error updating patient');
    });
});

// Remove error styling when user starts typing
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('input', function() {
        this.classList.remove('border-red-500');
        const errorDiv = this.parentNode.querySelector('.text-red-500');
        if (errorDiv) {
            errorDiv.remove();
        }
    });
});
</script>
@endpush
