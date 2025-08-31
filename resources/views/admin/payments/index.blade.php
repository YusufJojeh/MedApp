@extends('layouts.app')

@section('title', 'Payment Management - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payment Management</h1>
                    <p class="text-gray-600 dark:text-gray-400">View and manage all payment transactions</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                    <button class="btn btn-outline" onclick="exportPayments()">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Total Payments</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $payments->total() }}</p>
                    </div>
                    <i class="fas fa-credit-card text-blue-500 text-2xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalRevenue, 2) }}</p>
                    </div>
                    <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Successful Payments</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $successfulPayments }}</p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Failed Payments</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $failedPayments }}</p>
                    </div>
                    <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Methods</option>
                        <option value="wallet" {{ request('payment_method') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                        <option value="pay_on_site" {{ request('payment_method') == 'pay_on_site' ? 'selected' : '' }}>Pay on Site</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <div class="md:col-span-4 flex justify-end space-x-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline">
                        <i class="fas fa-times mr-2"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Payments Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Transactions</h3>
            </div>

            @if($payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Transaction
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Patient
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Doctor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Appointment
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Method
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            #{{ $payment->transaction_id ?? $payment->id }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ ucfirst($payment->provider) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $payment->first_name }} {{ $payment->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $payment->email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            Dr. {{ $payment->doctor_name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($payment->appointment_date)->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($payment->appointment_time)->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            ${{ number_format($payment->amount, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 text-xs rounded-full
                                            @if($payment->provider === 'stripe') bg-green-100 text-green-800
                                            @elseif($payment->provider === 'paypal') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($payment->provider) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 text-xs rounded-full
                                            @if($payment->STATUS === 'succeeded') bg-green-100 text-green-800
                                            @elseif($payment->STATUS === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($payment->STATUS === 'failed') bg-red-100 text-red-800
                                            @elseif($payment->STATUS === 'refunded') bg-gray-100 text-gray-800
                                            @elseif($payment->STATUS === 'canceled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($payment->STATUS) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $payments->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-credit-card text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-gray-900 dark:text-white text-lg font-semibold mb-2">No Payments Found</h3>
                    <p class="text-gray-600 dark:text-gray-400">No payment transactions match your current filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function exportPayments() {
    // Get current filters
    const urlParams = new URLSearchParams(window.location.search);
    const exportUrl = `{{ route('admin.payments.export') }}?${urlParams.toString()}`;

    // Create a temporary link and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = 'payments-export.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
