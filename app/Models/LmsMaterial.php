<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'mata_pelajaran_id',
        'master_guru_id',
        'rombel_id',
        'title',
        'content',
        'file_path',
        'is_published',
    ];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(MasterGuru::class, 'master_guru_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }
}
