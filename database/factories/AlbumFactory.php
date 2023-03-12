<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlbumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'release_date' => $this->faker->date,
            'user_id' => User::factory(),
            'image' => $this->faker->optional()->imageUrl() ?? 'https://via.placeholder.com/640x480.png/00ccbb?text=album+image'
        ];
    }
}
