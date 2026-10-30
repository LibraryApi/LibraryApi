<?php

namespace App\Http\Resources\Subscription;

use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SubscriptionCollection extends ResourceCollection
{
    public $collects = SubscriptionResource::class;

    public function toArray($request): array
    {
        return ['data' => $this->collection];
    }
}
