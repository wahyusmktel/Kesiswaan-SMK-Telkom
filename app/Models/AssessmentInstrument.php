<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentInstrument extends Model
{
    public const TYPES = [
        'principal_to_teacher' => 'Kepala Sekolah menilai Guru',
        'teacher_to_principal' => 'Guru menilai Kepala Sekolah',
        'teacher_to_teacher' => 'Guru menilai Guru',
        'student_to_teacher' => 'Siswa menilai Guru',
        'student_to_student' => 'Siswa menilai Siswa',
    ];

    protected $fillable = [
        'assessment_period_id',
        'type',
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'assessment_period_id');
    }

    public function questions()
    {
        return $this->hasMany(AssessmentQuestion::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
