<?php

namespace App\Imports;

use App\Models\TranscriptGrade;
use App\Models\TranscriptSubject;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;

class TranscriptGradeImport implements SkipsEmptyRows, ToCollection, WithCalculatedFormulas
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
        if ($this->isOfficialCurriculumFormat($rows)) {
            $this->importOfficialCurriculumFormat($rows);
            return;
        }

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

    private function isOfficialCurriculumFormat(Collection $rows): bool
    {
        $titleRow = collect($rows->get(3, []));
        $subjectRow = collect($rows->get(4, []));
        $firstDataRow = collect($rows->get(5, []));

        return Str::contains(Str::upper((string) ($titleRow[36] ?? '')), 'NILAI TRANSKRIP')
            && trim((string) ($subjectRow[36] ?? '')) !== ''
            && trim((string) ($firstDataRow[1] ?? '')) !== '';
    }

    private function importOfficialCurriculumFormat(Collection $rows): void
    {
        $subjectColumns = $this->officialSubjectColumns(collect($rows->get(4, [])));

        if (empty($subjectColumns)) {
            $this->skipped++;
            $this->messages[] = 'Kolom AK sampai BA tidak cocok dengan Mapel Transkrip aktif.';
            return;
        }

        foreach ($rows->slice(5) as $rowIndex => $row) {
            $row = collect($row);
            $name = trim((string) ($row[1] ?? ''));
            $nisn = trim((string) ($row[2] ?? ''));

            if ($name === '' && $nisn === '') {
                continue;
            }

            $student = $this->findStudent($name, $nisn);

            if (! $student) {
                $this->skipped++;
                $this->messages[] = 'Baris ' . ($rowIndex + 1) . " dilewati: {$name} / {$nisn} tidak ditemukan di rombel terpilih.";
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

    private function officialSubjectColumns(Collection $subjectRow): array
    {
        $subjects = $this->subjects->keyBy(fn ($subject) => $this->normalizeHeading($subject->name));
        $columns = [];

        for ($index = 36; $index <= 52; $index++) {
            $subjectName = trim((string) ($subjectRow[$index] ?? ''));

            if ($subjectName === '') {
                continue;
            }

            $key = $this->normalizeHeading($subjectName);
            $subject = $subjects->get($key);

            if (! $subject) {
                $subject = TranscriptSubject::firstOrCreate(
                    ['name' => $subjectName, 'group' => $this->inferGroup($subjectName)],
                    [
                        'sort_order' => $index - 35,
                        'is_active' => true,
                    ]
                );

                $this->subjects->push($subject);
                $subjects->put($this->normalizeHeading($subject->name), $subject);
            }

            $columns[$index] = $subject;
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
        return Str::of($heading)
            ->lower()
            ->replace('paktik', 'praktik')
            ->replace('praktek', 'praktik')
            ->replace('dan', '')
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();
    }

    private function inferGroup(string $subjectName): string
    {
        $name = $this->normalizeHeading($subjectName);

        if (Str::contains($name, ['lampung', 'anti_korupsi'])) {
            return 'muatan_lokal';
        }

        if (Str::contains($name, ['informatika', 'ipas', 'program_keahlian', 'kompetensi_keahlian', 'kewirausahaan', 'pilihan', 'praktik_kerja_lapangan'])) {
            return 'kejuruan';
        }

        return 'umum';
    }
}
