<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\AssetReport;
use App\Models\GuruIzin;
use App\Models\Keterlambatan;
use App\Models\MasterGuru;
use App\Models\MasterSiswa;
use App\Models\OkrPeriod;
use App\Models\OkrProgressUpdate;
use App\Models\OkrUnit;
use App\Models\PrakerinJurnal;
use App\Models\PrakerinPenempatan;
use App\Models\Rombel;
use App\Models\SiswaPelanggaran;
use App\Models\TahunPelajaran;
use App\Models\TuLetterRequest;
use App\Models\UksMedicalRecord;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $dashboard = Cache::remember('kepala-sekolah.dashboard.v1', now()->addMinutes(2), function () {
            $activePeriod = OkrPeriod::query()
                ->with('academicYear')
                ->where('status', 'active')
                ->latest('id')
                ->first();

            $units = OkrUnit::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->when($activePeriod, fn ($query) => $query->with([
                    'plans' => fn ($plans) => $plans
                        ->whereNull('parent_id')
                        ->whereHas('keyResult.objective', fn ($objectives) => $objectives
                            ->where('okr_period_id', $activePeriod->id)),
                ]))
                ->get();

            $unitProgress = $units->map(function (OkrUnit $unit) {
                $plans = $unit->relationLoaded('plans') ? $unit->plans : collect();
                $progress = round((float) $plans->avg('progress_percent'), 1);

                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'code' => $unit->code,
                    'progress' => $progress,
                    'total' => $plans->count(),
                    'completed' => $plans->where('status', 'completed')->count(),
                    'at_risk' => $plans->where('status', 'at_risk')->count(),
                    'last_update' => $plans->max('updated_at'),
                ];
            });

            $recentUpdates = $activePeriod
                ? OkrProgressUpdate::query()
                    ->with(['plan.unit:id,name', 'plan.keyResult:id,title', 'user:id,name'])
                    ->whereHas('plan.keyResult.objective', fn ($query) => $query
                        ->where('okr_period_id', $activePeriod->id))
                    ->latest('recorded_at')
                    ->latest('id')
                    ->limit(8)
                    ->get()
                : collect();

            $activeAcademicYear = TahunPelajaran::query()->where('is_active', true)->first();
            $openAssetReports = AssetReport::query()->whereIn('status', ['baru', 'diverifikasi', 'diproses']);
            $pendingTeacherPermits = GuruIzin::query()->where(function ($query) {
                $query->where('status_piket', 'menunggu')
                    ->orWhere('status_kurikulum', 'menunggu')
                    ->orWhere('status_sdm', 'menunggu');
            });

            $highlights = [
                [
                    'unit' => 'Kesiswaan',
                    'metric' => MasterSiswa::query()->where('status', 'aktif')->count(),
                    'label' => 'siswa aktif',
                    'detail' => Keterlambatan::query()->whereDate('waktu_dicatat_security', today())->count().' terlambat hari ini',
                    'tone' => 'blue',
                ],
                [
                    'unit' => 'Kurikulum',
                    'metric' => Rombel::query()
                        ->when($activeAcademicYear, fn ($query) => $query->where('tahun_pelajaran_id', $activeAcademicYear->id))
                        ->count(),
                    'label' => 'rombel aktif',
                    'detail' => MasterGuru::query()->count().' guru terdata',
                    'tone' => 'indigo',
                ],
                [
                    'unit' => 'SDM',
                    'metric' => (clone $pendingTeacherPermits)->count(),
                    'label' => 'izin perlu diproses',
                    'detail' => GuruIzin::query()->whereMonth('tanggal_mulai', now()->month)->whereYear('tanggal_mulai', now()->year)->count().' pengajuan bulan ini',
                    'tone' => 'violet',
                ],
                [
                    'unit' => 'Sarana Prasarana',
                    'metric' => (clone $openAssetReports)->count(),
                    'label' => 'laporan masih terbuka',
                    'detail' => (clone $openAssetReports)->whereIn('urgency', ['tinggi', 'darurat'])->count().' prioritas tinggi',
                    'tone' => 'amber',
                ],
                [
                    'unit' => 'UKS',
                    'metric' => UksMedicalRecord::query()->whereMonth('visited_at', now()->month)->whereYear('visited_at', now()->year)->count(),
                    'label' => 'kunjungan bulan ini',
                    'detail' => UksMedicalRecord::query()->whereMonth('visited_at', now()->month)->whereYear('visited_at', now()->year)->where('condition', 'berat')->count().' kondisi berat',
                    'tone' => 'rose',
                ],
                [
                    'unit' => 'Prakerin',
                    'metric' => PrakerinPenempatan::query()->where('status', 'aktif')->count(),
                    'label' => 'penempatan aktif',
                    'detail' => PrakerinJurnal::query()->where('status_verifikasi', 'menunggu')->count().' jurnal menunggu verifikasi',
                    'tone' => 'emerald',
                ],
                [
                    'unit' => 'Tata Usaha',
                    'metric' => TuLetterRequest::query()->where('status', 'pending')->count(),
                    'label' => 'permohonan menunggu',
                    'detail' => TuLetterRequest::query()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count().' permohonan bulan ini',
                    'tone' => 'cyan',
                ],
                [
                    'unit' => 'Pembinaan Siswa',
                    'metric' => SiswaPelanggaran::query()->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->count(),
                    'label' => 'pelanggaran bulan ini',
                    'detail' => Keterlambatan::query()->whereMonth('waktu_dicatat_security', now()->month)->whereYear('waktu_dicatat_security', now()->year)->count().' keterlambatan bulan ini',
                    'tone' => 'orange',
                ],
            ];

            return compact('activePeriod', 'unitProgress', 'recentUpdates', 'highlights') + [
                'overallProgress' => round((float) $unitProgress->where('total', '>', 0)->avg('progress'), 1),
                'atRiskCount' => $unitProgress->sum('at_risk'),
                'completedCount' => $unitProgress->sum('completed'),
                'generatedAt' => now(),
            ];
        });

        return view('pages.kepala-sekolah.dashboard', $dashboard);
    }
}
