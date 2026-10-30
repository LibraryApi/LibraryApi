<?php

namespace App\Services\Application\Post;

use App\DTO\Post\StorePostDTO;
use App\DTO\Post\UpdatePostDTO;
use App\Models\Book;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PostService
{
    public function getPosts(array $filters, int $perPage)
    {
        $query = $this->baseQuery();
        $postMorph = (new Post())->getMorphClass();

        $hasValue = static fn (string $key): bool => isset($filters[$key])
            && $filters[$key] !== ''
            && $filters[$key] !== null
            && $filters[$key] !== [];

        if ($hasValue('search')) {
            $search = trim((string) $filters['search']);

            if ($search !== '') {
                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery
                        ->where('posts.title', 'like', "%{$search}%")
                        ->orWhere('posts.content', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }
        }

        if ($hasValue('author_id')) {
            $query->where('posts.user_id', (int) $filters['author_id']);
        }

        if ($hasValue('author')) {
            $author = trim((string) $filters['author']);

            if ($author !== '') {
                $query->whereHas('user', function (Builder $userQuery) use ($author) {
                    $userQuery->where('name', 'like', "%{$author}%");
                });
            }
        }

        if ($hasValue('category_ids')) {
            $categoryIds = array_values(array_filter(array_map(
                'intval',
                is_array($filters['category_ids']) ? $filters['category_ids'] : explode(',', (string) $filters['category_ids'])
            )));

            if ($categoryIds !== []) {
                $query->whereHas('book.categories', function (Builder $categoryQuery) use ($categoryIds) {
                    $categoryQuery->whereIn('categories.id', $categoryIds);
                });
            }
        }

        if ($hasValue('category')) {
            $query->whereHas('book.categories', function (Builder $categoryQuery) use ($filters) {
                $categoryQuery->where('categories.name', $filters['category']);
            });
        }

        if ($hasValue('tags')) {
            $tags = is_array($filters['tags']) ? $filters['tags'] : explode(',', (string) $filters['tags']);
            $tags = array_values(array_filter(array_map(static fn ($tag) => trim((string) $tag), $tags)));

            if ($tags !== []) {
                $query->whereHas('book.categories', function (Builder $categoryQuery) use ($tags) {
                    $categoryQuery->whereIn('categories.name', $tags);
                });
            }
        }

        $timezone = $filters['timezone'] ?? config('app.timezone');

        if ($hasValue('startDate')) {
            $query->where(
                'posts.created_at',
                '>=',
                Carbon::parse($filters['startDate'], $timezone)->startOfDay()->utc()
            );
        }

        if ($hasValue('endDate')) {
            $query->where(
                'posts.created_at',
                '<=',
                Carbon::parse($filters['endDate'], $timezone)->endOfDay()->utc()
            );
        }

        $minLikes = (int) ($filters['minLikes'] ?? 0);
        $maxLikes = (int) ($filters['maxLikes'] ?? 0);

        if ($minLikes > 0) {
            $query->has('likes', '>=', $minLikes);
        }

        if ($maxLikes > 0) {
            $query->has('likes', '<=', $maxLikes);
        }

        $minRating = (float) ($filters['minRating'] ?? 0);
        $maxRating = (float) ($filters['maxRating'] ?? 5);

        if ($minRating > 0 || $maxRating < 5) {
            $query->whereIn('posts.id', function ($ratingQuery) use ($postMorph, $minRating, $maxRating) {
                $ratingQuery
                    ->select('ratings.rateable_id')
                    ->from('ratings')
                    ->where('ratings.rateable_type', $postMorph)
                    ->groupBy('ratings.rateable_id');

                if ($minRating > 0) {
                    $ratingQuery->havingRaw('avg(ratings.value) >= ?', [$minRating]);
                }

                if ($maxRating < 5) {
                    $ratingQuery->havingRaw('avg(ratings.value) <= ?', [$maxRating]);
                }
            });
        }

        $this->applySorting($query, $filters, $postMorph);

        return $query->paginate($perPage)->withQueryString();
    }

    public function getAuthors(string $search = '', int $limit = 100): array
    {
        return User::query()
            ->whereHas('posts')
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->withCount('posts')
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name'])
            ->map(static fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'posts_count' => $user->posts_count,
            ])
            ->values()
            ->all();
    }

    public function createPost(StorePostDTO $data)
    {
        if ($data->book_id && !Book::find($data->book_id)) {
            return null;
        }

        $post = new Post([
            'title' => $data->title,
            'content' => $data->content,
            'book_id' => $data->book_id,
            'user_id' => auth()->id(),
        ]);

        $post->save();

        return $post;
    }

    public function getPostById(int $id)
    {
        return $this->baseQuery()->find($id);
    }

    public function updatePost(UpdatePostDTO $data, int $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return null;
        }

        $post->update([
            'title' => $data->title ?? $post->title,
            'content' => $data->content ?? $post->content,
            'book_id' => $data->book_id ?? $post->book_id,
        ]);

        return $post;
    }

    public function deletePost(int $id): bool
    {
        $post = Post::find($id);
        if (!$post) {
            return false;
        }

        $post->delete();

        return true;
    }

    private function baseQuery(): Builder
    {
        return Post::query()
            ->with([
                'user',
                'book' => function ($query) {
                    $query->with('categories')->select('id', 'title', 'cover_image');
                },
            ])
            ->withCount(['likes', 'comments', 'ratings'])
            ->withAvg('ratings', 'value');
    }

    private function applySorting(Builder $query, array $filters, string $postMorph): void
    {
        $sort = $filters['sort'] ?? null;
        $hasSort = false;

        if ($sort === 'date_desc' || $sort === 'date_asc') {
            $query->orderBy('posts.created_at', $sort === 'date_asc' ? 'asc' : 'desc');
            $hasSort = true;
        } elseif ($sort === 'title_asc' || $sort === 'title_desc') {
            $query->orderBy('posts.title', $sort === 'title_asc' ? 'asc' : 'desc');
            $hasSort = true;
        } elseif ($sort === 'likes_desc' || $sort === 'likes_asc') {
            $direction = $sort === 'likes_asc' ? 'asc' : 'desc';
            $query->orderByRaw(
                "(select count(*) from likes where likes.likeable_type = ? and likes.likeable_id = posts.id) {$direction}",
                [$postMorph]
            );
            $hasSort = true;
        } elseif ($sort === 'rating_desc' || $sort === 'rating_asc') {
            $direction = $sort === 'rating_asc' ? 'asc' : 'desc';
            $query->orderByRaw(
                "(select coalesce(avg(ratings.value), 0) from ratings where ratings.rateable_type = ? and ratings.rateable_id = posts.id) {$direction}",
                [$postMorph]
            );
            $hasSort = true;
        }

        if ($hasSort) {
            $query->orderByDesc('posts.id');

            return;
        }

        if (isset($filters['sortLikes']) && in_array($filters['sortLikes'], ['asc', 'desc'], true)) {
            $query->orderByRaw(
                '(select count(*) from likes where likes.likeable_type = ? and likes.likeable_id = posts.id) ' .
                ($filters['sortLikes'] === 'asc' ? 'asc' : 'desc'),
                [$postMorph]
            );
            $hasSort = true;
        }

        if (isset($filters['sortRating']) && in_array($filters['sortRating'], ['asc', 'desc'], true)) {
            $query->orderByRaw(
                '(select coalesce(avg(ratings.value), 0) from ratings where ratings.rateable_type = ? and ratings.rateable_id = posts.id) ' .
                ($filters['sortRating'] === 'asc' ? 'asc' : 'desc'),
                [$postMorph]
            );
            $hasSort = true;
        }

        if (isset($filters['sortDate']) && in_array($filters['sortDate'], ['asc', 'desc'], true)) {
            $query->orderBy('posts.created_at', $filters['sortDate']);
            $hasSort = true;
        }

        if (isset($filters['sortName']) && in_array($filters['sortName'], ['asc', 'desc'], true)) {
            $query->orderBy('posts.title', $filters['sortName']);
            $hasSort = true;
        }

        if (!$hasSort) {
            $query->orderByDesc('posts.created_at')->orderByDesc('posts.id');
        } else {
            $query->orderByDesc('posts.id');
        }
    }
}
