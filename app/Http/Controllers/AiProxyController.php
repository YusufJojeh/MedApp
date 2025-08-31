<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Added DB facade

class AiProxyController extends Controller
{
    private $flaskUrl = 'http://127.0.0.1:5006';

    /**
     * Proxy health check to Flask service
     */
    public function health()
    {
        try {
            $response = Http::timeout(10)->get($this->flaskUrl . '/health');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Flask service is not responding',
                'status_code' => $response->status()
            ], 503);

        } catch (\Exception $e) {
            Log::error('AI Proxy Health Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Flask service connection failed',
                'error' => $e->getMessage()
            ], 503);
        }
    }

    /**
     * Proxy process request to Flask service
     */
    public function process(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'text' => 'required|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Use the AiAssistantService for proper formatting
            $aiService = new \App\Services\AiAssistantService();
            $result = $aiService->processText($request->text, auth()->id());

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'response' => $result['data']['response'],
                    'intent' => $result['data']['intent']['intent'] ?? 'general',
                    'data' => $result['data'],
                    'suggestions' => $this->getSuggestions($result['data']['intent']['intent'] ?? 'general')
                ]);
            }

            Log::error('Flask API Error: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => 'AI service error: ' . $response->status()
            ], 500);

        } catch (\Exception $e) {
            Log::error('AI Proxy Process Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle AI booking with availability validation
     */
    public function bookAppointment(Request $request)
    {
        try {


            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required. Please login to book appointments.',
                    'auth_required' => true
                ], 401);
            }

            $user = auth()->user();

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'doctor_id' => 'required|integer|exists:doctors,id',
                'appointment_date' => 'required|date|after:today',
                'appointment_time' => 'required|string',
                'consultation_fee' => 'required|numeric|min:0',
                'payment_method' => 'required|in:wallet,pay_on_site',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $doctorId = $request->doctor_id;
            $appointmentDate = $request->appointment_date;
            $appointmentTime = $request->appointment_time;
            $consultationFee = $request->consultation_fee;
            $paymentMethod = $request->payment_method;

            // Get user information
            $patientName = $user->first_name . ' ' . $user->last_name;
            $patientEmail = $user->email;
            $patientPhone = $user->phone ?? '';

            // Check doctor availability
            $existingAppointment = DB::table('appointments')
                ->where('doctor_id', $doctorId)
                ->where('appointment_date', $appointmentDate)
                ->where('appointment_time', $appointmentTime)
                ->whereIn('STATUS', ['scheduled', 'confirmed'])
                ->first();

            if ($existingAppointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'This time slot is already booked. Please choose a different time.',
                    'available_slots' => $this->getAvailableSlots($doctorId, $appointmentDate)
                ], 409);
            }

            // Check if doctor works on this day
            $doctor = DB::table('doctors')->where('id', $doctorId)->first();
            if (!$doctor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor not found'
                ], 404);
            }

            // Check working hours (assuming 9 AM to 5 PM)
            $appointmentHour = (int)substr($appointmentTime, 0, 2);
            if ($appointmentHour < 9 || $appointmentHour >= 17) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointments are only available between 9 AM and 5 PM',
                    'available_slots' => $this->getAvailableSlots($doctorId, $appointmentDate)
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Get or create patient record for the authenticated user
                $patient = DB::table('patients')->where('user_id', $user->id)->first();

                if (!$patient) {
                    $patientId = DB::table('patients')->insertGetId([
                        'user_id' => $user->id,
                        'name' => $patientName,
                        'email' => $patientEmail,
                        'phone' => $patientPhone
                    ]);
                } else {
                    $patientId = $patient->id;
                    // Update patient info if needed
                    DB::table('patients')->where('id', $patientId)->update([
                        'name' => $patientName,
                        'phone' => $patientPhone
                    ]);
                }

                // Create appointment
                $appointmentId = DB::table('appointments')->insertGetId([
                    'patient_id' => $patientId,
                    'doctor_id' => $doctorId,
                    'appointment_date' => $appointmentDate,
                    'appointment_time' => $appointmentTime,
                    'consultation_fee' => $consultationFee,
                    'STATUS' => $paymentMethod === 'wallet' ? 'confirmed' : 'scheduled',
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentMethod === 'wallet' ? 'paid' : 'pending',
                    'notes' => 'Booked via AI Assistant',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Process wallet payment if applicable
                if ($paymentMethod === 'wallet') {
                    $wallet = DB::table('wallets')->where('user_id', $patientId)->first();

                    if (!$wallet || $wallet->balance < $consultationFee) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient wallet balance. Please choose "Pay on Site" option.',
                            'wallet_balance' => $wallet ? $wallet->balance : 0,
                            'required_amount' => $consultationFee
                        ], 400);
                    }

                    // Deduct from wallet
                    DB::table('wallets')
                        ->where('user_id', $patientId)
                        ->update([
                            'balance' => $wallet->balance - $consultationFee,
                            'updated_at' => now()
                        ]);

                    // Create wallet transaction
                    DB::table('wallet_transactions')->insert([
                        'wallet_id' => $wallet->id,
                        'amount' => -$consultationFee,
                        'type' => 'debit',
                        'description' => "Appointment booking with Dr. " . $doctor->name,
                        'reference' => 'appointment_' . $appointmentId,
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
                    'appointment_id' => $appointmentId,
                    'payment_status' => $paymentMethod === 'wallet' ? 'paid' : 'pending',
                    'booking_details' => [
                        'patient_id' => $patientId,
                        'doctor_id' => $doctorId,
                        'doctor_name' => $doctor->name,
                        'appointment_date' => $appointmentDate,
                        'appointment_time' => $appointmentTime,
                        'consultation_fee' => $consultationFee,
                        'payment_method' => $paymentMethod
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('AI Booking Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available time slots for a doctor on a specific date
     */
    private function getAvailableSlots($doctorId, $date)
    {
        $bookedSlots = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->whereIn('STATUS', ['scheduled', 'confirmed'])
            ->pluck('appointment_time')
            ->toArray();

        $allSlots = [];
        for ($hour = 9; $hour < 17; $hour++) {
            $timeSlot = sprintf('%02d:00:00', $hour);
            if (!in_array($timeSlot, $bookedSlots)) {
                $allSlots[] = $timeSlot;
            }
        }

        return $allSlots;
    }

    /**
     * Format the AI response for frontend display
     */
    private function formatResponse($data)
    {
        $response = '';
        $intent = $data['intent']['intent'] ?? 'general';
        $specialty = $data['intent']['specialty_hint'] ?? null;

        // Handle Q&A response
        if (isset($data['qa']) && $data['qa'] && isset($data['qa']['answer'])) {
            $response = $data['qa']['answer'];
        }

        // Handle health tips
        if (isset($data['health_tips']) && $data['health_tips'] && isset($data['health_tips']['tips'])) {
            if (empty($response)) {
                $response = "Here are some health tips for {$data['health_tips']['specialty']}:\n\n";
            }
            foreach ($data['health_tips']['tips'] as $i => $tip) {
                $response .= ($i + 1) . ". {$tip}\n";
            }
        }

        // Handle doctor suggestions with enhanced formatting
        if (isset($data['doctors']) && $data['doctors'] && isset($data['doctors']['doctors'])) {
            $doctors = $data['doctors']['doctors'];
            $doctorCount = count($doctors);

            if ($doctorCount > 0) {
                if (empty($response)) {
                    $response = "I found {$doctorCount} available doctors for your appointment:\n\n";
                } else {
                    $response .= "\n\nI also found {$doctorCount} available doctors:\n\n";
                }

                foreach ($doctors as $i => $doctor) {
                    $response .= ($i + 1) . ". **Dr. " . ($doctor['name'] ?? 'Unknown') . "** - " . ($doctor['specialty'] ?? 'General') . "\n";
                    $response .= "   • Experience: " . ($doctor['experience_years'] ?? '5') . " years\n";
                    $response .= "   • Rating: " . ($doctor['rating'] ?? '4.5') . "/5 ⭐\n";
                    $response .= "   • Consultation Fee: \$" . ($doctor['consultation_fee'] ?? '50') . "\n";
                    $response .= "   • Languages: " . ($doctor['languages'] ?? 'English') . "\n\n";
                }

                $response .= "💳 **Payment Options Available:**\n";
                $response .= "• Pay with Wallet (if sufficient balance)\n";
                $response .= "• Pay on Site\n\n";
                $response .= "**To complete your booking:**\n";
                $response .= "1. Select a doctor from the list above\n";
                $response .= "2. Choose your preferred payment method\n";
                $response .= "3. Confirm your appointment\n";

                // Add booking button data for frontend
                $response .= "\n\n[BOOKING_BUTTONS_START]\n";
                foreach ($doctors as $i => $doctor) {
                    $response .= "BUTTON:" . ($doctor['id'] ?? ($i + 1)) . ":" . ($doctor['name'] ?? 'Unknown') . ":" . ($doctor['consultation_fee'] ?? '50') . ":" . ($doctor['specialty'] ?? 'General') . "\n";
                }
                $response .= "[BOOKING_BUTTONS_END]";
            }
        }

        // Handle formatted doctors (enhanced format)
        if (isset($data['formatted_doctors']) && is_array($data['formatted_doctors']) && count($data['formatted_doctors']) > 0) {
            if (empty($response)) {
                $response = "Here are the available doctors for your appointment:\n\n";
            } else {
                $response .= "\n\nAvailable doctors:\n\n";
            }

            foreach ($data['formatted_doctors'] as $doctor) {
                $response .= ($doctor['list_number'] ?? '1') . ". " . ($doctor['display_text'] ?? 'Doctor information not available') . "\n";
            }

            $response .= "\n💳 **Payment Options Available:**\n";
            $response .= "• Pay with Wallet (if sufficient balance)\n";
            $response .= "• Pay on Site\n\n";
            $response .= "**To complete your booking:**\n";
            $response .= "1. Select a doctor from the list above\n";
            $response .= "2. Choose your preferred payment method\n";
            $response .= "3. Confirm your appointment\n";
        }

        // Handle database health tips
        if (isset($data['database_health_tips']) && is_array($data['database_health_tips']) && count($data['database_health_tips']) > 0) {
            if (empty($response)) {
                $response = "Here are some health tips for " . ($specialty ?: 'general health') . ":\n\n";
            } else {
                $response .= "\n\nHealth tips:\n\n";
            }

            foreach ($data['database_health_tips'] as $i => $tip) {
                $response .= ($i + 1) . ". {$tip['tip']}\n";
            }
        }

        // Handle response message
        if (isset($data['response_message']) && !empty($data['response_message'])) {
            if (empty($response)) {
                $response = $data['response_message'];
            } else {
                $response = $data['response_message'] . "\n\n" . $response;
            }
        }

        // Fallback response if no specific data
        if (empty($response)) {
            switch ($intent) {
                case 'book_appointment':
                    if ($specialty) {
                        $response = "I can help you book an appointment with a {$specialty} specialist. Let me find available doctors for you.";
                    } else {
                        $response = "I can help you book an appointment. What specialty are you looking for? You can choose from:\n\n";
                        $response .= "• Cardiology (Heart)\n";
                        $response .= "• Neurology (Brain & Nervous System)\n";
                        $response .= "• Dermatology (Skin)\n";
                        $response .= "• Pediatrics (Children)\n";
                        $response .= "• Orthopedics (Bones & Joints)\n";
                        $response .= "• Psychiatry (Mental Health)\n";
                        $response .= "• Ophthalmology (Eyes)\n";
                        $response .= "• Dentistry (Teeth)\n";
                        $response .= "• Nutrition (Diet)\n\n";
                        $response .= "Please specify your preferred specialty and I'll find available doctors for you.";
                    }
                    break;
                case 'search_doctors':
                    $response = "I'll help you find doctors. What specialty are you looking for?";
                    break;
                case 'health_tips':
                    $response = "Here are some general health tips:\n\n";
                    $response .= "1. Stay hydrated - Drink 8 glasses of water daily\n";
                    $response .= "2. Exercise regularly - Aim for 150 minutes per week\n";
                    $response .= "3. Get adequate sleep - 7-9 hours per night\n";
                    $response .= "4. Eat a balanced diet - Include fruits, vegetables, and lean proteins\n";
                    $response .= "5. Manage stress - Practice relaxation techniques\n";
                    $response .= "6. Regular check-ups - Visit your doctor annually\n";
                    $response .= "7. Avoid smoking and limit alcohol consumption\n";
                    $response .= "8. Maintain a healthy weight\n";
                    break;
                case 'medical_inquiry':
                    $response = "I understand you have a medical question. Please provide more details about your symptoms or concern so I can give you the most accurate information.";
                    break;
                case 'medication_info':
                    $response = "I can help you with medication information. Please specify which medication you'd like to know about, including dosage, interactions, or side effects.";
                    break;
                default:
                    $response = "I'm here to help with your medical needs. How can I assist you today?";
            }
        }

        return $response;
    }

    /**
     * Get suggestions based on intent
     */
    private function getSuggestions($intent)
    {
        switch ($intent) {
            case 'book_appointment':
                return [
                    'Book with a cardiologist',
                    'Book with a dermatologist',
                    'Book with a neurologist',
                    'Book with a pediatrician',
                    'Book with an orthopedist',
                    'Check available slots',
                    'What specialties are available?'
                ];
            case 'search_doctors':
                return [
                    'Find cardiologists',
                    'Find dermatologists',
                    'Find neurologists',
                    'Find pediatricians',
                    'Find psychiatrists',
                    'Find ophthalmologists'
                ];
            case 'medical_inquiry':
                return [
                    'Describe my symptoms',
                    'Get medical advice',
                    'Check medication info',
                    'Find emergency care',
                    'What are the symptoms of diabetes?',
                    'How to treat a fever?'
                ];
            case 'health_tips':
                return [
                    'Tips for heart health',
                    'Tips for mental health',
                    'Tips for better sleep',
                    'Tips for weight management',
                    'Tips for stress relief',
                    'Tips for healthy eating'
                ];
            case 'medication_info':
                return [
                    'What medications interact with aspirin?',
                    'Side effects of common medications',
                    'How to take medications properly',
                    'Medication storage tips',
                    'Over-the-counter vs prescription'
                ];
            default:
                return [
                    'Book an appointment',
                    'Find a doctor',
                    'Get medical advice',
                    'Check medication info'
                ];
        }
    }
}
