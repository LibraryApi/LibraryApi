<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "title" =>$this->faker->sentence(5), // Название книги
            "author" => $this->faker->name,
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            }, // Имя автора
            "description" => $this->faker->paragraph(4) // Описание книги необязательное поле

        ];
    }
}
