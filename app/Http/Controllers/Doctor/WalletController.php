<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Traits\ExportTrait;

class WalletController extends Controller
{
    use ExportTrait;
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    /**
     * Display wallet overview
     */
    public function index()
    {
        $userId = Auth::id();

        $wallet = DB::table('wallets')
            ->where('user_id', $userId)
            ->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        // Get recent transactions
        $recentTransactions = DB::table('wallet_transactions')
            ->where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get transaction statistics
        $stats = $this->getTransactionStats($wallet->id);

        return view('doctor.wallet.index', compact('wallet', 'recentTransactions', 'stats'));
    }

    /**
     * Get wallet balance and information
     */
    public function getWallet()
    {
        $userId = Auth::id();

        $wallet = DB::table('wallets')
            ->where('user_id', $userId)
            ->first();

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
        $userId = Auth::id();
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $query = DB::table('wallet_transactions')
            ->where('wallet_id', $wallet->id);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($transactions);
    }

    /**
     * Get transaction statistics
     */
    public function getStats()
    {
        $userId = Auth::id();
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $stats = $this->getTransactionStats($wallet->id);

        return response()->json($stats);
    }

    /**
     * Request withdrawal
     */
    public function requestWithdrawal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'bank_account' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        if ($request->amount > $wallet->balance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        // Check minimum withdrawal amount
        if ($request->amount < 50) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum withdrawal amount is 50 SAR'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Create withdrawal request
            DB::table('wallet_transactions')->insert([
                'wallet_id' => $wallet->id,
                'type' => 'withdrawal',
                'amount' => $request->amount,
                'description' => 'Withdrawal request',
                'status' => 'pending',
                'metadata' => json_encode([
                    'bank_account' => $request->bank_account,
                    'bank_name' => $request->bank_name,
                    'account_holder' => $request->account_holder,
                    'notes' => $request->notes
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update wallet balance
            DB::table('wallets')->where('id', $wallet->id)->update([
                'balance' => $wallet->balance - $request->amount,
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error submitting withdrawal request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get earnings history
     */
    public function getEarnings(Request $request)
    {
        $userId = Auth::id();
        $doctor = DB::table('doctors')->where('user_id', $userId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $query = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->where('payments.STATUS', 'succeeded')
            ->select(
                'payments.*',
                'appointments.appointment_date',
                'appointments.appointment_time'
            );

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('payments.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payments.created_at', '<=', $request->date_to);
        }

        $earnings = $query->orderBy('payments.created_at', 'desc')
            ->paginate(15);

        return response()->json($earnings);
    }

    /**
     * Get earnings statistics
     */
    public function getEarningsStats()
    {
        $userId = Auth::id();
        $doctor = DB::table('doctors')->where('user_id', $userId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $stats = [
            'total_earnings' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.doctor_id', $doctor->id)
                ->where('payments.STATUS', 'succeeded')
                ->sum('payments.amount'),
            'this_month_earnings' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.doctor_id', $doctor->id)
                ->where('payments.STATUS', 'succeeded')
                ->whereMonth('payments.created_at', $currentDate->month)
                ->whereYear('payments.created_at', $currentDate->year)
                ->sum('payments.amount'),
            'last_month_earnings' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.doctor_id', $doctor->id)
                ->where('payments.STATUS', 'succeeded')
                ->whereMonth('payments.created_at', $lastMonth->month)
                ->whereYear('payments.created_at', $lastMonth->year)
                ->sum('payments.amount'),
            'pending_earnings' => DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.doctor_id', $doctor->id)
                ->where('payments.STATUS', 'pending')
                ->sum('payments.amount'),
        ];

        // Calculate growth
        $stats['earnings_growth'] = $stats['last_month_earnings'] > 0
            ? (($stats['this_month_earnings'] - $stats['last_month_earnings']) / $stats['last_month_earnings']) * 100
            : 0;

        return response()->json($stats);
    }

    /**
     * Get monthly earnings chart data
     */
    public function getMonthlyEarnings()
    {
        $userId = Auth::id();
        $doctor = DB::table('doctors')->where('user_id', $userId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $monthlyEarnings = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $earnings = DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.doctor_id', $doctor->id)
                ->where('payments.STATUS', 'succeeded')
                ->whereMonth('payments.created_at', $month->month)
                ->whereYear('payments.created_at', $month->year)
                ->sum('payments.amount');

            $monthlyEarnings[] = [
                'month' => $month->format('M Y'),
                'earnings' => $earnings
            ];
        }

        return response()->json($monthlyEarnings);
    }

    /**
     * Get withdrawal history
     */
    public function getWithdrawals(Request $request)
    {
        $userId = Auth::id();
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $query = DB::table('wallet_transactions')
            ->where('wallet_id', $wallet->id)
            ->where('type', 'withdrawal');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $withdrawals = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($withdrawals);
    }

    /**
     * Cancel withdrawal request
     */
    public function cancelWithdrawal($id)
    {
        $userId = Auth::id();
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $transaction = DB::table('wallet_transactions')
            ->where('id', $id)
            ->where('wallet_id', $wallet->id)
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Withdrawal request not found or cannot be cancelled'], 404);
        }

        DB::beginTransaction();
        try {
            // Update transaction status
            DB::table('wallet_transactions')->where('id', $id)->update([
                'status' => 'cancelled',
                'updated_at' => now(),
            ]);

            // Refund the amount to wallet
            DB::table('wallets')->where('id', $wallet->id)->update([
                'balance' => $wallet->balance + $transaction->amount,
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling withdrawal request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction details
     */
    public function getTransaction($id)
    {
        $userId = Auth::id();
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();

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
     * Export wallet data as CSV
     */
    public function export(Request $request)
    {
        $userId = Auth::id();
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $doctor = DB::table('doctors')->where('user_id', $userId)->first();
        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $type = $request->get('type', 'transactions');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        switch ($type) {
            case 'transactions':
                $query = DB::table('wallet_transactions')
                    ->where('wallet_id', $wallet->id)
                    ->select(
                        'wallet_transactions.id',
                        'wallet_transactions.type',
                        'wallet_transactions.amount',
                        'wallet_transactions.currency',
                        'wallet_transactions.status',
                        'wallet_transactions.description',
                        'wallet_transactions.reference_id',
                        'wallet_transactions.created_at',
                        'wallet_transactions.updated_at'
                    );
                break;

            case 'withdrawals':
                $query = DB::table('wallet_transactions')
                    ->where('wallet_id', $wallet->id)
                    ->where('type', 'withdrawal')
                    ->select(
                        'wallet_transactions.id',
                        'wallet_transactions.amount',
                        'wallet_transactions.currency',
                        'wallet_transactions.status',
                        'wallet_transactions.description',
                        'wallet_transactions.reference_id',
                        'wallet_transactions.created_at',
                        'wallet_transactions.updated_at'
                    );
                break;

            case 'earnings':
                $query = DB::table('payments')
                    ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                    ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                    ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
                    ->where('appointments.doctor_id', $doctor->id)
                    ->where('payments.STATUS', 'succeeded')
                    ->select(
                        'payments.id',
                        'payments.provider_payment_id',
                        'payments.amount',
                        'payments.currency',
                        'payments.platform_fee',
                        'payments.net_amount',
                        'payments.meta',
                        'payments.created_at',
                        'payments.updated_at',
                        'patients.NAME as patient_name',
                        'patients.phone as patient_phone',
                        'appointments.appointment_date',
                        'appointments.appointment_time',
                        'appointments.notes',
                        'specialties.name_en as specialty_name'
                    );
                break;

            default:
                return response()->json(['error' => 'Invalid export type'], 400);
        }

        // Apply date filters
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        // Prepare CSV data
        $csvData = [];
        if ($type === 'transactions') {
            foreach ($data as $transaction) {
                $csvData[] = [
                    $transaction->id,
                    $this->formatStatus($transaction->type),
                    $this->formatCurrency($transaction->amount, $transaction->currency),
                    $transaction->currency,
                    $this->formatStatus($transaction->status),
                    $transaction->description,
                    $transaction->reference_id,
                    $this->formatDateTime($transaction->created_at),
                    $this->formatDateTime($transaction->updated_at)
                ];
            }

            $headers = [
                'Transaction ID',
                'Type',
                'Amount',
                'Currency',
                'Status',
                'Description',
                'Reference ID',
                'Created At',
                'Updated At'
            ];
        } elseif ($type === 'withdrawals') {
            foreach ($data as $withdrawal) {
                $csvData[] = [
                    $withdrawal->id,
                    $this->formatCurrency($withdrawal->amount, $withdrawal->currency),
                    $withdrawal->currency,
                    $this->formatStatus($withdrawal->status),
                    $withdrawal->description,
                    $withdrawal->reference_id,
                    $this->formatDateTime($withdrawal->created_at),
                    $this->formatDateTime($withdrawal->updated_at)
                ];
            }

            $headers = [
                'Withdrawal ID',
                'Amount',
                'Currency',
                'Status',
                'Description',
                'Reference ID',
                'Created At',
                'Updated At'
            ];
        } else {
            foreach ($data as $earning) {
                $csvData[] = [
                    $earning->id,
                    $earning->provider_payment_id,
                    $this->formatCurrency($earning->amount, $earning->currency),
                    $earning->currency,
                    $this->formatCurrency($earning->platform_fee, $earning->currency),
                    $this->formatCurrency($earning->net_amount, $earning->currency),
                    $earning->patient_name,
                    $earning->patient_phone,
                    $earning->specialty_name,
                    $this->formatDate($earning->appointment_date),
                    $this->formatDateTime($earning->appointment_time, 'g:i A'),
                    $this->getAppointmentType($earning->notes),
                    $earning->notes,
                    $this->extractPaymentMethod($earning->meta),
                    $this->formatDateTime($earning->created_at),
                    $this->formatDateTime($earning->updated_at)
                ];
            }

            $headers = [
                'Payment ID',
                'Provider Payment ID',
                'Amount',
                'Currency',
                'Platform Fee',
                'Net Amount',
                'Patient Name',
                'Patient Phone',
                'Specialty',
                'Appointment Date',
                'Appointment Time',
                'Appointment Type',
                'Notes',
                'Payment Method',
                'Created At',
                'Updated At'
            ];
        }

        // Generate filename
        $filename = $this->generateExportFilename($type, 'doctor_wallet_' . $doctor->name);

        // Get summary
        $summary = $this->getExportSummary($data, $type, $doctor->name);
        $summary['Doctor Name'] = $doctor->name;
        $summary['Wallet Balance'] = $this->formatCurrency($wallet->balance, $wallet->currency ?? 'USD');

        if ($type === 'transactions') {
            $summary['Total Deposits'] = $this->formatCurrency($data->where('type', 'deposit')->where('status', 'completed')->sum('amount'));
            $summary['Total Withdrawals'] = $this->formatCurrency($data->where('type', 'withdrawal')->where('status', 'completed')->sum('amount'));
        } elseif ($type === 'withdrawals') {
            $summary['Total Withdrawn'] = $this->formatCurrency($data->where('status', 'completed')->sum('amount'));
            $summary['Pending Withdrawals'] = $data->where('status', 'pending')->count();
        } else {
            $summary['Total Earnings'] = $this->formatCurrency($data->sum('net_amount'));
            $summary['Total Platform Fees'] = $this->formatCurrency($data->sum('platform_fee'));
        }

        return $this->generateCSV($csvData, $filename, $headers, $summary);
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
            'pending_withdrawals' => DB::table('wallet_transactions')
                ->where('wallet_id', $walletId)
                ->where('type', 'withdrawal')
                ->where('status', 'pending')
                ->count(),
            'total_withdrawn' => DB::table('wallet_transactions')
                ->where('wallet_id', $walletId)
                ->where('type', 'withdrawal')
                ->where('status', 'completed')
                ->sum('amount'),
            'pending_amount' => DB::table('wallet_transactions')
                ->where('wallet_id', $walletId)
                ->where('type', 'withdrawal')
                ->where('status', 'pending')
                ->sum('amount'),
        ];
    }
}
