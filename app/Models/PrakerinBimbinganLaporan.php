<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrakerinBimbinganLaporan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prakerin_bimbingan_laporans';

    protected $fillable = [
        'prakerin_penempatan_id',
        'judul_laporan',
        'deskripsi_judul',
        'status_judul',
        'catatan_judul',
        'judul_diajukan_at',
        'judul_reviewed_at',
        'status_laporan',
        'catatan_acc',
        'acc_at',
        'acc_by',
    ];

    protected $casts = [
        'judul_diajukan_at' => 'datetime',
        'judul_reviewed_at' => 'datetime',
        'acc_at' => 'datetime',
    ];

    /**
     * Template bab bawaan untuk laporan prakerin baru.
     */
    public const DEFAULT_TAHAP_TEMPLATES = [
        [
            'judul_tahap' => 'Bagian Depan & Lembar Pengesahan',
            'deskripsi' => 'Cover/sampul, halaman pengesahan sekolah & industri, kata pengantar, dan daftar isi.',
            'urutan' => 1,
        ],
        [
            'judul_tahap' => 'BAB I: Pendahuluan',
            'deskripsi' => 'Latar belakang pelaksanaan PKL, maksud & tujuan, waktu & tempat pelaksanaan.',
            'urutan' => 2,
        ],
        [
            'judul_tahap' => 'BAB II: Gambaran Umum Perusahaan',
            'deskripsi' => 'Sejarah singkat, struktur organisasi, visi misi, dan bidang usaha industri mitra.',
            'urutan' => 3,
        ],
        [
            'judul_tahap' => 'BAB III: Pelaksanaan & Pembahasan PKL',
            'deskripsi' => 'Uraian kegiatan/tugas prakerin, kompetensi yang dipelajari, kendala & solusi pemecahan.',
            'urutan' => 4,
        ],
        [
            'judul_tahap' => 'BAB IV: Penutup & Kesimpulan',
            'deskripsi' => 'Kesimpulan hasil pelaksanaan PKL serta saran untuk sekolah dan industri.',
            'urutan' => 5,
        ],
        [
            'judul_tahap' => 'Lampiran & Dokumentasi',
            'deskripsi' => 'Foto kegiatan, jurnal kegiatan mingguan, sertifikat atau dokumen pendukung lainnya.',
            'urutan' => 6,
        ],
    ];

    public function penempatan()
    {
        return $this->belongsTo(PrakerinPenempatan::class, 'prakerin_penempatan_id');
    }

    public function accBy()
    {
        return $this->belongsTo(User::class, 'acc_by');
    }

    public function tahaps()
    {
        return $this->hasMany(PrakerinBimbinganTahap::class, 'prakerin_bimbingan_laporan_id')->orderBy('urutan');
    }

    public function aktivitasLogs()
    {
        return $this->hasMany(PrakerinBimbinganAktivitasLog::class, 'prakerin_bimbingan_laporan_id')->latest();
    }

    /**
     * Hitung progres bab yang disetujui (persentase 0-100%).
     */
    public function getPersentaseSelesaiAttribute(): int
    {
        $total = $this->tahaps()->count();
        if ($total === 0) return 0;

        $disetujui = $this->tahaps()->where('status', 'disetujui')->count();

        return (int) round(($disetujui / $total) * 100);
    }

    /**
     * Cek apakah seluruh bab telah disetujui.
     */
    public function getSemuaBabDisetujuiAttribute(): bool
    {
        $tahaps = $this->tahaps;
        if ($tahaps->isEmpty()) return false;

        return $tahaps->every(fn ($t) => $t->status === 'disetujui');
    }

    /**
     * Ambil tahap terakhir yang sedang aktif atau perlu direview.
     */
    public function getTahapTerakhirAttribute()
    {
        return $this->tahaps()
            ->whereNotNull('file_pdf_path')
            ->orderByDesc('updated_at')
            ->first();
    }

    public function getJudulAttribute(): ?string
    {
        return $this->judul_laporan;
    }

    public function getAbstrakRencanaAttribute(): ?string
    {
        return $this->deskripsi_judul;
    }

    public function getCatatanPembimbingAttribute(): ?string
    {
        return $this->catatan_judul;
    }

    public function getStatusAttribute(): string
    {
        return $this->status_laporan === 'selesai_acc' ? 'selesai' : ($this->status_laporan ?: 'draft');
    }

    public function getJudulDisetujuiAtAttribute()
    {
        return $this->judul_reviewed_at;
    }

    public function getJudulStatusAttribute(): string
    {
        return $this->attributes['status_judul'] ?? 'draft';
    }

    public function getStatusJudulAttribute($value): string
    {
        return $value ?? 'draft';
    }
}
