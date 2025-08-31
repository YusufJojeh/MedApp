<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class WalletController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('patient');
    }

    /**
     * Display wallet overview
     */
    public function index()
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patient.profile.create')
                ->with('error', 'Please complete your patient profile first.');
        }

        // Get or create wallet
        $wallet = DB::table('wallets')->where('user_id', $user->id)->first();

        if (!$wallet) {
            // Create wallet if it doesn't exist
            $walletId = DB::table('wallets')->insertGetId([
                'user_id' => $user->id,
                'balance' => 0.00,
                'currency' => 'SAR',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $wallet = DB::table('wallets')->where('id', $walletId)->first();
        }

        // Get recent transactions
        $recentTransactions = DB::table('wallet_transactions')
            ->where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get transaction statistics
        $stats = $this->getTransactionStats($wallet->id);

        // Get payment history
        $recentPayments = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'payments.*',
                'doctors.name as doctor_name',
                'appointments.appointment_date',
                'appointments.appointment_time'
            )
            ->orderBy('payments.created_at', 'desc')
            ->limit(5)
            ->get();

        // Get payment statistics
        $paymentStats = $this->getPaymentStats($patient->id);

        return view('patient.wallet.index', compact(
            'wallet',
            'recentTransactions',
            'stats',
            'recentPayments',
            'paymentStats'
        ));
    }

    /**
     * Get wallet balance and information
     */
    public function getWallet()
    {
        $user = Auth::user();
        $wallet = DB::table('wallets')->where('user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        return response()->json($wallet);
    }

    /**
     * Get wallet transactions
     */
    public function getTransactions(Request $request)
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patient.profile.create')
                ->with('error', 'Please complete your patient profile first.');
        }

        $wallet = DB::table('wallets')->where('user_id', $user->id)->first();

        if (!$wallet) {
            // Create wallet if it doesn't exist
            $walletId = DB::table('wallets')->insertGetId([
                'user_id' => $user->id,
                'balance' => 0.00,
                'currency' => 'SAR',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $wallet = DB::table('wallets')->where('id', $walletId)->first();
        }

        $query = DB::table('wallet_transactions')
            ->where('wallet_id', $wallet->id);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('TYPE', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get transaction statistics
        $stats = $this->getTransactionStats($wallet->id);

        return view('patient.wallet.transactions', compact('transactions', 'wallet', 'stats'));
    }

    /**
     * Get transaction statistics
     */
    public function getStats()
    {
        $user = Auth::user();
        $wallet = DB::table('wallets')->where('user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $stats = $this->getTransactionStats($wallet->id);

        return response()->json($stats);
    }

    /**
     * Add funds to wallet
     */
    public function addFunds(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:credit_card,bank_transfer,wallet',
            'card_number' => 'required_if:payment_method,credit_card|string|max:20',
            'expiry_date' => 'required_if:payment_method,credit_card|string|max:5',
            'cvv' => 'required_if:payment_method,credit_card|string|max:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $wallet = DB::table('wallets')->where('user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        DB::beginTransaction();
        try {
            $balanceBefore = $wallet->balance;
            $balanceAfter = $balanceBefore + $request->amount;

            // Create transaction record
            DB::table('wallet_transactions')->insert([
                'wallet_id' => $wallet->id,
                'TYPE' => 'credit',
                'amount' => $request->amount,
                'reason' => 'Wallet top-up via ' . $request->payment_method,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'meta' => json_encode([
                    'payment_method' => $request->payment_method,
                    'card_number' => $request->payment_method === 'credit_card' ? substr($request->card_number, -4) : null,
                ]),
                'created_at' => now(),
            ]);

            // Update wallet balance
            DB::table('wallets')->where('id', $wallet->id)->update([
                'balance' => $balanceAfter,
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Funds added successfully',
                'new_balance' => $balanceAfter
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error adding funds: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Withdraw funds from wallet
     */
    public function withdrawFunds(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'bank_account' => 'required|string|max:255',
            'reason' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $wallet = DB::table('wallets')->where('user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        if ($wallet->balance < $request->amount) {
            return response()->json(['error' => 'Insufficient balance'], 422);
        }

        DB::beginTransaction();
        try {
            $balanceBefore = $wallet->balance;
            $balanceAfter = $balanceBefore - $request->amount;

            // Create transaction record
            DB::table('wallet_transactions')->insert([
                'wallet_id' => $wallet->id,
                'TYPE' => 'debit',
                'amount' => $request->amount,
                'reason' => $request->reason ?: 'Withdrawal to bank account',
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'meta' => json_encode([
                    'bank_account' => $request->bank_account,
                ]),
                'created_at' => now(),
            ]);

            // Update wallet balance
            DB::table('wallets')->where('id', $wallet->id)->update([
                'balance' => $balanceAfter,
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully',
                'new_balance' => $balanceAfter
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error processing withdrawal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history
     */
    public function getPaymentHistory(Request $request)
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $query = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'payments.*',
                'doctors.name as doctor_name',
                'appointments.appointment_date',
                'appointments.appointment_time'
            );

        // Apply filters
        if ($request->filled('status')) {
            $query->where('payments.STATUS', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payments.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payments.created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payments.created_at', 'desc')
            ->paginate(15);

        return response()->json($payments);
    }



    /**
     * Get monthly spending chart data
     */
    public function getMonthlySpending()
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $monthlySpending = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $spending = DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.patient_id', $patient->id)
                ->where('payments.STATUS', 'succeeded')
                ->whereMonth('payments.created_at', $month->month)
                ->whereYear('payments.created_at', $month->year)
                ->sum('payments.amount');

            $monthlySpending[] = [
                'month' => $month->format('M Y'),
                'spending' => $spending
            ];
        }

        return response()->json($monthlySpending);
    }

    /**
     * Get transaction details
     */
    public function getTransaction($id)
    {
        $user = Auth::user();
        $wallet = DB::table('wallets')->where('user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $transaction = DB::table('wallet_transactions')
            ->where('id', $id)
            ->where('wallet_id', $wallet->id)
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    /**
     * Get payment details
     */
    public function getPayment($id)
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $payment = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->where('payments.id', $id)
            ->where('appointments.patient_id', $patient->id)
            ->select(
                'payments.*',
                'doctors.name as doctor_name',
                'appointments.appointment_date',
                'appointments.appointment_time'
            )
            ->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        return response()->json($payment);
    }

    /**
     * Request refund for a payment
     */
    public function requestRefund(Request $request, $paymentId)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $payment = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->where('payments.id', $paymentId)
            ->where('appointments.patient_id', $patient->id)
            ->where('payments.STATUS', 'succeeded')
            ->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found or cannot be refunded'], 404);
        }

        // Check if appointment is within refund period (e.g., 24 hours before appointment)
        $appointmentDateTime = Carbon::parse($payment->appointment_date . ' ' . $payment->appointment_time);
        if ($appointmentDateTime->diffInHours(now()) < 24) {
            return response()->json(['error' => 'Cannot request refund within 24 hours of appointment'], 400);
        }

        DB::beginTransaction();
        try {
            // Update payment status
            DB::table('payments')->where('id', $paymentId)->update([
                'STATUS' => 'refund_requested',
                'updated_at' => now(),
            ]);

            // Create refund request record (if refund_requests table exists)
            if (DB::getSchemaBuilder()->hasTable('refund_requests')) {
                DB::table('refund_requests')->insert([
                    'payment_id' => $paymentId,
                    'patient_id' => $patient->id,
                    'amount' => $payment->amount,
                    'reason' => $request->reason,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Refund request submitted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error requesting refund: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show payment methods page
     */
    public function paymentMethods()
    {
        $patient = auth()->user()->patient;

        $paymentMethods = DB::table('payment_methods')
            ->where('user_id', auth()->id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('patient.wallet.payment-methods', compact('paymentMethods'));
    }

    /**
     * Store a new payment method
     */
    public function storePaymentMethod(Request $request)
    {
        $request->validate([
            'type' => 'required|in:credit_card,debit_card,bank_account',
            'card_number' => 'required_if:type,credit_card,debit_card|string|max:20',
            'expiry_month' => 'required_if:type,credit_card,debit_card|string|size:2',
            'expiry_year' => 'required_if:type,credit_card,debit_card|string|size:4',
            'cvv' => 'required_if:type,credit_card,debit_card|string|max:4',
            'cardholder_name' => 'required_if:type,credit_card,debit_card|string|max:255',
            'bank_name' => 'required_if:type,bank_account|string|max:255',
            'account_number' => 'required_if:type,bank_account|string|max:50',
            'routing_number' => 'required_if:type,bank_account|string|max:20',
            'account_type' => 'required_if:type,bank_account|in:checking,savings',
            'is_default' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // If setting as default, remove default from other methods
            if ($request->is_default) {
                DB::table('payment_methods')
                    ->where('user_id', auth()->id())
                    ->update(['is_default' => false]);
            }

            $paymentMethodData = [
                'user_id' => auth()->id(),
                'type' => $request->type,
                'is_default' => $request->is_default ?? false,
                'is_verified' => false, // Will be verified through payment gateway
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (in_array($request->type, ['credit_card', 'debit_card'])) {
                // Extract card brand from number
                $cardBrand = $this->getCardBrand($request->card_number);
                $lastFourDigits = substr(preg_replace('/\s+/', '', $request->card_number), -4);

                $paymentMethodData = array_merge($paymentMethodData, [
                    'card_brand' => $cardBrand,
                    'last_four_digits' => $lastFourDigits,
                    'expiry_month' => $request->expiry_month,
                    'expiry_year' => $request->expiry_year,
                    'cardholder_name' => $request->cardholder_name,
                    // In production, these would be encrypted and stored securely
                    'card_number_encrypted' => encrypt($request->card_number),
                    'cvv_encrypted' => encrypt($request->cvv)
                ]);
            } else {
                $paymentMethodData = array_merge($paymentMethodData, [
                    'bank_name' => $request->bank_name,
                    'account_number_encrypted' => encrypt($request->account_number),
                    'routing_number_encrypted' => encrypt($request->routing_number),
                    'account_type' => $request->account_type
                ]);
            }

            $paymentMethodId = DB::table('payment_methods')->insertGetId($paymentMethodData);

            // Verify the payment method with payment gateway
            $verificationResult = $this->verifyPaymentMethod($paymentMethodId);

            if ($verificationResult['success']) {
                DB::table('payment_methods')
                    ->where('id', $paymentMethodId)
                    ->update(['is_verified' => true]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment method added successfully!',
                'payment_method_id' => $paymentMethodId,
                'verified' => $verificationResult['success']
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error adding payment method: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment method details
     */
    public function getPaymentMethod($id)
    {
        $paymentMethod = DB::table('payment_methods')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$paymentMethod) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'payment_method' => $paymentMethod
        ]);
    }

    /**
     * Update payment method
     */
    public function updatePaymentMethod(Request $request, $id)
    {
        $request->validate([
            'cardholder_name' => 'required|string|max:255',
            'expiry_month' => 'required|string|size:2',
            'expiry_year' => 'required|string|size:4',
            'is_default' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Check if payment method belongs to user
            $paymentMethod = DB::table('payment_methods')
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment method not found'
                ], 404);
            }

            // If setting as default, remove default from other methods
            if ($request->is_default) {
                DB::table('payment_methods')
                    ->where('user_id', auth()->id())
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            DB::table('payment_methods')
                ->where('id', $id)
                ->update([
                    'cardholder_name' => $request->cardholder_name,
                    'expiry_month' => $request->expiry_month,
                    'expiry_year' => $request->expiry_year,
                    'is_default' => $request->is_default ?? false,
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment method updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating payment method: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set payment method as default
     */
    public function setDefaultPaymentMethod($id)
    {
        try {
            DB::beginTransaction();

            // Check if payment method belongs to user
            $paymentMethod = DB::table('payment_methods')
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment method not found'
                ], 404);
            }

            // Remove default from all other methods
            DB::table('payment_methods')
                ->where('user_id', auth()->id())
                ->update(['is_default' => false]);

            // Set this method as default
            DB::table('payment_methods')
                ->where('id', $id)
                ->update(['is_default' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Default payment method updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error setting default payment method: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete payment method
     */
    public function deletePaymentMethod($id)
    {
        try {
            DB::beginTransaction();

            // Check if payment method belongs to user
            $paymentMethod = DB::table('payment_methods')
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment method not found'
                ], 404);
            }

            // Check if it's the default method
            if ($paymentMethod->is_default) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete default payment method. Please set another method as default first.'
                ], 400);
            }

            // Check if it's being used in any recent transactions
            $recentTransactions = DB::table('payments')
                ->where('payment_method_id', $id)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            if ($recentTransactions > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete payment method that has been used in recent transactions.'
                ], 400);
            }

            DB::table('payment_methods')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment method deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting payment method: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get card brand from card number
     */
    private function getCardBrand($cardNumber)
    {
        $cardNumber = preg_replace('/\s+/', '', $cardNumber);

        if (preg_match('/^4/', $cardNumber)) {
            return 'Visa';
        } elseif (preg_match('/^5[1-5]/', $cardNumber)) {
            return 'Mastercard';
        } elseif (preg_match('/^3[47]/', $cardNumber)) {
            return 'American Express';
        } elseif (preg_match('/^6/', $cardNumber)) {
            return 'Discover';
        } else {
            return 'Unknown';
        }
    }

    /**
     * Verify payment method with payment gateway
     */
    private function verifyPaymentMethod($paymentMethodId)
    {
        // In production, this would integrate with a real payment gateway
        // For now, we'll simulate a verification process

        try {
            // Simulate API call to payment gateway
            $verificationData = [
                'payment_method_id' => $paymentMethodId,
                'amount' => 1.00, // Small verification amount
                'currency' => 'SAR'
            ];

            // Simulate successful verification (90% success rate)
            $success = rand(1, 10) <= 9;

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Payment method verified successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Payment method verification failed'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Verification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process payment using saved payment method
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'appointment_id' => 'required|exists:appointments,id',
            'description' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Check if payment method belongs to user
            $paymentMethod = DB::table('payment_methods')
                ->where('id', $request->payment_method_id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment method not found'
                ], 404);
            }

            // Check if payment method is verified
            if (!$paymentMethod->is_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment method is not verified'
                ], 400);
            }

            // Process payment through payment gateway
            $paymentResult = $this->processPaymentWithGateway($paymentMethod, $request->amount, $request->description);

            if ($paymentResult['success']) {
                // Create payment record
                $paymentId = DB::table('payments')->insertGetId([
                    'appointment_id' => $request->appointment_id,
                    'payment_method_id' => $request->payment_method_id,
                    'amount' => $request->amount,
                    'currency' => 'SAR',
                    'STATUS' => 'succeeded',
                    'gateway_transaction_id' => $paymentResult['transaction_id'],
                    'description' => $request->description,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update appointment status
                DB::table('appointments')
                    ->where('id', $request->appointment_id)
                    ->update(['payment_status' => 'paid']);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully!',
                    'payment_id' => $paymentId,
                    'transaction_id' => $paymentResult['transaction_id']
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed: ' . $paymentResult['message']
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment with payment gateway
     */
    private function processPaymentWithGateway($paymentMethod, $amount, $description)
    {
        // In production, this would integrate with a real payment gateway
        // For now, we'll simulate a payment process

        try {
            // Simulate API call to payment gateway
            $paymentData = [
                'payment_method_id' => $paymentMethod->id,
                'amount' => $amount,
                'currency' => 'SAR',
                'description' => $description
            ];

            // Simulate payment processing (95% success rate)
            $success = rand(1, 100) <= 95;

            if ($success) {
                return [
                    'success' => true,
                    'transaction_id' => 'TXN_' . strtoupper(uniqid()),
                    'message' => 'Payment processed successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Payment declined by bank'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Payment gateway error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get transaction statistics
     */
    private function getTransactionStats($walletId)
    {
        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        return [
            'total_transactions' => DB::table('wallet_transactions')
                ->where('wallet_id', $walletId)
                ->count(),
            'this_month_transactions' => DB::table('wallet_transactions')
                ->where('wallet_id', $walletId)
                ->whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count(),
            'total_credits' => DB::table('wallet_transactions')
                ->where('wallet_id', $walletId)
                ->where('TYPE', 'credit')
                ->sum('amount'),
            'total_debits' => DB::table('wallet_transactions')
                ->where('wallet_id', $walletId)
                ->where('TYPE', 'debit')
                ->sum('amount'),
        ];
    }

    /**
     * Get payment statistics
     */
    private function getPaymentStats($patientId)
    {
        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        return [
            'total_payments' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.patient_id', $patientId)
                ->count(),
            'total_spent' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.patient_id', $patientId)
                ->where('payments.STATUS', 'succeeded')
                ->sum('payments.amount'),
            'this_month_spent' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.patient_id', $patientId)
                ->where('payments.STATUS', 'succeeded')
                ->whereMonth('payments.created_at', $currentDate->month)
                ->whereYear('payments.created_at', $currentDate->year)
                ->sum('payments.amount'),
            'last_month_spent' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.patient_id', $patientId)
                ->where('payments.STATUS', 'succeeded')
                ->whereMonth('payments.created_at', $lastMonth->month)
                ->whereYear('payments.created_at', $lastMonth->year)
                ->sum('payments.amount'),
            'pending_payments' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.patient_id', $patientId)
                ->where('payments.STATUS', 'pending')
                ->sum('payments.amount'),
        ];
    }

}
