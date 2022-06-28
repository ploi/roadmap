<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Changelog>
 */
class ChangelogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->words(3, true),
            'slug' => $this->faker->word,
            'content' => $this->faker->paragraphs(3, true),
        ];
    }

    public function published()
    {
        return $this->state(function () {
            return [
                'published_at' => now(),
            ];
        });
    }
}
