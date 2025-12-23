<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tag;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => strtolower($name),
            'slug' => fake()->unique()->slug(),
        ];
    }
}