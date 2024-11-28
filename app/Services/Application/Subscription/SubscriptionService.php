<?php

namespace App\Services\Application\Subscription;

use App\Models\User;
use App\Interfaces\Subscription\SubscriptionServiceInterface;
use App\Models\Subscription;

class SubscriptionService implements SubscriptionServiceInterface
{
    public function getAllSubscriptions()
    {
        return Subscription::all();
    }

    public function getUserSubscriptions(User $user)
    {
        return $user->subscriptions;
    }

    public function storeSubscription($subscriptionData)
    {
        return Subscription::create($subscriptionData);
    }

    public function updateSubscription(Subscription $subscription, $data)
    {
        $subscription->update($data);
        return $subscription;
    }

    public function deleteSubscription(Subscription $subscription)
    {
        if (!$subscription) {
            throw new \Exception('Подписка не найдена');
        }

        try {
            $subscription->delete();
        } catch (\Exception $e) {
            throw new \Exception('Ошибка при удалении подписки: ' . $e->getMessage());
        }
    }


    public function subscribeToSubscription(User $user, int $subscriptionId)
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

    public function unsubscribeFromSubscription(User $user)
    {
        $subscription = $user->subscriptions()->first();

        if (!$subscription) {
            return ['success' => false, 'message' => 'У пользователя нет больше оформленных подписок', 'status' => 404];
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
        return ['success' => false, 'message' => 'Подписки не найдены', 'status' => 404];
    }
}
