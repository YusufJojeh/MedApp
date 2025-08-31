<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Traits\ExportTrait;

class PaymentManagementController extends Controller
{
    use ExportTrait;
    /**
     * Display a listing of all payments
     */
    public function index(Request $request)
    {
        $query = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->join('doctors', 'payments.doctor_id', '=', 'doctors.id')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->select(
                'payments.*',
                'users.first_name',
                'users.last_name',
                'users.email',
                'doctors.name as doctor_name',
                'appointments.appointment_date',
                'appointments.appointment_time'
            );

        // Apply filters
        if ($request->filled('status')) {
            $query->where('payments.STATUS', $request->status);
        }

        if ($request->filled('provider')) {
            $query->where('payments.provider', $request->provider);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payments.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payments.created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('doctors.name', 'like', "%{$search}%")
                  ->orWhere('payments.provider_payment_id', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('payments.created_at', 'desc')->paginate(15);

        // Calculate statistics
        $totalRevenue = DB::table('payments')
            ->where('STATUS', 'succeeded')
            ->sum('amount');

        $successfulPayments = DB::table('payments')
            ->where('STATUS', 'succeeded')
            ->count();

        $failedPayments = DB::table('payments')
            ->where('STATUS', 'failed')
            ->count();

        $statuses = ['succeeded', 'pending', 'failed', 'refunded', 'canceled'];
        $paymentMethods = ['stripe', 'paypal'];

        return view('admin.payments.index', compact(
            'payments',
            'statuses',
            'paymentMethods',
            'totalRevenue',
            'successfulPayments',
            'failedPayments'
        ));
    }

    /**
     * Display the specified payment
     */
    public function show($id)
    {
        $payment = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->join('doctors', 'payments.doctor_id', '=', 'doctors.id')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->leftJoin('patients', 'users.id', '=', 'patients.user_id')
            ->leftJoin('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->select(
                'payments.*',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'doctors.name as doctor_name',
                'doctors.consultation_fee',
                'doctors.experience_years as doctor_experience',
                'doctors.rating as doctor_rating',
                'specialties.name_en as doctor_specialty',
                'appointments.appointment_date',
                'appointments.appointment_time',
                'appointments.STATUS as appointment_status',
                'appointments.notes as appointment_notes',
                'patients.date_of_birth as patient_dob',
                'patients.gender as patient_gender',
                'patients.blood_type as patient_blood_type',
                'patients.address as patient_address'
            )
            ->where('payments.id', $id)
            ->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Get payment webhooks
        $webhooks = DB::table('payment_webhooks')
            ->where('payment_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.payments.show', compact('payment', 'webhooks'));
    }

    /**
     * Update payment status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,succeeded,failed,refunded,canceled',
            'note' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment = DB::table('payments')->where('id', $id)->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Update payment status
            DB::table('payments')->where('id', $id)->update([
                'STATUS' => $request->status,
                'updated_at' => now()
            ]);

            // Log the status change
            DB::table('payment_webhooks')->insert([
                'provider' => $payment->provider,
                'event_type' => 'admin_status_update',
                'event_id' => 'admin_' . time(),
                'payment_id' => $id,
                'payload' => json_encode([
                    'old_status' => $payment->STATUS,
                    'new_status' => $request->status,
                    'note' => $request->note,
                    'admin_action' => true
                ]),
                'processed' => true,
                'created_at' => now()
            ]);

            // If refunding, update wallet balance
            if ($request->status === 'refunded' && $payment->STATUS === 'succeeded') {
                $wallet = DB::table('wallets')->where('user_id', $payment->user_id)->first();
                if ($wallet) {
                    $newBalance = $wallet->balance + $payment->amount;
                    DB::table('wallets')->where('id', $wallet->id)->update([
                        'balance' => $newBalance,
                        'updated_at' => now()
                    ]);

                    // Log wallet transaction
                    DB::table('wallet_transactions')->insert([
                        'wallet_id' => $wallet->id,
                        'payment_id' => $id,
                        'TYPE' => 'credit',
                        'amount' => $payment->amount,
                        'reason' => 'Payment refund',
                        'balance_before' => $wallet->balance,
                        'balance_after' => $newBalance,
                        'meta' => json_encode(['refund_payment_id' => $id]),
                        'created_at' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process refund
     */
    public function processRefund(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'refund_amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment = DB::table('payments')->where('id', $id)->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($payment->STATUS !== 'succeeded') {
            return response()->json(['error' => 'Payment must be succeeded to process refund'], 400);
        }

        if ($request->refund_amount > $payment->amount) {
            return response()->json(['error' => 'Refund amount cannot exceed payment amount'], 400);
        }

        DB::beginTransaction();
        try {
            // Update payment status
            DB::table('payments')->where('id', $id)->update([
                'STATUS' => 'refunded',
                'updated_at' => now()
            ]);

            // Log refund
            DB::table('payment_webhooks')->insert([
                'provider' => $payment->provider,
                'event_type' => 'refund_processed',
                'event_id' => 'refund_' . time(),
                'payment_id' => $id,
                'payload' => json_encode([
                    'refund_amount' => $request->refund_amount,
                    'reason' => $request->reason,
                    'admin_action' => true
                ]),
                'processed' => true,
                'created_at' => now()
            ]);

            // Update wallet balance
            $wallet = DB::table('wallets')->where('user_id', $payment->user_id)->first();
            if ($wallet) {
                $newBalance = $wallet->balance + $request->refund_amount;
                DB::table('wallets')->where('id', $wallet->id)->update([
                    'balance' => $newBalance,
                    'updated_at' => now()
                ]);

                // Log wallet transaction
                DB::table('wallet_transactions')->insert([
                    'wallet_id' => $wallet->id,
                    'payment_id' => $id,
                    'TYPE' => 'credit',
                    'amount' => $request->refund_amount,
                    'reason' => 'Payment refund: ' . $request->reason,
                    'balance_before' => $wallet->balance,
                    'balance_after' => $newBalance,
                    'meta' => json_encode([
                        'refund_payment_id' => $id,
                        'refund_reason' => $request->reason
                    ]),
                    'created_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error processing refund: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment statistics
     */
    public function getStats()
    {
        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $stats = [
            'total_payments' => DB::table('payments')->count(),
            'total_revenue' => DB::table('payments')
                ->where('STATUS', 'succeeded')
                ->sum('amount'),
            'this_month_revenue' => DB::table('payments')
                ->where('STATUS', 'succeeded')
                ->whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->sum('amount'),
            'last_month_revenue' => DB::table('payments')
                ->where('STATUS', 'succeeded')
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->sum('amount'),
            'pending_payments' => DB::table('payments')
                ->where('STATUS', 'pending')
                ->count(),
            'failed_payments' => DB::table('payments')
                ->where('STATUS', 'failed')
                ->count(),
            'refunded_payments' => DB::table('payments')
                ->where('STATUS', 'refunded')
                ->count(),
        ];

        // Calculate growth
        $stats['revenue_growth'] = $stats['last_month_revenue'] > 0
            ? (($stats['this_month_revenue'] - $stats['last_month_revenue']) / $stats['last_month_revenue']) * 100
            : 0;

        return response()->json($stats);
    }

    /**
     * Get payment chart data
     */
    public function getChartData()
    {
        $currentDate = Carbon::now();

        // Last 7 days revenue
        $dailyRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = DB::table('payments')
                ->where('STATUS', 'succeeded')
                ->whereDate('created_at', $date->toDateString())
                ->sum('amount');

            $dailyRevenue[] = [
                'date' => $date->format('M d'),
                'revenue' => $revenue
            ];
        }

        // Payment status distribution
        $statusDistribution = DB::table('payments')
            ->select('STATUS', DB::raw('COUNT(*) as count'))
            ->groupBy('STATUS')
            ->get();

        // Provider distribution
        $providerDistribution = DB::table('payments')
            ->select('provider', DB::raw('COUNT(*) as count'))
            ->groupBy('provider')
            ->get();

        return response()->json([
            'daily_revenue' => $dailyRevenue,
            'status_distribution' => $statusDistribution,
            'provider_distribution' => $providerDistribution
        ]);
    }

    /**
     * Export payments data as CSV
     */
    public function export(Request $request)
    {
        $query = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->join('doctors', 'payments.doctor_id', '=', 'doctors.id')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->leftJoin('patients', 'users.id', '=', 'patients.user_id')
            ->leftJoin('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->select(
                'payments.id',
                'payments.provider_payment_id',
                'payments.provider',
                'payments.STATUS',
                'payments.amount',
                'payments.currency',
                'payments.platform_fee',
                'payments.net_amount',
                'payments.meta',
                'payments.created_at',
                'payments.updated_at',
                'users.first_name',
                'users.last_name',
                'users.email',
                'doctors.name as doctor_name',
                'specialties.name_en as specialty_name',
                'appointments.appointment_date',
                'appointments.appointment_time',
                'appointments.notes',
                'patients.NAME as patient_name',
                'patients.phone as patient_phone'
            );

        // Apply filters
        if ($request->filled('status')) {
            $query->where('payments.STATUS', $request->status);
        }

        if ($request->filled('provider')) {
            $query->where('payments.provider', $request->provider);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payments.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payments.created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('doctors.name', 'like', "%{$search}%")
                  ->orWhere('payments.provider_payment_id', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('payments.created_at', 'desc')->get();

        // Prepare CSV data
        $csvData = [];
        foreach ($payments as $payment) {
            $csvData[] = [
                $payment->id,
                $payment->provider_payment_id,
                $payment->provider,
                $this->formatStatus($payment->STATUS),
                $this->formatCurrency($payment->amount, $payment->currency),
                $payment->currency,
                $this->formatCurrency($payment->platform_fee, $payment->currency),
                $this->formatCurrency($payment->net_amount, $payment->currency),
                $payment->first_name . ' ' . $payment->last_name,
                $payment->email,
                $payment->doctor_name,
                $payment->specialty_name,
                $payment->patient_name,
                $payment->patient_phone,
                $this->formatDate($payment->appointment_date),
                $this->formatDateTime($payment->appointment_time, 'g:i A'),
                $this->getAppointmentType($payment->notes),
                $payment->notes,
                $this->extractPaymentMethod($payment->meta),
                $this->formatDateTime($payment->created_at),
                $this->formatDateTime($payment->updated_at)
            ];
        }

        // Headers
        $headers = [
            'Payment ID',
            'Provider Payment ID',
            'Provider',
            'Status',
            'Amount',
            'Currency',
            'Platform Fee',
            'Net Amount',
            'Customer Name',
            'Customer Email',
            'Doctor Name',
            'Specialty',
            'Patient Name',
            'Patient Phone',
            'Appointment Date',
            'Appointment Time',
            'Appointment Type',
            'Notes',
            'Payment Method',
            'Created At',
            'Updated At'
        ];

        // Generate filename
        $filename = $this->generateExportFilename('payments', 'admin_management');

        // Get summary
        $summary = $this->getExportSummary($payments, 'payments', 'Admin');
        $summary['Total Revenue'] = $this->formatCurrency($payments->where('STATUS', 'succeeded')->sum('amount'));
        $summary['Total Platform Fees'] = $this->formatCurrency($payments->where('STATUS', 'succeeded')->sum('platform_fee'));
        $summary['Successful Payments'] = $payments->where('STATUS', 'succeeded')->count();
        $summary['Failed Payments'] = $payments->where('STATUS', 'failed')->count();
        $summary['Pending Payments'] = $payments->where('STATUS', 'pending')->count();

        return $this->generateCSV($csvData, $filename, $headers, $summary);
    }

    /**
     * Get payment webhooks
     */
    public function getWebhooks(Request $request)
    {
        $query = DB::table('payment_webhooks')
            ->leftJoin('payments', 'payment_webhooks.payment_id', '=', 'payments.id')
            ->select('payment_webhooks.*', 'payments.provider_payment_id');

        // Apply filters
        if ($request->filled('provider')) {
            $query->where('payment_webhooks.provider', $request->provider);
        }

        if ($request->filled('event_type')) {
            $query->where('payment_webhooks.event_type', $request->event_type);
        }

        if ($request->filled('processed')) {
            $query->where('payment_webhooks.processed', $request->processed);
        }

        $webhooks = $query->orderBy('payment_webhooks.created_at', 'desc')->paginate(15);

        return view('admin.payments.webhooks', compact('webhooks'));
    }

    /**
     * Retry failed webhook
     */
    public function retryWebhook($id)
    {
        $webhook = DB::table('payment_webhooks')->where('id', $id)->first();

        if (!$webhook) {
            return response()->json(['error' => 'Webhook not found'], 404);
        }

        if ($webhook->processed) {
            return response()->json(['error' => 'Webhook already processed'], 400);
        }

        try {
            // Here you would implement the webhook processing logic
            // For now, we'll just mark it as processed
            DB::table('payment_webhooks')->where('id', $id)->update([
                'processed' => true,
                'error_message' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);

        } catch (\Exception $e) {
            DB::table('payment_webhooks')->where('id', $id)->update([
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing webhook: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get financial report
     */
    public function getFinancialReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $report = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'revenue' => [
                'total' => DB::table('payments')
                    ->where('STATUS', 'succeeded')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('amount'),
                'platform_fees' => DB::table('payments')
                    ->where('STATUS', 'succeeded')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('platform_fee'),
                'net_revenue' => DB::table('payments')
                    ->where('STATUS', 'succeeded')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('net_amount'),
            ],
            'transactions' => [
                'total' => DB::table('payments')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'succeeded' => DB::table('payments')
                    ->where('STATUS', 'succeeded')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'failed' => DB::table('payments')
                    ->where('STATUS', 'failed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'refunded' => DB::table('payments')
                    ->where('STATUS', 'refunded')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
            ],
            'providers' => DB::table('payments')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select('provider', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
                ->groupBy('provider')
                ->get(),
        ];

        return response()->json($report);
    }
}
