<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Post;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'social_links',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'social_links' => 'array',
    ];

    /**
     * Get the posts authored by this user.
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    /**
     * Get the user's avatar URL or fallback to default.
     */
    public function getAvatarUrlAttribute(): string
    {
        if (empty($this->avatar)) {
            return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?s=150&d=mp';
        }

        $thumbPath = str_ireplace('original', 'thumb', $this->avatar);
        $originalPath = $this->avatar;

        if (Storage::disk('public')->exists($thumbPath)) {
            return asset('storage/' . $thumbPath);
        }

        if (Storage::disk('public')->exists($originalPath)) {
            return asset('storage/' . $originalPath);
        }

        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?s=150&d=mp';
    }

    /**
     * Check if user has admin role.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    /**
     * Check if user is an editor.
     */
    public function isEditor(): bool
    {
        return $this->hasRole('Editor');
    }

    /**
     * Check if user is an author.
     */
    public function isAuthor(): bool
    {
        return $this->hasRole('Author');
    }

    /**
    * Get unread notifications count.
    */
    public function unreadNotificationsCount(): int
    {
        return $this->unreadNotifications()->count();
    }

    // Add relationship for profile updates
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
    * Get user's published posts count.
    */
    public function getPublishedPostsCountAttribute(): int
    {
        return $this->posts()->published()->count();
    }
}