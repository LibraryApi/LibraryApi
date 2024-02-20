<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;

class PostPolicy
{
    public function create(User $user)
    {
        return $user->hasAnyRole(USER::ROLE_READER, USER::ROLE_AUTHOR, USER::ROLE_ADMIN);
    }

    public function update(User $user, Post $post = null)
    {
        return $user->hasRole(USER::ROLE_ADMIN) || $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post = null)
    {
        return $user->hasRole(USER::ROLE_ADMIN) || $user->id === $post->user_id;
    }
}
