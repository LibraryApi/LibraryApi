<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $authUser)
    {
        // Все пользователи могут видеть список пользователей
        return true;
    }

    public function view(User $authUser, User $targetUser = null)
    {
        // Админы могут просматривать любого пользователя, другие пользователи - только себя
        return $authUser->hasRole(USER::ROLE_ADMIN) || $authUser->id === $targetUser->id;
    }

    public function update(User $authUser, User $targetUser = null)
    {
        // Админы могут обновлять любого пользователя, другие пользователи - только себя
        return $authUser->hasRole(USER::ROLE_ADMIN) || $authUser->id === $targetUser->id;
    }

    public function delete(User $authUser, User $targetUser = null)
    {
        // Админы могут удалять любого пользователя, другие пользователи - только себя
        return $authUser->hasRole(USER::ROLE_ADMIN) || $authUser->id === $targetUser->id;
    }
}
