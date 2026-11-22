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
use App\Models\UkkNilaiSikap;
use App\Models\UkkPenguji;
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
        $isPenguji = UkkPenguji::where('ukk_ujian_id', $ujian->id)
            ->where('user_id', Auth::id())
            ->exists()
            || DB::table('ukk_penguji')->where('ukk_ujian_id', $ujian->id)->where('user_id', Auth::id())->exists();

        abort_unless(
            $isPenguji,
            403,
            'Anda tidak terdaftar sebagai penguji untuk ujian ini.'
        );
    }

    public function index()
    {
        $ujians = UkkUjian::with(['tahunPelajaran', 'rombels.siswa', 'instrumens.soalPengetahuan', 'ukkPengujis'])
            ->whereHas('ukkPengujis', fn ($q) => $q->where('user_id', Auth::id()))
            ->latest()
            ->get();

        foreach ($ujians as $ujian) {
            $pengujiRecords = $ujian->ukkPengujis->where('user_id', Auth::id());
            $hasGlobalAssignment = $pengujiRecords->contains(fn ($p) => is_null($p->rombel_id));
            $assignedRombelIds = $pengujiRecords->pluck('rombel_id')->filter()->unique();

            $filteredRombels = ($hasGlobalAssignment || $assignedRombelIds->isEmpty())
                ? $ujian->rombels
                : $ujian->rombels->whereIn('id', $assignedRombelIds);

            $siswaIds = collect();
            foreach ($filteredRombels as $rombel) {
                $rombelSiswas = $rombel->siswa->sortBy('nama_lengkap')->values();
                $pengujisForThisRombel = $pengujiRecords->filter(fn ($p) => $p->rombel_id === $rombel->id || is_null($p->rombel_id));

                $hasUnlimited = false;
                $allowedSiswaIds = collect();

                foreach ($pengujisForThisRombel as $p) {
                    if (is_array($p->siswa_ids) && !empty($p->siswa_ids)) {
                        $allowedSiswaIds = $allowedSiswaIds->merge($p->siswa_ids);
                    } elseif ($p->no_urut_mulai || $p->no_urut_selesai) {
                        $m = $p->no_urut_mulai ?? 1;
                        $s = $p->no_urut_selesai ?? 999;
                        foreach ($rombelSiswas as $idx => $sw) {
                            $no = $idx + 1;
                            if ($no >= $m && $no <= $s) {
                                $allowedSiswaIds->push($sw->id);
                            }
                        }
                    } else {
                        $hasUnlimited = true;
                    }
                }

                foreach ($rombelSiswas as $idx => $s) {
                    $noUrut = $idx + 1;
                    if (!$hasUnlimited && $pengujisForThisRombel->isNotEmpty()) {
                        if (!$allowedSiswaIds->contains($s->id)) continue;
                    }
                    $siswaIds->push($s->id);
                }
            }
            $siswaIds = $siswaIds->unique();

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

    public function show(Request $request, UkkUjian $ujian)
    {
        $this->authorizeAsPenguji($ujian);

        $pengujiRecords = UkkPenguji::where('ukk_ujian_id', $ujian->id)
            ->where('user_id', Auth::id())
            ->get();

        $perans = $pengujiRecords->pluck('peran')->unique();

        $ujian->load([
            'tahunPelajaran',
            'rombels.kelas',
            'rombels.siswa',
            'instrumens.soalPengetahuan',
            'instrumens.kategoriKeterampilan.indikator',
        ]);

        $hasGlobalAssignment = $pengujiRecords->contains(fn ($p) => is_null($p->rombel_id));
        $assignedRombelIds = $pengujiRecords->pluck('rombel_id')->filter()->unique();

        $availableRombels = ($hasGlobalAssignment || $assignedRombelIds->isEmpty())
            ? $ujian->rombels
            : $ujian->rombels->whereIn('id', $assignedRombelIds);

        // Filter by selected rombel if requested
        $selectedRombelId = $request->query('rombel');
        if ($selectedRombelId && $availableRombels->contains('id', (int) $selectedRombelId)) {
            $activeRombels = $availableRombels->where('id', (int) $selectedRombelId);
            $matchedP = $pengujiRecords->first(fn ($p) => $p->rombel_id == $selectedRombelId);
            $peranPenguji = $matchedP?->peran ?? 'semua';
        } else {
            $activeRombels = $availableRombels;
            $selectedRombelId = null;
            $specificRoles = $pengujiRecords->pluck('peran')->unique();
            if ($specificRoles->count() === 1) {
                $peranPenguji = $specificRoles->first();
            } else {
                $peranPenguji = $perans->contains('semua') ? 'semua' : ($perans->first() ?? 'semua');
            }
        }

        $siswas = collect();
        foreach ($activeRombels as $rombel) {
            $rombelSiswas = $rombel->siswa->sortBy('nama_lengkap')->values();
            $pengujisForThisRombel = $pengujiRecords->filter(fn ($p) => $p->rombel_id === $rombel->id || is_null($p->rombel_id));

            $hasUnlimited = false;
            $allowedSiswaIds = collect();

            foreach ($pengujisForThisRombel as $p) {
                if (is_array($p->siswa_ids) && !empty($p->siswa_ids)) {
                    $allowedSiswaIds = $allowedSiswaIds->merge($p->siswa_ids);
                } elseif ($p->no_urut_mulai || $p->no_urut_selesai) {
                    $m = $p->no_urut_mulai ?? 1;
                    $s = $p->no_urut_selesai ?? 999;
                    foreach ($rombelSiswas as $idx => $sw) {
                        $no = $idx + 1;
                        if ($no >= $m && $no <= $s) {
                            $allowedSiswaIds->push($sw->id);
                        }
                    }
                } else {
                    $hasUnlimited = true;
                }
            }

            foreach ($rombelSiswas as $idx => $s) {
                $noUrut = $idx + 1;
                $s->nomor_urut   = $noUrut;
                $s->rombel_nama  = $rombel->kelas?->nama_kelas;

                if (!$hasUnlimited && $pengujisForThisRombel->isNotEmpty()) {
                    if (!$allowedSiswaIds->contains($s->id)) continue;
                }

                $siswas->push($s);
            }
        }
        $siswas = $siswas->unique('id')->values();

        $activePenguji = $pengujiRecords->first(fn ($p) => $selectedRombelId ? $p->rombel_id == $selectedRombelId : true);
        $rentangLabel = null;
        if ($activePenguji) {
            if (is_array($activePenguji->siswa_ids) && !empty($activePenguji->siswa_ids)) {
                $rentangLabel = count($activePenguji->siswa_ids) . ' Siswa Ditugaskan';
            } elseif ($activePenguji->no_urut_mulai || $activePenguji->no_urut_selesai) {
                $rentangLabel = 'No. Urut ' . ($activePenguji->no_urut_mulai ?? 1) . ' s.d. ' . ($activePenguji->no_urut_selesai ?? 'Akhir');
            }
        }

        // Tentukan jadwal sesi aktif atau jadwal rombel
        $activeRombelFirst = $activeRombels->first();
        $rombelPivot = DB::table('ukk_rombel_mappings')
            ->where('ukk_ujian_id', $ujian->id)
            ->where('rombel_id', $activeRombelFirst?->id)
            ->first();

        $tgl = $activePenguji?->tanggal_pelaksanaan ?? $rombelPivot?->tanggal_pelaksanaan;
        $jamMulai = $activePenguji?->jam_mulai ? substr($activePenguji->jam_mulai, 0, 5) : ($rombelPivot?->jam_mulai ? substr($rombelPivot->jam_mulai, 0, 5) : null);
        $jamSelesai = $activePenguji?->jam_selesai ? substr($activePenguji->jam_selesai, 0, 5) : ($rombelPivot?->jam_selesai ? substr($rombelPivot->jam_selesai, 0, 5) : null);
        $ruangan = $activePenguji?->ruangan ?? $rombelPivot?->ruangan;

        $jadwalSesiLabel = null;
        if ($tgl) {
            $tglStr = \Carbon\Carbon::parse($tgl)->translatedFormat('d M Y');
            $jamStr = $jamMulai ? (' · ⏰ ' . $jamMulai . ($jamSelesai ? ' - ' . $jamSelesai : '') . ' WIB') : '';
            $ruangStr = $ruangan ? (' · 📍 ' . $ruangan) : '';
            $jadwalSesiLabel = '📅 ' . $tglStr . $jamStr . $ruangStr;
        }

        $isExamBilingual = (bool) $ujian->is_bilingual;

        // Filter soal yang menjadi kewenangan penguji ini
        $examinerSoals = $ujian->instrumens->flatMap(function ($i) use ($isExamBilingual, $peranPenguji) {
            return $i->soalPengetahuan->filter(function ($soal) use ($isExamBilingual, $peranPenguji) {
                $isBilingual = ($soal->jenis_soal === 'bilingual')
                    || ($isExamBilingual && (empty($soal->jenis_soal) || $soal->jenis_soal === 'bilingual'));

                if ($peranPenguji === 'keterampilan') {
                    return false;
                }
                if ($peranPenguji === 'pengetahuan') {
                    return $isExamBilingual ? $isBilingual : true;
                }
                // peran 'semua': Pada ujian bilingual, soal bilingual HANYA untuk penguji bilingual (pengetahuan)!
                if ($isExamBilingual) {
                    return !$isBilingual;
                }
                return true;
            });
        });

        $examinerSoalIds = $examinerSoals->pluck('id');
        $totalSoal       = $examinerSoalIds->count();

        $allIndikatorIds = $ujian->instrumens->flatMap(fn ($i) =>
            $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id'))
        );
        $totalIndikator = ($peranPenguji === 'pengetahuan') ? 0 : $allIndikatorIds->count();

        $siswaIdList = $siswas->pluck('id');

        $gradedP = $examinerSoalIds->isNotEmpty()
            ? UkkNilaiPengetahuan::whereIn('soal_id', $examinerSoalIds)
                ->whereIn('master_siswa_id', $siswaIdList)
                ->selectRaw('master_siswa_id, count(*) as cnt')
                ->groupBy('master_siswa_id')
                ->pluck('cnt', 'master_siswa_id')
            : collect();

        $gradedK = $allIndikatorIds->isNotEmpty()
            ? UkkNilaiKeterampilan::whereIn('indikator_id', $allIndikatorIds)
                ->whereIn('master_siswa_id', $siswaIdList)
                ->selectRaw('master_siswa_id, count(*) as cnt')
                ->groupBy('master_siswa_id')
                ->pluck('cnt', 'master_siswa_id')
            : collect();

        $allSoalIds = $ujian->instrumens->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));
        $pengujiPengetahuanMap = UkkNilaiPengetahuan::with('penguji.masterGuru')
            ->whereIn('soal_id', $allSoalIds)
            ->whereIn('master_siswa_id', $siswaIdList)
            ->whereNotNull('penguji_id')
            ->get()
            ->groupBy('master_siswa_id')
            ->map(function ($items) {
                $p = $items->first()?->penguji;
                return $p ? ($p->masterGuru?->nama_lengkap ?? $p->name) : null;
            });

        return view('pages.guru-kelas.ukk.show', compact(
            'ujian', 'siswas', 'totalSoal', 'totalIndikator', 'gradedP', 'gradedK',
            'peranPenguji', 'pengujiPengetahuanMap', 'availableRombels', 'selectedRombelId', 'rentangLabel', 'jadwalSesiLabel'
        ));
    }

    public function penilaian(UkkUjian $ujian, MasterSiswa $siswa)
    {
        $this->authorizeAsPenguji($ujian);

        $pengujiRecords = UkkPenguji::where('ukk_ujian_id', $ujian->id)
            ->where('user_id', Auth::id())
            ->get();

        // Cari rombel siswa untuk ujian ini agar peran penguji tepat sesuai rombel
        $ujianRombelIds = $ujian->rombels()->pluck('rombels.id')->all();
        $siswaRombelId  = $siswa->rombels()->whereIn('rombels.id', $ujianRombelIds)->value('rombels.id');

        $activePenguji = $pengujiRecords->first(fn ($p) => $siswaRombelId && (int)$p->rombel_id === (int)$siswaRombelId)
            ?? $pengujiRecords->first(fn ($p) => !is_null($p->rombel_id))
            ?? $pengujiRecords->first();

        $peranPenguji = $activePenguji?->peran ?? 'semua';

        $ujian->load([
            'instrumens.soalPengetahuan',
            'instrumens.kategoriKeterampilan.indikator',
        ]);

        $isExamBilingual = (bool) $ujian->is_bilingual;

        // Filter butir soal yang menjadi kewenangan penguji ini
        foreach ($ujian->instrumens as $ins) {
            $ins->filteredSoalPengetahuan = $ins->soalPengetahuan->filter(function ($soal) use ($isExamBilingual, $peranPenguji) {
                $isBilingual = ($soal->jenis_soal === 'bilingual')
                    || ($isExamBilingual && (empty($soal->jenis_soal) || $soal->jenis_soal === 'bilingual'));

                if ($peranPenguji === 'keterampilan') {
                    return false;
                }

                if ($peranPenguji === 'pengetahuan') {
                    // Penguji bilingual / pengetahuan hanya menguji soal bilingual pada ujian bilingual
                    return $isExamBilingual ? $isBilingual : true;
                }

                // Penguji peran 'semua': Pada ujian bilingual, soal bilingual HANYA untuk penguji bilingual!
                // Penguji 'semua' sama sekali TIDAK melihat soal bilingual.
                if ($isExamBilingual) {
                    return !$isBilingual;
                }

                return true;
            })->values();
        }

        $hasPengetahuanForExaminer = $ujian->instrumens->contains(fn ($i) => $i->filteredSoalPengetahuan->isNotEmpty());
        $hasKeterampilanForExaminer = $peranPenguji !== 'pengetahuan' && $ujian->instrumens->contains(fn ($i) => $i->kategoriKeterampilan->isNotEmpty());

        $allSoalIds      = $ujian->instrumens->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));
        $allIndikatorIds = $ujian->instrumens->flatMap(fn ($i) =>
            $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id'))
        );

        $recordsP = UkkNilaiPengetahuan::where('master_siswa_id', $siswa->id)
            ->whereIn('soal_id', $allSoalIds)
            ->get()
            ->keyBy('soal_id');

        $nilaiP   = $recordsP->map(fn ($r) => $r->nilai);
        $jawabanP = $recordsP->map(fn ($r) => $r->jawaban_siswa);
        $catatanP = $recordsP->map(fn ($r) => $r->catatan_penguji);

        $nilaiK = UkkNilaiKeterampilan::where('master_siswa_id', $siswa->id)
            ->whereIn('indikator_id', $allIndikatorIds)
            ->pluck('nilai', 'indikator_id');

        $nilaiSikap = UkkNilaiSikap::where('ukk_ujian_id', $ujian->id)
            ->where('master_siswa_id', $siswa->id)
            ->first();

        return view('pages.guru-kelas.ukk.penilaian', compact(
            'ujian', 'siswa', 'nilaiP', 'jawabanP', 'catatanP', 'nilaiK', 'peranPenguji', 'nilaiSikap',
            'hasPengetahuanForExaminer', 'hasKeterampilanForExaminer'
        ));
    }

    public function simpan(Request $request, UkkUjian $ujian, MasterSiswa $siswa)
    {
        $this->authorizeAsPenguji($ujian);

        $pengujiRecords = UkkPenguji::where('ukk_ujian_id', $ujian->id)
            ->where('user_id', Auth::id())
            ->get();

        // Cari rombel siswa untuk ujian ini agar peran penguji tepat sesuai rombel
        $ujianRombelIds = $ujian->rombels()->pluck('rombels.id')->all();
        $siswaRombelId  = $siswa->rombels()->whereIn('rombels.id', $ujianRombelIds)->value('rombels.id');

        $activePenguji = $pengujiRecords->first(fn ($p) => $siswaRombelId && (int)$p->rombel_id === (int)$siswaRombelId)
            ?? $pengujiRecords->first(fn ($p) => !is_null($p->rombel_id))
            ?? $pengujiRecords->first();

        $peranPenguji = $activePenguji?->peran ?? 'semua';

        $request->validate([
            'pengetahuan'               => 'nullable|array',
            'pengetahuan.*'             => 'nullable|integer|in:0,1',
            'catatan_pengetahuan'       => 'nullable|array',
            'catatan_pengetahuan.*'     => 'nullable|string',
            'keterampilan'              => 'nullable|array',
            'keterampilan.*'            => 'integer|in:0,1,2,3',
            'sikap'                     => 'nullable|array',
            'sikap.kerapian'            => 'nullable|string|in:kurang,cukup,baik,sangat_baik',
            'sikap.ketelitian'          => 'nullable|string|in:kurang,cukup,baik,sangat_baik',
            'sikap.kedisiplinan'        => 'nullable|string|in:kurang,cukup,baik,sangat_baik',
            'sikap.tanggung_jawab'      => 'nullable|string|in:kurang,cukup,baik,sangat_baik',
            'sikap.inisiatif'           => 'nullable|string|in:kurang,cukup,baik,sangat_baik',
            'sikap.kesimpulan'          => 'nullable|string|max:50',
            'sikap.catatan'             => 'nullable|string',
        ]);

        // Verify soal & indikator belong to this ujian
        $instrumenIds = $ujian->instrumens()->pluck('id');
        $allSoals     = UkkInstrumenSoal::whereIn('instrumen_id', $instrumenIds)->get()->keyBy('id');
        $validSoalIds = $allSoals->pluck('id');
        $validIndIds  = UkkInstrumenIndikator::whereIn('kategori_id',
            DB::table('ukk_instrumen_kategoris')->whereIn('instrumen_id', $instrumenIds)->pluck('id')
        )->pluck('id');

        DB::transaction(function () use ($request, $ujian, $siswa, $allSoals, $validSoalIds, $validIndIds, $peranPenguji) {
            // Simpan penilaian pengetahuan jika peran penguji adalah 'semua' atau 'pengetahuan'
            if (in_array($peranPenguji, ['semua', 'pengetahuan'])) {
                foreach ($request->input('pengetahuan', []) as $soalId => $nilai) {
                    if (!$validSoalIds->contains((int) $soalId)) continue;

                    $soal = $allSoals->get((int) $soalId);
                    $isBilingual = ($soal && $soal->jenis_soal === 'bilingual')
                        || ($ujian->is_bilingual && (!$soal || !in_array($soal->jenis_soal, ['observasi', 'esai'])));

                    // ATURAN BILINGUAL:
                    // Butir soal wawancara bilingual HANYA dinilai oleh penguji dengan peran 'pengetahuan' (Guru B. Inggris).
                    // Penguji dengan peran 'semua' (Guru Kejuruan) TIDAK menilai / menimpa soal bilingual.
                    if ($isBilingual && $peranPenguji === 'semua') {
                        continue;
                    }

                    $catatan = $request->input("catatan_pengetahuan.{$soalId}");
                    $record  = UkkNilaiPengetahuan::firstOrNew([
                        'master_siswa_id' => $siswa->id,
                        'soal_id'         => (int) $soalId,
                    ]);
                    $record->penguji_id = Auth::id();
                    $record->nilai      = ($nilai !== null && $nilai !== '') ? (int) $nilai : null;
                    if ($catatan !== null) {
                        $record->catatan_penguji = $catatan;
                    }
                    $record->save();
                }
            }

            // Simpan penilaian keterampilan jika peran penguji adalah 'semua' atau 'keterampilan'
            if (in_array($peranPenguji, ['semua', 'keterampilan'])) {
                foreach ($request->input('keterampilan', []) as $indId => $nilai) {
                    if (!$validIndIds->contains((int) $indId)) continue;
                    UkkNilaiKeterampilan::updateOrCreate(
                        ['master_siswa_id' => $siswa->id, 'indikator_id' => (int) $indId],
                        ['nilai' => (int) $nilai, 'penguji_id' => Auth::id()]
                    );
                }
            }

            // Simpan penilaian sikap kerja (dilakukan oleh penguji keterampilan atau penguji semua)
            if (in_array($peranPenguji, ['semua', 'keterampilan']) && $request->has('sikap')) {
                $sikapInput = $request->input('sikap', []);
                if (is_array($sikapInput)) {
                    $recordSikap = UkkNilaiSikap::firstOrNew([
                        'ukk_ujian_id'    => $ujian->id,
                        'master_siswa_id' => $siswa->id,
                    ]);
                    $recordSikap->penguji_id     = Auth::id();
                    $recordSikap->kerapian       = $sikapInput['kerapian'] ?? null;
                    $recordSikap->ketelitian     = $sikapInput['ketelitian'] ?? null;
                    $recordSikap->kedisiplinan   = $sikapInput['kedisiplinan'] ?? null;
                    $recordSikap->tanggung_jawab = $sikapInput['tanggung_jawab'] ?? null;
                    $recordSikap->inisiatif      = $sikapInput['inisiatif'] ?? null;
                    $recordSikap->kesimpulan     = $sikapInput['kesimpulan'] ?? null;
                    $recordSikap->catatan        = $sikapInput['catatan'] ?? null;
                    $recordSikap->save();
                }
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

        $nilaiSikap = UkkNilaiSikap::where('ukk_ujian_id', $ujian->id)
            ->where('master_siswa_id', $siswa->id)
            ->first();

        $pdf = Pdf::loadView('pdf.penilaian-ukk', compact(
            'ujian', 'siswa', 'nilaiP', 'nilaiK', 'nilaiSikap',
            'settings', 'logoBase64',
            'user', 'ttdBase64', 'digDoc', 'qrBase64',
            'instrumenScores', 'nilaiAkhirFinal'
        ))->setPaper('a4', 'portrait');

        $filename = 'Penilaian_UKK_' . str_replace(' ', '_', $siswa->nama_lengkap) . '.pdf';

        return $pdf->stream($filename);
    }
}
