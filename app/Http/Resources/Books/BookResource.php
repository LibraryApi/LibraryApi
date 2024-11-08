<?php

namespace App\Http\Resources\Books;

use App\Http\Resources\ImageResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
            "user_id" => new UserResource($this->whenLoaded('user')),
            "images" => ImageResource::collection($this->images),
            'cover_image' => $this->cover_image,
            'author_bio' => $this->author_bio,
            'language' => $this->language,
            'rating' => $this->rating,
            'number_of_pages' => $this->number_of_pages,
            'is_published' => $this->is_published,
            'comments_count' => $this->comments_count,
            'likes_count' => $this->likes_count,
            'views_count' => $this->views_count,
            'updated_at' => $this->updated_at,
        ];
    }
}
