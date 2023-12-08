<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;

class PostPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        // Все пользователи могут просматривать посты
        return true;
    }

    public function view(User $user, Post $post = null)
    { 
        // Все пользователи могут просматривать посты
        return true;
    }

    public function create(User $user, Post $post = null)
    {
        // Читатели и авторы и админы могут создавать посты
        return $user->hasAnyRole(USER::ROLE_READER, USER::ROLE_AUTHOR, USER::ROLE_ADMIN);
    }

    public function update(User $user, Post $post = null)
    {
        // Авторы и админы могут обновлять свои посты
        return $user->hasRole(USER::ROLE_ADMIN) || ($user->hasRole(USER::ROLE_AUTHOR) && $user->id === $post->user_id);
    }

    public function delete(User $user, Post $post = null)
    {
        // Авторы и админы могут удалять свои посты
        return $user->hasRole(USER::ROLE_ADMIN) || ($user->hasRole(USER::ROLE_AUTHOR) && $user->id === $post->user_id);
    }
}
