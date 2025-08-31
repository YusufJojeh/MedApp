@extends('layouts.app')

@section('title', 'Doctor Profile')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Doctor Profile</h1>
                    <p class="text-muted mt-2">Manage your professional profile and settings</p>
                </div>
                <div class="flex space-x-4">
                    <button class="btn btn-outline" onclick="window.history.back()">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </button>
                    <button class="btn btn-primary" onclick="saveProfile()">
                        <i class="fas fa-save mr-2"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="card feature-card" data-aos="fade-up">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Basic Information</h3>

                        <!-- Profile Image Section -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-text mb-4">Profile Image</label>
                            <div class="flex items-center space-x-6">
                                <div class="relative">
                                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        @if($doctor->profile_image)
                                            <img id="profile-image-preview" src="{{ asset('storage/' . $doctor->profile_image) }}"
                                                 alt="Profile Image" class="w-full h-full object-cover">
                                        @else
                                            <div id="profile-image-preview" class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-user-md text-4xl text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" onclick="document.getElementById('profile-image-input').click()"
                                            class="absolute -bottom-2 -right-2 w-8 h-8 bg-gold rounded-full flex items-center justify-center text-white hover:bg-gold-deep transition-colors">
                                        <i class="fas fa-camera text-sm"></i>
                                    </button>
                                </div>
                                <div class="flex-1">
                                    <input type="file" id="profile-image-input" name="profile_image" accept="image/*"
                                           class="hidden" onchange="previewImage(this)">
                                    <div class="space-y-2">
                                        <p class="text-sm text-muted">Upload a professional photo (JPG, PNG, max 2MB)</p>
                                        <button type="button" onclick="document.getElementById('profile-image-input').click()"
                                                class="btn btn-outline btn-sm">
                                            <i class="fas fa-upload mr-2"></i>
                                            Choose Image
                                        </button>
                                        @if($doctor->profile_image)
                                            <button type="button" onclick="removeProfileImage()"
                                                    class="btn btn-outline btn-sm text-red-600 hover:text-red-700">
                                                <i class="fas fa-trash mr-2"></i>
                                                Remove Image
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-text mb-2">Full Name</label>
                                <input type="text" id="name" name="name" value="{{ $doctor->name ?? '' }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            </div>

                            <div>
                                <label for="specialty_id" class="block text-sm font-medium text-text mb-2">Specialty</label>
                                <select id="specialty_id" name="specialty_id"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                    <option value="">Select Specialty</option>
                                    @foreach($specialties as $specialty)
                                        <option value="{{ $specialty->id }}" {{ ($doctor->specialty_id ?? '') == $specialty->id ? 'selected' : '' }}>
                                            {{ $specialty->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="experience_years" class="block text-sm font-medium text-text mb-2">Years of Experience</label>
                                <input type="number" id="experience_years" name="experience_years" value="{{ $doctor->experience_years ?? '' }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            </div>

                            <div>
                                <label for="consultation_fee" class="block text-sm font-medium text-text mb-2">Consultation Fee ($)</label>
                                <input type="number" step="0.01" id="consultation_fee" name="consultation_fee" value="{{ $doctor->consultation_fee ?? '' }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            </div>

                            <div>
                                <label for="education" class="block text-sm font-medium text-text mb-2">Education</label>
                                <input type="text" id="education" name="education" value="{{ $doctor->education ?? '' }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            </div>

                            <div>
                                <label for="languages" class="block text-sm font-medium text-text mb-2">Languages</label>
                                <input type="text" id="languages" name="languages" value="{{ $doctor->languages ?? '' }}"
                                       placeholder="English, Arabic, French"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-text mb-2">Professional Description</label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">{{ $doctor->description ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Working Hours -->
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Working Hours</h3>

                        <div class="space-y-4">
                            @php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            @endphp

                            @foreach($days as $index => $day)
                                @php
                                    $workingHour = $workingHours->where('day_of_week', $index)->first();
                                @endphp
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="day_{{ $index }}" name="working_days[]" value="{{ $index }}"
                                               {{ $workingHour ? 'checked' : '' }}
                                               class="w-4 h-4 text-gold bg-gray-100 border-gray-300 rounded focus:ring-gold focus:ring-2">
                                        <label for="day_{{ $index }}" class="ml-3 text-sm font-medium text-text">{{ $day }}</label>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <input type="time" name="start_time_{{ $index }}" value="{{ $workingHour->start_time ?? '09:00' }}"
                                               class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                        <span class="text-muted">to</span>
                                        <input type="time" name="end_time_{{ $index }}" value="{{ $workingHour->end_time ?? '17:00' }}"
                                               class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Profile Stats -->
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Profile Statistics</h3>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Rating</span>
                                <div class="flex items-center">
                                    <div class="flex text-gold">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= ($doctor->rating ?? 0) ? '' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-text font-medium">{{ number_format($doctor->rating ?? 0, 1) }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-muted">Total Reviews</span>
                                <span class="text-text font-medium">{{ $doctor->total_reviews ?? 0 }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-muted">Total Appointments</span>
                                <span class="text-text font-medium">{{ $doctor->total_appointments ?? 0 }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-muted">Completed</span>
                                <span class="text-text font-medium">{{ $doctor->completed_appointments ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wallet Information -->
                @if($wallet)
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Wallet Balance</h3>

                        <div class="text-center">
                            <div class="text-3xl font-bold text-gold mb-2">${{ number_format($wallet->balance ?? 0, 2) }}</div>
                            <p class="text-muted text-sm">Available for withdrawal</p>
                        </div>

                        <div class="mt-6 space-y-2">
                            <button class="w-full btn btn-outline text-sm">
                                <i class="fas fa-download mr-2"></i>
                                Withdraw Funds
                            </button>
                            <button class="w-full btn btn-outline text-sm">
                                <i class="fas fa-history mr-2"></i>
                                Transaction History
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Profile Status -->
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Profile Status</h3>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Verification</span>
                                <span class="px-2 py-1 text-xs rounded-full {{ ($doctor->is_verified ?? false) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ($doctor->is_verified ?? false) ? 'Verified' : 'Pending' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-muted">Status</span>
                                <span class="px-2 py-1 text-xs rounded-full {{ ($doctor->is_active ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ($doctor->is_active ?? false) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-muted">License</span>
                                <span class="text-text font-medium">{{ $doctor->license_number ?? 'Not provided' }}</span>
                            </div>
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
    // Profile image handling
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                showNotification('Image size must be less than 2MB', 'error');
                input.value = '';
                return;
            }
            
            // Validate file type
            if (!file.type.match('image.*')) {
                showNotification('Please select a valid image file', 'error');
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profile-image-preview');
                preview.innerHTML = `<img src="${e.target.result}" alt="Profile Image" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(file);
        }
    }

    function removeProfileImage() {
        if (confirm('Are you sure you want to remove your profile image?')) {
            const preview = document.getElementById('profile-image-preview');
            preview.innerHTML = '<i class="fas fa-user-md text-4xl text-gray-400"></i>';
            document.getElementById('profile-image-input').value = '';
            
            // Send request to remove image
            fetch('{{ route("doctor.profile.remove-image") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Profile image removed successfully!', 'success');
                } else {
                    showNotification('Error removing profile image', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error removing profile image', 'error');
            });
        }
    }

    function saveProfile() {
        // Create FormData for file upload
        const formData = new FormData();
        
        // Add basic form data
        formData.append('name', document.getElementById('name').value);
        formData.append('specialty_id', document.getElementById('specialty_id').value);
        formData.append('experience_years', document.getElementById('experience_years').value);
        formData.append('consultation_fee', document.getElementById('consultation_fee').value);
        formData.append('education', document.getElementById('education').value);
        formData.append('languages', document.getElementById('languages').value);
        formData.append('description', document.getElementById('description').value);
        
        // Add profile image if selected
        const imageInput = document.getElementById('profile-image-input');
        if (imageInput.files && imageInput.files[0]) {
            formData.append('profile_image', imageInput.files[0]);
        }
        
        // Add working hours
        const workingHours = [];
        for (let i = 0; i < 7; i++) {
            const checkbox = document.getElementById(`day_${i}`);
            if (checkbox.checked) {
                workingHours.push({
                    day_of_week: i,
                    start_time: document.querySelector(`input[name="start_time_${i}"]`).value,
                    end_time: document.querySelector(`input[name="end_time_${i}"]`).value
                });
            }
        }
        formData.append('working_hours', JSON.stringify(workingHours));

        // Send update request
        fetch('{{ route("doctor.profile.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Profile updated successfully!', 'success');
                // Refresh page to show updated image
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(data.message || 'Error updating profile', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating profile', 'error');
        });
    }

    // Auto-save working hours when changed
    document.querySelectorAll('input[type="checkbox"], input[type="time"]').forEach(input => {
        input.addEventListener('change', function() {
            // You can implement auto-save here if needed
        });
    });
</script>
@endpush
