<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookAuthorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'name' => $this->resource['name'] ?? '',
            'books_count' => (int) ($this->resource['books_count'] ?? 0),
        ];
    }
}
