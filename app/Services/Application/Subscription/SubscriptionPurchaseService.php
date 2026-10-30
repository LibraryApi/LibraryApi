<?php

namespace App\Services\Application\Subscription;

use App\Interfaces\Subscription\SubscriptionServiceInterface;
use App\Models\User;
use App\Services\WrapperServices\PaymentService\PaymentService;
use Throwable;

class SubscriptionPurchaseService
{
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptions,
        private readonly PaymentService $payments
    ) {
    }

    public function purchase(User $user, int $subscriptionId): array
    {
        $result = $this->subscriptions->subscribeToSubscription($user, $subscriptionId);

        if (!($result['success'] ?? false)) {
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Не удалось оформить подписку',
                'status' => $result['status'] ?? 422,
            ];
        }

        try {
            $payment = $this->payments->createPayment($subscriptionId);
            $url = $payment->getConfirmation()?->getConfirmationUrl();

            if (!$url) {
                throw new \RuntimeException('Платежная система не вернула ссылку на оплату');
            }

            return ['success' => true, 'payment_url' => $url];
        } catch (Throwable $exception) {
            return [
                'success' => false,
                'message' => 'Не удалось создать платеж: ' . $exception->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function unsubscribe(User $user): array
    {
        return $this->subscriptions->unsubscribeFromSubscription($user);
    }

    public function handleWebhook(array $event): array
    {
        if (($event['event'] ?? null) !== 'payment.succeeded') {
            return ['status' => 'unhandled event'];
        }

        return $this->payments->handleWebhook($event);
    }
}
