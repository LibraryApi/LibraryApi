<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Http\Resources\Comments\CommentResource;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $comments = CommentResource::collection(Comment::with('user', 'commentable')->get());

        return response()->json($comments);
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {

        $comment = new Comment([
            'content' => $request->input('content'),
            'user_id' => auth()->user()->id,
            'commentable_id' => $request->input('commentable_id'),
            'commentable_type' => $request->input('commentable_type'),
        ]);

        $this->authorize('createComment', $comment);

        $comment->save();

        return response()->json(['message' => 'Комментарий успешно создан'], 201);
    }

    public function show(Comment $comment): JsonResponse
    {
        return response()->json(new CommentResource($comment));
    }

    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $this->authorize('updateComment', $comment);

        $comment->update([
            'content' => $request->input('content'),
        ]);

        return response()->json(['message' => 'Комментарий успешно обновлен'], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Комментарий не найден'], 404);
        }

        $this->authorize('deleteComment', $comment);

        $comment->delete();
        return response()->json(['message' => 'Комментарий успешно удален'], 200);
    }
}
