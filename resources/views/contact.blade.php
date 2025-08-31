@extends('layouts.app')

@section('title', 'Contact Us - Medical Booking System')

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
                    Contact <span class="text-gold">Us</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    We're here to help! Get in touch with our support team for any questions,
                    concerns, or assistance you need with your healthcare journey.
                </p>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <!-- Phone -->
            <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-phone text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Call Us</h3>
                <p class="text-gray-300 mb-4">Speak with our support team</p>
                <div class="space-y-2">
                    <a href="tel:+1-800-MEDBOOK" class="text-gold text-xl font-bold hover:text-gold-deep transition-colors">
                        +1 (800) MEDBOOK
                    </a>
                    <p class="text-gray-400 text-sm">24/7 Support Available</p>
                </div>
            </div>

            <!-- Email -->
            <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-envelope text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Email Us</h3>
                <p class="text-gray-300 mb-4">Send us a message</p>
                <div class="space-y-2">
                    <a href="mailto:support@medbook.com" class="text-gold text-xl font-bold hover:text-gold-deep transition-colors">
                        support@medbook.com
                    </a>
                    <p class="text-gray-400 text-sm">Response within 2 hours</p>
                </div>
            </div>

            <!-- Live Chat -->
            <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10 text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-comments text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Live Chat</h3>
                <p class="text-gray-300 mb-4">Chat with our AI assistant</p>
                <div class="space-y-2">
                    <button onclick="openChat()" class="text-gold text-xl font-bold hover:text-gold-deep transition-colors">
                        Start Chat
                    </button>
                    <p class="text-gray-400 text-sm">Instant responses</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Form & Map -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10" data-aos="fade-right">
                <h2 class="text-3xl font-bold text-white mb-6">Send us a Message</h2>
                <form id="contactForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">First Name</label>
                            <input type="text" name="first_name" required
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent text-white placeholder-gray-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Last Name</label>
                            <input type="text" name="last_name" required
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent text-white placeholder-gray-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Email Address</label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent text-white placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Phone Number</label>
                        <input type="tel" name="phone"
                               class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent text-white placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Subject</label>
                        <select name="subject" required
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent text-white">
                            <option value="">Select a subject</option>
                            <option value="general">General Inquiry</option>
                            <option value="technical">Technical Support</option>
                            <option value="billing">Billing & Payment</option>
                            <option value="appointment">Appointment Issues</option>
                            <option value="feedback">Feedback & Suggestions</option>
                            <option value="partnership">Partnership Opportunities</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Message</label>
                        <textarea name="message" rows="6" required
                                  class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent text-white placeholder-gray-400"
                                  placeholder="Tell us how we can help you..."></textarea>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="privacy" name="privacy" required
                               class="w-4 h-4 text-gold bg-white/10 border-white/20 rounded focus:ring-gold focus:ring-2">
                        <label for="privacy" class="ml-2 text-sm text-gray-300">
                            I agree to the <a href="#" class="text-gold hover:text-gold-deep">Privacy Policy</a> and
                            <a href="#" class="text-gold hover:text-gold-deep">Terms of Service</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-full">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Office Information -->
            <div class="space-y-8" data-aos="fade-left">
                <!-- Office Location -->
                <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10">
                    <h3 class="text-2xl font-bold text-white mb-6">Our Office</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-deep rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white mb-1">Headquarters</h4>
                                <p class="text-gray-300">
                                    123 Healthcare Plaza<br>
                                    Medical District<br>
                                    New York, NY 10001<br>
                                    United States
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white mb-1">Business Hours</h4>
                                <p class="text-gray-300">
                                    Monday - Friday: 9:00 AM - 6:00 PM<br>
                                    Saturday: 10:00 AM - 4:00 PM<br>
                                    Sunday: Closed<br>
                                    <span class="text-gold">24/7 Online Support Available</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="backdrop-blur-xl bg-gradient-to-r from-red-500/20 to-red-600/20 rounded-3xl p-8 border border-red-500/30">
                    <h3 class="text-2xl font-bold text-white mb-4">Emergency Contact</h3>
                    <p class="text-gray-300 mb-4">
                        For medical emergencies, please contact emergency services immediately.
                    </p>
                    <div class="space-y-2">
                        <a href="tel:911" class="flex items-center text-red-400 hover:text-red-300 transition-colors">
                            <i class="fas fa-phone mr-2"></i>
                            Emergency: 911
                        </a>
                        <a href="tel:+1-800-MEDBOOK" class="flex items-center text-red-400 hover:text-red-300 transition-colors">
                            <i class="fas fa-ambulance mr-2"></i>
                            MedBook Emergency: +1 (800) MEDBOOK
                        </a>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-8 border border-white/10">
                    <h3 class="text-2xl font-bold text-white mb-6">Follow Us</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="#" class="flex items-center space-x-3 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-colors">
                            <i class="fab fa-facebook text-blue-400 text-xl"></i>
                            <span class="text-white">Facebook</span>
                        </a>
                        <a href="#" class="flex items-center space-x-3 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-colors">
                            <i class="fab fa-twitter text-blue-400 text-xl"></i>
                            <span class="text-white">Twitter</span>
                        </a>
                        <a href="#" class="flex items-center space-x-3 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-colors">
                            <i class="fab fa-linkedin text-blue-400 text-xl"></i>
                            <span class="text-white">LinkedIn</span>
                        </a>
                        <a href="#" class="flex items-center space-x-3 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-colors">
                            <i class="fab fa-instagram text-pink-400 text-xl"></i>
                            <span class="text-white">Instagram</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-12 border border-white/10" data-aos="fade-up">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-white mb-4">Frequently Asked Questions</h2>
                <p class="text-gray-300 max-w-2xl mx-auto">
                    Quick answers to common questions about our services
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div class="border-b border-white/10 pb-4">
                        <h4 class="text-lg font-bold text-white mb-2">How do I book an appointment?</h4>
                        <p class="text-gray-300">
                            Simply browse our doctors, select your preferred specialist,
                            choose an available time slot, and complete your booking online.
                        </p>
                    </div>

                    <div class="border-b border-white/10 pb-4">
                        <h4 class="text-lg font-bold text-white mb-2">What if I need to cancel my appointment?</h4>
                        <p class="text-gray-300">
                            You can cancel or reschedule your appointment up to 24 hours
                            before the scheduled time through your account dashboard.
                        </p>
                    </div>

                    <div class="border-b border-white/10 pb-4">
                        <h4 class="text-lg font-bold text-white mb-2">Are virtual consultations available?</h4>
                        <p class="text-gray-300">
                            Yes! Many of our doctors offer telemedicine consultations.
                            Look for the video icon when browsing available appointments.
                        </p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="border-b border-white/10 pb-4">
                        <h4 class="text-lg font-bold text-white mb-2">How do I pay for my appointment?</h4>
                        <p class="text-gray-300">
                            We accept all major credit cards, debit cards, and digital wallets.
                            Payment is processed securely at the time of booking.
                        </p>
                    </div>

                    <div class="border-b border-white/10 pb-4">
                        <h4 class="text-lg font-bold text-white mb-2">Is my health information secure?</h4>
                        <p class="text-gray-300">
                            Absolutely. We use bank-level encryption and comply with all
                            healthcare privacy regulations to protect your data.
                        </p>
                    </div>

                    <div class="border-b border-white/10 pb-4">
                        <h4 class="text-lg font-bold text-white mb-2">Can I get a prescription online?</h4>
                        <p class="text-gray-300">
                            Yes, our doctors can prescribe medications when appropriate.
                            Prescriptions are sent directly to your preferred pharmacy.
                        </p>
                    </div>
                </div>
            </div>

                         <div class="text-center mt-8">
                 <a href="#" class="btn btn-outline">
                     <i class="fas fa-question-circle mr-2"></i>
                     View All FAQs
                 </a>
             </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="backdrop-blur-xl bg-gradient-to-r from-gold/20 to-gold-deep/20 rounded-3xl p-12 border border-gold/30 text-center" data-aos="fade-up">
            <h2 class="text-4xl font-bold text-white mb-4">Still Have Questions?</h2>
            <p class="text-gray-300 max-w-2xl mx-auto mb-8">
                Our support team is here to help you 24/7. Don't hesitate to reach out
                for any assistance you need with your healthcare journey.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="tel:+1-800-MEDBOOK" class="btn btn-primary btn-lg">
                    <i class="fas fa-phone mr-2"></i>
                    Call Now
                </a>
                <button onclick="openChat()" class="btn btn-outline btn-lg">
                    <i class="fas fa-comments mr-2"></i>
                    Start Live Chat
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="backdrop-blur-xl bg-white/10 rounded-3xl p-8 border border-white/20 max-w-md w-full text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-white text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">Message Sent!</h3>
            <p class="text-gray-300 mb-6">
                Thank you for contacting us. We'll get back to you within 2 hours.
            </p>
            <button onclick="closeSuccessModal()" class="btn btn-primary">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Contact form submission
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
        submitBtn.disabled = true;

                 fetch('/contact/submit', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessModal();
                this.reset();
            } else {
                showNotification(data.message || 'Error sending message', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error sending message', 'error');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Open chat
    function openChat() {
        // Redirect to AI assistant or open chat widget
                 window.location.href = '/ai';
    }

    // Show success modal
    function showSuccessModal() {
        document.getElementById('successModal').classList.remove('hidden');
    }

    // Close success modal
    function closeSuccessModal() {
        document.getElementById('successModal').classList.add('hidden');
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Simple notification - you can enhance this with a proper notification system
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(`${type.toUpperCase()}: ${message}`);
    }
</script>
@endpush
