<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            // Starter Plan Features
            ['plan_id' => 1, 'label' => 'Up to 50 appointments per month', 'is_included' => true, 'note' => null, 'sort_order' => 1],
            ['plan_id' => 1, 'label' => 'Basic patient management', 'is_included' => true, 'note' => null, 'sort_order' => 2],
            ['plan_id' => 1, 'label' => 'Email support', 'is_included' => true, 'note' => null, 'sort_order' => 3],
            ['plan_id' => 1, 'label' => 'Standard appointment scheduling', 'is_included' => true, 'note' => null, 'sort_order' => 4],
            ['plan_id' => 1, 'label' => 'Advanced analytics', 'is_included' => false, 'note' => 'Upgrade to Professional', 'sort_order' => 5],
            ['plan_id' => 1, 'label' => 'Priority support', 'is_included' => false, 'note' => 'Upgrade to Professional', 'sort_order' => 6],
            ['plan_id' => 1, 'label' => 'Custom branding', 'is_included' => false, 'note' => 'Upgrade to Premium', 'sort_order' => 7],
            ['plan_id' => 1, 'label' => 'API access', 'is_included' => false, 'note' => 'Upgrade to Enterprise', 'sort_order' => 8],

            // Professional Plan Features
            ['plan_id' => 2, 'label' => 'Up to 200 appointments per month', 'is_included' => true, 'note' => null, 'sort_order' => 1],
            ['plan_id' => 2, 'label' => 'Advanced patient management', 'is_included' => true, 'note' => null, 'sort_order' => 2],
            ['plan_id' => 2, 'label' => 'Priority email support', 'is_included' => true, 'note' => null, 'sort_order' => 3],
            ['plan_id' => 2, 'label' => 'Advanced appointment scheduling', 'is_included' => true, 'note' => null, 'sort_order' => 4],
            ['plan_id' => 2, 'label' => 'Advanced analytics & reporting', 'is_included' => true, 'note' => null, 'sort_order' => 5],
            ['plan_id' => 2, 'label' => 'Priority support', 'is_included' => true, 'note' => null, 'sort_order' => 6],
            ['plan_id' => 2, 'label' => 'Custom branding', 'is_included' => false, 'note' => 'Upgrade to Premium', 'sort_order' => 7],
            ['plan_id' => 2, 'label' => 'API access', 'is_included' => false, 'note' => 'Upgrade to Enterprise', 'sort_order' => 8],

            // Premium Plan Features
            ['plan_id' => 3, 'label' => 'Unlimited appointments', 'is_included' => true, 'note' => null, 'sort_order' => 1],
            ['plan_id' => 3, 'label' => 'Premium patient management', 'is_included' => true, 'note' => null, 'sort_order' => 2],
            ['plan_id' => 3, 'label' => '24/7 phone support', 'is_included' => true, 'note' => null, 'sort_order' => 3],
            ['plan_id' => 3, 'label' => 'Advanced appointment scheduling', 'is_included' => true, 'note' => null, 'sort_order' => 4],
            ['plan_id' => 3, 'label' => 'Advanced analytics & reporting', 'is_included' => true, 'note' => null, 'sort_order' => 5],
            ['plan_id' => 3, 'label' => 'Priority support', 'is_included' => true, 'note' => null, 'sort_order' => 6],
            ['plan_id' => 3, 'label' => 'Custom branding', 'is_included' => true, 'note' => null, 'sort_order' => 7],
            ['plan_id' => 3, 'label' => 'API access', 'is_included' => false, 'note' => 'Upgrade to Enterprise', 'sort_order' => 8],

            // Enterprise Plan Features
            ['plan_id' => 4, 'label' => 'Unlimited appointments', 'is_included' => true, 'note' => null, 'sort_order' => 1],
            ['plan_id' => 4, 'label' => 'Enterprise patient management', 'is_included' => true, 'note' => null, 'sort_order' => 2],
            ['plan_id' => 4, 'label' => '24/7 dedicated support', 'is_included' => true, 'note' => null, 'sort_order' => 3],
            ['plan_id' => 4, 'label' => 'Advanced appointment scheduling', 'is_included' => true, 'note' => null, 'sort_order' => 4],
            ['plan_id' => 4, 'label' => 'Advanced analytics & reporting', 'is_included' => true, 'note' => null, 'sort_order' => 5],
            ['plan_id' => 4, 'label' => 'Priority support', 'is_included' => true, 'note' => null, 'sort_order' => 6],
            ['plan_id' => 4, 'label' => 'Custom branding', 'is_included' => true, 'note' => null, 'sort_order' => 7],
            ['plan_id' => 4, 'label' => 'Full API access', 'is_included' => true, 'note' => null, 'sort_order' => 8],
        ];

        foreach ($features as $feature) {
            DB::table('plan_features')->insert($feature);
        }
    }
}
