<?php

namespace App\Http\Resources\Comments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "author" => $this->user->name,
            "title" => $this->title,
            "content" => $this->content,
            "book_id" => $this->book_id,
            "created_at" => $this->created_at,
        ];
    }
}
