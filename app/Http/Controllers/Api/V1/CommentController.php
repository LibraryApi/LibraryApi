<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\IndexCommentRequest;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\Comments\CommentResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\MessageResource;
use App\Models\Comment;
use App\Services\Application\Comment\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(private readonly CommentService $commentService)
    {
    }

    public function index(IndexCommentRequest $request): JsonResponse
    {
        return response()->json(new CommentCollection(
            $this->commentService->paginate($request->filters(), $request->perPage())
        ));
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        $comment = $this->commentService->make($request->validated(), auth()->id());
        $this->authorize('createComment', $comment);

        return response()->json(new CommentResource($this->commentService->save($comment)), 201);
    }

    public function show(Comment $comment): JsonResponse
    {
        return response()->json(new CommentResource($comment));
    }

    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $this->authorize('updateComment', $comment);
        $this->commentService->update($comment, $request->validated());

        return response()->json(new MessageResource('Комментарий успешно обновлен'));
    }

    public function destroy(int $id): JsonResponse
    {
        $comment = $this->commentService->find($id);

        if (!$comment) {
            return response()->json(new ErrorResource('Комментарий не найден'), 404);
        }

        $this->authorize('deleteComment', $comment);
        $this->commentService->delete($comment);

        return response()->json(new MessageResource('Комментарий успешно удален'));
    }
}
