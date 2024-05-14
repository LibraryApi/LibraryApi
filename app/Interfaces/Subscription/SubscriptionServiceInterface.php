<?php

namespace App\Interfaces\Subscription;

use App\Models\User;

interface SubscriptionServiceInterface
{
    public function subscribe(User $user, array $data);
}
