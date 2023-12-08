<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleService
{
    public function createRole($roleName)
    {
        return Role::create(['name' => $roleName]);
    }

    public function assignRoleToUser($user, $roleName)
    {
        $role = Role::findByName($roleName);

        if ($role) {
            $user->assignRole($role);
            return true;
        }

        return false;
    }
}