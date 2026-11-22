<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\UkkInstrumenIndikator;
use App\Models\UkkInstrumenSoal;
use App\Models\UkkNilaiBilingual;
use App\Models\UkkNilaiKeterampilan;
use App\Models\UkkNilaiPengetahuan;
use App\Models\UkkNilaiSikap;
use App\Models\UkkPenguji;
use App\Models\UkkUjian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UkkMobileController extends Controller
{
    /**
     * Check if user is authorized as examiner or has management privileges.
     */
    private function authorizePenguji(UkkUjian $ujian): void
    {
        $userId = Auth::id();
        $user = Auth::user();

        $isExplicitPenguji = UkkPenguji::where('ukk_ujian_id', $ujian->id)
            ->where('user_id', $userId)
            ->exists()
            || DB::table('ukk_penguji')->where('ukk_ujian_id', $ujian->id)->where('user_id', $userId)->exists();

        $isPrivileged = $user && $user->hasAnyRole(['Super Admin', 'adminup', 'Kaprodi', 'Kaprodi PPLGIM ANIMASI', 'Kaprodi TJKT', 'Kurikulum']);

        abort_unless(
            $isExplicitPenguji || $isPrivileged,
            403,
            'Anda tidak terdaftar sebagai penguji untuk ujian ini.'
        );
    }

    /**
     * Get list of UKK exams accessible to the logged-in user.
     */
    public function ujians()
    {
        $userId = Auth::id();
        $user = Auth::user();

        $query = UkkUjian::with([
            'tahunPelajaran',
            'rombels.siswa',
            'instrumens.soalPengetahuan',
            'ukkPengujis',
        ]);

        $hasExplicit = UkkPenguji::where('user_id', $userId)->exists();
        if ($hasExplicit) {
            $query->whereHas('ukkPengujis', fn ($q) => $q->where('user_id', $userId));
        } else {
            // For admin / kaprodi testing if not explicitly assigned
            if ($user && $user->hasAnyRole(['Super Admin', 'adminup', 'Kaprodi', 'Kaprodi PPLGIM ANIMASI', 'Kaprodi TJKT', 'Kurikulum'])) {
                $query->latest();
            } else {
                return response()->json(['success' => true, 'data' => []]);
            }
        }

        $ujians = $query->latest()->get();

        $result = $ujians->map(function ($ujian) use ($userId) {
            $pengujiRecords = $ujian->ukkPengujis->where('user_id', $userId);
            $hasGlobalAssignment = $pengujiRecords->isEmpty() || $pengujiRecords->contains(fn ($p) => is_null($p->rombel_id));
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
                    if (!$hasUnlimited && $pengujisForThisRombel->isNotEmpty()) {
                        if (!$allowedSiswaIds->contains($s->id)) continue;
                    }
                    $siswaIds->push($s->id);
                }
            }
            $siswaIds = $siswaIds->unique();
            $totalSiswa = $siswaIds->count();

            $soalIds = $ujian->instrumens->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));
            $sudahDinilai = $soalIds->isEmpty() ? 0
                : UkkNilaiPengetahuan::whereIn('soal_id', $soalIds)
                    ->whereIn('master_siswa_id', $siswaIds)
                    ->distinct('master_siswa_id')
                    ->count('master_siswa_id');

            $firstPenguji = $pengujiRecords->first();
            $peran = $firstPenguji?->peran ?? 'semua';

            return [
                'id' => $ujian->id,
                'nama_ujian' => $ujian->nama_ujian,
                'nomor_paket' => $ujian->nomor_paket,
                'judul_paket' => $ujian->judul_paket,
                'tahun_pelajaran' => $ujian->tahunPelajaran?->tahun,
                'semester' => $ujian->tahunPelajaran?->semester,
                'is_bilingual' => (bool) $ujian->is_bilingual,
                'status' => $ujian->status ?? 'Aktif',
                'peran_penguji' => $peran,
                'total_siswa' => $totalSiswa,
                'sudah_dinilai' => $sudahDinilai,
                'persentase_selesai' => $totalSiswa > 0 ? round(($sudahDinilai / $totalSiswa) * 100, 1) : 0,
                'rombel_names' => $filteredRombels->pluck('kelas.nama_kelas')->filter()->values(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Get list of students for an exam, optionally filtered by rombel.
     */
    public function siswas(UkkUjian $ujian, Request $request)
    {
        $this->authorizePenguji($ujian);

        $userId = Auth::id();
        $pengujiRecords = UkkPenguji::where('ukk_ujian_id', $ujian->id)
            ->where('user_id', $userId)
            ->get();

        $ujian->load([
            'tahunPelajaran',
            'rombels.kelas',
            'rombels.siswa',
            'instrumens.soalPengetahuan',
            'instrumens.kategoriKeterampilan.indikator',
        ]);

        $hasGlobalAssignment = $pengujiRecords->isEmpty() || $pengujiRecords->contains(fn ($p) => is_null($p->rombel_id));
        $assignedRombelIds = $pengujiRecords->pluck('rombel_id')->filter()->unique();

        $availableRombels = ($hasGlobalAssignment || $assignedRombelIds->isEmpty())
            ? $ujian->rombels
            : $ujian->rombels->whereIn('id', $assignedRombelIds);

        $selectedRombelId = $request->query('rombel_id');
        if ($selectedRombelId && $availableRombels->contains('id', (int) $selectedRombelId)) {
            $activeRombels = $availableRombels->where('id', (int) $selectedRombelId);
            $matchedP = $pengujiRecords->first(fn ($p) => $p->rombel_id == $selectedRombelId);
            $peranPenguji = $matchedP?->peran ?? 'semua';
        } else {
            $activeRombels = $availableRombels;
            $selectedRombelId = null;
            $specificRoles = $pengujiRecords->pluck('peran')->unique();
            $peranPenguji = $specificRoles->count() === 1 ? $specificRoles->first() : ($pengujiRecords->first()?->peran ?? 'semua');
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
                $s->nomor_urut = $noUrut;
                $s->rombel_id = $rombel->id;
                $s->rombel_nama = $rombel->kelas?->nama_kelas;

                if (!$hasUnlimited && $pengujisForThisRombel->isNotEmpty()) {
                    if (!$allowedSiswaIds->contains($s->id)) continue;
                }
                $siswas->push($s);
            }
        }
        $siswas = $siswas->unique('id')->values();

        $isExamBilingual = (bool) $ujian->is_bilingual;

        // Filter soal yang menjadi kewenangan penguji ini
        $examinerSoals = $ujian->instrumens->flatMap(function ($i) use ($isExamBilingual, $peranPenguji) {
            return $i->soalPengetahuan->filter(function ($soal) use ($isExamBilingual, $peranPenguji) {
                $isBilingual = ($soal->jenis_soal === 'bilingual')
                    || ($isExamBilingual && (empty($soal->jenis_soal) || $soal->jenis_soal === 'bilingual'));

                if ($peranPenguji === 'keterampilan') return false;
                if ($peranPenguji === 'pengetahuan') return $isExamBilingual ? $isBilingual : true;
                if ($isExamBilingual) return !$isBilingual;
                return true;
            });
        });

        $examinerSoalIds = $examinerSoals->pluck('id');
        $totalSoalExaminer = $examinerSoalIds->count();

        $allIndikatorIds = $ujian->instrumens->flatMap(fn ($i) =>
            $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id'))
        );
        $totalIndikatorExaminer = ($peranPenguji === 'pengetahuan') ? 0 : $allIndikatorIds->count();

        $siswaIdList = $siswas->pluck('id');

        $gradedP = $examinerSoalIds->isNotEmpty()
            ? UkkNilaiPengetahuan::whereIn('soal_id', $examinerSoalIds)
                ->whereIn('master_siswa_id', $siswaIdList)
                ->whereNotNull('nilai')
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

        $sikapMap = UkkNilaiSikap::where('ukk_ujian_id', $ujian->id)
            ->whereIn('master_siswa_id', $siswaIdList)
            ->get()
            ->keyBy('master_siswa_id');

        $bilingualMap = UkkNilaiBilingual::where('ukk_ujian_id', $ujian->id)
            ->whereIn('master_siswa_id', $siswaIdList)
            ->get()
            ->keyBy('master_siswa_id');

        $studentList = $siswas->map(function ($s) use (
            $totalSoalExaminer,
            $totalIndikatorExaminer,
            $gradedP,
            $gradedK,
            $sikapMap,
            $bilingualMap,
            $peranPenguji,
            $isExamBilingual
        ) {
            $cntP = $gradedP[$s->id] ?? 0;
            $cntK = $gradedK[$s->id] ?? 0;
            $hasSikap = isset($sikapMap[$s->id]);
            $hasBilingual = isset($bilingualMap[$s->id]);

            $isDoneP = $totalSoalExaminer === 0 || $cntP >= $totalSoalExaminer;
            $isDoneK = $totalIndikatorExaminer === 0 || $cntK >= $totalIndikatorExaminer;
            $isDoneSikap = ($peranPenguji === 'pengetahuan') ? true : $hasSikap;
            $isDoneBilingual = (!$isExamBilingual || $peranPenguji === 'keterampilan') ? true : $hasBilingual;

            $isCompleted = $isDoneP && $isDoneK && $isDoneSikap && $isDoneBilingual;
            $hasStarted = ($cntP > 0) || ($cntK > 0) || $hasSikap || $hasBilingual;

            $status = $isCompleted ? 'selesai' : ($hasStarted ? 'sebagian' : 'belum');

            return [
                'id' => $s->id,
                'nama_lengkap' => $s->nama_lengkap,
                'nis' => $s->nis,
                'nisn' => $s->nisn,
                'nomor_urut' => $s->nomor_urut,
                'rombel_id' => $s->rombel_id,
                'rombel_nama' => $s->rombel_nama,
                'foto' => $s->foto ? asset('storage/' . $s->foto) : null,
                'status' => $status,
                'is_completed' => $isCompleted,
                'graded_pengetahuan' => $cntP,
                'total_pengetahuan' => $totalSoalExaminer,
                'graded_keterampilan' => $cntK,
                'total_keterampilan' => $totalIndikatorExaminer,
                'has_sikap' => $hasSikap,
                'has_bilingual' => $hasBilingual,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'ujian' => [
                    'id' => $ujian->id,
                    'nama_ujian' => $ujian->nama_ujian,
                    'is_bilingual' => $isExamBilingual,
                    'peran_penguji' => $peranPenguji,
                ],
                'rombels' => $availableRombels->map(fn ($r) => [
                    'id' => $r->id,
                    'nama_kelas' => $r->kelas?->nama_kelas,
                ])->values(),
                'siswas' => $studentList,
            ],
        ]);
    }

    /**
     * Get detail assessment data for a specific student.
     */
    public function showSiswa(UkkUjian $ujian, MasterSiswa $siswa)
    {
        $this->authorizePenguji($ujian);

        $userId = Auth::id();
        $pengujiRecords = UkkPenguji::where('ukk_ujian_id', $ujian->id)
            ->where('user_id', $userId)
            ->get();

        $ujianRombelIds = $ujian->rombels()->pluck('rombels.id')->all();
        $siswaRombelId = $siswa->rombels()->whereIn('rombels.id', $ujianRombelIds)->value('rombels.id');

        $activePenguji = $pengujiRecords->first(fn ($p) => $siswaRombelId && (int)$p->rombel_id === (int)$siswaRombelId)
            ?? $pengujiRecords->first(fn ($p) => !is_null($p->rombel_id))
            ?? $pengujiRecords->first();

        $peranPenguji = $activePenguji?->peran ?? 'semua';

        $ujian->load([
            'instrumens.soalPengetahuan',
            'instrumens.kategoriKeterampilan.indikator',
        ]);

        $isExamBilingual = (bool) $ujian->is_bilingual;

        $canGradePengetahuan = in_array($peranPenguji, ['semua', 'pengetahuan']);
        $canGradeKeterampilan = in_array($peranPenguji, ['semua', 'keterampilan']);
        $canGradeSikap = in_array($peranPenguji, ['semua', 'keterampilan']);
        $canGradeBilingual = $isExamBilingual && in_array($peranPenguji, ['pengetahuan', 'semua']);

        $allSoalIds = $ujian->instrumens->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));
        $allIndikatorIds = $ujian->instrumens->flatMap(fn ($i) =>
            $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id'))
        );

        $recordsP = UkkNilaiPengetahuan::where('master_siswa_id', $siswa->id)
            ->whereIn('soal_id', $allSoalIds)
            ->get()
            ->keyBy('soal_id');

        $nilaiK = UkkNilaiKeterampilan::where('master_siswa_id', $siswa->id)
            ->whereIn('indikator_id', $allIndikatorIds)
            ->pluck('nilai', 'indikator_id');

        $nilaiSikap = UkkNilaiSikap::where('ukk_ujian_id', $ujian->id)
            ->where('master_siswa_id', $siswa->id)
            ->first();

        $nilaiBilingual = UkkNilaiBilingual::where('ukk_ujian_id', $ujian->id)
            ->where('master_siswa_id', $siswa->id)
            ->first();

        // Instruments formatting
        $instrumensData = $ujian->instrumens->map(function ($ins) use (
            $isExamBilingual,
            $peranPenguji,
            $canGradePengetahuan,
            $canGradeKeterampilan,
            $recordsP,
            $nilaiK
        ) {
            $filteredSoals = collect();
            if ($canGradePengetahuan) {
                $filteredSoals = $ins->soalPengetahuan->filter(function ($soal) use ($isExamBilingual, $peranPenguji) {
                    $isBilingual = ($soal->jenis_soal === 'bilingual')
                        || ($isExamBilingual && (empty($soal->jenis_soal) || $soal->jenis_soal === 'bilingual'));

                    if ($peranPenguji === 'keterampilan') return false;
                    if ($peranPenguji === 'pengetahuan') return $isExamBilingual ? $isBilingual : true;
                    if ($isExamBilingual) return !$isBilingual;
                    return true;
                })->values();
            }

            $categories = collect();
            if ($canGradeKeterampilan) {
                $categories = $ins->kategoriKeterampilan->map(function ($kat) use ($nilaiK) {
                    return [
                        'id' => $kat->id,
                        'nama_kategori' => $kat->nama_kategori,
                        'indikators' => $kat->indikator->map(function ($ind) use ($nilaiK) {
                            return [
                                'id' => $ind->id,
                                'nomor_indikator' => $ind->nomor_indikator,
                                'nama_indikator' => $ind->nama_indikator,
                                'bobot' => $ind->bobot ?? 1,
                                'nilai' => $nilaiK[$ind->id] ?? null,
                            ];
                        })->values(),
                    ];
                })->values();
            }

            return [
                'id' => $ins->id,
                'nama_instrumen' => $ins->nama_instrumen ?? 'Instrumen UKK',
                'bobot_pengetahuan' => $ins->bobot_pengetahuan,
                'bobot_keterampilan' => $ins->bobot_keterampilan,
                'soal_pengetahuan' => $filteredSoals->map(function ($soal) use ($recordsP) {
                    $pRec = $recordsP->get($soal->id);
                    return [
                        'id' => $soal->id,
                        'nomor_soal' => $soal->nomor_soal,
                        'pertanyaan' => $soal->pertanyaan,
                        'kunci_jawaban' => $soal->kunci_jawaban,
                        'jenis_soal' => $soal->jenis_soal,
                        'nilai' => $pRec ? $pRec->nilai : null,
                        'catatan' => $pRec ? $pRec->catatan_penguji : '',
                    ];
                })->values(),
                'kategori_keterampilan' => $categories,
            ];
        });

        // Compute current live scores
        $totalSoalBenar = 0;
        $totalSoalCount = 0;
        foreach ($instrumensData as $ins) {
            foreach ($ins['soal_pengetahuan'] as $s) {
                $totalSoalCount++;
                if (($s['nilai'] ?? null) === 1) {
                    $totalSoalBenar++;
                }
            }
        }
        $skorPKonten = $totalSoalCount > 0 ? round(($totalSoalBenar / $totalSoalCount) * 100, 1) : 0;
        $skorP = $skorPKonten;
        $skorBilingualKecakapan = null;

        if ($isExamBilingual && $nilaiBilingual && $nilaiBilingual->getRataRataScore() !== null) {
            $skorBilingualKecakapan = round(($nilaiBilingual->getRataRataScore() / 4) * 100, 1);
            $skorP = round(($skorPKonten * 0.5) + ($skorBilingualKecakapan * 0.5), 1);
        }

        $totalIndVal = 0;
        $totalIndMax = 0;
        foreach ($instrumensData as $ins) {
            foreach ($ins['kategori_keterampilan'] as $kat) {
                foreach ($kat['indikators'] as $ind) {
                    $totalIndMax += 3;
                    $totalIndVal += ($ind['nilai'] ?? 0);
                }
            }
        }
        $skorK = $totalIndMax > 0 ? round(($totalIndVal / $totalIndMax) * 100, 1) : 0;

        $siswaRombelObj = $siswa->rombels()->whereIn('rombels.id', $ujianRombelIds)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'siswa' => [
                    'id' => $siswa->id,
                    'nama_lengkap' => $siswa->nama_lengkap,
                    'nis' => $siswa->nis,
                    'nisn' => $siswa->nisn,
                    'rombel_nama' => $siswaRombelObj?->kelas?->nama_kelas ?? '-',
                    'foto' => $siswa->foto ? asset('storage/' . $siswa->foto) : null,
                ],
                'ujian' => [
                    'id' => $ujian->id,
                    'nama_ujian' => $ujian->nama_ujian,
                    'nomor_paket' => $ujian->nomor_paket,
                    'judul_paket' => $ujian->judul_paket,
                    'is_bilingual' => $isExamBilingual,
                ],
                'permissions' => [
                    'peran_penguji' => $peranPenguji,
                    'can_grade_pengetahuan' => $canGradePengetahuan,
                    'can_grade_keterampilan' => $canGradeKeterampilan,
                    'can_grade_sikap' => $canGradeSikap,
                    'can_grade_bilingual' => $canGradeBilingual,
                ],
                'scores_summary' => [
                    'skor_pengetahuan' => $skorP,
                    'skor_pengetahuan_konten' => $skorPKonten,
                    'skor_bilingual_kecakapan' => $skorBilingualKecakapan,
                    'skor_keterampilan' => $skorK,
                ],
                'instrumens' => $instrumensData,
                'sikap' => $nilaiSikap ? [
                    'kerapian' => $nilaiSikap->kerapian,
                    'ketelitian' => $nilaiSikap->ketelitian,
                    'kedisiplinan' => $nilaiSikap->kedisiplinan,
                    'tanggung_jawab' => $nilaiSikap->tanggung_jawab,
                    'inisiatif' => $nilaiSikap->inisiatif,
                    'kesimpulan' => $nilaiSikap->kesimpulan,
                    'catatan' => $nilaiSikap->catatan,
                ] : null,
                'bilingual' => $nilaiBilingual ? [
                    'fluency' => $nilaiBilingual->fluency,
                    'pronunciation' => $nilaiBilingual->pronunciation,
                    'vocabulary' => $nilaiBilingual->vocabulary,
                    'comprehension' => $nilaiBilingual->comprehension,
                    'kesimpulan' => $nilaiBilingual->kesimpulan,
                    'catatan' => $nilaiBilingual->catatan,
                ] : null,
            ],
        ]);
    }

    /**
     * Store assessment for a student.
     */
    public function simpanPenilaian(Request $request, UkkUjian $ujian, MasterSiswa $siswa)
    {
        $this->authorizePenguji($ujian);

        $userId = Auth::id();
        $pengujiRecords = UkkPenguji::where('ukk_ujian_id', $ujian->id)
            ->where('user_id', $userId)
            ->get();

        $ujianRombelIds = $ujian->rombels()->pluck('rombels.id')->all();
        $siswaRombelId = $siswa->rombels()->whereIn('rombels.id', $ujianRombelIds)->value('rombels.id');

        $activePenguji = $pengujiRecords->first(fn ($p) => $siswaRombelId && (int)$p->rombel_id === (int)$siswaRombelId)
            ?? $pengujiRecords->first(fn ($p) => !is_null($p->rombel_id))
            ?? $pengujiRecords->first();

        $peranPenguji = $activePenguji?->peran ?? 'semua';

        $request->validate([
            'pengetahuan' => 'nullable|array',
            'catatan_pengetahuan' => 'nullable|array',
            'keterampilan' => 'nullable|array',
            'sikap' => 'nullable|array',
            'sikap.kerapian' => 'nullable|string|in:kurang,cukup,baik,sangat_baik',
            'sikap.ketelitian' => 'nullable|string|in:kurang,cukup,baik,sangat_baik',
            'sikap.kedisiplinan' => 'nullable|string|in:kurang,cukup,baik,sangat_baik',
            'sikap.tanggung_jawab' => 'nullable|string|in:kurang,cukup,baik,sangat_baik',
            'sikap.inisiatif' => 'nullable|string|in:kurang,cukup,baik,sangat_baik',
            'sikap.kesimpulan' => 'nullable|string|max:50',
            'sikap.catatan' => 'nullable|string',
            'bilingual' => 'nullable|array',
            'bilingual.fluency' => 'nullable|string|in:poor,fair,good,excellent',
            'bilingual.pronunciation' => 'nullable|string|in:poor,fair,good,excellent',
            'bilingual.vocabulary' => 'nullable|string|in:poor,fair,good,excellent',
            'bilingual.comprehension' => 'nullable|string|in:poor,fair,good,excellent',
            'bilingual.kesimpulan' => 'nullable|string|max:50',
            'bilingual.catatan' => 'nullable|string',
        ]);

        $instrumenIds = $ujian->instrumens()->pluck('id');
        $allSoals = UkkInstrumenSoal::whereIn('instrumen_id', $instrumenIds)->get()->keyBy('id');
        $validSoalIds = $allSoals->pluck('id');
        $validIndIds = UkkInstrumenIndikator::whereIn('kategori_id',
            DB::table('ukk_instrumen_kategoris')->whereIn('instrumen_id', $instrumenIds)->pluck('id')
        )->pluck('id');

        DB::transaction(function () use ($request, $ujian, $siswa, $allSoals, $validSoalIds, $validIndIds, $peranPenguji, $userId) {
            // Simpan penilaian pengetahuan jika peran 'semua' atau 'pengetahuan'
            if (in_array($peranPenguji, ['semua', 'pengetahuan']) && $request->has('pengetahuan')) {
                foreach ($request->input('pengetahuan', []) as $soalId => $nilai) {
                    if (!$validSoalIds->contains((int) $soalId)) continue;

                    $soal = $allSoals->get((int) $soalId);
                    $isBilingual = ($soal && $soal->jenis_soal === 'bilingual')
                        || ($ujian->is_bilingual && (!$soal || !in_array($soal->jenis_soal, ['observasi', 'esai'])));

                    if ($isBilingual && $peranPenguji === 'semua') {
                        continue;
                    }

                    $catatan = $request->input("catatan_pengetahuan.{$soalId}");
                    $record = UkkNilaiPengetahuan::firstOrNew([
                        'master_siswa_id' => $siswa->id,
                        'soal_id' => (int) $soalId,
                    ]);
                    $record->penguji_id = $userId;
                    $record->nilai = ($nilai !== null && $nilai !== '') ? (int) $nilai : null;
                    if ($catatan !== null) {
                        $record->catatan_penguji = $catatan;
                    }
                    $record->save();
                }
            }

            // Simpan penilaian keterampilan jika peran 'semua' atau 'keterampilan'
            if (in_array($peranPenguji, ['semua', 'keterampilan']) && $request->has('keterampilan')) {
                foreach ($request->input('keterampilan', []) as $indId => $nilai) {
                    if (!$validIndIds->contains((int) $indId)) continue;
                    UkkNilaiKeterampilan::updateOrCreate(
                        ['master_siswa_id' => $siswa->id, 'indikator_id' => (int) $indId],
                        ['nilai' => (int) $nilai, 'penguji_id' => $userId]
                    );
                }
            }

            // Simpan penilaian sikap kerja
            if (in_array($peranPenguji, ['semua', 'keterampilan']) && $request->has('sikap')) {
                $sikapInput = $request->input('sikap', []);
                if (is_array($sikapInput)) {
                    $recordSikap = UkkNilaiSikap::firstOrNew([
                        'ukk_ujian_id' => $ujian->id,
                        'master_siswa_id' => $siswa->id,
                    ]);
                    $recordSikap->penguji_id = $userId;
                    $recordSikap->kerapian = $sikapInput['kerapian'] ?? null;
                    $recordSikap->ketelitian = $sikapInput['ketelitian'] ?? null;
                    $recordSikap->kedisiplinan = $sikapInput['kedisiplinan'] ?? null;
                    $recordSikap->tanggung_jawab = $sikapInput['tanggung_jawab'] ?? null;
                    $recordSikap->inisiatif = $sikapInput['inisiatif'] ?? null;
                    $recordSikap->kesimpulan = $sikapInput['kesimpulan'] ?? null;
                    $recordSikap->catatan = $sikapInput['catatan'] ?? null;
                    $recordSikap->save();
                }
            }

            // Simpan penilaian performa wawancara bilingual
            if ($ujian->is_bilingual && in_array($peranPenguji, ['pengetahuan', 'semua']) && $request->has('bilingual')) {
                $bilingualInput = $request->input('bilingual', []);
                if (is_array($bilingualInput)) {
                    $recordBilingual = UkkNilaiBilingual::firstOrNew([
                        'ukk_ujian_id' => $ujian->id,
                        'master_siswa_id' => $siswa->id,
                    ]);
                    $recordBilingual->penguji_id = $userId;
                    $recordBilingual->fluency = $bilingualInput['fluency'] ?? null;
                    $recordBilingual->pronunciation = $bilingualInput['pronunciation'] ?? null;
                    $recordBilingual->vocabulary = $bilingualInput['vocabulary'] ?? null;
                    $recordBilingual->comprehension = $bilingualInput['comprehension'] ?? null;
                    $recordBilingual->kesimpulan = $bilingualInput['kesimpulan'] ?? null;
                    $recordBilingual->catatan = $bilingualInput['catatan'] ?? null;
                    $recordBilingual->save();
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Penilaian UKK berhasil disimpan ke sistem.',
        ]);
    }
}
