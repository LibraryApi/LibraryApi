<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;

class BookPolicy
{
    public function create(User $user)
    {
        return $user->hasAnyRole(User::ROLE_READER, User::ROLE_AUTHOR, User::ROLE_ADMIN);
    }

    public function update(User $user, Book $book)
    {
        return $user->hasRole(User::ROLE_ADMIN) || ($user->hasRole(User::ROLE_AUTHOR) && $user->id === $book->user_id);
    }

    public function delete(User $user, Book $book)
    {
        return $user->hasRole(User::ROLE_ADMIN) || ($user->hasRole(User::ROLE_AUTHOR) && $user->id === $book->user_id);
    }
}
