<?php

namespace App\Interfaces\Subscription;

use App\Models\Subscription;
use App\Models\User;

interface SubscriptionServiceInterface
{
    public function subscribe(User $user, int $subscriptionId);
    public function unsubscribe(User $user);
    public function getSubscription(int $subscriptionId);
}

