<?php

namespace App\Http\Resources\Subscription;

use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StoredSubscriptionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'message' => $this->resource['message'],
            'data' => new SubscriptionResource($this->resource['data']),
        ];
    }
}
