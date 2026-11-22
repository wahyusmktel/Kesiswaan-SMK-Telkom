<?php

namespace App\Http\Controllers\Kaprodi;

use App\Exports\MonitoringPengujiUkkExport;
use App\Http\Controllers\Controller;
use App\Models\TahunPelajaran;
use App\Models\UkkNilaiBilingual;
use App\Models\UkkNilaiKeterampilan;
use App\Models\UkkNilaiPengetahuan;
use App\Models\UkkNilaiSikap;
use App\Models\UkkPenguji;
use App\Models\UkkUjian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringPengujiUkkController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $ujians = UkkUjian::with('tahunPelajaran')
            ->when($tahunAktif, fn ($q) => $q->where('tahun_pelajaran_id', $tahunAktif->id))
            ->latest()
            ->get();

        if ($ujians->isEmpty()) {
            $ujians = UkkUjian::with('tahunPelajaran')->latest()->get();
        }

        $selectedUjianId  = $request->input('ujian_id', $ujians->first()?->id);
        $selectedRombelId = $request->input('rombel_id');
        $selectedStatus   = $request->input('status'); // Selesai, Sedang Menilai, Belum Mulai

        $ujian = $ujians->firstWhere('id', $selectedUjianId);
        $rombels = collect();
        $pengujisData = collect();
        $kpis = [
            'total_penguji' => 0,
            'selesai_count' => 0,
            'proses_count'  => 0,
            'belum_count'   => 0,
            'avg_progress'  => 0,
            'total_target_siswa'  => 0,
            'total_selesai_siswa' => 0,
        ];

        if ($ujian) {
            $data = $this->buildMonitoringData($ujian);
            $rombels = $data['rombels'];
            $pengujisData = $data['pengujis'];

            // Filter by rombel if selected
            if ($selectedRombelId) {
                $pengujisData = $pengujisData->filter(fn ($p) => $p['rombel_id'] == $selectedRombelId);
            }

            // Filter by status if selected
            if ($selectedStatus) {
                $pengujisData = $pengujisData->filter(fn ($p) => $p['status'] === $selectedStatus);
            }

            $kpis = $data['kpis'];
        }

        return view('pages.kaprodi.monitoring-penguji.index', compact(
            'ujians',
            'ujian',
            'rombels',
            'selectedUjianId',
            'selectedRombelId',
            'selectedStatus',
            'pengujisData',
            'kpis'
        ));
    }

    public function export(Request $request)
    {
        $ujianId  = $request->input('ujian_id');
        $rombelId = $request->input('rombel_id');
        $status   = $request->input('status');

        $ujian = UkkUjian::with('tahunPelajaran')->findOrFail($ujianId);
        $data  = $this->buildMonitoringData($ujian);

        $pengujisData = $data['pengujis'];
        $rombels      = $data['rombels'];

        $rombelLabel = 'Semua Kelas';
        if ($rombelId) {
            $matchedRombel = $rombels->firstWhere('id', (int) $rombelId);
            $rombelLabel   = $matchedRombel ? ($matchedRombel->kelas?->nama_kelas ?? 'Kelas #' . $rombelId) : 'Kelas #' . $rombelId;
            $pengujisData  = $pengujisData->filter(fn ($p) => $p['rombel_id'] == $rombelId);
        }

        if ($status) {
            $pengujisData = $pengujisData->filter(fn ($p) => $p['status'] === $status);
        }

        $filename = 'Monitoring_Penguji_UKK_' . str_replace(' ', '_', $ujian->nama_ujian);
        if ($rombelId) {
            $filename .= '_' . str_replace(' ', '_', $rombelLabel);
        }
        $filename .= '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new MonitoringPengujiUkkExport(
            $ujian,
            $pengujisData,
            $rombelLabel,
            $data['kpis']
        ), $filename);
    }

    private function buildMonitoringData(UkkUjian $ujian): array
    {
        $ujian->load([
            'tahunPelajaran',
            'rombels.kelas',
            'rombels.siswa',
            'instrumens.soalPengetahuan',
            'instrumens.kategoriKeterampilan.indikator',
            'ukkPengujis.user.masterGuru',
            'ukkPengujis.rombel.kelas',
        ]);

        $rombels = $ujian->rombels->sortBy(fn ($r) => $r->kelas->nama_kelas ?? '');

        // Pre-fetch all evaluations for this exam
        $allSoalIds = $ujian->instrumens->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'))->all();
        $allIndIds  = $ujian->instrumens->flatMap(fn ($i) => $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id')))->all();

        $allNilaiP = UkkNilaiPengetahuan::whereIn('soal_id', $allSoalIds)
            ->get()
            ->groupBy('master_siswa_id');

        $allNilaiK = UkkNilaiKeterampilan::whereIn('indikator_id', $allIndIds)
            ->get()
            ->groupBy('master_siswa_id');

        $allNilaiSikap = UkkNilaiSikap::where('ukk_ujian_id', $ujian->id)
            ->get()
            ->keyBy('master_siswa_id');

        $allNilaiBilingual = UkkNilaiBilingual::where('ukk_ujian_id', $ujian->id)
            ->get()
            ->keyBy('master_siswa_id');

        // Pre-fetch timestamps per penguji
        $timestampsPerUser = collect();
        $userTimestampsP = UkkNilaiPengetahuan::whereIn('soal_id', $allSoalIds)
            ->whereNotNull('penguji_id')
            ->select('penguji_id', 'updated_at')
            ->get();
        $userTimestampsK = UkkNilaiKeterampilan::whereIn('indikator_id', $allIndIds)
            ->whereNotNull('penguji_id')
            ->select('penguji_id', 'updated_at')
            ->get();
        $userTimestampsS = UkkNilaiSikap::where('ukk_ujian_id', $ujian->id)
            ->whereNotNull('penguji_id')
            ->select('penguji_id', 'updated_at')
            ->get();
        $userTimestampsB = UkkNilaiBilingual::where('ukk_ujian_id', $ujian->id)
            ->whereNotNull('penguji_id')
            ->select('penguji_id', 'updated_at')
            ->get();

        foreach ([$userTimestampsP, $userTimestampsK, $userTimestampsS, $userTimestampsB] as $tsColl) {
            foreach ($tsColl as $row) {
                $uid = $row->penguji_id;
                $cur = $timestampsPerUser->get($uid);
                if (!$cur || Carbon::parse($row->updated_at)->gt($cur)) {
                    $timestampsPerUser->put($uid, Carbon::parse($row->updated_at));
                }
            }
        }

        $pengujisData = collect();

        foreach ($ujian->ukkPengujis as $p) {
            $user = $p->user;
            if (!$user) continue;

            $namaPenguji = $user->masterGuru?->nama_lengkap ?? $user->name;
            $kodeGuru    = $user->masterGuru?->kode_guru ?? '-';
            $avatar      = $user->avatar;

            $rombel = $p->rombel;
            $rombelNama = $rombel ? ($rombel->kelas?->nama_kelas ?? 'Kelas #' . $rombel->id) : 'Semua Kelas';

            // Determine target students
            $assignedSiswas = collect();
            if ($rombel) {
                $sortedSiswas = $rombel->siswa->sortBy('nama_lengkap')->values();
                if (is_array($p->siswa_ids) && !empty($p->siswa_ids)) {
                    $allowedSet = collect($p->siswa_ids)->flip();
                    $assignedSiswas = $sortedSiswas->filter(fn ($s) => $allowedSet->has($s->id))->values();
                } elseif ($p->no_urut_mulai || $p->no_urut_selesai) {
                    $m = $p->no_urut_mulai ?? 1;
                    $s = $p->no_urut_selesai ?? 999;
                    $assignedSiswas = $sortedSiswas->filter(function ($sw, $idx) use ($m, $s) {
                        $no = $idx + 1;
                        return $no >= $m && $no <= $s;
                    })->values();
                } else {
                    $assignedSiswas = $sortedSiswas;
                }
            } else {
                // Global assignment
                foreach ($rombels as $r) {
                    $assignedSiswas = $assignedSiswas->merge($r->siswa->sortBy('nama_lengkap')->values());
                }
                $assignedSiswas = $assignedSiswas->unique('id')->values();
            }

            // Target Label
            $targetLabel = 'Seluruh Siswa (' . $assignedSiswas->count() . ' Siswa)';
            if (is_array($p->siswa_ids) && !empty($p->siswa_ids)) {
                $targetLabel = count($p->siswa_ids) . ' Siswa Terpilih';
            } elseif ($p->no_urut_mulai || $p->no_urut_selesai) {
                $targetLabel = 'No. Urut ' . ($p->no_urut_mulai ?? 1) . ' s.d. ' . ($p->no_urut_selesai ?? $assignedSiswas->count());
            }

            // Schedule Label
            $tglStr = $p->tanggal_pelaksanaan ? Carbon::parse($p->tanggal_pelaksanaan)->translatedFormat('d M Y') : null;
            $jamStr = $p->jam_mulai ? (substr($p->jam_mulai, 0, 5) . ($p->jam_selesai ? ' - ' . substr($p->jam_selesai, 0, 5) : '') . ' WIB') : null;
            $ruangStr = $p->ruangan ? 'Ruang: ' . $p->ruangan : null;
            $jadwalParts = array_filter([$tglStr, $jamStr, $ruangStr]);
            $jadwalLabel = !empty($jadwalParts) ? implode(' · ', $jadwalParts) : 'Belum diatur';

            // Role label
            $peran = $p->peran;
            $peranLabel = match ($peran) {
                'pengetahuan' => $ujian->is_bilingual ? 'Wawancara Bilingual (EN)' : 'Pengetahuan / Teori',
                'keterampilan' => 'Keterampilan Praktik & Sikap',
                'semua' => 'Semua Aspek (Teori & Praktik)',
                default => ucfirst($peran),
            };

            // Evaluate each student
            $siswaDetails = collect();
            $selesaiCount = 0;
            $sebagianCount = 0;
            $belumCount = 0;

            // Relevant questions/indicators based on role
            $isExamBilingual = (bool) $ujian->is_bilingual;
            $reqSoals = $ujian->instrumens->flatMap(function ($i) use ($isExamBilingual, $peran) {
                return $i->soalPengetahuan->filter(function ($soal) use ($isExamBilingual, $peran) {
                    $isBilingual = ($soal->jenis_soal === 'bilingual')
                        || ($isExamBilingual && (empty($soal->jenis_soal) || $soal->jenis_soal === 'bilingual'));
                    if ($peran === 'keterampilan') return false;
                    if ($peran === 'pengetahuan') return $isExamBilingual ? $isBilingual : true;
                    if ($isExamBilingual) return !$isBilingual;
                    return true;
                });
            });
            $reqSoalIds = $reqSoals->pluck('id')->all();

            $reqInds = $ujian->instrumens->flatMap(function ($i) use ($peran) {
                if ($peran === 'pengetahuan') return collect();
                return $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id'));
            })->all();

            foreach ($assignedSiswas as $sIdx => $siswa) {
                $gradedPCount = 0;
                if (!empty($reqSoalIds)) {
                    $sNilaiP = $allNilaiP->get($siswa->id, collect())->keyBy('soal_id');
                    $gradedPCount = collect($reqSoalIds)->filter(fn ($id) => $sNilaiP->has($id))->count();
                }

                $gradedKCount = 0;
                if (!empty($reqInds)) {
                    $sNilaiK = $allNilaiK->get($siswa->id, collect())->keyBy('indikator_id');
                    $gradedKCount = collect($reqInds)->filter(fn ($id) => $sNilaiK->has($id))->count();
                }

                $sikapDone = true;
                if (in_array($peran, ['semua', 'keterampilan'])) {
                    $sSikap = $allNilaiSikap->get($siswa->id);
                    $sikapDone = $sSikap && !empty($sSikap->kerapian);
                }

                $bilingualDone = true;
                if ($ujian->is_bilingual && $peran === 'pengetahuan') {
                    $sBil = $allNilaiBilingual->get($siswa->id);
                    $bilingualDone = $sBil && !empty($sBil->fluency) && !empty($sBil->pronunciation) && !empty($sBil->vocabulary) && !empty($sBil->comprehension);
                }

                $pComplete = empty($reqSoalIds) || ($gradedPCount >= count($reqSoalIds) && $bilingualDone);
                $kComplete = empty($reqInds) || ($gradedKCount >= count($reqInds) && $sikapDone);

                $totalReqItems = count($reqSoalIds) + count($reqInds) + (in_array($peran, ['semua', 'keterampilan']) ? 1 : 0) + ($ujian->is_bilingual && $peran === 'pengetahuan' ? 1 : 0);
                $totalGradedItems = $gradedPCount + $gradedKCount + ($sikapDone && in_array($peran, ['semua', 'keterampilan']) ? 1 : 0) + ($bilingualDone && $ujian->is_bilingual && $peran === 'pengetahuan' ? 1 : 0);

                if ($pComplete && $kComplete) {
                    $sStatus = 'Selesai';
                    $selesaiCount++;
                } elseif ($totalGradedItems > 0) {
                    $sStatus = 'Sebagian';
                    $sebagianCount++;
                } else {
                    $sStatus = 'Belum';
                    $belumCount++;
                }

                $siswaDetails->push([
                    'id'            => $siswa->id,
                    'nomor_urut'    => $sIdx + 1,
                    'nis'           => $siswa->nis ?: '-',
                    'nama_lengkap'  => $siswa->nama_lengkap,
                    'status'        => $sStatus,
                    'items_graded'  => $totalGradedItems . ' / ' . $totalReqItems . ' item',
                ]);
            }

            $targetCount = $assignedSiswas->count();
            $persentase  = $targetCount > 0 ? round(($selesaiCount / $targetCount) * 100, 1) : 0;

            if ($targetCount > 0 && $selesaiCount >= $targetCount) {
                $statusPenguji = 'Selesai';
            } elseif ($selesaiCount > 0 || $sebagianCount > 0) {
                $statusPenguji = 'Sedang Menilai';
            } else {
                $statusPenguji = 'Belum Mulai';
            }

            // Last active timestamp
            $lastActiveTs = $timestampsPerUser->get($user->id);
            $terakhirAktifStr = 'Belum ada aktivitas';
            $terakhirAktifDiff = '';
            if ($lastActiveTs) {
                $terakhirAktifStr = $lastActiveTs->translatedFormat('d M Y, H:i') . ' WIB';
                $terakhirAktifDiff = $lastActiveTs->diffForHumans();
            }

            $pengujisData->push([
                'id'                  => $p->id,
                'user_id'             => $user->id,
                'nama_penguji'        => $namaPenguji,
                'kode_guru'           => $kodeGuru,
                'avatar'              => $avatar,
                'peran'               => $peran,
                'peran_label'         => $peranLabel,
                'rombel_id'           => $p->rombel_id,
                'rombel_nama'         => $rombelNama,
                'target_label'        => $targetLabel,
                'jadwal_label'        => $jadwalLabel,
                'target_count'        => $targetCount,
                'selesai_count'       => $selesaiCount,
                'sebagian_count'      => $sebagianCount,
                'belum_count'         => $belumCount,
                'persentase'          => $persentase,
                'status'              => $statusPenguji,
                'terakhir_aktif'      => $terakhirAktifStr,
                'terakhir_aktif_diff' => $terakhirAktifDiff,
                'siswa_details'       => $siswaDetails,
            ]);
        }

        // Sort: Belum Mulai / Sedang Menilai first, then Selesai
        $statusOrder = ['Belum Mulai' => 1, 'Sedang Menilai' => 2, 'Selesai' => 3];
        $pengujisData = $pengujisData->sortBy(fn ($p) => $statusOrder[$p['status']] ?? 99)->values();

        // Calculate KPIs
        $totalPenguji  = $pengujisData->count();
        $selesaiPenguji = $pengujisData->where('status', 'Selesai')->count();
        $prosesPenguji  = $pengujisData->where('status', 'Sedang Menilai')->count();
        $belumPenguji   = $pengujisData->where('status', 'Belum Mulai')->count();
        $avgProgress   = $totalPenguji > 0 ? round($pengujisData->avg('persentase'), 1) : 0;
        $totalTarget   = $pengujisData->sum('target_count');
        $totalSelesai  = $pengujisData->sum('selesai_count');

        $kpis = [
            'total_penguji'       => $totalPenguji,
            'selesai_count'       => $selesaiPenguji,
            'proses_count'        => $prosesPenguji,
            'belum_count'         => $belumPenguji,
            'avg_progress'        => $avgProgress,
            'total_target_siswa'  => $totalTarget,
            'total_selesai_siswa' => $totalSelesai,
        ];

        return [
            'rombels'  => $rombels,
            'pengujis' => $pengujisData,
            'kpis'     => $kpis,
        ];
    }
}
