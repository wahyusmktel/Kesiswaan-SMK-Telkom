<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuLetterRequest extends Model
{
    protected $fillable = [
        'user_id',
        'letter_code_id',
        'subject',
        'file_path',
        'type',
        'content',
        'status',
        'notes',
        'outgoing_letter_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function letterCode()
    {
        return $this->belongsTo(TuLetterCode::class, 'letter_code_id');
    }

    public function outgoingLetter()
    {
        return $this->belongsTo(TuOutgoingLetter::class);
    }
}
