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

 
        $authorUser = User::factory()->create([
            'name' => 'author',
            'email' => 'author@gmail.com',
            'password' => 'author'
        ]);
        $authorUser->assignRole('author');


        $authorUser1 = User::factory()->create([
            'name' => 'author1',
            'email' => 'author1@gmail.com',
            'password' => 'author1'
        ]);
        $authorUser1->assignRole('author');

        $authorUser2 = User::factory()->create([
            'name' => 'Леха',
            'email' => 'leha@gmail.com',
            'password' => 'leha'
        ]);
        $authorUser2->assignRole('author');

        $authorUser3 = User::factory()->create([
            'name' => 'Султан',
            'email' => 'sultan@gmail.com',
            'password' => 'sultan'
        ]);
        $authorUser3->assignRole('author');

        $authorUser4 = User::factory()->create([
            'name' => 'Руслан',
            'email' => 'ruslan@gmail.com',
            'password' => 'ruslan'
        ]);
        $authorUser4->assignRole('author');

        $authorUser4 = User::factory()->create([
            'name' => 'Алексей',
            'email' => 'alexey@gmail.com',
            'password' => 'alexey'
        ]);
        $authorUser4->assignRole('author');
        
        $authorUser5 = User::factory()->create([
            'name' => 'Олег',
            'email' => 'oleg@gmail.com',
            'password' => 'oleg'
        ]);
        $authorUser5->assignRole('author');
        
        $authorUser6 = User::factory()->create([
            'name' => 'Марина',
            'email' => 'marina@gmail.com',
            'password' => 'marina'
        ]);
        $authorUser6->assignRole('author');
        
        $authorUser7 = User::factory()->create([
            'name' => 'Екатерина',
            'email' => 'ekaterina@gmail.com',
            'password' => 'ekaterina'
        ]);
        $authorUser7->assignRole('author');
        
        $authorUser8 = User::factory()->create([
            'name' => 'Денис',
            'email' => 'denis@gmail.com',
            'password' => 'denis'
        ]);
        $authorUser8->assignRole('author');
        
        $authorUser9 = User::factory()->create([
            'name' => 'Татьяна',
            'email' => 'tatiana@gmail.com',
            'password' => 'tatiana'
        ]);
        $authorUser9->assignRole('author');

        $reader = User::factory()->create([
            'name' => 'reader',
            'email' => 'reader@gmail.com',
            'password' => 'reader'
        ]);
        $reader->assignRole('author');
    }
}
