<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payments = [
            [
                'user_id' => 9,
                'doctor_id' => 1,
                'appointment_id' => 1,
                'provider' => 'stripe',
                'provider_payment_id' => '1',
                'STATUS' => 'succeeded',
                'amount' => 200.00,
                'currency' => 'SAR',
                'platform_fee' => 20.00,
                'net_amount' => 180.00,
                'meta' => json_encode(['test' => true, 'sample_payment' => true]),
            ],
        ];

        foreach ($payments as $payment) {
            DB::table('payments')->insert($payment);
        }
    }
}
