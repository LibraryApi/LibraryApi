<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\PaymentWebhookRequest;
use App\Http\Requests\Subscription\SubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\Subscription\PaymentResource;
use App\Http\Resources\Subscription\StoredSubscriptionResource;
use App\Http\Resources\Subscription\SubscriptionCollection;
use App\Http\Resources\WebhookResource;
use App\Interfaces\Subscription\SubscriptionServiceInterface;
use App\Services\Application\Subscription\SubscriptionPurchaseService;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptions,
        private readonly SubscriptionPurchaseService $purchases
    ) {
    }

    public function getAllSubscriptions(): JsonResponse
    {
        return response()->json(new SubscriptionCollection($this->subscriptions->getAllSubscriptions()));
    }

    public function userSubscriptions(): JsonResponse
    {
        return response()->json(new SubscriptionCollection(
            $this->subscriptions->getUserSubscriptions(auth()->user())
        ));
    }

    public function storeSubscription(SubscriptionRequest $request): JsonResponse
    {
        $subscription = $this->subscriptions->storeSubscription($request->validated());

        return response()->json(new StoredSubscriptionResource([
            'message' => __('subscription.subscription_created'),
            'data' => $subscription,
        ]), 201);
    }

    public function updateSubscription(UpdateSubscriptionRequest $request, int $id): JsonResponse
    {
        $subscription = $this->subscriptions->find($id);

        if (!$subscription) {
            return response()->json(new ErrorResource(__('subscription.subscription_not_found')), 404);
        }

        $this->subscriptions->updateSubscription($subscription, $request->validated());

        return response()->json(new MessageResource(__('subscription.subscription_updated')));
    }

    public function destroySubscription(int $id): JsonResponse
    {
        $subscription = $this->subscriptions->find($id);

        if (!$subscription) {
            return response()->json(new ErrorResource(__('subscription.subscription_not_found')), 404);
        }

        $this->subscriptions->deleteSubscription($subscription);

        return response()->json(new MessageResource(__('subscription.subscription_deleted')));
    }

    public function subscribeToSubscription(int $subscriptionId): JsonResponse
    {
        $result = $this->purchases->purchase(auth()->user(), $subscriptionId);

        if (!$result['success']) {
            return response()->json(new ErrorResource($result['message']), $result['status']);
        }

        return response()->json(new PaymentResource(['payment_url' => $result['payment_url']]));
    }

    public function unsubscribeFromSubscription(): JsonResponse
    {
        $result = $this->purchases->unsubscribe(auth()->user());

        if (!($result['success'] ?? false)) {
            return response()->json(new ErrorResource($result['message'] ?? 'Не удалось отменить подписку'), $result['status'] ?? 422);
        }

        return response()->json(new MessageResource($result['message']));
    }

    public function paymentsWebhook(PaymentWebhookRequest $request): JsonResponse
    {
        return response()->json(new WebhookResource($this->purchases->handleWebhook($request->validated())));
    }
}
