@extends('layouts.app')

@section('title', 'AI Health Assistant - Medical Booking System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 w-72 h-72 bg-gold/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center" data-aos="fade-up">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    AI <span class="text-gold">Health Assistant</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    Your intelligent healthcare companion. Get instant health insights, symptom analysis,
                    and personalized recommendations powered by advanced artificial intelligence.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Chat Interface -->
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Chat History Sidebar -->
            <div class="lg:col-span-1">
                <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-6 border border-white/10">
                    <h3 class="text-xl font-bold text-white mb-4">Recent Conversations</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @if($conversationHistory && count($conversationHistory) > 0)
                            @foreach($conversationHistory as $conversation)
                                <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-3 border border-white/10 hover:border-gold/30 transition-all duration-300 cursor-pointer">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center">
                                            <i class="fas fa-robot text-white text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-white truncate">
                                                {{ Str::limit($conversation->user_message, 30) }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ \Carbon\Carbon::parse($conversation->created_at)->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gradient-to-br from-gold/20 to-gold-deep/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-comments text-gold text-2xl"></i>
                                </div>
                                <p class="text-gray-400 text-sm">No conversations yet</p>
                                <p class="text-gray-500 text-xs">Start chatting with your AI assistant</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="lg:col-span-3">
                <div class="backdrop-blur-xl bg-white/5 rounded-3xl border border-white/10 overflow-hidden">
                    <!-- Chat Header -->
                    <div class="bg-gradient-to-r from-gold/20 to-gold-deep/20 p-6 border-b border-white/10">
                        <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-deep rounded-2xl flex items-center justify-center">
                                <i class="fas fa-robot text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">AI Health Assistant</h2>
                                <p id="ai-status" class="text-gray-300 text-sm">Connecting to AI service...</p>
                            </div>
                            </div>
                            <!-- Voice Settings -->
                            <div class="flex items-center space-x-3">
                                <button id="voice-settings-btn"
                                        class="bg-white/10 hover:bg-white/20 text-white p-2 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-gold/50">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <button id="tts-toggle"
                                        class="bg-white/10 hover:bg-white/20 text-white p-2 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-gold/50"
                                        title="Toggle Text-to-Speech">
                                    <i class="fas fa-volume-up"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div id="chat-messages" class="h-96 overflow-y-auto p-6 space-y-4">
                        <!-- Welcome Message -->
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-robot text-white text-sm"></i>
                            </div>
                            <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-4 border border-white/10 max-w-3xl">
                                <p class="text-white">
                                    Hello! I'm your AI Health Assistant. I can help you with:
                                </p>
                                <ul class="text-gray-300 mt-3 space-y-2">
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-gold mr-2 text-xs"></i>
                                        Symptom analysis and preliminary diagnosis
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-gold mr-2 text-xs"></i>
                                        Medication information and interactions
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-gold mr-2 text-xs"></i>
                                        Health tips and lifestyle recommendations
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-gold mr-2 text-xs"></i>
                                        Appointment scheduling assistance
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-microphone text-gold mr-2 text-xs"></i>
                                        Voice commands and speech recognition
                                    </li>
                                </ul>
                                <p class="text-gray-300 mt-3">
                                    How can I help you today?
                                </p>
                                <div class="mt-4 p-3 bg-blue-500/10 rounded-lg border border-blue-500/20">
                                    <p class="text-blue-300 text-sm mb-2">
                                        <i class="fas fa-microphone mr-1"></i>
                                        <strong>Voice Commands Available:</strong>
                                    </p>
                                    <ul class="text-blue-200 text-xs space-y-1">
                                        <li>• "Book an appointment with a cardiologist"</li>
                                        <li>• "What are the symptoms of diabetes?"</li>
                                        <li>• "Tell me about medication interactions"</li>
                                        <li>• "Give me health tips for heart disease"</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Input -->
                    <div class="p-6 border-t border-white/10">
                        <form id="chat-form" class="flex space-x-4">
                            @csrf
                            <div class="flex-1 relative">
                                <input type="text"
                                       id="message-input"
                                       name="message"
                                       placeholder="Type your health question here or use voice..."
                                       class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 pr-12 text-white placeholder-gray-400 focus:outline-none focus:border-gold/50 focus:ring-2 focus:ring-gold/20 backdrop-blur-xl">
                                <!-- Voice Input Button -->
                                <button type="button"
                                        id="voice-btn"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-blue-500 to-blue-600 text-white p-2 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                                    <i class="fas fa-microphone"></i>
                                </button>
                                <!-- Voice Status Indicator -->
                                <div id="voice-status" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                                    <div class="w-6 h-6 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                                </div>
                            </div>
                            <button type="submit"
                                    class="bg-gradient-to-r from-gold to-gold-deep text-white px-6 py-3 rounded-2xl font-medium hover:from-gold-deep hover:to-gold transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-gold/50">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Send
                            </button>
                        </form>
                        <!-- Voice Instructions -->
                        <div id="voice-instructions" class="mt-3 text-center hidden">
                            <p class="text-gray-400 text-sm">
                                <i class="fas fa-microphone mr-1"></i>
                                Listening... Speak now
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button onclick="sendQuickMessage('I have a headache, what should I do?')"
                        class="backdrop-blur-xl bg-white/5 rounded-2xl p-4 border border-white/10 hover:border-gold/30 transition-all duration-300 text-left group">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-head-side-cough text-white"></i>
                    </div>
                    <h4 class="text-white font-medium mb-1">Headache Relief</h4>
                    <p class="text-gray-400 text-sm">Get advice for headache symptoms</p>
                </button>

                <button onclick="sendQuickMessage('What are the symptoms of COVID-19?')"
                        class="backdrop-blur-xl bg-white/5 rounded-2xl p-4 border border-white/10 hover:border-gold/30 transition-all duration-300 text-left group">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-virus text-white"></i>
                    </div>
                    <h4 class="text-white font-medium mb-1">COVID-19 Info</h4>
                    <p class="text-gray-400 text-sm">Learn about COVID-19 symptoms</p>
                </button>

                <button onclick="sendQuickMessage('Help me book an appointment')"
                        class="backdrop-blur-xl bg-white/5 rounded-2xl p-4 border border-white/10 hover:border-gold/30 transition-all duration-300 text-left group">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-calendar-plus text-white"></i>
                    </div>
                    <h4 class="text-white font-medium mb-1">Book Appointment</h4>
                    <p class="text-gray-400 text-sm">Schedule a doctor visit</p>
                </button>

                <button onclick="sendQuickMessage('What medications interact with aspirin?')"
                        class="backdrop-blur-xl bg-white/5 rounded-2xl p-4 border border-white/10 hover:border-gold/30 transition-all duration-300 text-left group">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-pills text-white"></i>
                    </div>
                    <h4 class="text-white font-medium mb-1">Medication Info</h4>
                    <p class="text-gray-400 text-sm">Check drug interactions</p>
                </button>

                <button onclick="startVoiceInput()"
                        class="backdrop-blur-xl bg-white/5 rounded-2xl p-4 border border-white/10 hover:border-gold/30 transition-all duration-300 text-left group">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-microphone text-white"></i>
                    </div>
                    <h4 class="text-white font-medium mb-1">Voice Command</h4>
                    <p class="text-gray-400 text-sm">Speak your question</p>
                </button>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="backdrop-blur-xl bg-gradient-to-r from-gold/20 to-gold-deep/20 rounded-3xl p-8 border border-gold/30">
            <div class="text-center mb-8">
                <h3 class="text-3xl font-bold text-white mb-4">Why Choose Our AI Assistant?</h3>
                <p class="text-gray-300 max-w-2xl mx-auto">
                    Experience the future of healthcare with intelligent, personalized assistance
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-gold to-gold-deep rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-brain text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-2">Intelligent Analysis</h4>
                    <p class="text-gray-300 text-sm">
                        Advanced AI algorithms provide accurate symptom analysis and health insights
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-2">Privacy First</h4>
                    <p class="text-gray-300 text-sm">
                        Your health information is protected with bank-level security and encryption
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-2">24/7 Availability</h4>
                    <p class="text-gray-300 text-sm">
                        Get instant health guidance anytime, anywhere, without waiting
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-microphone text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-2">Voice Commands</h4>
                    <p class="text-gray-300 text-sm">
                        Speak naturally with voice recognition and text-to-speech responses
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="backdrop-blur-xl bg-white/10 rounded-3xl p-8 border border-white/20">
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 border-2 border-gold border-t-transparent rounded-full animate-spin"></div>
                <p class="text-white font-medium">AI is thinking...</p>
            </div>
        </div>
    </div>
</div>

<!-- Voice Settings Modal -->
<div id="voice-settings-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="backdrop-blur-xl bg-white/10 rounded-3xl p-8 border border-white/20 max-w-md w-full">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white">Voice Settings</h3>
                <button onclick="closeVoiceSettings()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Speech Recognition Settings -->
                <div>
                    <h4 class="text-white font-medium mb-3">Speech Recognition</h4>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" id="continuous-listening" class="mr-3">
                            <span class="text-gray-300">Continuous listening mode</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="auto-send" class="mr-3">
                            <span class="text-gray-300">Auto-send after speech</span>
                        </label>
                    </div>
                </div>

                <!-- Text-to-Speech Settings -->
                <div>
                    <h4 class="text-white font-medium mb-3">Text-to-Speech</h4>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" id="tts-enabled" class="mr-3">
                            <span class="text-gray-300">Enable TTS for AI responses</span>
                        </label>
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">Voice Speed</label>
                            <input type="range" id="tts-speed" min="0.5" max="2" step="0.1" value="1" class="w-full">
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>Slow</span>
                                <span>Normal</span>
                                <span>Fast</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">Voice Pitch</label>
                            <input type="range" id="tts-pitch" min="0.5" max="2" step="0.1" value="1" class="w-full">
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>Low</span>
                                <span>Normal</span>
                                <span>High</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Voice Test -->
                <div>
                    <h4 class="text-white font-medium mb-3">Test Voice</h4>
                    <div class="flex space-x-3">
                        <button id="test-tts" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                            Test TTS
                        </button>
                        <button id="test-speech" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                            Test Speech
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const chatMessages = document.getElementById('chat-messages');
    const loadingOverlay = document.getElementById('loading-overlay');
    const voiceBtn = document.getElementById('voice-btn');
    const voiceStatus = document.getElementById('voice-status');
    const voiceInstructions = document.getElementById('voice-instructions');
    const ttsToggle = document.getElementById('tts-toggle');
    const voiceSettingsBtn = document.getElementById('voice-settings-btn');

    // Voice settings
    let isListening = false;
    let isSubmitting = false;
    let ttsEnabled = false;
    let speechRecognition = null;
    let speechSynthesis = window.speechSynthesis;
    let currentUtterance = null;

    // Initialize voice capabilities
    initializeVoice();

    // Voice button event listener
    voiceBtn.addEventListener('click', toggleVoiceInput);

    // TTS toggle event listener
    ttsToggle.addEventListener('click', toggleTTS);

    // Voice settings button event listener
    voiceSettingsBtn.addEventListener('click', openVoiceSettings);

    // Load saved settings
    loadVoiceSettings();

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Prevent duplicate submissions
        if (isSubmitting) {
            console.log('Form submission already in progress');
            return;
        }

        const message = messageInput.value.trim();
        if (!message) return;

        // Set submitting flag
        isSubmitting = true;

        // Display user message
        addMessage(message, 'user');
        messageInput.value = '';

        // Show loading
        loadingOverlay.classList.remove('hidden');

        // Send to AI via Laravel proxy
        fetch('/api/ai/proxy/process', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ text: message })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            loadingOverlay.classList.add('hidden');
            isSubmitting = false; // Reset submitting flag

            if (data && data.success && !data.error) {
                // Use the pre-formatted response from the backend
                let formatted = data.response || "I'm here to help with your medical needs. How can I assist you today?";

                const intent = data.intent?.intent || 'general';

                                                // Add the AI response
                addMessage(formatted, 'ai', intent);

                // Add booking buttons if it's an appointment booking intent with doctors
                if (intent === 'book_appointment') {
                    const doctors = data.formatted_doctors || (data.doctors && data.doctors.doctors) || [];
                    if (doctors.length > 0) {
                        setTimeout(() => { addBookingButtons(doctors); }, 1000);
                    }
                }

                // Parse and add booking buttons from response text
                if (formatted.includes('[BOOKING_BUTTONS_START]')) {
                    const buttonData = parseBookingButtons(formatted);
                    if (buttonData.length > 0) {
                        setTimeout(() => { addBookingButtonsFromData(buttonData); }, 1000);
                    }
                }

                const suggestions = getSuggestions(intent);
                if (suggestions && suggestions.length > 0) {
                    setTimeout(() => { addSuggestions(suggestions); }, 1000);
                }
            } else {
                console.error('AI Error:', data.error || data.message || 'Unknown error');
                addMessage('Sorry, I encountered an error: ' + (data.error || data.message || 'Unknown error'), 'ai', 'error');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            loadingOverlay.classList.add('hidden');
            isSubmitting = false; // Reset submitting flag

            let errorMessage = 'Sorry, I encountered an error: ' + error.message;

            // Provide more specific error messages
            if (error.message.includes('404')) {
                errorMessage = 'AI service endpoint not found. Please check the server configuration.';
            } else if (error.message.includes('503')) {
                errorMessage = 'AI service is temporarily unavailable. Please try again in a moment.';
            } else if (error.message.includes('500')) {
                errorMessage = 'AI service encountered an internal error. Please try again.';
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage = 'Unable to connect to AI service. Please check your internet connection.';
            }

            addMessage(errorMessage, 'ai', 'error');
        });
    });

    function addMessage(message, sender, intent = null) {
        const div = document.createElement('div');
        div.className = 'flex items-start space-x-3';
        if (sender === 'user') {
            div.innerHTML = `
                <div class="flex-1"></div>
                <div class="backdrop-blur-xl bg-gradient-to-r from-gold/20 to-gold-deep/20 rounded-2xl p-4 border border-gold/30 max-w-3xl">
                    <p class="text-white">${escapeHtml(message)}</p>
                </div>
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-white text-sm"></i>
                </div>
            `;
        } else {
            let iconClass = 'fa-robot';
            let bgClass = 'bg-gradient-to-br from-gold to-gold-deep';
            switch (intent) {
                case 'appointment':
                case 'book_appointment':
                    iconClass = 'fa-calendar';
                    bgClass = 'bg-gradient-to-br from-green-500 to-green-600';
                    break;
                case 'symptom':
                case 'medical_inquiry':
                    iconClass = 'fa-stethoscope';
                    bgClass = 'bg-gradient-to-br from-red-500 to-red-600';
                    break;
                case 'medication':
                    iconClass = 'fa-pills';
                    bgClass = 'bg-gradient-to-br from-purple-500 to-purple-600';
                    break;
                case 'health_tips':
                    iconClass = 'fa-heart';
                    bgClass = 'bg-gradient-to-br from-pink-500 to-pink-600';
                    break;
                case 'error':
                    iconClass = 'fa-exclamation-triangle';
                    bgClass = 'bg-gradient-to-br from-red-500 to-red-600';
                    break;
                case 'listening':
                    iconClass = 'fa-microphone';
                    bgClass = 'bg-gradient-to-br from-blue-500 to-blue-600';
                    break;
                case 'processing':
                    iconClass = 'fa-cog';
                    bgClass = 'bg-gradient-to-br from-gray-500 to-gray-600';
                    break;
            }
            div.innerHTML = `
                <div class="w-8 h-8 ${bgClass} rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas ${iconClass} text-white text-sm"></i>
                </div>
                <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-4 border border-white/10 max-w-3xl">
                    <p class="text-white whitespace-pre-line">${escapeHtml(message)}</p>
                </div>
            `;
        }
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function addSuggestions(suggestions) {
        const sDiv = document.createElement('div');
        sDiv.className = 'flex items-start space-x-3';
        sDiv.innerHTML = `
            <div class="w-8 h-8 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-lightbulb text-white text-sm"></i>
            </div>
            <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-4 border border-white/10 max-w-3xl">
                <p class="text-white mb-3">You might also want to ask:</p>
                <div class="flex flex-wrap gap-2">
                    ${suggestions.map(s => `
                        <button onclick="sendQuickMessage('${s}')" class="bg-gradient-to-r from-gold/20 to-gold-deep/20 text-white px-3 py-1 rounded-lg text-sm hover:from-gold/30 hover:to-gold-deep/30 transition-all duration-300 border border-gold/30">
                            ${s}
                        </button>
                    `).join('')}
                </div>
            </div>
        `;
        chatMessages.appendChild(sDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function parseBookingButtons(responseText) {
        const buttonData = [];
        const startMarker = '[BOOKING_BUTTONS_START]';
        const endMarker = '[BOOKING_BUTTONS_END]';

        const startIndex = responseText.indexOf(startMarker);
        const endIndex = responseText.indexOf(endMarker);

        if (startIndex !== -1 && endIndex !== -1) {
            const buttonSection = responseText.substring(startIndex + startMarker.length, endIndex).trim();
            const lines = buttonSection.split('\n');

            lines.forEach(line => {
                if (line.startsWith('BUTTON:')) {
                    const parts = line.substring(7).split(':');
                    if (parts.length >= 4) {
                        buttonData.push({
                            id: parts[0],
                            name: parts[1],
                            fee: parts[2],
                            specialty: parts[3]
                        });
                    }
                }
            });
        }

        return buttonData;
    }

    function addBookingButtonsFromData(buttonData) {
        const div = document.createElement('div');
        div.className = 'flex items-start space-x-3 mt-4';

        const doctorButtons = buttonData.map((doctor, index) => {
            return `
                <div class="bg-white/10 rounded-lg p-3">
                    <h5 class="text-white font-medium mb-2">Dr. ${doctor.name}</h5>
                    <p class="text-green-200 text-sm mb-3">${doctor.specialty} • Consultation Fee: $${doctor.fee}</p>
                    <div class="flex space-x-2">
                        <button data-doctor-id="${doctor.id}" data-doctor-name="${doctor.name}" data-fee="${doctor.fee}" data-payment="wallet"
                                class="book-wallet-btn bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                            💳 Book with Wallet
                        </button>
                        <button data-doctor-id="${doctor.id}" data-doctor-name="${doctor.name}" data-fee="${doctor.fee}" data-payment="pay_on_site"
                                class="book-site-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                            💰 Pay on Site
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        div.innerHTML = `
            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar text-white text-sm"></i>
            </div>
            <div class="backdrop-blur-xl bg-gradient-to-r from-green-500/20 to-green-600/20 rounded-2xl p-4 border border-green-500/30 max-w-3xl">
                <h4 class="text-white font-semibold mb-3">📅 Book Appointment:</h4>
                <div class="space-y-3">
                    ${doctorButtons}
                </div>
            </div>
        `;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Add event listeners for booking buttons
        setTimeout(() => {
            const walletBtns = div.querySelectorAll('.book-wallet-btn');
            const siteBtns = div.querySelectorAll('.book-site-btn');

            walletBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const doctorId = this.getAttribute('data-doctor-id');
                    const doctorName = this.getAttribute('data-doctor-name');
                    const fee = this.getAttribute('data-fee');
                    window.confirmBooking(doctorId, doctorName, fee, 'wallet');
                });
            });

            siteBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const doctorId = this.getAttribute('data-doctor-id');
                    const doctorName = this.getAttribute('data-doctor-name');
                    const fee = this.getAttribute('data-fee');
                    window.confirmBooking(doctorId, doctorName, fee, 'pay_on_site');
                });
            });
        }, 100);
    }

    function addBookingButtons(doctors) {
        const div = document.createElement('div');
        div.className = 'flex items-start space-x-3 mt-4';

        // Handle both doctor data formats
        const doctorButtons = doctors.map((doctor, index) => {
            let doctorId, doctorName, consultationFee, displayName, displayInfo;

            if (doctor.id && doctor.name) {
                // Formatted doctor format
                doctorId = doctor.id;
                doctorName = doctor.name;
                consultationFee = doctor.consultation_fee;
                displayName = doctor.short_display || `Dr. ${doctor.name}`;
                displayInfo = doctor.display_text || `${doctor.specialty} • ${doctor.experience_years} years • ${doctor.rating}/5 ⭐`;
            } else if (doctor.doctor_id && doctor.doctor_name) {
                // Alternative format
                doctorId = doctor.doctor_id;
                doctorName = doctor.doctor_name;
                consultationFee = doctor.consultation_fee || '200.00';
                displayName = doctor.short_display || `Dr. ${doctor.doctor_name}`;
                displayInfo = doctor.display_text || `${doctor.specialty_name || 'General'} • ${doctor.experience_years || '5'} years • ${doctor.rating || '4.5'}/5 ⭐`;
            } else {
                // Fallback format
                doctorId = doctor.id || index + 1;
                doctorName = doctor.name || doctor.doctor_name || `Doctor ${index + 1}`;
                consultationFee = doctor.consultation_fee || '200.00';
                displayName = `Dr. ${doctorName}`;
                displayInfo = `${doctor.specialty || doctor.specialty_name || 'General'} • ${doctor.experience_years || '5'} years • ${doctor.rating || '4.5'}/5 ⭐`;
            }

            return `
                        <div class="bg-white/10 rounded-lg p-3">
                    <h5 class="text-white font-medium mb-2">${displayName}</h5>
                    <p class="text-green-200 text-sm mb-3">${displayInfo}</p>
                            <div class="flex space-x-2">
                        <button data-doctor-id="${doctorId}" data-doctor-name="${doctorName}" data-fee="${consultationFee}" data-payment="wallet"
                                        class="book-wallet-btn bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                                    💳 Book with Wallet
                                </button>
                        <button data-doctor-id="${doctorId}" data-doctor-name="${doctorName}" data-fee="${consultationFee}" data-payment="pay_on_site"
                                        class="book-site-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                                    💰 Pay on Site
                                </button>
                            </div>
                        </div>
            `;
        }).join('');

        div.innerHTML = `
            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar text-white text-sm"></i>
            </div>
            <div class="backdrop-blur-xl bg-gradient-to-r from-green-500/20 to-green-600/20 rounded-2xl p-4 border border-green-500/30 max-w-3xl">
                <h4 class="text-white font-semibold mb-3">📅 Book Appointment:</h4>
                <div class="space-y-3">
                    ${doctorButtons}
                </div>
            </div>
        `;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Add event listeners for booking buttons
        setTimeout(() => {
            const walletBtns = div.querySelectorAll('.book-wallet-btn');
            const siteBtns = div.querySelectorAll('.book-site-btn');

            walletBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const doctorId = this.getAttribute('data-doctor-id');
                    const doctorName = this.getAttribute('data-doctor-name');
                    const fee = this.getAttribute('data-fee');
                    window.confirmBooking(doctorId, doctorName, fee, 'wallet');
                });
            });

            siteBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const doctorId = this.getAttribute('data-doctor-id');
                    const doctorName = this.getAttribute('data-doctor-name');
                    const fee = this.getAttribute('data-fee');
                    window.confirmBooking(doctorId, doctorName, fee, 'pay_on_site');
                });
            });
        }, 100);
    }

    window.confirmBooking = function(doctorId, doctorName, consultationFee, paymentMethod = 'wallet') {
        // Show booking confirmation modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center';
        modal.innerHTML = `
            <div class="backdrop-blur-xl bg-white/10 rounded-3xl p-8 border border-white/20 max-w-md w-full mx-4">
                <h3 class="text-white text-xl font-bold mb-4">Confirm Booking</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-white mb-2"><strong>Doctor:</strong> ${doctorName}</p>
                        <p class="text-white mb-2"><strong>Fee:</strong> $${consultationFee}</p>
                        <p class="text-white mb-4"><strong>Payment:</strong> ${paymentMethod === 'wallet' ? 'Wallet Payment' : 'Pay on Site'}</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="window.processBooking(${doctorId}, ${consultationFee}, '${paymentMethod}')"
                                class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Confirm Booking
                        </button>
                        <button onclick="this.closest('.fixed').remove()"
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    };

    window.processBooking = function(doctorId, consultationFee, paymentMethod) {
        // Check if user is authenticated first
        fetch('/ai/user-profile', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Authentication required');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // User is authenticated, show booking form
                showBookingForm(doctorId, consultationFee, paymentMethod, data.user);
            } else {
                throw new Error('Authentication required');
            }
        })
        .catch(error => {
            // Show authentication required message
            const authMessage = `🔐 **Authentication Required**\n\nTo book an appointment, please:\n\n1. **Login** to your account, or\n2. **Register** if you don't have an account\n\nYou can login/register from the top navigation menu.\n\nOnce logged in, you can book appointments directly through the AI assistant!`;
            addMessage(authMessage, 'ai', 'book_appointment');
            window.showToast('Please login to book appointments', 'info');
        });
    };

    function showBookingForm(doctorId, consultationFee, paymentMethod, user) {
        // Show appointment details confirmation
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center';
        modal.innerHTML = `
            <div class="backdrop-blur-xl bg-white/10 rounded-3xl p-8 border border-white/20 max-w-md w-full mx-4">
                <h3 class="text-white text-xl font-bold mb-4">Confirm Appointment</h3>
                <form id="appointment-form" class="space-y-4">
                    <div class="bg-white/5 rounded-lg p-4 mb-4">
                        <p class="text-white text-sm mb-2"><strong>Booking for:</strong> ${user.first_name} ${user.last_name} (${user.email})</p>
                        <p class="text-white text-sm"><strong>Payment Method:</strong> ${paymentMethod === 'wallet' ? 'Wallet Payment' : 'Pay on Site'}</p>
                    </div>
                    <div>
                        <label class="block text-white text-sm font-medium mb-2">Preferred Date</label>
                        <input type="date" id="appointment-date" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-white text-sm font-medium mb-2">Preferred Time</label>
                        <select id="appointment-time" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-gold">
                            <option value="">Select time</option>
                            <option value="09:00:00">9:00 AM</option>
                            <option value="10:00:00">10:00 AM</option>
                            <option value="11:00:00">11:00 AM</option>
                            <option value="12:00:00">12:00 PM</option>
                            <option value="13:00:00">1:00 PM</option>
                            <option value="14:00:00">2:00 PM</option>
                            <option value="15:00:00">3:00 PM</option>
                            <option value="16:00:00">4:00 PM</option>
                        </select>
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Confirm Booking
                        </button>
                        <button type="button" onclick="this.closest('.fixed').remove()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        `;
        document.body.appendChild(modal);

        // Set default date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('appointment-date').value = tomorrow.toISOString().split('T')[0];

        // Handle form submission
        document.getElementById('appointment-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const appointmentDate = document.getElementById('appointment-date').value;
            const appointmentTime = document.getElementById('appointment-time').value;

            if (!appointmentDate || !appointmentTime) {
                window.showToast('Please select date and time', 'error');
                return;
            }

        // Process the booking
            fetch('/ai/book-appointment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                doctor_id: doctorId,
                appointment_date: appointmentDate,
                appointment_time: appointmentTime,
                consultation_fee: consultationFee,
                payment_method: paymentMethod
            })
        })
                .then(response => {
            if (response.status === 401) {
                throw new Error('Authentication required');
            }
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Remove modal
            document.querySelector('.fixed').remove();

            if (data.success) {
                // Show success message
                window.showToast(data.message, 'success');

                // Add success message to chat
                const successMessage = `✅ ${data.message}\n\n📋 **Booking Details:**\n• Appointment ID: #${data.appointment_id}\n• Doctor: ${data.booking_details.doctor_name}\n• Date: ${data.booking_details.appointment_date}\n• Time: ${data.booking_details.appointment_time}\n• Fee: $${data.booking_details.consultation_fee}\n• Payment Status: ${data.payment_status === 'paid' ? 'Paid' : 'Pending'}`;
                addMessage(successMessage, 'ai', 'book_appointment');
            } else {
                // Handle specific error cases
                let errorMessage = data.message;

                if (data.available_slots && data.available_slots.length > 0) {
                    errorMessage += '\n\n🕐 **Available Time Slots:**\n';
                    data.available_slots.forEach(slot => {
                        const time = slot.substring(0, 5); // Convert 09:00:00 to 09:00
                        errorMessage += `• ${time}\n`;
                    });
                }

                if (data.wallet_balance !== undefined) {
                    errorMessage += `\n💰 **Wallet Balance:** $${data.wallet_balance}\n`;
                    errorMessage += `💳 **Required Amount:** $${data.required_amount}`;
                }

                window.showToast(data.message, 'error');
                addMessage(errorMessage, 'ai', 'error');
            }
        })
        .catch(error => {
            document.querySelector('.fixed').remove();

            if (error.message === 'Authentication required' || error.status === 401) {
                const authMessage = `🔐 **Authentication Required**\n\nTo book an appointment, please:\n\n1. **Login** to your account, or\n2. **Register** if you don't have an account\n\nYou can login/register from the top navigation menu.\n\nOnce logged in, you can book appointments directly through the AI assistant!`;
                addMessage(authMessage, 'ai', 'book_appointment');
                window.showToast('Please login to book appointments', 'info');
            } else {
            window.showToast('Error processing booking', 'error');
            console.error('Booking error:', error);
            }
        });
        });
    };

    window.showToast = function(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg text-white font-medium transition-all duration-300 transform translate-x-full`;

        if (type === 'success') {
            toast.classList.add('bg-green-500');
        } else if (type === 'error') {
            toast.classList.add('bg-red-500');
        } else {
            toast.classList.add('bg-blue-500');
        }

        toast.textContent = message;
        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Animate out and remove
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    };

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getSuggestions(intent) {
        switch (intent) {
            case 'book_appointment':
                return [
                    'Book with a cardiologist',
                    'Book with a dermatologist',
                    'Book with a neurologist',
                    'Check available slots'
                ];
            case 'search_doctors':
                return [
                    'Find cardiologists',
                    'Find dermatologists',
                    'Find neurologists',
                    'Find pediatricians'
                ];
            case 'medical_inquiry':
                return [
                    'Describe my symptoms',
                    'Get medical advice',
                    'Check medication info',
                    'Find emergency care'
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

    window.sendQuickMessage = function(message) {
        messageInput.value = message;
        chatForm.dispatchEvent(new Event('submit'));
    };

    // Test AI connection on page load – via Laravel proxy
    function testAIConnection() {
        fetch('/api/ai/proxy/health')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'ok' || data.status === 'healthy' || data.data_loaded) {
                console.log('AI service is running:', data);
                // Update status indicator
                const statusElement = document.getElementById('ai-status');
                if (statusElement) {
                    statusElement.innerHTML = 'Online • AI Ready • Voice Enabled';
                    statusElement.className = 'text-green-300 text-sm';
                }
            } else {
                console.warn('AI service health check failed:', data);
                // Update status indicator
                const statusElement = document.getElementById('ai-status');
                if (statusElement) {
                    statusElement.innerHTML = 'AI Service Issues • Limited Functionality';
                    statusElement.className = 'text-yellow-300 text-sm';
                }
                // Show warning in chat
                addMessage('⚠️ AI service is experiencing issues. Some features may be limited.', 'ai', 'warning');
            }
        })
        .catch(error => {
            console.error('AI service connection error:', error);
            // Update status indicator
            const statusElement = document.getElementById('ai-status');
            if (statusElement) {
                statusElement.innerHTML = 'Offline • AI Service Unavailable';
                statusElement.className = 'text-red-300 text-sm';
            }
            // Show error in chat
            addMessage('⚠️ Unable to connect to AI service. Please check your connection and try again.', 'ai', 'error');
        });
    }

    // Initial connection test
    testAIConnection();

    // Retry connection every 30 seconds if failed
    setInterval(() => {
        const statusElement = document.getElementById('ai-status');
        if (statusElement && statusElement.innerHTML.includes('Offline')) {
            console.log('Retrying AI service connection...');
            testAIConnection();
        }
    }, 30000);

    // Voice Functions
    function initializeVoice() {
        // Check for speech recognition support
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            speechRecognition = new SpeechRecognition();
            speechRecognition.continuous = false;
            speechRecognition.interimResults = false;
            speechRecognition.lang = 'en-US';

            speechRecognition.onstart = function() {
                isListening = true;
                voiceBtn.classList.add('bg-red-500');
                voiceBtn.classList.remove('bg-blue-500', 'bg-blue-600');
                voiceStatus.classList.remove('hidden');
                voiceInstructions.classList.remove('hidden');
                voiceBtn.innerHTML = '<i class="fas fa-stop"></i>';
            };

            speechRecognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                messageInput.value = transcript;

                // Auto-send if enabled
                if (document.getElementById('auto-send') && document.getElementById('auto-send').checked) {
                    chatForm.dispatchEvent(new Event('submit'));
                }
            };

            speechRecognition.onerror = function(event) {
                console.error('Speech recognition error:', event.error);
                stopVoiceInput();
                showToast('Voice recognition error: ' + event.error, 'error');
            };

            speechRecognition.onend = function() {
                stopVoiceInput();
            };
        } else {
            voiceBtn.style.display = 'none';
            console.warn('Speech recognition not supported');
        }

        // Check for speech synthesis support
        if (!speechSynthesis) {
            ttsToggle.style.display = 'none';
            console.warn('Speech synthesis not supported');
        }
    }

    function toggleVoiceInput() {
        if (isListening) {
            stopVoiceInput();
        } else {
            startVoiceInput();
        }
    }

    function startVoiceInput() {
        if (speechRecognition) {
            try {
                speechRecognition.start();
            } catch (error) {
                console.error('Error starting speech recognition:', error);
                showToast('Error starting voice recognition', 'error');
            }
        } else {
            showToast('Voice recognition not supported', 'error');
        }
    }

    function stopVoiceInput() {
        if (speechRecognition) {
            speechRecognition.stop();
        }
        isListening = false;
        voiceBtn.classList.remove('bg-red-500');
        voiceBtn.classList.add('bg-gradient-to-r', 'from-blue-500', 'to-blue-600');
        voiceStatus.classList.add('hidden');
        voiceInstructions.classList.add('hidden');
        voiceBtn.innerHTML = '<i class="fas fa-microphone"></i>';
    }

    function toggleTTS() {
        ttsEnabled = !ttsEnabled;
        if (ttsEnabled) {
            ttsToggle.classList.add('bg-green-500');
            ttsToggle.classList.remove('bg-white/10');
            ttsToggle.innerHTML = '<i class="fas fa-volume-mute"></i>';
        } else {
            ttsToggle.classList.remove('bg-green-500');
            ttsToggle.classList.add('bg-white/10');
            ttsToggle.innerHTML = '<i class="fas fa-volume-up"></i>';
            // Stop current speech
            if (currentUtterance) {
                speechSynthesis.cancel();
            }
        }
        saveVoiceSettings();
    }

    function speakText(text) {
        if (!ttsEnabled || !speechSynthesis) return;

        // Stop any current speech
        if (currentUtterance) {
            speechSynthesis.cancel();
        }

        // Clean text for speech (remove markdown, etc.)
        const cleanText = text.replace(/\*\*(.*?)\*\*/g, '$1')
                             .replace(/\*(.*?)\*/g, '$1')
                             .replace(/\[(.*?)\]\(.*?\)/g, '$1')
                             .replace(/`(.*?)`/g, '$1')
                             .replace(/#{1,6}\s/g, '')
                             .replace(/\n/g, ' ');

        currentUtterance = new SpeechSynthesisUtterance(cleanText);

        // Apply settings
        const speed = document.getElementById('tts-speed') ? parseFloat(document.getElementById('tts-speed').value) : 1;
        const pitch = document.getElementById('tts-pitch') ? parseFloat(document.getElementById('tts-pitch').value) : 1;

        currentUtterance.rate = speed;
        currentUtterance.pitch = pitch;
        currentUtterance.volume = 0.8;

        // Get available voices and select a good one
        const voices = speechSynthesis.getVoices();
        const preferredVoice = voices.find(voice =>
            voice.lang.startsWith('en') &&
            (voice.name.includes('Google') || voice.name.includes('Natural') || voice.name.includes('Premium'))
        ) || voices.find(voice => voice.lang.startsWith('en')) || voices[0];

        if (preferredVoice) {
            currentUtterance.voice = preferredVoice;
        }

        speechSynthesis.speak(currentUtterance);
    }

    function openVoiceSettings() {
        document.getElementById('voice-settings-modal').classList.remove('hidden');
    }

    function closeVoiceSettings() {
        document.getElementById('voice-settings-modal').classList.add('hidden');
    }

    function loadVoiceSettings() {
        const settings = JSON.parse(localStorage.getItem('voiceSettings') || '{}');

        if (settings.ttsEnabled !== undefined) {
            ttsEnabled = settings.ttsEnabled;
            if (ttsEnabled) {
                ttsToggle.classList.add('bg-green-500');
                ttsToggle.classList.remove('bg-white/10');
                ttsToggle.innerHTML = '<i class="fas fa-volume-mute"></i>';
            }
        }

        if (settings.continuousListening !== undefined) {
            document.getElementById('continuous-listening').checked = settings.continuousListening;
        }

        if (settings.autoSend !== undefined) {
            document.getElementById('auto-send').checked = settings.autoSend;
        }

        if (settings.ttsSpeed !== undefined) {
            document.getElementById('tts-speed').value = settings.ttsSpeed;
        }

        if (settings.ttsPitch !== undefined) {
            document.getElementById('tts-pitch').value = settings.ttsPitch;
        }
    }

    function saveVoiceSettings() {
        const settings = {
            ttsEnabled: ttsEnabled,
            continuousListening: document.getElementById('continuous-listening').checked,
            autoSend: document.getElementById('auto-send').checked,
            ttsSpeed: parseFloat(document.getElementById('tts-speed').value),
            ttsPitch: parseFloat(document.getElementById('tts-pitch').value)
        };
        localStorage.setItem('voiceSettings', JSON.stringify(settings));
    }

    // Voice settings event listeners
    document.getElementById('continuous-listening').addEventListener('change', saveVoiceSettings);
    document.getElementById('auto-send').addEventListener('change', saveVoiceSettings);
    document.getElementById('tts-speed').addEventListener('input', saveVoiceSettings);
    document.getElementById('tts-pitch').addEventListener('input', saveVoiceSettings);

    // Test buttons
    document.getElementById('test-tts').addEventListener('click', function() {
        speakText('This is a test of the text-to-speech functionality. How does it sound?');
    });

    document.getElementById('test-speech').addEventListener('click', function() {
        if (!isListening) {
            startVoiceInput();
            setTimeout(() => {
                if (isListening) {
                    stopVoiceInput();
                }
            }, 5000);
        }
    });

    // Enhanced addMessage function to include TTS
    const originalAddMessage = addMessage;
    addMessage = function(message, sender, intent = null) {
        originalAddMessage(message, sender, intent);

        // Speak AI responses if TTS is enabled
        if (sender === 'ai' && ttsEnabled) {
            setTimeout(() => {
                speakText(message);
            }, 500);
        }
    };
});
</script>
@endpush
