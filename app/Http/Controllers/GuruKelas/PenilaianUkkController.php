<?php

namespace App\Http\Controllers\GuruKelas;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\DigitalDocument;
use App\Models\MasterSiswa;
use App\Models\UkkInstrumenIndikator;
use App\Models\UkkInstrumenSoal;
use App\Models\UkkNilaiKeterampilan;
use App\Models\UkkNilaiPengetahuan;
use App\Models\UkkUjian;
use App\Models\UserDigitalSignature;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PenilaianUkkController extends Controller
{
    private function authorizeAsPenguji(UkkUjian $ujian): void
    {
        abort_unless(
            $ujian->penguji()->where('user_id', Auth::id())->exists(),
            403,
            'Anda tidak terdaftar sebagai penguji untuk ujian ini.'
        );
    }

    public function index()
    {
        $ujians = UkkUjian::with(['tahunPelajaran', 'rombels', 'instrumens'])
            ->whereHas('penguji', fn ($q) => $q->where('user_id', Auth::id()))
            ->latest()
            ->get();

        foreach ($ujians as $ujian) {
            $siswaIds = $ujian->rombels
                ->flatMap(fn ($r) => $r->siswa->pluck('id'))
                ->unique();

            $ujian->total_siswa = $siswaIds->count();

            $soalIds = $ujian->instrumens
                ->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));

            $ujian->sudah_dinilai = $soalIds->isEmpty() ? 0
                : UkkNilaiPengetahuan::whereIn('soal_id', $soalIds)
                    ->whereIn('master_siswa_id', $siswaIds)
                    ->distinct('master_siswa_id')
                    ->count('master_siswa_id');
        }

        return view('pages.guru-kelas.ukk.index', compact('ujians'));
    }

    public function show(UkkUjian $ujian)
    {
        $this->authorizeAsPenguji($ujian);

        $ujian->load([
            'tahunPelajaran',
            'rombels.siswa',
            'instrumens.soalPengetahuan',
            'instrumens.kategoriKeterampilan.indikator',
        ]);

        $siswas = $ujian->rombels
            ->flatMap(fn ($r) => $r->siswa)
            ->unique('id')
            ->sortBy('nama_lengkap')
            ->values();

        $allSoalIds      = $ujian->instrumens->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));
        $allIndikatorIds = $ujian->instrumens->flatMap(fn ($i) =>
            $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id'))
        );

        $totalSoal      = $allSoalIds->count();
        $totalIndikator = $allIndikatorIds->count();

        $siswaIdList = $siswas->pluck('id');

        $gradedP = UkkNilaiPengetahuan::whereIn('soal_id', $allSoalIds)
            ->whereIn('master_siswa_id', $siswaIdList)
            ->selectRaw('master_siswa_id, count(*) as cnt')
            ->groupBy('master_siswa_id')
            ->pluck('cnt', 'master_siswa_id');

        $gradedK = UkkNilaiKeterampilan::whereIn('indikator_id', $allIndikatorIds)
            ->whereIn('master_siswa_id', $siswaIdList)
            ->selectRaw('master_siswa_id, count(*) as cnt')
            ->groupBy('master_siswa_id')
            ->pluck('cnt', 'master_siswa_id');

        return view('pages.guru-kelas.ukk.show', compact(
            'ujian', 'siswas', 'totalSoal', 'totalIndikator', 'gradedP', 'gradedK'
        ));
    }

    public function penilaian(UkkUjian $ujian, MasterSiswa $siswa)
    {
        $this->authorizeAsPenguji($ujian);

        $ujian->load([
            'instrumens.soalPengetahuan',
            'instrumens.kategoriKeterampilan.indikator',
        ]);

        $allSoalIds      = $ujian->instrumens->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));
        $allIndikatorIds = $ujian->instrumens->flatMap(fn ($i) =>
            $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id'))
        );

        $nilaiP = UkkNilaiPengetahuan::where('master_siswa_id', $siswa->id)
            ->whereIn('soal_id', $allSoalIds)
            ->pluck('nilai', 'soal_id');

        $nilaiK = UkkNilaiKeterampilan::where('master_siswa_id', $siswa->id)
            ->whereIn('indikator_id', $allIndikatorIds)
            ->pluck('nilai', 'indikator_id');

        return view('pages.guru-kelas.ukk.penilaian', compact(
            'ujian', 'siswa', 'nilaiP', 'nilaiK'
        ));
    }

    public function simpan(Request $request, UkkUjian $ujian, MasterSiswa $siswa)
    {
        $this->authorizeAsPenguji($ujian);

        $request->validate([
            'pengetahuan'               => 'nullable|array',
            'pengetahuan.*'             => 'integer|in:0,1',
            'keterampilan'              => 'nullable|array',
            'keterampilan.*'            => 'integer|in:0,1,2,3',
        ]);

        // Verify soal & indikator belong to this ujian
        $instrumenIds    = $ujian->instrumens()->pluck('id');
        $validSoalIds    = UkkInstrumenSoal::whereIn('instrumen_id', $instrumenIds)->pluck('id');
        $validIndIds     = UkkInstrumenIndikator::whereIn('kategori_id',
            DB::table('ukk_instrumen_kategoris')->whereIn('instrumen_id', $instrumenIds)->pluck('id')
        )->pluck('id');

        DB::transaction(function () use ($request, $siswa, $validSoalIds, $validIndIds) {
            foreach ($request->input('pengetahuan', []) as $soalId => $nilai) {
                if (!$validSoalIds->contains((int) $soalId)) continue;
                UkkNilaiPengetahuan::updateOrCreate(
                    ['master_siswa_id' => $siswa->id, 'soal_id' => (int) $soalId],
                    ['nilai' => (int) $nilai, 'penguji_id' => Auth::id()]
                );
            }
            foreach ($request->input('keterampilan', []) as $indId => $nilai) {
                if (!$validIndIds->contains((int) $indId)) continue;
                UkkNilaiKeterampilan::updateOrCreate(
                    ['master_siswa_id' => $siswa->id, 'indikator_id' => (int) $indId],
                    ['nilai' => (int) $nilai, 'penguji_id' => Auth::id()]
                );
            }
        });

        return response()->json(['message' => 'Penilaian berhasil disimpan.']);
    }

    public function cetakPdf(UkkUjian $ujian, MasterSiswa $siswa)
    {
        $this->authorizeAsPenguji($ujian);

        $ujian->load([
            'tahunPelajaran',
            'instrumens.soalPengetahuan',
            'instrumens.kategoriKeterampilan.indikator',
        ]);

        $allSoalIds      = $ujian->instrumens->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));
        $allIndikatorIds = $ujian->instrumens->flatMap(fn ($i) =>
            $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id'))
        );

        $nilaiP = UkkNilaiPengetahuan::where('master_siswa_id', $siswa->id)
            ->whereIn('soal_id', $allSoalIds)
            ->pluck('nilai', 'soal_id');

        $nilaiK = UkkNilaiKeterampilan::where('master_siswa_id', $siswa->id)
            ->whereIn('indikator_id', $allIndikatorIds)
            ->pluck('nilai', 'indikator_id');

        // App settings & kop surat
        $settings   = AppSetting::first();
        $logoBase64 = null;
        $kopPath    = $settings->kop_surat_ukk ?? null;
        if ($kopPath && Storage::disk('public')->exists($kopPath)) {
            $kopData    = Storage::disk('public')->get($kopPath);
            $kopMime    = Storage::disk('public')->mimeType($kopPath);
            $logoBase64 = 'data:' . $kopMime . ';base64,' . base64_encode($kopData);
        }

        // Penguji digital signature
        $user      = Auth::user();
        $sigRecord = UserDigitalSignature::where('user_id', $user->id)->first();
        $ttdBase64 = null;
        if ($sigRecord && $sigRecord->ttd_image_path && Storage::disk('public')->exists($sigRecord->ttd_image_path)) {
            $ttdData   = Storage::disk('public')->get($sigRecord->ttd_image_path);
            $ttdMime   = Storage::disk('public')->mimeType($sigRecord->ttd_image_path);
            $ttdBase64 = 'data:' . $ttdMime . ';base64,' . base64_encode($ttdData);
        }

        // Auto-sign if enabled
        $docType  = 'PENILAIAN_UKK_' . $ujian->id;
        $digDoc   = null;
        $qrBase64 = null;

        if ($sigRecord && $sigRecord->isReady() && $sigRecord->auto_sign_penilaian_ukk) {
            $digDoc = DigitalDocument::autoSign(
                $user,
                $docType,
                'Penilaian UKK — ' . $siswa->nama_lengkap . ' (' . $ujian->nama_ujian . ')',
                $siswa->id,
                ['PENILAIAN_UKK', (string) $ujian->id, (string) $siswa->id, $siswa->nama_lengkap]
            );
        } else {
            $digDoc = DigitalDocument::where('document_type', $docType)
                ->where('reference_id', $siswa->id)
                ->where('is_valid', true)
                ->first();
        }

        if ($digDoc) {
            $qrSvg    = QrCode::format('svg')->size(90)->margin(0)->generate(route('verifikasi.dokumen', $digDoc->token));
            $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        }

        // Compute scores per instrumen
        $instrumenScores = $ujian->instrumens->map(function ($ins) use ($nilaiP, $nilaiK) {
            $soalIds     = $ins->soalPengetahuan->pluck('id');
            $totalSoal   = $soalIds->count();
            $benar       = $soalIds->filter(fn ($id) => ($nilaiP[$id] ?? null) === 1)->count();
            $skorP       = $totalSoal > 0 ? round($benar / $totalSoal * 100, 1) : 0;

            $totalIndMax = 0;
            $totalIndVal = 0;
            foreach ($ins->kategoriKeterampilan as $kat) {
                $indIds = $kat->indikator->pluck('id');
                foreach ($indIds as $indId) {
                    $totalIndMax += 3;
                    $totalIndVal += ($nilaiK[$indId] ?? 0);
                }
            }
            $skorK = $totalIndMax > 0 ? round($totalIndVal / $totalIndMax * 100, 1) : 0;

            $bp          = $ins->bobot_pengetahuan / 100;
            $nilaiAkhir  = round($skorP * $bp + $skorK * (1 - $bp), 1);

            return [
                'instrumen'   => $ins,
                'benar'       => $benar,
                'total_soal'  => $totalSoal,
                'skor_p'      => $skorP,
                'skor_k'      => $skorK,
                'nilai_akhir' => $nilaiAkhir,
            ];
        });

        $nilaiAkhirFinal = $instrumenScores->count()
            ? round($instrumenScores->avg('nilai_akhir'), 1)
            : 0;

        $pdf = Pdf::loadView('pdf.penilaian-ukk', compact(
            'ujian', 'siswa', 'nilaiP', 'nilaiK',
            'settings', 'logoBase64',
            'user', 'ttdBase64', 'digDoc', 'qrBase64',
            'instrumenScores', 'nilaiAkhirFinal'
        ))->setPaper('a4', 'portrait');

        $filename = 'Penilaian_UKK_' . str_replace(' ', '_', $siswa->nama_lengkap) . '.pdf';

        return $pdf->stream($filename);
    }
}
