@extends('layouts.app')

@section('title', 'Transaction History - Patient Wallet')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Transaction History</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">View all your wallet transactions</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('patient.wallet.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Wallet
                    </a>
                    <button class="btn btn-primary" onclick="exportTransactions()">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Transaction Type</label>
                    <select id="typeFilter" class="form-select w-full" onchange="filterTransactions()">
                        <option value="">All Types</option>
                        <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Deposits</option>
                        <option value="debit" {{ request('type') === 'debit' ? 'selected' : '' }}>Withdrawals</option>
                        <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustments</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                    <input type="date" id="dateFromFilter" class="form-input w-full" value="{{ request('date_from') }}" onchange="filterTransactions()">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                    <input type="date" id="dateToFilter" class="form-input w-full" value="{{ request('date_to') }}" onchange="filterTransactions()">
                </div>
                <div class="flex items-end">
                    <button onclick="clearFilters()" class="btn btn-outline w-full">
                        <i class="fas fa-times mr-2"></i>
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Transactions List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">All Transactions</h3>
            </div>
            <div class="p-6">
                @if($transactions->count() > 0)
                    <div class="space-y-4">
                        @foreach($transactions as $transaction)
                            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $transaction->TYPE === 'credit' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                        <i class="fas {{ $transaction->TYPE === 'credit' ? 'fa-plus' : 'fa-minus' }} text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $transaction->reason }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('M j, Y g:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-lg {{ $transaction->TYPE === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->TYPE === 'credit' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Balance: ${{ number_format($transaction->balance_after, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-receipt text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Transactions Found</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">You haven't made any transactions yet</p>
                        <a href="{{ route('patient.wallet.index') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>
                            Add Funds
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function filterTransactions() {
        const type = document.getElementById('typeFilter').value;
        const dateFrom = document.getElementById('dateFromFilter').value;
        const dateTo = document.getElementById('dateToFilter').value;

        const params = new URLSearchParams();
        if (type) params.append('type', type);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);

        window.location.href = `{{ route('patient.wallet.transactions') }}?${params.toString()}`;
    }

    function clearFilters() {
        window.location.href = '{{ route('patient.wallet.transactions') }}';
    }

    function exportTransactions() {
        const type = document.getElementById('typeFilter').value;
        const dateFrom = document.getElementById('dateFromFilter').value;
        const dateTo = document.getElementById('dateToFilter').value;

        const params = new URLSearchParams();
        params.append('export', '1');
        if (type) params.append('type', type);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);

                 window.location.href = `{{ route('patient.wallet.transactions') }}?${params.toString()}`;
     }
 </script>
 @endpush
                showNotification('Error loading transactions', 'error');
            });
    }

    function displayTransactions(transactions) {
        const container = document.getElementById('transactionsList');

        if (transactions.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-wallet text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No transactions found</h3>
                    <p class="text-gray-500 dark:text-gray-400">Try adjusting your filters</p>
                </div>
            `;
            return;
        }

        container.innerHTML = transactions.map(transaction => `
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center mr-4
                        ${transaction.TYPE === 'credit' ? 'bg-green-100 dark:bg-green-900' :
                          transaction.TYPE === 'debit' ? 'bg-red-100 dark:bg-red-900' :
                          'bg-gray-100 dark:bg-gray-600'}">
                        <i class="fas
                            ${transaction.TYPE === 'credit' ? 'fa-arrow-up text-green-600 dark:text-green-400' :
                              transaction.TYPE === 'debit' ? 'fa-arrow-down text-red-600 dark:text-red-400' :
                              'fa-exchange-alt text-gray-600 dark:text-gray-400'}">
                        </i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">
                            ${transaction.TYPE.charAt(0).toUpperCase() + transaction.TYPE.slice(1)}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            ${transaction.reason}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            ${new Date(transaction.created_at).toLocaleDateString()} at
                            ${new Date(transaction.created_at).toLocaleTimeString()}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-lg
                        ${transaction.TYPE === 'credit' ? 'text-green-600 dark:text-green-400' :
                          transaction.TYPE === 'debit' ? 'text-red-600 dark:text-red-400' :
                          'text-gray-600 dark:text-gray-400'}">
                        ${transaction.TYPE === 'credit' ? '+' : '-'}
                        ${parseFloat(transaction.amount).toFixed(2)} SAR
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Balance: ${parseFloat(transaction.balance_after).toFixed(2)}
                    </p>
                </div>
            </div>
        `).join('');
    }

    function displayPagination(data) {
        const container = document.getElementById('pagination');

        if (!data.links || data.links.length <= 3) {
            container.innerHTML = '';
            return;
        }

        let paginationHtml = '<div class="flex items-center justify-between">';

        // Previous button
        if (data.prev_page_url) {
            paginationHtml += `
                <button onclick="loadTransactions(${data.current_page - 1})"
                        class="btn btn-outline btn-sm">
                    <i class="fas fa-chevron-left mr-2"></i>
                    Previous
                </button>
            `;
        } else {
            paginationHtml += '<div></div>';
        }

        // Page numbers
        paginationHtml += '<div class="flex space-x-2">';
        data.links.forEach(link => {
            if (link.url && !link.label.includes('Previous') && !link.label.includes('Next')) {
                const isActive = link.active ? 'btn-primary' : 'btn-outline';
                paginationHtml += `
                    <button onclick="loadTransactions(${link.label})"
                            class="btn ${isActive} btn-sm">
                        ${link.label}
                    </button>
                `;
            }
        });
        paginationHtml += '</div>';

        // Next button
        if (data.next_page_url) {
            paginationHtml += `
                <button onclick="loadTransactions(${data.current_page + 1})"
                        class="btn btn-outline btn-sm">
                    Next
                    <i class="fas fa-chevron-right ml-2"></i>
                </button>
            `;
        } else {
            paginationHtml += '<div></div>';
        }

        paginationHtml += '</div>';
        container.innerHTML = paginationHtml;
    }

    function filterTransactions() {
        filters = {
            type: document.getElementById('typeFilter').value,
            date_from: document.getElementById('dateFromFilter').value,
            date_to: document.getElementById('dateToFilter').value
        };

        // Remove empty filters
        Object.keys(filters).forEach(key => {
            if (!filters[key]) delete filters[key];
        });

        loadTransactions(1);
    }

    function clearFilters() {
        document.getElementById('typeFilter').value = '';
        document.getElementById('dateFromFilter').value = '';
        document.getElementById('dateToFilter').value = '';
        filters = {};
        loadTransactions(1);
    }

    function exportTransactions() {
        const queryParams = new URLSearchParams(filters);
        window.open(`{{ route('patient.wallet.export') }}?type=transactions&${queryParams}`, '_blank');
    }

    function showNotification(message, type = 'info') {
        // Simple notification implementation
        alert(message);
    }
</script>
@endpush
