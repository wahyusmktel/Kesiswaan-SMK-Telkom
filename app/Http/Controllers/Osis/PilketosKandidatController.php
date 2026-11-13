<?php

namespace App\Http\Controllers\Osis;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\MasterSiswa;
use App\Models\PilketosKandidat;
use App\Models\PilketosPeriode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PilketosKandidatController extends Controller
{
    public function index(): View
    {
        $appSetting = AppSetting::first();
        $periode = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();

        $kandidats = collect();
        if ($periode) {
            $kandidats = $periode->kandidats()->withCount('votes')->get();
        }

        return view('pages.osis.pilketos.kandidat.index', compact('appSetting', 'periode', 'kandidats'));
    }

    public function create(): View
    {
        $appSetting = AppSetting::first();
        $periode = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();
        $students = MasterSiswa::with('rombels.kelas')->where('status', 'aktif')->orderBy('nama_lengkap')->get();

        $nextNumber = $periode ? (($periode->kandidats()->max('nomor_urut') ?? 0) + 1) : 1;

        return view('pages.osis.pilketos.kandidat.create', compact('appSetting', 'periode', 'students', 'nextNumber'));
    }

    public function store(Request $request): RedirectResponse
    {
        $periode = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();
        if (!$periode) {
            return back()->with('error', 'Buat atau aktifkan periode pemilihan terlebih dahulu.');
        }

        $validated = $request->validate([
            'nomor_urut' => 'required|integer|min:1',
            'ketua_siswa_id' => 'nullable|exists:master_siswa,id',
            'nama_ketua' => 'required|string|max:255',
            'nis_ketua' => 'nullable|string|max:50',
            'kelas_ketua' => 'nullable|string|max:100',
            'wakil_siswa_id' => 'nullable|exists:master_siswa,id',
            'nama_wakil' => 'nullable|string|max:255',
            'nis_wakil' => 'nullable|string|max:50',
            'kelas_wakil' => 'nullable|string|max:100',
            'tagline' => 'nullable|string|max:255',
            'visi' => 'required|string',
            'misi' => 'required|string',
            'program_unggulan' => 'nullable|string',
            'video_url' => 'nullable|string|max:255',
            'foto_paslon' => 'nullable|image|max:3072',
            'foto_ketua' => 'nullable|image|max:2048',
            'foto_wakil' => 'nullable|image|max:2048',
        ]);

        $validated['pilketos_periode_id'] = $periode->id;

        // Auto-fill nama/nis/kelas dari master siswa jika dipilih
        if ($request->filled('ketua_siswa_id')) {
            $siswaKetua = MasterSiswa::with('rombels.kelas')->find($request->ketua_siswa_id);
            if ($siswaKetua) {
                $validated['nama_ketua'] = $siswaKetua->nama_lengkap;
                $validated['nis_ketua'] = $siswaKetua->nis;
                $validated['kelas_ketua'] = $validated['kelas_ketua'] ?: ($siswaKetua->rombels->first()?->kelas?->nama_kelas ?? '-');
            }
        }

        if ($request->filled('wakil_siswa_id')) {
            $siswaWakil = MasterSiswa::with('rombels.kelas')->find($request->wakil_siswa_id);
            if ($siswaWakil) {
                $validated['nama_wakil'] = $siswaWakil->nama_lengkap;
                $validated['nis_wakil'] = $siswaWakil->nis;
                $validated['kelas_wakil'] = $validated['kelas_wakil'] ?: ($siswaWakil->rombels->first()?->kelas?->nama_kelas ?? '-');
            }
        }

        if ($request->hasFile('foto_paslon')) {
            $validated['foto_paslon'] = $request->file('foto_paslon')->store('osis/pilketos', 'public');
        }
        if ($request->hasFile('foto_ketua')) {
            $validated['foto_ketua'] = $request->file('foto_ketua')->store('osis/pilketos', 'public');
        }
        if ($request->hasFile('foto_wakil')) {
            $validated['foto_wakil'] = $request->file('foto_wakil')->store('osis/pilketos', 'public');
        }

        PilketosKandidat::create($validated);

        return redirect()->route('osis.pilketos.kandidat.index')->with('success', 'Kandidat Paslon berhasil didaftarkan!');
    }

    public function edit(PilketosKandidat $kandidat): View
    {
        $appSetting = AppSetting::first();
        $students = MasterSiswa::with('rombels.kelas')->where('status', 'aktif')->orderBy('nama_lengkap')->get();

        return view('pages.osis.pilketos.kandidat.edit', compact('appSetting', 'kandidat', 'students'));
    }

    public function update(Request $request, PilketosKandidat $kandidat): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_urut' => 'required|integer|min:1',
            'ketua_siswa_id' => 'nullable|exists:master_siswa,id',
            'nama_ketua' => 'required|string|max:255',
            'nis_ketua' => 'nullable|string|max:50',
            'kelas_ketua' => 'nullable|string|max:100',
            'wakil_siswa_id' => 'nullable|exists:master_siswa,id',
            'nama_wakil' => 'nullable|string|max:255',
            'nis_wakil' => 'nullable|string|max:50',
            'kelas_wakil' => 'nullable|string|max:100',
            'tagline' => 'nullable|string|max:255',
            'visi' => 'required|string',
            'misi' => 'required|string',
            'program_unggulan' => 'nullable|string',
            'video_url' => 'nullable|string|max:255',
            'foto_paslon' => 'nullable|image|max:3072',
            'foto_ketua' => 'nullable|image|max:2048',
            'foto_wakil' => 'nullable|image|max:2048',
        ]);

        if ($request->filled('ketua_siswa_id')) {
            $siswaKetua = MasterSiswa::with('rombels.kelas')->find($request->ketua_siswa_id);
            if ($siswaKetua) {
                $validated['nama_ketua'] = $siswaKetua->nama_lengkap;
                $validated['nis_ketua'] = $siswaKetua->nis;
            }
        }

        if ($request->filled('wakil_siswa_id')) {
            $siswaWakil = MasterSiswa::with('rombels.kelas')->find($request->wakil_siswa_id);
            if ($siswaWakil) {
                $validated['nama_wakil'] = $siswaWakil->nama_lengkap;
                $validated['nis_wakil'] = $siswaWakil->nis;
            }
        }

        if ($request->hasFile('foto_paslon')) {
            if ($kandidat->foto_paslon) { Storage::delete($kandidat->foto_paslon); }
            $validated['foto_paslon'] = $request->file('foto_paslon')->store('osis/pilketos', 'public');
        }
        if ($request->hasFile('foto_ketua')) {
            if ($kandidat->foto_ketua) { Storage::delete($kandidat->foto_ketua); }
            $validated['foto_ketua'] = $request->file('foto_ketua')->store('osis/pilketos', 'public');
        }
        if ($request->hasFile('foto_wakil')) {
            if ($kandidat->foto_wakil) { Storage::delete($kandidat->foto_wakil); }
            $validated['foto_wakil'] = $request->file('foto_wakil')->store('osis/pilketos', 'public');
        }

        $kandidat->update($validated);

        return redirect()->route('osis.pilketos.kandidat.index')->with('success', 'Data kandidat paslon berhasil diperbarui!');
    }

    public function destroy(PilketosKandidat $kandidat): RedirectResponse
    {
        if ($kandidat->foto_paslon) { Storage::delete($kandidat->foto_paslon); }
        if ($kandidat->foto_ketua) { Storage::delete($kandidat->foto_ketua); }
        if ($kandidat->foto_wakil) { Storage::delete($kandidat->foto_wakil); }
        $kandidat->delete();

        return redirect()->route('osis.pilketos.kandidat.index')->with('success', 'Kandidat berhasil dihapus!');
    }
}
