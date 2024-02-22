<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chapter>
 */
class ChapterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(5, true),
            'number' => $this->faker->numberBetween(1, 10),
            'duration' => $this->faker->time('H:i:s'),
            'characters' => $this->faker->sentence,
            'images' => $this->faker->imageUrl(),
        ];
    }
}
