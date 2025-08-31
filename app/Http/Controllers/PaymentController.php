<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->select(
                'payments.*',
                'doctors.name as doctor_name',
                'patients.NAME as patient_name',
                'appointments.appointment_date',
                'appointments.appointment_time'
            );

        // Apply role-based filtering
        if ($user->role === 'doctor') {
            $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
            if ($doctor) {
                $query->where('appointments.doctor_id', $doctor->id);
            }
        } elseif ($user->role === 'patient') {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('appointments.patient_id', $patient->id);
            }
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('payments.STATUS', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payments.payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payments.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payments.created_at', '<=', $request->date_to);
        }

        if ($request->filled('min_amount')) {
            $query->where('payments.amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('payments.amount', '<=', $request->max_amount);
        }

        $payments = $query->orderBy('payments.created_at', 'desc')
            ->paginate(15);

        $statuses = ['pending', 'processing', 'succeeded', 'failed', 'cancelled', 'refunded'];
        $paymentMethods = ['credit_card', 'debit_card', 'bank_transfer', 'wallet', 'cash'];

        return view('payments.index', compact('payments', 'statuses', 'paymentMethods'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create()
    {
        $appointments = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.STATUS', 'confirmed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('payments')
                      ->whereRaw('payments.appointment_id = appointments.id')
                      ->where('payments.STATUS', 'succeeded');
            })
            ->select(
                'appointments.*',
                'doctors.name as doctor_name',
                'patients.NAME as patient_name'
            )
            ->get();

        return view('payments.create', compact('appointments'));
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|exists:appointments,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:credit_card,debit_card,bank_transfer,wallet,cash',
            'payment_gateway' => 'nullable|string|max:100',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if payment already exists for this appointment
        $existingPayment = DB::table('payments')
            ->where('appointment_id', $request->appointment_id)
            ->where('STATUS', 'succeeded')
            ->first();

        if ($existingPayment) {
            return response()->json(['error' => 'Payment already exists for this appointment'], 400);
        }

        DB::beginTransaction();
        try {
            $paymentId = DB::table('payments')->insertGetId([
                'appointment_id' => $request->appointment_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_gateway' => $request->payment_gateway,
                'transaction_id' => $request->transaction_id,
                'STATUS' => 'pending',
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment created successfully',
                'payment_id' => $paymentId
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified payment
     */
    public function show($id)
    {
        $user = Auth::user();
        $query = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('payments.id', $id)
            ->select(
                'payments.*',
                'doctors.name as doctor_name',
                'patients.NAME as patient_name',
                'appointments.appointment_date',
                'appointments.appointment_time'
            );

        // Apply role-based access
        if ($user->role === 'doctor') {
            $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
            if ($doctor) {
                $query->where('appointments.doctor_id', $doctor->id);
            }
        } elseif ($user->role === 'patient') {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('appointments.patient_id', $patient->id);
            }
        }

        $payment = $query->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Get payment webhooks
        $webhooks = DB::table('payment_webhooks')
            ->where('payment_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payments.show', compact('payment', 'webhooks'));
    }

    /**
     * Show the form for editing the specified payment
     */
    public function edit($id)
    {
        $payment = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('payments.id', $id)
            ->select(
                'payments.*',
                'doctors.name as doctor_name',
                'patients.NAME as patient_name',
                'appointments.appointment_date',
                'appointments.appointment_time'
            )
            ->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified payment
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:credit_card,debit_card,bank_transfer,wallet,cash',
            'payment_gateway' => 'nullable|string|max:100',
            'transaction_id' => 'nullable|string|max:255',
            'STATUS' => 'required|in:pending,processing,succeeded,failed,cancelled,refunded',
            'notes' => 'nullable|string',
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
            DB::table('payments')->where('id', $id)->update([
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_gateway' => $request->payment_gateway,
                'transaction_id' => $request->transaction_id,
                'STATUS' => $request->STATUS,
                'notes' => $request->notes,
                'updated_at' => now(),
            ]);

            // Log payment status change
            DB::table('payment_webhooks')->insert([
                'payment_id' => $id,
                'event_type' => 'status_updated',
                'payload' => json_encode([
                    'old_status' => $payment->STATUS,
                    'new_status' => $request->STATUS,
                    'updated_by' => Auth::id(),
                    'updated_at' => now(),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:credit_card,debit_card,bank_transfer,wallet',
            'card_number' => 'required_if:payment_method,credit_card,debit_card|string|max:20',
            'expiry_date' => 'required_if:payment_method,credit_card,debit_card|string|max:5',
            'cvv' => 'required_if:payment_method,credit_card,debit_card|string|max:4',
            'card_holder_name' => 'required_if:payment_method,credit_card,debit_card|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment = DB::table('payments')->where('id', $id)->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($payment->STATUS !== 'pending') {
            return response()->json(['error' => 'Payment cannot be processed in current status'], 400);
        }

        DB::beginTransaction();
        try {
            // Update payment status to processing
            DB::table('payments')->where('id', $id)->update([
                'STATUS' => 'processing',
                'payment_method' => $request->payment_method,
                'updated_at' => now(),
            ]);

            // Simulate payment processing
            $success = $this->processPaymentWithGateway($request, $payment);

            if ($success) {
                DB::table('payments')->where('id', $id)->update([
                    'STATUS' => 'succeeded',
                    'transaction_id' => 'TXN_' . time() . '_' . $id,
                    'updated_at' => now(),
                ]);

                // Update appointment status
                DB::table('appointments')->where('id', $payment->appointment_id)->update([
                    'STATUS' => 'confirmed',
                    'updated_at' => now(),
                ]);

                // Log successful payment
                DB::table('payment_webhooks')->insert([
                    'payment_id' => $id,
                    'event_type' => 'payment_succeeded',
                    'payload' => json_encode([
                        'amount' => $payment->amount,
                        'payment_method' => $request->payment_method,
                        'processed_at' => now(),
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully'
                ]);
            } else {
                DB::table('payments')->where('id', $id)->update([
                    'STATUS' => 'failed',
                    'updated_at' => now(),
                ]);

                // Log failed payment
                DB::table('payment_webhooks')->insert([
                    'payment_id' => $id,
                    'event_type' => 'payment_failed',
                    'payload' => json_encode([
                        'reason' => 'Payment gateway error',
                        'failed_at' => now(),
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed'
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment: ' . $e->getMessage()
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
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment = DB::table('payments')->where('id', $id)->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($payment->STATUS !== 'succeeded') {
            return response()->json(['error' => 'Only successful payments can be refunded'], 400);
        }

        if ($request->refund_amount > $payment->amount) {
            return response()->json(['error' => 'Refund amount cannot exceed payment amount'], 400);
        }

        DB::beginTransaction();
        try {
            // Update payment status
            DB::table('payments')->where('id', $id)->update([
                'STATUS' => 'refunded',
                'updated_at' => now(),
            ]);

            // Log refund
            DB::table('payment_webhooks')->insert([
                'payment_id' => $id,
                'event_type' => 'refund_processed',
                'payload' => json_encode([
                    'refund_amount' => $request->refund_amount,
                    'reason' => $request->reason,
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update patient wallet if applicable
            $appointment = DB::table('appointments')->where('id', $payment->appointment_id)->first();
            if ($appointment) {
                $wallet = DB::table('wallets')->where('user_id', $appointment->patient_id)->first();
                if ($wallet) {
                    $newBalance = $wallet->balance + $request->refund_amount;
                    DB::table('wallets')->where('id', $wallet->id)->update([
                        'balance' => $newBalance,
                        'updated_at' => now(),
                    ]);

                    // Log wallet transaction
                    DB::table('wallet_transactions')->insert([
                        'wallet_id' => $wallet->id,
                        'type' => 'refund',
                        'amount' => $request->refund_amount,
                        'description' => 'Refund for payment #' . $id,
                        'status' => 'completed',
                        'metadata' => json_encode([
                            'payment_id' => $id,
                            'reason' => $request->reason,
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
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
        $user = Auth::user();
        $query = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id');

        // Apply role-based filtering
        if ($user->role === 'doctor') {
            $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
            if ($doctor) {
                $query->where('appointments.doctor_id', $doctor->id);
            }
        } elseif ($user->role === 'patient') {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('appointments.patient_id', $patient->id);
            }
        }

        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $stats = [
            'total_payments' => $query->count(),
            'total_amount' => $query->where('payments.STATUS', 'succeeded')->sum('payments.amount'),
            'this_month_amount' => $query->where('payments.STATUS', 'succeeded')
                ->whereMonth('payments.created_at', $currentDate->month)
                ->whereYear('payments.created_at', $currentDate->year)
                ->sum('payments.amount'),
            'last_month_amount' => $query->where('payments.STATUS', 'succeeded')
                ->whereMonth('payments.created_at', $lastMonth->month)
                ->whereYear('payments.created_at', $lastMonth->year)
                ->sum('payments.amount'),
            'successful_payments' => $query->where('payments.STATUS', 'succeeded')->count(),
            'failed_payments' => $query->where('payments.STATUS', 'failed')->count(),
            'pending_payments' => $query->where('payments.STATUS', 'pending')->count(),
        ];

        // Calculate growth
        $stats['growth'] = $stats['last_month_amount'] > 0
            ? (($stats['this_month_amount'] - $stats['last_month_amount']) / $stats['last_month_amount']) * 100
            : 0;

        return response()->json($stats);
    }

    /**
     * Export payments
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $query = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->select(
                'payments.*',
                'doctors.name as doctor_name',
                'patients.NAME as patient_name',
                'appointments.appointment_date',
                'appointments.appointment_time'
            );

        // Apply role-based filtering
        if ($user->role === 'doctor') {
            $doctor = DB::table('doctors')->where('user_id', $user->id)->first();
            if ($doctor) {
                $query->where('appointments.doctor_id', $doctor->id);
            }
        } elseif ($user->role === 'patient') {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('appointments.patient_id', $patient->id);
            }
        }

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

        $payments = $query->orderBy('payments.created_at', 'desc')->get();

        return response()->json($payments);
    }

    /**
     * Simulate payment processing with gateway
     */
    private function processPaymentWithGateway($request, $payment)
    {
        // This is a simulation - in real implementation, you would integrate with actual payment gateways
        // like Stripe, PayPal, etc.

        // Simulate 95% success rate
        return rand(1, 100) <= 95;
    }
}
