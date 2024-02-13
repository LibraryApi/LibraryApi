<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;

class BookPolicy
{
    public function create(User $user)
    {
        return $user->hasAnyRole(USER::ROLE_READER, USER::ROLE_AUTHOR, USER::ROLE_ADMIN);
    }

    public function update(User $user, Book $book)
    {
        return $user->hasRole(USER::ROLE_ADMIN) || ($user->hasRole(USER::ROLE_AUTHOR) && $user->id === $book->user_id);
    }

    public function delete(User $user, Book $book)
    {
        return $user->hasRole(USER::ROLE_ADMIN) || ($user->hasRole(USER::ROLE_AUTHOR) && $user->id === $book->user_id);
    }
}
