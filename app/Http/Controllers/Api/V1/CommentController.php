<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Book;
use App\Models\Post;
use App\Http\Resources\Comments\CommentResource;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $comments = CommentResource::collection(Comment::with('user')->paginate(10));

        return response()->json($comments);
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        $commentable = null;
        if ($request['commentable_type'] === 'post') {
            $commentable = Post::find($request['commentable_id']);
        } elseif ($request['commentable_type'] === 'book') {
            $commentable = Book::find($request['commentable_id']);
        }
    
        if (!$commentable) {
            return response()->json(['error' => 'Комментируемый объект не найден'], 404);
        }

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

    public function show(string $id): JsonResponse
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['error' => 'Комментарий не найден не найден'], 404);
        }
        return response()->json(new CommentResource($comment));
    }

    public function update(UpdateCommentRequest $request, string $id): JsonResponse
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['error' => 'Комментарий не найден'], 404);
        }

        $this->authorize('updateComment', $comment);

        $comment->update([
            'content' => $request->has('content') ? $request->input('content') : $comment->content,
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
