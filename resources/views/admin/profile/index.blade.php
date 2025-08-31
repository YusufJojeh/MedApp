@extends('layouts.app')

@section('title', 'Admin Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Profile</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    Manage your profile information and account settings.
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.profile.edit') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Profile
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="p-6">
                    <!-- Profile Image -->
                    <div class="text-center mb-6">
                        <div class="relative inline-block">
                            @if($admin->profile_image)
                                <img src="{{ Storage::url($admin->profile_image) }}"
                                     alt="Profile Image"
                                     class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 dark:border-gray-600">
                            @else
                                <div class="w-32 h-32 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center border-4 border-gray-200 dark:border-gray-600">
                                    <i class="fas fa-user text-4xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                            @endif
                            @if($admin->profile_image)
                                <button onclick="deleteProfileImage()"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 transition-colors">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            @endif
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mt-4">
                            {{ $admin->first_name }} {{ $admin->last_name }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">Administrator</p>
                    </div>

                    <!-- Profile Stats -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Profile Completion</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white" id="profileCompletion">Loading...</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-percentage text-blue-600 dark:text-blue-400"></i>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Logins</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white" id="totalLogins">Loading...</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-sign-in-alt text-green-600 dark:text-green-400"></i>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Last Login</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white" id="lastLogin">Loading...</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-6 space-y-2">
                        <a href="{{ route('admin.profile.edit') }}"
                           class="w-full flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-edit mr-3"></i>
                            Edit Profile
                        </a>
                        <a href="{{ route('admin.profile.change-password') }}"
                           class="w-full flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-key mr-3"></i>
                            Change Password
                        </a>
                        <a href="{{ route('admin.profile.security') }}"
                           class="w-full flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-shield-alt mr-3"></i>
                            Security Settings
                        </a>
                        <a href="{{ route('admin.profile.activity') }}"
                           class="w-full flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-history mr-3"></i>
                            Activity Log
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Profile Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Personal Information</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $admin->first_name }} {{ $admin->last_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $admin->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $adminData->phone ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $adminData && $adminData->date_of_birth ? \Carbon\Carbon::parse($adminData->date_of_birth)->format('M d, Y') : 'Not provided' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Gender</label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $adminData && $adminData->gender ? ucfirst($adminData->gender) : 'Not provided' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Address Information</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Address</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $adminData->address ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">City</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $adminData->city ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">State</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $adminData->state ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Country</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $adminData->country ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Postal Code</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $adminData->postal_code ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bio -->
                    @if($adminData && $adminData->bio)
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Bio</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $adminData->bio }}</p>
                        </div>
                    @endif

                    <!-- Account Information -->
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Account Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $admin->created_at ? $admin->created_at->format('M d, Y') : 'Unknown' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $admin->updated_at ? $admin->updated_at->format('M d, Y') : 'Unknown' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load profile statistics
document.addEventListener('DOMContentLoaded', function() {
    loadProfileStats();
});

function loadProfileStats() {
    fetch('{{ route("admin.profile.stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('profileCompletion').textContent = data.profile_completion + '%';
            document.getElementById('totalLogins').textContent = data.total_logins;

            if (data.last_login) {
                const lastLogin = new Date(data.last_login);
                document.getElementById('lastLogin').textContent = lastLogin.toLocaleDateString();
            } else {
                document.getElementById('lastLogin').textContent = 'Never';
            }
        })
        .catch(error => {
            console.error('Error loading profile stats:', error);
        });
}

function deleteProfileImage() {
    if (!confirm('Are you sure you want to delete your profile image?')) {
        return;
    }

    fetch('{{ route("admin.profile.delete-image") }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error deleting profile image: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting profile image. Please try again.');
    });
}
</script>
@endsection
