<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appointments = [
            [
                'patient_id' => 1,
                'doctor_id' => 1,
                'appointment_date' => '2025-08-24',
                'appointment_time' => '10:00:00',
                'STATUS' => 'scheduled',
                'notes' => 'Regular checkup',
            ],
            [
                'patient_id' => 2,
                'doctor_id' => 2,
                'appointment_date' => '2025-08-25',
                'appointment_time' => '14:00:00',
                'STATUS' => 'confirmed',
                'notes' => 'Follow-up appointment',
            ],
            [
                'patient_id' => 3,
                'doctor_id' => 3,
                'appointment_date' => '2025-08-26',
                'appointment_time' => '11:30:00',
                'STATUS' => 'scheduled',
                'notes' => 'Eye examination',
            ],
            [
                'patient_id' => 4,
                'doctor_id' => 4,
                'appointment_date' => '2025-08-24',
                'appointment_time' => '16:00:00',
                'STATUS' => 'scheduled',
                'notes' => 'Dental cleaning',
            ],
            [
                'patient_id' => 5,
                'doctor_id' => 5,
                'appointment_date' => '2025-08-25',
                'appointment_time' => '09:00:00',
                'STATUS' => 'confirmed',
                'notes' => 'Child vaccination',
            ],
            [
                'patient_id' => 6,
                'doctor_id' => 6,
                'appointment_date' => '2025-08-24',
                'appointment_time' => '13:00:00',
                'STATUS' => 'scheduled',
                'notes' => 'Prenatal checkup',
            ],
        ];

        foreach ($appointments as $appointment) {
            DB::table('appointments')->insert($appointment);
        }
    }
}
