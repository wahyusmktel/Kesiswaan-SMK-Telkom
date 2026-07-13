<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassPromotion extends Model
{
    protected $fillable = [
        'source_tahun_pelajaran_id',
        'target_tahun_pelajaran_id',
        'processed_by',
        'promoted_count',
        'graduated_count',
        'created_rombel_count',
        'summary',
    ];

    protected $casts = [
        'summary' => 'array',
    ];

    public function sourceTahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'source_tahun_pelajaran_id');
    }

    public function targetTahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'target_tahun_pelajaran_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
