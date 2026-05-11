<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DapodikGuruSubmission extends Model
{
    protected $table = 'dapodik_guru_submissions';

    protected $fillable = [
        'master_guru_id',
        'old_data',
        'new_data',
        'status',
        'rejection_reason',
        'operator_id',
        'submitted_at',
        'processed_at',
    ];

    protected $casts = [
        'old_data'     => 'array',
        'new_data'     => 'array',
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function masterGuru()
    {
        return $this->belongsTo(MasterGuru::class, 'master_guru_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
