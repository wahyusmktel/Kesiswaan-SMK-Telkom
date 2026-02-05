<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class NottedPost extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'image',
    ];

    /**
     * Get the user that owns the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(NottedComment::class);
    }

    /**
     * Get only top-level comments for the post.
     */
    public function rootComments(): HasMany
    {
        return $this->hasMany(NottedComment::class)->whereNull('parent_id');
    }

    /**
     * Get the likes for the post.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(NottedLike::class, 'likeable');
    }

    /**
     * Check if the post is liked by a specific user.
     */
    public function isLikedBy($user): bool
    {
        if (!$user)
            return false;
        $userId = is_object($user) ? $user->id : $user;
        return $this->likes()->where('user_id', $userId)->exists();
    }
}
