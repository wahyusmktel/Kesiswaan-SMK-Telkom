<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrakerinLaporanBimbingan extends Model
{
    use HasFactory;

    protected $fillable = [
        'prakerin_penempatan_id',
        'uploaded_by',
        'judul',
        'file_path',
        'status',
        'catatan_pembimbing',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function penempatan()
    {
        return $this->belongsTo(PrakerinPenempatan::class, 'prakerin_penempatan_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
