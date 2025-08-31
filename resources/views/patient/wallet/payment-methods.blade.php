@extends('layouts.app')

@section('title', 'Payment Methods - Patient Wallet')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Payment Methods</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Manage your payment cards and methods</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('patient.wallet.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Wallet
                    </a>
                    <button class="btn btn-primary" onclick="addPaymentMethod()">
                        <i class="fas fa-plus mr-2"></i>
                        Add Payment Method
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Payment Methods Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-credit-card text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $paymentMethods->count() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Saved Cards</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $paymentMethods->where('is_default', true)->count() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Default Method</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $paymentMethods->where('is_verified', true)->count() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Verified Methods</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Your Payment Methods</h3>
            </div>
            <div class="p-6">
                @if($paymentMethods->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($paymentMethods as $method)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-6 hover:shadow-lg transition-shadow">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3
                                            @if($method->type == 'credit_card') bg-blue-100 dark:bg-blue-900
                                            @elseif($method->type == 'debit_card') bg-green-100 dark:bg-green-900
                                            @elseif($method->type == 'bank_account') bg-purple-100 dark:bg-purple-900
                                            @else bg-gray-100 dark:bg-gray-700 @endif">
                                            <i class="fas
                                                @if($method->type == 'credit_card') fa-credit-card text-blue-600 dark:text-blue-400
                                                @elseif($method->type == 'debit_card') fa-credit-card text-green-600 dark:text-green-400
                                                @elseif($method->type == 'bank_account') fa-university text-purple-600 dark:text-purple-400
                                                @else fa-credit-card text-gray-600 dark:text-gray-400 @endif">
                                            </i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">
                                                {{ ucfirst(str_replace('_', ' ', $method->type)) }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $method->card_brand ?? $method->bank_name ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($method->is_default)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                <i class="fas fa-star mr-1"></i>
                                                Default
                                            </span>
                                        @endif
                                        @if($method->is_verified)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                <i class="fas fa-check mr-1"></i>
                                                Verified
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Card Number:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            **** **** **** {{ $method->last_four_digits }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Expires:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $method->expiry_month }}/{{ $method->expiry_year }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Added:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($method->created_at)->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex space-x-2">
                                    @if(!$method->is_default)
                                        <button onclick="setDefaultMethod({{ $method->id }})" class="btn btn-outline btn-sm flex-1">
                                            <i class="fas fa-star mr-1"></i>
                                            Set Default
                                        </button>
                                    @endif
                                    <button onclick="editPaymentMethod({{ $method->id }})" class="btn btn-outline btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deletePaymentMethod({{ $method->id }})" class="btn btn-outline btn-sm text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-credit-card text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No payment methods yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">Add your first payment method to start making payments</p>
                        <button onclick="addPaymentMethod()" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>
                            Add Payment Method
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Security Information -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Security & Privacy</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-shield-alt text-green-600 dark:text-green-400 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-1">Secure Storage</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Your payment information is encrypted and stored securely using industry-standard protocols.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-lock text-blue-600 dark:text-blue-400 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-1">PCI Compliant</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">We follow PCI DSS standards to ensure your payment data is handled securely.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-eye-slash text-purple-600 dark:text-purple-400 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-1">Masked Display</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Only the last 4 digits of your card are displayed for security purposes.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-bell text-yellow-600 dark:text-yellow-400 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-1">Transaction Alerts</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Receive notifications for all transactions made with your payment methods.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Method Modal -->
<div id="addPaymentMethodModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add Payment Method</h3>
                <button onclick="closeModal('addPaymentMethodModal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addPaymentMethodForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Type</label>
                    <select name="type" required onchange="togglePaymentFields()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Select payment type</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="bank_account">Bank Account</option>
                    </select>
                </div>

                <!-- Credit/Debit Card Fields -->
                <div id="cardFields" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Card Number</label>
                        <input type="text" name="card_number" maxlength="20" placeholder="1234 5678 9012 3456"
                               data-originally-required="true"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Month</label>
                            <select name="expiry_month" required data-originally-required="true"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Select Month</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Year</label>
                            <select name="expiry_year" required data-originally-required="true"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Select Year</option>
                                @for($i = date('Y'); $i <= date('Y') + 20; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CVV</label>
                        <input type="text" name="cvv" maxlength="4" placeholder="123"
                               data-originally-required="true"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cardholder Name</label>
                        <input type="text" name="cardholder_name" placeholder="John Doe"
                               data-originally-required="true"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>

                <!-- Bank Account Fields -->
                <div id="bankFields" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bank Name</label>
                        <input type="text" name="bank_name" placeholder="Bank Name"
                               data-originally-required="true"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Number</label>
                        <input type="text" name="account_number" placeholder="Account Number"
                               data-originally-required="true"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Routing Number</label>
                        <input type="text" name="routing_number" placeholder="Routing Number"
                               data-originally-required="true"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Type</label>
                        <select name="account_type" data-originally-required="true"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Select Account Type</option>
                            <option value="checking">Checking</option>
                            <option value="savings">Savings</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" class="mr-2">
                    <label for="is_default" class="text-sm text-gray-700 dark:text-gray-300">Set as default payment method</label>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal('addPaymentMethodModal')" class="btn btn-outline flex-1">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary flex-1">
                        Add Payment Method
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Payment Method Modal -->
<div id="editPaymentMethodModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Payment Method</h3>
                <button onclick="closeModal('editPaymentMethodModal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editPaymentMethodForm" class="space-y-4">
                @csrf
                <input type="hidden" name="payment_method_id" id="edit_payment_method_id">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cardholder Name</label>
                    <input type="text" name="cardholder_name" id="edit_cardholder_name"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Month</label>
                        <select name="expiry_month" id="edit_expiry_month" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Select Month</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Year</label>
                        <select name="expiry_year" id="edit_expiry_year" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Select Year</option>
                            @for($i = date('Y'); $i <= date('Y') + 20; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="edit_is_default" class="mr-2">
                    <label for="edit_is_default" class="text-sm text-gray-700 dark:text-gray-300">Set as default payment method</label>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal('editPaymentMethodModal')" class="btn btn-outline flex-1">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary flex-1">
                        Update Payment Method
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function addPaymentMethod() {
        document.getElementById('addPaymentMethodModal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function togglePaymentFields() {
        const type = document.querySelector('select[name="type"]').value;
        const cardFields = document.getElementById('cardFields');
        const bankFields = document.getElementById('bankFields');

        // Hide all fields first
        cardFields.classList.add('hidden');
        bankFields.classList.add('hidden');

        // Remove required attributes from all fields
        const cardInputs = cardFields.querySelectorAll('input, select');
        const bankInputs = bankFields.querySelectorAll('input, select');

        cardInputs.forEach(input => {
            if (input.hasAttribute('data-originally-required')) {
                input.required = false;
            }
        });

        bankInputs.forEach(input => {
            if (input.hasAttribute('data-originally-required')) {
                input.required = false;
            }
        });

        // Show and set required attributes for selected type
        if (type === 'credit_card' || type === 'debit_card') {
            cardFields.classList.remove('hidden');
            cardInputs.forEach(input => {
                if (input.hasAttribute('data-originally-required')) {
                    input.required = true;
                }
            });
        } else if (type === 'bank_account') {
            bankFields.classList.remove('hidden');
            bankInputs.forEach(input => {
                if (input.hasAttribute('data-originally-required')) {
                    input.required = true;
                }
            });
        }
    }

    function editPaymentMethod(methodId) {
        // Load payment method data
        fetch(`{{ route('patient.wallet.index') }}/payment-methods/${methodId}`)
            .then(response => response.json())
            .then(data => {
                if (data.payment_method) {
                    const method = data.payment_method;
                    document.getElementById('edit_payment_method_id').value = method.id;
                    document.getElementById('edit_cardholder_name').value = method.cardholder_name || '';
                    document.getElementById('edit_expiry_month').value = method.expiry_month || '';
                    document.getElementById('edit_expiry_year').value = method.expiry_year || '';
                    document.getElementById('edit_is_default').checked = method.is_default;
                    document.getElementById('editPaymentMethodModal').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading payment method details');
            });
    }

    function setDefaultMethod(methodId) {
        if (confirm('Set this as your default payment method?')) {
            fetch(`{{ route('patient.wallet.index') }}/payment-methods/${methodId}/default`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error setting default method');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error setting default method');
            });
        }
    }

    function deletePaymentMethod(methodId) {
        if (confirm('Are you sure you want to delete this payment method? This action cannot be undone.')) {
            fetch(`{{ route('patient.wallet.index') }}/payment-methods/${methodId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error deleting payment method');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting payment method');
            });
        }
    }

        // Form submissions
    document.getElementById('addPaymentMethodForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate form before submission
        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }

        const formData = new FormData(this);

        // Remove empty values for conditional fields
        const type = formData.get('type');
        if (type !== 'credit_card' && type !== 'debit_card') {
            formData.delete('card_number');
            formData.delete('expiry_month');
            formData.delete('expiry_year');
            formData.delete('cvv');
            formData.delete('cardholder_name');
        }
        if (type !== 'bank_account') {
            formData.delete('bank_name');
            formData.delete('account_number');
            formData.delete('routing_number');
            formData.delete('account_type');
        }

        fetch('{{ route("patient.wallet.payment-methods.store") }}', {
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
                alert('Payment method added successfully!');
                location.reload();
            } else {
                alert(data.message || 'Error adding payment method');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding payment method');
        });
    });

        document.getElementById('editPaymentMethodForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate form before submission
        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }

        const formData = new FormData(this);
        const methodId = formData.get('payment_method_id');

        fetch(`{{ route("patient.wallet.index") }}/payment-methods/${methodId}`, {
            method: 'PUT',
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
                alert('Payment method updated successfully!');
                location.reload();
            } else {
                alert(data.message || 'Error updating payment method');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating payment method');
        });
    });

    // Close modals when clicking outside
    document.getElementById('addPaymentMethodModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal('addPaymentMethodModal');
        }
    });

    document.getElementById('editPaymentMethodModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal('editPaymentMethodModal');
        }
    });

    // Initialize form on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize card number formatting
        const cardNumberInput = document.querySelector('input[name="card_number"]');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                e.target.value = formattedValue;
            });
        }

        // Initialize form validation
        togglePaymentFields();
    });
</script>
@endpush
