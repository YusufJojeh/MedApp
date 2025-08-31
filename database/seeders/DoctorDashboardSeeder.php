<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DoctorDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the doctor ID (assuming user ID 3 is the doctor)
        $doctorId = DB::table('doctors')->where('user_id', 3)->value('id');

        if (!$doctorId) {
            $this->command->error('Doctor not found. Please ensure doctor exists with user_id 3');
            return;
        }

        // Create sample patients if they don't exist
        $patients = [
            [
                'NAME' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1234567890',
                'date_of_birth' => '1985-03-15',
                'gender' => 'male',
                'address' => '123 Main St, City, State',
                'emergency_contact' => 'Jane Doe - +1234567891',
                'blood_type' => 'O+',
                'medical_history' => 'Hypertension, High Cholesterol',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'NAME' => 'Sarah Wilson',
                'email' => 'sarah.wilson@example.com',
                'phone' => '+1234567892',
                'date_of_birth' => '1990-07-22',
                'gender' => 'female',
                'address' => '456 Oak Ave, City, State',
                'emergency_contact' => 'Mike Wilson - +1234567893',
                'blood_type' => 'A+',
                'medical_history' => 'Asthma, Seasonal Allergies',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'NAME' => 'Mike Johnson',
                'email' => 'mike.johnson@example.com',
                'phone' => '+1234567894',
                'date_of_birth' => '1978-11-08',
                'gender' => 'male',
                'address' => '789 Pine Rd, City, State',
                'emergency_contact' => 'Lisa Johnson - +1234567895',
                'blood_type' => 'B+',
                'medical_history' => 'Diabetes Type 2, Hypertension',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'NAME' => 'Emily Brown',
                'email' => 'emily.brown@example.com',
                'phone' => '+1234567896',
                'date_of_birth' => '1992-04-12',
                'gender' => 'female',
                'address' => '321 Elm St, City, State',
                'emergency_contact' => 'David Brown - +1234567897',
                'blood_type' => 'AB+',
                'medical_history' => 'None',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'NAME' => 'Ahmed Mohamed Ali',
                'email' => 'ahmed.ali@example.com',
                'phone' => '+1234567898',
                'date_of_birth' => '1982-09-18',
                'gender' => 'male',
                'address' => '654 Maple Dr, City, State',
                'emergency_contact' => 'Fatima Ali - +1234567899',
                'blood_type' => 'O-',
                'medical_history' => 'Heart Condition, Previous Surgery',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'NAME' => 'Lisa Rodriguez',
                'email' => 'lisa.rodriguez@example.com',
                'phone' => '+1234567900',
                'date_of_birth' => '1988-12-03',
                'gender' => 'female',
                'address' => '987 Cedar Ln, City, State',
                'emergency_contact' => 'Carlos Rodriguez - +1234567901',
                'blood_type' => 'A-',
                'medical_history' => 'Migraine, Anxiety',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'NAME' => 'Robert Chen',
                'email' => 'robert.chen@example.com',
                'phone' => '+1234567902',
                'date_of_birth' => '1975-06-25',
                'gender' => 'male',
                'address' => '147 Birch St, City, State',
                'emergency_contact' => 'Jennifer Chen - +1234567903',
                'blood_type' => 'B-',
                'medical_history' => 'Arthritis, Back Pain',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'NAME' => 'Maria Garcia',
                'email' => 'maria.garcia@example.com',
                'phone' => '+1234567904',
                'date_of_birth' => '1995-01-14',
                'gender' => 'female',
                'address' => '258 Willow Ave, City, State',
                'emergency_contact' => 'Jose Garcia - +1234567905',
                'blood_type' => 'O+',
                'medical_history' => 'None',
                'status' => 'active',
                'created_at' => now(),
            ],
        ];

        foreach ($patients as $patient) {
            $existingPatient = DB::table('patients')->where('email', $patient['email'])->first();
            if (!$existingPatient) {
                DB::table('patients')->insert($patient);
            }
        }

        // Get patient IDs
        $patientIds = DB::table('patients')->pluck('id')->toArray();

        // Create comprehensive sample appointments
        $appointments = [
            // Today's appointments (August 28, 2025)
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[0] ?? 1,
                'appointment_date' => Carbon::today(),
                'appointment_time' => '09:00:00',
                'STATUS' => 'confirmed',
                'notes' => 'General Checkup - Annual physical examination and health assessment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[1] ?? 2,
                'appointment_date' => Carbon::today(),
                'appointment_time' => '10:30:00',
                'STATUS' => 'confirmed',
                'notes' => 'Follow-up Consultation - Review of asthma treatment progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[2] ?? 3,
                'appointment_date' => Carbon::today(),
                'appointment_time' => '14:00:00',
                'STATUS' => 'scheduled',
                'notes' => 'Cardiology Consultation - Cardiac evaluation and stress test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[3] ?? 4,
                'appointment_date' => Carbon::today(),
                'appointment_time' => '15:30:00',
                'STATUS' => 'confirmed',
                'notes' => 'Dermatology Check - Skin condition evaluation and treatment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[4] ?? 5,
                'appointment_date' => Carbon::today(),
                'appointment_time' => '16:00:00',
                'STATUS' => 'scheduled',
                'notes' => 'Cardiology Follow-up - Post-surgery recovery monitoring',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Yesterday's appointments (August 27, 2025)
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[0] ?? 1,
                'appointment_date' => Carbon::yesterday(),
                'appointment_time' => '09:00:00',
                'STATUS' => 'completed',
                'notes' => 'General Checkup - Blood pressure monitoring and medication review',
                'created_at' => Carbon::yesterday(),
                'updated_at' => Carbon::yesterday(),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[1] ?? 2,
                'appointment_date' => Carbon::yesterday(),
                'appointment_time' => '14:00:00',
                'STATUS' => 'completed',
                'notes' => 'Follow-up Consultation - Asthma treatment completed successfully',
                'created_at' => Carbon::yesterday(),
                'updated_at' => Carbon::yesterday(),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[5] ?? 6,
                'appointment_date' => Carbon::yesterday(),
                'appointment_time' => '15:00:00',
                'STATUS' => 'completed',
                'notes' => 'Neurology Consultation - Migraine treatment and medication adjustment',
                'created_at' => Carbon::yesterday(),
                'updated_at' => Carbon::yesterday(),
            ],

            // Past appointments - Last week
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[2] ?? 3,
                'appointment_date' => Carbon::now()->subDays(3),
                'appointment_time' => '10:00:00',
                'STATUS' => 'completed',
                'notes' => 'Cardiology Consultation - Heart condition evaluation completed',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[3] ?? 4,
                'appointment_date' => Carbon::now()->subDays(5),
                'appointment_time' => '11:00:00',
                'STATUS' => 'completed',
                'notes' => 'General Checkup - Routine health assessment completed',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[6] ?? 7,
                'appointment_date' => Carbon::now()->subDays(6),
                'appointment_time' => '13:00:00',
                'STATUS' => 'completed',
                'notes' => 'Orthopedic Consultation - Arthritis treatment and physical therapy',
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(6),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[0] ?? 1,
                'appointment_date' => Carbon::now()->subDays(7),
                'appointment_time' => '09:30:00',
                'STATUS' => 'completed',
                'notes' => 'Follow-up Consultation - Hypertension management review',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[4] ?? 5,
                'appointment_date' => Carbon::now()->subDays(8),
                'appointment_time' => '10:30:00',
                'STATUS' => 'completed',
                'notes' => 'Cardiology Surgery - Heart surgery procedure completed',
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[7] ?? 8,
                'appointment_date' => Carbon::now()->subDays(10),
                'appointment_time' => '14:30:00',
                'STATUS' => 'completed',
                'notes' => 'General Checkup - First-time patient consultation',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],

            // More past appointments for comprehensive history
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[1] ?? 2,
                'appointment_date' => Carbon::now()->subDays(14),
                'appointment_time' => '11:00:00',
                'STATUS' => 'completed',
                'notes' => 'Asthma Treatment - Initial consultation and treatment plan',
                'created_at' => Carbon::now()->subDays(14),
                'updated_at' => Carbon::now()->subDays(14),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[2] ?? 3,
                'appointment_date' => Carbon::now()->subDays(21),
                'appointment_time' => '15:00:00',
                'STATUS' => 'completed',
                'notes' => 'Cardiology Screening - Preventive heart health check',
                'created_at' => Carbon::now()->subDays(21),
                'updated_at' => Carbon::now()->subDays(21),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[5] ?? 6,
                'appointment_date' => Carbon::now()->subDays(28),
                'appointment_time' => '10:00:00',
                'STATUS' => 'completed',
                'notes' => 'Neurology Consultation - Initial migraine assessment',
                'created_at' => Carbon::now()->subDays(28),
                'updated_at' => Carbon::now()->subDays(28),
            ],

            // Future appointments
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[2] ?? 3,
                'appointment_date' => Carbon::tomorrow(),
                'appointment_time' => '10:00:00',
                'STATUS' => 'confirmed',
                'notes' => 'General Checkup - Scheduled follow-up examination',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[3] ?? 4,
                'appointment_date' => Carbon::tomorrow()->addDays(1),
                'appointment_time' => '11:00:00',
                'STATUS' => 'scheduled',
                'notes' => 'Specialist Consultation - Referral for specialized treatment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[1] ?? 2,
                'appointment_date' => Carbon::tomorrow()->addDays(2),
                'appointment_time' => '14:00:00',
                'STATUS' => 'confirmed',
                'notes' => 'Cardiology Consultation - Scheduled cardiac evaluation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[6] ?? 7,
                'appointment_date' => Carbon::tomorrow()->addDays(3),
                'appointment_time' => '15:30:00',
                'STATUS' => 'scheduled',
                'notes' => 'Orthopedic Follow-up - Post-treatment evaluation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doctorId,
                'patient_id' => $patientIds[7] ?? 8,
                'appointment_date' => Carbon::tomorrow()->addDays(5),
                'appointment_time' => '09:00:00',
                'STATUS' => 'confirmed',
                'notes' => 'General Checkup - Annual health assessment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($appointments as $appointment) {
            $existingAppointment = DB::table('appointments')
                ->where('doctor_id', $appointment['doctor_id'])
                ->where('patient_id', $appointment['patient_id'])
                ->where('appointment_date', $appointment['appointment_date'])
                ->where('appointment_time', $appointment['appointment_time'])
                ->first();

            if (!$existingAppointment) {
                DB::table('appointments')->insert($appointment);
            }
        }

        // Create realistic payments for completed appointments
        $completedAppointments = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->where('STATUS', 'completed')
            ->get();

        foreach ($completedAppointments as $appointment) {
            $existingPayment = DB::table('payments')
                ->where('appointment_id', $appointment->id)
                ->first();

            if (!$existingPayment) {
                // Different fees based on appointment type
                $appointmentType = strtolower($appointment->notes);
                if (strpos($appointmentType, 'cardiology') !== false) {
                    $amount = 350; // Cardiology consultations are more expensive
                } elseif (strpos($appointmentType, 'surgery') !== false) {
                    $amount = 800; // Surgery procedures are most expensive
                } elseif (strpos($appointmentType, 'neurology') !== false || strpos($appointmentType, 'orthopedic') !== false) {
                    $amount = 300; // Specialist consultations
                } elseif (strpos($appointmentType, 'dermatology') !== false) {
                    $amount = 250; // Dermatology consultations
                } else {
                    $amount = 200; // General consultations
                }

                $platformFee = $amount * 0.05; // 5% platform fee
                $netAmount = $amount - $platformFee;

                // Different payment methods
                $paymentMethods = ['credit_card', 'debit_card', 'bank_transfer'];
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

                DB::table('payments')->insert([
                    'user_id' => $appointment->patient_id,
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

        // Create wallet for doctor if it doesn't exist
        $existingWallet = DB::table('wallets')->where('user_id', 3)->first();
        if (!$existingWallet) {
            DB::table('wallets')->insert([
                'user_id' => 3,
                'balance' => 2500.00,
                'currency' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update doctor rating
        DB::table('doctors')
            ->where('id', $doctorId)
            ->update([
                'rating' => 4.8,
                'total_reviews' => 25,
                'updated_at' => now(),
            ]);

        $this->command->info('Doctor dashboard sample data created successfully!');
    }
}
