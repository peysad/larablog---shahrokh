<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine if the user can update the profile.
     */
    public function update(User $user, User $subject): bool
    {
        return $user->id === $subject->id || $user->hasRole(['Admin', 'Editor']);
    }
}