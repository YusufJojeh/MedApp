@extends('layouts.app')

@section('title', 'Book Appointment')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-text">Book Your Appointment</h1>
                <p class="text-muted mt-2">Schedule your visit with our healthcare professionals</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Booking Form -->
            <div class="lg:col-span-2">
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Appointment Details</h3>

                        <form method="POST" action="{{ route('appointments.store') }}" data-validate>
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="doctor_id" class="block text-sm font-medium text-text mb-2">Select Doctor</label>
                                    <select id="doctor_id" name="doctor_id" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                        <option value="">Choose a doctor</option>
                                        <option value="1">Dr. Sarah Johnson - Cardiology</option>
                                        <option value="2">Dr. Michael Wilson - Dermatology</option>
                                        <option value="3">Dr. Emily Brown - Neurology</option>
                                        <option value="4">Dr. David Chen - Orthopedics</option>
                                        <option value="5">Dr. Lisa Rodriguez - Pediatrics</option>
                                        <option value="6">Dr. Robert Taylor - Psychiatry</option>
                                    </select>
                                    @error('doctor_id')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="appointment_type" class="block text-sm font-medium text-text mb-2">Appointment Type</label>
                                    <select id="appointment_type" name="appointment_type" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                        <option value="">Select type</option>
                                        <option value="consultation">General Consultation</option>
                                        <option value="follow_up">Follow-up Visit</option>
                                        <option value="emergency">Emergency Visit</option>
                                        <option value="routine">Routine Checkup</option>
                                        <option value="specialist">Specialist Consultation</option>
                                    </select>
                                    @error('appointment_type')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="appointment_date" class="block text-sm font-medium text-text mb-2">Preferred Date</label>
                                    <input type="date" id="appointment_date" name="appointment_date" required
                                           min="{{ date('Y-m-d') }}"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                    @error('appointment_date')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="appointment_time" class="block text-sm font-medium text-text mb-2">Preferred Time</label>
                                    <select id="appointment_time" name="appointment_time" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                        <option value="">Select time</option>
                                        <option value="09:00">9:00 AM</option>
                                        <option value="10:00">10:00 AM</option>
                                        <option value="11:00">11:00 AM</option>
                                        <option value="12:00">12:00 PM</option>
                                        <option value="14:00">2:00 PM</option>
                                        <option value="15:00">3:00 PM</option>
                                        <option value="16:00">4:00 PM</option>
                                        <option value="17:00">5:00 PM</option>
                                    </select>
                                    @error('appointment_time')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="symptoms" class="block text-sm font-medium text-text mb-2">Symptoms or Reason for Visit</label>
                                <textarea id="symptoms" name="symptoms" rows="4" placeholder="Please describe your symptoms or reason for the appointment..."
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"></textarea>
                                @error('symptoms')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="notes" class="block text-sm font-medium text-text mb-2">Additional Notes</label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Any additional information you'd like to share..."
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"></textarea>
                                @error('notes')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between">
                                <button type="button" onclick="history.back()" class="btn btn-outline">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Back
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-calendar-check mr-2"></i>
                                    Book Appointment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Appointment Summary -->
            <div class="lg:col-span-1">
                <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Appointment Summary</h3>

                        <div class="space-y-4">
                            <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="w-10 h-10 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user-md text-white"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-text">Doctor</p>
                                    <p class="text-sm text-muted" id="selected-doctor">Not selected</p>
                                </div>
                            </div>

                            <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar text-white"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-text">Date & Time</p>
                                    <p class="text-sm text-muted" id="selected-datetime">Not selected</p>
                                </div>
                            </div>

                            <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-stethoscope text-white"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-text">Type</p>
                                    <p class="text-sm text-muted" id="selected-type">Not selected</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-gold/10 rounded-lg">
                            <h4 class="font-medium text-text mb-2">Important Information</h4>
                            <ul class="text-sm text-muted space-y-1">
                                <li>• Please arrive 15 minutes early</li>
                                <li>• Bring your ID and insurance card</li>
                                <li>• Wear comfortable clothing</li>
                                <li>• Cancel 24 hours in advance if needed</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Available Time Slots -->
                <div class="card feature-card mt-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-4">Available Time Slots</h3>

                        <div class="grid grid-cols-2 gap-2">
                            <button class="p-2 text-sm bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
                                9:00 AM
                            </button>
                            <button class="p-2 text-sm bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
                                10:00 AM
                            </button>
                            <button class="p-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg cursor-not-allowed">
                                11:00 AM
                            </button>
                            <button class="p-2 text-sm bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
                                2:00 PM
                            </button>
                            <button class="p-2 text-sm bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
                                3:00 PM
                            </button>
                            <button class="p-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg cursor-not-allowed">
                                4:00 PM
                            </button>
                        </div>

                        <div class="mt-4 text-xs text-muted">
                            <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-1"></span>
                            Available
                            <span class="inline-block w-3 h-3 bg-gray-400 rounded-full mr-1 ml-3"></span>
                            Booked
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
    // Update appointment summary
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const typeSelect = document.getElementById('appointment_type');

    function updateSummary() {
        const selectedDoctor = doctorSelect.options[doctorSelect.selectedIndex].text;
        const selectedDate = dateInput.value;
        const selectedTime = timeSelect.options[timeSelect.selectedIndex].text;
        const selectedType = typeSelect.options[typeSelect.selectedIndex].text;

        document.getElementById('selected-doctor').textContent = selectedDoctor || 'Not selected';
        document.getElementById('selected-datetime').textContent = selectedDate && selectedTime ? `${selectedDate} at ${selectedTime}` : 'Not selected';
        document.getElementById('selected-type').textContent = selectedType || 'Not selected';
    }

    doctorSelect.addEventListener('change', updateSummary);
    dateInput.addEventListener('change', updateSummary);
    timeSelect.addEventListener('change', updateSummary);
    typeSelect.addEventListener('change', updateSummary);

    // Time slot selection
    document.querySelectorAll('.grid button:not(.cursor-not-allowed)').forEach(button => {
        button.addEventListener('click', function() {
            const time = this.textContent;
            timeSelect.value = time === '9:00 AM' ? '09:00' :
                              time === '10:00 AM' ? '10:00' :
                              time === '2:00 PM' ? '14:00' :
                              time === '3:00 PM' ? '15:00' : '';
            updateSummary();

            // Visual feedback
            document.querySelectorAll('.grid button').forEach(btn => {
                btn.classList.remove('ring-2', 'ring-gold');
            });
            this.classList.add('ring-2', 'ring-gold');
        });
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = ['doctor_id', 'appointment_type', 'appointment_date', 'appointment_time'];
        let isValid = true;

        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field.value) {
                field.classList.add('border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            e.preventDefault();
            showNotification('Please fill in all required fields', 'error');
        }
    });
</script>
@endpush
