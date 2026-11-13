<?php

namespace App\Http\Controllers\Osis;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\PilketosPeriode;
use App\Models\PilketosTimeline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PilketosManagementController extends Controller
{
    public function index(): View
    {
        $appSetting = AppSetting::first();
        $periode = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();

        if (!$periode) {
            $periode = PilketosPeriode::create([
                'nama_periode' => 'Pemilihan Ketua & Wakil Ketua OSIS ' . date('Y') . '/' . (date('Y') + 1),
                'tahun_pelajaran' => date('Y') . '/' . (date('Y') + 1),
                'status' => 'draft',
                'is_active' => true,
            ]);
        }

        $timelines = $periode->timelines;

        return view('pages.osis.pilketos.periode', compact('appSetting', 'periode', 'timelines'));
    }

    public function updatePeriode(Request $request, PilketosPeriode $periode): RedirectResponse
    {
        $validated = $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tahun_pelajaran' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:draft,aktif,voting_berlangsung,selesai',
            'tgl_mulai_voting' => 'nullable|date',
            'tgl_selesai_voting' => 'nullable|date',
        ]);

        $periode->update($validated);

        return redirect()->route('osis.pilketos.periode.index')->with('success', 'Pengaturan periode pemilihan berhasil diperbarui!');
    }

    public function storeTimeline(Request $request, PilketosPeriode $periode): RedirectResponse
    {
        $validated = $request->validate([
            'judul_tahap' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'deskripsi' => 'nullable|string',
            'status_tahap' => 'required|in:akan_datang,berlangsung,selesai',
            'urutan' => 'nullable|integer',
        ]);

        $validated['pilketos_periode_id'] = $periode->id;
        $validated['urutan'] = $validated['urutan'] ?? (($periode->timelines()->max('urutan') ?? 0) + 1);

        PilketosTimeline::create($validated);

        return redirect()->route('osis.pilketos.periode.index')->with('success', 'Tahapan timeline berhasil ditambahkan!');
    }

    public function updateTimeline(Request $request, PilketosTimeline $timeline): RedirectResponse
    {
        $validated = $request->validate([
            'judul_tahap' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'deskripsi' => 'nullable|string',
            'status_tahap' => 'required|in:akan_datang,berlangsung,selesai',
            'urutan' => 'nullable|integer',
        ]);

        $timeline->update($validated);

        return redirect()->route('osis.pilketos.periode.index')->with('success', 'Tahapan timeline berhasil diperbarui!');
    }

    public function destroyTimeline(PilketosTimeline $timeline): RedirectResponse
    {
        $timeline->delete();

        return redirect()->route('osis.pilketos.periode.index')->with('success', 'Tahapan timeline berhasil dihapus!');
    }
}
