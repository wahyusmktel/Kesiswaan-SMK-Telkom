<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMasterBookPeriod extends Model
{
    protected $fillable = [
        'student_master_book_id',
        'tahun_pelajaran_id',
        'rombel_id',
        'school_year',
        'semester',
        'grades',
        'extracurriculars',
        'sick_days',
        'permitted_days',
        'absent_days',
        'conduct',
        'development_notes',
        'updated_by',
    ];

    protected $casts = [
        'grades' => 'array',
        'extracurriculars' => 'array',
        'sick_days' => 'integer',
        'permitted_days' => 'integer',
        'absent_days' => 'integer',
    ];

    public function masterBook()
    {
        return $this->belongsTo(StudentMasterBook::class, 'student_master_book_id');
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }
}
