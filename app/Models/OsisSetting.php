<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OsisSetting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'nama_organisasi' => 'OSIS SMK Telkom Lampung',
            'semboyan' => 'Kreatif, Inovatif, Berkarakter & Berintegritas',
            'deskripsi' => 'Organisasi Siswa Intra Sekolah (OSIS) SMK Telkom Lampung adalah wadah utama pembinaan kepemimpinan, kreativitas, keagamaan, dan keterampilan siswa untuk mewujudkan generasi unggul berstandar industri dan berakhlak mulia.',
            'visi' => "Mewujudkan OSIS SMK Telkom Lampung sebagai episentrum kepemimpinan digital yang inovatif, berkarakter, inklusif, dan berdaya saing global dengan berlandaskan integritas dan nilai-nilai luhur.",
            'misi' => "1. Menumbuhkan budaya religius, disiplin, dan budi pekerti luhur di lingkungan sekolah.\n2. Mengembangkan potensi kepemimpinan dan soft skill siswa melalui program kerja berbasis teknologi dan industri kreatif.\n3. Memfasilitasi aspirasi dan minat bakat siswa dalam bidang akademik, seni, olahraga, dan teknologi.\n4. Membangun kolaborasi sinergis antara siswa, guru, alumni, dan dunia industri.\n5. Menjadi garda terdepan dalam menjaga nama baik dan prestasi SMK Telkom Lampung.",
            'periode_aktif' => '2025/2026',
            'nama_ketua_existing' => 'M. Fathan Pratama',
            'nama_wakil_existing' => 'Nabila Putri Zahra',
            'visi_misi_ketua_existing' => "Fokus pada akselerasi literasi teknologi, penguatan jiwa entrepreneurship siswa, dan ruang berekspresi yang terbuka bagi seluruh jurusan di SMK Telkom Lampung.",
            'pesan_ketua_existing' => 'Pemimpin yang hebat bukan mereka yang berdiri paling depan sendirian, tetapi mereka yang mampu membangkitkan potensi di sekelilingnya untuk melangkah bersama.',
            'instagram' => 'osissmktelkomlampung',
            'tiktok' => 'osissmktelkomlpg',
            'email' => 'osis@smktelkom-lpg.sch.id',
        ]);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    public function getFotoStrukturUrlAttribute(): ?string
    {
        return $this->foto_struktur ? Storage::url($this->foto_struktur) : null;
    }

    public function getFotoKetuaUrlAttribute(): string
    {
        return $this->foto_ketua_existing
            ? Storage::url($this->foto_ketua_existing)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_ketua_existing ?? 'Ketua OSIS') . '&background=dc2626&color=ffffff';
    }

    public function getFotoWakilUrlAttribute(): string
    {
        return $this->foto_wakil_existing
            ? Storage::url($this->foto_wakil_existing)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_wakil_existing ?? 'Wakil Ketua') . '&background=2563eb&color=ffffff';
    }
}
