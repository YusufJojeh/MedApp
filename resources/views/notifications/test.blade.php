@extends('layouts.app')

@section('title', 'Test Notifications')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Test Notifications</h1>
                    <p class="text-muted mt-2">Test various notification types and priorities</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Notifications
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Test Buttons -->
        <div class="card feature-card mb-8" data-aos="fade-up">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-text mb-6">Quick Test Notifications</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <button onclick="sendTestNotification('appointment', 'Appointment Confirmed', 'Your appointment has been confirmed for tomorrow at 2:00 PM.', 'normal', '📅', 'blue')" class="btn btn-outline">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Appointment Notification
                    </button>

                    <button onclick="sendTestNotification('payment', 'Payment Successful', 'Your payment of $150 has been processed successfully.', 'normal', '💳', 'green')" class="btn btn-outline">
                        <i class="fas fa-credit-card mr-2"></i>
                        Payment Notification
                    </button>

                    <button onclick="sendTestNotification('wallet', 'Wallet Updated', 'Your wallet balance has been updated. New balance: $250.', 'normal', '💰', 'purple')" class="btn btn-outline">
                        <i class="fas fa-wallet mr-2"></i>
                        Wallet Notification
                    </button>

                    <button onclick="sendTestNotification('system', 'System Maintenance', 'Scheduled maintenance will begin in 30 minutes.', 'high', '🔧', 'yellow')" class="btn btn-outline">
                        <i class="fas fa-cog mr-2"></i>
                        System Notification
                    </button>

                    <button onclick="sendTestNotification('security', 'Security Alert', 'New login detected from a new device.', 'urgent', '🔒', 'red')" class="btn btn-outline">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Security Alert
                    </button>

                    <button onclick="sendTestNotification('reminder', 'Appointment Reminder', 'Don\'t forget your appointment tomorrow at 10:00 AM.', 'normal', '⏰', 'orange')" class="btn btn-outline">
                        <i class="fas fa-clock mr-2"></i>
                        Reminder Notification
                    </button>
                </div>
            </div>
        </div>

        <!-- Custom Notification Form -->
        <div class="card feature-card" data-aos="fade-up">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-text mb-6">Custom Notification</h2>
                <form id="customNotificationForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="type" class="block text-sm font-medium text-text mb-2">Type</label>
                            <select id="type" name="type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                <option value="appointment">Appointment</option>
                                <option value="payment">Payment</option>
                                <option value="wallet">Wallet</option>
                                <option value="system">System</option>
                                <option value="security">Security</option>
                                <option value="reminder">Reminder</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium text-text mb-2">Priority</label>
                            <select id="priority" name="priority" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                <option value="low">Low</option>
                                <option value="normal" selected>Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-text mb-2">Title</label>
                        <input type="text" id="title" name="title" placeholder="Enter notification title" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-text mb-2">Message</label>
                        <textarea id="message" name="message" rows="3" placeholder="Enter notification message" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="icon" class="block text-sm font-medium text-text mb-2">Icon</label>
                            <input type="text" id="icon" name="icon" placeholder="🔔" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                        </div>

                        <div>
                            <label for="color" class="block text-sm font-medium text-text mb-2">Color</label>
                            <select id="color" name="color" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                <option value="blue">Blue</option>
                                <option value="green">Green</option>
                                <option value="yellow">Yellow</option>
                                <option value="red">Red</option>
                                <option value="purple">Purple</option>
                                <option value="orange">Orange</option>
                                <option value="gray">Gray</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send Test Notification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function sendTestNotification(type, title, message, priority, icon, color) {
    const data = {
        type: type,
        title: title,
        message: message,
        priority: priority,
        icon: icon,
        color: color
    };

    fetch('{{ route("notifications.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Test notification sent successfully!', 'success');
        } else {
            showNotification('Failed to send test notification: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error sending test notification', 'error');
    });
}

document.getElementById('customNotificationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        type: formData.get('type'),
        title: formData.get('title'),
        message: formData.get('message'),
        priority: formData.get('priority'),
        icon: formData.get('icon') || '🔔',
        color: formData.get('color')
    };

    fetch('{{ route("notifications.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Custom test notification sent successfully!', 'success');
            this.reset();
        } else {
            showNotification('Failed to send custom test notification: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error sending custom test notification', 'error');
    });
});

function showNotification(message, type = 'info') {
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
    } else {
        alert(message);
    }
}
</script>
@endpush
@endsection
