@extends('layouts.app')

@section('title', 'User Management - Admin Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">User Management</h1>
                    <p class="text-muted mt-2">Manage system users and their roles</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="btn btn-primary" onclick="createUser()">
                        <i class="fas fa-plus mr-2"></i>
                        Add User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card feature-card" data-aos="fade-up">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['total_users']) }}</p>
                            <p class="text-sm text-muted">Total Users</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-md text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['total_doctors']) }}</p>
                            <p class="text-sm text-muted">Doctors</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-injured text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['total_patients']) }}</p>
                            <p class="text-sm text-muted">Patients</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-shield text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['total_admins']) }}</p>
                            <p class="text-sm text-muted">Admins</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-4 mb-8">
            <button class="btn btn-outline" onclick="exportUsers()">
                <i class="fas fa-download mr-2"></i>
                Export Users
            </button>
            <button class="btn btn-outline" onclick="bulkActions()">
                <i class="fas fa-tasks mr-2"></i>
                Bulk Actions
            </button>
            <button class="btn btn-outline" onclick="importUsers()">
                <i class="fas fa-upload mr-2"></i>
                Import Users
            </button>
        </div>

        <!-- Filters -->
        <div class="card feature-card mb-8" data-aos="fade-up">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Search</label>
                        <input type="text" id="searchInput" placeholder="Search users..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Role</label>
                        <select id="roleFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="doctor">Doctor</option>
                            <option value="patient">Patient</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Status</label>
                        <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Date Range</label>
                        <select id="dateFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="btn btn-primary w-full" onclick="filterUsers()">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card feature-card" data-aos="fade-up">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-text">All Users</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-muted">Showing {{ $users->count() }} users</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-medium text-text">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-gold focus:ring-gold">
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-text">User</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Role</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Status</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Joined</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Last Login</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-4 px-4">
                                        <input type="checkbox" class="user-checkbox rounded border-gray-300 text-gold focus:ring-gold" value="{{ $user->id }}">
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-text">{{ $user->first_name }} {{ $user->last_name }}</p>
                                                <p class="text-sm text-muted">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-3 py-1 text-xs rounded-full {{ $user->role_badge_class }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-3 py-1 text-xs rounded-full {{ $user->status_badge_class }}">
                                            {{ $user->status_text }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div>
                                            @if($user->created_at)
                                                <p class="text-sm text-text">{{ \Carbon\Carbon::parse($user->created_at)->format('M j, Y') }}</p>
                                                <p class="text-xs text-muted">{{ \Carbon\Carbon::parse($user->created_at)->format('g:i A') }}</p>
                                            @else
                                                <p class="text-sm text-muted">N/A</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div>
                                            @if($user->last_login)
                                                <p class="text-sm text-text">{{ \Carbon\Carbon::parse($user->last_login)->format('M j, Y') }}</p>
                                                <p class="text-xs text-muted">{{ \Carbon\Carbon::parse($user->last_login)->format('g:i A') }}</p>
                                            @else
                                                <p class="text-sm text-muted">Never</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-2">
                                            <button class="btn btn-sm btn-outline" onclick="viewUser({{ $user->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($user->is_active)
                                                <button class="btn btn-sm btn-warning" onclick="deactivateUser({{ $user->id }})">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-success" onclick="activateUser({{ $user->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-sm btn-danger" onclick="deleteUser({{ $user->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-muted">
                                        <i class="fas fa-users text-4xl mb-4"></i>
                                        <p>No users found</p>
                                        <button class="btn btn-primary mt-4" onclick="createUser()">
                                            <i class="fas fa-plus mr-2"></i>
                                            Add Your First User
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-text">User Details</h3>
                    <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" onclick="closeModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="userDetails">
                    <!-- User details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-text" id="confirmTitle">Confirm Action</h3>
                    <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" onclick="closeConfirmModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-text mb-6" id="confirmMessage">Are you sure you want to perform this action?</p>
                <div class="flex space-x-3">
                    <button type="button" class="btn btn-outline flex-1" onclick="closeConfirmModal()">Cancel</button>
                    <button type="button" class="btn btn-danger flex-1" id="confirmAction">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentUserId = null;

    // Filter users
    function filterUsers() {
        const search = document.getElementById('searchInput').value;
        const role = document.getElementById('roleFilter').value;
        const status = document.getElementById('statusFilter').value;
        const dateRange = document.getElementById('dateFilter').value;

        let url = '{{ route("admin.users.index") }}?';
        if (search) url += `search=${encodeURIComponent(search)}&`;
        if (role) url += `role=${role}&`;
        if (status) url += `status=${status}&`;
        if (dateRange) url += `date_range=${dateRange}&`;

        window.location.href = url;
    }

    // Create user - redirect to create page
    function createUser() {
        window.location.href = '{{ route("admin.users.create") }}';
    }



    // View user details - redirect to show page
    function viewUser(userId) {
        window.location.href = `{{ route('admin.users.index') }}/${userId}`;
    }

    // Activate user
    function activateUser(userId) {
        showConfirmModal(
            'Activate User',
            'Are you sure you want to activate this user?',
            () => performUserAction(`{{ route('admin.users.index') }}/${userId}/activate`, 'POST', 'User activated successfully')
        );
    }

    // Deactivate user
    function deactivateUser(userId) {
        showConfirmModal(
            'Deactivate User',
            'Are you sure you want to deactivate this user?',
            () => performUserAction(`{{ route('admin.users.index') }}/${userId}/deactivate`, 'POST', 'User deactivated successfully')
        );
    }

    // Delete user
    function deleteUser(userId) {
        showConfirmModal(
            'Delete User',
            'Are you sure you want to delete this user? This action cannot be undone.',
            () => performUserAction(`{{ route('admin.users.index') }}/${userId}`, 'DELETE', 'User deleted successfully')
        );
    }

    // Perform user action (activate, deactivate, delete)
    function performUserAction(url, method, successMessage) {
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(successMessage, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message || 'Error performing action', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error performing action', 'error');
        });
    }

    // Close modals
    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    // Export users
    function exportUsers() {
        const search = document.getElementById('searchInput').value;
        const role = document.getElementById('roleFilter').value;
        const status = document.getElementById('statusFilter').value;
        const dateRange = document.getElementById('dateFilter').value;

        let url = '{{ route("admin.users.export") }}?';
        if (search) url += `search=${encodeURIComponent(search)}&`;
        if (role) url += `role=${role}&`;
        if (status) url += `status=${status}&`;
        if (dateRange) url += `date_range=${dateRange}&`;

        window.location.href = url;
    }

    // Bulk actions
    function bulkActions() {
        const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
        if (selectedUsers.length === 0) {
            showNotification('Please select users to perform bulk actions', 'warning');
            return;
        }

        const action = prompt('Enter action (activate/deactivate/delete):').toLowerCase();
        const userIds = Array.from(selectedUsers).map(cb => cb.value);

        if (['activate', 'deactivate', 'delete'].includes(action)) {
            if (confirm(`Are you sure you want to ${action} ${userIds.length} users?`)) {
                fetch('{{ route("admin.users.bulk-action") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        action: action,
                        user_ids: userIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(`Bulk ${action} completed successfully`, 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showNotification('Error performing bulk action', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error performing bulk action', 'error');
                });
            }
        } else {
            showNotification('Invalid action specified', 'error');
        }
    }

    // Import users
    function importUsers() {
        showNotification('Import functionality coming soon', 'info');
    }

    // Select all users
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Show confirmation modal
    function showConfirmModal(title, message, onConfirm) {
        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmAction').onclick = () => {
            closeConfirmModal();
            onConfirm();
        };
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    // Close confirmation modal
    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Simple notification - you can enhance this with a proper notification system
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(`${type.toUpperCase()}: ${message}`);
    }

    // Initialize filters on page load
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get('search')) {
            document.getElementById('searchInput').value = urlParams.get('search');
        }
        if (urlParams.get('role')) {
            document.getElementById('roleFilter').value = urlParams.get('role');
        }
        if (urlParams.get('status')) {
            document.getElementById('statusFilter').value = urlParams.get('status');
        }
        if (urlParams.get('date_range')) {
            document.getElementById('dateFilter').value = urlParams.get('date_range');
        }
    });
</script>
@endpush
