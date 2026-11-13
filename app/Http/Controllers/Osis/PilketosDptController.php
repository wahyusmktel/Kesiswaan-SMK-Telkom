<?php

namespace App\Http\Controllers\Osis;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\PilketosDpt;
use App\Models\PilketosPeriode;
use App\Models\Rombel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PilketosDptController extends Controller
{
    public function index(Request $request): View
    {
        $appSetting = AppSetting::first();
        $periode = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();

        $query = PilketosDpt::query();

        if ($periode) {
            $query->where('pilketos_periode_id', $periode->id);
        }

        if ($request->filled('status')) {
            $query->where('status_memilih', $request->status);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_pemilih', 'like', "%{$s}%")
                  ->orWhere('nis', 'like', "%{$s}%")
                  ->orWhere('barcode_token', 'like', "%{$s}%");
            });
        }

        $dpts = $query->orderBy('kelas')->orderBy('nama_pemilih')->paginate(20)->withQueryString();

        $kelasList = PilketosDpt::where('pilketos_periode_id', $periode?->id)->distinct()->pluck('kelas');

        $totalDpt = $periode ? $periode->dpts()->count() : 0;
        $totalSudah = $periode ? $periode->dpts()->where('status_memilih', 'sudah_memilih')->count() : 0;
        $totalBelum = $totalDpt - $totalSudah;

        $rombels = Rombel::with('kelas')->get();

        return view('pages.osis.pilketos.dpt.index', compact(
            'appSetting',
            'periode',
            'dpts',
            'kelasList',
            'totalDpt',
            'totalSudah',
            'totalBelum',
            'rombels'
        ));
    }

    /**
     * Generate DPT otomatis dari Master Siswa / Rombel
     */
    public function generateFromSiswa(Request $request): RedirectResponse
    {
        $periode = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();
        if (!$periode) {
            return back()->with('error', 'Tidak ada periode pemilihan yang aktif.');
        }

        $request->validate([
            'source' => 'required|in:all,rombel',
            'rombel_id' => 'nullable|exists:rombels,id',
        ]);

        $query = MasterSiswa::with('rombels.kelas')->where('status', 'aktif');

        if ($request->source === 'rombel' && $request->filled('rombel_id')) {
            $query->whereHas('rombels', function ($q) use ($request) {
                $q->where('rombels.id', $request->rombel_id);
            });
        }

        $students = $query->get();
        $generatedCount = 0;

        foreach ($students as $siswa) {
            // Cek apakah sudah terdaftar di DPT periode ini
            $exists = PilketosDpt::where('pilketos_periode_id', $periode->id)
                ->where(function ($q) use ($siswa) {
                    $q->where('master_siswa_id', $siswa->id)
                      ->orWhere('nis', $siswa->nis);
                })
                ->exists();

            if (!$exists) {
                $kelasNama = $siswa->rombels->first()?->kelas?->nama_kelas ?? 'Umum';
                $token = 'PIL-26-' . str_pad($siswa->nis, 8, '0', STR_PAD_LEFT);

                // Pastikan token unik
                while (PilketosDpt::where('barcode_token', $token)->exists()) {
                    $token = PilketosDpt::generateUniqueBarcodeToken();
                }

                PilketosDpt::create([
                    'pilketos_periode_id' => $periode->id,
                    'master_siswa_id' => $siswa->id,
                    'nama_pemilih' => $siswa->nama_lengkap,
                    'nis' => $siswa->nis,
                    'nisn' => $siswa->nisn ?? null,
                    'kelas' => $kelasNama,
                    'jenis_kelamin' => $siswa->jenis_kelamin,
                    'barcode_token' => $token,
                    'status_memilih' => 'belum_memilih',
                ]);

                $generatedCount++;
            }
        }

        return redirect()->route('osis.pilketos.dpt.index')
            ->with('success', "Berhasil menambahkan {$generatedCount} siswa ke dalam DPT Pemilihan Ketua OSIS!");
    }

    /**
     * Tampilan Cetak Kartu Pemilih Berbarcode (Code 128)
     */
    public function printCards(Request $request): View
    {
        $appSetting = AppSetting::first();
        $periode = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();

        $query = PilketosDpt::query();
        if ($periode) {
            $query->where('pilketos_periode_id', $periode->id);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('status')) {
            $query->where('status_memilih', $request->status);
        }

        $voters = $query->orderBy('kelas')->orderBy('nama_pemilih')->get();

        return view('pages.osis.pilketos.dpt.print-cards', compact('appSetting', 'periode', 'voters'));
    }

    /**
     * Reset Status Pemilih (jika ada kendala di bilik)
     */
    public function resetStatus(PilketosDpt $dpt): RedirectResponse
    {
        $dpt->update([
            'status_memilih' => 'belum_memilih',
            'waktu_memilih' => null,
            'bilik_id' => null,
        ]);

        return back()->with('success', "Status pemilih {$dpt->nama_pemilih} berhasil di-reset ke 'Belum Memilih'.");
    }

    public function destroy(PilketosDpt $dpt): RedirectResponse
    {
        $dpt->delete();

        return back()->with('success', 'Data pemilih berhasil dihapus dari DPT.');
    }
}
