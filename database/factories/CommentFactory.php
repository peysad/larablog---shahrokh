<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Comment, Post, User};

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'commentable_id' => Post::factory(),
            'commentable_type' => Post::class,
            'parent_id' => null,
            'body' => fake()->paragraph(),
            'approved' => fake()->boolean(80),
            'guest_name' => null,
            'guest_email' => null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    /**
     * Indicate that the comment is a reply.
     */
    public function reply(Comment $parent): Factory
    {
        return $this->state(function (array $attributes) use ($parent) {
            return [
                'commentable_id' => $parent->commentable_id,
                'commentable_type' => $parent->commentable_type,
                'parent_id' => $parent->id,
            ];
        });
    }

    /**
     * Indicate that the comment is from a guest.
     */
    public function guest(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => null,
                'guest_name' => fake()->name(),
                'guest_email' => fake()->email(),
            ];
        });
    }

    /**
     * Indicate that the comment is pending approval.
     */
    public function pending(): Factory
    {
        return $this->state(function (array $attributes) {
            return ['approved' => false];
        });
    }
}