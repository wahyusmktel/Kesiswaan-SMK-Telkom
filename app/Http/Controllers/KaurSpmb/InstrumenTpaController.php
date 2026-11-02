<?php

namespace App\Http\Controllers\KaurSpmb;

use App\Http\Controllers\Controller;
use App\Models\SpmbTpaKategori;
use App\Models\SpmbTpaSoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class InstrumenTpaController extends Controller
{
    /**
     * Display a listing of TPA categories & question banks.
     */
    public function index(Request $request)
    {
        // Auto-seed if category table is empty so user never starts with a blank screen
        if (SpmbTpaKategori::count() === 0) {
            Artisan::call('db:seed', [
                '--class' => 'SpmbTpaSeeder',
                '--force' => true,
            ]);
        }

        $kategoris = SpmbTpaKategori::withCount(['soals', 'activeSoals'])
            ->orderBy('nomor_urut', 'asc')
            ->get();

        // Selected category
        $selectedKode = $request->query('kategori', $kategoris->first()?->kode ?? 'BINDO');
        $currentKategori = $kategoris->firstWhere('kode', $selectedKode) ?? $kategoris->first();

        // Query questions
        $search = $request->query('q');
        $soalsQuery = SpmbTpaSoal::with('kategori')
            ->when($currentKategori, function ($query) use ($currentKategori) {
                $query->where('spmb_tpa_kategori_id', $currentKategori->id);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pertanyaan', 'like', "%{$search}%")
                        ->orWhere('pembahasan', 'like', "%{$search}%")
                        ->orWhere('opsi_a', 'like', "%{$search}%")
                        ->orWhere('opsi_b', 'like', "%{$search}%")
                        ->orWhere('opsi_c', 'like', "%{$search}%")
                        ->orWhere('opsi_d', 'like', "%{$search}%");
                });
            })
            ->orderBy('nomor_urut', 'asc');

        $soals = $soalsQuery->paginate(15)->withQueryString();

        // Global stats
        $totalBankSoal = SpmbTpaSoal::count();
        $totalSoalAktif = SpmbTpaSoal::where('is_active', true)->count();
        $totalDurasiMenit = $kategoris->sum('durasi_menit');
        $avgPassingGrade = $kategoris->count() > 0 ? round($kategoris->avg('passing_grade')) : 65;

        return view('pages.kaur-spmb.instrumen-tpa.index', compact(
            'kategoris',
            'currentKategori',
            'soals',
            'totalBankSoal',
            'totalSoalAktif',
            'totalDurasiMenit',
            'avgPassingGrade',
            'search'
        ));
    }

    /**
     * Store a newly created question in storage.
     */
    public function storeSoal(Request $request)
    {
        $validated = $request->validate([
            'spmb_tpa_kategori_id' => 'required|exists:spmb_tpa_kategoris,id',
            'pertanyaan' => 'required|string',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'opsi_e' => 'nullable|string',
            'kunci_jawaban' => 'required|in:A,B,C,D,E',
            'tingkat_kesulitan' => 'required|in:Mudah,Sedang,Sulit',
            'bobot' => 'nullable|integer|min:1|max:100',
            'pembahasan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $maxNomor = SpmbTpaSoal::where('spmb_tpa_kategori_id', $validated['spmb_tpa_kategori_id'])->max('nomor_urut') ?? 0;

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('spmb/tpa', 'public');
        }

        SpmbTpaSoal::create([
            'spmb_tpa_kategori_id' => $validated['spmb_tpa_kategori_id'],
            'nomor_urut' => $maxNomor + 1,
            'pertanyaan' => $validated['pertanyaan'],
            'gambar' => $gambarPath,
            'tipe_soal' => 'pilihan_ganda',
            'opsi_a' => $validated['opsi_a'],
            'opsi_b' => $validated['opsi_b'],
            'opsi_c' => $validated['opsi_c'],
            'opsi_d' => $validated['opsi_d'],
            'opsi_e' => $validated['opsi_e'] ?? null,
            'kunci_jawaban' => $validated['kunci_jawaban'],
            'tingkat_kesulitan' => $validated['tingkat_kesulitan'],
            'bobot' => $validated['bobot'] ?? 1,
            'pembahasan' => $validated['pembahasan'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $kategori = SpmbTpaKategori::find($validated['spmb_tpa_kategori_id']);

        return redirect()->route('kaur-spmb.administrasi.instrumen-tpa.index', ['kategori' => $kategori?->kode])
            ->with('success', 'Butir soal baru berhasil ditambahkan ke bank soal TPA.');
    }

    /**
     * Update the specified question in storage.
     */
    public function updateSoal(Request $request, SpmbTpaSoal $soal)
    {
        $validated = $request->validate([
            'spmb_tpa_kategori_id' => 'required|exists:spmb_tpa_kategoris,id',
            'pertanyaan' => 'required|string',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'opsi_e' => 'nullable|string',
            'kunci_jawaban' => 'required|in:A,B,C,D,E',
            'tingkat_kesulitan' => 'required|in:Mudah,Sedang,Sulit',
            'bobot' => 'nullable|integer|min:1|max:100',
            'pembahasan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'hapus_gambar' => 'nullable|boolean',
        ]);

        $gambarPath = $soal->gambar;
        if ($request->boolean('hapus_gambar') && $gambarPath) {
            if (! str_starts_with($gambarPath, 'images/')) {
                Storage::disk('public')->delete($gambarPath);
            }
            $gambarPath = null;
        }

        if ($request->hasFile('gambar')) {
            if ($soal->gambar && ! str_starts_with($soal->gambar, 'images/')) {
                Storage::disk('public')->delete($soal->gambar);
            }
            $gambarPath = $request->file('gambar')->store('spmb/tpa', 'public');
        }

        $soal->update([
            'spmb_tpa_kategori_id' => $validated['spmb_tpa_kategori_id'],
            'pertanyaan' => $validated['pertanyaan'],
            'gambar' => $gambarPath,
            'opsi_a' => $validated['opsi_a'],
            'opsi_b' => $validated['opsi_b'],
            'opsi_c' => $validated['opsi_c'],
            'opsi_d' => $validated['opsi_d'],
            'opsi_e' => $validated['opsi_e'] ?? null,
            'kunci_jawaban' => $validated['kunci_jawaban'],
            'tingkat_kesulitan' => $validated['tingkat_kesulitan'],
            'bobot' => $validated['bobot'] ?? 1,
            'pembahasan' => $validated['pembahasan'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('kaur-spmb.administrasi.instrumen-tpa.index', ['kategori' => $soal->kategori?->kode])
            ->with('success', "Soal No. {$soal->nomor_urut} berhasil diperbarui.");
    }

    /**
     * Remove the specified question.
     */
    public function destroySoal(SpmbTpaSoal $soal)
    {
        $kategoriKode = $soal->kategori?->kode;

        if ($soal->gambar && ! str_starts_with($soal->gambar, 'images/')) {
            Storage::disk('public')->delete($soal->gambar);
        }

        $nomor = $soal->nomor_urut;
        $soal->delete();

        return redirect()->route('kaur-spmb.administrasi.instrumen-tpa.index', ['kategori' => $kategoriKode])
            ->with('success', "Soal No. {$nomor} berhasil dihapus.");
    }

    /**
     * Toggle active status of a question.
     */
    public function toggleSoal(SpmbTpaSoal $soal)
    {
        $soal->update(['is_active' => ! $soal->is_active]);

        return redirect()->back()->with('success', "Status soal No. {$soal->nomor_urut} berhasil diubah.");
    }

    /**
     * Update settings of a category (durasi, passing grade, jumlah soal tampil).
     */
    public function updateKategori(Request $request, SpmbTpaKategori $kategori)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
            'durasi_menit' => 'required|integer|min:5|max:180',
            'passing_grade' => 'required|integer|min:0|max:100',
            'jumlah_soal_ditampilkan' => 'required|integer|min:1|max:100',
            'warna_tema' => 'required|in:red,blue,purple,emerald,amber,indigo',
            'is_active' => 'nullable|boolean',
        ]);

        $kategori->update([
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'durasi_menit' => $validated['durasi_menit'],
            'passing_grade' => $validated['passing_grade'],
            'jumlah_soal_ditampilkan' => $validated['jumlah_soal_ditampilkan'],
            'warna_tema' => $validated['warna_tema'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('kaur-spmb.administrasi.instrumen-tpa.index', ['kategori' => $kategori->kode])
            ->with('success', "Pengaturan mata uji {$kategori->nama} berhasil disimpan.");
    }

    /**
     * Reset / re-seed TPA questions from default database seeder.
     */
    public function resetSeeder()
    {
        Artisan::call('db:seed', [
            '--class' => 'SpmbTpaSeeder',
            '--force' => true,
        ]);

        return redirect()->route('kaur-spmb.administrasi.instrumen-tpa.index')
            ->with('success', 'Bank soal dan kategori TPA berhasil disinkronkan ulang dengan bank soal standar.');
    }
}
