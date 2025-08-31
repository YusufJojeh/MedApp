@extends('layouts.app')

@section('title', 'Frequently Asked Questions - Medical Booking System')

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

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center" data-aos="fade-up">
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6">
                    Frequently Asked <span class="text-gold">Questions</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    Find answers to common questions about our medical booking platform,
                    services, and how to get the most out of your healthcare experience.
                </p>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10" data-aos="fade-up">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-white mb-4">Search FAQs</h2>
                <p class="text-gray-300">Can't find what you're looking for? Search our knowledge base</p>
            </div>
            <div class="relative">
                <input type="text" id="faqSearch" placeholder="Search questions..."
                       class="w-full px-6 py-4 bg-white/10 border border-white/20 rounded-2xl focus:ring-2 focus:ring-gold focus:border-transparent text-white placeholder-gray-400 text-lg">
                <button class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gold transition-colors">
                    <i class="fas fa-search text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- FAQ Categories -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Getting Started -->
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 mb-12" data-aos="fade-up">
            <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-deep rounded-2xl flex items-center justify-center mr-4">
                    <i class="fas fa-rocket text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-white">Getting Started</h2>
            </div>

            <div class="space-y-6">
                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>How do I create an account?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Creating an account is simple! Click the "Sign Up" button in the top navigation, fill in your personal information, verify your email address, and you'll be ready to book appointments with our healthcare providers.</p>
                    </div>
                </div>

                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>What information do I need to provide?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>We require basic information including your full name, email address, phone number, date of birth, and emergency contact. For medical appointments, you may also need to provide insurance information and medical history.</p>
                    </div>
                </div>

                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>Is my personal information secure?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Absolutely! We use bank-level encryption and comply with HIPAA regulations to protect your health information. Your data is stored securely and never shared with unauthorized parties.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>Can I use the platform without creating an account?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>You can browse doctors and view their profiles without an account, but you'll need to create an account to book appointments, access your medical records, and use our full range of services.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Appointments -->
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 mb-12" data-aos="fade-up">
            <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mr-4">
                    <i class="fas fa-calendar-check text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-white">Booking Appointments</h2>
            </div>

            <div class="space-y-6">
                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>How do I book an appointment?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>To book an appointment, browse our doctors by specialty, select your preferred doctor, choose an available time slot, provide your symptoms or reason for visit, and complete the payment process. You'll receive a confirmation email with appointment details.</p>
                    </div>
                </div>

                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>Can I cancel or reschedule my appointment?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Yes, you can cancel or reschedule appointments up to 24 hours before the scheduled time. Go to your dashboard, find the appointment, and use the "Cancel" or "Reschedule" option. Late cancellations may incur fees.</p>
                    </div>
                </div>

                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>What types of appointments are available?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>We offer in-person consultations, virtual telemedicine appointments, follow-up visits, emergency consultations, and specialized care appointments across 25+ medical specialties including primary care, cardiology, dermatology, and more.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>How far in advance should I book?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>For routine appointments, we recommend booking 1-2 weeks in advance. For urgent care, same-day appointments are often available. Popular specialists may require booking 2-4 weeks ahead. You can check real-time availability when browsing doctors.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments & Billing -->
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 mb-12" data-aos="fade-up">
            <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mr-4">
                    <i class="fas fa-credit-card text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-white">Payments & Billing</h2>
            </div>

            <div class="space-y-6">
                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>What payment methods do you accept?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>We accept all major credit cards (Visa, MasterCard, American Express), debit cards, digital wallets (Apple Pay, Google Pay, PayPal), and bank transfers. All payments are processed securely through our encrypted payment system.</p>
                    </div>
                </div>

                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>Do you accept insurance?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Yes, we work with most major insurance providers. You can add your insurance information to your profile, and we'll verify coverage before your appointment. You'll only be responsible for copays, deductibles, and non-covered services.</p>
                    </div>
                </div>

                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>Can I get a refund if I cancel my appointment?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Full refunds are provided for cancellations made 24+ hours before the appointment. Cancellations within 24 hours may incur a cancellation fee. Emergency cancellations are handled on a case-by-case basis with proper documentation.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>How do I view my billing history?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>You can view your complete billing history in your account dashboard under the "Billing" section. This includes all past payments, invoices, and receipts. You can also download receipts for tax or reimbursement purposes.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Telemedicine -->
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 mb-12" data-aos="fade-up">
            <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mr-4">
                    <i class="fas fa-video text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-white">Telemedicine</h2>
            </div>

            <div class="space-y-6">
                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>How do virtual consultations work?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Virtual consultations are conducted through our secure video platform. At your appointment time, click the "Join Video Call" button in your dashboard. Ensure you have a stable internet connection, camera, and microphone. The doctor will join and begin your consultation.</p>
                    </div>
                </div>

                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>Can I get prescriptions through telemedicine?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Yes, doctors can prescribe medications during virtual consultations when appropriate. Prescriptions are sent electronically to your preferred pharmacy. Controlled substances and certain medications may require in-person visits.</p>
                    </div>
                </div>

                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>What if I have technical issues during a call?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>If you experience technical issues, try refreshing the page or checking your internet connection. Our support team is available 24/7 to help resolve technical problems. If the call cannot be completed, we'll reschedule at no additional cost.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>Are virtual consultations as effective as in-person visits?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Virtual consultations are highly effective for many types of care including follow-ups, consultations, mental health, and minor illnesses. However, some conditions require physical examination, and your doctor will recommend in-person visits when necessary.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Assistant -->
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 mb-12" data-aos="fade-up">
            <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center mr-4">
                    <i class="fas fa-robot text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-white">AI Health Assistant</h2>
            </div>

            <div class="space-y-6">
                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>What can the AI assistant help me with?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Our AI assistant can help with symptom analysis, medication information, health tips, appointment scheduling, finding doctors, answering general health questions, and providing preliminary guidance. It's available 24/7 for instant assistance.</p>
                    </div>
                </div>

                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>Is the AI assistant a replacement for doctors?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>No, the AI assistant is designed to complement, not replace, professional medical care. It provides information and guidance but always recommends consulting with healthcare professionals for diagnosis and treatment decisions.</p>
                    </div>
                </div>

                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>How accurate is the AI's health information?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Our AI is trained on extensive medical databases and regularly updated with the latest medical research. However, it's designed for informational purposes and should not be used as a substitute for professional medical advice.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>Can I access my chat history with the AI?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Yes, all your conversations with the AI assistant are saved in your account dashboard. You can review past conversations, export them, or share relevant information with your healthcare providers during appointments.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Privacy & Security -->
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10" data-aos="fade-up">
            <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mr-4">
                    <i class="fas fa-shield-alt text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-white">Privacy & Security</h2>
            </div>

            <div class="space-y-6">
                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>How do you protect my health information?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>We use enterprise-grade encryption, secure servers, and strict access controls to protect your data. We comply with HIPAA regulations and never share your information without explicit consent. All data transmission is encrypted using SSL/TLS protocols.</p>
                    </div>
                </div>

                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>Who has access to my medical records?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Only authorized healthcare providers involved in your care have access to your medical records. Our staff access is limited and logged for security. You control who can view your information and can revoke access at any time.</p>
                    </div>
                </div>

                <div class="faq-item border-b border-white/10 pb-6">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>Can I delete my account and data?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Yes, you can request account deletion at any time. We'll delete your personal information, but medical records may be retained as required by law. You can also export your data before deletion. Contact our support team to initiate this process.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question w-full text-left flex items-center justify-between text-lg font-semibold text-white hover:text-gold transition-colors" onclick="toggleFaq(this)">
                        <span>Are virtual consultations recorded?</span>
                        <i class="fas fa-chevron-down text-gold transition-transform"></i>
                    </button>
                    <div class="faq-answer mt-4 text-gray-300 hidden">
                        <p>Virtual consultations are not recorded by default. If recording is necessary for medical purposes, you'll be informed and asked for consent beforehand. Any recordings are stored securely and only accessible to authorized personnel.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Still Have Questions -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="backdrop-blur-xl bg-gradient-to-r from-gold/20 to-gold-deep/20 rounded-3xl p-12 border border-gold/30 text-center" data-aos="fade-up">
            <h2 class="text-4xl font-bold text-white mb-4">Still Have Questions?</h2>
            <p class="text-gray-300 max-w-2xl mx-auto mb-8">
                Can't find the answer you're looking for? Our support team is here to help
                you 24/7 with any questions or concerns.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-envelope mr-2"></i>
                    Contact Support
                </a>
                <a href="/ai" class="btn btn-outline btn-lg">
                    <i class="fas fa-robot mr-2"></i>
                    Chat with AI Assistant
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // FAQ search functionality
    document.getElementById('faqSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question span').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();

            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
                if (searchTerm) {
                    item.querySelector('.faq-answer').classList.remove('hidden');
                }
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Toggle FAQ answers
    function toggleFaq(button) {
        const answer = button.nextElementSibling;
        const icon = button.querySelector('i');

        if (answer.classList.contains('hidden')) {
            answer.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            answer.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }

    // Expand all FAQs
    function expandAll() {
        const answers = document.querySelectorAll('.faq-answer');
        const icons = document.querySelectorAll('.faq-question i');

        answers.forEach(answer => answer.classList.remove('hidden'));
        icons.forEach(icon => icon.style.transform = 'rotate(180deg)');
    }

    // Collapse all FAQs
    function collapseAll() {
        const answers = document.querySelectorAll('.faq-answer');
        const icons = document.querySelectorAll('.faq-question i');

        answers.forEach(answer => answer.classList.add('hidden'));
        icons.forEach(icon => icon.style.transform = 'rotate(0deg)');
    }
</script>
@endpush
