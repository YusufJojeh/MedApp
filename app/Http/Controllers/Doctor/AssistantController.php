<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AssistantController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    /**
     * Display the AI assistant dashboard
     */
    public function index()
    {
        $userId = Auth::id();
        $doctor = DB::table('doctors')->where('user_id', $userId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        // Get recent conversations
        $recentConversations = DB::table('ai_conversations')
            ->where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Get assistant statistics
        $stats = $this->getAssistantStats($userId);

        // Get today's appointments for quick access
        $todayAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->whereDate('appointments.appointment_date', today())
            ->where('appointments.status', 'scheduled')
            ->select(
                'appointments.*',
                'patients.name as patient_name',
                'patients.phone as patient_phone'
            )
            ->orderBy('appointments.appointment_time')
            ->get();

        return view('doctor.assistant.index', compact('recentConversations', 'stats', 'todayAppointments'));
    }

    /**
     * Start a new conversation
     */
    public function startConversation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic' => 'required|string|max:255',
            'initial_message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();

        DB::beginTransaction();
        try {
            // Create new conversation
            $conversationId = DB::table('ai_conversations')->insertGetId([
                'user_id' => $userId,
                'topic' => $request->topic,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add initial message
            DB::table('ai_messages')->insert([
                'conversation_id' => $conversationId,
                'sender_type' => 'user',
                'message' => $request->initial_message,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Generate AI response
            $aiResponse = $this->generateAIResponse($request->initial_message, $userId);

            // Add AI response
            DB::table('ai_messages')->insert([
                'conversation_id' => $conversationId,
                'sender_type' => 'assistant',
                'message' => $aiResponse,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'conversation_id' => $conversationId,
                'message' => 'Conversation started successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error starting conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a message in an existing conversation
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|exists:ai_conversations,id',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();
        $conversationId = $request->conversation_id;

        // Verify conversation belongs to user
        $conversation = DB::table('ai_conversations')
            ->where('id', $conversationId)
            ->where('user_id', $userId)
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Add user message
            DB::table('ai_messages')->insert([
                'conversation_id' => $conversationId,
                'sender_type' => 'user',
                'message' => $request->message,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Generate AI response
            $aiResponse = $this->generateAIResponse($request->message, $userId, $conversationId);

            // Add AI response
            DB::table('ai_messages')->insert([
                'conversation_id' => $conversationId,
                'sender_type' => 'assistant',
                'message' => $aiResponse,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update conversation timestamp
            DB::table('ai_conversations')->where('id', $conversationId)->update([
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error sending message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conversation messages
     */
    public function getConversation($id)
    {
        $userId = Auth::id();

        $conversation = DB::table('ai_conversations')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        $messages = DB::table('ai_messages')
            ->where('conversation_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }

    /**
     * Get all conversations
     */
    public function getConversations(Request $request)
    {
        $userId = Auth::id();

        $query = DB::table('ai_conversations')
            ->where('user_id', $userId);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('topic', 'like', '%' . $request->search . '%');
        }

        $conversations = $query->orderBy('updated_at', 'desc')
            ->paginate(15);

        return response()->json($conversations);
    }

    /**
     * Close a conversation
     */
    public function closeConversation($id)
    {
        $userId = Auth::id();

        $conversation = DB::table('ai_conversations')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        DB::table('ai_conversations')->where('id', $id)->update([
            'status' => 'closed',
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation closed successfully'
        ]);
    }

    /**
     * Get quick actions for the assistant
     */
    public function getQuickActions()
    {
        $userId = Auth::id();
        $doctor = DB::table('doctors')->where('user_id', $userId)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $actions = [
            [
                'id' => 'schedule_appointment',
                'title' => 'Schedule Appointment',
                'description' => 'Help me schedule a new appointment',
                'icon' => 'fas fa-calendar-plus',
                'color' => 'blue'
            ],
            [
                'id' => 'patient_info',
                'title' => 'Patient Information',
                'description' => 'Get information about a patient',
                'icon' => 'fas fa-user-injured',
                'color' => 'green'
            ],
            [
                'id' => 'medical_advice',
                'title' => 'Medical Advice',
                'description' => 'Get medical advice and guidelines',
                'icon' => 'fas fa-stethoscope',
                'color' => 'purple'
            ],
            [
                'id' => 'prescription_help',
                'title' => 'Prescription Help',
                'description' => 'Help with prescription writing',
                'icon' => 'fas fa-pills',
                'color' => 'orange'
            ],
            [
                'id' => 'schedule_management',
                'title' => 'Schedule Management',
                'description' => 'Manage my working hours and availability',
                'icon' => 'fas fa-clock',
                'color' => 'indigo'
            ],
            [
                'id' => 'billing_help',
                'title' => 'Billing Help',
                'description' => 'Help with billing and payments',
                'icon' => 'fas fa-credit-card',
                'color' => 'teal'
            ]
        ];

        return response()->json($actions);
    }

    /**
     * Execute a quick action
     */
    public function executeQuickAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|string',
            'parameters' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();
        $action = $request->action;
        $parameters = $request->parameters ?? [];

        $response = $this->handleQuickAction($action, $parameters, $userId);

        return response()->json($response);
    }

    /**
     * Get assistant statistics
     */
    public function getStats()
    {
        $userId = Auth::id();
        $stats = $this->getAssistantStats($userId);

        return response()->json($stats);
    }

    /**
     * Check NLP service health
     */
    public function checkNLPHealth()
    {
        try {
            $nlp_path = base_path('nlp/api_handler.php');
            if (!file_exists($nlp_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'NLP API handler not found',
                    'status' => 'unavailable'
                ]);
            }

            $conn = $this->getDatabaseConnection();
            if (!$conn) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database connection failed',
                    'status' => 'unavailable'
                ]);
            }

            require_once $nlp_path;
            $assistant = new \MedicalVoiceAssistant($conn);

            $health = $assistant->checkHealth();

            if ($health['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'NLP service is running',
                    'status' => 'available',
                    'data' => $health['data'] ?? null
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'NLP service is not responding',
                    'status' => 'unavailable',
                    'error' => $health['error'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking NLP service health',
                'status' => 'error',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get enhanced medical knowledge from NLP
     */
    public function getMedicalKnowledge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $nlp_path = base_path('nlp/api_handler.php');
            if (!file_exists($nlp_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'NLP service not available'
                ]);
            }

            $conn = $this->getDatabaseConnection();
            if (!$conn) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database connection failed'
                ]);
            }

            require_once $nlp_path;
            $assistant = new \MedicalVoiceAssistant($conn);

            $question = "Provide medical information about: " . $request->topic;
            $result = $assistant->answerQA($question);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to retrieve medical information',
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving medical knowledge',
                'error' => $e->getMessage()
            ]);
        }
    }

        /**
     * Generate AI response using NLP models
     */
    private function generateAIResponse($message, $userId, $conversationId = null)
    {
        try {
            // Include the NLP API handler
            $nlp_path = base_path('nlp/api_handler.php');
            if (!file_exists($nlp_path)) {
                return $this->getFallbackResponse($message);
            }

            // Initialize database connection for NLP handler
            $conn = $this->getDatabaseConnection();
            if (!$conn) {
                return $this->getFallbackResponse($message);
            }

            // Create NLP assistant instance
            require_once $nlp_path;
            $assistant = new \MedicalVoiceAssistant($conn);

            // Set current user context
            if (isset($_SESSION)) {
                $_SESSION['user_id'] = $userId;
            }

            // Process message with role-based context for doctor
            $result = $assistant->handleChatMessage($message, 'doctor');

            if ($result['success']) {
                $response = $result['response'];

                // Add any additional context based on conversation history
                if ($conversationId) {
                    $response = $this->addConversationContext($response, $conversationId);
                }

                return $response;
            } else {
                // Fallback to basic response if NLP fails
                return $this->getFallbackResponse($message);
            }

        } catch (\Exception $e) {
            \Log::error('NLP API Error: ' . $e->getMessage());
            return $this->getFallbackResponse($message);
        }
    }

    /**
     * Get database connection for NLP handler
     */
    private function getDatabaseConnection()
    {
        try {
            $host = config('database.connections.mysql.host');
            $dbname = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            $conn = new \PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            return $conn;
        } catch (\Exception $e) {
            \Log::error('Database connection error for NLP: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Add conversation context to response
     */
    private function addConversationContext($response, $conversationId)
    {
        try {
            // Get recent messages from this conversation
            $recentMessages = DB::table('ai_messages')
                ->where('conversation_id', $conversationId)
                ->where('sender_type', 'user')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->pluck('message')
                ->toArray();

            if (empty($recentMessages)) {
                return $response;
            }

            // Add context if this is a follow-up question
            $contextKeywords = ['it', 'this', 'that', 'the patient', 'the appointment', 'the medication'];
            $hasContext = false;

            foreach ($contextKeywords as $keyword) {
                if (stripos($response, $keyword) !== false) {
                    $hasContext = true;
                    break;
                }
            }

            if ($hasContext && !empty($recentMessages)) {
                $lastMessage = $recentMessages[0];
                $response = "Based on your previous message about \"$lastMessage\", $response";
            }

            return $response;

        } catch (\Exception $e) {
            \Log::error('Error adding conversation context: ' . $e->getMessage());
            return $response;
        }
    }

    /**
     * Get fallback response when NLP is unavailable
     */
    private function getFallbackResponse($message)
    {
        $messageLower = strtolower($message);

        $responses = [
            'schedule' => 'I can help you schedule appointments. What date and time would you prefer?',
            'patient' => 'I can help you find patient information. What patient are you looking for?',
            'medical' => 'I can provide medical guidelines and advice. What specific topic do you need help with?',
            'prescription' => 'I can help you with prescription writing. What medication are you considering?',
            'billing' => 'I can help you with billing questions. What specific billing issue do you have?',
            'default' => 'I\'m here to help you with your medical practice. How can I assist you today?'
        ];

        if (str_contains($messageLower, 'schedule') || str_contains($messageLower, 'appointment')) {
            return $responses['schedule'];
        } elseif (str_contains($messageLower, 'patient')) {
            return $responses['patient'];
        } elseif (str_contains($messageLower, 'medical') || str_contains($messageLower, 'advice')) {
            return $responses['medical'];
        } elseif (str_contains($messageLower, 'prescription') || str_contains($messageLower, 'medication')) {
            return $responses['prescription'];
        } elseif (str_contains($messageLower, 'billing') || str_contains($messageLower, 'payment')) {
            return $responses['billing'];
        }

        return $responses['default'];
    }

    /**
     * Handle quick actions with NLP integration
     */
    private function handleQuickAction($action, $parameters, $userId)
    {
        try {
            // Try to use NLP for enhanced responses
            $nlp_response = $this->getNLPResponseForAction($action, $parameters, $userId);
            if ($nlp_response) {
                return $nlp_response;
            }
        } catch (\Exception $e) {
            \Log::error('NLP action handling error: ' . $e->getMessage());
        }

        // Fallback to basic responses
        switch ($action) {
            case 'schedule_appointment':
                return [
                    'success' => true,
                    'message' => 'I can help you schedule an appointment. Please provide the patient name and preferred date/time.',
                    'action' => 'schedule_appointment',
                    'data' => $this->getAvailableSlots($userId)
                ];

            case 'patient_info':
                return [
                    'success' => true,
                    'message' => 'I can help you find patient information. Please provide the patient name or ID.',
                    'action' => 'patient_info',
                    'data' => $this->getRecentPatients($userId)
                ];

            case 'medical_advice':
                return [
                    'success' => true,
                    'message' => 'I can provide medical guidelines and advice. What specific medical topic do you need help with?',
                    'action' => 'medical_advice',
                    'data' => $this->getMedicalTopics()
                ];

            case 'prescription_help':
                return [
                    'success' => true,
                    'message' => 'I can help you with prescription writing. What medication and dosage are you considering?',
                    'action' => 'prescription_help',
                    'data' => $this->getPrescriptionGuidelines()
                ];

            case 'schedule_management':
                return [
                    'success' => true,
                    'message' => 'I can help you manage your schedule. Would you like to update your working hours or check availability?',
                    'action' => 'schedule_management',
                    'data' => $this->getScheduleData($userId)
                ];

            case 'billing_help':
                return [
                    'success' => true,
                    'message' => 'I can help you with billing questions. What specific billing issue do you need assistance with?',
                    'action' => 'billing_help',
                    'data' => $this->getBillingInfo($userId)
                ];

            default:
                return [
                    'success' => false,
                    'message' => 'Unknown action',
                    'action' => null,
                    'data' => null
                ];
        }
    }

    /**
     * Get NLP response for specific actions
     */
    private function getNLPResponseForAction($action, $parameters, $userId)
    {
        try {
            $nlp_path = base_path('nlp/api_handler.php');
            if (!file_exists($nlp_path)) {
                return null;
            }

            $conn = $this->getDatabaseConnection();
            if (!$conn) {
                return null;
            }

            require_once $nlp_path;
            $assistant = new \MedicalVoiceAssistant($conn);

            // Create context-specific messages for each action
            $actionMessages = [
                'schedule_appointment' => 'Help me schedule an appointment with a patient',
                'patient_info' => 'I need to find information about a patient',
                'medical_advice' => 'I need medical advice and guidelines',
                'prescription_help' => 'I need help with prescription writing',
                'schedule_management' => 'I need to manage my schedule and working hours',
                'billing_help' => 'I need help with billing and payment questions'
            ];

            $message = $actionMessages[$action] ?? 'Help me with ' . $action;
            $result = $assistant->handleChatMessage($message, 'doctor');

            if ($result['success']) {
                return [
                    'success' => true,
                    'message' => $result['response'],
                    'action' => $action,
                    'data' => $result['data'] ?? null
                ];
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('NLP action response error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get available appointment slots
     */
    private function getAvailableSlots($userId)
    {
        try {
            $doctor = DB::table('doctors')->where('user_id', $userId)->first();
            if (!$doctor) {
                return null;
            }

            $today = now();
            $slots = [];

            for ($i = 0; $i < 7; $i++) {
                $date = $today->copy()->addDays($i);
                $dayOfWeek = $date->dayOfWeek - 1; // Convert to 0-6 format

                $workingHour = DB::table('working_hours')
                    ->where('doctor_id', $doctor->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('is_available', true)
                    ->first();

                if ($workingHour) {
                    $slots[] = [
                        'date' => $date->format('Y-m-d'),
                        'day' => $date->format('l'),
                        'start_time' => $workingHour->start_time,
                        'end_time' => $workingHour->end_time
                    ];
                }
            }

            return $slots;
        } catch (\Exception $e) {
            \Log::error('Error getting available slots: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get recent patients
     */
    private function getRecentPatients($userId)
    {
        try {
            $doctor = DB::table('doctors')->where('user_id', $userId)->first();
            if (!$doctor) {
                return null;
            }

            return DB::table('appointments')
                ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                ->where('appointments.doctor_id', $doctor->id)
                ->select('patients.id', 'patients.name', 'patients.phone')
                ->distinct()
                ->orderBy('appointments.created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Error getting recent patients: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get medical topics
     */
    private function getMedicalTopics()
    {
        return [
            'Cardiology' => 'Heart health, blood pressure, cholesterol',
            'Dermatology' => 'Skin conditions, rashes, moles',
            'Neurology' => 'Brain health, headaches, memory',
            'Orthopedics' => 'Bones, joints, sports injuries',
            'Pediatrics' => 'Child health, vaccinations, growth',
            'Psychiatry' => 'Mental health, anxiety, depression',
            'Endocrinology' => 'Diabetes, thyroid, hormones',
            'Gastroenterology' => 'Digestive health, stomach issues'
        ];
    }

    /**
     * Get prescription guidelines
     */
    private function getPrescriptionGuidelines()
    {
        return [
            'antibiotics' => 'Consider bacterial vs viral infections, resistance patterns',
            'pain_medication' => 'Assess pain level, consider non-opioid alternatives',
            'chronic_conditions' => 'Review current medications, check interactions',
            'pediatric_dosing' => 'Calculate based on weight, age-appropriate formulations',
            'elderly_patients' => 'Consider renal function, drug interactions, side effects'
        ];
    }

    /**
     * Get schedule data
     */
    private function getScheduleData($userId)
    {
        try {
            $doctor = DB::table('doctors')->where('user_id', $userId)->first();
            if (!$doctor) {
                return null;
            }

            $workingHours = DB::table('working_hours')
                ->where('doctor_id', $doctor->id)
                ->get();

            $todayAppointments = DB::table('appointments')
                ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                ->where('appointments.doctor_id', $doctor->id)
                ->whereDate('appointments.appointment_date', today())
                ->select('appointments.*', 'patients.name as patient_name')
                ->orderBy('appointments.appointment_time')
                ->get();

            return [
                'working_hours' => $workingHours,
                'today_appointments' => $todayAppointments
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting schedule data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get billing information
     */
    private function getBillingInfo($userId)
    {
        try {
            $doctor = DB::table('doctors')->where('user_id', $userId)->first();
            if (!$doctor) {
                return null;
            }

            $wallet = DB::table('wallets')->where('user_id', $userId)->first();
            $recentPayments = DB::table('payments')
                ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
                ->where('appointments.doctor_id', $doctor->id)
                ->where('payments.status', 'succeeded')
                ->orderBy('payments.created_at', 'desc')
                ->limit(5)
                ->get();

            return [
                'wallet_balance' => $wallet ? $wallet->balance : 0,
                'recent_payments' => $recentPayments,
                'consultation_fee' => $doctor->consultation_fee
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting billing info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get assistant statistics
     */
    private function getAssistantStats($userId)
    {
        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        return [
            'total_conversations' => DB::table('ai_conversations')
                ->where('user_id', $userId)
                ->count(),
            'active_conversations' => DB::table('ai_conversations')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->count(),
            'this_month_conversations' => DB::table('ai_conversations')
                ->where('user_id', $userId)
                ->whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count(),
            'last_month_conversations' => DB::table('ai_conversations')
                ->where('user_id', $userId)
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count(),
            'total_messages' => DB::table('ai_messages')
                ->join('ai_conversations', 'ai_messages.conversation_id', '=', 'ai_conversations.id')
                ->where('ai_conversations.user_id', $userId)
                ->count(),
        ];
    }
}
