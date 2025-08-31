@extends('layouts.app')
@section('title', 'Edit Profile')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Profile</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Update your personal information and profile settings</p>
        </div>

        <!-- Profile Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Profile Image Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Profile Image</h3>
                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                                @if($admin->profile_image)
                                    <img src="{{ asset('storage/' . $admin->profile_image) }}"
                                         alt="Profile Image"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-user text-3xl text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            @if($admin->profile_image)
                                <button type="button"
                                        onclick="deleteProfileImage()"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label for="profile_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Upload New Image
                            </label>
                            <input type="file"
                                   id="profile_image"
                                   name="profile_image"
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-full file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-blue-50 file:text-blue-700
                                          hover:file:bg-blue-100
                                          dark:file:bg-gray-700 dark:file:text-gray-300">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Recommended: Square image, max 2MB
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                First Name *
                            </label>
                            <input type="text"
                                   id="first_name"
                                   name="first_name"
                                   value="{{ old('first_name', $admin->first_name) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Last Name *
                            </label>
                            <input type="text"
                                   id="last_name"
                                   name="last_name"
                                   value="{{ old('last_name', $admin->last_name) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Address *
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $admin->email) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Number
                            </label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', $adminData->phone ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Date of Birth
                            </label>
                            <input type="date"
                                   id="date_of_birth"
                                   name="date_of_birth"
                                   value="{{ old('date_of_birth', $adminData->date_of_birth ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('date_of_birth')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Gender
                            </label>
                            <select id="gender"
                                    name="gender"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $adminData->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $adminData->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $adminData->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Address Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Street Address
                            </label>
                            <input type="text"
                                   id="address"
                                   name="address"
                                   value="{{ old('address', $adminData->address ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                City
                            </label>
                            <input type="text"
                                   id="city"
                                   name="city"
                                   value="{{ old('city', $adminData->city ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('city')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                State/Province
                            </label>
                            <input type="text"
                                   id="state"
                                   name="state"
                                   value="{{ old('state', $adminData->state ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('state')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Country
                            </label>
                            <input type="text"
                                   id="country"
                                   name="country"
                                   value="{{ old('country', $adminData->country ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('country')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Postal Code
                            </label>
                            <input type="text"
                                   id="postal_code"
                                   name="postal_code"
                                   value="{{ old('postal_code', $adminData->postal_code ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('postal_code')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Bio Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Bio</h3>
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            About Me
                        </label>
                        <textarea id="bio"
                                  name="bio"
                                  rows="4"
                                  placeholder="Tell us about yourself..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">{{ old('bio', $adminData->bio ?? '') }}</textarea>
                        @error('bio')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.profile.index') }}"
                       class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteProfileImage() {
    if (confirm('Are you sure you want to delete your profile image?')) {
        fetch('{{ route("admin.profile.delete-image") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting image: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting image');
        });
    }
}
</script>
@endsection
