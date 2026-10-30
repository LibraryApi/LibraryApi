<?php

namespace App\Services\Application\Interaction;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Rating;

class InteractionService
{
    private array $targets = [
        'book' => Book::class,
        'books' => Book::class,
        'chapter' => Chapter::class,
        'chapters' => Chapter::class,
        'post' => Post::class,
        'posts' => Post::class,
        'comment' => Comment::class,
        'comments' => Comment::class,
    ];

    public function like(int $userId, string $type, int $id): ?array
    {
        $target = $this->target($type, $id);

        if (!$target) {
            return null;
        }

        $like = Like::firstOrCreate([
            'user_id' => $userId,
            'likeable_id' => $target->id,
            'likeable_type' => $target->getMorphClass(),
        ]);

        return [
            'created' => $like->wasRecentlyCreated,
            'data' => [
                'liked' => true,
                'likes_count' => $target->likes()->count(),
            ],
        ];
    }

    public function unlike(int $userId, string $type, int $id): ?array
    {
        $target = $this->target($type, $id);

        if (!$target) {
            return null;
        }

        Like::where([
            'user_id' => $userId,
            'likeable_id' => $target->id,
            'likeable_type' => $target->getMorphClass(),
        ])->delete();

        return [
            'liked' => false,
            'likes_count' => $target->likes()->count(),
        ];
    }

    public function rate(int $userId, string $type, int $id, int $value): ?array
    {
        $target = $this->target($type, $id);

        if (!$target) {
            return null;
        }

        Rating::updateOrCreate(
            [
                'user_id' => $userId,
                'rateable_id' => $target->id,
                'rateable_type' => $target->getMorphClass(),
            ],
            ['value' => $value]
        );

        $average = round((float) $target->ratings()->avg('value'), 2);
        $this->updateAggregateRating($target, $average);

        return [
            'rating' => $average,
            'ratings_count' => $target->ratings()->count(),
        ];
    }

    public function unrate(int $userId, string $type, int $id): ?array
    {
        $target = $this->target($type, $id);

        if (!$target) {
            return null;
        }

        Rating::where([
            'user_id' => $userId,
            'rateable_id' => $target->id,
            'rateable_type' => $target->getMorphClass(),
        ])->delete();

        $average = $target->ratings()->avg('value');
        $this->updateAggregateRating($target, $average);

        return [
            'rating' => $average,
            'ratings_count' => $target->ratings()->count(),
        ];
    }

    private function target(string $type, int $id)
    {
        $class = $this->targets[$type] ?? null;

        return $class ? $class::find($id) : null;
    }

    private function updateAggregateRating($target, $average): void
    {
        if (in_array('rating', $target->getFillable(), true)) {
            $target->forceFill(['rating' => $average])->saveQuietly();
        }
    }
}
