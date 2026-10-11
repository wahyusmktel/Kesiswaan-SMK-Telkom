<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\TeacherPortfolio;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;

class PublicTeacherDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $setting = AppSetting::first();
        $schoolName = $setting?->school_name ?: 'SMK Telkom Lampung';

        $search = trim($request->input('q', $request->input('search', '')));
        $kategori = $request->input('kategori');
        $skill = $request->input('skill');

        $query = TeacherProfile::with(['portfolios', 'user'])
            ->where('is_published', true);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%")
                    ->orWhere('bidang_studi', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%")
                    ->orWhereJsonContains('keahlian', $search);
            });
        }

        if ($kategori && array_key_exists($kategori, TeacherProfile::KATEGORI_LABELS)) {
            $query->where('kategori', $kategori);
        }

        if ($skill) {
            $query->whereJsonContains('keahlian', $skill);
        }

        // Order: Featured first, then alphabetical by nama_lengkap
        $teachers = $query->orderByDesc('is_featured')
            ->orderBy('nama_lengkap')
            ->paginate(12)
            ->withQueryString();

        // Summary Statistics for Hero Header
        $totalTeachers = TeacherProfile::where('is_published', true)->count();
        $totalPortfolios = TeacherPortfolio::whereHas('teacherProfile', fn($q) => $q->where('is_published', true))->count();

        // Top popular skills for quick filtering chips
        $allSkills = TeacherProfile::where('is_published', true)
            ->whereNotNull('keahlian')
            ->pluck('keahlian')
            ->flatten()
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(8)
            ->keys();

        // SEO Meta
        $seoTitle = "Direktori & Profil Guru SMK Telkom - Portofolio Pendidik | {$schoolName}";
        $seoDescription = "Jelajahi profil lengkap, keahlian, sertifikasi industri, dan portofolio karya guru serta tenaga pendidik {$schoolName}. Kenali guru hebat kami!";
        $canonicalUrl = route('pegawai.index');

        return view('public.pegawai.index', compact(
            'teachers',
            'setting',
            'schoolName',
            'search',
            'kategori',
            'skill',
            'totalTeachers',
            'totalPortfolios',
            'allSkills',
            'seoTitle',
            'seoDescription',
            'canonicalUrl'
        ));
    }

    public function show(Request $request, string $slug)
    {
        $setting = AppSetting::first();
        $schoolName = $setting?->school_name ?: 'SMK Telkom Lampung';

        // Find by slug first, fallback to ID if numeric
        $teacher = TeacherProfile::with(['portfolios', 'masterGuru.dapodikGuru', 'user'])
            ->where('slug', $slug)
            ->first();

        if (! $teacher && is_numeric($slug)) {
            $teacher = TeacherProfile::with(['portfolios', 'masterGuru.dapodikGuru', 'user'])
                ->find((int) $slug);
        }

        if (! $teacher) {
            abort(404, 'Profil pegawai atau guru tidak ditemukan.');
        }

        // Check publication status: allow viewing if published, or if current user owns it or is admin
        $canPreview = $teacher->is_published
            || ($request->user() && ($request->user()->id === $teacher->user_id || $request->user()->hasRole('Super Admin')));

        if (! $canPreview) {
            abort(404, 'Profil ini belum dipublikasikan untuk umum.');
        }

        // Increment views count safely without touching updated_at
        TeacherProfile::withoutTimestamps(function () use ($teacher) {
            $teacher->increment('views_count');
        });

        $canonicalUrl = route('pegawai.show', $teacher->slug);
        $schoolLogoUrl = $setting?->logo ? asset('storage/' . ltrim($setting->logo, '/')) : null;

        // Structured Data Schema.org (Google Person & ProfilePage)
        $schemaJsonLd = $teacher->getJsonLdSchema($schoolName, $schoolLogoUrl, $canonicalUrl);

        // Related teachers in same category / subject
        $relatedTeachers = TeacherProfile::where('is_published', true)
            ->where('id', '!=', $teacher->id)
            ->where(function ($q) use ($teacher) {
                $q->where('kategori', $teacher->kategori)
                    ->orWhere('bidang_studi', $teacher->bidang_studi);
            })
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('public.pegawai.show', compact(
            'teacher',
            'setting',
            'schoolName',
            'canonicalUrl',
            'schemaJsonLd',
            'relatedTeachers'
        ));
    }
}
