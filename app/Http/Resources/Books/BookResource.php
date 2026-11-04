<?php

namespace App\Http\Resources\Books;

use App\Http\Resources\UserResource;
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
        $coverImage = $this->cover_image;

        if ($coverImage && !str_starts_with($coverImage, 'http://') && !str_starts_with($coverImage, 'https://')) {
            $coverImage = url($coverImage);
        }

        // Оценка текущего пользователя (1–5), чтобы звёзды не «сбрасывались» после перезагрузки
        $viewer = $request->user('sanctum') ?? $request->user();
        $userRating = 0;

        if ($viewer) {
            if ($this->relationLoaded('ratings')) {
                $own = $this->ratings->firstWhere('user_id', $viewer->id);
                $userRating = $own ? (int) $own->value : 0;
            } else {
                $userRating = (int) ($this->ratings()->where('user_id', $viewer->id)->value('value') ?? 0);
            }
        }

        $inWishlist = false;
        if ($viewer) {
            $inWishlist = \App\Models\Wishlist::where('user_id', $viewer->id)
                ->where('book_id', $this->id)
                ->exists();
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'cover_image' => $coverImage,
            'author_bio' => $this->author_bio,
            'language' => $this->language,
            'rating' => $this->rating,
            'average_rating' => round((float) ($this->ratings_avg_value ?? 0), 2),
            'ratings_count' => (int) ($this->ratings_count ?? $this->ratings()->count()),
            'user_rating' => $userRating,
            'in_wishlist' => $inWishlist,
            'number_of_pages' => $this->number_of_pages,
            'is_published' => (bool) $this->is_published,
            'comments_count' => (int) ($this->comments_count ?? 0),
            'likes_count' => (int) ($this->likes_count ?? 0),
            'views_count' => (int) ($this->views_count ?? 0),
            'chapters_count' => $this->whenCounted('chapters'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                    ];
                })->values()->all();
            }),
            'chapters' => $this->whenLoaded('chapters', function () {
                return $this->chapters->map(function ($chapter) {
                    return [
                        'id' => $chapter->id,
                        'title' => $chapter->title,
                        'number' => $chapter->number,
                        'duration' => $chapter->duration,
                        'is_read' => (bool) ($chapter->is_read ?? false),
                    ];
                })->values()->all();
            }),
        ];
    }
}
