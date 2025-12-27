<?php

namespace App\Policies;

use App\Models\{Comment, User};
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can view any comments.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage comments');
    }

    /**
     * Determine whether the user can view the comment.
     */
    public function view(User $user, Comment $comment): bool
    {
        // Approved comments are public
        if ($comment->approved) {
            return true;
        }

        // Unapproved comments visible to moderators and author
        return $user->hasPermissionTo('approve comments') || $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can create comments.
     */
    public function create(?User $user = null): bool
    {
        // Guests can comment if enabled
        if (!$user) {
            return config('blog.allow_guest_comments', true);
        }

        // Logged-in users can comment if they have permission
        return $user->hasPermissionTo('create comments') ?? true;
    }

    /**
     * Determine whether the user can update the comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Users can edit their own comments within 15 minutes
        if ($user->id === $comment->user_id && $comment->created_at->diffInMinutes(now()) < 15) {
            return true;
        }

        // Moderators can edit any comment
        return $user->hasPermissionTo('manage comments');
    }

    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Admins can delete any comment
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Editors can delete but not admin comments
        if ($user->hasRole('Editor') && !$comment->author?->hasRole('Admin')) {
            return $user->hasPermissionTo('delete comments');
        }

        // Users can delete their own comments
        return $user->id === $comment->user_id && $user->hasPermissionTo('delete comments');
    }

    /**
     * Determine whether the user can approve comments.
     */
    public function approve(User $user): bool
    {
        return $user->hasPermissionTo('approve comments');
    }
}