<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAnyComments(User $user)
    {
        return $user->hasRole(USER::ROLE_ADMIN);
    }

    public function viewComment(User $user, Comment $comment = null)
    {
        return true;
    }

    public function createComment(User $user, Comment $comment = null)
    {
        return $user->hasAnyRole(USER::ROLE_READER, USER::ROLE_AUTHOR, USER::ROLE_ADMIN);
    }

    public function updateComment(User $user, Comment $comment = null)
    {
        return $user->hasRole(USER::ROLE_ADMIN) || ($user->hasRole(USER::ROLE_AUTHOR) && $user->id === $comment->user_id);
    }

    public function deleteComment(User $user, Comment $comment = null)
    {
        return $user->hasRole(USER::ROLE_ADMIN) || ($user->hasRole(USER::ROLE_AUTHOR) && $user->id === $comment->user_id);
    }
}
