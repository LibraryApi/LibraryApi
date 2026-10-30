<?php

namespace App\Http\Resources;

use App\Http\Resources\Comments\CommentResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentCollection extends ResourceCollection
{
    public $collects = CommentResource::class;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'total' => $this->resource->total(),
            'per_page' => $this->resource->perPage(),
            'current_page' => $this->resource->currentPage(),
            'last_page' => $this->resource->lastPage(),
        ];
    }
}
