<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wallets = [
            ['user_id' => 3, 'balance' => 0.00, 'currency' => 'SAR'],
            ['user_id' => 4, 'balance' => 0.00, 'currency' => 'SAR'],
            ['user_id' => 5, 'balance' => 0.00, 'currency' => 'SAR'],
            ['user_id' => 6, 'balance' => 0.00, 'currency' => 'SAR'],
            ['user_id' => 7, 'balance' => 0.00, 'currency' => 'SAR'],
            ['user_id' => 8, 'balance' => 0.00, 'currency' => 'SAR'],
        ];

        foreach ($wallets as $wallet) {
            DB::table('wallets')->insert($wallet);
        }
    }
}
