<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BookAuthorCollection extends ResourceCollection
{
    public $collects = BookAuthorResource::class;

    public function toArray($request): array
    {
        return ['authors' => $this->collection];
    }
}
