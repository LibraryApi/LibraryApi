<?php

namespace App\Http\Resources;

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
        $tags = [];

        if ($this->book && $this->book->relationLoaded('categories')) {
            $tags = $this->book->categories->map(static fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
            ])->values()->all();
        }

        $viewer = $request->user('sanctum') ?? $request->user();

        $isLiked = false;
        if ($viewer) {
            $isLiked = $this->likes()
                ->where('user_id', $viewer->id)
                ->exists();
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->user),
            'comments' => null,
            'title' => $this->title,
            'content' => $this->content,
            'book_id' => $this->book_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'likes_count' => (int) ($this->likes_count ?? 0),
            'comments_count' => (int) ($this->comments_count ?? 0),
            'views_count' => (int) ($this->views_count ?? 0),
            'is_liked' => $isLiked,
            'average_rating' => round((float) ($this->ratings_avg_value ?? 0), 2),
            'ratings_count' => (int) ($this->ratings_count ?? 0),
            'tags' => $tags,
            'categories' => $tags,
            'image' => $this->book?->cover_image,
        ];
    }
}
