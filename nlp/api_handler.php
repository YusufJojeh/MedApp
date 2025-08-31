<?php
/**
 * Medical Voice Assistant - PHP Bridge
 * 
 * This file handles communication between the PHP frontend and the Flask NLP backend.
 * Provides unified endpoints for intent prediction, medical Q&A, and doctor suggestions.
 * Supports role-based assistance for patients and doctors.
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
                'health_tips' => $data['health_tips'] ?? null,
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
     * Handle chat messages with role-based responses
     */
    public function handleChatMessage($message, $user_role = 'guest') {
        if (empty($message)) {
            return [
                'success' => false,
                'error' => 'Message is required'
            ];
        }

        // Check for appointment booking requests
        $booking_result = $this->handleAppointmentBooking($message, $user_role);
        if ($booking_result['success']) {
            return $booking_result;
        }

        // First, try to get comprehensive response from Flask
        $flask_result = $this->processText($message);
        
        if ($flask_result['success']) {
            $data = $flask_result['data'];
            $response = $this->formatRoleBasedResponse($data, $user_role, $message);
            return [
                'success' => true,
                'response' => $response['text'],
                'data' => $response['data'],
                'action' => $response['action'] ?? null
            ];
        }

        // Fallback to role-based responses
        return $this->generateRoleBasedFallback($message, $user_role);
    }

    /**
     * Handle appointment booking requests
     */
    private function handleAppointmentBooking($message, $user_role) {
        $lower_message = strtolower($message);
        
        // Check if this is an appointment booking request
        $booking_keywords = ['book appointment', 'schedule appointment', 'make appointment', 'book with', 'schedule with'];
        $is_booking_request = false;
        
        foreach ($booking_keywords as $keyword) {
            if (strpos($lower_message, $keyword) !== false) {
                $is_booking_request = true;
                break;
            }
        }
        
        if (!$is_booking_request) {
            return ['success' => false];
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            return [
                'success' => true,
                'response' => "To book an appointment, please log in first. You'll be redirected to the login page.",
                'data' => [
                    'action' => 'redirect_login',
                    'url' => 'login.php'
                ]
            ];
        }
        
        // Extract doctor information
        $doctor_info = $this->extractDoctorFromMessage($message);
        if (!$doctor_info) {
            return [
                'success' => true,
                'response' => "I couldn't identify the doctor from your message. Please specify the doctor's name clearly.",
                'data' => [
                    'action' => 'show_doctors',
                    'url' => 'doctors.php'
                ]
            ];
        }
        
        // Extract date and time
        $datetime_info = $this->extractDateTimeFromMessage($message);
        
        // Try to book the appointment
        $booking_result = $this->bookAppointment($doctor_info, $datetime_info, $_SESSION['user_id']);
        
        if ($booking_result['success']) {
            return [
                'success' => true,
                'response' => $booking_result['message'],
                'data' => [
                    'action' => 'appointment_booked',
                    'appointment_id' => $booking_result['appointment_id'],
                    'doctor_name' => $doctor_info['name'],
                    'appointment_date' => $booking_result['appointment_date'],
                    'appointment_time' => $booking_result['appointment_time']
                ]
            ];
        } else {
            return [
                'success' => true,
                'response' => $booking_result['message'],
                'data' => [
                    'action' => 'booking_failed',
                    'error' => $booking_result['error']
                ]
            ];
        }
    }

    /**
     * Extract doctor information from message
     */
    private function extractDoctorFromMessage($message) {
        // Common doctor name patterns
        $doctor_patterns = [
            '/dr\.?\s+([a-zA-Z]+\s+[a-zA-Z]+)/i',
            '/doctor\s+([a-zA-Z]+\s+[a-zA-Z]+)/i',
            '/with\s+([a-zA-Z]+\s+[a-zA-Z]+)/i'
        ];
        
        foreach ($doctor_patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $doctor_name = trim($matches[1]);
                
                // Search for doctor in database
                $doctor = $this->findDoctorByName($doctor_name);
                if ($doctor) {
                    return $doctor;
                }
            }
        }
        
        return null;
    }

    /**
     * Find doctor by name in database
     */
    private function findDoctorByName($name) {
        try {
            $stmt = $this->conn->prepare("
                SELECT d.*, s.name as specialty_name 
                FROM doctors d 
                JOIN specialties s ON d.specialty_id = s.id 
                WHERE d.name LIKE ? AND d.is_active = 1
            ");
            $stmt->execute(["%$name%"]);
            $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($doctor) {
                return [
                    'id' => $doctor['id'],
                    'name' => $doctor['name'],
                    'specialty' => $doctor['specialty_name'],
                    'experience_years' => $doctor['experience_years'],
                    'consultation_fee' => $doctor['consultation_fee']
                ];
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error finding doctor: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract date and time from message
     */
    private function extractDateTimeFromMessage($message) {
        $datetime_info = [
            'date' => null,
            'time' => null,
            'date_string' => null,
            'time_string' => null
        ];
        
        // Extract time patterns
        $time_patterns = [
            '/(\d{1,2}):(\d{2})\s*(am|pm)/i',
            '/(\d{1,2})\s*(am|pm)/i',
            '/(\d{1,2}):(\d{2})/',
            '/(\d{1,2})\s*pm/i',
            '/(\d{1,2})\s*am/i'
        ];
        
        foreach ($time_patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $hour = intval($matches[1]);
                $minute = isset($matches[2]) ? intval($matches[2]) : 0;
                $ampm = isset($matches[3]) ? strtolower($matches[3]) : '';
                
                // Convert to 24-hour format
                if ($ampm === 'pm' && $hour < 12) {
                    $hour += 12;
                } elseif ($ampm === 'am' && $hour == 12) {
                    $hour = 0;
                }
                
                $datetime_info['time'] = sprintf('%02d:%02d:00', $hour, $minute);
                $datetime_info['time_string'] = sprintf('%02d:%02d %s', 
                    $ampm === 'pm' && $hour > 12 ? $hour - 12 : ($hour === 0 ? 12 : $hour), 
                    $minute, 
                    $ampm ? strtoupper($ampm) : ''
                );
                break;
            }
        }
        
        // Extract date patterns
        $date_patterns = [
            '/(\d{1,2})\/(\d{1,2})\/(\d{4})/',
            '/(\d{1,2})-(\d{1,2})-(\d{4})/',
            '/(\d{1,2})\/(\d{1,2})\/(\d{2})/',
            '/(\d{1,2})-(\d{1,2})-(\d{2})/',
            '/tomorrow/i',
            '/today/i',
            '/next week/i'
        ];
        
        foreach ($date_patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                if (strtolower($matches[0]) === 'tomorrow') {
                    $date = date('Y-m-d', strtotime('+1 day'));
                    $datetime_info['date'] = $date;
                    $datetime_info['date_string'] = 'tomorrow';
                } elseif (strtolower($matches[0]) === 'today') {
                    $date = date('Y-m-d');
                    $datetime_info['date'] = $date;
                    $datetime_info['date_string'] = 'today';
                } elseif (strtolower($matches[0]) === 'next week') {
                    $date = date('Y-m-d', strtotime('+1 week'));
                    $datetime_info['date'] = $date;
                    $datetime_info['date_string'] = 'next week';
                } else {
                    $day = intval($matches[1]);
                    $month = intval($matches[2]);
                    $year = intval($matches[3]);
                    
                    // Handle 2-digit years
                    if ($year < 100) {
                        $year += 2000;
                    }
                    
                    $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $datetime_info['date'] = $date;
                    $datetime_info['date_string'] = sprintf('%d/%d/%d', $day, $month, $year);
                }
                break;
            }
        }
        
        return $datetime_info;
    }

    /**
     * Book appointment in database
     */
    private function bookAppointment($doctor_info, $datetime_info, $user_id) {
        try {
            // Validate doctor availability
            if (!$this->checkDoctorAvailability($doctor_info['id'], $datetime_info['date'], $datetime_info['time'])) {
                return [
                    'success' => false,
                    'message' => "Dr. {$doctor_info['name']} is not available at the requested time. Please choose a different time or date.",
                    'error' => 'doctor_unavailable'
                ];
            }
            
            // Check if time slot is already booked
            if ($this->isTimeSlotBooked($doctor_info['id'], $datetime_info['date'], $datetime_info['time'])) {
                return [
                    'success' => false,
                    'message' => "This time slot is already booked. Please choose a different time.",
                    'error' => 'slot_booked'
                ];
            }
            
            // Insert appointment
            $stmt = $this->conn->prepare("
                INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status, created_at)
                VALUES (?, ?, ?, ?, 'scheduled', NOW())
            ");
            
            $stmt->execute([
                $user_id,
                $doctor_info['id'],
                $datetime_info['date'],
                $datetime_info['time']
            ]);
            
            $appointment_id = $this->conn->lastInsertId();
            
            // Format response message
            $date_display = $datetime_info['date_string'] ?: date('F j, Y', strtotime($datetime_info['date']));
            $time_display = $datetime_info['time_string'] ?: date('g:i A', strtotime($datetime_info['time']));
            
            $message = "✅ Appointment booked successfully!\n\n";
            $message .= "**Appointment Details:**\n";
            $message .= "• Doctor: Dr. {$doctor_info['name']}\n";
            $message .= "• Specialty: {$doctor_info['specialty']}\n";
            $message .= "• Date: {$date_display}\n";
            $message .= "• Time: {$time_display}\n";
            $message .= "• Appointment ID: #{$appointment_id}\n\n";
            $message .= "You will receive a confirmation email shortly. Please arrive 10 minutes before your appointment time.";
            
            return [
                'success' => true,
                'message' => $message,
                'appointment_id' => $appointment_id,
                'appointment_date' => $datetime_info['date'],
                'appointment_time' => $datetime_info['time']
            ];
            
        } catch (Exception $e) {
            error_log("Error booking appointment: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Sorry, there was an error booking your appointment. Please try again or contact our support.",
                'error' => 'database_error'
            ];
        }
    }

    /**
     * Check if doctor is available on given date and time
     */
    private function checkDoctorAvailability($doctor_id, $date, $time) {
        try {
            // Get day of week (0 = Sunday, 1 = Monday, etc.)
            $day_of_week = date('w', strtotime($date));
            
            // Check working hours
            $stmt = $this->conn->prepare("
                SELECT * FROM working_hours 
                WHERE doctor_id = ? AND day_of_week = ? AND is_available = 1
            ");
            $stmt->execute([$doctor_id, $day_of_week]);
            $working_hours = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$working_hours) {
                return false; // Doctor doesn't work on this day
            }
            
            // Check if time is within working hours
            $appointment_time = strtotime($time);
            $start_time = strtotime($working_hours['start_time']);
            $end_time = strtotime($working_hours['end_time']);
            
            if ($appointment_time < $start_time || $appointment_time >= $end_time) {
                return false; // Outside working hours
            }
            
            // Check break time
            if ($working_hours['break_start'] && $working_hours['break_end']) {
                $break_start = strtotime($working_hours['break_start']);
                $break_end = strtotime($working_hours['break_end']);
                
                if ($appointment_time >= $break_start && $appointment_time < $break_end) {
                    return false; // During break time
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error checking doctor availability: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if time slot is already booked
     */
    private function isTimeSlotBooked($doctor_id, $date, $time) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as count FROM appointments 
                WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? 
                AND status IN ('scheduled', 'confirmed')
            ");
            $stmt->execute([$doctor_id, $date, $time]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (Exception $e) {
            error_log("Error checking time slot: " . $e->getMessage());
            return true; // Assume booked if error
        }
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
        
        // Add health tips response
        if (isset($data['health_tips']) && $data['health_tips'] && isset($data['health_tips']['tips'])) {
            $tips = $data['health_tips']['tips'];
            $specialty = $data['health_tips']['specialty'] ?? 'general health';
            $response_parts[] = "Here are {$specialty} tips: " . implode('. ', array_slice($tips, 0, 3)) . ".";
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

    /**
     * Format role-based response
     */
    private function formatRoleBasedResponse($data, $user_role, $original_message) {
        $text = '';
        $action = null;
        $response_data = [];

        // Handle intent-based responses
        if (isset($data['intent']) && $data['intent']['intent'] !== 'unknown') {
            $intent = $data['intent']['intent'];
            $specialty_hint = $data['intent']['specialty_hint'] ?? null;

            switch ($intent) {
                case 'book_appointment':
                    if ($user_role === 'patient') {
                        $text = "I'll help you book an appointment! ";
                        if ($specialty_hint) {
                            $text .= "I see you're looking for a {$specialty_hint} specialist. ";
                        }
                        $text .= "Let me show you available doctors and time slots.";
                        $action = 'navigate';
                        $response_data['url'] = 'doctors.php';
                    } else {
                        $text = "To book appointments, please log in as a patient or contact our reception.";
                    }
                    break;

                case 'search_doctors':
                    $text = "I'll help you find the right doctor! ";
                    if ($specialty_hint) {
                        $text .= "Here are our {$specialty_hint} specialists:";
                        $action = 'search_doctors';
                        $response_data['specialty'] = $specialty_hint;
                    } else {
                        $text .= "What type of specialist are you looking for?";
                    }
                    break;

                case 'medical_inquiry':
                    if (isset($data['qa']) && $data['qa']['answer']) {
                        $text = $data['qa']['answer'];
                        $text .= "\n\n⚠️ **Medical Disclaimer**: This information is educational and not a substitute for professional medical advice. Please consult a healthcare provider for proper diagnosis and treatment.";
                    } else {
                        $text = "I understand you have a medical question. Let me search our medical knowledge base for you.";
                    }
                    break;

                case 'health_tips':
                    if (isset($data['health_tips']) && $data['health_tips'] && isset($data['health_tips']['tips'])) {
                        $tips = $data['health_tips']['tips'];
                        $specialty = $data['health_tips']['specialty'] ?? 'General Health';
                        $text = "Here are some {$specialty} tips for you:\n\n";
                        
                        foreach ($tips as $index => $tip) {
                            $text .= "• {$tip}\n";
                        }
                        
                        $text .= "\nWould you like specific tips for any other health concern?";
                    } else {
                        $text = "Here are some general health tips for you:\n\n";
                        $text .= "• Stay hydrated by drinking 8 glasses of water daily\n";
                        $text .= "• Get 7-9 hours of quality sleep each night\n";
                        $text .= "• Exercise regularly (30 minutes daily)\n";
                        $text .= "• Eat a balanced diet with fruits and vegetables\n";
                        $text .= "• Schedule regular check-ups with your doctor\n\n";
                        $text .= "Would you like specific tips for any particular health concern?";
                    }
                    break;

                default:
                    $text = "I understand you need medical assistance. How can I help you today?";
            }
        }

        // Add doctor suggestions if available
        if (isset($data['doctors']) && $data['doctors']['doctors'] && count($data['doctors']['doctors']) > 0) {
            if (!empty($text)) $text .= "\n\n";
            $text .= "**Recommended Doctors:**\n\n";
            
            foreach ($data['doctors']['doctors'] as $doctor) {
                $text .= "👨‍⚕️ **{$doctor['name']}**\n";
                $text .= "   • Specialty: {$doctor['specialty']}\n";
                $text .= "   • Experience: {$doctor['experience_years']} years\n";
                $text .= "   • Fee: \${$doctor['consultation_fee']}\n\n";
            }
            
            $text .= "Would you like to book an appointment with any of these doctors?";
            $action = 'show_doctors';
            $response_data['doctors'] = $data['doctors']['doctors'];
        }

        // Add Q&A response if available
        if (isset($data['qa']) && $data['qa']['answer'] && empty($text)) {
            $text = $data['qa']['answer'];
            $text .= "\n\n⚠️ **Medical Disclaimer**: This information is educational and not a substitute for professional medical advice.";
        }

        // Role-specific enhancements
        if ($user_role === 'doctor') {
            $text = $this->enhanceForDoctor($text, $original_message);
        } elseif ($user_role === 'patient') {
            $text = $this->enhanceForPatient($text, $original_message);
        }

        return [
            'text' => $text,
            'data' => $response_data,
            'action' => $action
        ];
    }

    /**
     * Enhance response for doctor role
     */
    private function enhanceForDoctor($text, $message) {
        $lower_message = strtolower($message);
        
        if (strpos($lower_message, 'appointment') !== false || strpos($lower_message, 'schedule') !== false) {
            $text .= "\n\n**Doctor Actions Available:**\n";
            $text .= "• View today's appointments\n";
            $text .= "• Check upcoming schedule\n";
            $text .= "• Manage patient records\n";
            $text .= "• Set working hours";
        }
        
        if (strpos($lower_message, 'patient') !== false || strpos($lower_message, 'symptom') !== false) {
            $text .= "\n\n**Patient Management:**\n";
            $text .= "• Review patient history\n";
            $text .= "• Check symptoms\n";
            $text .= "• Update medical notes\n";
            $text .= "• Schedule follow-ups";
        }
        
        return $text;
    }

    /**
     * Enhance response for patient role
     */
    private function enhanceForPatient($text, $message) {
        $lower_message = strtolower($message);
        
        if (strpos($lower_message, 'appointment') !== false || strpos($lower_message, 'book') !== false) {
            $text .= "\n\n**Patient Actions Available:**\n";
            $text .= "• Book new appointment\n";
            $text .= "• View upcoming appointments\n";
            $text .= "• Cancel/reschedule appointments\n";
            $text .= "• View medical history";
        }
        
        if (strpos($lower_message, 'symptom') !== false || strpos($lower_message, 'pain') !== false) {
            $text .= "\n\n**Health Guidance:**\n";
            $text .= "• Schedule consultation\n";
            $text .= "• Get medical advice\n";
            $text .= "• Find specialists\n";
            $text .= "• Emergency information";
        }
        
        return $text;
    }

    /**
     * Generate role-based fallback responses
     */
    private function generateRoleBasedFallback($message, $user_role) {
        $lower_message = strtolower($message);
        
        // Check for common patterns
        if (strpos($lower_message, 'hello') !== false || strpos($lower_message, 'hi') !== false) {
            $greeting = $user_role === 'doctor' ? 'Hello Doctor!' : 'Hello!';
            return [
                'success' => true,
                'response' => $greeting . " I'm your AI medical assistant. How can I help you today?",
                'data' => []
            ];
        }
        
        if (strpos($lower_message, 'help') !== false) {
            $help_text = $this->getRoleBasedHelp($user_role);
            return [
                'success' => true,
                'response' => $help_text,
                'data' => []
            ];
        }
        
        // Handle health tips requests
        if (strpos($lower_message, 'health tips') !== false || strpos($lower_message, 'health advice') !== false || 
            strpos($lower_message, 'wellness tips') !== false || strpos($lower_message, 'healthy lifestyle') !== false) {
            
            // Extract specialty from message
            $specialty = $this->extractSpecialtyFromMessage($lower_message);
            
            // Call Flask API for health tips
            $flask_result = $this->callFlaskAPI('/health_tips', ['specialty' => $specialty, 'count' => 5]);
            
            if ($flask_result['success'] && isset($flask_result['data']['tips'])) {
                $tips = $flask_result['data']['tips'];
                $specialty_name = $flask_result['data']['specialty'] ?? 'General Health';
                
                $text = "Here are some {$specialty_name} tips for you:\n\n";
                foreach ($tips as $index => $tip) {
                    $text .= "• {$tip}\n";
                }
                $text .= "\nWould you like specific tips for any other health concern?";
                
                return [
                    'success' => true,
                    'response' => $text,
                    'data' => [
                        'health_tips' => $flask_result['data'],
                        'action' => 'show_health_tips'
                    ]
                ];
            } else {
                // Fallback to general tips
                $text = "Here are some general health tips for you:\n\n";
                $text .= "• Stay hydrated by drinking 8 glasses of water daily\n";
                $text .= "• Get 7-9 hours of quality sleep each night\n";
                $text .= "• Exercise regularly (30 minutes daily)\n";
                $text .= "• Eat a balanced diet with fruits and vegetables\n";
                $text .= "• Schedule regular check-ups with your doctor\n\n";
                $text .= "Would you like specific tips for any particular health concern?";
                
                return [
                    'success' => true,
                    'response' => $text,
                    'data' => [
                        'action' => 'show_health_tips'
                    ]
                ];
            }
        }
        
        // Handle doctor search requests
        if (strpos($lower_message, 'doctor') !== false || strpos($lower_message, 'cardiolog') !== false || 
            strpos($lower_message, 'heart') !== false || strpos($lower_message, 'specialist') !== false) {
            
            // Extract specialty from message
            $specialty = $this->extractSpecialtyFromMessage($lower_message);
            
            // Get doctors from database
            $doctors = $this->searchDoctorsBySpecialty($specialty);
            
            if (!empty($doctors)) {
                $text = "I found some excellent doctors for you:\n\n";
                foreach ($doctors as $doctor) {
                    $text .= "**Dr. " . htmlspecialchars($doctor['name']) . "**\n";
                    $text .= "• Specialty: " . htmlspecialchars($doctor['specialty_name']) . "\n";
                    $text .= "• Experience: " . htmlspecialchars($doctor['experience_years']) . " years\n";
                    $text .= "• Consultation Fee: $" . htmlspecialchars($doctor['consultation_fee']) . "\n";
                    $text .= "• Languages: " . htmlspecialchars($doctor['languages']) . "\n\n";
                }
                $text .= "Would you like to book an appointment with any of these doctors?";
                
                return [
                    'success' => true,
                    'response' => $text,
                    'data' => [
                        'doctors' => $doctors,
                        'action' => 'show_doctors'
                    ]
                ];
            } else {
                return [
                    'success' => true,
                    'response' => "I couldn't find any doctors matching your request. Please try searching for a different specialty or contact our reception for assistance.",
                    'data' => []
                ];
            }
        }
        
        // Handle appointment booking requests
        if (strpos($lower_message, 'appointment') !== false || strpos($lower_message, 'book') !== false) {
            return [
                'success' => true,
                'response' => "I'll help you book an appointment! Please visit our doctors page to see available specialists and their schedules.",
                'data' => [
                    'action' => 'navigate',
                    'url' => 'doctors.php'
                ]
            ];
        }
        
        // Default response
        return [
            'success' => true,
            'response' => "I understand you're asking about: '$message'. Let me help you with that. Could you please provide more details about what you need?",
            'data' => []
        ];
    }
    
    /**
     * Extract specialty from message
     */
    private function extractSpecialtyFromMessage($message) {
        $specialty_mappings = [
            'cardiolog' => 'Cardiology',
            'heart' => 'Cardiology',
            'cardiac' => 'Cardiology',
            'dermatolog' => 'Dermatology',
            'skin' => 'Dermatology',
            'neurolog' => 'Neurology',
            'brain' => 'Neurology',
            'pediatr' => 'Pediatrics',
            'child' => 'Pediatrics',
            'orthoped' => 'Orthopedics',
            'bone' => 'Orthopedics',
            'psychiatr' => 'Psychiatry',
            'mental' => 'Psychiatry',
            'ophthalmolog' => 'Ophthalmology',
            'eye' => 'Ophthalmology',
            'dentist' => 'Dentistry',
            'dental' => 'Dentistry',
            'gynecolog' => 'Obstetrics & Gynecology',
            'obstetr' => 'Obstetrics & Gynecology',
            'internal' => 'Internal Medicine',
            'general' => 'Internal Medicine'
        ];
        
        foreach ($specialty_mappings as $keyword => $specialty) {
            if (strpos($message, $keyword) !== false) {
                return $specialty;
            }
        }
        
        return null;
    }
    
    /**
     * Search doctors by specialty
     */
    private function searchDoctorsBySpecialty($specialty = null) {
        try {
            if ($specialty) {
                $stmt = $this->conn->prepare("
                    SELECT d.*, s.name as specialty_name 
                    FROM doctors d 
                    JOIN specialties s ON d.specialty_id = s.id 
                    WHERE s.name LIKE ? AND d.is_active = 1 
                    ORDER BY d.experience_years DESC
                    LIMIT 5
                ");
                $stmt->execute(["%$specialty%"]);
            } else {
                $stmt = $this->conn->prepare("
                    SELECT d.*, s.name as specialty_name 
                    FROM doctors d 
                    JOIN specialties s ON d.specialty_id = s.id 
                    WHERE d.is_active = 1 
                    ORDER BY d.experience_years DESC
                    LIMIT 5
                ");
                $stmt->execute();
            }
            
            $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add fallback values
            foreach ($doctors as &$doctor) {
                $doctor['name'] = $doctor['name'] ?? 'Dr. Unknown';
                $doctor['specialty_name'] = $doctor['specialty_name'] ?? 'General Medicine';
                $doctor['experience_years'] = $doctor['experience_years'] ?? 0;
                $doctor['consultation_fee'] = $doctor['consultation_fee'] ?? 0;
                $doctor['languages'] = $doctor['languages'] ?? 'English';
            }
            
            return $doctors;
        } catch (Exception $e) {
            error_log("Error searching doctors: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get role-based help information
     */
    private function getRoleBasedHelp($user_role) {
        switch ($user_role) {
            case 'doctor':
                return "**Doctor Assistant Help:**\n\n" .
                       "I can help you with:\n" .
                       "• View your appointments and schedule\n" .
                       "• Check patient information\n" .
                       "• Medical consultations and advice\n" .
                       "• Manage your working hours\n" .
                       "• Access patient records\n\n" .
                       "Just ask me what you need!";
                       
            case 'patient':
                return "**Patient Assistant Help:**\n\n" .
                       "I can help you with:\n" .
                       "• Book appointments with specialists\n" .
                       "• Find doctors by specialty\n" .
                       "• Get medical information and advice\n" .
                       "• View your appointment history\n" .
                       "• Health tips and guidance\n\n" .
                       "What would you like to do?";
                       
            case 'admin':
                return "**Admin Assistant Help:**\n\n" .
                       "I can help you with:\n" .
                       "• System management and statistics\n" .
                       "• User management\n" .
                       "• Doctor and patient oversight\n" .
                       "• Generate reports\n" .
                       "• System configuration\n\n" .
                       "How can I assist you?";
                       
            default:
                return "**Medical Assistant Help:**\n\n" .
                       "I can help you with:\n" .
                       "• Find doctors and specialties\n" .
                       "• Book appointments\n" .
                       "• Get medical information\n" .
                       "• Health tips and guidance\n\n" .
                       "Please log in for personalized assistance!";
        }
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
                $user_role = $input['user_role'] ?? 'guest';
                $response = $assistant->handleChatMessage($text, $user_role);
                break;
                
            case 'process_text':
                $text = $input['text'] ?? $input['message'] ?? '';
                $response = $assistant->processText($text);
                break;
                
            default:
                $text = $input['text'] ?? $input['message'] ?? '';
                $user_role = $input['user_role'] ?? 'guest';
                $response = $assistant->handleChatMessage($text, $user_role);
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
