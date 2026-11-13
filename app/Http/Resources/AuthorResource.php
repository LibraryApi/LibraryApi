<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $viewer = $request->user('sanctum') ?? $request->user();
        $books = $this->relationLoaded('books') ? $this->books : collect();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar_url,
            'city' => $this->city,
            'about' => $this->about,
            'books_count' => (int) ($this->books_count ?? $books->count()),
            'followers_count' => (int) ($this->followers_count ?? 0),
            'is_following' => $viewer ? $viewer->isFollowing($this->resource) : false,
            'subscribed_at' => $this->pivot?->created_at?->toIso8601String(),
            'books' => $books->take(3)->map(static function ($book) {
                $cover = $book->cover_image;

                if ($cover && !str_starts_with($cover, 'http://') && !str_starts_with($cover, 'https://')) {
                    $cover = url($cover);
                }

                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'cover_image' => $cover,
                ];
            })->values()->all(),
        ];
    }
}
