<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\Songs;
use Illuminate\Database\Eloquent\Factories\Factory;

class SongsFactory extends Factory
{
    protected $model = Songs::class;

    public function definition()
    {
        return [
            'title' => $this->faker->words(3, true),
            'length' => $this->faker->time('i:s'),
            'gerne' => $this->faker->randomElement(Songs::GENRES),
            'album_id' => Album::factory(),
        ];
    }
}