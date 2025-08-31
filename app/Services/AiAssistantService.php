<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AiAssistantService
{
    private $flaskUrl;
    private $timeout;
    private $cacheTtl;

    public function __construct()
    {
        // Try to get config values, with fallbacks
        try {
            $this->flaskUrl = config('services.ai.flask_url', 'http://127.0.0.1:5006');
            $this->timeout = config('services.ai.timeout', 10);
            $this->cacheTtl = config('services.ai.cache_ttl', 3600); // 1 hour
        } catch (\Exception $e) {
            // Fallback values if config is not available
            $this->flaskUrl = env('AI_FLASK_URL', 'http://127.0.0.1:5006');
            $this->timeout = env('AI_TIMEOUT', 10);
            $this->cacheTtl = env('AI_CACHE_TTL', 3600);
        }
    }

    /**
     * Health check for AI service
     */
    public function healthCheck()
    {
        try {
            $result = $this->callFlaskAPI('/health');

            if ($result['success']) {
                return [
                    'status' => 'healthy',
                    'flask_service' => $result['data']['status'] ?? 'unknown',
                    'timestamp' => now()
                ];
            } else {
                return [
                    'status' => 'unhealthy',
                    'error' => $result['error'],
                    'timestamp' => now()
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()
            ];
        }
    }

    /**
     * Make HTTP request to Flask backend
     */
    private function callFlaskAPI($endpoint, $data = null)
    {
        $url = $this->flaskUrl . $endpoint;

        try {
            // Check if we're in a Laravel context
            if (class_exists('Illuminate\Support\Facades\Http')) {
                $response = \Illuminate\Support\Facades\Http::timeout($this->timeout)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ]);

                if ($data !== null) {
                    $response = $response->post($url, $data);
                } else {
                    $response = $response->get($url);
                }

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'data' => $response->json()
                    ];
                }

                return [
                    'success' => false,
                    'error' => 'Flask service returned HTTP ' . $response->status()
                ];
            } else {
                // Fallback to cURL if Http facade is not available
                return $this->callFlaskAPIWithCurl($url, $data);
            }

        } catch (\Exception $e) {
            if (class_exists('Illuminate\Support\Facades\Log')) {
                \Illuminate\Support\Facades\Log::error('AI Service Error: ' . $e->getMessage());
            }
            return [
                'success' => false,
                'error' => 'Flask service connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fallback HTTP request using cURL
     */
    private function callFlaskAPIWithCurl($url, $data = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            return [
                'success' => false,
                'error' => 'cURL error: ' . curl_error($ch)
            ];
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'data' => json_decode($response, true)
            ];
        }

        return [
            'success' => false,
            'error' => 'Flask service returned HTTP ' . $httpCode
        ];
    }

    /**
     * Check Flask service health
     */
    public function checkHealth()
    {
        $cacheKey = 'ai_service_health';

        // Check if we're in a Laravel context
        if (class_exists('Illuminate\Support\Facades\Cache')) {
            return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () {
                return $this->callFlaskAPI('/health');
            });
        } else {
            // Fallback without caching
            return $this->callFlaskAPI('/health');
        }
    }

    /**
     * Predict intent from text
     */
    public function predictIntent($text)
    {
        if (empty($text)) {
            return [
                'success' => false,
                'error' => 'Text parameter is required'
            ];
        }

        $cacheKey = 'ai_intent_' . md5($text);

        // Check if we're in a Laravel context
        if (class_exists('Illuminate\Support\Facades\Cache')) {
            return \Illuminate\Support\Facades\Cache::remember($cacheKey, $this->cacheTtl, function () use ($text) {
                return $this->callFlaskAPI('/predict_intent', ['text' => $text]);
            });
        } else {
            // Fallback without caching
            return $this->callFlaskAPI('/predict_intent', ['text' => $text]);
        }
    }

    /**
     * Answer medical question
     */
    public function answerQA($question)
    {
        if (empty($question)) {
            return [
                'success' => false,
                'error' => 'Question parameter is required'
            ];
        }

        $cacheKey = 'ai_qa_' . md5($question);

        // Check if we're in a Laravel context
        if (class_exists('Illuminate\Support\Facades\Cache')) {
            return \Illuminate\Support\Facades\Cache::remember($cacheKey, $this->cacheTtl, function () use ($question) {
                return $this->callFlaskAPI('/answer_qa', ['question' => $question]);
            });
        } else {
            // Fallback without caching
            return $this->callFlaskAPI('/answer_qa', ['question' => $question]);
        }
    }

    /**
     * Suggest doctors by specialty
     */
    public function suggestDoctors($specialty)
    {
        if (empty($specialty)) {
            return [
                'success' => false,
                'error' => 'Specialty parameter is required'
            ];
        }

        // First try to get from cache
        $cacheKey = 'ai_doctors_' . md5($specialty);

        // Check if we're in a Laravel context
        if (class_exists('Illuminate\Support\Facades\Cache')) {
            return \Illuminate\Support\Facades\Cache::remember($cacheKey, $this->cacheTtl, function () use ($specialty) {
                $result = $this->callFlaskAPI('/suggest_doctor', ['specialty' => $specialty]);

                if ($result['success']) {
                    // Enhance with database data
                    $result['data']['doctors'] = $this->enhanceDoctorSuggestions($result['data']['doctors'] ?? []);
                }

                return $result;
            });
        } else {
            // Fallback without caching
            $result = $this->callFlaskAPI('/suggest_doctor', ['specialty' => $specialty]);

            if ($result['success']) {
                // Enhance with database data
                $result['data']['doctors'] = $this->enhanceDoctorSuggestions($result['data']['doctors'] ?? []);
            }

            return $result;
        }
    }

    /**
     * Process text comprehensively (intent + Q&A + doctors)
     */
    public function processText($text, $userId = null)
    {
        if (empty($text)) {
            return [
                'success' => false,
                'error' => 'Text parameter is required'
            ];
        }

        $cacheKey = 'ai_process_' . md5($text . $userId);

        // Check if we're in a Laravel context
        if (class_exists('Illuminate\Support\Facades\Cache')) {
            return \Illuminate\Support\Facades\Cache::remember($cacheKey, $this->cacheTtl, function () use ($text, $userId) {
                $result = $this->callFlaskAPI('/process', ['text' => $text]);

                if ($result['success']) {
                    // Enhance with user-specific data
                    $result['data'] = $this->enhanceWithUserData($result['data'], $userId);

                    // Format the response for the frontend
                    $formattedResponse = $this->formatResponse($result['data'], $text);
                    $result['data']['response'] = $formattedResponse;
                }

                return $result;
            });
        } else {
            // Fallback without caching
            $result = $this->callFlaskAPI('/process', ['text' => $text]);

            if ($result['success']) {
                // Enhance with user-specific data
                $result['data'] = $this->enhanceWithUserData($result['data'], $userId);

                // Format the response for the frontend
                $formattedResponse = $this->formatResponse($result['data'], $text);
                $result['data']['response'] = $formattedResponse;
            }

            return $result;
        }
    }

    /**
     * Handle voice input with STT transcript
     */
    public function processVoiceInput($transcript, $userId = null)
    {
        if (empty($transcript)) {
            return [
                'success' => false,
                'error' => 'Voice transcript is required'
            ];
        }

        // Process the transcript
        $result = $this->processText($transcript, $userId);

        if ($result['success']) {
            $data = $result['data'];

            // Add voice-specific response formatting
            $response = [
                'input_text' => $transcript,
                'intent' => $data['intent'] ?? null,
                'qa' => $data['qa'] ?? null,
                'doctors' => $data['doctors'] ?? null,
                'voice_response' => $this->formatVoiceResponse($data)
            ];

            return [
                'success' => true,
                'data' => $response
            ];
        }

        return $result;
    }

    /**
     * Enhance doctor suggestions with database data
     */
    private function enhanceDoctorSuggestions($suggestions)
    {
        if (empty($suggestions)) {
            return [];
        }

        $enhancedSuggestions = [];

        foreach ($suggestions as $suggestion) {
            // Find matching doctors in database
            $doctors = DB::table('doctors')
                ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
                ->where('doctors.is_active', true)
                ->where(function ($query) use ($suggestion) {
                    $query->where('specialties.name_en', 'like', '%' . $suggestion['specialty'] . '%')
                          ->orWhere('specialties.name_ar', 'like', '%' . $suggestion['specialty'] . '%');
                })
                ->select(
                    'doctors.*',
                    'specialties.name_en as specialty_name',
                    'specialties.name_ar as specialty_name_ar'
                )
                ->orderBy('doctors.rating', 'desc')
                ->limit(5)
                ->get();

            $enhancedSuggestions[] = [
                'ai_suggestion' => $suggestion,
                'available_doctors' => $doctors,
                'total_doctors' => $doctors->count()
            ];
        }

        return $enhancedSuggestions;
    }

    /**
     * Enhance AI response with user-specific data
     */
    private function enhanceWithUserData($data, $userId)
    {
        if (!$userId) {
            return $data;
        }

        // Get user information
        $user = DB::table('users')->where('id', $userId)->first();
        if (!$user) {
            return $data;
        }

        // Add user context
        $data['user_context'] = [
            'role' => $user->role,
            'name' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email
        ];

        // Add role-specific data
        switch ($user->role) {
            case 'patient':
                $patient = DB::table('patients')->where('user_id', $userId)->first();
                if ($patient) {
                    $data['user_context']['patient'] = [
                        'medical_history' => $patient->medical_history,
                        'blood_type' => $patient->blood_type,
                        'emergency_contact' => $patient->emergency_contact
                    ];

                    // Get recent appointments
                    $recentAppointments = DB::table('appointments')
                        ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
                        ->where('appointments.patient_id', $patient->id)
                        ->orderBy('appointments.appointment_date', 'desc')
                        ->limit(3)
                        ->get();

                    $data['user_context']['recent_appointments'] = $recentAppointments;
                }
                break;

            case 'doctor':
                $doctor = DB::table('doctors')
                    ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
                    ->where('doctors.user_id', $userId)
                    ->first();
                if ($doctor) {
                    $data['user_context']['doctor'] = [
                        'specialty' => $doctor->specialty_name,
                        'experience_years' => $doctor->experience_years,
                        'consultation_fee' => $doctor->consultation_fee
                    ];
                }
                break;
        }

        return $data;
    }

    /**
     * Format response for voice output
     */
    private function formatVoiceResponse($data)
    {
        $responseParts = [];

        // Add intent-based response
        if (isset($data['intent']) && $data['intent']['intent'] !== 'unknown') {
            $intent = $data['intent']['intent'];
            $confidence = $data['intent']['confidence'];

            switch ($intent) {
                case 'book_appointment':
                    $responseParts[] = 'I can help you book an appointment.';
                    break;
                case 'search_doctors':
                    $responseParts[] = 'I\'ll help you find doctors.';
                    break;
                case 'health_tips':
                    $responseParts[] = 'Here are some health tips for you.';
                    break;
                case 'medical_inquiry':
                    $responseParts[] = 'I\'ll answer your medical question.';
                    break;
                default:
                    $responseParts[] = 'I understand you need medical assistance.';
            }
        }

        // Add Q&A response
        if (isset($data['qa']) && $data['qa'] && isset($data['qa']['answer'])) {
            $responseParts[] = $data['qa']['answer'];
        }

        // Add doctor suggestions
        if (isset($data['doctors']) && $data['doctors'] && isset($data['doctors']['doctors'])) {
            $doctorCount = count($data['doctors']['doctors']);
            if ($doctorCount > 0) {
                $responseParts[] = "I found {$doctorCount} doctors for you.";
            }
        }

        // Fallback if no specific response
        if (empty($responseParts)) {
            $responseParts[] = 'I\'m here to help with your medical needs. How can I assist you?';
        }

        return implode(' ', $responseParts);
    }

    /**
     * Get appointment booking suggestions
     */
    public function getAppointmentSuggestions($specialty, $preferredDate = null, $urgency = null)
    {
        $query = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('doctors.is_active', true)
            ->select('doctors.*', 'specialties.name_en as specialty_name');

        if ($specialty) {
            $query->where('specialties.name_en', 'like', '%' . $specialty . '%');
        }

        $doctors = $query->get();

        $suggestions = [];
        foreach ($doctors as $doctor) {
            $nextAvailable = $this->getNextAvailableSlot($doctor->id);

            if ($nextAvailable) {
                $suggestions[] = [
                    'doctor' => $doctor,
                    'next_available' => $nextAvailable,
                    'urgency_match' => $this->assessUrgencyMatch($doctor, $urgency)
                ];
            }
        }

        return collect($suggestions)->sortBy('urgency_match')->values();
    }

    /**
     * Get next available slot for doctor
     */
    private function getNextAvailableSlot($doctorId)
    {
        $today = Carbon::today();

        for ($i = 0; $i < 14; $i++) {
            $date = $today->copy()->addDays($i);
            $dayOfWeek = $date->dayOfWeek;

            $workingHours = DB::table('working_hours')
                ->where('doctor_id', $doctorId)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_available', true)
                ->first();

            if ($workingHours) {
                return [
                    'date' => $date->format('Y-m-d'),
                    'time' => $workingHours->start_time
                ];
            }
        }

        return null;
    }

    /**
     * Assess urgency match
     */
    private function assessUrgencyMatch($doctor, $urgency)
    {
        if ($urgency === 'emergency') {
            return $doctor->is_emergency_available ? 1 : 5;
        }

        return 3; // Default medium priority
    }

    /**
     * Save conversation to database
     */
    public function saveConversation($userId, $userMessage, $aiResponse, $intent)
    {
        try {
            DB::table('ai_conversations')->insert([
                'user_id' => $userId,
                'user_message' => $userMessage,
                'ai_response' => $aiResponse,
                'intent' => $intent,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error saving AI conversation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get conversation history
     */
    public function getConversationHistory($userId, $limit = 20)
    {
        return DB::table('ai_conversations')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Clear conversation history
     */
    public function clearConversationHistory($userId)
    {
        try {
            DB::table('ai_conversations')
                ->where('user_id', $userId)
                ->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Error clearing AI conversation history: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get AI suggestions based on context
     */
    public function getSuggestions($userId, $context = 'general')
    {
        $suggestions = [];

        switch ($context) {
            case 'booking':
                $suggestions = [
                    'Book an appointment',
                    'Find a specialist',
                    'Check doctor availability',
                    'View my appointments'
                ];
                break;

            case 'medical':
                $suggestions = [
                    'Describe my symptoms',
                    'Get medical advice',
                    'Find emergency care',
                    'Check medication information'
                ];
                break;

            default:
                $suggestions = [
                    'Book an appointment',
                    'Find a doctor',
                    'Get medical advice',
                    'Check medication info',
                    'Payment questions'
                ];
        }

        return $suggestions;
    }

    /**
     * Analyze symptoms and provide advice
     */
    public function analyzeSymptoms($symptoms, $duration = null, $severity = null)
    {
        $symptoms = strtolower($symptoms);

        $advice = "Based on your symptoms, here's what I can suggest:\n\n";

        // Basic symptom analysis
        if (str_contains($symptoms, 'fever') || str_contains($symptoms, 'temperature')) {
            $advice .= "• Monitor your temperature regularly\n";
            $advice .= "• Stay hydrated and rest\n";
            $advice .= "• Consider consulting a doctor if fever persists\n\n";
        }

        if (str_contains($symptoms, 'headache') || str_contains($symptoms, 'migraine')) {
            $advice .= "• Rest in a quiet, dark room\n";
            $advice .= "• Stay hydrated\n";
            $advice .= "• Consider over-the-counter pain relievers\n\n";
        }

        if (str_contains($symptoms, 'cough') || str_contains($symptoms, 'cold')) {
            $advice .= "• Rest and stay hydrated\n";
            $advice .= "• Use honey for cough relief\n";
            $advice .= "• Consider steam inhalation\n\n";
        }

        $advice .= "**Important**: This is general advice only. For specific medical concerns, please consult a healthcare professional.";

        return $advice;
    }

    /**
     * Get relevant specialists for symptoms
     */
    public function getRelevantSpecialists($symptoms)
    {
        $symptoms = strtolower($symptoms);
        $specialists = [];

        if (str_contains($symptoms, 'heart') || str_contains($symptoms, 'chest pain')) {
            $specialists[] = 'Cardiologist';
        }

        if (str_contains($symptoms, 'head') || str_contains($symptoms, 'brain')) {
            $specialists[] = 'Neurologist';
        }

        if (str_contains($symptoms, 'stomach') || str_contains($symptoms, 'digestive')) {
            $specialists[] = 'Gastroenterologist';
        }

        if (str_contains($symptoms, 'skin') || str_contains($symptoms, 'rash')) {
            $specialists[] = 'Dermatologist';
        }

        return $specialists;
    }

        /**
     * Assess urgency level
     */
    public function assessUrgency($symptoms, $severity = null)
    {
        $symptoms = strtolower($symptoms);

        if (str_contains($symptoms, 'chest pain') || str_contains($symptoms, 'difficulty breathing')) {
            return 'emergency';
        }

        if ($severity === 'severe') {
            return 'high';
        }

        if ($severity === 'moderate') {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Format response for frontend display
     */
    private function formatResponse($data, $originalText)
    {
        $response = '';
        $bookingButtons = [];



        // Handle Q&A response
        if (isset($data['qa']) && $data['qa'] && isset($data['qa']['answer'])) {
            $response = $data['qa']['answer'];
        }

        // Handle existing response from Flask service (only if no doctors found)
        if (isset($data['response']) && !empty($data['response']) && empty($data['doctors'])) {
            $response = $data['response'];
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

                // Handle doctor suggestions with enhanced formatting (takes priority)
        if (isset($data['doctors']) && $data['doctors'] && isset($data['doctors']['doctors'])) {
            $doctors = $data['doctors']['doctors'];
            $specialty = $data['doctors']['specialty'] ?? 'your appointment';

            // Get real doctors from database for this specialty
            $realDoctors = DB::table('doctors')
                ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
                ->where('doctors.is_active', true)
                ->where(function ($query) use ($specialty) {
                    $query->where('specialties.name_en', 'like', '%' . $specialty . '%')
                          ->orWhere('specialties.name_ar', 'like', '%' . $specialty . '%')
                          ->orWhere('specialties.name_en', 'like', '%' . strtolower($specialty) . '%');
                })
                ->select(
                    'doctors.*',
                    'specialties.name_en as specialty_name',
                    'specialties.name_ar as specialty_name_ar'
                )
                ->orderBy('doctors.rating', 'desc')
                ->limit(5)
                ->get();

            if ($realDoctors->count() > 0) {
                $doctorCount = $realDoctors->count();
                $response = "I found {$doctorCount} available doctors for {$specialty}:\n\n";

                foreach ($realDoctors as $i => $doctor) {
                    $name = $doctor->name ?? 'Unknown Doctor';
                    $specialty = $doctor->specialty_name ?? 'General';
                    $experience = $doctor->experience_years ?? '5';
                    $rating = $doctor->rating ?? '4.5';
                    $fee = $doctor->consultation_fee ?? '200';
                    $languages = $doctor->languages ?? 'English';

                    // Clean up name (remove duplicate "Dr.")
                    $cleanName = preg_replace('/^Dr\.\s*Dr\.\s*/', 'Dr. ', $name);
                    $cleanName = preg_replace('/^Dr\.\s*/', 'Dr. ', $cleanName);

                    $response .= ($i + 1) . ". **{$cleanName}** - {$specialty}\n";
                    $response .= "   • Experience: {$experience} years\n";
                    $response .= "   • Rating: {$rating}/5 ⭐\n";
                    $response .= "   • Consultation Fee: \${$fee}\n";
                    $response .= "   • Languages: {$languages}\n\n";

                    // Add to booking buttons with real database doctor ID
                    $bookingButtons[] = [
                        'id' => $doctor->id, // Real database ID
                        'name' => $cleanName,
                        'fee' => $fee,
                        'specialty' => $specialty
                    ];
                }

                // Add payment options
                $response .= "💳 **Payment Options Available:**\n";
                $response .= "• Pay with Wallet (if sufficient balance)\n";
                $response .= "• Pay on Site\n\n";

                $response .= "**To complete your booking:**\n";
                $response .= "1. Select a doctor from the list above\n";
                $response .= "2. Choose your preferred payment method\n";
                $response .= "3. Confirm your appointment\n\n";

                // Add booking buttons data
                if (!empty($bookingButtons)) {
                    $response .= "[BOOKING_BUTTONS_START]\n";
                    foreach ($bookingButtons as $button) {
                        $response .= "BUTTON:{$button['id']}:{$button['name']}:{$button['fee']}:{$button['specialty']}\n";
                    }
                    $response .= "[BOOKING_BUTTONS_END]\n";
                }
            } else {
                // Fallback: No real doctors found, use AI suggestions but with warning
                $doctorCount = count($doctors);
                $response = "I found {$doctorCount} doctors for {$specialty}, but they may not be available for booking:\n\n";

                foreach ($doctors as $i => $doctor) {
                    $name = $doctor['name'] ?? $doctor['doctor_name'] ?? 'Unknown Doctor';
                    $specialty = $doctor['specialty'] ?? $doctor['specialty_name'] ?? 'General';
                    $experience = $doctor['experience_years'] ?? '5';
                    $rating = $doctor['rating'] ?? '4.5';
                    $fee = $doctor['consultation_fee'] ?? '200';
                    $languages = $doctor['languages'] ?? 'English';

                    // Clean up name (remove duplicate "Dr.")
                    $cleanName = preg_replace('/^Dr\.\s*Dr\.\s*/', 'Dr. ', $name);
                    $cleanName = preg_replace('/^Dr\.\s*/', 'Dr. ', $cleanName);

                    $response .= ($i + 1) . ". **{$cleanName}** - {$specialty}\n";
                    $response .= "   • Experience: {$experience} years\n";
                    $response .= "   • Rating: {$rating}/5 ⭐\n";
                    $response .= "   • Consultation Fee: \${$fee}\n";
                    $response .= "   • Languages: {$languages}\n\n";
                }

                $response .= "⚠️ **Note:** These doctors may not be available for immediate booking. Please contact our support team for assistance.\n\n";
            }
        }

        // Fallback response
        if (empty($response)) {
            $intent = $data['intent']['intent'] ?? 'general';
            switch ($intent) {
                case 'book_appointment':
                    $response = "I can help you book an appointment. Please specify your preferred specialty and date.";
                    break;
                case 'search_doctors':
                    $response = "I'll help you find doctors. What specialty are you looking for?";
                    break;
                case 'health_tips':
                    $response = "Here are some general health tips: Stay hydrated, exercise regularly, get adequate sleep, and eat a balanced diet.";
                    break;
                default:
                    $response = "I'm here to help with your medical needs. How can I assist you today?";
            }
        }

        return $response;
    }
}
