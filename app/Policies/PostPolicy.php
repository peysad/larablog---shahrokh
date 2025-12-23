<?php

namespace App\Policies;

use App\Models\{Post, User};
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Determine whether the user can view any posts.
     */
    public function viewAny(User $user): bool
    {
        return true; // Public viewing is allowed
    }

    /**
     * Determine whether the user can view the post.
     */
    public function view(?User $user, Post $post): bool
    {
        // Allow viewing if published
        if ($post->isPublished()) {
            return true;
        }

        // Allow viewing if user is the author or has appropriate role
        return $user?->id === $post->user_id || $user?->hasRole(['Admin', 'Editor']);
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create posts');
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        // Admin/Editor can update any post
        if ($user->hasRole(['Admin', 'Editor'])) {
            return true;
        }

        // Authors can only update their own posts
        return $user->id === $post->user_id && $user->hasPermissionTo('edit posts');
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Editors can delete but not Admin-level posts
        if ($user->hasRole('Editor') && !$post->author->hasRole('Admin')) {
            return $user->hasPermissionTo('delete posts');
        }

        // Authors can delete their own posts
        return $user->id === $post->user_id && $user->hasPermissionTo('delete posts');
    }

    /**
     * Determine whether the user can publish posts.
     */
    public function publish(User $user): bool
    {
        return $user->hasPermissionTo('publish posts');
    }
}