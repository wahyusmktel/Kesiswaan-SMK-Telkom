<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MasterGuru;
use App\Models\TeacherPortfolio;
use App\Models\TeacherProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherPortfolioManagementController extends Controller
{
    /**
     * Show the profile & portfolio management dashboard for the current teacher.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = $this->resolveOrCreateProfile($user);

        $profile->load('portfolios');

        return view('pages.teacher.portfolio.edit', compact('user', 'profile'));
    }

    /**
     * Update basic profile information, bio, skills, education, & certifications.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $this->resolveOrCreateProfile($user);

        $validated = $request->validate([
            'gelar_depan' => ['nullable', 'string', 'max:50'],
            'gelar_belakang' => ['nullable', 'string', 'max:50'],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'bidang_studi' => ['nullable', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'in:guru_produktif,guru_umum,tendik,manajemen'],
            'bio' => ['nullable', 'string', 'max:3000'],
            'filosofi_mengajar' => ['nullable', 'string', 'max:500'],
            'keahlian' => ['nullable', 'string'], // Comma-separated string from input
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'is_published' => ['nullable', 'boolean'],
            'sosial_media.linkedin' => ['nullable', 'url', 'max:255'],
            'sosial_media.github' => ['nullable', 'url', 'max:255'],
            'sosial_media.instagram' => ['nullable', 'url', 'max:255'],
            'sosial_media.website' => ['nullable', 'url', 'max:255'],
            'pendidikan' => ['nullable', 'array'],
            'pendidikan.*.jenjang' => ['nullable', 'string', 'max:50'],
            'pendidikan.*.institusi' => ['nullable', 'string', 'max:150'],
            'pendidikan.*.jurusan' => ['nullable', 'string', 'max:150'],
            'pendidikan.*.tahun_lulus' => ['nullable', 'string', 'max:20'],
            'sertifikasi' => ['nullable', 'array'],
            'sertifikasi.*.nama' => ['nullable', 'string', 'max:150'],
            'sertifikasi.*.penerbit' => ['nullable', 'string', 'max:150'],
            'sertifikasi.*.tahun' => ['nullable', 'string', 'max:20'],
        ]);

        // Process foto upload if any
        if ($request->hasFile('foto')) {
            if ($profile->foto && Storage::disk('public')->exists($profile->foto)) {
                Storage::disk('public')->delete($profile->foto);
            }
            $profile->foto = $request->file('foto')->store('teacher-profiles', 'public');
        }

        // Process keahlian (parse comma-separated to clean array)
        $keahlian = [];
        if (!empty($validated['keahlian'])) {
            $keahlian = array_values(array_filter(array_map('trim', explode(',', $validated['keahlian']))));
        }

        // Process clean education array
        $pendidikan = [];
        if (!empty($validated['pendidikan'])) {
            foreach ($validated['pendidikan'] as $edu) {
                if (!empty($edu['institusi']) || !empty($edu['jenjang'])) {
                    $pendidikan[] = [
                        'jenjang' => trim($edu['jenjang'] ?? ''),
                        'institusi' => trim($edu['institusi'] ?? ''),
                        'jurusan' => trim($edu['jurusan'] ?? ''),
                        'tahun_lulus' => trim($edu['tahun_lulus'] ?? ''),
                    ];
                }
            }
        }

        // Process clean certification array
        $sertifikasi = [];
        if (!empty($validated['sertifikasi'])) {
            foreach ($validated['sertifikasi'] as $cert) {
                if (!empty($cert['nama'])) {
                    $sertifikasi[] = [
                        'nama' => trim($cert['nama'] ?? ''),
                        'penerbit' => trim($cert['penerbit'] ?? ''),
                        'tahun' => trim($cert['tahun'] ?? ''),
                    ];
                }
            }
        }

        $profile->fill([
            'gelar_depan' => $validated['gelar_depan'] ?? null,
            'gelar_belakang' => $validated['gelar_belakang'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
            'bidang_studi' => $validated['bidang_studi'] ?? null,
            'kategori' => $validated['kategori'],
            'bio' => $validated['bio'] ?? null,
            'filosofi_mengajar' => $validated['filosofi_mengajar'] ?? null,
            'keahlian' => $keahlian,
            'riwayat_pendidikan' => $pendidikan,
            'sertifikasi' => $sertifikasi,
            'sosial_media' => array_filter($validated['sosial_media'] ?? []),
            'is_published' => $request->boolean('is_published', true),
        ]);

        $profile->save();

        return redirect()->route('teacher.portfolio.edit')
            ->with('success', 'Profil publik dan portofolio berhasil diperbarui.');
    }

    /**
     * Store a new portfolio item.
     */
    public function storePortfolio(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $this->resolveOrCreateProfile($user);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:3000'],
            'tahun' => ['nullable', 'string', 'max:10'],
            'tautan_proyek' => ['nullable', 'url', 'max:255'],
            'teknologi' => ['nullable', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('teacher-portfolios', 'public');
        }

        $teknologi = [];
        if (!empty($validated['teknologi'])) {
            $teknologi = array_values(array_filter(array_map('trim', explode(',', $validated['teknologi']))));
        }

        $profile->portfolios()->create([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'] ?? 'Karya & Proyek',
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tahun' => $validated['tahun'] ?? date('Y'),
            'tautan_proyek' => $validated['tautan_proyek'] ?? null,
            'gambar' => $gambarPath,
            'teknologi' => $teknologi,
            'is_featured' => $request->boolean('is_featured', false),
            'sort_order' => $profile->portfolios()->count() + 1,
        ]);

        return redirect()->route('teacher.portfolio.edit')
            ->with('success', 'Karya portofolio berhasil ditambahkan.');
    }

    /**
     * Delete a portfolio item.
     */
    public function destroyPortfolio(Request $request, TeacherPortfolio $portfolio): RedirectResponse
    {
        $user = $request->user();
        $profile = $this->resolveOrCreateProfile($user);

        abort_unless($portfolio->teacher_profile_id === $profile->id, 403, 'Anda tidak memiliki hak untuk menghapus karya ini.');

        if ($portfolio->gambar && Storage::disk('public')->exists($portfolio->gambar)) {
            Storage::disk('public')->delete($portfolio->gambar);
        }

        $portfolio->delete();

        return redirect()->route('teacher.portfolio.edit')
            ->with('success', 'Karya portofolio berhasil dihapus.');
    }

    /**
     * Resolve or initialize TeacherProfile for the given User.
     */
    private function resolveOrCreateProfile($user): TeacherProfile
    {
        $profile = TeacherProfile::where('user_id', $user->id)->first();

        if (! $profile) {
            $masterGuru = MasterGuru::where('user_id', $user->id)->first();

            $nama = $masterGuru?->nama_lengkap ?: $user->name;

            $profile = TeacherProfile::create([
                'user_id' => $user->id,
                'master_guru_id' => $masterGuru?->id,
                'nama_lengkap' => $nama,
                'slug' => TeacherProfile::generateUniqueSlug($nama),
                'kategori' => $masterGuru?->employee_category === MasterGuru::CATEGORY_TPA
                    ? TeacherProfile::KATEGORI_TENDIK
                    : TeacherProfile::KATEGORI_PRODUKTIF,
                'is_published' => true,
            ]);
        }

        return $profile;
    }
}
