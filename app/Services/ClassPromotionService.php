<?php

namespace App\Services;

use App\Models\ClassPromotion;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClassPromotionService
{
    public function preview(TahunPelajaran $source, TahunPelajaran $target): array
    {
        $this->validatePeriod($source, $target);

        $sourceRombels = Rombel::with(['kelas', 'waliKelas', 'siswa' => fn ($query) => $query->where('status', 'aktif')])
            ->where('tahun_pelajaran_id', $source->id)
            ->orderBy('kelas_id')
            ->get();

        $classes = Kelas::orderBy('nama_kelas')->get();
        $classByName = $classes->keyBy(fn (Kelas $kelas) => mb_strtolower(trim($kelas->nama_kelas)));
        $targetRombels = Rombel::where('tahun_pelajaran_id', $target->id)->get()->keyBy('kelas_id');
        $rows = collect();
        $errors = collect();

        if ($sourceRombels->isEmpty()) {
            $errors->push('Belum ada rombel pada semester sumber.');
        }

        foreach ($sourceRombels as $rombel) {
            $level = $this->classLevel($rombel->kelas?->nama_kelas);

            if ($level === null) {
                $errors->push("Tingkat kelas {$rombel->kelas?->nama_kelas} tidak dikenali.");
                continue;
            }

            if ($level === 12) {
                $rows->push([
                    'source' => $rombel,
                    'target_class' => null,
                    'target_rombel' => null,
                    'action' => 'Lulus / Alumni',
                    'student_count' => $rombel->siswa->count(),
                ]);
                continue;
            }

            $targetName = $this->promotedClassName($rombel->kelas->nama_kelas);
            $targetClass = $classByName->get(mb_strtolower($targetName));

            if (!$targetClass) {
                $errors->push("Data kelas tujuan {$targetName} belum tersedia di Master Data Kelas.");
                continue;
            }

            $rows->push([
                'source' => $rombel,
                'target_class' => $targetClass,
                'target_rombel' => $targetRombels->get($targetClass->id),
                'action' => "Naik ke {$targetClass->nama_kelas}",
                'student_count' => $rombel->siswa->count(),
            ]);
        }

        $duplicateSourceClasses = $sourceRombels->groupBy('kelas_id')->filter(fn (Collection $items) => $items->count() > 1);
        foreach ($duplicateSourceClasses as $items) {
            $errors->push("Terdapat lebih dari satu rombel {$items->first()->kelas?->nama_kelas} pada semester sumber.");
        }

        $duplicateTargetClasses = Rombel::with('kelas')
            ->where('tahun_pelajaran_id', $target->id)
            ->get()
            ->groupBy('kelas_id')
            ->filter(fn (Collection $items) => $items->count() > 1);
        foreach ($duplicateTargetClasses as $items) {
            $errors->push("Terdapat lebih dari satu rombel {$items->first()->kelas?->nama_kelas} pada semester tujuan.");
        }

        $alreadyProcessed = ClassPromotion::where('source_tahun_pelajaran_id', $source->id)
            ->where('target_tahun_pelajaran_id', $target->id)
            ->first();

        return [
            'rows' => $rows,
            'errors' => $errors->unique()->values(),
            'already_processed' => $alreadyProcessed,
            'promoted_count' => $rows->whereNotNull('target_class')->sum('student_count'),
            'graduated_count' => $rows->whereNull('target_class')->sum('student_count'),
            'target_rombel_count' => $classes->count(),
            'rombel_to_create_count' => $classes->whereNotIn('id', $targetRombels->keys())->count(),
        ];
    }

    public function process(TahunPelajaran $source, TahunPelajaran $target, User $processor): ClassPromotion
    {
        $preview = $this->preview($source, $target);

        if ($preview['already_processed']) {
            throw ValidationException::withMessages([
                'source_tahun_pelajaran_id' => 'Kenaikan kelas untuk pasangan semester ini sudah pernah diproses.',
            ]);
        }

        if ($preview['errors']->isNotEmpty()) {
            throw ValidationException::withMessages([
                'source_tahun_pelajaran_id' => $preview['errors']->all(),
            ]);
        }

        return DB::transaction(function () use ($source, $target, $processor, $preview) {
            $targetRombels = $this->ensureTargetRombels($source, $target);
            $promoted = 0;
            $graduated = 0;
            $movements = [];

            foreach ($preview['rows'] as $row) {
                /** @var Rombel $sourceRombel */
                $sourceRombel = Rombel::with(['kelas', 'siswa.dapodik'])
                    ->lockForUpdate()
                    ->findOrFail($row['source']->id);

                if ($row['target_class'] === null) {
                    $activeStudents = $sourceRombel->siswa->where('status', 'aktif');
                    foreach ($activeStudents as $student) {
                        $student->update([
                            'status' => 'alumni',
                            'graduated_at' => now()->toDateString(),
                            'graduation_tahun_pelajaran_id' => $source->id,
                        ]);
                        $student->dapodik?->update(['rombel_saat_ini' => 'Lulus']);
                        $student->rombels()->detach($targetRombels->pluck('id')->all());
                        $graduated++;
                    }

                    $movements[] = [
                        'from' => $sourceRombel->kelas->nama_kelas,
                        'to' => 'Alumni',
                        'count' => $activeStudents->count(),
                    ];
                    continue;
                }

                $targetRombel = $targetRombels->get($row['target_class']->id);
                $activeStudents = $sourceRombel->siswa->where('status', 'aktif');
                foreach ($activeStudents as $student) {
                    $otherTargetRombel = $student->rombels()
                        ->where('tahun_pelajaran_id', $target->id)
                        ->where('rombels.id', '!=', $targetRombel->id)
                        ->first();

                    if ($otherTargetRombel) {
                        throw ValidationException::withMessages([
                            'source_tahun_pelajaran_id' => "{$student->nama_lengkap} sudah terdaftar pada rombel lain di semester tujuan.",
                        ]);
                    }

                    $targetRombel->siswa()->syncWithoutDetaching([$student->id]);
                    $student->dapodik?->update(['rombel_saat_ini' => $targetRombel->kelas->nama_kelas]);
                    $promoted++;
                }

                $movements[] = [
                    'from' => $sourceRombel->kelas->nama_kelas,
                    'to' => $targetRombel->kelas->nama_kelas,
                    'count' => $activeStudents->count(),
                ];
            }

            return ClassPromotion::create([
                'source_tahun_pelajaran_id' => $source->id,
                'target_tahun_pelajaran_id' => $target->id,
                'processed_by' => $processor->id,
                'promoted_count' => $promoted,
                'graduated_count' => $graduated,
                'created_rombel_count' => $preview['rombel_to_create_count'],
                'summary' => ['movements' => $movements],
            ]);
        });
    }

    public function classLevel(?string $className): ?int
    {
        if (!$className || !preg_match('/^\s*(XII|XI|X)(?=\s|[-_.]|$)/i', $className, $matches)) {
            return null;
        }

        return match (strtoupper($matches[1])) {
            'X' => 10,
            'XI' => 11,
            'XII' => 12,
        };
    }

    public function promotedClassName(string $className): string
    {
        $level = $this->classLevel($className);
        $replacement = match ($level) {
            10 => 'XI',
            11 => 'XII',
            default => throw ValidationException::withMessages(['kelas' => 'Kelas XII tidak memiliki kelas tujuan.']),
        };

        return preg_replace('/^\s*(XII|XI|X)(?=\s|[-_.]|$)/i', $replacement, trim($className), 1);
    }

    private function validatePeriod(TahunPelajaran $source, TahunPelajaran $target): void
    {
        $errors = [];

        if ($source->id === $target->id) {
            $errors[] = 'Semester sumber dan tujuan harus berbeda.';
        }
        if (strcasecmp($source->semester, 'Genap') !== 0) {
            $errors[] = 'Kenaikan kelas hanya dapat bersumber dari semester Genap.';
        }
        if (strcasecmp($target->semester, 'Ganjil') !== 0) {
            $errors[] = 'Semester tujuan kenaikan kelas harus semester Ganjil.';
        }
        if (!$target->is_active) {
            $errors[] = 'Semester tujuan harus merupakan tahun pelajaran yang sedang aktif.';
        }
        if ($this->endYear($source->tahun) !== $this->startYear($target->tahun)) {
            $errors[] = 'Tahun pelajaran sumber dan tujuan tidak berurutan.';
        }

        if ($errors) {
            throw ValidationException::withMessages(['source_tahun_pelajaran_id' => $errors]);
        }
    }

    private function ensureTargetRombels(TahunPelajaran $source, TahunPelajaran $target): Collection
    {
        $sourceByClass = Rombel::where('tahun_pelajaran_id', $source->id)->get()->keyBy('kelas_id');

        foreach (Kelas::all() as $kelas) {
            Rombel::firstOrCreate(
                ['tahun_pelajaran_id' => $target->id, 'kelas_id' => $kelas->id],
                [
                    'tahun_ajaran' => $target->tahun,
                    'wali_kelas_id' => $sourceByClass->get($kelas->id)?->wali_kelas_id
                        ?? $sourceByClass->first()?->wali_kelas_id,
                ]
            );
        }

        return Rombel::with('kelas')->where('tahun_pelajaran_id', $target->id)->get()->keyBy('kelas_id');
    }

    private function startYear(string $academicYear): ?int
    {
        return preg_match('/^(\d{4})\/(\d{4})$/', trim($academicYear), $matches) ? (int) $matches[1] : null;
    }

    private function endYear(string $academicYear): ?int
    {
        return preg_match('/^(\d{4})\/(\d{4})$/', trim($academicYear), $matches) ? (int) $matches[2] : null;
    }
}
