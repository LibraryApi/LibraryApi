<?php

namespace App\Repositories\Api\V1;

use App\Models\Comment;

class CommentRepository
{
    public function all($request)
    {
        $data = $request->all();

        $comments = Comment::with(['user', 'children.user', 'commentable', 'parent.author'])
            ->where('commentable_type', $data['commentableType'])
            ->where('commentable_id', $data['commentableId'])
            ->whereNull('parent_id')
            ->get();

        return $comments;
    }

    public function find(int $id): ?Comment
    {
        return Comment::find($id);
    }

    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    public function update(Comment $comment, array $data)
    {
        $comment->update($data);
    }

    public function delete(Comment $comment)
    {
        $comment->delete();
    }
}
