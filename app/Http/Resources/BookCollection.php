<?php

namespace App\Http\Resources;

use App\Http\Resources\Books\BookResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BookCollection extends ResourceCollection
{
    public $collects = BookResource::class;

    public function toArray($request): array
    {
        return [
            'total_books' => $this->resource->total(),
            'per_page' => $this->resource->perPage(),
            'current_page' => $this->resource->currentPage(),
            'last_page' => $this->resource->lastPage(),
            'books' => $this->collection,
        ];
    }
}
