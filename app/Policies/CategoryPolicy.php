<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function update(User $user, Category $category)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function delete(User $user, Category $category)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }
}
