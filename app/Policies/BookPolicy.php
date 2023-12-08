<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;

class BookPolicy
{
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        // Все пользователи могут просматривать посты
        return true;
    }

    public function view(User $user, Book $book = null)
    { 
        // Все пользователи могут просматривать посты
        return true;
    }

    public function create(User $user, Book $book = null)
    {
        // Читатели и авторы и админы могут создавать посты
        return $user->hasAnyRole(USER::ROLE_READER, USER::ROLE_AUTHOR, USER::ROLE_ADMIN);
    }

    public function update(User $user, Book $book = null)
    {
        // Авторы и админы могут обновлять свои посты
        return $user->hasRole(USER::ROLE_ADMIN) || ($user->hasRole(USER::ROLE_AUTHOR) && $user->id === $book->user_id);
    }

    public function delete(User $user, Book $book = null)
    {
        // Авторы и админы могут удалять свои посты
        return $user->hasRole(USER::ROLE_ADMIN) || ($user->hasRole(USER::ROLE_AUTHOR) && $user->id === $book->user_id);
    }
}
