<?php

namespace App\Interfaces\Subscription;

use App\DTO\SubscriptionDTO;
use App\Models\User;

interface SubscriptionServiceInterface
{
    public function subscribe(User $user, SubscriptionDTO $subscriptionDTO);
}
