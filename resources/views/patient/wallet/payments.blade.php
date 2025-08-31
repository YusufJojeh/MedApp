@extends('layouts.app')

@section('title', 'Payment History - Patient Wallet')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Payment History</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">View all your appointment payments</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('patient.wallet.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Wallet
                    </a>
                    <button class="btn btn-primary" onclick="exportPayments()">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Payment Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-credit-card text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($paymentStats['total_spent'] ?? 0, 2) }} SAR</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Spent</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $paymentStats['total_payments'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Payments</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($paymentStats['pending_payments'] ?? 0, 2) }} SAR</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pending Payments</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($paymentStats['this_month_spent'] ?? 0, 2) }} SAR</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">This Month</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Status</label>
                    <select id="statusFilter" class="form-select w-full" onchange="filterPayments()">
                        <option value="">All Status</option>
                        <option value="succeeded">Succeeded</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                    <input type="date" id="dateFromFilter" class="form-input w-full" onchange="filterPayments()">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                    <input type="date" id="dateToFilter" class="form-input w-full" onchange="filterPayments()">
                </div>
                <div class="flex items-end">
                    <button onclick="clearFilters()" class="btn btn-outline w-full">
                        <i class="fas fa-times mr-2"></i>
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Payments List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">All Payments</h3>
            </div>
            <div class="p-6">
                <div id="paymentsList" class="space-y-4">
                    <!-- Payments will be loaded here -->
                </div>
                <div id="pagination" class="mt-6">
                    <!-- Pagination will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="paymentDetails">
                <!-- Payment details will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let filters = {};

    // Load payments on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadPayments();
    });

    function loadPayments(page = 1) {
        currentPage = page;
        const queryParams = new URLSearchParams({
            page: page,
            ...filters
        });

        fetch(`{{ route('patient.wallet.payment-history') }}?${queryParams}`)
            .then(response => response.json())
            .then(data => {
                displayPayments(data.data);
                displayPagination(data);
            })
            .catch(error => {
                console.error('Error loading payments:', error);
                showNotification('Error loading payments', 'error');
            });
    }

    function displayPayments(payments) {
        const container = document.getElementById('paymentsList');

        if (payments.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-credit-card text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No payments found</h3>
                    <p class="text-gray-500 dark:text-gray-400">Try adjusting your filters</p>
                </div>
            `;
            return;
        }

        container.innerHTML = payments.map(payment => `
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center mr-4 bg-blue-100 dark:bg-blue-900">
                        <i class="fas fa-user-md text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">
                            ${payment.doctor_name}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            ${new Date(payment.appointment_date).toLocaleDateString()} at
                            ${new Date(payment.appointment_time).toLocaleTimeString()}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Payment ID: ${payment.id}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-lg text-gray-900 dark:text-white">
                        ${parseFloat(payment.amount).toFixed(2)} ${payment.currency || 'SAR'}
                    </p>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                        ${payment.STATUS === 'succeeded' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                          payment.STATUS === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                          payment.STATUS === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                          'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'}">
                        ${payment.STATUS.charAt(0).toUpperCase() + payment.STATUS.slice(1)}
                    </span>
                    <div class="mt-2">
                        <button onclick="viewPaymentDetails(${payment.id})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                            View Details
                        </button>
                    </div>
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
                <button onclick="loadPayments(${data.current_page - 1})"
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
                    <button onclick="loadPayments(${link.label})"
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
                <button onclick="loadPayments(${data.current_page + 1})"
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

    function filterPayments() {
        filters = {
            status: document.getElementById('statusFilter').value,
            date_from: document.getElementById('dateFromFilter').value,
            date_to: document.getElementById('dateToFilter').value
        };

        // Remove empty filters
        Object.keys(filters).forEach(key => {
            if (!filters[key]) delete filters[key];
        });

        loadPayments(1);
    }

    function clearFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('dateFromFilter').value = '';
        document.getElementById('dateToFilter').value = '';
        filters = {};
        loadPayments(1);
    }

    function viewPaymentDetails(paymentId) {
        fetch(`{{ route('patient.wallet.index') }}/payments/${paymentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.payment) {
                    const payment = data.payment;
                    const detailsHtml = `
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">Payment #${payment.id}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">${payment.provider || 'N/A'}</p>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Doctor</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">${payment.doctor_name}</p>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Appointment</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    ${new Date(payment.appointment_date).toLocaleDateString()} at
                                    ${new Date(payment.appointment_time).toLocaleTimeString()}
                                </p>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Amount</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">${parseFloat(payment.amount).toFixed(2)} ${payment.currency || 'SAR'}</p>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Status</h5>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    ${payment.STATUS === 'succeeded' ? 'bg-green-100 text-green-800' :
                                      payment.STATUS === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                      payment.STATUS === 'failed' ? 'bg-red-100 text-red-800' :
                                      'bg-gray-100 text-gray-800'}">
                                    ${payment.STATUS.charAt(0).toUpperCase() + payment.STATUS.slice(1)}
                                </span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Date</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">${new Date(payment.created_at).toLocaleString()}</p>
                            </div>
                        </div>
                    `;
                    document.getElementById('paymentDetails').innerHTML = detailsHtml;
                    document.getElementById('paymentModal').classList.remove('hidden');
                } else {
                    alert('Error loading payment details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading payment details');
            });
    }

    function closeModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    function exportPayments() {
        const queryParams = new URLSearchParams(filters);
        window.open(`{{ route('patient.wallet.export') }}?type=payments&${queryParams}`, '_blank');
    }

    function showNotification(message, type = 'info') {
        // Simple notification implementation
        alert(message);
    }

    // Close modal when clicking outside
    document.getElementById('paymentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endpush
