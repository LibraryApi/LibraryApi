<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => 'admin'
        ]);
        $adminUser->assignRole('admin');

        // Создание автора
        $authorUser = User::factory()->create([
            'name' => 'author',
            'email' => 'author@gmail.com',
            'password' => 'author'
        ]);
        $authorUser->assignRole('author');

        // Создание читателя
        $readerUser = User::factory()->create([
            'name' => 'reader',
            'email' => 'reader@gmail.com',
            'password' => 'reader'
        ]);
        $readerUser->assignRole('reader');

        $readerUser2 = User::factory()->create([
            'name' => 'Леха',
            'email' => 'leha@gmail.com',
            'password' => 'leha'
        ]);
        $readerUser2->assignRole('reader');

        $authorUser2 = User::factory()->create([
            'name' => 'Султан',
            'email' => 'sultan@gmail.com',
            'password' => 'sultan'
        ]);
        $authorUser2->assignRole('author');

        $authorUser3 = User::factory()->create([
            'name' => 'Руслан',
            'email' => 'ruslan@gmail.com',
            'password' => 'ruslan'
        ]);
        $authorUser3->assignRole('author');

    }
}
