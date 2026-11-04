<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\Post\StorePostDTO;
use App\DTO\Post\UpdatePostDTO;
use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\AuthorPostRequest;
use App\Http\Requests\Post\IndexPostRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\Post\PostAuthorCollection;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Services\Application\Post\PostService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public function __construct(private readonly PostService $postService)
    {
    }

    public function index(IndexPostRequest $request): JsonResponse
    {
        $posts = $this->postService->getPosts($request->filters(), $request->perPage());

        return response()->json(new PostCollection($posts));
    }

    public function authors(AuthorPostRequest $request): JsonResponse
    {
        $authors = $this->postService->getAuthors($request->search(), $request->limit());

        return response()->json(new PostAuthorCollection($authors));
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->postService->createPost(StorePostDTO::fromRequest($request->validated()));

        if (!$post) {
            return response()->json(new ErrorResource('Указанная книга не найдена'), Response::HTTP_NOT_FOUND);
        }

        return response()->json(new PostResource($post), Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $post = $this->postService->getPostById($id);

        if (!$post) {
            return response()->json(new ErrorResource('Пост не найден'), Response::HTTP_NOT_FOUND);
        }

        return response()->json(new PostResource($post));
    }

    public function update(UpdatePostRequest $request, int $id): JsonResponse
    {
        $existing = Post::find($id);

        if (!$existing) {
            return response()->json(new ErrorResource('Пост не найден'), Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $existing);

        $post = $this->postService->updatePost(UpdatePostDTO::fromRequest($request->validated()), $id);

        if (!$post) {
            return response()->json(new ErrorResource('Пост не найден'), Response::HTTP_NOT_FOUND);
        }

        return response()->json(new PostResource($post));
    }

    public function destroy(int $id): JsonResponse
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(new ErrorResource('Пост не найден'), Response::HTTP_NOT_FOUND);
        }

        $this->authorize('delete', $post);

        $status = $this->postService->deletePost($id);

        if (!$status) {
            return response()->json(new ErrorResource('Пост не найден'), Response::HTTP_NOT_FOUND);
        }

        return response()->json(new MessageResource('Пост успешно удален'));
    }
}
