<?php

namespace App\Http\Controllers\KaurSpmb;

use App\Http\Controllers\Controller;
use App\Models\SpmbTesBacaQuranRubrik;
use Illuminate\Http\Request;

class InstrumenTesBacaQuranController extends Controller
{
    /**
     * Display a listing of Al-Qur'an reading test rubrics & instrument settings.
     */
    public function index()
    {
        // Auto-seed if table is empty so user never needs to input manually
        if (SpmbTesBacaQuranRubrik::count() === 0) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'SpmbTesBacaQuranSeeder',
                '--force' => true,
            ]);
        }

        $rubriks = SpmbTesBacaQuranRubrik::orderBy('nomor_urut', 'asc')->get();

        $totalRubrik = $rubriks->count();
        $totalAktif = $rubriks->where('is_active', true)->count();
        $totalAspekPenilaian = $rubriks->where('kategori', 'penilaian')->count();

        return view('pages.kaur-spmb.instrumen-baca-quran.index', compact(
            'rubriks',
            'totalRubrik',
            'totalAktif',
            'totalAspekPenilaian'
        ));
    }

    /**
     * Store a newly created rubric in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'aspek' => 'required|string|max:255',
            'kategori' => 'required|string|in:penilaian,rekapitulasi,media,catatan',
            'petunjuk' => 'nullable|string|max:500',
            'tipe_input' => 'required|string|in:radio,select,file_upload,textarea',
            'opsi' => 'nullable|array',
            'opsi.*' => 'nullable|string|max:100',
            'skor' => 'nullable|array',
            'skor.*' => 'nullable|numeric|min:0|max:100',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $maxNomor = SpmbTesBacaQuranRubrik::max('nomor_urut') ?? 0;
        $nomorUrut = $maxNomor + 1;

        $pilihanOpsi = null;
        $skorBobot = null;

        if (in_array($validated['tipe_input'], ['radio', 'select']) && ! empty($validated['opsi'])) {
            $pilihanOpsi = array_values(array_filter($validated['opsi']));
            if (! empty($validated['skor'])) {
                $skorBobot = [];
                foreach ($pilihanOpsi as $idx => $opt) {
                    if (isset($validated['skor'][$idx])) {
                        $skorBobot[$opt] = (float) $validated['skor'][$idx];
                    }
                }
            }
        }

        SpmbTesBacaQuranRubrik::create([
            'nomor_urut' => $nomorUrut,
            'kategori' => $validated['kategori'],
            'aspek' => $validated['aspek'],
            'petunjuk' => $validated['petunjuk'],
            'tipe_input' => $validated['tipe_input'],
            'pilihan_opsi' => $pilihanOpsi,
            'skor_bobot' => $skorBobot,
            'is_required' => $request->boolean('is_required', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', "Aspek penilaian '{$validated['aspek']}' berhasil ditambahkan ke Instrumen Tes Baca Al-Qur'an.");
    }

    /**
     * Update the specified rubric in storage.
     */
    public function update(Request $request, SpmbTesBacaQuranRubrik $rubrik)
    {
        $validated = $request->validate([
            'aspek' => 'required|string|max:255',
            'kategori' => 'required|string|in:penilaian,rekapitulasi,media,catatan',
            'petunjuk' => 'nullable|string|max:500',
            'tipe_input' => 'required|string|in:radio,select,file_upload,textarea',
            'opsi' => 'nullable|array',
            'opsi.*' => 'nullable|string|max:100',
            'skor' => 'nullable|array',
            'skor.*' => 'nullable|numeric|min:0|max:100',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $pilihanOpsi = null;
        $skorBobot = null;

        if (in_array($validated['tipe_input'], ['radio', 'select']) && ! empty($validated['opsi'])) {
            $pilihanOpsi = array_values(array_filter($validated['opsi']));
            if (! empty($validated['skor'])) {
                $skorBobot = [];
                foreach ($pilihanOpsi as $idx => $opt) {
                    if (isset($validated['skor'][$idx])) {
                        $skorBobot[$opt] = (float) $validated['skor'][$idx];
                    }
                }
            }
        }

        $rubrik->update([
            'aspek' => $validated['aspek'],
            'kategori' => $validated['kategori'],
            'petunjuk' => $validated['petunjuk'],
            'tipe_input' => $validated['tipe_input'],
            'pilihan_opsi' => $pilihanOpsi,
            'skor_bobot' => $skorBobot,
            'is_required' => $request->boolean('is_required', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', "Aspek penilaian '{$rubrik->aspek}' berhasil diperbarui.");
    }

    /**
     * Remove the specified rubric from storage.
     */
    public function destroy(SpmbTesBacaQuranRubrik $rubrik)
    {
        $aspek = $rubrik->aspek;
        $rubrik->delete();

        // Re-number remaining
        $remaining = SpmbTesBacaQuranRubrik::orderBy('nomor_urut', 'asc')->get();
        foreach ($remaining as $idx => $item) {
            $item->update(['nomor_urut' => $idx + 1]);
        }

        return redirect()->back()->with('success', "Aspek penilaian '{$aspek}' berhasil dihapus dari instrumen.");
    }

    /**
     * Toggle active state of the rubric.
     */
    public function toggle(SpmbTesBacaQuranRubrik $rubrik)
    {
        $rubrik->update(['is_active' => ! $rubrik->is_active]);

        $statusStr = $rubrik->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Aspek '{$rubrik->aspek}' berhasil {$statusStr}.");
    }

    /**
     * Reorder rubrics.
     */
    public function reorder(Request $request)
    {
        $order = $request->input('order', []);

        if (is_array($order)) {
            foreach ($order as $index => $id) {
                SpmbTesBacaQuranRubrik::where('id', $id)->update(['nomor_urut' => $index + 1]);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Urutan instrumen berhasil diperbarui.']);
    }
}
