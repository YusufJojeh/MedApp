<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\AiBookingService;

class AiBookingController extends Controller
{
    protected $aiBookingService;

    public function __construct(AiBookingService $aiBookingService)
    {
        $this->aiBookingService = $aiBookingService;
    }

    /**
     * Process AI booking intent
     */
    public function processIntent(Request $request)
    {
        // Check authentication
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // Check if AI booking is enabled
        if (!config('services.ai.booking_enabled', false)) {
            return response()->json([
                'success' => false,
                'message' => 'AI booking is currently disabled',
                'feature_disabled' => true
            ], 503);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $result = $this->aiBookingService->processBookingIntent($request->message, $user->id);

        return response()->json($result);
    }

    /**
     * Get available doctors by specialty
     */
    public function getDoctors(Request $request)
    {
        // Check authentication
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // Check if AI booking is enabled
        if (!config('services.ai.booking_enabled', false)) {
            return response()->json([
                'success' => false,
                'message' => 'AI booking is currently disabled'
            ], 503);
        }

        $validator = Validator::make($request->all(), [
            'specialty' => 'nullable|string|max:100',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $doctors = $this->aiBookingService->getAvailableDoctors(
                $request->specialty,
                $request->limit ?? 10
            );

            return response()->json([
                'success' => true,
                'doctors' => $doctors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching doctors: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check doctor availability
     */
    public function checkAvailability(Request $request)
    {
        // Check authentication
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // Check if AI booking is enabled
        if (!config('services.ai.booking_enabled', false)) {
            return response()->json([
                'success' => false,
                'message' => 'AI booking is currently disabled'
            ], 503);
        }

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|integer|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $availability = $this->aiBookingService->getDoctorAvailability(
                $request->doctor_id,
                $request->date
            );

            return response()->json([
                'success' => true,
                'availability' => $availability,
                'date' => $request->date
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Book appointment with wallet integration
     */
    public function bookAppointment(Request $request)
    {
        // Check authentication
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // Check if AI booking and wallet integration are enabled
        if (!config('services.ai.booking_enabled', false)) {
            return response()->json([
                'success' => false,
                'message' => 'AI booking is currently disabled'
            ], 503);
        }

        if (!config('services.ai.wallet_integration', false)) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet integration is currently disabled'
            ], 503);
        }

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|integer|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $result = $this->aiBookingService->bookAppointmentWithWallet(
            $request->doctor_id,
            $request->date,
            $request->time,
            $user->id
        );

        return response()->json($result);
    }

    /**
     * Get booking suggestions
     */
    public function getSuggestions()
    {
        // Check if AI booking is enabled
        if (!config('services.ai.booking_enabled', false)) {
            return response()->json([
                'success' => false,
                'message' => 'AI booking is currently disabled'
            ], 503);
        }

        $suggestions = [
            'Browse doctors by specialty',
            'Check appointment availability',
            'View consultation fees',
            'Add funds to wallet',
            'Book an appointment',
            'Check my wallet balance'
        ];

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Get feature status
     */
    public function getFeatureStatus()
    {
        return response()->json([
            'success' => true,
            'features' => [
                'ai_booking_enabled' => config('services.ai.booking_enabled', false),
                'ai_proxy_enabled' => config('services.ai.proxy_enabled', true),
                'wallet_integration_enabled' => config('services.ai.wallet_integration', false),
                'ai_service_url' => config('services.ai.flask_url', 'http://127.0.0.1:5005'),
            ]
        ]);
    }

    /**
     * Confirm booking with payment options
     */
    public function confirmBooking(Request $request)
    {
        // Check authentication
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $request->validate([
            'doctor_id' => 'required|integer',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|string',
            'consultation_fee' => 'required|numeric|min:0'
        ]);

        try {
            $user = auth()->user();
            $doctorId = $request->doctor_id;
            $appointmentDate = $request->appointment_date;
            $appointmentTime = $request->appointment_time;
            $consultationFee = $request->consultation_fee;

            // Get doctor details
            $doctor = DB::table('doctors')
                ->join('users', 'doctors.user_id', '=', 'users.id')
                ->where('doctors.id', $doctorId)
                ->where('doctors.is_active', true)
                ->where('users.status', 'active')
                ->select('doctors.*', 'users.name as doctor_name')
                ->first();

            if (!$doctor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor not found or unavailable'
                ], 404);
            }

            // Check availability
            $isAvailable = $this->aiBookingService->getDoctorAvailability($doctorId, $appointmentDate);
            $requestedSlot = $appointmentTime;
            $slotAvailable = collect($isAvailable)->contains('time', $requestedSlot);

            if (!$slotAvailable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requested time slot is not available'
                ], 400);
            }

            // Get user wallet
            $wallet = DB::table('wallets')->where('user_id', $user->id)->first();
            $walletBalance = $wallet ? $wallet->balance : 0;

            // Check if user has sufficient balance
            $hasSufficientBalance = $walletBalance >= $consultationFee;

            return response()->json([
                'success' => true,
                'booking_details' => [
                    'doctor_id' => $doctorId,
                    'doctor_name' => $doctor->name,
                    'appointment_date' => $appointmentDate,
                    'appointment_time' => $appointmentTime,
                    'consultation_fee' => $consultationFee,
                    'wallet_balance' => $walletBalance,
                    'has_sufficient_balance' => $hasSufficientBalance
                ],
                'payment_options' => [
                    'wallet_payment' => $hasSufficientBalance,
                    'pay_on_site' => true,
                    'wallet_balance' => $walletBalance,
                    'required_amount' => $consultationFee
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Booking confirmation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error confirming booking'
            ], 500);
        }
    }

    /**
     * Process payment and complete booking
     */
    public function processPayment(Request $request)
    {
        // Check authentication
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $request->validate([
            'doctor_id' => 'required|integer',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|string',
            'consultation_fee' => 'required|numeric|min:0',
            'payment_method' => 'required|in:wallet,pay_on_site'
        ]);

        try {
            $user = auth()->user();
            $doctorId = $request->doctor_id;
            $appointmentDate = $request->appointment_date;
            $appointmentTime = $request->appointment_time;
            $consultationFee = $request->consultation_fee;
            $paymentMethod = $request->payment_method;

            DB::beginTransaction();

            // Create appointment
            $appointment = DB::table('appointments')->insertGetId([
                'patient_id' => $user->id,
                'doctor_id' => $doctorId,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'consultation_fee' => $consultationFee,
                'STATUS' => $paymentMethod === 'wallet' ? 'confirmed' : 'scheduled',
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'wallet' ? 'paid' : 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Process payment if using wallet
            if ($paymentMethod === 'wallet') {
                $wallet = DB::table('wallets')->where('user_id', $user->id)->first();

                if (!$wallet || $wallet->balance < $consultationFee) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient wallet balance'
                    ], 400);
                }

                // Deduct from wallet
                DB::table('wallets')
                    ->where('user_id', $user->id)
                    ->update([
                        'balance' => $wallet->balance - $consultationFee,
                        'updated_at' => now()
                    ]);

                // Create wallet transaction
                DB::table('wallet_transactions')->insert([
                    'wallet_id' => $wallet->id,
                    'amount' => -$consultationFee,
                    'type' => 'debit',
                    'description' => "Appointment booking with Dr. " . DB::table('doctors')->where('id', $doctorId)->value('name'),
                    'reference' => 'appointment_' . $appointment,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $paymentMethod === 'wallet'
                    ? 'Appointment booked successfully! Payment deducted from wallet.'
                    : 'Appointment booked successfully! Please pay on site.',
                'appointment_id' => $appointment,
                'payment_status' => $paymentMethod === 'wallet' ? 'paid' : 'pending',
                'booking_details' => [
                    'doctor_id' => $doctorId,
                    'appointment_date' => $appointmentDate,
                    'appointment_time' => $appointmentTime,
                    'consultation_fee' => $consultationFee,
                    'payment_method' => $paymentMethod
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment processing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment'
            ], 500);
        }
    }
}
