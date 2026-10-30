<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FollowResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'is_following' => (bool) ($this->resource['is_following'] ?? false),
            'followers_count' => (int) ($this->resource['followers_count'] ?? 0),
            'following_count' => (int) ($this->resource['following_count'] ?? 0),
        ];
    }
}
