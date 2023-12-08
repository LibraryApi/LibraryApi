<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\User;
use App\Models\Post;
use app\Models\Comment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSystemSeeder::class,
            UserSeeder::class,
            BookSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
            
        ]);
        /* $book = Book::factory()->count(10)->create();
        $post = Post::factory()->create([
            'book_id' => $book->id,
        ]);
        $comment = Comment::factory()->create([
            'post_id'=> $book->id,
        ]); */
    }
}
