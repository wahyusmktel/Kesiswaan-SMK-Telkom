<?php

namespace App\Models\Erapor;

use Illuminate\Database\Eloquent\Model;

class EraporRefCurriculumSubject extends Model
{
    protected $fillable = [
        'erapor_ref_curriculum_id',
        'erapor_ref_subject_id',
        'education_level_id',
        'hours',
        'maximum_hours',
        'curriculum_status',
        'is_required',
        'is_active',
        'reference_import_id',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];
}
