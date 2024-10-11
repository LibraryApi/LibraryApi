<?php

namespace App\Repositories\Api\V1;

use App\DTO\SubscriptionDTO;
use App\Models\Subscription;
use App\Models\User;

class SubscriptionRepository
{
    public function createSubscription(User $user, SubscriptionDTO $subscriptionDTO): Subscription
    {
        return $user->subscription()->create([
            'name' => $subscriptionDTO->name,
            'price' => $subscriptionDTO->price,
            'duration' => $subscriptionDTO->duration,
            'access_level' => $subscriptionDTO->access_level,
        ]);
    }

    public function deleteSubscription(Subscription $subscription): void
    {
        $subscription->delete();
    }
}