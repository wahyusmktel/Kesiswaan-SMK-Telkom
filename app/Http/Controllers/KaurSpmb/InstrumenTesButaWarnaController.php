<?php

namespace App\Http\Controllers\KaurSpmb;

use App\Http\Controllers\Controller;
use App\Models\SpmbTesButaWarnaSoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InstrumenTesButaWarnaController extends Controller
{
    /**
     * Display a listing of color blind test instrument questions.
     */
    public function index()
    {
        // Auto-seed if table is empty
        if (SpmbTesButaWarnaSoal::count() === 0) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'SpmbTesButaWarnaSeeder',
                '--force' => true,
            ]);
        }

        $soals = SpmbTesButaWarnaSoal::orderBy('nomor_urut', 'asc')->get();

        $totalSoal = $soals->count();
        $totalAktif = $soals->where('is_active', true)->count();
        $totalBobot = $soals->sum('bobot');

        return view('pages.kaur-spmb.instrumen-buta-warna.index', compact(
            'soals',
            'totalSoal',
            'totalAktif',
            'totalBobot'
        ));
    }

    /**
     * Store a newly created question.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'petunjuk' => 'nullable|string|max:500',
            'gambar_file' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'opsi' => 'required|array|min:3|max:5',
            'opsi.*' => 'required|string|max:100',
            'kunci_jawaban' => 'required|string|max:100',
            'bobot' => 'required|integer|min:1|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $maxNomor = SpmbTesButaWarnaSoal::max('nomor_urut') ?? 0;
        $nomorUrut = $maxNomor + 1;

        $path = $request->file('gambar_file')->store('spmb-buta-warna', 'public');

        SpmbTesButaWarnaSoal::create([
            'nomor_urut' => $nomorUrut,
            'judul' => $validated['judul'],
            'petunjuk' => $validated['petunjuk'] ?? 'Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut',
            'gambar' => $path,
            'pilihan_opsi' => array_values(array_filter($validated['opsi'])),
            'kunci_jawaban' => $validated['kunci_jawaban'],
            'bobot' => $validated['bobot'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', "Soal baru #{$nomorUrut} berhasil ditambahkan ke Instrumen Tes Buta Warna.");
    }

    /**
     * Update the specified question.
     */
    public function update(Request $request, SpmbTesButaWarnaSoal $soal)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'petunjuk' => 'nullable|string|max:500',
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'opsi' => 'required|array|min:3|max:5',
            'opsi.*' => 'required|string|max:100',
            'kunci_jawaban' => 'required|string|max:100',
            'bobot' => 'required|integer|min:1|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $updateData = [
            'judul' => $validated['judul'],
            'petunjuk' => $validated['petunjuk'] ?? 'Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut',
            'pilihan_opsi' => array_values(array_filter($validated['opsi'])),
            'kunci_jawaban' => $validated['kunci_jawaban'],
            'bobot' => $validated['bobot'],
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('gambar_file')) {
            // Delete old file if in public storage
            if (! str_starts_with($soal->gambar, 'images/') && Storage::disk('public')->exists($soal->gambar)) {
                Storage::disk('public')->delete($soal->gambar);
            }
            $updateData['gambar'] = $request->file('gambar_file')->store('spmb-buta-warna', 'public');
        }

        $soal->update($updateData);

        return redirect()->back()->with('success', "Soal #{$soal->nomor_urut} ({$soal->judul}) berhasil diperbarui.");
    }

    /**
     * Remove the specified question.
     */
    public function destroy(SpmbTesButaWarnaSoal $soal)
    {
        $nomor = $soal->nomor_urut;

        // Delete image if uploaded to storage
        if (! str_starts_with($soal->gambar, 'images/') && Storage::disk('public')->exists($soal->gambar)) {
            Storage::disk('public')->delete($soal->gambar);
        }

        $soal->delete();

        // Re-number remaining questions
        $remaining = SpmbTesButaWarnaSoal::orderBy('nomor_urut', 'asc')->get();
        foreach ($remaining as $idx => $item) {
            $item->update(['nomor_urut' => $idx + 1]);
        }

        return redirect()->back()->with('success', "Soal #{$nomor} berhasil dihapus dari instrumen tes buta warna.");
    }

    /**
     * Toggle active state of question.
     */
    public function toggle(SpmbTesButaWarnaSoal $soal)
    {
        $soal->update(['is_active' => ! $soal->is_active]);

        $statusStr = $soal->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Soal #{$soal->nomor_urut} berhasil {$statusStr}.");
    }

    /**
     * Reorder questions.
     */
    public function reorder(Request $request)
    {
        $order = $request->input('order', []);

        if (is_array($order)) {
            foreach ($order as $index => $id) {
                SpmbTesButaWarnaSoal::where('id', $id)->update(['nomor_urut' => $index + 1]);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Urutan soal berhasil diperbarui.']);
    }
}
