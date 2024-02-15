<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
class UserPolicy
{
    use HandlesAuthorization;

    public function update(User $authUser, User $targetUser)
    {
        return $authUser->hasRole(USER::ROLE_ADMIN) || $authUser->id === $targetUser->id;
    }

    public function delete(User $authUser, User $targetUser)
    {
        return $authUser->hasRole(USER::ROLE_ADMIN) || $authUser->id === $targetUser->id;
    }
}
