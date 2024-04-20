<?php

namespace App\Services\SubscriptionService;

use App\Models\User;
use App\Interfaces\Subscription\SubscriptionServiceInterface;

class SubscriptionService implements SubscriptionServiceInterface
{
    public function subscribe(User $user, array $data)
    {
        $existingSubscription = $user->subscription;

        if ($existingSubscription) {
            if ($existingSubscription->access_level == $data['access_level']) {
                return ['success' => false, 'message' => 'У вас уже есть подписка этого уровня доступа', 'status' => 422];
            } elseif ($this->isHigherAccessLevel($existingSubscription->access_level, $data['access_level'])) {
                return ['success' => false, 'message' => 'У вас уже оформлена подписка более высокого уровня', 'status' => 422];
            } elseif (!$this->isHigherAccessLevel($existingSubscription->access_level, $data['access_level'])) {
                $existingSubscription->delete();
            }
        }

        $subscription = $user->subscription()->create($data);

        return ['success' => true, 'subscription' => $subscription];
    }



    private function isHigherAccessLevel($currentLevel, $newLevel)
    {
        $accessLevels = [
            'basic' => 1,
            'premium' => 2,
        ];

        return $accessLevels[$currentLevel] > $accessLevels[$newLevel];
    }

    public function unsubscribe(User $user)
    {
        $subscription = $user->subscription;

        if (!$subscription) {
            return ['success' => false, 'message' => 'Подписка не найдена', 'status' => 404];
        }

        $subscription->delete();
        return ['success' => true, 'message' => 'Вы успешно отписались', 'status' => 200];
    }
}
