<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AllDoctorsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all doctors
        $doctors = DB::table('doctors')->get();

        if ($doctors->isEmpty()) {
            $this->command->error('No doctors found in the database.');
            return;
        }

        $this->command->info('Creating comprehensive data for ' . $doctors->count() . ' doctors...');

        foreach ($doctors as $doctor) {
            $this->createDataForDoctor($doctor);
        }

        $this->command->info('All doctors data created successfully!');
    }

    private function createDataForDoctor($doctor)
    {
        $this->command->info("Creating data for {$doctor->name}...");

        // Create patients for this doctor
        $patients = $this->createPatientsForDoctor($doctor);

        // Create appointments for this doctor
        $this->createAppointmentsForDoctor($doctor, $patients);

        // Create payments for completed appointments
        $this->createPaymentsForDoctor($doctor);

        // Update doctor rating
        $this->updateDoctorRating($doctor);
    }

    private function createPatientsForDoctor($doctor)
    {
        // Generate 8-12 patients per doctor
        $patientCount = rand(8, 12);
        $patients = [];

        $firstNames = ['John', 'Sarah', 'Mike', 'Emily', 'Ahmed', 'Lisa', 'Robert', 'Maria', 'David', 'Jennifer', 'Mohamed', 'Fatima', 'Ali', 'Nora', 'Hassan', 'Aisha'];
        $lastNames = ['Doe', 'Wilson', 'Johnson', 'Brown', 'Ali', 'Rodriguez', 'Chen', 'Garcia', 'Smith', 'Taylor', 'Hassan', 'Ahmed', 'Mohamed', 'Khan', 'Patel', 'Singh'];

        for ($i = 0; $i < $patientCount; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $name = $firstName . ' ' . $lastName;

            // Ensure unique email
            $email = strtolower($firstName . '.' . $lastName . rand(1, 999) . '@example.com');

            // Create user record first
            $userId = DB::table('users')->insertGetId([
                'username' => strtolower($firstName . $lastName . rand(1, 999)),
                'email' => $email,
                'password' => bcrypt('password'),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'role' => 'patient',
                'status' => 'active',
                'created_at' => Carbon::now()->subDays(rand(30, 365)),
                'updated_at' => Carbon::now()->subDays(rand(30, 365)),
            ]);

            $patient = [
                'user_id' => $userId,
                'NAME' => $name,
                'email' => $email,
                'phone' => '+1' . rand(2000000000, 9999999999),
                'date_of_birth' => Carbon::now()->subYears(rand(18, 80))->format('Y-m-d'),
                'gender' => rand(0, 1) ? 'male' : 'female',
                'address' => rand(100, 9999) . ' ' . ['Main St', 'Oak Ave', 'Pine Rd', 'Elm St', 'Maple Dr', 'Cedar Ln', 'Birch St', 'Willow Ave'][array_rand(['Main St', 'Oak Ave', 'Pine Rd', 'Elm St', 'Maple Dr', 'Cedar Ln', 'Birch St', 'Willow Ave'])] . ', City, State',
                'emergency_contact' => $firstName . ' ' . $lastName . ' - +1' . rand(2000000000, 9999999999),
                'blood_type' => ['O+', 'A+', 'B+', 'AB+', 'O-', 'A-', 'B-', 'AB-'][array_rand(['O+', 'A+', 'B+', 'AB+', 'O-', 'A-', 'B-', 'AB-'])],
                'medical_history' => $this->getMedicalHistoryForSpecialty($doctor->specialty_id),
                'status' => 'active',
                'created_at' => Carbon::now()->subDays(rand(30, 365)),
            ];

            $existingPatient = DB::table('patients')->where('email', $patient['email'])->first();
            if (!$existingPatient) {
                DB::table('patients')->insert($patient);
                $patients[] = $patient;
            }
        }

        return $patients;
    }

    private function getMedicalHistoryForSpecialty($specialtyId)
    {
        $medicalHistories = [
            1 => ['Hypertension', 'High Cholesterol', 'Heart Condition', 'Previous Heart Surgery', 'Chest Pain', 'Arrhythmia'],
            2 => ['Migraine', 'Anxiety', 'Depression', 'Epilepsy', 'Headache', 'Neurological Disorder'],
            3 => ['Vision Problems', 'Glaucoma', 'Cataracts', 'Diabetic Retinopathy', 'Eye Pain', 'Blurred Vision'],
            4 => ['Dental Pain', 'Gum Disease', 'Tooth Decay', 'Orthodontic Issues', 'Dental Anxiety', 'Previous Dental Work'],
            5 => ['Childhood Illness', 'Vaccination History', 'Growth Issues', 'Developmental Delay', 'Allergies', 'Asthma'],
            6 => ['Pregnancy History', 'Gynecological Issues', 'Menstrual Problems', 'Fertility Issues', 'Previous Surgery', 'Hormonal Imbalance']
        ];

        $histories = $medicalHistories[$specialtyId] ?? ['General Health Issues'];
        $count = rand(0, 2); // 0-2 conditions
        if ($count === 0) return 'None';

        $selected = array_rand($histories, min($count, count($histories)));
        if (!is_array($selected)) $selected = [$selected];

        return implode(', ', array_map(function($index) use ($histories) {
            return $histories[$index];
        }, $selected));
    }

    private function createAppointmentsForDoctor($doctor, $patients)
    {
        $patientIds = DB::table('patients')->pluck('id')->toArray();
        $appointments = [];

        // Generate 15-25 appointments per doctor
        $appointmentCount = rand(15, 25);

        for ($i = 0; $i < $appointmentCount; $i++) {
            $patientId = $patientIds[array_rand($patientIds)];
            $appointmentDate = $this->getRandomAppointmentDate();
            $appointmentTime = $this->getRandomAppointmentTime();
            $status = $this->getRandomStatus($appointmentDate);
            $notes = $this->getAppointmentNotesForSpecialty($doctor->specialty_id, $status);

            $appointment = [
                'doctor_id' => $doctor->id,
                'patient_id' => $patientId,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'STATUS' => $status,
                'notes' => $notes,
                'created_at' => Carbon::parse($appointmentDate)->subDays(rand(1, 30)),
                'updated_at' => Carbon::parse($appointmentDate)->subDays(rand(0, 7)),
            ];

            $existingAppointment = DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('patient_id', $patientId)
                ->where('appointment_date', $appointmentDate)
                ->where('appointment_time', $appointmentTime)
                ->first();

            if (!$existingAppointment) {
                DB::table('appointments')->insert($appointment);
                $appointments[] = $appointment;
            }
        }
    }

    private function getRandomAppointmentDate()
    {
        $today = Carbon::today();
        $daysOffset = rand(-30, 30); // Past 30 days to future 30 days
        return $today->copy()->addDays($daysOffset)->format('Y-m-d');
    }

    private function getRandomAppointmentTime()
    {
        $hours = [9, 10, 11, 14, 15, 16, 17];
        $hour = $hours[array_rand($hours)];
        $minute = [0, 15, 30, 45][array_rand([0, 15, 30, 45])];
        return sprintf('%02d:%02d:00', $hour, $minute);
    }

    private function getRandomStatus($appointmentDate)
    {
        $today = Carbon::today();
        $appointmentDay = Carbon::parse($appointmentDate);

        if ($appointmentDay->lt($today)) {
            // Past appointments
            return ['completed', 'cancelled', 'no_show'][array_rand(['completed', 'cancelled', 'no_show'])];
        } elseif ($appointmentDay->eq($today)) {
            // Today's appointments
            return ['confirmed', 'scheduled'][array_rand(['confirmed', 'scheduled'])];
        } else {
            // Future appointments
            return ['confirmed', 'scheduled'][array_rand(['confirmed', 'scheduled'])];
        }
    }

    private function getAppointmentNotesForSpecialty($specialtyId, $status)
    {
        $appointmentTypes = [
            1 => ['Cardiology Consultation', 'Heart Evaluation', 'Stress Test', 'EKG Review', 'Cardiac Surgery', 'Heart Surgery Follow-up'],
            2 => ['Neurology Consultation', 'Migraine Treatment', 'Epilepsy Management', 'Headache Evaluation', 'Neurological Assessment', 'Medication Review'],
            3 => ['Eye Examination', 'Vision Test', 'Glaucoma Screening', 'Cataract Surgery', 'Retinal Examination', 'Laser Treatment'],
            4 => ['Dental Checkup', 'Teeth Cleaning', 'Orthodontic Consultation', 'Root Canal', 'Dental Surgery', 'Cosmetic Dentistry'],
            5 => ['Pediatric Checkup', 'Child Vaccination', 'Growth Monitoring', 'Developmental Assessment', 'Child Illness', 'Wellness Visit'],
            6 => ['Gynecology Consultation', 'Pregnancy Checkup', 'Ultrasound', 'Prenatal Care', 'Fertility Consultation', 'Gynecological Surgery']
        ];

        $types = $appointmentTypes[$specialtyId] ?? ['General Consultation'];
        $type = $types[array_rand($types)];

        $descriptions = [
            'completed' => ['Completed successfully', 'Treatment completed', 'Procedure finished', 'Assessment completed', 'Follow-up completed'],
            'confirmed' => ['Confirmed appointment', 'Patient confirmed', 'Ready for consultation', 'Confirmed visit'],
            'scheduled' => ['Scheduled consultation', 'Appointment scheduled', 'Planned visit', 'Scheduled examination'],
            'cancelled' => ['Cancelled by patient', 'Rescheduled', 'Cancelled due to emergency', 'No longer needed'],
            'no_show' => ['Patient did not show', 'Missed appointment', 'No-show', 'Failed to attend']
        ];

        $description = $descriptions[$status][array_rand($descriptions[$status])];
        return $type . ' - ' . $description;
    }

    private function createPaymentsForDoctor($doctor)
    {
        $completedAppointments = DB::table('appointments')
            ->where('doctor_id', $doctor->id)
            ->where('STATUS', 'completed')
            ->get();

        foreach ($completedAppointments as $appointment) {
            $existingPayment = DB::table('payments')
                ->where('appointment_id', $appointment->id)
                ->first();

            if (!$existingPayment) {
                $amount = $this->getPaymentAmountForSpecialty($doctor->specialty_id);
                $platformFee = $amount * 0.05; // 5% platform fee
                $netAmount = $amount - $platformFee;

                $paymentMethods = ['credit_card', 'debit_card', 'bank_transfer'];
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

                // Get the user_id from the patient record
                $patient = DB::table('patients')->where('id', $appointment->patient_id)->first();
                if ($patient && $patient->user_id) {
                    DB::table('payments')->insert([
                        'user_id' => $patient->user_id,
                        'doctor_id' => $appointment->doctor_id,
                        'appointment_id' => $appointment->id,
                        'provider' => 'stripe',
                        'provider_payment_id' => 'pi_' . strtoupper(uniqid()),
                        'STATUS' => 'succeeded',
                        'amount' => $amount,
                        'currency' => 'SAR',
                        'platform_fee' => $platformFee,
                        'net_amount' => $netAmount,
                        'meta' => json_encode([
                            'appointment_type' => explode(' - ', $appointment->notes)[0] ?? 'consultation',
                            'payment_method' => $paymentMethod
                        ]),
                        'created_at' => $appointment->created_at,
                        'updated_at' => $appointment->updated_at,
                    ]);
                }
            }
        }
    }

    private function getPaymentAmountForSpecialty($specialtyId)
    {
        $amounts = [
            1 => rand(300, 500), // Cardiology: 300-500
            2 => rand(250, 400), // Neurology: 250-400
            3 => rand(200, 350), // Ophthalmology: 200-350
            4 => rand(150, 300), // Dentistry: 150-300
            5 => rand(120, 250), // Pediatrics: 120-250
            6 => rand(200, 400), // Obstetrics: 200-400
        ];

        return $amounts[$specialtyId] ?? rand(150, 300);
    }

    private function updateDoctorRating($doctor)
    {
        $rating = rand(35, 50) / 10; // 3.5 to 5.0
        $totalReviews = rand(10, 50);

        DB::table('doctors')
            ->where('id', $doctor->id)
            ->update([
                'rating' => $rating,
                'total_reviews' => $totalReviews,
                'updated_at' => now(),
            ]);
    }
}
