<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            [
                'name_en' => 'Cardiology',
                'name_ar' => 'طب القلب',
                'description' => 'Specialized in heart and cardiovascular diseases',
                'icon' => null,
            ],
            [
                'name_en' => 'Neurology',
                'name_ar' => 'طب الأعصاب',
                'description' => 'Specialized in nervous system disorders',
                'icon' => null,
            ],
            [
                'name_en' => 'Ophthalmology',
                'name_ar' => 'طب العيون',
                'description' => 'Specialized in eye diseases and vision',
                'icon' => null,
            ],
            [
                'name_en' => 'Dentistry',
                'name_ar' => 'طب الأسنان',
                'description' => 'Specialized in oral and dental health',
                'icon' => null,
            ],
            [
                'name_en' => 'Pediatrics',
                'name_ar' => 'طب الأطفال',
                'description' => 'Specialized in child care',
                'icon' => null,
            ],
            [
                'name_en' => 'Obstetrics & Gynecology',
                'name_ar' => 'طب النساء والولادة',
                'description' => 'Specialized in women health and childbirth',
                'icon' => null,
            ],
            [
                'name_en' => 'Dermatology',
                'name_ar' => 'طب الجلدية',
                'description' => 'Specialized in skin diseases',
                'icon' => null,
            ],
            [
                'name_en' => 'Orthopedics',
                'name_ar' => 'طب العظام',
                'description' => 'Specialized in bone and joint diseases',
                'icon' => null,
            ],
            [
                'name_en' => 'Internal Medicine',
                'name_ar' => 'طب الباطنة',
                'description' => 'Specialized in internal diseases',
                'icon' => null,
            ],
            [
                'name_en' => 'Psychiatry',
                'name_ar' => 'طب النفسية',
                'description' => 'Specialized in mental health',
                'icon' => null,
            ],
        ];

        foreach ($specialties as $specialty) {
            DB::table('specialties')->insert($specialty);
        }
    }
}
