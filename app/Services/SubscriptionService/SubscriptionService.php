<?php

namespace App\Services\SubscriptionService;

use App\Models\User;
use App\Interfaces\Subscription\SubscriptionServiceInterface;
use App\Models\Subscription;

class SubscriptionService implements SubscriptionServiceInterface
{
    public function subscribe(User $user, int $subscriptionId)
    {
        $subscription = $this->getSubscription($subscriptionId);

        if (isset($subscription['success']) && !$subscription['success']) {
            return $subscription;
        }

        $existingSubscriptions = $user->subscriptions;
        $hasBaseSubscription = $existingSubscriptions->contains('access_level', 'base');
        $hasPremiumSubscription = $existingSubscriptions->contains('access_level', 'premium');

        if ($existingSubscriptions->where('id', $subscription->id)->isNotEmpty()) {
            return ['success' => false, 'message' => 'У вас уже есть такая подписка', 'status' => 422];
        }

        if ($hasPremiumSubscription && $subscription->access_level === 'base') {
            return ['success' => false, 'message' => 'Нельзя приобрести базовую подписку, имея премиум подписку', 'status' => 422];
        }

        if ($hasPremiumSubscription && $subscription->access_level === 'premium') {
            return ['success' => false, 'message' => 'Нельзя продли премиум подписку', 'status' => 422];
        }

        return $this->createNewSubscription($user, $subscription);
    }

    private function createNewSubscription(User $user, $subscription)
    {
        $user->subscriptions()->attach($subscription->id, [
            'start_date' => now(),
            'end_date' => now()->addMonths($subscription->duration_months)
        ]);

        return ['success' => true, 'message' => 'Подписка успешно оформлена', 'subscription' => $subscription, 'status' => 200];
    }

    public function unsubscribe(User $user)
    {
        $subscription = $user->subscriptions()->first();

        if (!$subscription) {
            return ['success' => false, 'message' => 'Подписка не найдена', 'status' => 404];
        }

        $user->subscriptions()->detach($subscription->id);

        return ['success' => true, 'message' => 'Вы успешно отписались', 'status' => 200];
    }

    public function getSubscription(int $subscriptionId)
    {
        $subscription = Subscription::find($subscriptionId);
        if ($subscription) {
            return $subscription;
        }
        return ['success' => false, 'message' => 'Подписка не найдена', 'status' => 404];
    }

    public function getUserSubscriptions(User $user)
    {
        return $user->subscriptions;
    }
}
