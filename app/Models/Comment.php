<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, MorphTo};

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commentable_id',
        'commentable_type',
        'parent_id',
        'body',
        'approved',
        'guest_name',
        'guest_email',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'approved' => 'boolean',
    ];

    /**
     * Get the parent commentable model (Post, Page, etc.)
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the comment author (can be null for guests)
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get child replies to this comment
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->with('replies') // Recursive eager loading
                    ->where('approved', true)
                    ->latest();
    }

    /**
     * Get parent comment
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Scope: Only approved comments
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    /**
     * Scope: Top-level comments (not replies)
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Get comments with nested replies up to depth limit
     */
    public function scopeWithNestedReplies($query, $depth = 5)
    {
        if ($depth <= 0) {
            return $query;
        }

        return $query->with(['replies' => fn($q) => $q->withNestedReplies($depth - 1)]);
    }

    /**
     * Approve the comment
     */
    public function approve(): void
    {
        $this->update(['approved' => true]);
    }

    /**
     * Reject (unapprove) the comment
     */
    public function reject(): void
    {
        $this->update(['approved' => false]);
    }

    /**
     * Check if comment is a reply
     */
    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    /**
     * Get comment display name (user or guest)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->author?->name ?? $this->guest_name ?? 'Anonymous';
    }

    /**
     * Get comment display avatar
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->author?->avatar_url ?? asset('images/default-guest-avatar.png');
    }

    /**
     * Check if user can moderate this comment
     */
    public function canBeModeratedBy(User $user): bool
    {
        return $user->hasPermissionTo('approve comments');
    }

     /**
     * Get all replies recursively (flattened).
     */
    public function getAllReplies()
    {
        $allReplies = collect();

        foreach ($this->replies as $reply) {
            $allReplies->push($reply);

            if ($reply->replies->count() > 0) {
                $allReplies = $allReplies->merge($reply->getAllReplies());
            }
        }

        return $allReplies;
    }
}