<?php

namespace App\Services\WrapperServices\PaymentService;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;

class PaymentService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth(config('yookassa.shop_id'), config('yookassa.secret_key'));
    }

    public function createPayment(int $subscriptionId)
{
    try {
        $subscription = Subscription::findOrFail($subscriptionId);

        $payment = $this->client->createPayment([
            'amount' => [
                'value' => $subscription->price,
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => config('yookassa.return_url'),
            ],
            'capture' => true,
            'description' => 'Subscription payment for ' . $subscription->name,
        ], uniqid('', true));

        $user = User::find(1);

        $paymentRecord = Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId,
            'payment_id' => $payment->id, 
            'amount' => $subscription->price,
            'status' => $payment->status,
        ]);

        return $payment;
    } catch (\Exception $e) {
        return response()->json(['error' => 'Не удалось создать платеж: ' . $e->getMessage()], 500);
    }
}


public function handleWebhook(array $event)
{

    if ($event['event'] === 'payment.succeeded') {
        $paymentId = $event['object']['id'];
        
        $payment = Payment::where('payment_id', $paymentId)->first();
        
        if ($payment) {
            $payment->status = 'succeeded';
            $payment->save();
        } else {

            Log::error('Payment not found for ID: ' . $paymentId);
        }
    }

    return ['status' => 'ok'];
}

}
