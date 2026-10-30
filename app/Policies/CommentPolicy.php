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
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function viewComment(User $user, Comment $comment = null)
    {
        return true;
    }

    public function createComment(User $user, Comment $comment = null)
    {
        return $user->hasAnyRole(User::ROLE_READER, User::ROLE_AUTHOR, User::ROLE_ADMIN);
    }

    public function updateComment(User $user, Comment $comment = null)
    {
        if (!$comment) {
            return false;
        }
        return $user->hasRole(User::ROLE_ADMIN) || $user->id === $comment->user_id;
    }

    public function deleteComment(User $user, Comment $comment = null)
    {
        if (!$comment) {
            return false;
        }
        return $user->hasRole(User::ROLE_ADMIN) || $user->id === $comment->user_id;
    }
}
