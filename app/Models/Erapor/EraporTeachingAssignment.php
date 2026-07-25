<?php

namespace App\Models\Erapor;

use App\Models\MasterGuru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use Illuminate\Database\Eloquent\Model;

class EraporTeachingAssignment extends Model
{
    protected $fillable = [
        'tahun_pelajaran_id',
        'rombel_id',
        'mata_pelajaran_id',
        'master_guru_id',
        'erapor_subject_mapping_id',
        'subject_group',
        'sort_order',
        'passing_grade',
        'source',
        'source_key',
        'is_active',
        'last_synced_at',
        'sync_metadata',
    ];

    protected $casts = [
        'passing_grade' => 'decimal:2',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
        'sync_metadata' => 'array',
    ];

    public function academicPeriod()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }

    public function subject()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function teacher()
    {
        return $this->belongsTo(MasterGuru::class, 'master_guru_id');
    }

    public function subjectMapping()
    {
        return $this->belongsTo(EraporSubjectMapping::class, 'erapor_subject_mapping_id');
    }
}
