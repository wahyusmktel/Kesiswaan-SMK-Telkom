<?php

namespace App\Http\Controllers\KaurSpmb;

use App\Http\Controllers\Controller;
use App\Models\SpmbFee;
use App\Models\SpmbWawancaraPertanyaan;
use Illuminate\Http\Request;

class InstrumenWawancaraController extends Controller
{
    /**
     * Display a listing of interview instrument questions.
     */
    public function index(Request $request)
    {
        $kategori = $request->get('kategori', 'siswa');
        if (! in_array($kategori, ['siswa', 'orang_tua'])) {
            $kategori = 'siswa';
        }

        $search = trim($request->get('search', ''));

        $query = SpmbWawancaraPertanyaan::where('kategori', $kategori);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('pertanyaan', 'like', "%{$search}%")
                    ->orWhere('petunjuk', 'like', "%{$search}%")
                    ->orWhere('field_key', 'like', "%{$search}%");
            });
        }

        $pertanyaans = $query->orderBy('nomor_urut', 'asc')->get();

        $totalSiswa = SpmbWawancaraPertanyaan::where('kategori', 'siswa')->count();
        $totalOrtu = SpmbWawancaraPertanyaan::where('kategori', 'orang_tua')->count();
        $totalAktif = SpmbWawancaraPertanyaan::where('kategori', $kategori)->where('is_active', true)->count();

        // Load SPMB Fees for reference in Ortu header
        $spmbFees = SpmbFee::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $totalBiaya = $spmbFees->sum('amount');

        return view('pages.kaur-spmb.instrumen-wawancara.index', compact(
            'kategori',
            'pertanyaans',
            'totalSiswa',
            'totalOrtu',
            'totalAktif',
            'spmbFees',
            'totalBiaya',
            'search'
        ));
    }

    /**
     * Store a newly created question.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:siswa,orang_tua',
            'pertanyaan' => 'required|string|max:500',
            'petunjuk' => 'nullable|string|max:500',
            'tipe_input' => 'required|in:text,textarea,radio,checkbox,scale_1_5,file_upload,date',
            'pilihan_opsi' => 'nullable|string', // separated by newline
            'has_other' => 'nullable|boolean',
            'is_required' => 'nullable|boolean',
            'bobot' => 'nullable|integer|min:0|max:100',
        ]);

        $maxUrut = SpmbWawancaraPertanyaan::where('kategori', $validated['kategori'])->max('nomor_urut') ?? 0;

        $opsiArray = null;
        if (in_array($validated['tipe_input'], ['radio', 'checkbox']) && ! empty($validated['pilihan_opsi'])) {
            $opsiArray = array_values(array_filter(array_map('trim', explode("\n", $validated['pilihan_opsi']))));
        }

        SpmbWawancaraPertanyaan::create([
            'kategori' => $validated['kategori'],
            'nomor_urut' => $maxUrut + 1,
            'pertanyaan' => $validated['pertanyaan'],
            'petunjuk' => $validated['petunjuk'] ?? null,
            'tipe_input' => $validated['tipe_input'],
            'pilihan_opsi' => $opsiArray,
            'has_other' => $request->boolean('has_other'),
            'is_required' => $request->boolean('is_required', true),
            'is_readonly' => false,
            'is_active' => true,
            'bobot' => (int) ($validated['bobot'] ?? 5),
        ]);

        return redirect()->route('kaur-spmb.administrasi.instrumen-wawancara.index', ['kategori' => $validated['kategori']])
            ->with('success', 'Pertanyaan instrumen wawancara berhasil ditambahkan.');
    }

    /**
     * Update the specified question.
     */
    public function update(Request $request, SpmbWawancaraPertanyaan $pertanyaan)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:500',
            'petunjuk' => 'nullable|string|max:500',
            'tipe_input' => 'required|in:text,textarea,radio,checkbox,scale_1_5,file_upload,date',
            'pilihan_opsi' => 'nullable|string',
            'has_other' => 'nullable|boolean',
            'is_required' => 'nullable|boolean',
            'bobot' => 'nullable|integer|min:0|max:100',
        ]);

        $opsiArray = null;
        if (in_array($validated['tipe_input'], ['radio', 'checkbox']) && ! empty($validated['pilihan_opsi'])) {
            $opsiArray = array_values(array_filter(array_map('trim', explode("\n", $validated['pilihan_opsi']))));
        }

        $pertanyaan->update([
            'pertanyaan' => $validated['pertanyaan'],
            'petunjuk' => $validated['petunjuk'] ?? null,
            'tipe_input' => $validated['tipe_input'],
            'pilihan_opsi' => $opsiArray,
            'has_other' => $request->boolean('has_other'),
            'is_required' => $request->boolean('is_required'),
            'bobot' => (int) ($validated['bobot'] ?? $pertanyaan->bobot),
        ]);

        return redirect()->route('kaur-spmb.administrasi.instrumen-wawancara.index', ['kategori' => $pertanyaan->kategori])
            ->with('success', "Pertanyaan nomor {$pertanyaan->nomor_urut} berhasil diperbarui.");
    }

    /**
     * Toggle active status.
     */
    public function toggle(SpmbWawancaraPertanyaan $pertanyaan)
    {
        $pertanyaan->update(['is_active' => ! $pertanyaan->is_active]);

        $statusStr = $pertanyaan->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Pertanyaan nomor {$pertanyaan->nomor_urut} berhasil {$statusStr}.");
    }

    /**
     * Remove the specified question.
     */
    public function destroy(SpmbWawancaraPertanyaan $pertanyaan)
    {
        $kategori = $pertanyaan->kategori;
        $nomor = $pertanyaan->nomor_urut;
        $pertanyaan->delete();

        // Re-index nomor_urut
        $items = SpmbWawancaraPertanyaan::where('kategori', $kategori)->orderBy('nomor_urut')->get();
        foreach ($items as $idx => $item) {
            $item->update(['nomor_urut' => $idx + 1]);
        }

        return redirect()->route('kaur-spmb.administrasi.instrumen-wawancara.index', ['kategori' => $kategori])
            ->with('success', "Pertanyaan nomor {$nomor} berhasil dihapus.");
    }

    /**
     * Reorder questions.
     */
    public function reorder(Request $request)
    {
        $order = $request->input('order', []); // array of IDs in new order
        if (is_array($order)) {
            foreach ($order as $index => $id) {
                SpmbWawancaraPertanyaan::where('id', $id)->update(['nomor_urut' => $index + 1]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Urutan pertanyaan berhasil disimpan.']);
    }
}
