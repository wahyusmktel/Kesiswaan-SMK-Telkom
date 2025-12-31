<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DapodikSubmission extends Model
{
    protected $table = 'dapodik_submissions';

    protected $fillable = [
        'master_siswa_id',
        'old_data',
        'new_data',
        'attachments',
        'status',
        'rejection_reason',
        'operator_id',
        'submitted_at',
        'processed_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'attachments' => 'array',
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function masterSiswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
