<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AiBookingService
{
    private $flaskUrl;
    private $timeout;

    public function __construct()
    {
        $this->flaskUrl = config('services.ai.flask_url', 'http://127.0.0.1:5005');
        $this->timeout = config('services.ai.timeout', 10);
    }

    /**
     * Process AI intent and execute booking flow
     */
    public function processBookingIntent(string $userMessage, int $userId)
    {
        // Check if AI booking is enabled
        if (!config('services.ai.booking_enabled', false)) {
            return [
                'success' => false,
                'message' => 'AI booking is currently disabled',
                'fallback' => true
            ];
        }

        try {
            // Step 1: Get AI intent from Flask service
            $intentResult = $this->getAiIntent($userMessage);

            if (!$intentResult['success']) {
                return $intentResult;
            }

            $intent = $intentResult['data']['intent']['intent'] ?? 'general';
            $confidence = $intentResult['data']['intent']['confidence'] ?? 0;

            // Step 2: Handle different intents
            switch ($intent) {
                case 'book_appointment':
                    return $this->handleAppointmentBooking($userMessage, $userId, $intentResult['data']);

                case 'search_doctors':
                    return $this->handleDoctorSearch($userMessage, $userId, $intentResult['data']);

                case 'check_availability':
                    return $this->handleAvailabilityCheck($userMessage, $userId, $intentResult['data']);

                default:
                    return [
                        'success' => true,
                        'message' => 'I understand you want to book an appointment. Let me help you with that.',
                        'intent' => $intent,
                        'confidence' => $confidence,
                        'suggestions' => $this->getBookingSuggestions()
                    ];
            }

        } catch (\Exception $e) {
            Log::error('AI Booking Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Sorry, I encountered an error processing your booking request.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get AI intent from Flask service
     */
    private function getAiIntent(string $message)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->flaskUrl . '/process', ['text' => $message]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'AI service is temporarily unavailable'
                ];
            }

            return [
                'success' => true,
                'data' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('AI Intent Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Unable to process your request at this time'
            ];
        }
    }

    /**
     * Handle appointment booking flow
     */
    private function handleAppointmentBooking(string $message, int $userId, array $aiData)
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $userId)->first();

        if (!$patient) {
            return [
                'success' => false,
                'message' => 'Patient profile not found. Please complete your profile first.'
            ];
        }

        // Extract specialty hint from AI response
        $specialtyHint = $aiData['intent']['specialty_hint'] ?? null;

        // Get available doctors by specialty
        $doctors = $this->getAvailableDoctors($specialtyHint);

        return [
            'success' => true,
            'message' => 'I found some doctors for you. Here are the available options:',
            'intent' => 'book_appointment',
            'doctors' => $doctors,
            'suggestions' => [
                'Select a doctor from the list above',
                'Tell me your preferred date and time',
                'Ask about consultation fees'
            ]
        ];
    }

    /**
     * Handle doctor search
     */
    private function handleDoctorSearch(string $message, int $userId, array $aiData)
    {
        $specialtyHint = $aiData['intent']['specialty_hint'] ?? null;
        $doctors = $this->getAvailableDoctors($specialtyHint);

        return [
            'success' => true,
            'message' => 'Here are the doctors I found for you:',
            'intent' => 'search_doctors',
            'doctors' => $doctors,
            'suggestions' => [
                'Book an appointment with any doctor',
                'View doctor details and reviews',
                'Check consultation fees'
            ]
        ];
    }

    /**
     * Handle availability check
     */
    private function handleAvailabilityCheck(string $message, int $userId, array $aiData)
    {
        // Extract doctor and date from message (simplified)
        $doctorId = $this->extractDoctorId($message);
        $date = $this->extractDate($message);

        if (!$doctorId || !$date) {
            return [
                'success' => true,
                'message' => 'Please specify which doctor and date you\'d like to check availability for.',
                'intent' => 'check_availability',
                'suggestions' => [
                    'Tell me the doctor\'s name',
                    'Specify your preferred date',
                    'Browse available doctors first'
                ]
            ];
        }

        $availability = $this->getDoctorAvailability($doctorId, $date);

        return [
            'success' => true,
            'message' => 'Here are the available time slots:',
            'intent' => 'check_availability',
            'availability' => $availability,
            'suggestions' => [
                'Book an appointment at an available time',
                'Check a different date',
                'View other doctors'
            ]
        ];
    }

    /**
     * Get available doctors by specialty (with caching)
     */
    public function getAvailableDoctors(?string $specialty = null, int $limit = 10)
    {
        // Create cache key
        $cacheKey = "doctors_" . ($specialty ?? 'all') . "_" . $limit;

        // Try to get from cache first
        return Cache::remember($cacheKey, 300, function () use ($specialty, $limit) {
            $query = DB::table('doctors')
                ->join('users', 'doctors.user_id', '=', 'users.id')
                ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
                ->where('doctors.is_active', true)
                ->where('users.role', 'doctor')
                ->where('users.status', 'active')
                ->select(
                    'doctors.id',
                    'doctors.name',
                    'doctors.consultation_fee',
                    'doctors.rating',
                    'doctors.experience_years',
                    'doctors.description',
                    'specialties.name_en as specialty'
                );

            if ($specialty) {
                // Enhanced specialty matching with common terms
                $specialtyTerms = [
                    'eye' => ['ophthalmology', 'eye', 'vision', 'ophthalmologist'],
                    'heart' => ['cardiology', 'heart', 'cardiovascular', 'cardiologist'],
                    'brain' => ['neurology', 'brain', 'neurologist'],
                    'child' => ['pediatrics', 'pediatric', 'child', 'children'],
                    'pregnancy' => ['obstetrics', 'gynecology', 'obstetrician', 'gynecologist'],
                    'teeth' => ['dentistry', 'dental', 'dentist'],
                    'skin' => ['dermatology', 'dermatologist', 'skin'],
                    'bone' => ['orthopedics', 'orthopedic', 'bone', 'joint'],
                    'cancer' => ['oncology', 'oncologist', 'cancer'],
                    'mental' => ['psychiatry', 'psychologist', 'mental', 'psychiatric']
                ];

                $specialtyLower = strtolower($specialty);
                $matchedSpecialties = [];

                foreach ($specialtyTerms as $keyword => $terms) {
                    if (strpos($specialtyLower, $keyword) !== false) {
                        $matchedSpecialties = array_merge($matchedSpecialties, $terms);
                    }
                }

                if (!empty($matchedSpecialties)) {
                    $query->where(function($q) use ($matchedSpecialties) {
                        foreach ($matchedSpecialties as $term) {
                            $q->orWhere('specialties.name_en', 'like', '%' . $term . '%');
                        }
                    });
                } else {
                    // Fallback to original matching
                    $query->where('specialties.name_en', 'like', '%' . $specialty . '%');
                }
            }

            return $query->orderBy('doctors.rating', 'desc')
                ->orderBy('doctors.experience_years', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get doctor availability for a specific date
     */
    public function getDoctorAvailability(int $doctorId, string $date)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        // Get working hours
        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$workingHours) {
            return [];
        }

        // Get booked appointments
        $bookedSlots = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('appointment_time')
            ->toArray();

        // Generate available time slots
        $startTime = Carbon::parse($workingHours->start_time);
        $endTime = Carbon::parse($workingHours->end_time);
        $slotDuration = 30; // 30 minutes per slot
        $availableSlots = [];

        while ($startTime < $endTime) {
            $timeSlot = $startTime->format('H:i:s');

            if (!in_array($timeSlot, $bookedSlots)) {
                $availableSlots[] = [
                    'time' => $timeSlot,
                    'formatted_time' => $startTime->format('g:i A'),
                    'available' => true
                ];
            }

            $startTime->addMinutes($slotDuration);
        }

        return $availableSlots;
    }

    /**
     * Book appointment with wallet integration
     */
    public function bookAppointmentWithWallet(int $doctorId, string $date, string $time, int $userId)
    {
        // Check if wallet integration is enabled
        if (!config('services.ai.wallet_integration', false)) {
            return [
                'success' => false,
                'message' => 'Wallet integration is currently disabled'
            ];
        }

        DB::beginTransaction();
        try {
            // Get doctor and consultation fee
            $doctor = DB::table('doctors')->where('id', $doctorId)->first();
            if (!$doctor) {
                throw new \Exception('Doctor not found');
            }

            $consultationFee = $doctor->consultation_fee;

            // Get patient
            $patient = DB::table('patients')->where('user_id', $userId)->first();
            if (!$patient) {
                throw new \Exception('Patient profile not found');
            }

            // Get wallet
            $wallet = DB::table('wallets')->where('user_id', $userId)->first();
            if (!$wallet) {
                throw new \Exception('Wallet not found');
            }

            // Check wallet balance
            if ($wallet->balance < $consultationFee) {
                throw new \Exception('Insufficient wallet balance. Required: $' . $consultationFee . ', Available: $' . $wallet->balance);
            }

            // Check availability
            $availability = $this->getDoctorAvailability($doctorId, $date);
            $timeAvailable = collect($availability)->where('time', $time)->first();

            if (!$timeAvailable) {
                throw new \Exception('Selected time slot is not available');
            }

            // Create appointment
            $appointmentId = DB::table('appointments')->insertGetId([
                'patient_id' => $patient->id,
                'doctor_id' => $doctorId,
                'appointment_date' => $date,
                'appointment_time' => $time,
                'status' => 'scheduled',
                'consultation_fee' => $consultationFee,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Deduct from wallet
            DB::table('wallets')
                ->where('id', $wallet->id)
                ->update([
                    'balance' => DB::raw("balance - $consultationFee"),
                    'total_spent' => DB::raw("total_spent + $consultationFee"),
                    'last_transaction_at' => now(),
                    'updated_at' => now(),
                ]);

            // Create wallet transaction
            DB::table('wallet_transactions')->insert([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $consultationFee,
                'description' => "Appointment booking with Dr. {$doctor->name}",
                'status' => 'completed',
                'metadata' => json_encode([
                    'appointment_id' => $appointmentId,
                    'doctor_id' => $doctorId,
                    'appointment_date' => $date,
                    'appointment_time' => $time,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create payment record
            DB::table('payments')->insert([
                'appointment_id' => $appointmentId,
                'amount' => $consultationFee,
                'payment_method' => 'wallet',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => "Appointment booked successfully with Dr. {$doctor->name} on {$date} at " . Carbon::parse($time)->format('g:i A'),
                'appointment_id' => $appointmentId,
                'consultation_fee' => $consultationFee,
                'wallet_balance' => $wallet->balance - $consultationFee
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('AI Booking Error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Extract doctor ID from message (simplified)
     */
    private function extractDoctorId(string $message)
    {
        // This is a simplified extraction - in a real implementation,
        // you might use NLP or pattern matching
        if (preg_match('/doctor\s+(\d+)/i', $message, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    /**
     * Extract date from message (simplified)
     */
    private function extractDate(string $message)
    {
        // This is a simplified extraction - in a real implementation,
        // you might use NLP or date parsing libraries
        if (preg_match('/(\d{4}-\d{2}-\d{2})/', $message, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Get booking suggestions
     */
    private function getBookingSuggestions()
    {
        return [
            'Browse doctors by specialty',
            'Check appointment availability',
            'View consultation fees',
            'Add funds to wallet'
        ];
    }
}
