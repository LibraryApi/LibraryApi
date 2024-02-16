<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function createComment(User $user)
    {
        return $user->hasAnyRole(USER::ROLE_READER, USER::ROLE_AUTHOR, USER::ROLE_ADMIN);
    }

    public function updateComment(User $user, Comment $comment = null)
    {
        return $user->hasRole(USER::ROLE_ADMIN) || $user->id === $comment->user_id;
    }

    public function deleteComment(User $user, Comment $comment = null)
    {
        return $user->hasRole(USER::ROLE_ADMIN) || $user->id === $comment->user_id;
    }
}
