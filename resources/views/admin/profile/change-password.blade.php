@extends('layouts.app')
@section('title', 'Change Password')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Change Password</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Update your account password to keep your account secure</p>
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

        <!-- Password Change Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <form action="{{ route('admin.profile.update-password') }}" method="POST">

                <div class="space-y-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Current Password *
                        </label>
                        <input type="password"
                               id="current_password"
                               name="current_password"
                               required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            New Password *
                        </label>
                        <input type="password"
                               id="new_password"
                               name="new_password"
                               required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Password must be at least 8 characters long
                        </p>
                        @error('new_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Confirm New Password *
                        </label>
                        <input type="password"
                               id="new_password_confirmation"
                               name="new_password_confirmation"
                               required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        @error('new_password_confirmation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700 mt-8">
                    <a href="{{ route('admin.profile.index') }}"
                       class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
