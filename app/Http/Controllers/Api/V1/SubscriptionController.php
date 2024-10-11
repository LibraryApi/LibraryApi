<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\SubscriptionRequest;
use App\DTO\SubscriptionDTO;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService\SubscriptionService;


class SubscriptionController extends Controller
{
    public function index()
    {
        return Subscription::all();
    }

    public function subscribe(SubscriptionRequest $request, SubscriptionService $subscriptionService)
    {
        $user = User::findOrFail(auth()->user()->id);

        $subscriptionDTO = new SubscriptionDTO($request->validated());
        $result = $subscriptionService->subscribe($user, $subscriptionDTO);

        if ($result['success']) {
            return response()->json($result['subscription'], 201);
        } else {
            return response()->json(['message' => $result['message']], $result['status']);
        }
    }

    public function unsubscribe(SubscriptionService $subscriptionService)
    {
        $user = User::findOrFail(auth()->user()->id);
        $result = $subscriptionService->unsubscribe($user);

        return response()->json($result, $result['status']);
    }
}
