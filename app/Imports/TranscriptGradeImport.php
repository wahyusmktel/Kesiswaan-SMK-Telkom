<?php

namespace App\Imports;

use App\Models\TranscriptGrade;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;

class TranscriptGradeImport implements SkipsEmptyRows, ToCollection
{
    public int $created = 0;

    public int $updated = 0;

    public int $skipped = 0;

    public int $scores = 0;

    public array $messages = [];

    public function __construct(private Collection $students, private Collection $subjects)
    {
        $this->students = $this->students->loadMissing(['dapodik']);
    }

    public function collection(Collection $rows): void
    {
        $headings = $rows->first();

        if (! $headings) {
            $this->skipped++;
            $this->messages[] = 'File kosong atau header tidak ditemukan.';
            return;
        }

        $subjectColumns = $this->subjectColumns($headings);

        if (empty($subjectColumns)) {
            $this->skipped++;
            $this->messages[] = 'Tidak ada kolom mapel yang cocok dengan Mapel Transkrip aktif.';
            return;
        }

        foreach ($rows->skip(1) as $rowIndex => $row) {
            $student = $this->findStudent((string) ($row[1] ?? ''), (string) ($row[3] ?? ''));

            if (! $student) {
                $this->skipped++;
                $this->messages[] = 'Baris ' . ($rowIndex + 1) . ' dilewati: siswa tidak ditemukan di rombel terpilih.';
                continue;
            }

            foreach ($subjectColumns as $columnIndex => $subject) {
                $rawScore = $row[$columnIndex] ?? null;

                if ($rawScore === null || trim((string) $rawScore) === '') {
                    continue;
                }

                $score = $this->normalizeScore($rawScore);

                if ($score === null) {
                    $this->skipped++;
                    $this->messages[] = 'Baris ' . ($rowIndex + 1) . ' kolom ' . $subject->name . ' dilewati: format nilai tidak valid.';
                    continue;
                }

                $existing = TranscriptGrade::where('master_siswa_id', $student->id)
                    ->where('transcript_subject_id', $subject->id)
                    ->first();

                TranscriptGrade::updateOrCreate(
                    [
                        'master_siswa_id' => $student->id,
                        'transcript_subject_id' => $subject->id,
                    ],
                    ['score' => $score]
                );

                $existing ? $this->updated++ : $this->created++;
                $this->scores++;
            }
        }
    }

    public function summary(): array
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'scores' => $this->scores,
            'skipped' => $this->skipped,
            'messages' => array_slice($this->messages, 0, 10),
        ];
    }

    private function subjectColumns(Collection $headings): array
    {
        $subjects = $this->subjects->keyBy(fn ($subject) => $this->normalizeHeading($subject->name));
        $columns = [];

        foreach ($headings as $index => $heading) {
            $key = $this->normalizeHeading((string) $heading);

            if ($subjects->has($key)) {
                $columns[$index] = $subjects->get($key);
            }
        }

        return $columns;
    }

    private function findStudent(string $name, string $nisn)
    {
        $name = trim($name);
        $nisn = trim($nisn);

        return $this->students->first(function ($student) use ($name, $nisn) {
            if ($nisn !== '' && $student->dapodik?->nisn === $nisn) {
                return true;
            }

            return Str::lower(trim($student->nama_lengkap)) === Str::lower($name);
        });
    }

    private function normalizeScore(mixed $value): ?string
    {
        $score = str_replace(',', '.', trim((string) $value));

        if (! is_numeric($score) || (float) $score < 0 || (float) $score > 100) {
            return null;
        }

        return number_format((float) $score, 2, '.', '');
    }

    private function normalizeHeading(string $heading): string
    {
        return Str::of($heading)->lower()->replaceMatches('/[^a-z0-9]+/', '_')->trim('_')->toString();
    }
}
