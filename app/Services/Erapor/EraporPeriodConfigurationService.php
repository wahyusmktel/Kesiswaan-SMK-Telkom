<?php

namespace App\Services\Erapor;

use App\Models\Erapor\EraporPeriodSetting;
use App\Models\Erapor\EraporTeachingAssignment;
use App\Models\Rombel;
use App\Models\TahunPelajaran;

class EraporPeriodConfigurationService
{
    /**
     * @return array{
     *     rombels: int,
     *     configured_rombels: int,
     *     unconfigured_rombels: int,
     *     assignments: int,
     *     unmapped_assignments: int,
     *     assignments_without_passing_grade: int,
     *     can_open_assessment: bool
     * }
     */
    public function readiness(TahunPelajaran $period, ?EraporPeriodSetting $setting = null): array
    {
        $setting ??= EraporPeriodSetting::query()
            ->where('tahun_pelajaran_id', $period->id)
            ->first();

        $rombelCount = Rombel::query()
            ->where('tahun_pelajaran_id', $period->id)
            ->count();
        $configuredRombelCount = $setting
            ? $setting->rombelCurricula()->count()
            : 0;
        $assignmentQuery = EraporTeachingAssignment::query()
            ->where('tahun_pelajaran_id', $period->id)
            ->where('is_active', true);
        $assignmentCount = (clone $assignmentQuery)->count();
        $unmappedAssignmentCount = (clone $assignmentQuery)
            ->whereNull('erapor_subject_mapping_id')
            ->count();
        $missingPassingGradeCount = (clone $assignmentQuery)
            ->whereNull('passing_grade')
            ->count();

        return [
            'rombels' => $rombelCount,
            'configured_rombels' => $configuredRombelCount,
            'unconfigured_rombels' => max(0, $rombelCount - $configuredRombelCount),
            'assignments' => $assignmentCount,
            'unmapped_assignments' => $unmappedAssignmentCount,
            'assignments_without_passing_grade' => $missingPassingGradeCount,
            'can_open_assessment' => $rombelCount > 0
                && $configuredRombelCount === $rombelCount
                && $assignmentCount > 0
                && $unmappedAssignmentCount === 0
                && $missingPassingGradeCount === 0,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function blockers(TahunPelajaran $period, ?EraporPeriodSetting $setting = null): array
    {
        $readiness = $this->readiness($period, $setting);
        $blockers = [];

        if ($readiness['unconfigured_rombels'] > 0) {
            $blockers[] = "{$readiness['unconfigured_rombels']} rombel belum memiliki kurikulum.";
        }

        if ($readiness['assignments'] === 0) {
            $blockers[] = 'Penugasan permanen belum disinkronkan dari jadwal.';
        }

        if ($readiness['unmapped_assignments'] > 0) {
            $blockers[] = "{$readiness['unmapped_assignments']} penugasan belum terhubung ke referensi mapel.";
        }

        if ($readiness['assignments_without_passing_grade'] > 0) {
            $blockers[] = "{$readiness['assignments_without_passing_grade']} penugasan belum memiliki KKM/KKTP.";
        }

        return $blockers;
    }
}
