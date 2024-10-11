<?php

namespace App\Services\SubscriptionService;

use App\DTO\SubscriptionDTO;
use App\Models\User;
use App\Interfaces\Subscription\SubscriptionServiceInterface;
use App\Repositories\Api\V1\SubscriptionRepository;

class SubscriptionService implements SubscriptionServiceInterface
{
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function subscribe(User $user, SubscriptionDTO $subscriptionDTO)
    {
        $existingSubscription = $user->subscription;

        if ($existingSubscription) {
            if ($existingSubscription->access_level === $subscriptionDTO->access_level) {
                return ['success' => false, 'message' => 'У вас уже есть подписка этого уровня доступа', 'status' => 422];
            } elseif ($this->isHigherAccessLevel($existingSubscription->access_level, $subscriptionDTO->access_level)) {
                return ['success' => false, 'message' => 'У вас уже оформлена подписка более высокого уровня', 'status' => 422];
            } else {
                $existingSubscription->delete();
            }
        }

        $subscription = $this->subscriptionRepository->createSubscription($user, $subscriptionDTO);

        return ['success' => true, 'subscription' => $subscription];
    }

    private function isHigherAccessLevel(string $currentLevel, string $newLevel): bool
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

        $this->subscriptionRepository->deleteSubscription($subscription);
        return ['success' => true, 'message' => 'Вы успешно отписались', 'status' => 200];
    }
}