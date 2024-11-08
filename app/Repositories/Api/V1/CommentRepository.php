<?php

namespace App\Repositories\Api\V1;

use App\Models\Comment;

class CommentRepository
{
    public function all($request)
    {
        $data = $request->all();

        return Comment::with(['user', 'commentable', 'children.children.user', 'children.user'])
            ->where('commentable_type', $data['commentableType'])
            ->where('commentable_id', $data['commentableId'])
            ->get();
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
