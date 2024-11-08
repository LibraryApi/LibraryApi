<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\CommentDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\Comments\CommentResource;
use App\Services\Wrappers\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function index(Request $request): JsonResponse
    {
        $comments = $this->commentService->getAllComments($request);
        return response()->json(CommentResource::collection($comments));
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        $commentDTO = new CommentDTO(array_merge(
            $request->validated(),
            ['user_id' => auth()->user()->id]
        ));

        $comment = $this->commentService->createComment($commentDTO);

        return response()->json(['message' => 'Комментарий успешно создан'], 201);
    }

    public function show(int $id): JsonResponse
    {
        $comment = $this->commentService->getComment($id);
        return response()->json(new CommentResource($comment));
    }

    public function update(UpdateCommentRequest $request, int $id): JsonResponse
    {
        $commentDTO = new CommentDTO($request->validated());
        $comment = $this->commentService->getComment($id);

        $this->commentService->updateComment($comment, $commentDTO);

        return response()->json(['message' => 'Комментарий успешно обновлен'], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->commentService->deleteComment($id);
        return response()->json(['message' => 'Комментарий успешно удален'], 200);
    }
}
