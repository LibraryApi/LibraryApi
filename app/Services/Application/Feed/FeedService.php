<?php

namespace App\Services\Application\Feed;

use App\Models\Book;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;

class FeedService
{
    public function get(User $user): array
    {
        $following = $user->following()
            ->select('users.id', 'users.name', 'users.avatar', 'users.city')
            ->orderBy('users.name')
            ->get();

        $followingIds = $following->pluck('id')->all();

        $feedPosts = $this->postQuery()
            ->whereIn('user_id', $followingIds)
            ->latest()
            ->limit(6)
            ->get();

        $recommendedPosts = $this->postQuery()
            ->latest()
            ->limit(6)
            ->get();

        $suggestedAuthors = User::query()
            ->whereHas('posts')
            ->where('id', '!=', $user->id)
            ->when($followingIds !== [], function ($query) use ($followingIds) {
                $query->whereNotIn('id', $followingIds);
            })
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->limit(4)
            ->get();

        $newBooks = $this->bookQuery()->latest()->limit(4)->get();
        $popularBooks = $this->bookQuery()
            ->orderByDesc('views_count')
            ->orderByDesc('likes_count')
            ->limit(4)
            ->get();

        $plans = Subscription::query()->orderBy('duration_months')->get();

        return [
            'user' => $user,
            'following' => $following,
            'suggested_authors' => $suggestedAuthors,
            'posts' => $feedPosts,
            'recommended_posts' => $recommendedPosts,
            'new_books' => $newBooks,
            'popular_books' => $popularBooks,
            'plans' => $plans,
            'stats' => [
                'following_count' => $following->count(),
                'posts_count' => (int) $user->posts()->count(),
                'books_count' => (int) $user->books()->count(),
            ],
        ];
    }

    private function postQuery()
    {
        return Post::query()
            ->with([
                'user',
                'book' => fn ($bookQuery) => $bookQuery->with('categories'),
            ])
            ->withCount(['likes', 'comments', 'ratings'])
            ->withAvg('ratings', 'value');
    }

    private function bookQuery()
    {
        return Book::query()
            ->with('categories')
            ->withCount(['chapters', 'likes', 'comments'])
            ->withAvg('ratings', 'value');
    }
}
