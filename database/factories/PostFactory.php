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
            "user_id"=> rand(1,9),
            'book_id' => rand(1,9),
            "title" => $this->faker->sentence(6), // Заголовок поста
            "content" => $this->faker->paragraph(8) // Содержание поста
        ];
    }
}
