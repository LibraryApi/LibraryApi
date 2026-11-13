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
            ->select('users.id', 'users.name', 'users.avatar', 'users.city', 'users.about')
            ->whereHas('books')
            ->withCount('books')
            ->orderBy('users.name')
            ->get();

        $followingIds = $following->pluck('id')->all();
        $postsEnabled = (bool) config('features.posts_enabled', false);

        $feedPosts = $postsEnabled
            ? $this->postQuery()
                ->whereIn('user_id', $followingIds)
                ->latest()
                ->limit(6)
                ->get()
            : collect();

        $recommendedPosts = $postsEnabled
            ? $this->postQuery()
                ->latest()
                ->limit(6)
                ->get()
            : collect();

        $suggestedAuthors = User::query()
            ->whereHas('books')
            ->where('id', '!=', $user->id)
            ->when($followingIds !== [], function ($query) use ($followingIds) {
                $query->whereNotIn('id', $followingIds);
            })
            ->withCount(['books', 'followers'])
            ->orderByDesc('books_count')
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
                'posts_count' => $postsEnabled ? (int) $user->posts()->count() : 0,
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
