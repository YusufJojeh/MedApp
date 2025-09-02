@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Notifications</h1>
                    <p class="text-muted mt-2">Manage your notifications and preferences</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('notifications.settings') }}" class="btn btn-outline">
                        <i class="fas fa-cog mr-2"></i>
                        Settings
                    </a>
                    <a href="{{ route('notifications.test-page') }}" class="btn btn-primary">
                        <i class="fas fa-flask mr-2"></i>
                        Test Notifications
                    </a>
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
                            <i class="fas fa-bell text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['total']) }}</p>
                            <p class="text-sm text-muted">Total Notifications</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-exclamation-circle text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['unread']) }}</p>
                            <p class="text-sm text-muted">Unread</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['read']) }}</p>
                            <p class="text-sm text-muted">Read</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-check text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['appointments']) }}</p>
                            <p class="text-sm text-muted">Appointment Notifications</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-4 mb-8">
            <button class="btn btn-outline" onclick="markAllAsRead()">
                <i class="fas fa-check-double mr-2"></i>
                Mark All as Read
            </button>
            <button class="btn btn-outline" onclick="deleteRead()">
                <i class="fas fa-trash mr-2"></i>
                Delete Read
            </button>
            <button class="btn btn-outline" onclick="refreshNotifications()">
                <i class="fas fa-sync-alt mr-2"></i>
                Refresh
            </button>
        </div>

        <!-- Filters -->
        <div class="card feature-card mb-8" data-aos="fade-up">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Search</label>
                        <input type="text" id="searchInput" placeholder="Search notifications..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Type</label>
                        <select id="typeFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Types</option>
                            <option value="appointment">Appointment</option>
                            <option value="payment">Payment</option>
                            <option value="wallet">Wallet</option>
                            <option value="system">System</option>
                            <option value="security">Security</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Status</label>
                        <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Status</option>
                            <option value="unread">Unread</option>
                            <option value="read">Read</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Priority</label>
                        <select id="priorityFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Priorities</option>
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button class="btn btn-primary flex-1" onclick="filterNotifications()">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                        <button class="btn btn-outline flex-1" onclick="clearFilters()">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="card feature-card" data-aos="fade-up">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-text">All Notifications</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-muted">Showing {{ $notifications->count() }} notifications</span>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($notifications as $notification)
                        <div class="notification-item border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ $notification->isUnread ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}" data-id="{{ $notification->id }}" data-type="{{ $notification->type }}" data-status="{{ $notification->isUnread ? 'unread' : 'read' }}" data-priority="{{ $notification->priority }}">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <div class="w-12 h-12 bg-gradient-to-br from-{{ $notification->colorClass }}-500 to-{{ $notification->colorClass }}-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-xl">{{ $notification->icon }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <h4 class="font-semibold text-text">{{ $notification->title }}</h4>
                                            @if($notification->isUnread)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Unread
                                                </span>
                                            @endif
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $notification->priorityClass }}-100 text-{{ $notification->priorityClass }}-800 dark:bg-{{ $notification->priorityClass }}-900 dark:text-{{ $notification->priorityClass }}-200">
                                                {{ ucfirst($notification->priority) }}
                                            </span>
                                        </div>
                                        <p class="text-muted text-sm mb-2">{{ $notification->message }}</p>
                                        <div class="flex items-center space-x-4 text-xs text-muted">
                                            <span>{{ $notification->timeAgo }}</span>
                                            <span class="capitalize">{{ $notification->type }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 ml-4">
                                    @if($notification->isUnread)
                                        <button onclick="markAsRead({{ $notification->id }})" class="btn btn-sm btn-outline" title="Mark as Read">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @else
                                        <button onclick="markAsUnread({{ $notification->id }})" class="btn btn-sm btn-outline" title="Mark as Unread">
                                            <i class="fas fa-eye-slash"></i>
                                        </button>
                                    @endif
                                    <button onclick="deleteNotification({{ $notification->id }})" class="btn btn-sm btn-outline text-red-600 hover:text-red-700" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-bell-slash text-white text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-text mb-2">No notifications found</h3>
                            <p class="text-muted">You're all caught up! No new notifications at the moment.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="mt-8">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Filter functionality
function filterNotifications() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const type = document.getElementById('typeFilter').value;
    const status = document.getElementById('statusFilter').value;
    const priority = document.getElementById('priorityFilter').value;

    const items = document.querySelectorAll('.notification-item');

    items.forEach(item => {
        const title = item.querySelector('h4').textContent.toLowerCase();
        const message = item.querySelector('p').textContent.toLowerCase();
        const itemType = item.dataset.type;
        const itemStatus = item.dataset.status;
        const itemPriority = item.dataset.priority;

        const matchesSearch = title.includes(search) || message.includes(search);
        const matchesType = !type || itemType === type;
        const matchesStatus = !status || itemStatus === status;
        const matchesPriority = !priority || itemPriority === priority;

        if (matchesSearch && matchesType && matchesStatus && matchesPriority) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('priorityFilter').value = '';

    const items = document.querySelectorAll('.notification-item');
    items.forEach(item => item.style.display = 'block');
}

// Notification actions
function markAsRead(id) {
    fetch(`/notifications/mark-read/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAsUnread(id) {
    fetch(`/notifications/mark-unread/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteNotification(id) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function markAllAsRead() {
    if (confirm('Mark all notifications as read?')) {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function deleteRead() {
    if (confirm('Delete all read notifications?')) {
        fetch('/notifications/delete-read', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function refreshNotifications() {
    location.reload();
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', filterNotifications);
document.getElementById('typeFilter').addEventListener('change', filterNotifications);
document.getElementById('statusFilter').addEventListener('change', filterNotifications);
document.getElementById('priorityFilter').addEventListener('change', filterNotifications);
</script>
@endpush
@endsection
