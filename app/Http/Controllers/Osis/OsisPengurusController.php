<?php

namespace App\Http\Controllers\Osis;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\MasterSiswa;
use App\Models\OsisPengurus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OsisPengurusController extends Controller
{
    public function index(Request $request): View
    {
        $appSetting = AppSetting::first();
        $query = OsisPengurus::with('masterSiswa');

        if ($request->filled('sekbid')) {
            $query->where('sekbid', $request->sekbid);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('jabatan', 'like', "%{$s}%")
                  ->orWhere('kelas', 'like', "%{$s}%");
            });
        }

        $penguruses = $query->orderBy('urutan')->paginate(15)->withQueryString();
        $sekbids = OsisPengurus::whereNotNull('sekbid')->distinct()->pluck('sekbid');

        return view('pages.osis.pengurus.index', compact('appSetting', 'penguruses', 'sekbids'));
    }

    public function create(): View
    {
        $appSetting = AppSetting::first();
        $students = MasterSiswa::where('status', 'aktif')->orderBy('nama_lengkap')->get(['id', 'nis', 'nama_lengkap']);

        return view('pages.osis.pengurus.create', compact('appSetting', 'students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'sekbid' => 'nullable|string|max:255',
            'kelas' => 'nullable|string|max:100',
            'master_siswa_id' => 'nullable|exists:master_siswa,id',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'foto' => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['urutan'] = $validated['urutan'] ?? 0;

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('osis/pengurus', 'public');
        }

        OsisPengurus::create($validated);

        return redirect()->route('osis.pengurus.index')->with('success', 'Pengurus OSIS berhasil ditambahkan!');
    }

    public function edit(OsisPengurus $pengurus): View
    {
        $appSetting = AppSetting::first();
        $students = MasterSiswa::where('status', 'aktif')->orderBy('nama_lengkap')->get(['id', 'nis', 'nama_lengkap']);

        return view('pages.osis.pengurus.edit', compact('appSetting', 'pengurus', 'students'));
    }

    public function update(Request $request, OsisPengurus $pengurus): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'sekbid' => 'nullable|string|max:255',
            'kelas' => 'nullable|string|max:100',
            'master_siswa_id' => 'nullable|exists:master_siswa,id',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'foto' => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['urutan'] = $validated['urutan'] ?? $pengurus->urutan;

        if ($request->hasFile('foto')) {
            if ($pengurus->foto) {
                Storage::delete($pengurus->foto);
            }
            $validated['foto'] = $request->file('foto')->store('osis/pengurus', 'public');
        }

        $pengurus->update($validated);

        return redirect()->route('osis.pengurus.index')->with('success', 'Data pengurus OSIS berhasil diperbarui!');
    }

    public function destroy(OsisPengurus $pengurus): RedirectResponse
    {
        if ($pengurus->foto) {
            Storage::delete($pengurus->foto);
        }
        $pengurus->delete();

        return redirect()->route('osis.pengurus.index')->with('success', 'Pengurus OSIS berhasil dihapus!');
    }
}
