<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    protected $model = Movie::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'release_date' => $this->faker->date(),
            'description' => $this->faker->paragraph(),
            'rating' => $this->faker->randomFloat(1, 1, 10),
            'duration' => $this->faker->numberBetween(80, 180), // minutes
            'poster_url' => $this->faker->imageUrl(300, 450, 'movies', true),
            'country_id' => $this->faker->numberBetween(1, 10),
            'studio_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}
