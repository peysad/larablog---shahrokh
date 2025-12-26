<?php

namespace App\Policies;

use App\Models\{Tag, User};
use Illuminate\Auth\Access\Response;

class TagPolicy
{
    /**
     * Determine whether the user can view any tags.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage tags');
    }

    /**
     * Determine whether the user can view the tag.
     */
    public function view(User $user, Tag $tag): bool
    {
        return true; // Public viewing is allowed
    }

    /**
     * Determine whether the user can create tags.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage tags');
    }

    /**
     * Determine whether the user can update the tag.
     */
    public function update(User $user, Tag $tag): bool
    {
        return $user->hasPermissionTo('manage tags');
    }

    /**
     * Determine whether the user can delete the tag.
     */
    public function delete(User $user, Tag $tag): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Prevent deletion if tag has posts
        if ($tag->posts()->exists()) {
            return false;
        }

        return $user->hasPermissionTo('manage tags');
    }
}