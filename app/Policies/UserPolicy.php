<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function view(User $user, User $subject): bool
    {
        return $user->hasRole('Admin');
    }

    public function updateRole(User $user, User $subject): bool
    {
        if (!$user->hasRole('Admin')) return false;
        
        // Prevent modifying own role
        if ($user->id === $subject->id) return false;
        
        // Prevent demoting the last admin
        if ($subject->isAdmin() && $this->isLastAdmin()) return false;
        
        return true;
    }

    public function ban(User $user, User $subject): bool
    {
        if (!$user->hasRole('Admin')) return false;
        
        // Cannot ban self
        if ($user->id === $subject->id) return false;
        
        // Cannot ban super admin or if already banned
        if ($subject->isAdmin() && $this->isLastAdmin()) return false;
        
        return !$subject->isBanned();
    }

    public function unban(User $user, User $subject): bool
    {
        return $user->hasRole('Admin') && $subject->isBanned();
    }

    public function delete(User $user, User $subject): bool
    {
        if (!$user->hasRole('Admin')) return false;
        
        // Cannot delete self
        if ($user->id === $subject->id) return false;
        
        // Cannot delete last admin
        if ($subject->isAdmin() && $this->isLastAdmin()) return false;
        
        return true;
    }

    public function restore(User $user, User $subject): bool
    {
        return $user->hasRole('Admin');
    }

    public function forceDelete(User $user, User $subject): bool
    {
        return $user->hasRole('Admin') && $subject->trashed();
    }

    private function isLastAdmin(): bool
    {
        return \App\Models\User::role('Admin')->count() <= 1;
    }

    // Profile update (existing)
    public function update(User $user, User $subject): bool
    {
        return $user->id === $subject->id || $user->hasRole(['Admin', 'Editor']);
    }
}