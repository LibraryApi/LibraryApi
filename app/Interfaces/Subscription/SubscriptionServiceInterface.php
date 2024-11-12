<?php

namespace App\Interfaces\Subscription;

use App\Models\Subscription;
use App\Models\User;

interface SubscriptionServiceInterface
{
    public function subscribeToSubscription(User $user, int $subscriptionId);
    public function unsubscribeFromSubscription(User $user);
    public function getSubscription(int $subscriptionId);

    public function storeSubscription($subscription);
    public function getUserSubscriptions(User $user);
    public function getAllSubscriptions();
    public function updateSubscription(Subscription $subscription, $data);
    public function deleteSubscription(Subscription $subscription);
}
