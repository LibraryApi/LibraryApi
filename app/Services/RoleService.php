<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleService
{
    public function createRole($roleName)
    {
        return Role::create(['name' => $roleName]);
    }

    public function assignRoleToUser($user, $role)
    {
        $user->assignRole($role);
    }
}
