@extends('layouts.app')

@section('title')
    @if($rescheduleAppointment)
        Reschedule Appointment - Patient Dashboard
    @elseif($followupAppointment)
        Book Follow-up Appointment - Patient Dashboard
    @else
        Book Appointment - Patient Dashboard
    @endif
@endsection

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">
                        @if($rescheduleAppointment)
                            Reschedule Appointment
                        @elseif($followupAppointment)
                            Book Follow-up Appointment
                        @else
                            Book Appointment
                        @endif
                    </h1>
                    <p class="text-muted mt-2">
                        @if($rescheduleAppointment)
                            Change the date of your existing appointment
                        @elseif($followupAppointment)
                            Schedule a follow-up appointment with your doctor
                        @else
                            Schedule your healthcare appointment
                        @endif
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="btn btn-outline" onclick="window.location.href='{{ route('patient.appointments.index') }}'">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Appointments
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Booking Form -->
            <div class="lg:col-span-2">
                <div class="card feature-card" data-aos="fade-up">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Appointment Details</h3>

                        @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 mr-2"></i>
                                <span class="text-red-800 dark:text-red-200">{{ session('error') }}</span>
                            </div>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400 mr-2"></i>
                                <span class="text-green-800 dark:text-green-200">{{ session('success') }}</span>
                            </div>
                        </div>
                        @endif

                        @if($rescheduleAppointment)
                        <!-- Reschedule Info -->
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-calendar-alt text-blue-600 dark:text-blue-400 mr-2"></i>
                                <h4 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Rescheduling Appointment</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-muted">Current Doctor:</span>
                                    <span class="text-text font-medium">{{ $rescheduleAppointment->doctor_name }}</span>
                                </div>
                                <div>
                                    <span class="text-muted">Specialty:</span>
                                    <span class="text-text font-medium">{{ $rescheduleAppointment->specialty_name }}</span>
                                </div>
                                <div>
                                    <span class="text-muted">Current Date:</span>
                                    <span class="text-text font-medium">{{ \Carbon\Carbon::parse($rescheduleAppointment->appointment_date)->format('l, F j, Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-muted">Current Time:</span>
                                    <span class="text-text font-medium">{{ \Carbon\Carbon::parse($rescheduleAppointment->appointment_time)->format('g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($followupAppointment)
                        <!-- Follow-up Info -->
                        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-plus-circle text-green-600 dark:text-green-400 mr-2"></i>
                                <h4 class="text-lg font-semibold text-green-800 dark:text-green-200">Booking Follow-up Appointment</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-muted">Previous Doctor:</span>
                                    <span class="text-text font-medium">{{ $followupAppointment->doctor_name }}</span>
                                </div>
                                <div>
                                    <span class="text-muted">Specialty:</span>
                                    <span class="text-text font-medium">{{ $followupAppointment->specialty_name }}</span>
                                </div>
                                <div>
                                    <span class="text-muted">Previous Date:</span>
                                    <span class="text-text font-medium">{{ \Carbon\Carbon::parse($followupAppointment->appointment_date)->format('l, F j, Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-muted">Previous Time:</span>
                                    <span class="text-text font-medium">{{ \Carbon\Carbon::parse($followupAppointment->appointment_time)->format('g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <form id="bookingForm" class="space-y-6">
                            @csrf
                            @if($rescheduleAppointment)
                                <input type="hidden" name="reschedule_appointment_id" value="{{ $rescheduleAppointment->id }}">
                            @endif
                            @if($followupAppointment)
                                <input type="hidden" name="followup_appointment_id" value="{{ $followupAppointment->id }}">
                            @endif

                            @if($rescheduleAppointment)
                                <!-- For Reschedule: Show current doctor info (read-only) -->
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">Current Doctor</label>
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-300 dark:border-gray-600">
                                        <div class="flex items-center mb-3">
                                            <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user-md text-white"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-text">{{ $rescheduleAppointment->doctor_name }}</p>
                                                <p class="text-sm text-muted">{{ $rescheduleAppointment->specialty_name }}</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-muted">Specialty:</span>
                                                <span class="text-text">{{ $rescheduleAppointment->specialty_name }}</span>
                                            </div>
                                            <div>
                                                <span class="text-muted">Current Date:</span>
                                                <span class="text-text">{{ \Carbon\Carbon::parse($rescheduleAppointment->appointment_date)->format('l, F j, Y') }}</span>
                                            </div>
                                            <div>
                                                <span class="text-muted">Current Time:</span>
                                                <span class="text-text">{{ \Carbon\Carbon::parse($rescheduleAppointment->appointment_time)->format('g:i A') }}</span>
                                            </div>
                                            <div>
                                                <span class="text-muted">Status:</span>
                                                <span class="text-text capitalize">{{ $rescheduleAppointment->STATUS }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Hidden inputs to preserve doctor and specialty -->
                                    <input type="hidden" name="specialty_id" value="{{ $rescheduleAppointment->specialty_id }}">
                                    <input type="hidden" name="doctor_id" value="{{ $rescheduleAppointment->doctor_id }}">
                                </div>
                            @else
                                <!-- For new appointments: Allow specialty and doctor selection -->
                                <!-- Specialty Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">Medical Specialty</label>
                                    <select id="specialtySelect" name="specialty_id" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                        <option value="">Select a specialty</option>
                                        @foreach($specialties as $specialty)
                                            <option value="{{ $specialty->id }}"
                                                @if($followupAppointment && $followupAppointment->specialty_id == $specialty->id) selected @endif>
                                                {{ $specialty->name_en ?? $specialty->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Doctor Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">Select Doctor</label>
                                    <select id="doctorSelect" name="doctor_id" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                        <option value="">Select a doctor</option>
                                    </select>
                                    <div id="doctorInfo" class="mt-3 hidden">
                                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                            <div class="flex items-center mb-3">
                                                <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-user-md text-white"></i>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-text" id="doctorName"></p>
                                                    <p class="text-sm text-muted" id="doctorSpecialty"></p>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <span class="text-muted">Experience:</span>
                                                    <span class="text-text" id="doctorExperience"></span>
                                                </div>
                                                <div>
                                                    <span class="text-muted">Rating:</span>
                                                    <span class="text-text" id="doctorRating"></span>
                                                </div>
                                                <div>
                                                    <span class="text-muted">Consultation Fee:</span>
                                                    <span class="text-text" id="doctorFee"></span>
                                                </div>
                                                <div>
                                                    <span class="text-muted">Available:</span>
                                                    <span class="text-text" id="doctorAvailability"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Appointment Type -->
                            <div>
                                <label class="block text-sm font-medium text-text mb-2">Appointment Type</label>
                                <select name="appointment_type" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                                    <option value="">Select appointment type</option>
                                    <option value="consultation" @if($appointmentType == 'consultation') selected @endif>General Consultation</option>
                                    <option value="follow_up" @if($appointmentType == 'follow_up') selected @endif>Follow-up Visit</option>
                                    <option value="emergency" @if($appointmentType == 'emergency') selected @endif>Emergency Visit</option>
                                    <option value="routine_checkup" @if($appointmentType == 'routine_checkup') selected @endif>Routine Checkup</option>
                                    <option value="specialist_consultation" @if($appointmentType == 'specialist_consultation') selected @endif>Specialist Consultation</option>
                                </select>
                            </div>

                            <!-- Date Selection -->
                            <div>
                                <label class="block text-sm font-medium text-text mb-2">Preferred Date</label>
                                <input type="date" name="appointment_date" id="appointmentDate" required
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            </div>

                            @if($rescheduleAppointment)
                                <!-- For Reschedule: Show time slot availability -->
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">Time Slot Availability</label>
                                    <div id="timeSlotAvailability" class="space-y-3">
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm text-muted">Original Time:</p>
                                                    <p class="font-medium text-text">{{ \Carbon\Carbon::parse($rescheduleAppointment->appointment_time)->format('g:i A') }}</p>
                                                </div>
                                                <div id="timeSlotStatus" class="text-center">
                                                    <p class="text-sm text-muted">Select a date to check availability</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="alternativeSlots" class="hidden">
                                            <p class="text-sm text-muted mb-2">Alternative times available:</p>
                                            <div class="grid grid-cols-3 gap-2" id="alternativeSlotsGrid"></div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- For new appointments: Show all time slots -->
                                <div>
                                    <label class="block text-sm font-medium text-text mb-2">Available Time Slots</label>
                                    <div id="timeSlots" class="grid grid-cols-3 gap-3">
                                        <p class="text-muted col-span-3 text-center py-4">Select a doctor and date to see available time slots</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Symptoms/Notes -->
                            <div>
                                <label class="block text-sm font-medium text-text mb-2">Symptoms or Notes</label>
                                <textarea name="notes" rows="4"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text"
                                          placeholder="Describe your symptoms or any specific concerns...">{{ $rescheduleAppointment ? $rescheduleAppointment->notes : '' }}</textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex space-x-4">
                                <button type="button" class="btn btn-outline flex-1" onclick="window.location.href='{{ route('patient.appointments.index') }}'">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary flex-1" id="submitBtn">
                                    <i class="fas fa-calendar-check mr-2"></i>
                                    @if($rescheduleAppointment)
                                        Reschedule Appointment
                                    @else
                                        Book Appointment
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Appointment Summary -->
            <div class="lg:col-span-1">
                <div class="card feature-card sticky top-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-text mb-6">Appointment Summary</h3>

                        <div id="appointmentSummary" class="space-y-4">
                            <div class="text-center py-8 text-muted">
                                <i class="fas fa-calendar-plus text-4xl mb-4"></i>
                                <p>Fill in the form to see appointment details</p>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-8 space-y-3">
                            <button class="btn btn-outline w-full" onclick="window.location.href='{{ route('patient.doctors.index') }}'">
                                <i class="fas fa-search mr-2"></i>
                                Browse All Doctors
                            </button>
                            <button class="btn btn-outline w-full" onclick="window.location.href='{{ route('patient.appointments.index') }}'">
                                <i class="fas fa-calendar mr-2"></i>
                                View My Appointments
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold mx-auto mb-4"></div>
            <p class="text-text">Processing your appointment...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize form
    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();
        updateSummary();

        // Handle reschedule case
        @if($rescheduleAppointment)
            const rescheduleAppointment = @json($rescheduleAppointment);
            if (rescheduleAppointment) {
                // For reschedule, pre-load time slots for the current doctor
                const appointmentDate = document.getElementById('appointmentDate');
                if (appointmentDate) {
                    // Set minimum date to tomorrow
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    appointmentDate.min = tomorrow.toISOString().split('T')[0];
                }
            }
        @endif
    });

    // Setup event listeners
    function setupEventListeners() {
        // Only add specialty and doctor listeners if not rescheduling
        @if(!$rescheduleAppointment)
            const specialtySelect = document.getElementById('specialtySelect');
            if (specialtySelect) {
                specialtySelect.addEventListener('change', loadDoctors);
            }

            const doctorSelect = document.getElementById('doctorSelect');
            if (doctorSelect) {
                doctorSelect.addEventListener('change', loadDoctorInfo);
            }
        @endif

        // Date change listener - handle both reschedule and new appointment cases
        const appointmentDate = document.getElementById('appointmentDate');
        if (appointmentDate) {
            appointmentDate.addEventListener('change', function() {
                @if($rescheduleAppointment)
                    // For reschedule, check time slot availability
                    checkTimeSlotAvailability({{ $rescheduleAppointment->doctor_id }});
                @else
                    // For new appointments, use selected doctor
                    loadTimeSlots();
                @endif
            });
        }

        // Form submission
        document.getElementById('bookingForm').addEventListener('submit', submitBooking);
    }

    // Load doctors based on specialty
    function loadDoctors() {
        const specialtyId = document.getElementById('specialtySelect').value;
        const doctorSelect = document.getElementById('doctorSelect');

        if (!specialtyId) {
            doctorSelect.innerHTML = '<option value="">Select a doctor</option>';
            document.getElementById('doctorInfo').classList.add('hidden');
            return;
        }

        fetch(`{{ route('patient.appointments.available-doctors') }}?specialty_id=${specialtyId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                doctorSelect.innerHTML = '<option value="">Select a doctor</option>';

                data.doctors.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = `Dr. ${doctor.name} - ${doctor.specialty.name}`;
                    doctorSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading doctors:', error);
                showNotification('Error loading doctors', 'error');
            });
    }

    // Load doctor information
    function loadDoctorInfo() {
        const doctorId = document.getElementById('doctorSelect').value;

        if (!doctorId) {
            document.getElementById('doctorInfo').classList.add('hidden');
            updateSummary();
            return;
        }

        fetch(`{{ route('patient.doctors.index') }}/${doctorId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const doctor = data.doctor;

                    document.getElementById('doctorName').textContent = `Dr. ${doctor.name}`;
                    document.getElementById('doctorSpecialty').textContent = doctor.specialty.name;
                    document.getElementById('doctorExperience').textContent = `${doctor.experience_years} years`;
                    document.getElementById('doctorRating').textContent = `${doctor.average_rating}/5 ⭐`;
                    document.getElementById('doctorFee').textContent = `$${parseFloat(doctor.consultation_fee).toFixed(2)}`;
                    document.getElementById('doctorAvailability').textContent = doctor.is_available ? 'Available' : 'Not Available';

                    document.getElementById('doctorInfo').classList.remove('hidden');
                    updateSummary();
                }
            })
            .catch(error => {
                console.error('Error loading doctor info:', error);
                showNotification('Error loading doctor information', 'error');
            });
    }

    // Check time slot availability for reschedule
    function checkTimeSlotAvailability(doctorId) {
        const date = document.getElementById('appointmentDate').value;
        const originalTime = '{{ $rescheduleAppointment ? \Carbon\Carbon::parse($rescheduleAppointment->appointment_time)->format("H:i:s") : "" }}';

        if (!date) {
            document.getElementById('timeSlotStatus').innerHTML = '<p class="text-sm text-muted">Select a date to check availability</p>';
            document.getElementById('alternativeSlots').classList.add('hidden');
            return;
        }

        fetch(`{{ route('patient.appointments.create') }}?doctor_id=${doctorId}&date=${date}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const timeSlotStatus = document.getElementById('timeSlotStatus');
                const alternativeSlots = document.getElementById('alternativeSlots');
                const alternativeSlotsGrid = document.getElementById('alternativeSlotsGrid');

                if (data.slots && data.slots.length > 0) {
                    // Check if original time is available
                    const originalTimeSlot = data.slots.find(slot => slot.time === originalTime);

                    if (originalTimeSlot) {
                        // Original time is available
                        timeSlotStatus.innerHTML = `
                            <div class="text-green-600 dark:text-green-400">
                                <i class="fas fa-check-circle mr-1"></i>
                                <span class="text-sm font-medium">Available</span>
                            </div>
                        `;

                        // Set the original time as selected
                        let timeInput = document.getElementById('selectedTime');
                        if (!timeInput) {
                            timeInput = document.createElement('input');
                            timeInput.type = 'hidden';
                            timeInput.id = 'selectedTime';
                            timeInput.name = 'appointment_time';
                            document.getElementById('bookingForm').appendChild(timeInput);
                        }
                        timeInput.value = originalTime;

                        // Hide alternative slots
                        alternativeSlots.classList.add('hidden');
                    } else {
                        // Original time is not available, show alternatives
                        timeSlotStatus.innerHTML = `
                            <div class="text-red-600 dark:text-red-400">
                                <i class="fas fa-times-circle mr-1"></i>
                                <span class="text-sm font-medium">Not Available</span>
                            </div>
                        `;

                        // Show alternative slots
                        alternativeSlotsGrid.innerHTML = '';
                        const nearestSlots = data.slots.slice(0, 6); // Show up to 6 nearest slots

                        nearestSlots.forEach(slot => {
                            const slotButton = document.createElement('button');
                            slotButton.type = 'button';
                            slotButton.className = 'px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gold hover:text-white transition-colors text-xs';
                            slotButton.textContent = slot.formatted_time;
                            slotButton.onclick = () => selectTimeSlot(slot.time);
                            alternativeSlotsGrid.appendChild(slotButton);
                        });

                        alternativeSlots.classList.remove('hidden');
                    }
                } else {
                    // No slots available
                    timeSlotStatus.innerHTML = `
                        <div class="text-red-600 dark:text-red-400">
                            <i class="fas fa-times-circle mr-1"></i>
                            <span class="text-sm font-medium">No slots available</span>
                        </div>
                    `;
                    alternativeSlots.classList.add('hidden');
                }

                updateSummary();
            })
            .catch(error => {
                console.error('Error checking time slot availability:', error);
                document.getElementById('timeSlotStatus').innerHTML = `
                    <div class="text-red-600 dark:text-red-400">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <span class="text-sm font-medium">Error checking availability</span>
                    </div>
                `;
                document.getElementById('alternativeSlots').classList.add('hidden');
            });
    }

    // Load time slots for new appointments
    function loadTimeSlots() {
        const doctorId = document.getElementById('doctorSelect').value;
        const date = document.getElementById('appointmentDate').value;

        if (!doctorId || !date) {
            document.getElementById('timeSlots').innerHTML = '<p class="text-muted col-span-3 text-center py-4">Select a doctor and date to see available time slots</p>';
            return;
        }

        fetch(`{{ route('patient.appointments.create') }}?doctor_id=${doctorId}&date=${date}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const timeSlotsContainer = document.getElementById('timeSlots');

                if (data.slots && data.slots.length > 0) {
                    timeSlotsContainer.innerHTML = '';

                    data.slots.forEach(slot => {
                        const slotButton = document.createElement('button');
                        slotButton.type = 'button';
                        slotButton.className = 'px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gold hover:text-white transition-colors text-sm';
                        slotButton.textContent = slot.formatted_time;
                        slotButton.onclick = () => selectTimeSlot(slot.time);
                        timeSlotsContainer.appendChild(slotButton);
                    });
                } else {
                    timeSlotsContainer.innerHTML = '<p class="text-muted col-span-3 text-center py-4">No available slots for this date</p>';
                }
            })
            .catch(error => {
                console.error('Error loading time slots:', error);
                showNotification('Error loading time slots', 'error');
            });
    }

    // Select time slot
    function selectTimeSlot(time) {
        // Remove previous selection from alternative slots
        document.querySelectorAll('#alternativeSlotsGrid button').forEach(btn => {
            btn.classList.remove('bg-gold', 'text-white');
            btn.classList.add('border-gray-300', 'dark:border-gray-600');
        });

        // Remove previous selection from regular time slots
        document.querySelectorAll('#timeSlots button').forEach(btn => {
            btn.classList.remove('bg-gold', 'text-white');
            btn.classList.add('border-gray-300', 'dark:border-gray-600');
        });

        // Select new slot
        event.target.classList.add('bg-gold', 'text-white');
        event.target.classList.remove('border-gray-300', 'dark:border-gray-600');

        // Add hidden input for selected time
        let timeInput = document.getElementById('selectedTime');
        if (!timeInput) {
            timeInput = document.createElement('input');
            timeInput.type = 'hidden';
            timeInput.id = 'selectedTime';
            timeInput.name = 'appointment_time';
            document.getElementById('bookingForm').appendChild(timeInput);
        }
        timeInput.value = time;

        updateSummary();
    }

    // Update appointment summary
    function updateSummary() {
        const summaryContainer = document.getElementById('appointmentSummary');

        // Handle reschedule case
        @if($rescheduleAppointment)
            const rescheduleAppointment = @json($rescheduleAppointment);
            const appointmentType = document.querySelector('select[name="appointment_type"]')?.value || '';
            const date = document.getElementById('appointmentDate')?.value || '';
            const time = document.getElementById('selectedTime')?.value || '';
            const notes = document.querySelector('textarea[name="notes"]')?.value || '';

            if (!date) {
                summaryContainer.innerHTML = `
                    <div class="text-center py-8 text-muted">
                        <i class="fas fa-calendar-plus text-4xl mb-4"></i>
                        <p>Select a new date to reschedule</p>
                    </div>
                `;
                return;
            }

            let summaryHTML = '<div class="space-y-4">';

            // Show current doctor info for reschedule
            summaryHTML += `
                <div class="flex items-center justify-between">
                    <span class="text-muted">Doctor:</span>
                    <span class="text-text font-medium">${rescheduleAppointment.doctor_name}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-muted">Specialty:</span>
                    <span class="text-text font-medium">${rescheduleAppointment.specialty_name}</span>
                </div>
            `;

            if (appointmentType) {
                summaryHTML += `
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Type:</span>
                        <span class="text-text font-medium">${appointmentType.replace('_', ' ').toUpperCase()}</span>
                    </div>
                `;
            }

            if (date) {
                summaryHTML += `
                    <div class="flex items-center justify-between">
                        <span class="text-muted">New Date:</span>
                        <span class="text-text font-medium">${new Date(date).toLocaleDateString()}</span>
                    </div>
                `;
            }

            if (time) {
                const timeDisplay = new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
                summaryHTML += `
                    <div class="flex items-center justify-between">
                        <span class="text-muted">New Time:</span>
                        <span class="text-text font-medium">${timeDisplay}</span>
                    </div>
                `;
            }

            summaryHTML += '</div>';
            summaryContainer.innerHTML = summaryHTML;
        @else
            // Handle new appointment case
            const specialtySelect = document.getElementById('specialtySelect');
            const doctorSelect = document.getElementById('doctorSelect');
            const specialty = specialtySelect?.value || '';
            const doctor = doctorSelect?.value || '';
            const appointmentType = document.querySelector('select[name="appointment_type"]')?.value || '';
            const date = document.getElementById('appointmentDate')?.value || '';
            const time = document.getElementById('selectedTime')?.value || '';
            const notes = document.querySelector('textarea[name="notes"]')?.value || '';

            if (!specialty && !doctor && !appointmentType && !date) {
                summaryContainer.innerHTML = `
                    <div class="text-center py-8 text-muted">
                        <i class="fas fa-calendar-plus text-4xl mb-4"></i>
                        <p>Fill in the form to see appointment details</p>
                    </div>
                `;
                return;
            }

            let summaryHTML = '<div class="space-y-4">';

            if (specialty && specialtySelect) {
                const specialtyName = specialtySelect.options[specialtySelect.selectedIndex]?.text || '';
                summaryHTML += `
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Specialty:</span>
                        <span class="text-text font-medium">${specialtyName}</span>
                    </div>
                `;
            }

            if (doctor && doctorSelect) {
                const doctorName = doctorSelect.options[doctorSelect.selectedIndex]?.text || '';
                summaryHTML += `
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Doctor:</span>
                        <span class="text-text font-medium">${doctorName}</span>
                    </div>
                `;
            }

            if (appointmentType) {
                summaryHTML += `
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Type:</span>
                        <span class="text-text font-medium">${appointmentType.replace('_', ' ').toUpperCase()}</span>
                    </div>
                `;
            }

            if (date) {
                summaryHTML += `
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Date:</span>
                        <span class="text-text font-medium">${new Date(date).toLocaleDateString()}</span>
                    </div>
                `;
            }

            if (time) {
                const timeDisplay = new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
                summaryHTML += `
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Time:</span>
                        <span class="text-text font-medium">${timeDisplay}</span>
                    </div>
                `;
            }

            // Calculate estimated fee
            if (doctor) {
                const feeElement = document.getElementById('doctorFee');
                if (feeElement) {
                    const fee = feeElement.textContent.replace('$', '');
                    summaryHTML += `
                        <div class="border-t pt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Estimated Fee:</span>
                                <span class="text-text font-bold text-lg">$${fee}</span>
                            </div>
                        </div>
                    `;
                }
            }

            summaryHTML += '</div>';
            summaryContainer.innerHTML = summaryHTML;
        @endif
    }

    // Submit booking
    function submitBooking(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const timeInput = document.getElementById('selectedTime');

        if (timeInput) {
            formData.set('appointment_time', timeInput.value);
        }

        // Validate required fields
        @if($rescheduleAppointment)
            // For reschedule, we don't need specialty_id and doctor_id validation since they're hidden
            if (!formData.get('appointment_type') || !formData.get('appointment_date') || !formData.get('appointment_time')) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }
        @else
            // For new appointments, validate all fields
            if (!formData.get('specialty_id') || !formData.get('doctor_id') || !formData.get('appointment_type') ||
                !formData.get('appointment_date') || !formData.get('appointment_time')) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }
        @endif

        // Show loading
        document.getElementById('loadingModal').classList.remove('hidden');
        document.getElementById('submitBtn').disabled = true;

        fetch('{{ route("patient.appointments.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingModal').classList.add('hidden');
            document.getElementById('submitBtn').disabled = false;

            if (data.success) {
                showNotification('Appointment booked successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("patient.appointments.index") }}';
                }, 1500);
            } else {
                showNotification(data.message || 'Error booking appointment', 'error');
            }
        })
        .catch(error => {
            document.getElementById('loadingModal').classList.add('hidden');
            document.getElementById('submitBtn').disabled = false;
            console.error('Error:', error);
            showNotification('Error booking appointment', 'error');
        });
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Simple notification - you can enhance this with a proper notification system
        alert(`${type.toUpperCase()}: ${message}`);
    }
</script>
@endpush
