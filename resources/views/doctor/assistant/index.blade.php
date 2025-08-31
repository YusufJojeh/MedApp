@extends('layouts.app')

@section('title', 'AI Assistant - Doctor Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">AI Assistant</h1>
                    <p class="text-muted mt-2">Your intelligent medical practice companion</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                        <span class="text-sm text-green-600 font-medium">AI Online</span>
                    </div>
                    <button class="btn btn-outline" onclick="window.history.back()">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Chat Interface -->
            <div class="lg:col-span-3">
                <div class="card feature-card h-[600px] flex flex-col" data-aos="fade-up">
                    <!-- Chat Header -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-robot text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-text">Medical AI Assistant</h3>
                                    <p class="text-sm text-muted">Ready to help with your practice</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="btn btn-outline btn-sm" onclick="clearChat()">
                                    <i class="fas fa-trash mr-1"></i>
                                    Clear
                                </button>
                                <button class="btn btn-outline btn-sm" onclick="checkNLPHealth()">
                                    <i class="fas fa-heartbeat mr-1"></i>
                                    Health
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div class="flex-1 overflow-y-auto p-6" id="chatMessages">
                        <!-- Welcome Message -->
                        <div class="flex items-start mb-6">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-robot text-white text-sm"></i>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 max-w-3xl">
                                <p class="text-text">
                                    Hello! I'm your AI medical assistant. I can help you with:
                                </p>
                                <ul class="mt-2 text-sm text-muted space-y-1">
                                    <li>• Scheduling appointments and managing your calendar</li>
                                    <li>• Patient information and medical records</li>
                                    <li>• Medical guidelines and treatment recommendations</li>
                                    <li>• Prescription writing assistance</li>
                                    <li>• Billing and payment questions</li>
                                    <li>• Practice management advice</li>
                                </ul>
                                <p class="mt-3 text-text">
                                    How can I assist you today?
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Input -->
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <textarea id="messageInput" rows="2"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text resize-none"
                                          placeholder="Type your message here..."></textarea>
                            </div>
                            <button class="btn btn-primary self-end" onclick="sendMessage()">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Actions -->
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Quick Actions</h3>
                        <div class="space-y-3" id="quickActions">
                            <!-- Quick actions will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Today's Appointments -->
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Today's Appointments</h3>
                        <div class="space-y-3">
                            @forelse($todayAppointments as $appointment)
                                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="w-8 h-8 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-text">{{ $appointment->patient_name }}</p>
                                        <p class="text-xs text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-muted text-center py-4">No appointments today</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Assistant Stats -->
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Assistant Stats</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Total Conversations</span>
                                <span class="text-text font-medium">{{ $stats['total_conversations'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Active Conversations</span>
                                <span class="text-text font-medium">{{ $stats['active_conversations'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-muted">This Month</span>
                                <span class="text-text font-medium">{{ $stats['this_month_conversations'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Total Messages</span>
                                <span class="text-text font-medium">{{ $stats['total_messages'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentConversationId = null;
    let quickActions = [];

    // Initialize the assistant
    document.addEventListener('DOMContentLoaded', function() {
        loadQuickActions();
        setupEventListeners();
    });

    // Setup event listeners
    function setupEventListeners() {
        const messageInput = document.getElementById('messageInput');

        // Send message on Enter (but allow Shift+Enter for new lines)
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    // Load quick actions
    function loadQuickActions() {
        fetch('{{ route("doctor.assistant.quick-actions") }}')
            .then(response => response.json())
            .then(data => {
                quickActions = data;
                renderQuickActions();
            })
            .catch(error => {
                console.error('Error loading quick actions:', error);
            });
    }

    // Render quick actions
    function renderQuickActions() {
        const container = document.getElementById('quickActions');
        container.innerHTML = '';

        quickActions.forEach(action => {
            const actionElement = document.createElement('div');
            actionElement.className = 'flex items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors';
            actionElement.onclick = () => executeQuickAction(action.id);

            actionElement.innerHTML = `
                <div class="w-8 h-8 bg-gradient-to-br from-${action.color}-500 to-${action.color}-600 rounded-full flex items-center justify-center mr-3">
                    <i class="${action.icon} text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-text">${action.title}</p>
                    <p class="text-xs text-muted">${action.description}</p>
                </div>
            `;

            container.appendChild(actionElement);
        });
    }

    // Execute quick action
    function executeQuickAction(actionId) {
        fetch('{{ route("doctor.assistant.execute-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                action: actionId,
                parameters: {}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addMessage('assistant', data.message);
            } else {
                showNotification('Error executing action', 'error');
            }
        })
        .catch(error => {
            console.error('Error executing quick action:', error);
            showNotification('Error executing action', 'error');
        });
    }

    // Send message
    function sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();

        if (!message) return;

        // Add user message to chat
        addMessage('user', message);
        messageInput.value = '';

        // If no current conversation, start a new one
        if (!currentConversationId) {
            startConversationWithMessage(message);
        } else {
            // Send message to existing conversation
            sendMessageToConversation(message);
        }
    }

    // Start conversation with initial message
    function startConversationWithMessage(message) {
        fetch('{{ route("doctor.assistant.start-conversation") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                topic: 'General Consultation',
                initial_message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentConversationId = data.conversation_id;
                showNotification('Conversation started', 'success');
            } else {
                showNotification('Error starting conversation', 'error');
            }
        })
        .catch(error => {
            console.error('Error starting conversation:', error);
            showNotification('Error starting conversation', 'error');
        });
    }

    // Send message to existing conversation
    function sendMessageToConversation(message) {
        fetch('{{ route("doctor.assistant.send-message") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                conversation_id: currentConversationId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Message sent', 'success');
            } else {
                showNotification('Error sending message', 'error');
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            showNotification('Error sending message', 'error');
        });
    }

    // Add message to chat
    function addMessage(sender, message) {
        const chatMessages = document.getElementById('chatMessages');
        const messageElement = document.createElement('div');
        messageElement.className = 'flex items-start mb-6';

        if (sender === 'user') {
            messageElement.innerHTML = `
                <div class="flex-1"></div>
                <div class="bg-gold/10 rounded-lg p-4 max-w-3xl">
                    <p class="text-text">${message}</p>
                </div>
                <div class="w-8 h-8 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center ml-3 flex-shrink-0">
                    <i class="fas fa-user text-white text-sm"></i>
                </div>
            `;
        } else {
            messageElement.innerHTML = `
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                    <i class="fas fa-robot text-white text-sm"></i>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 max-w-3xl">
                    <p class="text-text">${message}</p>
                </div>
            `;
        }

        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Clear chat
    function clearChat() {
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.innerHTML = '';
        currentConversationId = null;

        // Add welcome message back
        addMessage('assistant', 'Hello! I\'m your AI medical assistant. How can I help you today?');
    }

    // Check NLP health
    function checkNLPHealth() {
        fetch('{{ route("doctor.assistant.nlp-health") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('NLP service is running', 'success');
                } else {
                    showNotification('NLP service is not available', 'error');
                }
            })
            .catch(error => {
                console.error('Error checking NLP health:', error);
                showNotification('Error checking NLP health', 'error');
            });
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Simple notification - you can enhance this with a proper notification system
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(`${type.toUpperCase()}: ${message}`);
    }
</script>
@endpush
