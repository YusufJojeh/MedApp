@extends('layouts.app')
@section('title', 'Activity Log')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Activity Log</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">View your account activity and login history</p>
        </div>

        <!-- Activity Log -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
                    <div class="flex items-center space-x-4">
                        <select id="filter-type" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">All Activities</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                            <option value="profile_update">Profile Update</option>
                            <option value="password_change">Password Change</option>
                        </select>
                        <button onclick="exportActivity()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Activity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                IP Address
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Device
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date & Time
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($activities as $activity)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($activity->action === 'login')
                                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-sign-in-alt text-green-600 dark:text-green-400 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Login</span>
                                        @elseif($activity->action === 'logout')
                                            <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-sign-out-alt text-red-600 dark:text-red-400 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Logout</span>
                                        @elseif($activity->action === 'profile_update')
                                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user-edit text-blue-600 dark:text-blue-400 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Profile Update</span>
                                        @elseif($activity->action === 'password_change')
                                            <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-key text-yellow-600 dark:text-yellow-400 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Password Change</span>
                                        @else
                                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-info text-gray-600 dark:text-gray-400 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($activity->action) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $activity->description ?? 'No description available' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $activity->ip_address ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        @if($activity->user_agent)
                                            @php
                                                $userAgent = $activity->user_agent;
                                                $browser = 'Unknown';
                                                $platform = 'Unknown';

                                                if (strpos($userAgent, 'Chrome') !== false) {
                                                    $browser = 'Chrome';
                                                } elseif (strpos($userAgent, 'Firefox') !== false) {
                                                    $browser = 'Firefox';
                                                } elseif (strpos($userAgent, 'Safari') !== false) {
                                                    $browser = 'Safari';
                                                } elseif (strpos($userAgent, 'Edge') !== false) {
                                                    $browser = 'Edge';
                                                }

                                                if (strpos($userAgent, 'Windows') !== false) {
                                                    $platform = 'Windows';
                                                } elseif (strpos($userAgent, 'Mac') !== false) {
                                                    $platform = 'macOS';
                                                } elseif (strpos($userAgent, 'Linux') !== false) {
                                                    $platform = 'Linux';
                                                } elseif (strpos($userAgent, 'Android') !== false) {
                                                    $platform = 'Android';
                                                } elseif (strpos($userAgent, 'iOS') !== false) {
                                                    $platform = 'iOS';
                                                }
                                            @endphp
                                            {{ $browser }} on {{ $platform }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $activity->created_at ? $activity->created_at->format('M d, Y H:i') : 'N/A' }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-history text-4xl mb-4"></i>
                                        <p class="text-lg font-medium">No activity found</p>
                                        <p class="text-sm">Your activity history will appear here</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($activities->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>

        <!-- Activity Summary -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-sign-in-alt text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Logins</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalLogins ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-user-edit text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Profile Updates</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalUpdates ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-key text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Password Changes</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalPasswordChanges ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportActivity() {
    // Implementation for exporting activity data
    alert('Export functionality will be implemented here');
}

document.getElementById('filter-type').addEventListener('change', function() {
    const filterValue = this.value;
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const activityCell = row.querySelector('td:first-child');
        if (activityCell) {
            const activityText = activityCell.textContent.toLowerCase();
            if (!filterValue || activityText.includes(filterValue.toLowerCase())) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});
</script>
@endsection
