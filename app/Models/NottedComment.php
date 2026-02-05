<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class NottedComment extends Model
{
    protected $fillable = [
        'notted_post_id',
        'user_id',
        'content',
        'parent_id',
    ];

    /**
     * Get the post that owns the comment.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(NottedPost::class, 'notted_post_id');
    }

    /**
     * Get the user that owns the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment if this is a reply.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NottedComment::class, 'parent_id');
    }

    /**
     * Get the replies for this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(NottedComment::class, 'parent_id');
    }

    /**
     * Get the likes for the comment.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(NottedLike::class, 'likeable');
    }

    /**
     * Check if the comment is liked by a specific user.
     */
    public function isLikedBy($user): bool
    {
        if (!$user)
            return false;
        $userId = is_object($user) ? $user->id : $user;
        return $this->likes()->where('user_id', $userId)->exists();
    }
}
