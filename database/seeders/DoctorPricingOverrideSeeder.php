<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctorPricingOverrideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $overrides = [
            [
                'doctor_id' => 1,
                'override_price' => 180.00,
                'currency' => 'SAR',
                'note' => 'Senior cardiologist premium rate',
            ],
            [
                'doctor_id' => 2,
                'override_price' => 200.00,
                'currency' => 'SAR',
                'note' => 'Specialist neurology consultation',
            ],
            [
                'doctor_id' => 3,
                'override_price' => 120.00,
                'currency' => 'SAR',
                'note' => 'Experienced ophthalmologist rate',
            ],
        ];

        foreach ($overrides as $override) {
            DB::table('doctor_pricing_overrides')->insert($override);
        }
    }
}
