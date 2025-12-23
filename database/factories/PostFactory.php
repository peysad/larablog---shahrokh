<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{User, Post};

class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->unique()->sentence(rand(6, 12)),
            'slug' => fake()->unique()->slug(),
            'excerpt' => fake()->paragraph(2),
            'body' => fake()->paragraphs(rand(5, 15), true),
            'status' => fake()->randomElement(['draft', 'published']),
            'published_at' => fake()->dateTimeBetween('-3 months', '+1 month'),
            'featured_image' => null,
            'views' => fake()->numberBetween(0, 5000),
        ];
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'published',
                'published_at' => now(),
            ];
        });
    }

    /**
     * Indicate that the post is a draft.
     */
    public function draft(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
                'published_at' => null,
            ];
        });
    }
}