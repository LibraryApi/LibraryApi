<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\SubscriptionRequest;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        //dd('hello');
        return Subscription::all();
    }

    public function subscribe(SubscriptionRequest $request)
    {
        $user = auth()->user();

        if ($user->subscription) {

            if ($user->subscription->access_level == $request->input('access_level')) {

                return response()->json(['message' => 'У вас уже есть подписка этого уровня доступа'], 422);
            } elseif ($this->isHigherAccessLevel($user->subscription->access_level, $request->input('access_level'))) {

                return response()->json(['message' => 'У вас уже оформлена подписка более высокого уровня'], 422);
            } else {

                $user->subscription->delete();
            }
        }

        $subscribe = $user->subscription()->create($request->all());

        return response()->json($subscribe, 201);
    }


    private function isHigherAccessLevel($currentLevel, $newLevel)
    {

        $accessLevels = [
            'basic' => 1,
            'premium' => 2,
        ];

        return  $accessLevels[$currentLevel] > $accessLevels[$newLevel];
    }



    public function unsubscribe(SubscriptionRequest $request)
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        if (!$subscription) {
            // Подписка не найдена
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        // Удаление подписки
        $subscription->delete();

        return response()->json(['message' => 'Unsubscribed successfully'], 200);
    }
}
