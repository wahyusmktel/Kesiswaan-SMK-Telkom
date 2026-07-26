<?php

namespace App\Models\Erapor;

use Illuminate\Database\Eloquent\Model;

class EraporRefCurriculum extends Model
{
    protected $fillable = [
        'external_id',
        'name',
        'education_level_id',
        'major_external_id',
        'valid_from',
        'valid_until',
        'is_active',
        'source_updated_at',
        'reference_import_id',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'source_updated_at' => 'datetime',
    ];

    public function subjects()
    {
        return $this->belongsToMany(
            EraporRefSubject::class,
            'erapor_ref_curriculum_subjects',
            'erapor_ref_curriculum_id',
            'erapor_ref_subject_id'
        )->withPivot([
            'education_level_id',
            'hours',
            'maximum_hours',
            'curriculum_status',
            'is_required',
            'is_active',
        ])->withTimestamps();
    }

    public function rombelAssignments()
    {
        return $this->hasMany(EraporRombelCurriculum::class, 'erapor_ref_curriculum_id');
    }
}
