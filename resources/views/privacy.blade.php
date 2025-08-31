@extends('layouts.app')

@section('title', 'Privacy Policy - Medical Booking System')

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
                    Privacy <span class="text-gold">Policy</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    Your privacy and data security are our top priorities. Learn how we collect, 
                    use, and protect your personal and health information.
                </p>
                <div class="mt-8 text-sm text-gray-400">
                    <p>Last updated: {{ now()->format('F j, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Content -->
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="backdrop-blur-xl bg-white/5 rounded-3xl p-12 border border-white/10" data-aos="fade-up">
            <!-- Introduction -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-6">Introduction</h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    MedBook ("we," "our," or "us") is committed to protecting your privacy and ensuring the security of your personal and health information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our medical booking platform and services.
                </p>
                <p class="text-gray-300 leading-relaxed">
                    By using our services, you agree to the collection and use of information in accordance with this policy. We are committed to maintaining the trust and confidence of our users and healthcare providers.
                </p>
            </div>

            <!-- Information We Collect -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-6">Information We Collect</h2>
                
                <h3 class="text-xl font-bold text-gold mb-4">Personal Information</h3>
                <p class="text-gray-300 leading-relaxed mb-4">
                    We collect personal information that you provide directly to us, including:
                </p>
                <ul class="text-gray-300 space-y-2 mb-6 ml-6">
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Name, email address, phone number, and date of birth</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Emergency contact information</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Insurance information and medical history</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Payment and billing information</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Profile pictures and preferences</span>
                    </li>
                </ul>

                <h3 class="text-xl font-bold text-gold mb-4">Health Information</h3>
                <p class="text-gray-300 leading-relaxed mb-4">
                    As a healthcare platform, we collect sensitive health information including:
                </p>
                <ul class="text-gray-300 space-y-2 mb-6 ml-6">
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Medical conditions, symptoms, and diagnoses</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Treatment plans and medication information</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Appointment history and medical records</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Communications with healthcare providers</span>
                    </li>
                </ul>

                <h3 class="text-xl font-bold text-gold mb-4">Automatically Collected Information</h3>
                <p class="text-gray-300 leading-relaxed mb-4">
                    We automatically collect certain information when you use our platform:
                </p>
                <ul class="text-gray-300 space-y-2 mb-6 ml-6">
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Device information and IP addresses</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Usage data and analytics</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Cookies and similar tracking technologies</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-gold mr-2 mt-1"></i>
                        <span>Location data (with your consent)</span>
                    </li>
                </ul>
            </div>

            <!-- How We Use Your Information -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-6">How We Use Your Information</h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    We use the information we collect for the following purposes:
                </p>
                <ul class="text-gray-300 space-y-3 mb-6 ml-6">
                    <li class="flex items-start">
                        <i class="fas fa-arrow-right text-gold mr-2 mt-1"></i>
                        <span><strong>Providing Healthcare Services:</strong> To facilitate appointments, connect you with healthcare providers, and manage your medical care</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-arrow-right text-gold mr-2 mt-1"></i>
                        <span><strong>Communication:</strong> To send appointment confirmations, reminders, and important updates about your care</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-arrow-right text-gold mr-2 mt-1"></i>
                        <span><strong>Payment Processing:</strong> To process payments, manage billing, and handle insurance claims</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-arrow-right text-gold mr-2 mt-1"></i>
                        <span><strong>AI Assistant:</strong> To provide personalized health insights and recommendations through our AI technology</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-arrow-right text-gold mr-2 mt-1"></i>
                        <span><strong>Platform Improvement:</strong> To enhance our services, develop new features, and improve user experience</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-arrow-right text-gold mr-2 mt-1"></i>
                        <span><strong>Legal Compliance:</strong> To comply with applicable laws, regulations, and healthcare standards</span>
                    </li>
                </ul>
            </div>

            <!-- Information Sharing -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-6">Information Sharing and Disclosure</h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    We do not sell, trade, or rent your personal information to third parties. We may share your information in the following circumstances:
                </p>
                
                <h3 class="text-xl font-bold text-gold mb-4">Healthcare Providers</h3>
                <p class="text-gray-300 leading-relaxed mb-4">
                    We share relevant health information with healthcare providers involved in your care to ensure proper treatment and coordination of services.
                </p>

                <h3 class="text-xl font-bold text-gold mb-4">Service Providers</h3>
                <p class="text-gray-300 leading-relaxed mb-4">
                    We may share information with trusted third-party service providers who assist us in operating our platform, such as payment processors, cloud storage providers, and analytics services.
                </p>

                <h3 class="text-xl font-bold text-gold mb-4">Legal Requirements</h3>
                <p class="text-gray-300 leading-relaxed mb-4">
                    We may disclose information when required by law, court order, or government request, or to protect our rights, property, or safety.
                </p>

                <h3 class="text-xl font-bold text-gold mb-4">Consent</h3>
                <p class="text-gray-300 leading-relaxed mb-4">
                    We may share information with your explicit consent for specific purposes not covered by this policy.
                </p>
            </div>

            <!-- Data Security -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-6">Data Security</h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    We implement comprehensive security measures to protect your information:
                </p>
                <ul class="text-gray-300 space-y-3 mb-6 ml-6">
                    <li class="flex items-start">
                        <i class="fas fa-shield-alt text-gold mr-2 mt-1"></i>
                        <span><strong>Encryption:</strong> All data is encrypted in transit and at rest using industry-standard protocols</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-shield-alt text-gold mr-2 mt-1"></i>
                        <span><strong>Access Controls:</strong> Strict access controls limit who can view your information</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-shield-alt text-gold mr-2 mt-1"></i>
                        <span><strong>Regular Audits:</strong> We conduct regular security audits and assessments</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-shield-alt text-gold mr-2 mt-1"></i>
                        <span><strong>HIPAA Compliance:</strong> We comply with HIPAA regulations and healthcare privacy standards</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-shield-alt text-gold mr-2 mt-1"></i>
                        <span><strong>Employee Training:</strong> All staff receive regular privacy and security training</span>
                    </li>
                </ul>
            </div>

            <!-- Your Rights -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-6">Your Privacy Rights</h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    You have the following rights regarding your personal information:
                </p>
                <ul class="text-gray-300 space-y-3 mb-6 ml-6">
                    <li class="flex items-start">
                        <i class="fas fa-eye text-gold mr-2 mt-1"></i>
                        <span><strong>Access:</strong> Request access to your personal information</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-edit text-gold mr-2 mt-1"></i>
                        <span><strong>Correction:</strong> Request correction of inaccurate information</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-download text-gold mr-2 mt-1"></i>
                        <span><strong>Portability:</strong> Request a copy of your data in a portable format</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-trash text-gold mr-2 mt-1"></i>
                        <span><strong>Deletion:</strong> Request deletion of your personal information (subject to legal requirements)</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-ban text-gold mr-2 mt-1"></i>
                        <span><strong>Restriction:</strong> Request restriction of processing in certain circumstances</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-times text-gold mr-2 mt-1"></i>
                        <span><strong>Objection:</strong> Object to processing of your information</span>
                    </li>
                </ul>
                <p class="text-gray-300 leading-relaxed">
                    To exercise these rights, please contact us using the information provided below.
                </p>
            </div>

            <!-- Cookies and Tracking -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-6">Cookies and Tracking Technologies</h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    We use cookies and similar technologies to enhance your experience:
                </p>
                <ul class="text-gray-300 space-y-2 mb-6 ml-6">
                    <li class="flex items-start">
                        <i class="fas fa-cookie-bite text-gold mr-2 mt-1"></i>
                        <span><strong>Essential Cookies:</strong> Required for basic platform functionality</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-cookie-bite text-gold mr-2 mt-1"></i>
                        <span><strong>Analytics Cookies:</strong> Help us understand how users interact with our platform</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-cookie-bite text-gold mr-2 mt-1"></i>
                        <span><strong>Preference Cookies:</strong> Remember your settings and preferences</span>
                    </li>
                </ul>
                <p class="text-gray-300 leading-relaxed">
                    You can control cookie settings through your browser preferences, though disabling certain cookies may affect platform functionality.
                </p>
            </div>

            <!-- Children's Privacy -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-6">Children's Privacy</h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    Our services are not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13. If you are a parent or guardian and believe your child has provided us with personal information, please contact us immediately.
                </p>
                <p class="text-gray-300 leading-relaxed">
                    For users between 13-18 years of age, we require parental consent for the collection and use of personal information.
                </p>
            </div>

            <!-- International Transfers -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-6">International Data Transfers</h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    Your information may be transferred to and processed in countries other than your own. We ensure that such transfers comply with applicable data protection laws and implement appropriate safeguards to protect your information.
                </p>
            </div>

            <!-- Changes to Policy -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-6">Changes to This Privacy Policy</h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    We may update this Privacy Policy from time to time. We will notify you of any material changes by:
                </p>
                <ul class="text-gray-300 space-y-2 mb-6 ml-6">
                    <li class="flex items-start">
                        <i class="fas fa-bell text-gold mr-2 mt-1"></i>
                        <span>Posting the updated policy on our platform</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-bell text-gold mr-2 mt-1"></i>
                        <span>Sending email notifications to registered users</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-bell text-gold mr-2 mt-1"></i>
                        <span>Updating the "Last updated" date at the top of this policy</span>
                    </li>
                </ul>
                <p class="text-gray-300 leading-relaxed">
                    Your continued use of our services after any changes constitutes acceptance of the updated policy.
                </p>
            </div>

            <!-- Contact Information -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-6">Contact Us</h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    If you have any questions about this Privacy Policy or our privacy practices, please contact us:
                </p>
                <div class="backdrop-blur-xl bg-white/5 rounded-2xl p-6 border border-white/10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-lg font-bold text-white mb-2">Email</h4>
                            <a href="mailto:privacy@medbook.com" class="text-gold hover:text-gold-deep transition-colors">
                                privacy@medbook.com
                            </a>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-white mb-2">Phone</h4>
                            <a href="tel:+1-800-MEDBOOK" class="text-gold hover:text-gold-deep transition-colors">
                                +1 (800) MEDBOOK
                            </a>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-white mb-2">Address</h4>
                            <p class="text-gray-300">
                                123 Healthcare Plaza<br>
                                Medical District<br>
                                New York, NY 10001<br>
                                United States
                            </p>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-white mb-2">Data Protection Officer</h4>
                            <a href="mailto:dpo@medbook.com" class="text-gold hover:text-gold-deep transition-colors">
                                dpo@medbook.com
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legal Basis -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-6">Legal Basis for Processing</h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    We process your personal information based on the following legal grounds:
                </p>
                <ul class="text-gray-300 space-y-2 mb-6 ml-6">
                    <li class="flex items-start">
                        <i class="fas fa-handshake text-gold mr-2 mt-1"></i>
                        <span><strong>Consent:</strong> When you explicitly agree to the processing of your information</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-handshake text-gold mr-2 mt-1"></i>
                        <span><strong>Contract:</strong> To fulfill our obligations under our terms of service</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-handshake text-gold mr-2 mt-1"></i>
                        <span><strong>Legal Obligation:</strong> To comply with applicable laws and regulations</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-handshake text-gold mr-2 mt-1"></i>
                        <span><strong>Legitimate Interest:</strong> To provide and improve our services</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-handshake text-gold mr-2 mt-1"></i>
                        <span><strong>Vital Interest:</strong> To protect your health and safety</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="backdrop-blur-xl bg-gradient-to-r from-gold/20 to-gold-deep/20 rounded-3xl p-12 border border-gold/30 text-center" data-aos="fade-up">
            <h2 class="text-4xl font-bold text-white mb-4">Questions About Privacy?</h2>
            <p class="text-gray-300 max-w-2xl mx-auto mb-8">
                We're committed to transparency and protecting your privacy. 
                Contact our privacy team if you have any questions or concerns.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-envelope mr-2"></i>
                    Contact Privacy Team
                </a>
                <a href="{{ route('terms') }}" class="btn btn-outline btn-lg">
                    <i class="fas fa-file-contract mr-2"></i>
                    View Terms of Service
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
