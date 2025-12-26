<?php

namespace App\Policies;

use App\Models\{Category, User};
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any categories.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage categories');
    }

    /**
     * Determine whether the user can view the category.
     */
    public function view(User $user, Category $category): bool
    {
        return true; // Public viewing is allowed
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage categories');
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('manage categories');
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function delete(User $user, Category $category): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Prevent deletion if category has posts
        if ($category->posts()->exists()) {
            return false;
        }

        return $user->hasPermissionTo('manage categories');
    }
}