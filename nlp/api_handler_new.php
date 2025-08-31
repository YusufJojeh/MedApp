<?php
/**
 * Medical Voice Assistant - PHP Bridge
 * 
 * This file handles communication between the PHP frontend and the Flask NLP backend.
 * Provides unified endpoints for intent prediction, medical Q&A, and doctor suggestions.
 */

// Set headers for JSON responses
if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

// Determine the correct path for includes
$base_path = dirname(__DIR__);
require_once $base_path . '/config/database.php';
require_once $base_path . '/includes/functions.php';

class MedicalVoiceAssistant {
    private $flask_url = 'http://127.0.0.1:5005';
    private $conn;
    private $current_user;
    private $timeout = 10; // seconds

    public function __construct($conn) {
        $this->conn = $conn;
        
        // Get current user if logged in
        if (isset($_SESSION['user_id'])) {
            $this->current_user = getCurrentUser($conn);
        }
    }

    /**
     * Make HTTP request to Flask backend
     */
    private function callFlaskAPI($endpoint, $data = null) {
        $url = $this->flask_url . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        
        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data))
            ]);
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return [
                'success' => false,
                'error' => 'Flask service connection error: ' . $error
            ];
        }
        
        if ($http_code !== 200) {
            return [
                'success' => false,
                'error' => 'Flask service returned HTTP ' . $http_code
            ];
        }
        
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'error' => 'Invalid JSON response from Flask service'
            ];
        }
        
        return [
            'success' => true,
            'data' => $result
        ];
    }

    /**
     * Check Flask service health
     */
    public function checkHealth() {
        return $this->callFlaskAPI('/health');
    }

    /**
     * Predict intent from text
     */
    public function predictIntent($text) {
        if (empty($text)) {
            return [
                'success' => false,
                'error' => 'Text parameter is required'
            ];
        }
        
        return $this->callFlaskAPI('/predict_intent', ['text' => $text]);
    }

    /**
     * Answer medical question
     */
    public function answerQA($question) {
        if (empty($question)) {
            return [
                'success' => false,
                'error' => 'Question parameter is required'
            ];
        }
        
        return $this->callFlaskAPI('/answer_qa', ['question' => $question]);
    }

    /**
     * Suggest doctors by specialty
     */
    public function suggestDoctors($specialty) {
        if (empty($specialty)) {
            return [
                'success' => false,
                'error' => 'Specialty parameter is required'
            ];
        }
        
        return $this->callFlaskAPI('/suggest_doctor', ['specialty' => $specialty]);
    }

    /**
     * Process text comprehensively (intent + Q&A + doctors)
     */
    public function processText($text) {
        if (empty($text)) {
            return [
                'success' => false,
                'error' => 'Text parameter is required'
            ];
        }
        
        return $this->callFlaskAPI('/process', ['text' => $text]);
    }

    /**
     * Handle voice input with STT transcript
     */
    public function processVoiceInput($transcript) {
        if (empty($transcript)) {
            return [
                'success' => false,
                'error' => 'Voice transcript is required'
            ];
        }
        
        // Process the transcript
        $result = $this->processText($transcript);
        
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
     * Format response for voice output
     */
    private function formatVoiceResponse($data) {
        $response_parts = [];
        
        // Add intent-based response
        if (isset($data['intent']) && $data['intent']['intent'] !== 'unknown') {
            $intent = $data['intent']['intent'];
            $confidence = $data['intent']['confidence'];
            
            switch ($intent) {
                case 'book_appointment':
                    $response_parts[] = 'I can help you book an appointment.';
                    break;
                case 'search_doctors':
                    $response_parts[] = 'I\'ll help you find doctors.';
                    break;
                case 'health_tips':
                    $response_parts[] = 'Here are some health tips for you.';
                    break;
                case 'medical_inquiry':
                    $response_parts[] = 'I\'ll answer your medical question.';
                    break;
                default:
                    $response_parts[] = 'I understand you need medical assistance.';
            }
        }
        
        // Add Q&A response
        if (isset($data['qa']) && $data['qa'] && isset($data['qa']['answer'])) {
            $response_parts[] = $data['qa']['answer'];
        }
        
        // Add doctor suggestions
        if (isset($data['doctors']) && $data['doctors'] && isset($data['doctors']['doctors'])) {
            $doctor_count = count($data['doctors']['doctors']);
            if ($doctor_count > 0) {
                $response_parts[] = "I found {$doctor_count} doctors for you.";
            }
        }
        
        // Fallback if no specific response
        if (empty($response_parts)) {
            $response_parts[] = 'I\'m here to help with your medical needs. How can I assist you?';
        }
        
        return implode(' ', $response_parts);
    }
}

// Handle API requests
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $assistant = new MedicalVoiceAssistant($conn);
        $action = $_GET['action'] ?? 'process';
        $response = [];
        
        switch ($action) {
            case 'health':
                $response = $assistant->checkHealth();
                break;
                
            case 'predict':
                $text = $input['text'] ?? '';
                $response = $assistant->predictIntent($text);
                break;
                
            case 'qa':
                $question = $input['question'] ?? '';
                $response = $assistant->answerQA($question);
                break;
                
            case 'suggest':
                $specialty = $input['specialty'] ?? '';
                $response = $assistant->suggestDoctors($specialty);
                break;
                
            case 'voice':
                $transcript = $input['transcript'] ?? '';
                $response = $assistant->processVoiceInput($transcript);
                break;
                
            case 'chat_message':
                $text = $input['message'] ?? $input['text'] ?? '';
                $response = $assistant->processText($text);
                break;
                
            default:
                $text = $input['text'] ?? $input['message'] ?? '';
                $response = $assistant->processText($text);
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
} elseif (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET requests (health check, etc.)
    try {
        $assistant = new MedicalVoiceAssistant($conn);
        $action = $_GET['action'] ?? 'health';
        
        switch ($action) {
            case 'health':
                $response = $assistant->checkHealth();
                break;
                
            default:
                $response = [
                    'success' => false,
                    'error' => 'Invalid action'
                ];
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}
?>
