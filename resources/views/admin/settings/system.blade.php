@extends('layouts.app')

@section('title', 'System Settings - Admin Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">System Settings</h1>
                    <p class="text-muted mt-2">Configure system-wide settings and preferences</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Settings Form -->
        <div class="card feature-card" data-aos="fade-up">
            <div class="p-6">
            <form id="systemSettingsForm" class="space-y-6">
                @csrf

                                <!-- Site Information -->
                <div class="space-y-4">
                    <h3 class="text-xl font-semibold text-text mb-4">Site Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-text mb-2">Site Name</label>
                            <input type="text" name="site_name" value="{{ $settings['site_name'] ?? '' }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text mb-2">Site Description</label>
                            <input type="text" name="site_description" value="{{ $settings['site_description'] ?? '' }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="space-y-4">
                    <h3 class="text-xl font-semibold text-white mb-4">Contact Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-white/80 mb-2">Contact Email</label>
                            <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}"
                                   class="form-input w-full" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white/80 mb-2">Contact Phone</label>
                            <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}"
                                   class="form-input w-full">
                        </div>
                    </div>
                </div>

                <!-- Regional Settings -->
                <div class="space-y-4">
                    <h3 class="text-xl font-semibold text-white mb-4">Regional Settings</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-white/80 mb-2">Default Currency</label>
                            <select name="default_currency" class="form-select w-full" required>
                                <option value="SAR" {{ ($settings['default_currency'] ?? '') === 'SAR' ? 'selected' : '' }}>SAR (Saudi Riyal)</option>
                                <option value="USD" {{ ($settings['default_currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                                <option value="EUR" {{ ($settings['default_currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                                <option value="GBP" {{ ($settings['default_currency'] ?? '') === 'GBP' ? 'selected' : '' }}>GBP (British Pound)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white/80 mb-2">Timezone</label>
                            <select name="timezone" class="form-select w-full" required>
                                <option value="Asia/Riyadh" {{ ($settings['timezone'] ?? '') === 'Asia/Riyadh' ? 'selected' : '' }}>Asia/Riyadh</option>
                                <option value="UTC" {{ ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                <option value="Europe/London" {{ ($settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Appointment Settings -->
                <div class="space-y-4">
                    <h3 class="text-xl font-semibold text-white mb-4">Appointment Settings</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-white/80 mb-2">Default Appointment Duration (minutes)</label>
                            <input type="number" name="appointment_duration" value="{{ $settings['appointment_duration'] ?? 30 }}"
                                   class="form-input w-full" min="15" max="120" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white/80 mb-2">Max Appointments Per Day</label>
                            <input type="number" name="max_appointments_per_day" value="{{ $settings['max_appointments_per_day'] ?? 20 }}"
                                   class="form-input w-full" min="1" max="100" required>
                        </div>
                    </div>
                </div>

                <!-- Payment Settings -->
                <div class="space-y-4">
                    <h3 class="text-xl font-semibold text-white mb-4">Payment Settings</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-white/80 mb-2">Payment Gateway</label>
                            <select name="payment_gateway" class="form-select w-full" required>
                                <option value="stripe" {{ ($settings['payment_gateway'] ?? '') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                                <option value="paypal" {{ ($settings['payment_gateway'] ?? '') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white/80 mb-2">Platform Fee Percentage</label>
                            <div class="relative">
                                <input type="number" name="platform_fee_percentage" value="{{ $settings['platform_fee_percentage'] ?? 5 }}"
                                       class="form-input w-full pr-8" min="0" max="50" step="0.1" required>
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/60">%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-6 border-t border-white/10">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('systemSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    submitBtn.disabled = true;

    fetch('{{ route("admin.settings.system.update") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Settings saved successfully!', 'success');
        } else {
            showNotification(data.message || 'Error saving settings', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving settings', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
@endsection
