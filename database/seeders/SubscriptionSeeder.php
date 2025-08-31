<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptions = [
            [
                'user_id' => 3,
                'plan_id' => 2,
                'STATUS' => 'active',
                'renews_at' => null,
                'canceled_at' => null,
                'provider' => 'manual',
                'provider_subscription_id' => null,
                'meta' => json_encode(['created_by' => 'system', 'sample_subscription' => true]),
            ],
            [
                'user_id' => 4,
                'plan_id' => 2,
                'STATUS' => 'active',
                'renews_at' => null,
                'canceled_at' => null,
                'provider' => 'manual',
                'provider_subscription_id' => null,
                'meta' => json_encode(['created_by' => 'system', 'sample_subscription' => true]),
            ],
            [
                'user_id' => 5,
                'plan_id' => 2,
                'STATUS' => 'active',
                'renews_at' => null,
                'canceled_at' => null,
                'provider' => 'manual',
                'provider_subscription_id' => null,
                'meta' => json_encode(['created_by' => 'system', 'sample_subscription' => true]),
            ],
        ];

        foreach ($subscriptions as $subscription) {
            DB::table('subscriptions')->insert($subscription);
        }
    }
}
