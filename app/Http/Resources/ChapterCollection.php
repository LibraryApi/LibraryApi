<?php

namespace App\Http\Resources;

use App\Http\Resources\Books\ChapterResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChapterCollection extends ResourceCollection
{
    public $collects = ChapterResource::class;

    public function toArray($request): array
    {
        return [
            'total_chapters' => $this->resource->total(),
            'per_page' => $this->resource->perPage(),
            'current_page' => $this->resource->currentPage(),
            'last_page' => $this->resource->lastPage(),
            'chapters' => $this->collection,
        ];
    }
}
