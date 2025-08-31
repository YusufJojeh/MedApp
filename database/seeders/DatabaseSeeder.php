<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SpecialtySeeder::class,
            SpecialtyPricingSeeder::class,
            UserSeeder::class,
            DoctorSeeder::class,
            DoctorPricingOverrideSeeder::class,
            WorkingHoursSeeder::class,
            PatientSeeder::class,
            PlanSeeder::class,
            PlanFeatureSeeder::class,
            SubscriptionSeeder::class,
            AppointmentSeeder::class,
            WalletSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
