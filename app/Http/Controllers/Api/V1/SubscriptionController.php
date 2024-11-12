<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\SubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Interfaces\Subscription\SubscriptionServiceInterface;
use App\Models\Subscription;
use App\Services\PaymentService\PaymentService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $subscriptionService;
    private $paymentService;

    public function __construct(SubscriptionServiceInterface $subscriptionService, PaymentService $paymentService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->paymentService = $paymentService;
    }

    /**
     * Получить список всех доступных подписок.
     */
    public function getAllSubscriptions()
    {
        $subscriptions = $this->subscriptionService->getAllSubscriptions();
        return response()->json(['data' => $subscriptions], 200);
    }

    /**
     * Получить список всех подписок текущего пользователя.
     */
    public function userSubscriptions()
    {
        $user = auth()->user();
        $subscriptions = $this->subscriptionService->getUserSubscriptions($user);
        return response()->json(['data' => $subscriptions], 200);
    }

    /**
     * Создать новую подписку.
     */
    public function storeSubscription(SubscriptionRequest $request)
    {
        $subscriptionResult = $this->subscriptionService->storeSubscription($request->all());

        return response()->json([
            'message' => __('messages.subscription_created'),
            'data' => new SubscriptionResource($subscriptionResult)
        ], 201);
    }

    /**
     * Обновить существующую подписку.
     */

    public function updateSubscription(Request $request, Subscription $subscription)
    {
        $updatedSubscription = $this->subscriptionService->updateSubscription($subscription, $request->all());

        return response()->json(['message' => __('messages.subscription_updated')], 200);
    }

    /**
     * Удалить указанную подписку.
     */
    public function destroySubscription($subscriptionId)
    {
        try {
            $subscription = Subscription::find($subscriptionId);
            if (!$subscription) {
                return response()->json(['message' => __('messages.subscription_not_found')], 404);
            }
            $this->subscriptionService->deleteSubscription($subscription);

            return response()->json(['message' => __('messages.subscription_deleted')], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }


    /**
     * Оформление подписки.
     * 
     * TODO нужно добавить
     *      сейчас подписка добавляется в бд даже если платеж не прошел. Нужо будет реализовать полноценную обработку добавив очереди 
     *      вместе с отправкой уведомления пользователя при получении вебхука от платежной системы
     */
    public function subscribeToSubscription(int $subscriptionId)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Пользователь не авторизован'], 401);
        }

        $subscriptionResult = $this->subscriptionService->subscribeToSubscription($user, $subscriptionId);
        if (!$subscriptionResult['success']) {
            return response()->json(['message' => $subscriptionResult['message']], $subscriptionResult['status']);
        }

        $payment = $this->paymentService->createPayment($subscriptionId);
        return response()->json(['payment_url' => $payment->getConfirmation()->getConfirmationUrl()], 200);
    }

    /**
     * Отмена подписки
     * 
     * TODO.
     */
    public function unsubscribeFromSubscription()
    {
        $user = auth()->user();
        $result = $this->subscriptionService->unsubscribeFromSubscription($user);
        return response()->json(['message' => $result['message']], $result['status']);
    }

    /**
     * Обработка успешного платежа.
     */
    public function successPayment(Request $request, $paymentId)
    {
        $url = "https://api.yookassa.ru/v3/payments/{$paymantId}/capture";
        // TO DO: Обработать успешный платеж.
    }

    /**
     * Отмена платежа.
     */
    public function cancelPayment(Request $request, $paymentId)
    {
        $url = "https://api.yookassa.ru/v3/payments/{$paymantId}/cancel";
        // TO DO: Обработать отмену платежа.
    }

    /**
     * Вебхук для обработки событий платежей. Сюда приходят уведомления от платежной системы
     */
    public function paymentsWebhook(Request $request)
    {
        $event = $request->all();

        if ($event['event'] === 'payment.succeeded') {
            $result = $this->paymentService->handleWebhook($event);
            return response()->json($result);
        }

        return response()->json(['status' => 'unhandled event']);
    }
}
