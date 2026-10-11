<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherProfile extends Model
{
    use HasFactory;

    public const KATEGORI_PRODUKTIF = 'guru_produktif';
    public const KATEGORI_UMUM = 'guru_umum';
    public const KATEGORI_TENDIK = 'tendik';
    public const KATEGORI_MANAJEMEN = 'manajemen';

    public const KATEGORI_LABELS = [
        self::KATEGORI_PRODUKTIF => 'Guru Produktif / Kejuruan',
        self::KATEGORI_UMUM => 'Guru Umum / Normatif-Adaptif',
        self::KATEGORI_TENDIK => 'Tenaga Kependidikan (Tendik)',
        self::KATEGORI_MANAJEMEN => 'Pimpinan & Manajemen',
    ];

    protected $fillable = [
        'master_guru_id',
        'user_id',
        'nama_lengkap',
        'slug',
        'gelar_depan',
        'gelar_belakang',
        'nip',
        'nuptk',
        'jabatan',
        'bidang_studi',
        'kategori',
        'bio',
        'filosofi_mengajar',
        'foto',
        'keahlian',
        'riwayat_pendidikan',
        'sertifikasi',
        'sosial_media',
        'is_published',
        'is_featured',
        'views_count',
    ];

    protected $casts = [
        'keahlian' => 'array',
        'riwayat_pendidikan' => 'array',
        'sertifikasi' => 'array',
        'sosial_media' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function (TeacherProfile $profile) {
            if (empty($profile->slug)) {
                $base = Str::slug($profile->nama_lengkap);
                $profile->slug = static::generateUniqueSlug($base);
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'pegawai';
        $slug = $base;
        $count = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function masterGuru(): BelongsTo
    {
        return $this->belongsTo(MasterGuru::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(TeacherPortfolio::class)->orderBy('sort_order')->orderByDesc('id');
    }

    public function getDisplayNameAttribute(): string
    {
        $depan = $this->gelar_depan ? trim($this->gelar_depan) . ' ' : '';
        $belakang = $this->gelar_belakang ? ', ' . trim($this->gelar_belakang) : '';
        return $depan . trim($this->nama_lengkap) . $belakang;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return Storage::url($this->foto);
        }

        if ($this->user?->avatar) {
            return Storage::url($this->user->avatar);
        }

        return null;
    }

    public function getKategoriLabelAttribute(): string
    {
        return self::KATEGORI_LABELS[$this->kategori] ?? 'Tenaga Pendidik';
    }

    /**
     * SEO Meta Title for Google Search (Under 60 chars recommended)
     */
    public function getSeoMetaTitle(string $schoolName): string
    {
        $role = $this->jabatan ?: ($this->bidang_studi ? 'Guru ' . $this->bidang_studi : 'Pendidik');
        return "Profil & Portofolio {$this->display_name} - {$role} | {$schoolName}";
    }

    /**
     * SEO Meta Description for Google Search (150 - 160 characters)
     */
    public function getSeoMetaDescription(string $schoolName): string
    {
        if ($this->bio) {
            $cleanBio = trim(strip_tags($this->bio));
            return Str::limit("Kenali profil, keahlian, dan portofolio {$this->display_name} ({$this->jabatan}) di {$schoolName}. {$cleanBio}", 158);
        }

        $skills = !empty($this->keahlian) ? implode(', ', array_slice($this->keahlian, 0, 3)) : ($this->bidang_studi ?: 'Pendidikan');
        return Str::limit("Profil, rekam jejak profesional, dan portofolio karya {$this->display_name} sebagai {$this->jabatan} di {$schoolName}. Fokus keahlian: {$skills}.", 158);
    }

    /**
     * SEO Keywords string
     */
    public function getSeoKeywords(string $schoolName): string
    {
        $keywords = [
            $this->nama_lengkap,
            $this->display_name,
            'guru ' . strtolower($schoolName),
            'portofolio ' . strtolower($this->nama_lengkap),
            'profil guru ' . strtolower($schoolName),
            $this->jabatan,
            $this->bidang_studi,
        ];

        if (!empty($this->keahlian)) {
            $keywords = array_merge($keywords, $this->keahlian);
        }

        return implode(', ', array_unique(array_filter($keywords)));
    }

    /**
     * Generate Schema.org ProfilePage & Person JSON-LD Array
     */
    public function getJsonLdSchema(string $schoolName, ?string $schoolLogoUrl, string $canonicalUrl): array
    {
        $sameAs = [];
        if (!empty($this->sosial_media)) {
            foreach ($this->sosial_media as $url) {
                if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                    $sameAs[] = $url;
                }
            }
        }

        $credentials = [];
        if (!empty($this->sertifikasi)) {
            foreach ($this->sertifikasi as $cert) {
                $credentials[] = [
                    '@type' => 'EducationalOccupationalCredential',
                    'name' => $cert['nama'] ?? 'Sertifikasi Profesi',
                    'recognizedBy' => [
                        '@type' => 'Organization',
                        'name' => $cert['penerbit'] ?? $schoolName,
                    ],
                ];
            }
        }

        $alumniOf = [];
        if (!empty($this->riwayat_pendidikan)) {
            foreach ($this->riwayat_pendidikan as $edu) {
                $alumniOf[] = [
                    '@type' => 'EducationalOrganization',
                    'name' => $edu['institusi'] ?? '',
                ];
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            'dateCreated' => $this->created_at?->toIso8601String(),
            'dateModified' => $this->updated_at?->toIso8601String(),
            'mainEntity' => [
                '@type' => 'Person',
                'name' => $this->display_name,
                'alternateName' => $this->nama_lengkap,
                'url' => $canonicalUrl,
                'image' => $this->photo_url ? url($this->photo_url) : ($schoolLogoUrl ?: null),
                'jobTitle' => $this->jabatan ?: 'Guru / Tenaga Pendidik',
                'description' => $this->getSeoMetaDescription($schoolName),
                'worksFor' => [
                    '@type' => 'EducationalOrganization',
                    'name' => $schoolName,
                    'url' => url('/'),
                    'logo' => $schoolLogoUrl,
                ],
                'knowsAbout' => $this->keahlian ?? [],
                'sameAs' => $sameAs,
                'hasCredential' => $credentials,
                'alumniOf' => $alumniOf,
            ],
        ];
    }
}
