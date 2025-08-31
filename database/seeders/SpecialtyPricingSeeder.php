<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialtyPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pricing = [
            ['specialty_id' => 1, 'base_price' => 150.00, 'currency' => 'SAR', 'note' => 'Cardiology consultation'],
            ['specialty_id' => 2, 'base_price' => 120.00, 'currency' => 'SAR', 'note' => 'Neurology consultation'],
            ['specialty_id' => 3, 'base_price' => 100.00, 'currency' => 'SAR', 'note' => 'Ophthalmology consultation'],
            ['specialty_id' => 4, 'base_price' => 80.00, 'currency' => 'SAR', 'note' => 'Dental consultation'],
            ['specialty_id' => 5, 'base_price' => 90.00, 'currency' => 'SAR', 'note' => 'Pediatric consultation'],
            ['specialty_id' => 6, 'base_price' => 130.00, 'currency' => 'SAR', 'note' => 'Obstetrics & Gynecology consultation'],
            ['specialty_id' => 7, 'base_price' => 110.00, 'currency' => 'SAR', 'note' => 'Dermatology consultation'],
            ['specialty_id' => 8, 'base_price' => 140.00, 'currency' => 'SAR', 'note' => 'Orthopedics consultation'],
            ['specialty_id' => 9, 'base_price' => 100.00, 'currency' => 'SAR', 'note' => 'Internal Medicine consultation'],
            ['specialty_id' => 10, 'base_price' => 160.00, 'currency' => 'SAR', 'note' => 'Psychiatry consultation'],
        ];

        foreach ($pricing as $price) {
            DB::table('specialty_pricing')->insert($price);
        }
    }
}
