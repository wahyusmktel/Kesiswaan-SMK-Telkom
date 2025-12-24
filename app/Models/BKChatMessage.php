<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BKChatMessage extends Model
{
    use HasFactory;

    protected $table = 'bk_chat_messages';

    protected $fillable = [
        'chat_room_id',
        'sender_id',
        'message',
        'type',
        'file_path',
        'is_read'
    ];

    protected $appends = ['file_url'];

    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function room()
    {
        return $this->belongsTo(BKChatRoom::class, 'chat_room_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
