<?php

namespace App\Services\Application\Comment;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommentService
{
    private array $typeMap = [
        'post' => Post::class,
        'posts' => Post::class,
        'book' => Book::class,
        'books' => Book::class,
        'chapter' => Chapter::class,
        'chapters' => Chapter::class,
        Post::class => Post::class,
        Book::class => Book::class,
        Chapter::class => Chapter::class,
    ];

    public function paginate(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = Comment::withCount('likes')
            ->with(['user', 'commentable', 'replies.user'])
            ->whereNull('parent_id')
            ->latest();

        $type = $this->resolveType($filters['commentable_type'] ?? null);

        if ($type && !empty($filters['commentable_id'])) {
            $query->where('commentable_type', $type)
                ->where('commentable_id', $filters['commentable_id']);
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?Comment
    {
        return Comment::find($id);
    }

    public function make(array $data, int $userId): Comment
    {
        return new Comment([
            'content' => $data['content'],
            'user_id' => $userId,
            'commentable_id' => $data['commentable_id'],
            'commentable_type' => $this->resolveType($data['commentable_type']),
            'parent_id' => $data['parent_id'] ?? null,
        ]);
    }

    public function save(Comment $comment): Comment
    {
        $comment->save();
        $comment->load(['user', 'replies.user']);

        return $comment;
    }

    public function update(Comment $comment, array $data): Comment
    {
        $comment->update(['content' => $data['content']]);

        return $comment;
    }

    public function delete(Comment $comment): void
    {
        $comment->delete();
    }

    public function resolveType(?string $type): ?string
    {
        if (!$type) {
            return null;
        }

        return $this->typeMap[$type] ?? null;
    }
}
