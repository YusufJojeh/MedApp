<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'audience' => 'doctor',
                'price' => 99.00,
                'currency' => 'SAR',
                'billing_cycle' => 'monthly',
                'is_popular' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'audience' => 'doctor',
                'price' => 199.00,
                'currency' => 'SAR',
                'billing_cycle' => 'monthly',
                'is_popular' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'audience' => 'doctor',
                'price' => 299.00,
                'currency' => 'SAR',
                'billing_cycle' => 'monthly',
                'is_popular' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'audience' => 'doctor',
                'price' => 499.00,
                'currency' => 'SAR',
                'billing_cycle' => 'monthly',
                'is_popular' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->insert($plan);
        }
    }
}
