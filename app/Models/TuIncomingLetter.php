<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuIncomingLetter extends Model
{
    protected $fillable = ['date', 'sender', 'subject', 'letter_number', 'file_path'];
}
