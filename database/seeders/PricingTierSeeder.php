<?php

namespace Database\Seeders;

use App\Models\PricingTier;
use Illuminate\Database\Seeder;

class PricingTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['min_calls' => 0, 'max_calls' => 9999, 'price_per_call' => 0.0750, 'order' => 1],
            ['min_calls' => 10000, 'max_calls' => 99999, 'price_per_call' => 0.0650, 'order' => 2],
            ['min_calls' => 100000, 'max_calls' => 999999, 'price_per_call' => 0.0500, 'order' => 3],
            ['min_calls' => 1000000, 'max_calls' => 9999999, 'price_per_call' => 0.0350, 'order' => 4],
            ['min_calls' => 10000000, 'max_calls' => null, 'price_per_call' => 0.0250, 'order' => 5],
        ];

        foreach ($tiers as $tier) {
            PricingTier::updateOrCreate(
                ['order' => $tier['order']],
                $tier
            );
        }
    }
}
