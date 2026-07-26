<?php

namespace App\Models\Erapor;

use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EraporPeriodSetting extends Model
{
    protected $fillable = [
        'tahun_pelajaran_id',
        'workflow_status',
        'score_entry_starts_at',
        'score_entry_ends_at',
        'report_date',
        'configured_by',
        'configured_at',
        'metadata',
    ];

    protected $casts = [
        'score_entry_starts_at' => 'datetime',
        'score_entry_ends_at' => 'datetime',
        'report_date' => 'date',
        'configured_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function academicPeriod()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function rombelCurricula()
    {
        return $this->hasMany(EraporRombelCurriculum::class);
    }

    public function configurator()
    {
        return $this->belongsTo(User::class, 'configured_by');
    }
}
