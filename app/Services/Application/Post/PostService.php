<?php

namespace App\Services\Application\Post;

use App\DTO\Post\StorePostDTO;
use App\DTO\Post\UpdatePostDTO;
use App\Models\Post;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class PostService
{
    public function getPosts(array $filters, int $perPage)
    {
        Log::info("идет");
        $query = Post::query()->with(["user", "comments"]);

        if (isset($filters['author'])) {
            $query->whereHas('user', function ($userQuery) use ($filters) {
                $userQuery->where('name', $filters['author']);
            });
        }

        if (isset($filters['category'])) {
            $query->whereHas('categories', function ($categoryQuery) use ($filters) {
                $categoryQuery->where('name', $filters['category']);
            });
        }

        return $query->paginate($perPage);
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
        return Post::find($id);
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
}
