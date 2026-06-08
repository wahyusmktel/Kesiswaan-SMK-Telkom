<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NottedUnoRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'host_user_id',
        'guest_user_id',
        'winner_user_id',
        'status',
        'state',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'state' => 'array',
            'last_activity_at' => 'datetime',
        ];
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_user_id');
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function hasPlayer(User $user): bool
    {
        return $this->host_user_id === $user->id || $this->guest_user_id === $user->id;
    }
}
