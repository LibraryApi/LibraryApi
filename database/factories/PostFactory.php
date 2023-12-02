<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => function () {
                return \App\Models\Book::factory()->create()->id;
            },
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            "title" => $this->faker->sentence(6), // Заголовок поста
            "content" => $this->faker->paragraph(8) // Содержание поста
        ];
    }
}
