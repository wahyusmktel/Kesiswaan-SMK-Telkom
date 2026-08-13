<?php

namespace App\Services;

use App\Models\FingerprintAttendance;
use App\Models\MasterGuru;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FingerprintAttendanceReportService
{
    public function daily(Carbon $date, bool $includeInactive = true): array
    {
        $employees = $this->employees($includeInactive);
        $recaps = $this->attendanceRecaps($date->copy()->startOfDay(), $date->copy()->endOfDay())
            ->keyBy(fn ($row) => (int) $row->app_user_id);

        $rows = $employees->map(function (array $employee) use ($recaps) {
            $recap = $employee['app_user_id'] ? $recaps->get($employee['app_user_id']) : null;

            return $employee + $this->scanTimes($recap);
        });

        return compact('rows', 'date');
    }

    public function monthly(Carbon $month, bool $includeInactive, bool $includeAttachments): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $employees = $this->employees($includeInactive);
        $recaps = $this->attendanceRecaps($start, $end);
        $attendanceCounts = $recaps
            ->groupBy(fn ($row) => (int) $row->app_user_id)
            ->map(fn (Collection $items) => $items->count());

        $summaryRows = $employees->map(fn (array $employee) => $employee + [
            'attendance_count' => $employee['app_user_id']
                ? (int) $attendanceCounts->get($employee['app_user_id'], 0)
                : 0,
        ]);

        $employeeByUser = $employees
            ->filter(fn (array $employee) => $employee['app_user_id'])
            ->keyBy('app_user_id');

        $attachments = collect();
        if ($includeAttachments) {
            $attachments = $recaps
                ->groupBy(fn ($row) => Carbon::parse($row->attendance_date)->toDateString())
                ->sortKeys()
                ->map(function (Collection $dailyRecaps, string $date) use ($employeeByUser) {
                    $rows = $dailyRecaps
                        ->map(function ($recap) use ($employeeByUser) {
                            $employee = $employeeByUser->get((int) $recap->app_user_id);

                            return $employee ? $employee + $this->scanTimes($recap) : null;
                        })
                        ->filter()
                        ->sortBy(fn (array $row) => mb_strtolower($row['name']))
                        ->values();

                    return [
                        'date' => Carbon::parse($date),
                        'rows' => $rows,
                    ];
                })
                ->filter(fn (array $day) => $day['rows']->isNotEmpty())
                ->values();
        }

        return compact('month', 'summaryRows', 'attachments');
    }

    private function employees(bool $includeInactive): Collection
    {
        $masterEmployees = MasterGuru::with(['user.roles', 'dapodikGuru'])
            ->get()
            ->map(function (MasterGuru $teacher) {
                $employmentStatus = trim((string) $teacher->dapodikGuru?->status_kepegawaian);

                return [
                    'app_user_id' => $teacher->user_id ? (int) $teacher->user_id : null,
                    'name' => $teacher->nama_lengkap ?: $teacher->user?->name ?: '-',
                    'nip' => trim((string) $teacher->dapodikGuru?->nip),
                    'nuptk' => trim((string) ($teacher->dapodikGuru?->nuptk ?: $teacher->nuptk)),
                    'employee_type' => $this->employeeType($teacher->dapodikGuru?->jenis_ptk, $teacher->user),
                    'employment_status' => $employmentStatus,
                    'is_active' => ! $this->isInactiveStatus($employmentStatus),
                ];
            });

        $knownUserIds = $masterEmployees->pluck('app_user_id')->filter()->all();
        $mappedEmployees = User::with('roles')
            ->whereHas('fingerprintUsers')
            ->whereNotIn('id', $knownUserIds)
            ->whereDoesntHave('roles', fn ($query) => $query->whereIn('name', ['Siswa', 'siswa', 'Kantin']))
            ->get()
            ->map(fn (User $user) => [
                'app_user_id' => (int) $user->id,
                'name' => $user->name,
                'nip' => '',
                'nuptk' => '',
                'employee_type' => $this->employeeType(null, $user),
                'employment_status' => '',
                'is_active' => true,
            ]);

        return $masterEmployees
            ->concat($mappedEmployees)
            ->when(! $includeInactive, fn (Collection $rows) => $rows->where('is_active', true))
            ->unique(fn (array $employee) => $employee['app_user_id'] ?: 'master:'.mb_strtolower($employee['name']))
            ->sortBy(fn (array $employee) => mb_strtolower($employee['name']))
            ->values();
    }

    private function attendanceRecaps(Carbon $start, Carbon $end): Collection
    {
        return FingerprintAttendance::query()
            ->select([
                'app_user_id',
                DB::raw('DATE(timestamp) as attendance_date'),
                DB::raw('MIN(timestamp) as first_scan'),
                DB::raw('MAX(timestamp) as last_scan'),
                DB::raw('COUNT(*) as total_scans'),
            ])
            ->whereNotNull('app_user_id')
            ->whereBetween('timestamp', [$start, $end])
            ->groupBy('app_user_id', DB::raw('DATE(timestamp)'))
            ->orderBy('attendance_date')
            ->get();
    }

    private function scanTimes($recap): array
    {
        if (! $recap) {
            return ['check_in' => null, 'check_out' => null];
        }

        return [
            'check_in' => Carbon::parse($recap->first_scan)->format('H:i'),
            'check_out' => (int) $recap->total_scans > 1
                ? Carbon::parse($recap->last_scan)->format('H:i')
                : null,
        ];
    }

    private function isInactiveStatus(string $status): bool
    {
        return str($status)->lower()->contains([
            'tidak aktif',
            'nonaktif',
            'pensiun',
            'keluar',
            'mutasi',
            'meninggal',
        ]);
    }

    private function employeeType(?string $dapodikType, ?User $user): string
    {
        if (filled($dapodikType)) {
            return trim((string) $dapodikType);
        }

        $roles = $user?->roles?->pluck('name') ?? collect();

        return match (true) {
            $roles->contains('Kepala Sekolah') => 'Kepala Sekolah',
            $roles->contains(fn ($role) => in_array($role, ['Guru', 'Guru Kelas', 'Wali Kelas', 'Guru BK', 'Koordinator Prakerin'], true)) => 'Guru',
            default => 'Tenaga Kependidikan',
        };
    }
}
