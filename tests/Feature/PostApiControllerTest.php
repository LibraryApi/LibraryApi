<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;
use App\Models\Book;
use App\Models\User;


class PostApiControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $book;
    private $postData;

    public function setUp(): void
    {
        parent::setUp();

        // Создание пользователя и книги один раз перед всеми тестами
        $this->user = User::factory(10)->create();
        $this->book = Book::factory(10)->create();

        // Инициализация данных для поста перед каждым тестом
        $this->postData = [
            'title' => 'Example Post',
            'content' => 'This is a test post.',
            'book_id' => $this->book->random()->id,
        ];
    }

    public function test_can_create_post()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $postData = [
            'title' => 'Example Post',
            'content' => 'This is a test post.',
            'book_id' => $book->id,
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/posts', $postData);

        $response->assertStatus(201)
            ->assertJsonFragment($postData);

        $this->assertDatabaseHas('posts', $postData);
    }

    public function test_can_get_all_posts()
    {
        User::factory(10)->create();
        Book::factory(10)->create();
        Post::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_get_single_post()
    {

        $post = Post::factory()->create();
        $response = $this->getJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson([

                'title' => $post->title,
                'content' => $post->content,
                'book_id' => $post->book_id,

            ]);
    }

    public function test_can_update_post()
    {

        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $newBook = Book::factory()->create();

        $updatedData = [
            'title' => 'Updated Title',
            'content' => 'This post has been updated.',
            'book_id' => $newBook->id,
        ];


        $response = $this->actingAs($user)->patchJson("/api/v1/posts/{$post->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'author' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'title' => $user->title,
                        'created_at' => $user->fresh()->created_at->toJSON(),
                    ],
                    'title' => 'Updated Title',
                    'content' => 'This post has been updated.',
                    'book_id' => $newBook->id,
                ],
            ]);



        $this->assertDatabaseHas('posts', $updatedData);
    }

    public function test_can_delete_post()
    {

        $user = User::factory()->create();


        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }


    /* //ЕЩЕ не реализован функционал 
    public function test_can_get_posts_with_criteria()
    {
        // Получаем id пользователя и книги
        $userId = $this->user->pluck('id')->first();
        $bookId = $this->book->pluck('id')->first();
        //dd($userId);
        // Создаем два поста для определенной книги.
        $posts = Post::factory(2)->create([
            'user_id' => $userId,
            'book_id' => $bookId,
        ]);

        // Создаем дополнительный пост с другой книгой.
        Post::factory()->create([
            'user_id' => $this->user->id,
            'book_id' => Book::factory()->create()->id,
        ]);

        // Получаем посты для определенной книги с использованием API.
        $response = $this->getJson("/api/v1/posts?book_id={$this->book->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1); // Ожидается только один пост для указанной книги.
    } */

    public function test_invalid_endpoint_returns_404()
    {
        $response = $this->getJson('/api/v1/nonexistent');

        $response->assertStatus(404);
    }

    public function test_update_nonexistent_post_returns_404()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->patchJson('/api/v1/posts/999', [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ]);
        $response->assertStatus(404);
    }

    public function test_delete_nonexistent_post_returns_404()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->deleteJson('/api/v1/posts/999');

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_access_protected_endpoints()
    {
        $response = $this->postJson('/api/v1/posts', $this->postData);

        $response->assertStatus(401);
    }

    public function test_unauthorized_user_cannot_update_post()
{
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)->patchJson("/api/v1/posts/{$post->id}", [
        'title' => 'Updated Title',
        'content' => 'Updated Content',
    ]);

    $response->assertStatus(401);
}

    public function test_invalid_data_returns_422()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/v1/posts', ['title' => '', 'content' => '']);

        $response->assertStatus(422);
    }
}
