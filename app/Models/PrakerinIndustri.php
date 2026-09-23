<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrakerinIndustri extends Model
{
    use HasFactory;

    public const BENTUK_KERJASAMA_OPTIONS = [
        'Prakerin / PKL Siswa',
        'Penyaluran Lulusan & Alumni (BKK)',
        'Guru Tamu & Praktisi Mengajar',
        'Kelas Industri',
        'Uji Kompetensi Keahlian (UKK)',
        'Sinergi Unit Produksi (UP)',
        'Magang Guru',
        'Beasiswa & Donasi Sarana',
    ];

    public const BIDANG_USAHA_OPTIONS = [
        'Teknologi Informasi & Rekayasa Perangkat Lunak',
        'Jaringan Komputer & Telekomunikasi',
        'Multimedia, Desain & Animasi',
        'Elektronika & Internet of Things (IoT)',
        'Perbankan, Keuangan & Fintech',
        'Manufaktur & Otomotif',
        'Retail & E-Commerce',
        'Pendidikan & Pelatihan',
        'Instansi Pemerintah / BUMN',
        'Lainnya',
    ];

    protected $fillable = [
        'nama_industri',
        'bidang_usaha',
        'website',
        'alamat',
        'kota',
        'provinsi_code',
        'provinsi_name',
        'kabupaten_code',
        'kabupaten_name',
        'kecamatan_code',
        'kecamatan_name',
        'desa_code',
        'desa_name',
        'telepon',
        'email_pic',
        'nama_pic',
        'jabatan_pic',
        'no_hp_pic',
        'nomor_mou',
        'tanggal_mou',
        'tanggal_akhir_mou',
        'is_mou_active',
        'latitude',
        'longitude',
        'catatan_mou',
        'bentuk_kerjasama',
        'file_mou',
    ];

    protected $casts = [
        'tanggal_mou' => 'date',
        'tanggal_akhir_mou' => 'date',
        'is_mou_active' => 'boolean',
        'bentuk_kerjasama' => 'array',
    ];

    public function pembimbings()
    {
        return $this->hasMany(PrakerinPembimbing::class);
    }

    public function rombels()
    {
        return $this->hasMany(PrakerinRombel::class);
    }

    /**
     * Menghitung status masa berlaku MoU
     * Return: 'aktif', 'segera_berakhir', 'berakhir', 'tanpa_mou'
     */
    public function getStatusMouAttribute(): string
    {
        if (!$this->is_mou_active) {
            return 'nonaktif';
        }

        if (!$this->tanggal_akhir_mou) {
            return $this->tanggal_mou ? 'aktif' : 'tanpa_mou';
        }

        $now = Carbon::today();
        if ($this->tanggal_akhir_mou->isPast()) {
            return 'berakhir';
        }

        if ($now->diffInDays($this->tanggal_akhir_mou, false) <= 60) {
            return 'segera_berakhir';
        }

        return 'aktif';
    }

    /**
     * Sisa hari masa berlaku MoU (jika ada tanggal akhir)
     */
    public function getSisaHariMouAttribute(): ?int
    {
        if (!$this->tanggal_akhir_mou) {
            return null;
        }

        return Carbon::today()->diffInDays($this->tanggal_akhir_mou, false);
    }
}
