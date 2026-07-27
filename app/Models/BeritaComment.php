<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BeritaComment extends Model
{
    protected $fillable = [
        'berita_id',
        'parent_id',
        'user_id',
        'name',
        'email',
        'content',
        'status',
        'ip_hash',
        'user_agent',
        'moderated_by',
        'moderated_at',
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
    ];

    public function berita()
    {
        return $this->belongsTo(Berita::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')->oldest();
    }

    public function approvedReplies()
    {
        return $this->replies()->where('status', 'approved');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }
}
