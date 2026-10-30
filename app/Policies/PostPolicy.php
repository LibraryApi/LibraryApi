<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;

class PostPolicy
{
    public function create(User $user)
    {
        return $user->hasAnyRole(User::ROLE_READER, User::ROLE_AUTHOR, User::ROLE_ADMIN);
    }

    public function update(User $user, Post $post = null)
    {
        if (!$post) {
            return false;
        }
        return $user->hasRole(User::ROLE_ADMIN) || $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post = null)
    {
        if (!$post) {
            return false;
        }
        return $user->hasRole(User::ROLE_ADMIN) || $user->id === $post->user_id;
    }
}
