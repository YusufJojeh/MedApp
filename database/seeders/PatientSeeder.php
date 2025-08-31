<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = [
            [
                'user_id' => 9,
                'NAME' => 'Ahmed Mohamed Ali',
                'phone' => '01012345678',
                'email' => 'ahmed@email.com',
                'date_of_birth' => '1985-03-15',
                'gender' => 'male',
                'blood_type' => 'A+',
                'address' => 'Nile Street, Cairo',
                'medical_history' => 'No chronic diseases',
                'emergency_contact' => 'Fatma Ali - 01098765432',
                'status' => 'active',
            ],
            [
                'user_id' => 10,
                'NAME' => 'Sara Ahmed Hassan',
                'phone' => '01123456789',
                'email' => 'sara@email.com',
                'date_of_birth' => '1990-07-22',
                'gender' => 'female',
                'blood_type' => 'O+',
                'address' => 'Tahrir Street, Giza',
                'medical_history' => 'Penicillin allergy',
                'emergency_contact' => 'Mohamed Hassan - 01187654321',
                'status' => 'active',
            ],
            [
                'user_id' => 11,
                'NAME' => 'Mohamed Abdul Rahman',
                'phone' => '01234567890',
                'email' => 'mohamed@email.com',
                'date_of_birth' => '1978-11-08',
                'gender' => 'male',
                'blood_type' => 'B+',
                'address' => 'Pyramid Street, Giza',
                'medical_history' => 'High blood pressure',
                'emergency_contact' => 'Fatma Abdul Rahman - 01265432109',
                'status' => 'active',
            ],
            [
                'user_id' => 12,
                'NAME' => 'Fatma Mahmoud',
                'phone' => '01345678901',
                'email' => 'fatma@email.com',
                'date_of_birth' => '1992-04-30',
                'gender' => 'female',
                'blood_type' => 'AB+',
                'address' => 'Maadi Street, Cairo',
                'medical_history' => 'No chronic diseases',
                'emergency_contact' => 'Ahmed Mahmoud - 01354321098',
                'status' => 'active',
            ],
            [
                'user_id' => 13,
                'NAME' => 'Ali Hassan Mohamed',
                'phone' => '01456789012',
                'email' => 'ali@email.com',
                'date_of_birth' => '1988-09-12',
                'gender' => 'male',
                'blood_type' => 'A-',
                'address' => 'New Cairo Street, Cairo',
                'medical_history' => 'Type 2 diabetes',
                'emergency_contact' => 'Nora Hassan - 01443210987',
                'status' => 'active',
            ],
            [
                'user_id' => 14,
                'NAME' => 'Nora Ahmed',
                'phone' => '01567890123',
                'email' => 'nora@email.com',
                'date_of_birth' => '1995-12-25',
                'gender' => 'female',
                'blood_type' => 'O-',
                'address' => 'Maadi Street, Cairo',
                'medical_history' => 'No chronic diseases',
                'emergency_contact' => 'Ahmed Ali - 01532109876',
                'status' => 'active',
            ],
        ];

        foreach ($patients as $patient) {
            DB::table('patients')->insert($patient);
        }
    }
}
