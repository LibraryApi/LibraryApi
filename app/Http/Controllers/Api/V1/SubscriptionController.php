<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

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


    public function subscribe(int $subscriptionId)
    {

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Пользователь не авторизован'], 401);
        }

        $subscriptionResult = $this->subscriptionService->subscribe($user, $subscriptionId);
        if (!$subscriptionResult['success']) {
            return response()->json(['message' => $subscriptionResult['message']], $subscriptionResult['status']);
        }

        $payment = $this->paymentService->createPayment($subscriptionId);
        return response()->json(['payment_url' => $payment->getConfirmation()->getConfirmationUrl()], 200);
    }

    public function unsubscribe()
    {
        $user = auth()->user();

        $result = $this->subscriptionService->unsubscribe($user);
        return response()->json(['message' => $result['message']], $result['status']);
    }

    public function successPayment(Request $request, $paymentId)
    {
        $url = "https://api.yookassa.ru/v3/payments/{$paymantId}/capture";
        //TO DO
    }

    public function cancelPayment(Request $request, $paymentId)
    {
        $url = "https://api.yookassa.ru/v3/payments/{$paymantId}/cancel";
        //TO DO
    }

    public function paymentsWebhook(Request $request)
    {
        $event = $request->all();
    
        if ($event['event'] === 'payment.succeeded') {
            $result = $this->paymentService->handleWebhook($event);
    
            return response()->json($result);
        }

        return response()->json(['status' => 'unhandled event']);
    }
    

    public function index()
    {
        $subscriptions = Subscription::all();
        return response()->json(['data' => $subscriptions], 200);
    }

    public function userSubscriptions()
    {
        $user = auth()->user();
        $subscriptions = $user->subscriptions;
        return response()->json(['data' => $subscriptions], 200);
    }

    public function storeSubscription(Request $request)
    {
        $subscription = Subscription::create($request->all());
        return response()->json(['message' => 'Подписка успешно создана', 'data' => $subscription], 201);
    }

    public function updateSubscription(Request $request, Subscription $subscription)
    {
        $subscription->update($request->all());
        return response()->json(['message' => 'Подписка успешно обновлена', 'data' => $subscription], 200);
    }

    public function deleteSubscription(Subscription $subscription)
    {
        $subscription->delete();
        return response()->json(['message' => 'Подписка успешно удалена'], 200);
    }
}
