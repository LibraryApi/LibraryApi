<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $viewer = $request->user('sanctum') ?? $request->user();
        $isOwnProfile = $viewer && (int) $viewer->id === (int) $this->id;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar_url,
            'banner_image' => $this->banner_url,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'city' => $this->city,
            'telegram' => $this->telegram,
            'phone' => $this->phone,
            'github' => $this->github,
            'vk' => $this->vk,
            'habr' => $this->habr,
            'about' => $this->about,
            'roles' => $this->getRoleNames()->values()->all(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_own_profile' => $isOwnProfile,
            'posts_count' => $this->whenCounted('posts'),
            'books_count' => $this->whenCounted('books'),
            'comments_count' => $this->whenCounted('comments'),
            'followers_count' => $this->whenCounted('followers'),
            'following_count' => $this->whenCounted('following'),
            'posts' => PostResource::collection($this->whenLoaded('posts')),
            'liked_posts' => PostResource::collection($this->whenLoaded('likedPosts')),
            'books' => $this->whenLoaded('books', fn () => $this->mapBooks($this->books)),
            'liked_books' => $this->whenLoaded('likedBooks', fn () => $this->mapBooks($this->likedBooks)),
            'followers' => $this->whenLoaded('followers', function () {
                return $this->followers->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar' => $user->avatar_url,
                        'city' => $user->city,
                    ];
                })->values()->all();
            }),
            'following' => $this->whenLoaded('following', function () {
                return $this->following->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar' => $user->avatar_url,
                        'city' => $user->city,
                    ];
                })->values()->all();
            }),
            'is_following' => $this->when(
                $this->relationLoaded('followers') && $viewer !== null,
                function () use ($viewer) {
                    return (int) $viewer->id !== (int) $this->id && $viewer->isFollowing($this->resource);
                }
            ),
        ];
    }

    private function mapBooks($books): array
    {
        return $books->map(function ($book) {
            $coverImage = $book->cover_image;

            if ($coverImage && !str_starts_with($coverImage, 'http://') && !str_starts_with($coverImage, 'https://')) {
                $coverImage = url($coverImage);
            }

            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'cover_image' => $coverImage,
                'description' => $book->description,
                'rating' => $book->rating,
                'chapters_count' => (int) ($book->chapters_count ?? 0),
                'likes_count' => (int) ($book->likes_count ?? 0),
                'comments_count' => (int) ($book->comments_count ?? 0),
                'views_count' => (int) ($book->views_count ?? 0),
                'categories' => $book->relationLoaded('categories')
                    ? $book->categories->map(fn ($category) => [
                        'id' => $category->id,
                        'name' => $category->name,
                    ])->values()->all()
                    : [],
            ];
        })->values()->all();
    }

}
