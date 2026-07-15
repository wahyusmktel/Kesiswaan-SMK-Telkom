<?php

namespace App\Models;

use App\Support\TeachingModuleSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeachingModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'program_keahlian',
        'mata_pelajaran_id',
        'mata_pelajaran',
        'fase',
        'nama_penyusun',
        'instansi',
        'tahun_pelajaran_id',
        'tahun_pelajaran',
        'semester',
        'nama_modul',
        'alokasi_waktu',
        'jenjang',
        'kelas',
        'kode_modul',
        'jumlah_murid',
        'lingkup_materi',
        'content',
        'content_version',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'content_version' => 'integer',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('teacher_id', $userId);
    }

    public function normalizedContent(): array
    {
        return TeachingModuleSchema::normalize($this->content, [
            'allocation' => $this->alokasi_waktu,
            'teacher_name' => $this->nama_penyusun,
        ]);
    }

    public function statusLabel(): string
    {
        return $this->status === 'complete' ? 'Lengkap' : 'Draft';
    }
}
