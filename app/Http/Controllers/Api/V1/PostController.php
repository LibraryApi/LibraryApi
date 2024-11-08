<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\PostDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use App\Services\Wrappers\PostService;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(Request $request): JsonResponse
    {
        $posts = $this->postService->index($request->input('per_page', 10), $request->all());

        $posts = PostResource::collection($posts);

        return response()->json([
            'posts' => $posts,
            'meta' => [
                'total_posts' => $posts->total(),
                'per_page' => $request->input('per_page', 10),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
            ],
        ]);
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $postDTO = new PostDTO($request->validated());
        $post = $this->postService->store($postDTO);
        return response()->json(new PostResource($post), 201);
    }

    public function show(string $id): JsonResponse
    {
        $post = $this->postService->show($id);
        return response()->json(new PostResource($post));
    }

    public function update(UpdatePostRequest $request, string $id): JsonResponse
    {
        $post = $this->postService->show($id);
        $postDTO = new PostDTO($request->validated());
        $updatedPost = $this->postService->update($post, $postDTO);
        return response()->json(new PostResource($updatedPost));
    }

    public function destroy(string $id): JsonResponse
    {
        $post = $this->postService->show($id);
        $this->postService->destroy($post);
        return response()->json(['message' => 'Пост успешно удален']);
    }
}
