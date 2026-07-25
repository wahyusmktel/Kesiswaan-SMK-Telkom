<?php

namespace App\Services\Erapor;

use App\Models\AppSetting;
use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use App\Models\MasterSiswa;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EraporReadinessService
{
    public function inspect(): array
    {
        $activePeriods = TahunPelajaran::query()
            ->where('is_active', true)
            ->orderByDesc('id')
            ->get();
        $period = $activePeriods->first();
        $checks = collect();

        $checks->push($this->activePeriodCheck($activePeriods));
        $checks->push($this->schoolIdentityCheck());

        $stats = [
            'teachers' => MasterGuru::count(),
            'students' => MasterSiswa::active()->count(),
            'rombels' => 0,
            'subjects' => MataPelajaran::count(),
            'assignments' => 0,
        ];

        if ($period) {
            $periodResult = $this->inspectPeriod($period);
            $checks = $checks->concat($periodResult['checks']);
            $stats = array_merge($stats, $periodResult['stats']);
        } else {
            $checks->push($this->check(
                'active_rombel',
                'Rombel periode aktif',
                'blocked',
                'Rombel belum dapat diperiksa sebelum tahun pelajaran/semester aktif ditetapkan.',
                0,
                'master-data.tahun-pelajaran.index',
                'manage tahun pelajaran',
                ['Waka Kesiswaan', 'Operator', 'Super Admin'],
            ));
            $checks->push($this->check(
                'teaching_assignments',
                'Penugasan pembelajaran',
                'blocked',
                'Penugasan guru–mapel–rombel belum dapat dibentuk tanpa periode aktif.',
                0,
            ));
        }

        $overallStatus = $checks->contains(fn (array $check) => $check['status'] === 'blocked')
            ? 'blocked'
            : ($checks->contains(fn (array $check) => $check['status'] === 'warning') ? 'warning' : 'ready');

        return [
            'period' => $period,
            'overall_status' => $overallStatus,
            'stats' => $stats,
            'checks' => $checks->values(),
            'issues_count' => $checks->whereIn('status', ['blocked', 'warning'])->count(),
            'generated_at' => now(),
        ];
    }

    private function inspectPeriod(TahunPelajaran $period): array
    {
        $rombelQuery = Rombel::query()->where('tahun_pelajaran_id', $period->id);
        $rombelCount = (clone $rombelQuery)->count();
        $emptyRombelCount = (clone $rombelQuery)->whereDoesntHave('siswa')->count();
        $unscheduledRombelCount = (clone $rombelQuery)->whereDoesntHave('jadwalPelajaran')->count();

        $activeStudentsInPeriod = MasterSiswa::query()
            ->active()
            ->whereHas('rombels', fn ($query) => $query->where('tahun_pelajaran_id', $period->id));
        $studentCount = (clone $activeStudentsInPeriod)->count();
        $incompleteStudentCount = (clone $activeStudentsInPeriod)
            ->where(function ($query) {
                $query->whereNull('tempat_lahir')
                    ->orWhereNull('tanggal_lahir')
                    ->orWhereNull('alamat');
            })
            ->count();
        $studentsWithoutActiveRombel = MasterSiswa::query()
            ->active()
            ->whereDoesntHave('rombels', fn ($query) => $query->where('tahun_pelajaran_id', $period->id))
            ->count();

        $duplicateMembershipCount = DB::table('rombel_siswa')
            ->join('rombels', 'rombels.id', '=', 'rombel_siswa.rombel_id')
            ->where('rombels.tahun_pelajaran_id', $period->id)
            ->select('rombel_siswa.master_siswa_id')
            ->groupBy('rombel_siswa.master_siswa_id')
            ->havingRaw('COUNT(DISTINCT rombel_siswa.rombel_id) > 1')
            ->get()
            ->count();

        $scheduleQuery = JadwalPelajaran::query()
            ->whereHas('rombel', fn ($query) => $query->where('tahun_pelajaran_id', $period->id));
        $assignments = (clone $scheduleQuery)
            ->select(['rombel_id', 'mata_pelajaran_id', 'master_guru_id'])
            ->distinct()
            ->get();
        $assignmentCount = $assignments->count();
        $scheduledTeacherIds = $assignments->pluck('master_guru_id')->unique()->values();
        $scheduledSubjectCount = $assignments->pluck('mata_pelajaran_id')->unique()->count();
        $teachersWithoutAccount = $scheduledTeacherIds->isEmpty()
            ? 0
            : MasterGuru::query()
                ->whereIn('id', $scheduledTeacherIds)
                ->whereNull('user_id')
                ->count();

        $checks = collect([
            $this->check(
                'active_rombel',
                'Rombel periode aktif',
                $rombelCount > 0 ? 'ready' : 'blocked',
                $rombelCount > 0
                    ? "{$rombelCount} rombel terhubung ke periode aktif."
                    : 'Belum ada rombel yang terhubung ke periode aktif.',
                $rombelCount,
                'master-data.rombel.index',
                'manage rombel',
                ['Waka Kesiswaan', 'Operator', 'Super Admin'],
            ),
            $this->check(
                'student_memberships',
                'Keanggotaan siswa',
                $duplicateMembershipCount > 0
                    ? 'blocked'
                    : (($studentCount === 0 || $emptyRombelCount > 0 || $studentsWithoutActiveRombel > 0) ? 'warning' : 'ready'),
                $this->studentMembershipMessage(
                    $studentCount,
                    $emptyRombelCount,
                    $studentsWithoutActiveRombel,
                    $duplicateMembershipCount,
                ),
                $studentCount,
                'master-data.rombel.index',
                'manage rombel',
                ['Waka Kesiswaan', 'Operator', 'Super Admin'],
            ),
            $this->check(
                'teaching_assignments',
                'Penugasan pembelajaran',
                $assignmentCount === 0 ? 'blocked' : ($unscheduledRombelCount > 0 ? 'warning' : 'ready'),
                $assignmentCount === 0
                    ? 'Belum ada pasangan unik guru–mapel–rombel dari jadwal periode aktif.'
                    : "{$assignmentCount} penugasan unik ditemukan; {$unscheduledRombelCount} rombel belum memiliki jadwal.",
                $assignmentCount,
                'kurikulum.jadwal-pelajaran.index',
                'manage jadwal pelajaran',
                ['Kurikulum'],
            ),
            $this->check(
                'teacher_accounts',
                'Akun guru pengampu',
                $teachersWithoutAccount > 0 ? 'warning' : 'ready',
                $teachersWithoutAccount > 0
                    ? "{$teachersWithoutAccount} guru terjadwal belum terhubung ke akun SISFO."
                    : 'Semua guru terjadwal telah terhubung ke akun SISFO.',
                $scheduledTeacherIds->count(),
                'kurikulum.master-guru.index',
                'manage guru',
                ['Kurikulum'],
            ),
            $this->check(
                'student_identity',
                'Identitas dasar siswa',
                $incompleteStudentCount > 0 ? 'warning' : 'ready',
                $incompleteStudentCount > 0
                    ? "{$incompleteStudentCount} siswa pada periode aktif belum memiliki tempat lahir, tanggal lahir, atau alamat lengkap."
                    : 'Identitas dasar seluruh siswa pada periode aktif telah lengkap.',
                $incompleteStudentCount,
                'master-data.siswa.index',
                'manage siswa',
                ['Waka Kesiswaan', 'Operator', 'Super Admin'],
            ),
        ]);

        return [
            'stats' => [
                'rombels' => $rombelCount,
                'students' => $studentCount,
                'subjects' => $scheduledSubjectCount,
                'assignments' => $assignmentCount,
            ],
            'checks' => $checks,
        ];
    }

    private function activePeriodCheck(Collection $activePeriods): array
    {
        $count = $activePeriods->count();

        return $this->check(
            'active_period',
            'Tahun pelajaran aktif',
            $count === 1 ? 'ready' : ($count === 0 ? 'blocked' : 'warning'),
            match (true) {
                $count === 0 => 'Belum ada tahun pelajaran/semester yang ditandai aktif.',
                $count > 1 => "{$count} periode ditandai aktif; e-Rapor memerlukan tepat satu periode aktif.",
                default => "Periode {$activePeriods->first()->tahun} {$activePeriods->first()->semester} siap digunakan.",
            },
            $count,
            'master-data.tahun-pelajaran.index',
            'manage tahun pelajaran',
            ['Waka Kesiswaan', 'Operator', 'Super Admin'],
        );
    }

    private function schoolIdentityCheck(): array
    {
        $settings = AppSetting::query()->first();
        $missing = collect([
            'nama sekolah' => $settings?->school_name,
            'alamat' => $settings?->address,
            'email' => $settings?->email,
            'telepon' => $settings?->phone,
        ])->filter(fn ($value) => blank($value))->keys();

        return $this->check(
            'school_identity',
            'Identitas sekolah',
            $missing->isEmpty() ? 'ready' : 'warning',
            $missing->isEmpty()
                ? 'Identitas dasar sekolah telah tersedia.'
                : 'Data belum lengkap: '.$missing->implode(', ').'.',
            $missing->count(),
        );
    }

    private function studentMembershipMessage(
        int $studentCount,
        int $emptyRombelCount,
        int $studentsWithoutActiveRombel,
        int $duplicateMembershipCount,
    ): string {
        $parts = ["{$studentCount} siswa aktif terhubung ke rombel periode ini"];

        if ($emptyRombelCount > 0) {
            $parts[] = "{$emptyRombelCount} rombel kosong";
        }

        if ($studentsWithoutActiveRombel > 0) {
            $parts[] = "{$studentsWithoutActiveRombel} siswa aktif belum masuk rombel";
        }

        if ($duplicateMembershipCount > 0) {
            $parts[] = "{$duplicateMembershipCount} siswa berada di lebih dari satu rombel aktif";
        }

        return implode('; ', $parts).'.';
    }

    private function check(
        string $key,
        string $label,
        string $status,
        string $message,
        int $count,
        ?string $route = null,
        ?string $permission = null,
        array $roles = [],
    ): array {
        return compact('key', 'label', 'status', 'message', 'count', 'route', 'permission', 'roles');
    }
}
