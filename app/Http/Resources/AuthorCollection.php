<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AuthorCollection extends ResourceCollection
{
    public $collects = AuthorResource::class;

    public function toArray($request): array
    {
        return [
            'authors' => $this->collection,
            'total' => $this->collection->count(),
        ];
    }
}
