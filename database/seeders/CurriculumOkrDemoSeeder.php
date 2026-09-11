<?php

namespace Database\Seeders;

use App\Models\OkrKeyResult;
use App\Models\OkrPeriod;
use App\Models\OkrPlan;
use App\Models\OkrUnit;
use App\Models\OkrWeeklyReport;
use App\Models\OkrWeeklyReportItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

class CurriculumOkrDemoSeeder extends Seeder
{
    private const OKR_SOURCE = 'Okr Unit Kurikulum.xlsx';

    private const WEEKLY_SOURCE = 'FORM RAPAT PEKANAN M1-SEPTEMBER.xlsx';

    public function run(): void
    {
        $this->call(SchoolOkrObjectiveSeeder::class);

        $period = OkrPeriod::query()
            ->where('status', 'active')
            ->latest('id')
            ->first()
            ?? OkrPeriod::query()->latest('id')->first();

        if (! $period) {
            throw new RuntimeException('Periode OKR belum tersedia. Buat periode aktif terlebih dahulu.');
        }

        $okrRows = $this->readCurriculumOkrRows();
        $weeklySource = $this->readWeeklySource();

        DB::transaction(function () use ($period, $okrRows, $weeklySource) {
            $unit = OkrUnit::query()->firstOrCreate(
                ['code' => 'KURIKULUM'],
                [
                    'name' => 'Kurikulum',
                    'role_names' => ['Kurikulum', 'Kaprodi', 'Guru Kelas', 'Wali Kelas'],
                    'sort_order' => 1,
                    'is_active' => true,
                ]
            );

            if (! $unit->is_active) {
                $unit->update(['is_active' => true]);
            }

            $owner = User::query()
                ->whereHas('roles', fn ($query) => $query->where('name', 'Kurikulum'))
                ->oldest('id')
                ->first();

            $plans = $this->seedCurriculumPlans($period, $unit, $owner, $okrRows);
            $this->seedWeeklyReport($period, $unit, $owner, $plans, $weeklySource);
        });

        $objectiveCount = collect($okrRows)->pluck('objective_code')->unique()->count();
        $this->command?->info(
            "Demo Kurikulum selesai: {$objectiveCount} Objektif Unit, ".count($okrRows).
            ' Key Result Unit, dan 1 laporan pekanan menunggu tinjauan.'
        );
    }

    /**
     * @return array<string, OkrPlan>
     */
    private function seedCurriculumPlans(OkrPeriod $period, OkrUnit $unit, ?User $owner, array $rows): array
    {
        $schoolKeyResults = $this->schoolKeyResults($period);
        $plans = [];

        foreach (collect($rows)->groupBy('objective_code') as $objectiveCode => $objectiveRows) {
            $first = $objectiveRows->first();
            $schoolKeyResultCode = $this->schoolKeyResultCode((string) $objectiveCode);
            $schoolKeyResult = $schoolKeyResults[$schoolKeyResultCode] ?? null;

            if (! $schoolKeyResult) {
                throw new RuntimeException("Key Result Sekolah {$schoolKeyResultCode} tidak ditemukan.");
            }

            $annualTitle = $this->planTitle((string) $objectiveCode, $first['objective_title']);
            $annualPlan = OkrPlan::query()->firstOrNew([
                'okr_key_result_id' => $schoolKeyResult->id,
                'okr_unit_id' => $unit->id,
                'parent_id' => null,
                'level' => 'annual',
                'title' => $annualTitle,
            ]);
            $annualPlan->fill([
                'owner_id' => $owner?->id,
                'description' => "Turunan dari Objective Sekolah: {$first['school_objective']}",
                'starts_at' => $period->starts_at,
                'ends_at' => $period->ends_at,
                'target_value' => 100,
                'metric_unit' => '% KR tercapai',
                'weight' => 1,
                'success_indicator' => 'Seluruh Key Result Unit pada objektif ini tercapai sesuai target terukur.',
                'created_by' => $owner?->id,
            ]);

            if (! $annualPlan->exists) {
                $annualPlan->fill([
                    'current_value' => 0,
                    'progress_percent' => 0,
                    'status' => 'not_started',
                ]);
            }

            $annualPlan->save();

            $plans[(string) $objectiveCode] = $annualPlan;

            foreach ($objectiveRows as $row) {
                [$targetValue, $metricUnit] = $this->metricFromTarget($row['target']);
                $krTitle = $this->planTitle($row['kr_code'], $row['kr_title']);

                $child = OkrPlan::query()->firstOrNew([
                    'okr_key_result_id' => $schoolKeyResult->id,
                    'okr_unit_id' => $unit->id,
                    'parent_id' => $annualPlan->id,
                    'level' => 'monthly',
                    'title' => $krTitle,
                ]);
                $child->fill([
                    'owner_id' => $owner?->id,
                    'description' => $this->planDescription($row),
                    'starts_at' => $period->starts_at,
                    'ends_at' => $period->ends_at,
                    'target_value' => $targetValue,
                    'metric_unit' => $metricUnit,
                    'weight' => 1,
                    'success_indicator' => $row['target'],
                    'created_by' => $owner?->id,
                ]);

                if (! $child->exists) {
                    $child->fill([
                        'current_value' => 0,
                        'progress_percent' => 0,
                        'status' => 'not_started',
                    ]);
                }

                $child->save();

                $plans[$row['kr_code']] = $child;
            }
        }

        return $plans;
    }

    private function seedWeeklyReport(
        OkrPeriod $period,
        OkrUnit $unit,
        ?User $owner,
        array $plans,
        array $source
    ): void {
        $report = OkrWeeklyReport::query()
            ->where('okr_period_id', $period->id)
            ->where('okr_unit_id', $unit->id)
            ->whereDate('week_start', '2026-09-07')
            ->first();

        if (! $report) {
            $report = new OkrWeeklyReport([
                'okr_period_id' => $period->id,
                'okr_unit_id' => $unit->id,
                'week_start' => '2026-09-07',
            ]);
        }

        $report->week_end = '2026-09-11';
        $report->weekly_focus = $source['focus'];
        $report->support_needed = $source['dependencies'];

        if (! $report->exists) {
            $report->created_by = $owner?->id;
        }

        if ($report->status !== 'reviewed') {
            $report->status = 'submitted';
            $report->submitted_by = $owner?->id;
            $report->submitted_at = '2026-09-11 15:30:00';
        }

        $report->save();

        // Laporan yang sudah direview Kepala Sekolah tidak disentuh saat seeder dijalankan ulang.
        if ($report->status === 'reviewed') {
            return;
        }

        $items = [
            [
                'priority_order' => 1,
                'okr_plan_id' => ($plans['KR.12-1'] ?? $plans['O-13'] ?? null)?->id,
                'commitment' => $source['commitments'][0],
                'measurable_target' => $source['targets'][1]."\n".$source['targets'][2],
                'actual_result' => $source['actuals'][1]."\n".$source['actuals'][2],
                'completion_percent' => 65,
                'final_status' => 'on_progress',
                'blockers' => $source['blockers'][0]."\n".$source['blockers'][1],
                'next_follow_up' => $source['follow_ups'][1]."\n".$source['follow_ups'][2],
            ],
            [
                'priority_order' => 2,
                'okr_plan_id' => ($plans['KR.12-1'] ?? $plans['O-13'] ?? null)?->id,
                'commitment' => $source['commitments'][1],
                'measurable_target' => $source['targets'][0],
                'actual_result' => $source['actuals'][0]."\n".$source['actuals'][3],
                'completion_percent' => 75,
                'final_status' => 'on_progress',
                'blockers' => $source['blockers'][0],
                'next_follow_up' => $source['follow_ups'][0]."\n".$source['follow_ups'][3],
            ],
            [
                'priority_order' => 3,
                'okr_plan_id' => ($plans['KR.12-1'] ?? $plans['O-13'] ?? null)?->id,
                'commitment' => $source['commitments'][2],
                'measurable_target' => $source['targets'][3],
                'actual_result' => $source['actuals'][4],
                'completion_percent' => 40,
                'final_status' => 'on_progress',
                'blockers' => $source['blockers'][2],
                'next_follow_up' => $source['follow_ups'][4],
            ],
        ];

        foreach ($items as $item) {
            OkrWeeklyReportItem::query()->updateOrCreate(
                [
                    'okr_weekly_report_id' => $report->id,
                    'priority_order' => $item['priority_order'],
                ],
                $item + [
                    'cross_unit_dependencies' => $source['dependencies'],
                    'approval_needs' => null,
                ]
            );
        }
    }

    private function schoolKeyResults(OkrPeriod $period): array
    {
        return OkrKeyResult::query()
            ->whereHas('objective', fn ($query) => $query->where('okr_period_id', $period->id))
            ->get()
            ->keyBy('code')
            ->all();
    }

    private function schoolKeyResultCode(string $objectiveCode): string
    {
        return [
            'O-1' => 'KR 1.2',
            'O-2' => 'KR 1.3',
            'O-3' => 'KR 1.4',
            'O-4' => 'KR 1.3',
            'O-5' => 'KR 2.1',
            'O-6' => 'KR 2.3',
            'O-7' => 'KR 3.1',
            'O-8' => 'KR 3.2',
            'O-9' => 'KR 4.1',
            'O-10' => 'KR 4.1',
            'O-11' => 'KR 4.3',
            'O-12' => 'KR 1.1',
            'O-13' => 'KR 1.1',
            'O-14' => 'KR 3.1',
            'O-15' => 'KR 1.1',
            'O-16' => 'KR 1.1',
        ][$objectiveCode] ?? throw new RuntimeException("Mapping {$objectiveCode} ke KR Sekolah belum tersedia.");
    }

    private function readCurriculumOkrRows(): array
    {
        $file = $this->sourcePath(self::OKR_SOURCE);
        $sheet = IOFactory::load($file)->getSheetByName('KURIKULUM');

        if (! $sheet) {
            throw new RuntimeException("Sheet KURIKULUM tidak ditemukan di {$file}.");
        }

        $rows = [];
        $current = [];

        for ($rowNumber = 5; $rowNumber <= $sheet->getHighestDataRow(); $rowNumber++) {
            $values = [];
            foreach (range('A', 'L') as $column) {
                $values[$column] = $this->cellText($sheet->getCell("{$column}{$rowNumber}")->getValue());
            }

            foreach (['A' => 'school_objective', 'B' => 'objective_code', 'C' => 'objective_title'] as $column => $key) {
                if ($values[$column] !== '') {
                    $current[$key] = $values[$column];
                }
            }

            if ($values['D'] === '') {
                continue;
            }

            if (! isset($current['school_objective'], $current['objective_code'], $current['objective_title'])) {
                throw new RuntimeException("Struktur Objective Unit tidak lengkap pada baris {$rowNumber}.");
            }

            $rows[] = $current + [
                'kr_code' => $values['D'],
                'kr_title' => $values['E'],
                'target' => $values['F'],
                'difficulty' => $values['G'],
                'actions' => $values['H'],
                'pic' => $values['I'],
                'related_units' => $values['J'],
                'timeframe' => $values['K'],
                'notes' => $values['L'],
            ];
        }

        if (collect($rows)->pluck('objective_code')->unique()->count() !== 16 || count($rows) !== 41) {
            throw new RuntimeException('Sumber OKR Kurikulum harus berisi tepat 16 Objektif Unit dan 41 Key Result Unit.');
        }

        return $rows;
    }

    private function readWeeklySource(): array
    {
        $file = $this->sourcePath(self::WEEKLY_SOURCE);
        $spreadsheet = IOFactory::load($file);
        $monday = $spreadsheet->getSheet(0);
        $friday = $spreadsheet->getSheet(1);

        $mondayRow = $this->findUnitRow($monday, 'Kurikulum');
        $fridayRow = $this->findUnitRow($friday, 'Kurikulum');

        return [
            'focus' => $this->cellText($monday->getCell("B{$mondayRow}")->getValue()),
            'commitments' => $this->numberedItems($monday->getCell("C{$mondayRow}")->getValue(), 3),
            'targets' => $this->numberedItems($monday->getCell("D{$mondayRow}")->getValue(), 4),
            'dependencies' => $this->cellText($monday->getCell("E{$mondayRow}")->getValue()),
            'actuals' => $this->numberedItems($friday->getCell("C{$fridayRow}")->getValue(), 5),
            'blockers' => $this->numberedItems($friday->getCell("E{$fridayRow}")->getValue(), 3),
            'follow_ups' => $this->numberedItems($friday->getCell("F{$fridayRow}")->getValue(), 5),
        ];
    }

    private function findUnitRow($sheet, string $needle): int
    {
        for ($row = 1; $row <= $sheet->getHighestDataRow(); $row++) {
            if (Str::contains(Str::lower($this->cellText($sheet->getCell("A{$row}")->getValue())), Str::lower($needle))) {
                return $row;
            }
        }

        throw new RuntimeException("Baris unit {$needle} tidak ditemukan pada sheet {$sheet->getTitle()}.");
    }

    private function numberedItems(mixed $value, int $expected): array
    {
        $text = $this->cellText($value);
        preg_match_all('/(?:^|\R)\s*\d+\.\s*(.*?)(?=(?:\R\s*\d+\.)|$)/su', $text, $matches);
        $items = array_values(array_map(fn ($item) => trim($item), $matches[1] ?? []));

        if (count($items) < $expected) {
            throw new RuntimeException("Format daftar bernomor pada laporan pekanan berubah; dibutuhkan {$expected} butir.");
        }

        return $items;
    }

    private function planDescription(array $row): string
    {
        return collect([
            'Tingkat kesulitan' => $row['difficulty'],
            'Strategi / action plan' => $row['actions'],
            'PIC' => $row['pic'],
            'Unit terkait' => $row['related_units'],
            'Rentang waktu' => $row['timeframe'],
            'Catatan' => $row['notes'],
        ])->reject(fn ($value) => $value === '' || $value === '-')
            ->map(fn ($value, $label) => "{$label}: {$value}")
            ->implode("\n\n");
    }

    private function metricFromTarget(string $target): array
    {
        if (preg_match('/(\d+(?:[,.]\d+)?)\s*%/u', $target, $match)) {
            return [(float) str_replace(',', '.', $match[1]), '%'];
        }

        if (preg_match('/(\d+(?:[,.]\d+)?)\s+dari\s+(?:skala\s+)?\d+/iu', $target, $match)) {
            return [(float) str_replace(',', '.', $match[1]), 'skor'];
        }

        preg_match('/(\d+(?:[,.]\d+)?)/u', $target, $match);
        $value = isset($match[1]) ? (float) str_replace(',', '.', $match[1]) : 100;
        $lower = Str::lower($target);
        $unit = match (true) {
            Str::contains($lower, 'jam pelajaran') => 'JP',
            Str::contains($lower, 'gelar') => 'gelar',
            Str::contains($lower, 'proposal') => 'proposal',
            Str::contains($lower, 'produk') => 'produk',
            Str::contains($lower, 'tim ') => 'tim',
            Str::contains($lower, 'siswa') => 'siswa',
            Str::contains($lower, 'lulusan') => 'lulusan',
            Str::contains($lower, 'nilai') => 'nilai',
            default => 'target',
        };

        return [$value, $unit];
    }

    private function planTitle(string $code, string $title): string
    {
        return Str::limit("{$code} · {$title}", 255, '');
    }

    private function cellText(mixed $value): string
    {
        return trim(preg_replace('/\R/u', "\n", (string) ($value ?? '')) ?? '');
    }

    private function sourcePath(string $filename): string
    {
        $path = base_path($filename);

        if (! is_file($path)) {
            throw new RuntimeException("File sumber {$filename} tidak ditemukan di root project.");
        }

        return $path;
    }
}
