<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuOutgoingLetter extends Model
{
    protected $fillable = [
        'number_sequence',
        'letter_code_id',
        'date',
        'subject',
        'recipient',
        'full_number',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function letterCode()
    {
        return $this->belongsTo(TuLetterCode::class, 'letter_code_id');
    }
}
