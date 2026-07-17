<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMasterBook extends Model
{
    protected $fillable = [
        'master_siswa_id',
        'admission_date',
        'admission_status',
        'previous_school',
        'previous_diploma_number',
        'previous_diploma_date',
        'blood_type',
        'medical_history',
        'special_needs_notes',
        'student_status',
        'transfer_date',
        'transfer_destination',
        'transfer_reason',
        'graduation_date',
        'graduation_certificate_number',
        'homeroom_notes',
        'additional_data',
        'completed_at',
        'updated_by',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'previous_diploma_date' => 'date',
        'transfer_date' => 'date',
        'graduation_date' => 'date',
        'additional_data' => 'array',
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function periods()
    {
        return $this->hasMany(StudentMasterBookPeriod::class)->orderBy('school_year')->orderBy('semester');
    }

    public function attachments()
    {
        return $this->hasMany(StudentMasterBookAttachment::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
