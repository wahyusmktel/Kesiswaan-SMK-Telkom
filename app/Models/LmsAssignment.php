<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'mata_pelajaran_id',
        'master_guru_id',
        'rombel_id',
        'title',
        'description',
        'file_path',
        'due_date',
        'points',
    ];

    protected $casts = [
        'due_date' => 'datetime',
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

    public function submissions()
    {
        return $this->hasMany(LmsSubmission::class);
    }
}
