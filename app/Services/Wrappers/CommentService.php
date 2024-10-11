<?php

namespace App\Services\Wrappers;

use App\DTO\CommentDTO;
use App\Models\Comment;
use App\Repositories\Api\V1\CommentRepository;
use Illuminate\Support\Facades\Gate;
use Exception;

class CommentService
{
    protected CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function getAllComments()
    {
        return $this->commentRepository->all();
    }

    public function getComment(int $id): Comment
    {
        $comment = $this->commentRepository->find($id);

        if (!$comment) {
            throw new Exception('Комментарий не найден');
        }

        return $comment;
    }

    public function createComment(CommentDTO $commentDTO): Comment
    {
        $commentData = [
            'content' => $commentDTO->content,
            'user_id' => $commentDTO->user_id,
            'commentable_id' => $commentDTO->commentable_id,
            'commentable_type' => $commentDTO->commentable_type,
        ];

        $comment = new Comment($commentData);

        Gate::authorize('createComment', $comment);

        $this->commentRepository->create($commentData);

        return $comment;
    }

    public function updateComment(Comment $comment, CommentDTO $commentDTO)
    {
        Gate::authorize('updateComment', $comment);

        $this->commentRepository->update($comment, ['content' => $commentDTO->content]);
    }

    public function deleteComment(int $id)
    {
        $comment = $this->getComment($id);

        Gate::authorize('deleteComment', $comment);

        $this->commentRepository->delete($comment);
    }
}
