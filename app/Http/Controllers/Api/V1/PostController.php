<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Services\Application\Post\PostService;
use App\DTO\Post\StorePostDTO;
use App\DTO\Post\UpdatePostDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['author', 'category']);
        $perPage = $request->input('per_page', 10);

        $posts = $this->postService->getPosts($filters, $perPage);

        return response()->json([
            'total_posts' => $posts->total(),
            'per_page' => $posts->perPage(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'posts' => PostResource::collection($posts->items()),
        ]);
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $postDTO = StorePostDTO::fromRequest($request->validated());
        $post = $this->postService->createPost($postDTO);

        if (!$post) {
            return response()->json(['error' => 'Книга не найдена'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(new PostResource($post), Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $post = $this->postService->getPostById($id);

        if (!$post) {
            return response()->json(['error' => 'Пост не найден'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(new PostResource($post));
    }

    public function update(UpdatePostRequest $request, int $id): JsonResponse
    {
        $postDTO = UpdatePostDTO::fromRequest($request->validated());
        $post = $this->postService->updatePost($postDTO, $id);

        if (!$post) {
            return response()->json(['error' => 'Пост не найден'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(new PostResource($post));
    }

    public function destroy(int $id): JsonResponse
    {
        $status = $this->postService->deletePost($id);

        if (!$status) {
            return response()->json(['error' => 'Пост не найден'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Пост успешно удален']);
    }
}
