<?php

namespace App\Imports;

use App\Models\MasterSiswa;
use App\Models\TranscriptDiplomaNumber;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TranscriptDiplomaNumberImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public int $created = 0;
    public int $updated = 0;
    public int $skipped = 0;
    public array $messages = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $name = trim((string) ($row['nama_siswa'] ?? $row['nama siswa'] ?? ''));
            $className = trim((string) ($row['kelas'] ?? ''));
            $diplomaNumber = trim((string) ($row['nomor_ijazah'] ?? $row['nomor ijazah'] ?? ''));

            if ($name === '' || $className === '' || $diplomaNumber === '') {
                $this->skipped++;
                continue;
            }

            $student = MasterSiswa::where('nama_lengkap', $name)
                ->whereHas('rombels.kelas', fn ($query) => $query->where('nama_kelas', $className))
                ->first();

            if (!$student) {
                $this->skipped++;
                $this->messages[] = 'Baris ' . ($index + 2) . ': siswa tidak ditemukan (' . $name . ' - ' . $className . ')';
                continue;
            }

            $record = TranscriptDiplomaNumber::firstOrNew(['master_siswa_id' => $student->id]);
            $record->diploma_number = $diplomaNumber;
            $record->exists ? $this->updated++ : $this->created++;
            $record->save();
        }
    }

    public function summary(): array
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
            'messages' => array_slice($this->messages, 0, 10),
        ];
    }
}
