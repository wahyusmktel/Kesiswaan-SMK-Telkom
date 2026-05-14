<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkInstrumenSoal extends Model
{
    protected $fillable = ['instrumen_id', 'pertanyaan', 'urutan'];

    public function instrumen()
    {
        return $this->belongsTo(UkkInstrumen::class, 'instrumen_id');
    }
}
