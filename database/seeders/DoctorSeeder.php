<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'user_id' => 3,
                'name' => 'Dr. Ahmed Mohamed Ali',
                'specialty_id' => 1,
                'description' => 'Cardiologist with 15 years of experience in diagnosing and treating heart diseases',
                'experience_years' => 15,
                'education' => 'PhD in Cardiology - Cairo University',
                'languages' => 'English, Arabic',
                'consultation_fee' => 200.00,
                'is_active' => true,
                'is_featured' => true,
                'rating' => 0.00,
                'total_reviews' => 0,
            ],
            [
                'user_id' => 4,
                'name' => 'Dr. Fatma Ahmed Hassan',
                'specialty_id' => 2,
                'description' => 'Neurologist specializing in epilepsy and headache treatment',
                'experience_years' => 12,
                'education' => 'PhD in Neurology - Ain Shams University',
                'languages' => 'English, Arabic, French',
                'consultation_fee' => 180.00,
                'is_active' => true,
                'is_featured' => true,
                'rating' => 0.00,
                'total_reviews' => 0,
            ],
            [
                'user_id' => 5,
                'name' => 'Dr. Mohamed Abdul Rahman',
                'specialty_id' => 3,
                'description' => 'Ophthalmologist specializing in retinal surgery and laser treatment',
                'experience_years' => 18,
                'education' => 'PhD in Ophthalmology - Alexandria University',
                'languages' => 'English, Arabic',
                'consultation_fee' => 250.00,
                'is_active' => true,
                'is_featured' => true,
                'rating' => 0.00,
                'total_reviews' => 0,
            ],
            [
                'user_id' => 6,
                'name' => 'Dr. Sara Mahmoud',
                'specialty_id' => 4,
                'description' => 'Dentist specializing in orthodontics and cosmetic surgery',
                'experience_years' => 10,
                'education' => 'PhD in Dentistry - Mansoura University',
                'languages' => 'English, Arabic',
                'consultation_fee' => 150.00,
                'is_active' => true,
                'is_featured' => true,
                'rating' => 0.00,
                'total_reviews' => 0,
            ],
            [
                'user_id' => 7,
                'name' => 'Dr. Ali Hassan Mohamed',
                'specialty_id' => 5,
                'description' => 'Pediatrician specializing in neonatology',
                'experience_years' => 14,
                'education' => 'PhD in Pediatrics - Zagazig University',
                'languages' => 'English, Arabic',
                'consultation_fee' => 120.00,
                'is_active' => true,
                'is_featured' => true,
                'rating' => 0.00,
                'total_reviews' => 0,
            ],
            [
                'user_id' => 8,
                'name' => 'Dr. Nora Ahmed',
                'specialty_id' => 6,
                'description' => 'Obstetrician specializing in natural childbirth',
                'experience_years' => 16,
                'education' => 'PhD in Obstetrics & Gynecology - Assiut University',
                'languages' => 'English, Arabic',
                'consultation_fee' => 220.00,
                'is_active' => true,
                'is_featured' => true,
                'rating' => 0.00,
                'total_reviews' => 0,
            ],
        ];

        foreach ($doctors as $doctor) {
            DB::table('doctors')->insert($doctor);
        }
    }
}
