<?php

namespace App\Services\Wrappers;

use App\DTO\PostDTO;
use App\Models\Book;
use App\Models\Post;
use App\Repositories\Api\V1\PostRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostService
{
    protected PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function index($perPage, $filters)
    {
        return $this->postRepository->allAndSort($perPage, $filters);
    }

    public function store(PostDTO $postDTO)
    {
        if ($postDTO->bookId && !Book::find($postDTO->bookId)) {
            throw new \Exception('Книга не найдена');
        }

        Gate::authorize('create', Post::class);

        $post = $this->postRepository->create($postDTO, Auth::id());

        return $post;
    }

    public function show(string $id)
    {
        $post = $this->postRepository->find($id);
        if (!$post) {
            throw new \Exception('Пост не найден');
        }
        return $post;
    }

    public function update(Post $post, PostDTO $postDTO)
    {
        if ($postDTO->bookId && !Book::find($postDTO->bookId)) {
            throw new \Exception('Книга не найдена');
        }

        Gate::authorize('update', $post);

        $this->postRepository->update($post, $postDTO);
        return $post;
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);
        return $this->postRepository->delete($post);
    }
}
