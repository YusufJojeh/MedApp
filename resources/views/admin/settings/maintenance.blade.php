@extends('layouts.app')

@section('title', 'Backup & Maintenance')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Backup & Maintenance</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    Manage database backups, system maintenance, and data integrity checks.
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.settings.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Settings
                </a>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <i class="fas fa-database text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Database Size</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $maintenance['database_size'] }} MB</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <i class="fas fa-hdd text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Storage Used</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $maintenance['storage_usage']['percentage'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Backup</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $maintenance['last_backup'] ? $maintenance['last_backup']->format('M d, Y') : 'Never' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 {{ $maintenance['maintenance_mode'] ? 'bg-red-100 dark:bg-red-900' : 'bg-green-100 dark:bg-green-900' }} rounded-lg">
                    <i class="fas fa-tools {{ $maintenance['maintenance_mode'] ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Maintenance Mode</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $maintenance['maintenance_mode'] ? 'Enabled' : 'Disabled' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Management -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Database Backup</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Create and manage database backups</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Backup Settings</h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Auto Backup</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       class="sr-only peer"
                                       {{ $maintenance['backup_enabled'] ? 'checked' : '' }}
                                       disabled>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Retention Period (Days)
                            </label>
                            <input type="number"
                                   value="{{ $maintenance['backup_retention_days'] }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   disabled>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Manual Backup</h3>

                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Create a manual backup of your database. This process may take a few minutes depending on the database size.
                        </p>

                        <button onclick="createBackup()"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Create Backup Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Maintenance -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">System Maintenance</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Optimize system performance and clear caches</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Cache Management</h3>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Application Cache</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Clear compiled application cache</p>
                            </div>
                            <button onclick="clearCache('config')"
                                    class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                Clear
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Route Cache</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Clear compiled route cache</p>
                            </div>
                            <button onclick="clearCache('route')"
                                    class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                Clear
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">View Cache</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Clear compiled view cache</p>
                            </div>
                            <button onclick="clearCache('view')"
                                    class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Database Optimization</h3>

                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Optimize database tables and rebuild indexes for better performance.
                        </p>

                        <button onclick="optimizeDatabase()"
                                class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-cogs mr-2"></i>
                            Optimize Database
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Storage Information</h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $maintenance['storage_usage']['total'] }} GB</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Storage</div>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $maintenance['storage_usage']['used'] }} GB</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Used Storage</div>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $maintenance['storage_usage']['free'] }} GB</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Free Storage</div>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                    <span>Storage Usage</span>
                    <span>{{ $maintenance['storage_usage']['percentage'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $maintenance['storage_usage']['percentage'] }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function createBackup() {
    if (!confirm('Are you sure you want to create a backup? This may take a few minutes.')) {
        return;
    }

    fetch('{{ route("admin.settings.maintenance.backup") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Backup created successfully!');
            location.reload();
        } else {
            alert('Error creating backup: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating backup. Please try again.');
    });
}

function optimizeDatabase() {
    if (!confirm('Are you sure you want to optimize the database? This may take a few minutes.')) {
        return;
    }

    fetch('{{ route("admin.settings.maintenance.optimize") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Database optimized successfully!');
        } else {
            alert('Error optimizing database: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error optimizing database. Please try again.');
    });
}

function clearCache(type) {
    if (!confirm(`Are you sure you want to clear the ${type} cache?`)) {
        return;
    }

    fetch('{{ route("admin.settings.clear-cache") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ type: type }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`${type.charAt(0).toUpperCase() + type.slice(1)} cache cleared successfully!`);
        } else {
            alert('Error clearing cache: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error clearing cache. Please try again.');
    });
}
</script>
@endsection
