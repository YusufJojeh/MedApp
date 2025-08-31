@extends('layouts.app')

@section('title', 'My Wallet - Patient Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Wallet</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Manage your payments and transactions</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="btn btn-primary" onclick="addFunds()">
                        <i class="fas fa-plus mr-2"></i>
                        Add Funds
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Wallet Balance Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-wallet text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($wallet->balance, 2) }} {{ $wallet->currency }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Current Balance</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-arrow-up text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_credits'] ?? 0, 2) }} {{ $wallet->currency }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Deposits</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-arrow-down text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_debits'] ?? 0, 2) }} {{ $wallet->currency }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Withdrawals</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-credit-card text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($paymentStats['total_spent'] ?? 0, 2) }} {{ $wallet->currency }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Spent</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-4 mb-8">
            <button class="btn btn-outline" onclick="addFunds()">
                <i class="fas fa-plus mr-2"></i>
                Add Funds
            </button>
            <button class="btn btn-outline" onclick="withdrawFunds()">
                <i class="fas fa-minus mr-2"></i>
                Withdraw
            </button>
            <button class="btn btn-outline" onclick="exportTransactions()">
                <i class="fas fa-download mr-2"></i>
                Export
            </button>
            <a href="{{ route('patient.wallet.payment-methods') }}" class="btn btn-outline">
                <i class="fas fa-credit-card mr-2"></i>
                Payment Methods
            </a>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Transaction History -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Transaction History</h3>
                            <div class="flex items-center space-x-4">
                                <select id="transactionFilter" class="form-select" onchange="filterTransactions()">
                                    <option value="">All Transactions</option>
                                    <option value="credit">Deposits</option>
                                    <option value="debit">Withdrawals</option>
                                    <option value="adjustment">Adjustments</option>
                                </select>
                                <button class="btn btn-outline btn-sm" onclick="loadMoreTransactions()">
                                    <i class="fas fa-sync-alt mr-2"></i>
                                    Refresh
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div id="transactionsList" class="space-y-4">
                            @if($recentTransactions->count() > 0)
                                @foreach($recentTransactions as $transaction)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-4
                                                @if($transaction->TYPE == 'credit') bg-green-100 dark:bg-green-900
                                                @elseif($transaction->TYPE == 'debit') bg-red-100 dark:bg-red-900
                                                @else bg-gray-100 dark:bg-gray-600 @endif">
                                                <i class="fas
                                                    @if($transaction->TYPE == 'credit') fa-arrow-up text-green-600 dark:text-green-400
                                                    @elseif($transaction->TYPE == 'debit') fa-arrow-down text-red-600 dark:text-red-400
                                                    @else fa-exchange-alt text-gray-600 dark:text-gray-400 @endif">
                                                </i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">
                                                    {{ ucfirst($transaction->TYPE) }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $transaction->reason }}
                                                </p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                                    {{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y g:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-lg
                                                @if($transaction->TYPE == 'credit') text-green-600 dark:text-green-400
                                                @elseif($transaction->TYPE == 'debit') text-red-600 dark:text-red-400
                                                @else text-gray-600 dark:text-gray-400 @endif">
                                                @if($transaction->TYPE == 'credit') + @else - @endif
                                                {{ number_format($transaction->amount, 2) }} {{ $wallet->currency }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Balance: {{ number_format($transaction->balance_after, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-wallet text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No transactions yet</h3>
                                    <p class="text-gray-500 dark:text-gray-400">Start by adding funds to your wallet</p>
                                </div>
                            @endif
                        </div>

                        @if($recentTransactions->count() > 0)
                            <div class="mt-6 text-center">
                                <button class="btn btn-outline" onclick="loadMoreTransactions()">
                                    <i class="fas fa-plus mr-2"></i>
                                    Load More Transactions
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Recent Payments -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Payments</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @if($recentPayments->count() > 0)
                                @foreach($recentPayments as $payment)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">
                                                {{ $payment->doctor_name }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($payment->appointment_date)->format('M d, Y') }}
                                            </p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ \Carbon\Carbon::parse($payment->appointment_time)->format('g:i A') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-gray-900 dark:text-white">
                                                {{ number_format($payment->amount, 2) }} {{ $payment->currency ?? 'SAR' }}
                                            </p>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($payment->STATUS == 'succeeded') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($payment->STATUS == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @elseif($payment->STATUS == 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                                {{ ucfirst($payment->STATUS) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <p class="text-gray-500 dark:text-gray-400">No payments yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Quick Stats</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">This Month Spent:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ number_format($paymentStats['this_month_spent'] ?? 0, 2) }} {{ $wallet->currency }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Last Month Spent:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ number_format($paymentStats['last_month_spent'] ?? 0, 2) }} {{ $wallet->currency }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total Transactions:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $stats['total_transactions'] ?? 0 }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Pending Payments:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ number_format($paymentStats['pending_payments'] ?? 0, 2) }} {{ $wallet->currency }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Funds Modal -->
<div id="addFundsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add Funds</h3>
                <button onclick="closeModal('addFundsModal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addFundsForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount</label>
                    <input type="number" name="amount" step="0.01" min="1" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                    <select name="payment_method" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Select payment method</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="wallet">Digital Wallet</option>
                    </select>
                </div>
                <div id="creditCardFields" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Card Number</label>
                        <input type="text" name="card_number" maxlength="20"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Date</label>
                            <input type="text" name="expiry_date" placeholder="MM/YY" maxlength="5"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CVV</label>
                            <input type="text" name="cvv" maxlength="4"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal('addFundsModal')" class="btn btn-outline flex-1">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary flex-1">
                        Add Funds
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Withdraw Funds Modal -->
<div id="withdrawFundsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Withdraw Funds</h3>
                <button onclick="closeModal('withdrawFundsModal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="withdrawFundsForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount</label>
                    <input type="number" name="amount" step="0.01" min="1" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bank Account</label>
                    <input type="text" name="bank_account" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="Enter your bank account number">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason (Optional)</label>
                    <textarea name="reason" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                              placeholder="Why are you withdrawing funds?"></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal('withdrawFundsModal')" class="btn btn-outline flex-1">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary flex-1">
                        Withdraw
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Modal functions
    function addFunds() {
        document.getElementById('addFundsModal').classList.remove('hidden');
    }

    function withdrawFunds() {
        document.getElementById('withdrawFundsModal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function exportTransactions() {
        // Implementation for exporting transactions
        alert('Export functionality will be implemented soon');
    }



    function filterTransactions() {
        const filter = document.getElementById('transactionFilter').value;
        // Implementation for filtering transactions
        console.log('Filtering transactions by:', filter);
    }

    function loadMoreTransactions() {
        // Implementation for loading more transactions
        console.log('Loading more transactions...');
    }

    // Form submissions
    document.getElementById('addFundsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route("patient.wallet.add-funds") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Funds added successfully!');
                location.reload();
            } else {
                alert(data.message || 'Error adding funds');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding funds');
        });
    });

    document.getElementById('withdrawFundsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route("patient.wallet.withdraw-funds") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Withdrawal request submitted successfully!');
                location.reload();
            } else {
                alert(data.message || 'Error processing withdrawal');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error processing withdrawal');
        });
    });

    // Show/hide credit card fields based on payment method
    document.querySelector('select[name="payment_method"]').addEventListener('change', function() {
        const creditCardFields = document.getElementById('creditCardFields');
        if (this.value === 'credit_card') {
            creditCardFields.classList.remove('hidden');
        } else {
            creditCardFields.classList.add('hidden');
        }
    });

    // Close modals when clicking outside
    document.getElementById('addFundsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal('addFundsModal');
        }
    });

    document.getElementById('withdrawFundsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal('withdrawFundsModal');
        }
    });
</script>
@endpush
