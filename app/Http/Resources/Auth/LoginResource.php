<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'user' => $this->resource['user']->only('name', 'email'),
            'token' => $this->resource['token'],
            'message' => $this->resource['message'],
        ];
    }
}
