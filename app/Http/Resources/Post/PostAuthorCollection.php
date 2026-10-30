<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostAuthorCollection extends ResourceCollection
{
    public $collects = PostAuthorResource::class;

    public function toArray($request): array
    {
        return ['authors' => $this->collection];
    }
}
