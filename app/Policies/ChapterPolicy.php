<?php

namespace App\Policies;

use App\Models\Chapter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChapterPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->hasAnyRole(User::ROLE_AUTHOR, User::ROLE_ADMIN);
    }

    public function update(User $user, Chapter $chapter)
    {
        return $user->hasRole(User::ROLE_ADMIN) || ($user->hasRole(User::ROLE_AUTHOR) && $user->id === $chapter->book->user_id);
    }

    public function delete(User $user, Chapter $chapter)
    {
        return $user->hasRole(User::ROLE_ADMIN) || ($user->hasRole(User::ROLE_AUTHOR) && $user->id === $chapter->book->user_id);
    }
}
