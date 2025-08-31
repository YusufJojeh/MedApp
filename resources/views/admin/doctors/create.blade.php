@extends('layouts.app')

@section('title', 'Create Doctor - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Doctor</h1>
                    <p class="text-gray-600 dark:text-gray-400">Add a new doctor to the system</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Doctors
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6">
                <form id="createDoctorForm" method="POST" action="{{ route('admin.doctors.store') }}">
                    @csrf

                    <!-- Personal Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                                <input type="text" id="first_name" name="first_name" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('first_name') }}">
                                @error('first_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('last_name') }}">
                                @error('last_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username *</label>
                                <input type="text" id="username" name="username" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('username') }}">
                                @error('username')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('email') }}">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                                <input type="tel" id="phone" name="phone"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('phone') }}">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                                <select id="status" name="status" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Professional Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="specialty_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specialty *</label>
                                <select id="specialty_id" name="specialty_id" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                    <option value="">Select Specialty</option>
                                    @foreach($specialties as $specialty)
                                        <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                            {{ $specialty->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('specialty_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="experience_years" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Experience (Years)</label>
                                <input type="number" id="experience_years" name="experience_years" min="0" max="50"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('experience_years') }}">
                                @error('experience_years')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="consultation_fee" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consultation Fee</label>
                                <input type="number" id="consultation_fee" name="consultation_fee" min="0" step="0.01"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('consultation_fee') }}">
                                @error('consultation_fee')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="education" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Education</label>
                                <input type="text" id="education" name="education"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('education') }}" placeholder="e.g., MBBS, MD, PhD">
                                @error('education')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="languages" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Languages</label>
                                <input type="text" id="languages" name="languages"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                       value="{{ old('languages') }}" placeholder="e.g., English, Arabic, French">
                                @error('languages')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio/Description</label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                      placeholder="Brief description about the doctor's expertise and experience">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Account Security -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Security</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password *</label>
                                <input type="password" id="password" name="password" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password *</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" name="role" value="doctor">

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Create Doctor
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
                <span class="text-gray-900 dark:text-white">Creating doctor...</span>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('createDoctorForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Show loading modal
    document.getElementById('loadingModal').classList.remove('hidden');

    // Submit form
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
            showNotification('Doctor created successfully!', 'success');
            setTimeout(() => {
                window.location.href = '{{ route("admin.doctors.index") }}';
            }, 1500);
        } else {
            if (data.errors) {
                // Display validation errors
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
                showNotification('Please fix the validation errors.', 'error');
            } else {
                showNotification(data.message || 'Error creating doctor', 'error');
            }
        }
    })
    .catch(error => {
        document.getElementById('loadingModal').classList.add('hidden');
        console.error('Error:', error);
        showNotification('Error creating doctor', 'error');
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

function showNotification(message, type = 'info') {
    // Simple notification - you can enhance this with a proper notification system
    console.log(`${type.toUpperCase()}: ${message}`);
    alert(`${type.toUpperCase()}: ${message}`);
}
</script>
@endpush
