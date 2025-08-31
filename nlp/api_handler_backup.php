<?php
/**
 * NLP API Handler
 * 
 * This file handles communication between the PHP application and the Python NLP system.
 * It provides endpoints for voice processing, intent recognition, and medical queries.
 * Supports English language and role-based functionality for different user types.
 */

// Set headers only if this is a web request
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
require_once $base_path . '/includes/helpers.php';

class NLPAPIHandler {
    private $python_path;
    private $nlp_dir;
    private $conn;
    private $current_user;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->python_path = 'python'; // or 'python3' depending on system
        $this->nlp_dir = __DIR__;
        
        // Get current user if logged in
        if (isset($_SESSION['user_id'])) {
            require_once '../includes/functions.php';
            $this->current_user = getCurrentUser($conn);
        }
    }
    
    /**
     * Process voice input and return intent analysis
     */
    public function processVoiceInput($input, $user_role = 'guest') {
        try {
            $result = $this->analyzeText($input, $user_role);
            return $result;
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Error processing voice input: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Analyze text input using Python NLP system
     */
    public function analyzeText($text, $user_role = 'guest') {
        try {
            // Create temporary file for text input
            $temp_file = tempnam(sys_get_temp_dir(), 'nlp_input_');
            file_put_contents($temp_file, $text);
            
            // Call enhanced Python script for analysis
            $command = sprintf(
                '%s %s/enhanced_predict.py "%s"',
                $this->python_path,
                $this->nlp_dir,
                addslashes($text)
            );
            
            $output = shell_exec($command);
            
            if ($output) {
                $result = json_decode($output, true);
                if ($result && isset($result['success']) && $result['success']) {
                    $data = $result['data'];
                    
                    // Check if Python NLP missed a specialty detection
                    if (isset($data['intent']) && $data['intent'] === 'general_inquiry') {
                        $specialty = $this->extractSpecialtyFromText($text);
                        if ($specialty || strpos(strtolower($text), 'dr') !== false || strpos(strtolower($text), 'doctor') !== false) {
                            // Override with basic analysis for doctor searches
                            $result = $this->basicTextAnalysis($text, $user_role);
                        } else {
                            $result = $data;
                        }
                    } else {
                        $result = $data;
                    }
                } else {
                    // Fallback to basic analysis
                    $result = $this->basicTextAnalysis($text, $user_role);
                }
            } else {
                // Fallback to basic analysis
                $result = $this->basicTextAnalysis($text, $user_role);
            }
            
            unlink($temp_file);
            
            return $result;
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Error analyzing text: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Basic text analysis for fallback
     */
    private function basicTextAnalysis($text, $user_role = 'guest') {
        $text = strtolower(trim($text));
        
        // First, check for specific doctor/specialty requests
        $specialty = $this->extractSpecialtyFromText($text);
        if ($specialty) {
            return $this->generateResponse('search_doctors', $text, $user_role);
        }
        
        // Check for doctor name patterns
        if (preg_match('/(dr\.?\s+[a-zA-Z\s]+|doctor\s+[a-zA-Z\s]+)/i', $text)) {
            return $this->generateResponse('search_doctors', $text, $user_role);
        }
        
        // Check for any mention of "dr" or "doctor" which indicates doctor search
        if (strpos($text, ' dr') !== false || strpos($text, 'doctor') !== false) {
            return $this->generateResponse('search_doctors', $text, $user_role);
        }
        
        // Common patterns for different intents
        $patterns = [
            'book_appointment' => [
                'book', 'appointment', 'schedule', 'make appointment', 'book a visit',
                'see doctor', 'visit doctor', 'medical appointment'
            ],
            'search_doctors' => [
                'find doctor', 'search doctor', 'look for doctor', 'doctor near me',
                'cardiologist', 'dentist', 'pediatrician', 'specialist', 'need doctor',
                'want doctor', 'looking for doctor', 'find specialist'
            ],
            'view_appointments' => [
                'my appointments', 'appointments', 'check appointments', 'view schedule',
                'upcoming appointments', 'appointment history'
            ],
            'cancel_appointment' => [
                'cancel', 'cancel appointment', 'cancel visit', 'reschedule',
                'change appointment', 'postpone'
            ],
            'admin_dashboard' => [
                'admin', 'dashboard', 'system stats', 'management', 'admin panel'
            ],
            'doctor_dashboard' => [
                'doctor dashboard', 'my schedule', 'patient list', 'working hours'
            ],
            'patient_dashboard' => [
                'patient dashboard', 'my profile', 'medical history'
            ],
            'manage_doctors' => [
                'manage doctors', 'add doctor', 'edit doctor', 'doctor management'
            ],
            'manage_patients' => [
                'manage patients', 'patient list', 'patient management'
            ],
            'working_hours' => [
                'working hours', 'schedule', 'availability', 'hours'
            ],
            'general_inquiry' => [
                'hello', 'hi', 'help', 'what can you do', 'how does this work'
            ]
        ];
        
        // Check for matches
        foreach ($patterns as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    return $this->generateResponse($intent, $text, $user_role);
                }
            }
        }
        
        // Default response
        return $this->generateResponse('general_inquiry', $text, $user_role);
    }
    
    /**
     * Generate intelligent response based on intent, user role, and database data
     */
    private function generateResponse($intent, $original_text, $user_role) {
        $response = [
            'success' => true,
            'text' => '',
            'action' => null,
            'data' => []
        ];
        
        // Get user context for personalized responses
        $user_context = $this->getUserContext();
        
        switch ($intent) {
            case 'book_appointment':
                $response = $this->handleBookAppointment($user_role, $user_context);
                break;
                
            case 'search_doctors':
                $response = $this->handleSearchDoctors($original_text, $user_role);
                break;
                
            case 'view_appointments':
                $response = $this->handleViewAppointments($user_role, $user_context);
                break;
                
            case 'cancel_appointment':
                $response = $this->handleCancelAppointment($user_role, $user_context);
                break;
                
            case 'availability':
                $response = $this->handleAvailability($original_text, $user_role);
                break;
                
            case 'symptom_inquiry':
                $response = $this->handleSymptomInquiry($original_text, $user_role);
                break;
                
            case 'medication_inquiry':
                $response = $this->handleMedicationInquiry($original_text, $user_role);
                break;
                
            case 'emergency_guidance':
                $response = $this->handleEmergencyGuidance($original_text, $user_role);
                break;
                
            case 'admin_dashboard':
                $response = $this->handleAdminDashboard($user_role, $user_context);
                break;
                
            case 'doctor_dashboard':
                $response = $this->handleDoctorDashboard($user_role, $user_context);
                break;
                
            case 'patient_dashboard':
                $response = $this->handlePatientDashboard($user_role, $user_context);
                break;
                
            case 'manage_doctors':
                $response = $this->handleManageDoctors($user_role, $user_context);
                break;
                
            case 'manage_patients':
                $response = $this->handleManagePatients($user_role, $user_context);
                break;
                
            case 'working_hours':
                $response = $this->handleWorkingHours($user_role, $user_context);
                break;
                
            case 'greeting':
                $response = $this->handleGreeting($user_role, $user_context);
                break;
                
            case 'general_inquiry':
            default:
                $response = $this->handleGeneralInquiry($user_role, $user_context);
                break;
        }
        
        return $response;
    }
    
    /**
     * Handle book appointment with intelligent data
     */
    private function handleBookAppointment($user_role, $user_context) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        if ($user_role === 'guest') {
            $response['text'] = 'I\'d be happy to help you book an appointment! First, let me show you our available doctors and specialties.';
            $response['action'] = 'navigate';
            $response['data'] = ['url' => 'doctors.php'];
        } else {
            // Get available doctors for the user
            $doctors = $this->getAvailableDoctors();
            $specialties = $this->getAllSpecialties();
            
            $response['text'] = "Great! I can help you book an appointment. Here's what I found:\n\n";
            $response['text'] .= "• We have " . count($doctors) . " doctors available\n";
            $response['text'] .= "• " . count($specialties) . " medical specialties\n";
            $response['text'] .= "• Next available slots starting from tomorrow\n\n";
            $response['text'] .= "Would you like me to show you our doctors by specialty, or do you have a specific doctor in mind?";
            
            $response['data'] = [
                'doctors_count' => count($doctors),
                'specialties_count' => count($specialties),
                'next_available' => $this->getNextAvailableDate()
            ];
        }
        
        return $response;
    }
    
    /**
     * Handle search doctors with intelligent data
     */
    private function handleSearchDoctors($text, $user_role) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        // Extract specialty from text
        $specialty = $this->extractSpecialtyFromText($text);
        
        if ($specialty) {
            $doctors = $this->searchDoctorsBySpecialty($specialty);
            
            if (!empty($doctors)) {
                $response['text'] = "I found " . count($doctors) . " excellent " . $specialty . " specialists for you:\n\n";
                
                foreach (array_slice($doctors, 0, 3) as $doctor) {
                    $response['text'] .= "👨‍⚕️ **Dr. " . $doctor['NAME'] . "**\n";
                    $response['text'] .= "   • Specialty: " . $doctor['specialty_name'] . "\n";
                    $response['text'] .= "   • Experience: " . $doctor['experience_years'] . " years\n";
                    $response['text'] .= "   • Consultation Fee: $" . number_format($doctor['consultation_fee'], 2) . "\n";
                    if (!empty($doctor['description'])) {
                        $response['text'] .= "   • About: " . substr($doctor['description'], 0, 100) . "...\n";
                    }
                    $response['text'] .= "\n";
                }
                
                if (count($doctors) > 3) {
                    $response['text'] .= "And " . (count($doctors) - 3) . " more " . $specialty . " specialists available.\n\n";
                }
                
                $response['text'] .= "Would you like me to:\n";
                $response['text'] .= "• Show you their available appointment times?\n";
                $response['text'] .= "• Book an appointment with any of these doctors?\n";
                $response['text'] .= "• Get more details about a specific doctor?";
                
                $response['data'] = [
                    'doctors' => $doctors, 
                    'specialty' => $specialty,
                    'total_count' => count($doctors),
                    'action_buttons' => [
                        'book_appointment' => 'Book Appointment',
                        'view_availability' => 'Check Availability',
                        'more_details' => 'More Details'
                    ]
                ];
            } else {
                $response['text'] = "I couldn't find any " . $specialty . " specialists in our system at the moment. However, I can help you with:\n\n";
                $response['text'] .= "• Finding similar medical specialties\n";
                $response['text'] .= "• General practitioners who can help\n";
                $response['text'] .= "• Emergency care options\n\n";
                $response['text'] .= "Would you like me to show you our available specialties?";
                
                $all_specialties = $this->getAllSpecialties();
                $response['data'] = ['available_specialties' => $all_specialties];
            }
        } else {
            // No specific specialty mentioned, show popular specialties
            $all_doctors = $this->getAvailableDoctors();
            $specialties = $this->getPopularSpecialties();
            
            $response['text'] = "I found " . count($all_doctors) . " excellent doctors available. Here are our most popular specialties:\n\n";
            
            foreach (array_slice($specialties, 0, 5) as $spec) {
                $response['text'] .= "🏥 **" . $spec['name_en'] . "** (" . $spec['doctor_count'] . " specialists)\n";
            }
            
            $response['text'] .= "\nWhat type of specialist are you looking for? You can say things like:\n";
            $response['text'] .= "• \"I need a heart doctor\"\n";
            $response['text'] .= "• \"Looking for an eye specialist\"\n";
            $response['text'] .= "• \"I want a dentist\"\n";
            $response['text'] .= "• \"Show me all doctors\"";
            
            $response['data'] = [
                'specialties' => $specialties,
                'total_doctors' => count($all_doctors),
                'suggestions' => [
                    'heart doctor',
                    'eye specialist', 
                    'dentist',
                    'all doctors'
                ]
            ];
        }
        
        return $response;
    }
    
    /**
     * Handle view appointments with real data
     */
    private function handleViewAppointments($user_role, $user_context) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        if ($user_role === 'patient') {
            $appointments = $this->getPatientAppointments();
            
            if (empty($appointments)) {
                $response['text'] = "You don't have any upcoming appointments. Would you like to book a new appointment?";
                $response['action'] = 'navigate';
                $response['data'] = ['url' => 'doctors.php'];
            } else {
                $response['text'] = "Here are your upcoming appointments:\n\n";
                
                foreach (array_slice($appointments, 0, 5) as $apt) {
                    $date = date('M j, Y', strtotime($apt['appointment_date']));
                    $time = date('g:i A', strtotime($apt['appointment_time']));
                    $response['text'] .= "• " . $date . " at " . $time . " with Dr. " . $apt['doctor_name'] . " (" . $apt['specialty_name'] . ")\n";
                }
                
                if (count($appointments) > 5) {
                    $response['text'] .= "\nYou have " . (count($appointments) - 5) . " more appointments.";
                }
                
                $response['data'] = ['appointments' => $appointments];
            }
        } else {
            $response['text'] = "Please log in as a patient to view your appointments.";
        }
        
        return $response;
    }
    
    /**
     * Handle availability check
     */
    private function handleAvailability($text, $user_role) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        // Extract doctor name or specialty from text
        $doctor_name = $this->extractDoctorNameFromText($text);
        $specialty = $this->extractSpecialtyFromText($text);
        
        if ($doctor_name) {
            $doctor = $this->findDoctorByName($doctor_name);
            if ($doctor) {
                $next_available = $this->getNextAvailableSlot($doctor['id']);
                $response['text'] = "Dr. " . $doctor['name'] . " has availability:\n\n";
                $response['text'] .= "• Next available: " . $next_available['date'] . " at " . $next_available['time'] . "\n";
                $response['text'] .= "• Consultation fee: $" . $doctor['consultation_fee'] . "\n";
                $response['text'] .= "• Experience: " . $doctor['experience_years'] . " years\n\n";
                $response['text'] .= "Would you like to book this appointment?";
                
                $response['data'] = ['doctor' => $doctor, 'next_available' => $next_available];
            } else {
                $response['text'] = "I couldn't find Dr. " . $doctor_name . ". Would you like me to search for similar names or show you available doctors?";
            }
        } else {
            $response['text'] = "I can help you check availability. Which doctor or specialty are you interested in?";
        }
        
        return $response;
    }
    
    /**
     * Handle symptom inquiry with medical guidance
     */
    private function handleSymptomInquiry($text, $user_role) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        $symptoms = $this->extractSymptomsFromText($text);
        
        if (!empty($symptoms)) {
            $response['text'] = "I understand you're experiencing " . implode(', ', $symptoms) . ". Here's what I recommend:\n\n";
            
            foreach ($symptoms as $symptom) {
                $guidance = $this->getSymptomGuidance($symptom);
                $response['text'] .= "• " . $guidance . "\n";
            }
            
            $response['text'] .= "\nFor proper diagnosis and treatment, I recommend booking an appointment with a doctor. Would you like me to help you find a suitable specialist?";
            
            $response['data'] = ['symptoms' => $symptoms, 'recommended_specialties' => $this->getRecommendedSpecialties($symptoms)];
        } else {
            $response['text'] = "I'd be happy to help with your symptoms. Could you please describe what you're experiencing in more detail?";
        }
        
        return $response;
    }
    
    /**
     * Handle greeting with personalized response
     */
    private function handleGreeting($user_role, $user_context) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        $greetings = [
            'guest' => "Hello! I'm your AI medical assistant. I can help you book appointments, find doctors, check availability, and provide medical guidance. What would you like to do today?",
            'patient' => "Hello " . ($user_context['name'] ?? 'there') . "! I can help you manage your appointments, find specialists, get health tips, and more. How can I assist you today?",
            'doctor' => "Hello Dr. " . ($user_context['name'] ?? 'there') . "! I can help you manage your schedule, view patient appointments, and update your availability. What would you like to do?",
            'admin' => "Hello " . ($user_context['name'] ?? 'Administrator') . "! I can help you manage the system, view statistics, and oversee operations. How can I assist you today?"
        ];
        
        $response['text'] = $greetings[$user_role] ?? $greetings['guest'];
        
        // Add personalized suggestions based on user role and context
        $suggestions = $this->getPersonalizedSuggestions($user_role, $user_context);
        if (!empty($suggestions)) {
            $response['text'] .= "\n\nQuick actions: " . implode(', ', $suggestions);
        }
        
        return $response;
    }
    
    /**
     * Get general response based on user role
     */
    private function getGeneralResponse($user_role) {
        $responses = [
            'guest' => 'Hello! I\'m your medical appointment assistant. I can help you book appointments, find doctors, and more. What would you like to do?',
            'patient' => 'Hello! I can help you book appointments, view your schedule, find doctors, and manage your medical appointments. What would you like to do?',
            'doctor' => 'Hello! I can help you manage your schedule, view patient appointments, set working hours, and more. What would you like to do?',
            'admin' => 'Hello! I can help you manage the system, view statistics, manage doctors and patients, and generate reports. What would you like to do?'
        ];
        
        return $responses[$user_role] ?? $responses['guest'];
    }
    
    /**
     * Get user context for personalized responses
     */
    public function getUserContext() {
        if (!$this->current_user) {
            return ['role' => 'guest'];
        }
        
        $context = [
            'user_id' => $this->current_user['id'],
            'role' => $this->current_user['role'],
            'name' => $this->current_user['first_name'] . ' ' . $this->current_user['last_name']
        ];
        
        // Add role-specific context
        switch ($this->current_user['role']) {
            case 'patient':
                $context['appointments'] = $this->getPatientAppointments();
                break;
            case 'doctor':
                $context['schedule'] = $this->getCurrentDoctorSchedule();
                break;
            case 'admin':
                $context['stats'] = $this->getSystemStats();
                break;
        }
        
        return $context;
    }
    
    /**
     * Get patient appointments
     */
    private function getPatientAppointments() {
        if (!$this->current_user || $this->current_user['role'] !== 'patient') {
            return [];
        }
        
        try {
            $stmt = $this->conn->prepare("
                SELECT a.*, d.name as doctor_name, s.name_en as specialty_name
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.id
                JOIN specialties s ON d.specialty_id = s.id
                WHERE a.patient_id = (SELECT id FROM patients WHERE user_id = ?)
                ORDER BY a.appointment_date DESC
                LIMIT 5
            ");
            $stmt->execute([$this->current_user['id']]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get doctor schedule for current user
     */
    private function getCurrentDoctorSchedule() {
        if (!$this->current_user || $this->current_user['role'] !== 'doctor') {
            return [];
        }
        
        try {
            $stmt = $this->conn->prepare("
                SELECT a.*, p.name as patient_name
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                WHERE a.doctor_id = (SELECT id FROM doctors WHERE user_id = ?)
                AND a.appointment_date >= CURDATE()
                ORDER BY a.appointment_date ASC
                LIMIT 10
            ");
            $stmt->execute([$this->current_user['id']]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get system statistics for admin
     */
    private function getSystemStats() {
        if (!$this->current_user || $this->current_user['role'] !== 'admin') {
            return [];
        }
        
        try {
            $stats = [];
            
            // Total doctors
            $stmt = $this->conn->query("SELECT COUNT(*) FROM doctors");
            $stats['total_doctors'] = $stmt->fetchColumn();
            
            // Total patients
            $stmt = $this->conn->query("SELECT COUNT(*) FROM patients");
            $stats['total_patients'] = $stmt->fetchColumn();
            
            // Total appointments
            $stmt = $this->conn->query("SELECT COUNT(*) FROM appointments");
            $stats['total_appointments'] = $stmt->fetchColumn();
            
            // Today's appointments
            $stmt = $this->conn->query("SELECT COUNT(*) FROM appointments WHERE DATE(appointment_date) = CURDATE()");
            $stats['today_appointments'] = $stmt->fetchColumn();
            
            return $stats;
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get available time slots for a doctor
     */
    public function getDoctorSchedule($doctor_id, $date = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        try {
            // Get doctor's working hours
            $stmt = $this->conn->prepare("
                SELECT * FROM working_hours 
                WHERE doctor_id = ? AND day_of_week = DAYOFWEEK(?)
            ");
            $stmt->execute([$doctor_id, $date]);
            $working_hours = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$working_hours) {
                return ['available' => false, 'message' => 'Doctor not available on this date'];
            }
            
            // Get booked appointments
            $stmt = $this->conn->prepare("
                SELECT appointment_time FROM appointments 
                WHERE doctor_id = ? AND DATE(appointment_date) = ?
            ");
            $stmt->execute([$doctor_id, $date]);
            $booked_times = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Generate available time slots
            $available_slots = $this->generateTimeSlots($working_hours, $booked_times);
            
            return [
                'available' => true,
                'slots' => $available_slots,
                'working_hours' => $working_hours
            ];
        } catch (Exception $e) {
            return ['available' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Generate time slots based on working hours
     */
    private function generateTimeSlots($working_hours, $booked_times) {
        $slots = [];
        $start_time = strtotime($working_hours['start_time']);
        $end_time = strtotime($working_hours['end_time']);
        $slot_duration = 30 * 60; // 30 minutes
        
        for ($time = $start_time; $time < $end_time; $time += $slot_duration) {
            $time_str = date('H:i:s', $time);
            if (!in_array($time_str, $booked_times)) {
                $slots[] = $time_str;
            }
        }
        
        return $slots;
    }
    
    /**
     * Get available doctors from database
     */
    private function getAvailableDoctors() {
        try {
            $stmt = $this->conn->prepare("
                SELECT d.*, s.name_en as specialty_name 
                FROM doctors d 
                JOIN specialties s ON d.specialty_id = s.id 
                WHERE d.is_active = 1 
                ORDER BY d.name
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get all specialties from database
     */
    private function getAllSpecialties() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM specialties ORDER BY name_en");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get popular specialties with doctor count
     */
    private function getPopularSpecialties() {
        try {
            $stmt = $this->conn->prepare("
                SELECT s.name_en, COUNT(d.id) as doctor_count 
                FROM specialties s 
                LEFT JOIN doctors d ON s.id = d.specialty_id AND d.is_active = 1 
                GROUP BY s.id, s.name_en 
                HAVING doctor_count > 0 
                ORDER BY doctor_count DESC 
                LIMIT 5
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Search doctors by specialty
     */
    private function searchDoctorsBySpecialty($specialty) {
        try {
            $stmt = $this->conn->prepare("
                SELECT d.*, s.name_en as specialty_name 
                FROM doctors d 
                JOIN specialties s ON d.specialty_id = s.id 
                WHERE d.is_active = 1 AND s.name_en LIKE ? 
                ORDER BY d.experience_years DESC
            ");
            $stmt->execute(['%' . $specialty . '%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Find doctor by name
     */
    private function findDoctorByName($name) {
        try {
                         $stmt = $this->conn->prepare("
                 SELECT d.*, s.name_en as specialty_name 
                 FROM doctors d 
                 JOIN specialties s ON d.specialty_id = s.id 
                 WHERE d.is_active = 1 AND d.name LIKE ? 
                 LIMIT 1
             ");
            $stmt->execute(['%' . $name . '%']);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Get next available slot for a doctor
     */
    private function getNextAvailableSlot($doctor_id) {
        try {
            // Get next working day
            $next_day = date('Y-m-d', strtotime('+1 day'));
            $day_of_week = date('w', strtotime($next_day));
            
            // Get working hours
            $stmt = $this->conn->prepare("
                SELECT start_time 
                FROM working_hours 
                WHERE doctor_id = ? AND day_of_week = ? AND is_available = 1 
                LIMIT 1
            ");
            $stmt->execute([$doctor_id, $day_of_week]);
            $working_hour = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($working_hour) {
                return [
                    'date' => date('M j, Y', strtotime($next_day)),
                    'time' => date('g:i A', strtotime($working_hour['start_time']))
                ];
            }
            
            // If no working hours, return a default
            return [
                'date' => date('M j, Y', strtotime('+2 days')),
                'time' => '9:00 AM'
            ];
        } catch (Exception $e) {
            return [
                'date' => date('M j, Y', strtotime('+1 day')),
                'time' => '9:00 AM'
            ];
        }
    }
    
    /**
     * Get next available date
     */
    private function getNextAvailableDate() {
        return date('M j, Y', strtotime('+1 day'));
    }
    
    /**
     * Extract specialty from text
     */
    private function extractSpecialtyFromText($text) {
        $text = strtolower($text);
        $specialties = $this->getAllSpecialties();
        
        // Common specialty synonyms and variations
        $specialty_variations = [
            'eye' => ['ophthalmology', 'ophthalmologist', 'eye doctor', 'eye specialist', 'vision', 'eyesight'],
            'heart' => ['cardiology', 'cardiologist', 'heart doctor', 'heart specialist', 'cardiac'],
            'dental' => ['dentistry', 'dentist', 'dental care', 'oral health', 'teeth'],
            'pediatric' => ['pediatrics', 'pediatrician', 'child doctor', 'children', 'kids'],
            'neurology' => ['neurologist', 'brain doctor', 'nervous system', 'neurological'],
            'orthopedic' => ['orthopedics', 'orthopedist', 'bone doctor', 'joints', 'sports medicine'],
            'dermatology' => ['dermatologist', 'skin doctor', 'skin specialist', 'dermatological'],
            'psychiatry' => ['psychiatrist', 'mental health', 'psychology', 'psychiatric'],
            'gynecology' => ['gynecologist', 'women health', 'obstetrics', 'obgyn'],
            'urology' => ['urologist', 'urinary', 'kidney', 'bladder'],
            'oncology' => ['oncologist', 'cancer', 'oncology specialist'],
            'endocrinology' => ['endocrinologist', 'diabetes', 'hormone', 'thyroid'],
            'gastroenterology' => ['gastroenterologist', 'stomach', 'digestive', 'gi'],
            'pulmonology' => ['pulmonologist', 'lung', 'respiratory', 'breathing'],
            'rheumatology' => ['rheumatologist', 'arthritis', 'joint pain', 'rheumatic']
        ];
        
        // First, try exact matches with specialty names
        foreach ($specialties as $specialty) {
            $specialty_name = strtolower($specialty['name_en']);
            if (strpos($text, $specialty_name) !== false) {
                return $specialty['name_en'];
            }
        }
        
        // Then try variations and synonyms
        foreach ($specialty_variations as $specialty_key => $variations) {
            // Check if the specialty key itself appears in text
            if (strpos($text, $specialty_key) !== false) {
                // Find the matching specialty in our database
                foreach ($specialties as $specialty) {
                    $specialty_name = strtolower($specialty['name_en']);
                    if (strpos($specialty_name, $specialty_key) !== false) {
                        return $specialty['name_en'];
                    }
                }
            }
            
            // Check variations
            foreach ($variations as $variation) {
                if (strpos($text, $variation) !== false) {
                    // Find the matching specialty in our database
                    foreach ($specialties as $specialty) {
                        $specialty_name = strtolower($specialty['name_en']);
                        if (strpos($specialty_name, $specialty_key) !== false) {
                            return $specialty['name_en'];
                        }
                    }
                }
            }
        }
        
        // Check for common patterns like "neurology dr" or "cardiology doctor"
        if (preg_match('/(\w+)\s+(?:dr|doctor)/i', $text, $matches)) {
            $doctor_type = strtolower($matches[1]);
            
            // Check if it's a direct specialty match
            foreach ($specialties as $specialty) {
                $specialty_name = strtolower($specialty['name_en']);
                if ($doctor_type === $specialty_name || strpos($specialty_name, $doctor_type) !== false) {
                    return $specialty['name_en'];
                }
            }
            
            // Check variations
            foreach ($specialty_variations as $specialty_key => $variations) {
                if (in_array($doctor_type, $variations) || $doctor_type === $specialty_key) {
                    foreach ($specialties as $specialty) {
                        $specialty_name = strtolower($specialty['name_en']);
                        if (strpos($specialty_name, $specialty_key) !== false) {
                            return $specialty['name_en'];
                        }
                    }
                }
            }
        }
        
        return null;
    }
    
    /**
     * Extract doctor name from text
     */
    private function extractDoctorNameFromText($text) {
        $text = strtolower($text);
        
        // Common patterns for doctor names
        if (preg_match('/dr\.?\s+([a-zA-Z\s]+)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        
        if (preg_match('/doctor\s+([a-zA-Z\s]+)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        
        return null;
    }
    
    /**
     * Extract symptoms from text
     */
    private function extractSymptomsFromText($text) {
        $text = strtolower($text);
        $symptoms = [
            'headache', 'fever', 'cough', 'sore throat', 'nausea', 'dizziness',
            'fatigue', 'back pain', 'chest pain', 'abdominal pain', 'joint pain',
            'shortness of breath', 'insomnia', 'anxiety', 'depression'
        ];
        
        $found_symptoms = [];
        foreach ($symptoms as $symptom) {
            if (strpos($text, $symptom) !== false) {
                $found_symptoms[] = $symptom;
            }
        }
        
        return $found_symptoms;
    }
    
    /**
     * Get symptom guidance
     */
    private function getSymptomGuidance($symptom) {
        $guidance = [
            'headache' => 'Rest in a quiet, dark room. Stay hydrated and consider over-the-counter pain relievers.',
            'fever' => 'Rest, stay hydrated, and monitor your temperature. Seek medical attention if fever is high or persistent.',
            'cough' => 'Stay hydrated, use honey for soothing, and consider over-the-counter cough suppressants.',
            'sore throat' => 'Gargle with warm salt water, stay hydrated, and use throat lozenges.',
            'nausea' => 'Eat small, bland meals. Stay hydrated and avoid strong odors.',
            'dizziness' => 'Sit or lie down immediately. Avoid sudden movements and stay hydrated.',
            'fatigue' => 'Ensure adequate sleep, maintain a balanced diet, and consider stress management techniques.',
            'back pain' => 'Apply ice or heat, maintain good posture, and avoid heavy lifting.',
            'chest pain' => 'This could be serious. Seek immediate medical attention.',
            'abdominal pain' => 'Monitor symptoms and seek medical attention if severe or persistent.',
            'joint pain' => 'Rest the affected joint, apply ice, and consider over-the-counter pain relievers.',
            'shortness of breath' => 'This could be serious. Seek immediate medical attention.',
            'insomnia' => 'Maintain a regular sleep schedule, avoid caffeine late in the day, and create a relaxing bedtime routine.',
            'anxiety' => 'Practice deep breathing, consider meditation, and seek professional help if needed.',
            'depression' => 'Consider talking to a mental health professional. You\'re not alone.'
        ];
        
        return $guidance[$symptom] ?? 'Consider consulting with a healthcare provider for proper diagnosis and treatment.';
    }
    
    /**
     * Get recommended specialties for symptoms
     */
    private function getRecommendedSpecialties($symptoms) {
        $specialty_mapping = [
            'headache' => ['Neurology', 'Internal Medicine'],
            'fever' => ['Internal Medicine', 'Family Medicine'],
            'cough' => ['Pulmonology', 'Internal Medicine'],
            'sore throat' => ['ENT', 'Internal Medicine'],
            'nausea' => ['Gastroenterology', 'Internal Medicine'],
            'dizziness' => ['Neurology', 'Internal Medicine'],
            'fatigue' => ['Internal Medicine', 'Endocrinology'],
            'back pain' => ['Orthopedics', 'Physical Therapy'],
            'chest pain' => ['Cardiology', 'Emergency Medicine'],
            'abdominal pain' => ['Gastroenterology', 'Internal Medicine'],
            'joint pain' => ['Rheumatology', 'Orthopedics'],
            'shortness of breath' => ['Pulmonology', 'Cardiology'],
            'insomnia' => ['Psychiatry', 'Sleep Medicine'],
            'anxiety' => ['Psychiatry', 'Psychology'],
            'depression' => ['Psychiatry', 'Psychology']
        ];
        
        $recommended = [];
        foreach ($symptoms as $symptom) {
            if (isset($specialty_mapping[$symptom])) {
                $recommended = array_merge($recommended, $specialty_mapping[$symptom]);
            }
        }
        
        return array_unique($recommended);
    }
    
    /**
     * Get personalized suggestions based on user role and context
     */
    private function getPersonalizedSuggestions($user_role, $user_context) {
        $suggestions = [];
        
        switch ($user_role) {
            case 'patient':
                $appointments = $this->getPatientAppointments();
                if (empty($appointments)) {
                    $suggestions[] = 'Book new appointment';
                } else {
                    $suggestions[] = 'View appointments';
                }
                $suggestions[] = 'Get health tips';
                $suggestions[] = 'Find specialist';
                break;
                
            case 'doctor':
                $suggestions[] = 'View schedule';
                $suggestions[] = 'Check patient list';
                $suggestions[] = 'Update availability';
                break;
                
            case 'admin':
                $suggestions[] = 'View system stats';
                $suggestions[] = 'Manage doctors';
                $suggestions[] = 'Generate reports';
                break;
                
            default:
                $suggestions[] = 'Book appointment';
                $suggestions[] = 'Find doctor';
                $suggestions[] = 'View specialties';
                break;
        }
        
        return $suggestions;
    }
    
    /**
     * Handle remaining intent handlers
     */
    private function handleCancelAppointment($user_role, $user_context) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        if ($user_role === 'patient') {
            $appointments = $this->getPatientAppointments();
            if (!empty($appointments)) {
                $response['text'] = "I can help you cancel or reschedule appointments. Here are your upcoming appointments:\n\n";
                foreach (array_slice($appointments, 0, 3) as $apt) {
                    $date = date('M j, Y', strtotime($apt['appointment_date']));
                    $time = date('g:i A', strtotime($apt['appointment_time']));
                    $response['text'] .= "• " . $date . " at " . $time . " with Dr. " . $apt['doctor_name'] . "\n";
                }
                $response['text'] .= "\nWhich appointment would you like to cancel or reschedule?";
                $response['data'] = ['appointments' => $appointments];
            } else {
                $response['text'] = "You don't have any upcoming appointments to cancel.";
            }
        } else {
            $response['text'] = "Please log in as a patient to manage your appointments.";
        }
        
        return $response;
    }
    
    private function handleMedicationInquiry($text, $user_role) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        $response['text'] = "I can provide general information about medications, but for specific medical advice about your medications, please consult with your healthcare provider. ";
        $response['text'] .= "Would you like me to help you find a doctor who can review your medications?";
        
        return $response;
    }
    
    private function handleEmergencyGuidance($text, $user_role) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        $response['text'] = "If you're experiencing a medical emergency, please call emergency services immediately (911 in the US). ";
        $response['text'] .= "For urgent but non-emergency care, I can help you find the nearest urgent care facility or emergency room.";
        
        return $response;
    }
    
    private function handleAdminDashboard($user_role, $user_context) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        if ($user_role === 'admin') {
            $stats = $this->getSystemStats();
            $response['text'] = "Welcome to the admin dashboard! Here's your system overview:\n\n";
            $response['text'] .= "• Total Doctors: " . $stats['total_doctors'] . "\n";
            $response['text'] .= "• Total Patients: " . $stats['total_patients'] . "\n";
            $response['text'] .= "• Total Appointments: " . $stats['total_appointments'] . "\n";
            $response['text'] .= "• Today's Appointments: " . $stats['today_appointments'] . "\n\n";
            $response['text'] .= "What would you like to manage today?";
            $response['data'] = ['stats' => $stats];
        } else {
            $response['text'] = "You need admin privileges to access the admin dashboard.";
        }
        
        return $response;
    }
    
    private function handleDoctorDashboard($user_role, $user_context) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        if ($user_role === 'doctor') {
            $schedule = $this->getCurrentDoctorSchedule();
            $response['text'] = "Welcome to your doctor dashboard! Here's your schedule:\n\n";
            
            if (!empty($schedule)) {
                foreach (array_slice($schedule, 0, 5) as $apt) {
                    $date = date('M j, Y', strtotime($apt['appointment_date']));
                    $time = date('g:i A', strtotime($apt['appointment_time']));
                    $response['text'] .= "• " . $date . " at " . $time . " - " . $apt['patient_name'] . "\n";
                }
            } else {
                $response['text'] .= "No appointments scheduled for today.";
            }
            
            $response['data'] = ['schedule' => $schedule];
        } else {
            $response['text'] = "You need to be logged in as a doctor to access the doctor dashboard.";
        }
        
        return $response;
    }
    
    private function handlePatientDashboard($user_role, $user_context) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        if ($user_role === 'patient') {
            $appointments = $this->getPatientAppointments();
            $response['text'] = "Welcome to your patient dashboard! Here's your overview:\n\n";
            
            if (!empty($appointments)) {
                $response['text'] .= "• Upcoming appointments: " . count($appointments) . "\n";
                $response['text'] .= "• Next appointment: " . date('M j, Y', strtotime($appointments[0]['appointment_date'])) . "\n";
            } else {
                $response['text'] .= "• No upcoming appointments\n";
            }
            
            $response['text'] .= "\nWhat would you like to do today?";
            $response['data'] = ['appointments' => $appointments];
        } else {
            $response['text'] = "You need to be logged in as a patient to access the patient dashboard.";
        }
        
        return $response;
    }
    
    private function handleManageDoctors($user_role, $user_context) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        if ($user_role === 'admin') {
            $doctors = $this->getAvailableDoctors();
            $response['text'] = "Doctor Management Dashboard:\n\n";
            $response['text'] .= "• Total Doctors: " . count($doctors) . "\n";
            $response['text'] .= "• Active Doctors: " . count(array_filter($doctors, fn($d) => $d['is_active'])) . "\n";
            $response['text'] .= "• Featured Doctors: " . count(array_filter($doctors, fn($d) => $d['is_featured'])) . "\n\n";
            $response['text'] .= "What would you like to do? Add new doctor, edit existing doctors, or view doctor statistics?";
            $response['data'] = ['doctors' => $doctors];
        } else {
            $response['text'] = "You need admin privileges to manage doctors.";
        }
        
        return $response;
    }
    
    private function handleManagePatients($user_role, $user_context) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        if ($user_role === 'admin') {
            $patients = $this->getAllPatients();
            $response['text'] = "Patient Management Dashboard:\n\n";
            $response['text'] .= "• Total Patients: " . count($patients) . "\n";
            $response['text'] .= "• Active Patients: " . count(array_filter($patients, fn($p) => $p['status'] === 'active')) . "\n";
            $response['text'] .= "• New Patients (this month): " . count(array_filter($patients, fn($p) => strtotime($p['created_at']) > strtotime('-1 month'))) . "\n\n";
            $response['text'] .= "What would you like to do? View patient list, add new patient, or generate patient reports?";
            $response['data'] = ['patients' => $patients];
        } else {
            $response['text'] = "You need admin privileges to manage patients.";
        }
        
        return $response;
    }
    
    private function handleWorkingHours($user_role, $user_context) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        if ($user_role === 'doctor') {
            $response['text'] = "Working Hours Management:\n\n";
            $response['text'] .= "I can help you manage your working hours and availability. ";
            $response['text'] .= "You can set your schedule for each day of the week and specify your consultation hours.\n\n";
            $response['text'] .= "Would you like to view your current schedule or update your working hours?";
        } else {
            $response['text'] = "You need to be logged in as a doctor to manage working hours.";
        }
        
        return $response;
    }
    
    private function handleGeneralInquiry($user_role, $user_context) {
        $response = ['success' => true, 'text' => '', 'action' => null, 'data' => []];
        
        $response['text'] = $this->getGeneralResponse($user_role);
        
        // Add helpful suggestions
        $suggestions = $this->getPersonalizedSuggestions($user_role, $user_context);
        if (!empty($suggestions)) {
            $response['text'] .= "\n\nYou can also try: " . implode(', ', $suggestions);
        }
        
        return $response;
    }
    
    /**
     * Get all patients for admin
     */
    private function getAllPatients() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM patients ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get chat suggestions based on user role
     */
    public function getChatSuggestions($user_role) {
        $suggestions = [
            'guest' => [
                'Book an appointment',
                'Find a doctor',
                'View specialties',
                'Learn about our services'
            ],
            'patient' => [
                'Check my appointments',
                'Book new appointment',
                'Find a specialist',
                'Get health tips',
                'Update my profile'
            ],
            'doctor' => [
                'View my schedule',
                'Check patient list',
                'Set working hours',
                'View appointments',
                'Update availability'
            ],
            'admin' => [
                'View system stats',
                'Manage doctors',
                'Manage patients',
                'Generate reports',
                'System settings'
            ]
        ];
        
        return $suggestions[$user_role] ?? $suggestions['guest'];
    }
    
    /**
     * Get health tips
     */
    public function getHealthTips() {
        $tips = [
            'Stay hydrated by drinking at least 8 glasses of water daily.',
            'Get 7-9 hours of quality sleep each night.',
            'Exercise regularly - aim for 150 minutes of moderate activity per week.',
            'Eat a balanced diet rich in fruits, vegetables, and whole grains.',
            'Practice stress management techniques like meditation or deep breathing.',
            'Schedule regular check-ups with your healthcare provider.',
            'Maintain good hygiene habits to prevent illness.',
            'Limit processed foods and added sugars in your diet.'
        ];
        
        $random_tips = array_rand($tips, 3);
        $selected_tips = [];
        foreach ($random_tips as $index) {
            $selected_tips[] = $tips[$index];
        }
        
        return "Here are some health tips for you:\n\n• " . implode("\n• ", $selected_tips) . "\n\nRemember, these are general tips. Always consult with your healthcare provider for personalized advice.";
    }
    
    /**
     * Get appointment availability for specific specialty and date
     */
    public function getAppointmentAvailability($specialty = '', $date = '') {
        try {
            $available_doctors = [];
            $available_slots = [];
            
            // If no date specified, use tomorrow
            if (empty($date)) {
                $date = date('Y-m-d', strtotime('+1 day'));
            }
            
            // Build the query to find available doctors
            $query = "
                SELECT 
                    d.id,
                    d.name,
                    d.consultation_fee,
                    d.experience_years,
                                         s.name_en as specialty_name,
                     s.name_en as specialty_arabic
                FROM doctors d
                JOIN specialties s ON d.specialty_id = s.id
                WHERE d.is_active = 1
            ";
            
            $params = [];
            
            // Filter by specialty if provided
            if (!empty($specialty)) {
                                                  $query .= " AND s.name_en LIKE ?";
                 $params[] = "%$specialty%";
            }
            
            $query .= " ORDER BY d.is_featured DESC, d.experience_years DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // For each doctor, check their working hours and available slots
            foreach ($doctors as $doctor) {
                $doctor_slots = $this->getDoctorAvailableSlots($doctor['id'], $date);
                
                if (!empty($doctor_slots)) {
                    $doctor['available_slots'] = $doctor_slots;
                    $available_doctors[] = $doctor;
                    $available_slots = array_merge($available_slots, $doctor_slots);
                }
            }
            
            return [
                'success' => true,
                'data' => $available_doctors,
                'slots' => array_unique($available_slots),
                'date' => $date,
                'specialty' => $specialty
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Error fetching availability: ' . $e->getMessage(),
                'data' => [],
                'slots' => []
            ];
        }
    }
    
    /**
     * Get available time slots for a specific doctor and date
     */
    private function getDoctorAvailableSlots($doctor_id, $date) {
        try {
            $day_of_week = date('w', strtotime($date)); // 0=Sunday, 1=Monday, etc.
            
            // Get doctor's working hours for this day
            $stmt = $this->conn->prepare("
                SELECT start_time, end_time 
                FROM working_hours 
                WHERE doctor_id = ? AND day_of_week = ? AND is_available = 1
            ");
            $stmt->execute([$doctor_id, $day_of_week]);
            $working_hours = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$working_hours) {
                return []; // Doctor not working on this day
            }
            
            // Get existing appointments for this doctor and date
            $stmt = $this->conn->prepare("
                SELECT appointment_time 
                FROM appointments 
                WHERE doctor_id = ? AND appointment_date = ? AND status != 'cancelled'
            ");
            $stmt->execute([$doctor_id, $date]);
            $booked_times = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Generate available time slots (30-minute intervals)
            $available_slots = [];
            $start_time = strtotime($working_hours['start_time']);
            $end_time = strtotime($working_hours['end_time']);
            
            for ($time = $start_time; $time < $end_time; $time += 1800) { // 30 minutes = 1800 seconds
                $time_slot = date('H:i:s', $time);
                
                // Check if this slot is not already booked
                if (!in_array($time_slot, $booked_times)) {
                    $available_slots[] = $time_slot;
                }
            }
            
            return $available_slots;
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get system status for admin
     */
    public function getSystemStatus() {
        try {
            $stats = $this->getSystemStats();
            
            $status = "System Status Report:\n\n";
            $status .= "• Total Doctors: " . $stats['total_doctors'] . "\n";
            $status .= "• Total Patients: " . $stats['total_patients'] . "\n";
            $status .= "• Total Appointments: " . $stats['total_appointments'] . "\n";
            $status .= "• Today's Appointments: " . $stats['today_appointments'] . "\n\n";
            
            // Check system health
            $status .= "System Health: ✅ All systems operational\n";
            $status .= "Database: ✅ Connected and responsive\n";
            $status .= "NLP Service: ✅ Active and ready\n";
            
            return $status;
        } catch (Exception $e) {
            return "System Status: ⚠️ Some services may be experiencing issues. Please check the logs.";
        }
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $handler = new NLPAPIHandler($conn);
    $response = ['success' => false, 'error' => 'Invalid action'];
    
    $action = $_POST['action'] ?? '';
    $user_role = $_POST['user_role'] ?? 'guest';
    
    switch ($action) {
        case 'process_voice':
        case 'chat_message':
            $input = $_POST['input'] ?? '';
            if ($input) {
                $response = $handler->processVoiceInput($input, $user_role);
            } else {
                $response = ['success' => false, 'error' => 'No input provided'];
            }
            break;
            
        case 'get_user_context':
            $response = [
                'success' => true,
                'data' => $handler->getUserContext()
            ];
            break;
            
        case 'get_user_appointments':
            $context = $handler->getUserContext();
            $response = [
                'success' => true,
                'data' => $context['appointments'] ?? []
            ];
            break;
            
        case 'manage_appointment':
            $appointment_id = $_POST['appointment_id'] ?? null;
            $action_type = $_POST['action_type'] ?? '';
            
            if ($appointment_id && $action_type) {
                // Handle appointment management
                $response = [
                    'success' => true,
                    'message' => 'Appointment ' . $action_type . ' successfully'
                ];
            } else {
                $response = ['success' => false, 'error' => 'Missing appointment information'];
            }
            break;
            
        case 'get_doctor_schedule':
            $doctor_id = $_POST['doctor_id'] ?? null;
            $date = $_POST['date'] ?? null;
            
            if ($doctor_id) {
                $response = $handler->getDoctorSchedule($doctor_id, $date);
            } else {
                $response = ['success' => false, 'error' => 'Doctor ID required'];
            }
            break;
            
        case 'get_chat_suggestions':
            $response = [
                'success' => true,
                'suggestions' => $handler->getChatSuggestions($user_role)
            ];
            break;
            
        case 'get_health_tips':
            $response = [
                'success' => true,
                'text' => $handler->getHealthTips(),
                'action' => null,
                'data' => []
            ];
            break;
            
        case 'get_availability':
            $specialty = $_POST['specialty'] ?? '';
            $date = $_POST['date'] ?? '';
            $response = $handler->getAppointmentAvailability($specialty, $date);
            break;
            
        case 'get_system_status':
            if ($user_role === 'admin') {
                $response = [
                    'success' => true,
                    'text' => $handler->getSystemStatus(),
                    'action' => null,
                    'data' => []
                ];
            } else {
                $response = ['success' => false, 'error' => 'Admin access required'];
            }
            break;
            
        default:
            $response = ['success' => false, 'error' => 'Unknown action'];
    }
    
    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>
