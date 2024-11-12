<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    public function run()
    {
        $subscriptions = [
            [
                'name' => 'Базовая подписка',
                'price' => 1000,
                'duration_months' => 1,
                'access_level' => 'base',
            ],
            [
                'name' => 'Премиум подписка',
                'price' => 5000,
                'duration_months' => 3,
                'access_level' => 'premium',
            ],
            [
                'name' => 'Годовая подписка',
                'price' => 15000,
                'duration_months' => 12,
                'access_level' => 'premium',
            ],
        ];

        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }
}
