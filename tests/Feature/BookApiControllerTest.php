<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;
class BookApiControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    /* use RefreshDatabase;

    public function test_can_create_book()
    {
        $data = [
            'title' => 'Example Book',
            'author' => 'John Doe',
            'description' => 'This is a test book.',
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'title' => 'Example Book',
                     'author' => 'John Doe',
                     'description' => 'This is a test book.',
                 ]);

        $this->assertDatabaseHas('books', $data);
    }

    public function test_can_get_all_books()
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/books');
        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_get_single_book()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/v1/books/{$book->id}");
                $response->assertStatus(200)
                 ->assertJson([
                     'title' => $book->title,
                     'author' => $book->author,
                     'description' => $book->description,
                 ]);
    }

    public function test_can_update_book()
    {
        $book = Book::factory()->create();

        $updatedData = [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
            'description' => 'This book has been updated.',
        ];

        $response = $this->patchJson("/api/v1/books/{$book->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJson($updatedData);

        $this->assertDatabaseHas('books', $updatedData);
    }

    public function test_can_delete_book()
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    } */
}
