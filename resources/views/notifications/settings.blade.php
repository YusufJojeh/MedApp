@extends('layouts.app')

@section('title', 'Notification Settings')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Notification Settings</h1>
                    <p class="text-muted mt-2">Manage your notification preferences and delivery channels</p>
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
        <!-- Settings Form -->
        <div class="card feature-card" data-aos="fade-up">
            <form id="notificationSettingsForm" class="p-6 space-y-8">
                <!-- Notification Channels -->
                <div>
                    <h2 class="text-xl font-semibold text-text mb-6">Notification Channels</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-envelope text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-text">Email Notifications</h3>
                                    <p class="text-sm text-muted">Receive notifications via email</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="email_notifications" name="email_notifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-mobile-alt text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-text">SMS Notifications</h3>
                                    <p class="text-sm text-muted">Receive notifications via SMS</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="sms_notifications" name="sms_notifications" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-bell text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-text">Push Notifications</h3>
                                    <p class="text-sm text-muted">Receive notifications in browser</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="push_notifications" name="push_notifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notification Types -->
                <div>
                    <h2 class="text-xl font-semibold text-text mb-6">Notification Types</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-text">Appointment Reminders</h3>
                                    <p class="text-sm text-muted">Get reminded about upcoming appointments</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="appointment_reminders" name="appointment_reminders" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-credit-card text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-text">Payment Notifications</h3>
                                    <p class="text-sm text-muted">Get notified about payment status</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="payment_notifications" name="payment_notifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-cog text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-text">System Notifications</h3>
                                    <p class="text-sm text-muted">Receive system updates and announcements</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="system_notifications" name="system_notifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div>
                    <h2 class="text-xl font-semibold text-text mb-6">Advanced Settings</h2>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="appointment_reminder_hours" class="block text-sm font-medium text-text mb-2">
                                    Appointment Reminder Time
                                </label>
                                <select id="appointment_reminder_hours" name="appointment_reminder_hours" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                    <option value="1">1 hour before</option>
                                    <option value="2">2 hours before</option>
                                    <option value="6">6 hours before</option>
                                    <option value="12">12 hours before</option>
                                    <option value="24" selected>24 hours before</option>
                                    <option value="48">48 hours before</option>
                                </select>
                            </div>

                            <div>
                                <label for="notification_timezone" class="block text-sm font-medium text-text mb-2">
                                    Timezone
                                </label>
                                <select id="notification_timezone" name="notification_timezone" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                    <option value="UTC">UTC</option>
                                    <option value="Asia/Riyadh" selected>Asia/Riyadh (Saudi Arabia)</option>
                                    <option value="America/New_York">America/New_York</option>
                                    <option value="Europe/London">Europe/London</option>
                                    <option value="Asia/Dubai">Asia/Dubai</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-text mb-4">Quiet Hours</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="quiet_hours_start" class="block text-sm font-medium text-text mb-2">
                                        Start Time
                                    </label>
                                    <input type="time" id="quiet_hours_start" name="quiet_hours_start" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                </div>
                                <div>
                                    <label for="quiet_hours_end" class="block text-sm font-medium text-text mb-2">
                                        End Time
                                    </label>
                                    <input type="time" id="quiet_hours_end" name="quiet_hours_end" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                </div>
                            </div>
                            <p class="text-sm text-muted mt-2">During quiet hours, only urgent notifications will be sent</p>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load current settings
    loadNotificationSettings();

    // Handle form submission
    document.getElementById('notificationSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveNotificationSettings();
    });
});

function loadNotificationSettings() {
    fetch('{{ route("notifications.settings.api") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const settings = data.settings;

                // Set checkbox values
                document.getElementById('email_notifications').checked = settings.email_notifications;
                document.getElementById('sms_notifications').checked = settings.sms_notifications;
                document.getElementById('push_notifications').checked = settings.push_notifications;
                document.getElementById('appointment_reminders').checked = settings.appointment_reminders;
                document.getElementById('payment_notifications').checked = settings.payment_notifications;
                document.getElementById('system_notifications').checked = settings.system_notifications;

                // Set select values
                document.getElementById('appointment_reminder_hours').value = settings.appointment_reminder_hours;
                document.getElementById('notification_timezone').value = settings.notification_timezone;

                // Set time values
                if (settings.quiet_hours_start) {
                    document.getElementById('quiet_hours_start').value = settings.quiet_hours_start;
                }
                if (settings.quiet_hours_end) {
                    document.getElementById('quiet_hours_end').value = settings.quiet_hours_end;
                }
            }
        })
        .catch(error => {
            console.error('Error loading notification settings:', error);
        });
}

function saveNotificationSettings() {
    const formData = new FormData(document.getElementById('notificationSettingsForm'));
    const data = {
        email_notifications: formData.get('email_notifications') === 'on',
        sms_notifications: formData.get('sms_notifications') === 'on',
        push_notifications: formData.get('push_notifications') === 'on',
        appointment_reminders: formData.get('appointment_reminders') === 'on',
        payment_notifications: formData.get('payment_notifications') === 'on',
        system_notifications: formData.get('system_notifications') === 'on',
        appointment_reminder_hours: formData.get('appointment_reminder_hours'),
        notification_timezone: formData.get('notification_timezone'),
        quiet_hours_start: formData.get('quiet_hours_start'),
        quiet_hours_end: formData.get('quiet_hours_end')
    };

    fetch('{{ route("notifications.update-settings") }}', {
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
            showNotification('Notification settings updated successfully!', 'success');
        } else {
            showNotification('Failed to update notification settings: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error saving notification settings:', error);
        showNotification('Error saving notification settings', 'error');
    });
}

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
