<?php

namespace App\Http\Resources;

use App\Http\Resources\Books\BookResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'user' => [
                'id' => $this->resource['user']->id,
                'name' => $this->resource['user']->name,
                'avatar' => $this->resource['user']->avatar_url,
            ],
            'following' => $this->resource['following']->map(static fn ($author) => [
                'id' => $author->id,
                'name' => $author->name,
                'avatar' => $author->avatar_url,
                'city' => $author->city,
            ])->values(),
            'suggested_authors' => $this->resource['suggested_authors']->map(static fn ($author) => [
                'id' => $author->id,
                'name' => $author->name,
                'avatar' => $author->avatar_url,
                'city' => $author->city,
                'posts_count' => (int) $author->posts_count,
            ])->values(),
            'posts' => PostResource::collection($this->resource['posts']),
            'recommended_posts' => PostResource::collection($this->resource['recommended_posts']),
            'new_books' => BookResource::collection($this->resource['new_books']),
            'popular_books' => BookResource::collection($this->resource['popular_books']),
            'plans' => SubscriptionResource::collection($this->resource['plans']),
            'stats' => $this->resource['stats'],
        ];
    }
}
