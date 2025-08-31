<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\AiAssistantService;
use Carbon\Carbon;

class AiAssistantController extends Controller
{
    protected $aiService;

    /**
     * Create a new controller instance.
     */
    public function __construct(AiAssistantService $aiService)
    {
        $this->middleware('auth');
        $this->aiService = $aiService;
    }

    /**
     * Display AI assistant interface
     */
    public function index()
    {
        $user = Auth::user();
        $conversationHistory = $this->aiService->getConversationHistory($user->id);

        return view('ai-assistant.index', compact('conversationHistory'));
    }

    /**
     * Health check for AI service
     */
    public function health()
    {
        try {
            // Check if AI service is available
            $healthCheck = $this->aiService->healthCheck();

            return response()->json([
                'status' => 'healthy',
                'ai_service' => $healthCheck['status'] ?? 'unknown',
                'timestamp' => now(),
                'message' => 'AI Assistant is ready'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()
            ], 503);
        }
    }

    /**
     * Process user message and generate AI response
     */
    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'context' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $message = $request->message;
        $context = $request->context;

        try {
            // Process text with AI service
            $result = $this->aiService->processText($message, $user->id);

            if ($result['success']) {
                $data = $result['data'];

                // Extract intent from the AI response
                $intent = $data['intent']['intent'] ?? 'general';

                // Build response from AI data
                $response = $this->buildResponseFromAiData($data, $message);

                // Save conversation
                $this->aiService->saveConversation($user->id, $message, $response, $intent);

                return response()->json([
                    'success' => true,
                    'response' => $response,
                    'intent' => $intent,
                    'data' => $data,
                    'suggestions' => $this->aiService->getSuggestions($user->id, $intent)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'AI service error: ' . ($result['error'] ?? 'Unknown error')
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing your request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get appointment booking assistance
     */
    public function bookAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'specialty' => 'required|string|max:100',
            'preferred_date' => 'nullable|date|after:today',
            'preferred_time' => 'nullable|string',
            'urgency' => 'nullable|in:low,medium,high,emergency',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        try {
            // Get appointment suggestions using AI service
            $suggestions = $this->aiService->getAppointmentSuggestions(
                $request->specialty,
                $request->preferred_date,
                $request->urgency
            );

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions,
                'message' => $this->generateBookingMessage($suggestions, $request->urgency)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error finding appointments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get medical advice and information
     */
    public function getMedicalAdvice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symptoms' => 'required|string|max:500',
            'duration' => 'nullable|string|max:100',
            'severity' => 'nullable|in:mild,moderate,severe',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $symptoms = $request->symptoms;
            $duration = $request->duration;
            $severity = $request->severity;

            // Analyze symptoms and provide advice using AI service
            $advice = $this->aiService->analyzeSymptoms($symptoms, $duration, $severity);

            // Get relevant specialists
            $specialists = $this->aiService->getRelevantSpecialists($symptoms);

            // Get urgency level
            $urgency = $this->aiService->assessUrgency($symptoms, $severity);

            return response()->json([
                'success' => true,
                'advice' => $advice,
                'specialists' => $specialists,
                'urgency' => $urgency,
                'recommendations' => $this->getRecommendations($urgency)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing symptoms: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get medication information
     */
    public function getMedicationInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medication' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $medication = $request->medication;

            // Get medication information
            $info = $this->getMedicationDetails($medication);

            // Check for interactions
            $interactions = $this->checkInteractions($medication);

            // Get side effects
            $sideEffects = $this->getSideEffects($medication);

            return response()->json([
                'success' => true,
                'medication' => $info,
                'interactions' => $interactions,
                'side_effects' => $sideEffects,
                'warnings' => $this->getWarnings($medication)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting medication info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conversation history
     */
    public function getHistory()
    {
        $user = Auth::user();
        $history = $this->aiService->getConversationHistory($user->id);

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    /**
     * Clear conversation history
     */
    public function clearHistory()
    {
        $user = Auth::user();

        $this->aiService->clearConversationHistory($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Conversation history cleared successfully'
        ]);
    }

    /**
     * Get AI suggestions based on user context
     */
    public function getSuggestions(Request $request)
    {
        $user = Auth::user();
        $context = $request->get('context', 'general');

        $suggestions = $this->aiService->getSuggestions($user->id, $context);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Analyze user intent from message
     */
    private function analyzeIntent($message)
    {
        $message = strtolower($message);

        if (str_contains($message, 'book') || str_contains($message, 'appointment') || str_contains($message, 'schedule')) {
            return 'booking';
        }

        if (str_contains($message, 'symptom') || str_contains($message, 'pain') || str_contains($message, 'hurt') || str_contains($message, 'feel')) {
            return 'medical_advice';
        }

        if (str_contains($message, 'medication') || str_contains($message, 'medicine') || str_contains($message, 'drug')) {
            return 'medication_info';
        }

        if (str_contains($message, 'doctor') || str_contains($message, 'specialist')) {
            return 'find_doctor';
        }

        if (str_contains($message, 'payment') || str_contains($message, 'bill') || str_contains($message, 'cost')) {
            return 'payment_info';
        }

        return 'general';
    }

    /**
     * Generate AI response based on intent
     */
    private function generateResponse($message, $intent, $user, $context = null)
    {
        switch ($intent) {
            case 'booking':
                return $this->generateBookingResponse($message, $user);

            case 'medical_advice':
                return $this->generateMedicalAdviceResponse($message, $user);

            case 'medication_info':
                return $this->generateMedicationResponse($message, $user);

            case 'find_doctor':
                return $this->generateDoctorResponse($message, $user);

            case 'payment_info':
                return $this->generatePaymentResponse($message, $user);

            default:
                return $this->generateGeneralResponse($message, $user, $context);
        }
    }

    /**
     * Generate booking response
     */
    private function generateBookingResponse($message, $user)
    {
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return "I can help you book an appointment. First, please complete your patient profile.";
        }

        $recentAppointments = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->where('appointments.patient_id', $patient->id)
            ->orderBy('appointments.appointment_date', 'desc')
            ->limit(3)
            ->get();

        if ($recentAppointments->count() > 0) {
            return "I can help you book an appointment. I see you've recently visited " .
                   $recentAppointments->first()->name . ". Would you like to book with the same doctor or find a different specialist?";
        }

        return "I can help you book an appointment. What type of specialist are you looking for? I can show you available doctors and their schedules.";
    }

    /**
     * Generate medical advice response
     */
    private function generateMedicalAdviceResponse($message, $user)
    {
        return "I can help you understand your symptoms and guide you to the right specialist. However, please note that I cannot provide medical diagnosis. For serious symptoms, please consult a healthcare professional immediately. What symptoms are you experiencing?";
    }

    /**
     * Generate medication response
     */
    private function generateMedicationResponse($message, $user)
    {
        return "I can provide general information about medications, including side effects and interactions. However, for specific medical advice about your medications, please consult your doctor. What medication would you like to know more about?";
    }

    /**
     * Generate doctor response
     */
    private function generateDoctorResponse($message, $user)
    {
        $specialties = DB::table('specialties')->select('name_en', 'name_ar')->get();

        return "I can help you find the right doctor. We have specialists in various fields including " .
               $specialties->pluck('name_en')->implode(', ') . ". What type of specialist are you looking for?";
    }

    /**
     * Generate payment response
     */
    private function generatePaymentResponse($message, $user)
    {
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if ($patient) {
            $wallet = DB::table('wallets')->where('user_id', $user->id)->first();
            $balance = $wallet ? $wallet->balance : 0;

            return "I can help you with payment information. Your current wallet balance is $" . number_format($balance, 2) . ". What payment information do you need?";
        }

        return "I can help you with payment information. What specific payment question do you have?";
    }

    /**
     * Generate general response
     */
    private function generateGeneralResponse($message, $user, $context)
    {
        return "Hello! I'm your AI medical assistant. I can help you with booking appointments, finding doctors, understanding symptoms, medication information, and payment questions. How can I assist you today?";
    }

    /**
     * Find available doctors
     */
    private function findAvailableDoctors($specialty, $preferredDate = null, $preferredTime = null)
    {
        $query = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('doctors.is_active', true)
            ->select('doctors.*', 'specialties.name_en as specialty_name');

        if ($specialty) {
            $query->where('specialties.name_en', 'like', '%' . $specialty . '%');
        }

        $doctors = $query->get();

        // Filter by availability if date/time provided
        if ($preferredDate && $preferredTime) {
            $doctors = $doctors->filter(function ($doctor) use ($preferredDate, $preferredTime) {
                return $this->isDoctorAvailable($doctor->id, $preferredDate, $preferredTime);
            });
        }

        return $doctors;
    }

    /**
     * Check if doctor is available
     */
    private function isDoctorAvailable($doctorId, $date, $time)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$workingHours) {
            return false;
        }

        $appointment = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('appointment_time', $time)
            ->where('STATUS', '!=', 'cancelled')
            ->first();

        return !$appointment;
    }

    /**
     * Get appointment suggestions
     */
    private function getAppointmentSuggestions($doctors, $urgency)
    {
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
     * Get next available slot
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
     * Analyze symptoms
     */
    private function analyzeSymptoms($symptoms, $duration, $severity)
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
     * Get relevant specialists
     */
    private function getRelevantSpecialists($symptoms)
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
     * Assess urgency
     */
    private function assessUrgency($symptoms, $severity)
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
     * Get recommendations based on urgency
     */
    private function getRecommendations($urgency)
    {
        switch ($urgency) {
            case 'emergency':
                return [
                    'Seek immediate medical attention',
                    'Call emergency services if needed',
                    'Do not delay treatment'
                ];

            case 'high':
                return [
                    'Book an appointment as soon as possible',
                    'Consider urgent care if appointment not available',
                    'Monitor symptoms closely'
                ];

            case 'medium':
                return [
                    'Book an appointment within a few days',
                    'Monitor symptoms',
                    'Rest and take care of yourself'
                ];

            default:
                return [
                    'Book a regular appointment',
                    'Monitor symptoms',
                    'Practice self-care'
                ];
        }
    }

    /**
     * Get medication details
     */
    private function getMedicationDetails($medication)
    {
        // This would typically connect to a medication database
        // For now, return basic information
        return [
            'name' => $medication,
            'description' => 'General information about ' . $medication,
            'dosage' => 'Consult your doctor for proper dosage',
            'precautions' => 'Always follow your doctor\'s instructions'
        ];
    }

    /**
     * Check medication interactions
     */
    private function checkInteractions($medication)
    {
        // This would typically check against a drug interaction database
        return [
            'message' => 'Always inform your doctor about all medications you are taking',
            'interactions' => []
        ];
    }

    /**
     * Get medication side effects
     */
    private function getSideEffects($medication)
    {
        // This would typically come from a medication database
        return [
            'common' => ['Nausea', 'Dizziness', 'Headache'],
            'serious' => ['Severe allergic reactions', 'Difficulty breathing'],
            'note' => 'This is general information. Consult your doctor for specific side effects.'
        ];
    }

    /**
     * Get medication warnings
     */
    private function getWarnings($medication)
    {
        return [
            'Always consult your doctor before taking any medication',
            'Do not exceed recommended dosage',
            'Keep medications out of reach of children',
            'Store medications properly'
        ];
    }

    /**
     * Save conversation
     */
    private function saveConversation($userId, $userMessage, $aiResponse, $intent)
    {
        DB::table('ai_conversations')->insert([
            'user_id' => $userId,
            'user_message' => $userMessage,
            'ai_response' => $aiResponse,
            'intent' => $intent,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Get conversation history
     */
    private function getConversationHistory($userId)
    {
        return DB::table('ai_conversations')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Generate suggestions
     */
    private function generateSuggestions($user, $context)
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
     * Generate booking message
     */
    private function generateBookingMessage($doctors, $urgency)
    {
        if ($doctors->count() === 0) {
            return "I couldn't find available doctors for your request. Please try a different specialty or date.";
        }

        $message = "I found " . $doctors->count() . " available doctor(s) for you. ";

        if ($urgency === 'emergency') {
            $message .= "Given the urgency, I recommend booking immediately.";
        } else {
            $message .= "You can view their profiles and book an appointment.";
        }

        return $message;
    }

    /**
     * Build response from AI data
     */
    private function buildResponseFromAiData($data, $originalMessage)
    {
        $response = '';

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

        // Handle doctor suggestions
        if (isset($data['doctors']) && $data['doctors'] && isset($data['doctors']['doctors'])) {
            $doctorCount = count($data['doctors']['doctors']);
            if ($doctorCount > 0) {
                if (empty($response)) {
                    $response = "I found {$doctorCount} doctors for {$data['doctors']['specialty']}.";
                } else {
                    $response .= "\n\nI also found {$doctorCount} doctors for {$data['doctors']['specialty']}.";
                }
            }
        }

        // Fallback response if no specific data
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
                case 'medical_inquiry':
                    $response = "I understand you have a medical question. Please provide more details about your symptoms or concern.";
                    break;
                default:
                    $response = "I'm here to help with your medical needs. How can I assist you today?";
            }
        }

        return $response;
    }

    /**
     * Process voice input
     */
    public function processVoiceInput(Request $request)
    {
        try {
            if (!$request->hasFile('audio')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No audio file provided'
                ], 400);
            }

            $audioFile = $request->file('audio');

            // For now, we'll use a simple approach
            // In a real implementation, you'd send this to a speech-to-text service
            $transcript = $this->processAudioFile($audioFile);

            return response()->json([
                'success' => true,
                'transcript' => $transcript
            ]);

        } catch (\Exception $e) {
            \Log::error('Voice Processing Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing voice input: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process audio file (placeholder for speech-to-text)
     */
    private function processAudioFile($audioFile)
    {
        // This is a placeholder implementation
        // In a real system, you would:
        // 1. Send the audio file to a speech-to-text service (Google Speech-to-Text, Azure Speech, etc.)
        // 2. Get the transcript back
        // 3. Return the transcript

        // For demo purposes, return a placeholder
        return "Voice input processed. Please type your question for better accuracy.";
    }
}
