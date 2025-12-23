<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Post, Category, Tag, User};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $categories = Category::factory(10)->create();

        // Create tags
        $tags = Tag::factory(20)->create();

        // Get authors
        $authors = User::whereHas('roles', fn($q) => $q->where('name', 'Author'))->get();

        // Create posts
        Post::factory(50)
            ->published()
            ->sequence(fn($seq) => ['user_id' => $authors->random()->id])
            ->create()
            ->each(function (Post $post) use ($categories, $tags) {
                // Attach random categories (1-3 per post)
                $post->categories()->attach(
                    $categories->random(rand(1, 3))->pluck('id')
                );

                // Attach random tags (2-5 per post)
                $post->tags()->attach(
                    $tags->random(rand(2, 5))->pluck('id')
                );

                // Add some initial views
                $post->increment('views', rand(10, 1000));
            });

        // Create some draft posts
        Post::factory(10)
            ->draft()
            ->sequence(fn($seq) => ['user_id' => $authors->random()->id])
            ->create()
            ->each(function (Post $post) use ($categories, $tags) {
                $post->categories()->attach($categories->random(rand(1, 2)));
                $post->tags()->attach($tags->random(rand(1, 3)));
            });

        Log::info('PostSeeder completed', [
            'total_posts' => Post::count(),
            'published_posts' => Post::published()->count(),
            'draft_posts' => Post::draft()->count(),
        ]);
    }
}