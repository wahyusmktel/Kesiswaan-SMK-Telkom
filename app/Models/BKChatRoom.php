<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BKChatRoom extends Model
{
    use HasFactory;

    protected $table = 'bk_chat_rooms';

    protected $fillable = [
        'siswa_user_id',
        'guru_bk_user_id',
        'last_message_at',
    ];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_user_id');
    }

    public function guruBK()
    {
        return $this->belongsTo(User::class, 'guru_bk_user_id');
    }

    public function messages()
    {
        return $this->hasMany(BKChatMessage::class, 'chat_room_id');
    }
}
