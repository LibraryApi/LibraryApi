<?php

namespace App\Http\Resources\Books;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'number' => $this->number,
            'characters' => $this->characters,
            'images' => $this->images,
            'comments_count' => $this->comments_count,
            'likes_count' => $this->likes_count,
            'views_count' => $this->views_count,
            'pages_count' => $this->whenCounted('pages'),
            'page_list' => $this->whenLoaded('pageList', function () {
                return $this->pageList->map(function ($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'number' => $page->number,
                    ];
                })->values()->all();
            }),
            'pages' => PageResource::collection($this->whenLoaded('pages')),
            'updated_at' => $this->updated_at,
        ];
    }
}
