<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class StudentRegistrationApprovalPact extends Model
{
    public const STATEMENTS = [
        'identity_verified' => 'Saya telah memeriksa identitas setiap calon siswa berdasarkan data dan dokumen yang tersedia.',
        'data_accurate' => 'Saya menyatakan data yang dipilih telah benar, lengkap, dan layak dibuat sebagai data siswa sementara.',
        'dapodik_follow_up' => 'Saya memahami data sementara wajib dicocokkan kembali dengan data resmi Dapodik saat data tersebut tersedia.',
        'accountability' => 'Saya bersedia bertanggung jawab atas persetujuan ini dan dapat mempertanggungjawabkan proses verifikasinya.',
    ];

    protected $fillable = [
        'uuid',
        'approver_user_id',
        'approver_name',
        'approver_email',
        'registration_ids',
        'student_snapshots',
        'statements',
        'approved_count',
        'signed_at',
    ];

    protected $casts = [
        'registration_ids' => 'array',
        'student_snapshots' => 'array',
        'statements' => 'array',
        'signed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (StudentRegistrationApprovalPact $pact) {
            $pact->uuid ??= (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
