<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@larablog.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(60),
            'avatar' => null,
            'bio' => 'System Administrator with full access',
            'social_links' => [
                'twitter' => '@admin',
                'linkedin' => 'admin-larablog'
            ],
        ]);
        $admin->assignRole('Admin');

        // Create Editor user
        $editor = User::create([
            'name' => 'Editor',
            'email' => 'editor@larablog.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(60),
            'avatar' => null,
            'bio' => 'Content Editor',
            'social_links' => [],
        ]);
        $editor->assignRole('Editor');

        // Create Author user
        $author = User::create([
            'name' => 'Author',
            'email' => 'author@larablog.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(60),
            'avatar' => null,
            'bio' => 'Regular content author',
            'social_links' => [
                'github' => 'author',
                'twitter' => '@author'
            ],
        ]);
        $author->assignRole('Author');

        // Create Regular user
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@larablog.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(60),
            'avatar' => null,
            'bio' => 'Standard blog reader',
            'social_links' => [],
        ]);
        $user->assignRole('User');

        // Create 5 additional sample authors
        User::factory(5)->create()->each(function ($user) {
            $user->assignRole('Author');
        });
    }
}