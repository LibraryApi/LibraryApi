<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
class RoleSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создание ролей
        $adminRole = Role::create(['name' => 'admin']);
        $authorRole = Role::create(['name' => 'author']);
        $readerRole = Role::create(['name' => 'reader']);
        $guestRole = Role::create(['name' => 'guest']);

        // Создание разрешений
        $createPostPermission = Permission::create(['name' => 'create post']);
        $updatePostPermission = Permission::create(['name' => 'update post']);
        $deletePostPermission = Permission::create(['name' => 'delete post']);
        $readPostPermission = Permission::create(['name' => 'read post']); 
        // Привязка разрешений к ролям
        $adminRole->givePermissionTo($createPostPermission, $updatePostPermission, $deletePostPermission);
        $authorRole->givePermissionTo($createPostPermission, $updatePostPermission);
        $readerRole->givePermissionTo($readPostPermission);
    }
}
