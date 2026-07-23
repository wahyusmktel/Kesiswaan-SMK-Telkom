<?php

namespace App\Imports;

use App\Models\WorkCalendarEvent;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

class WorkCalendarEventsImport implements SkipsEmptyRows, ToCollection, WithStartRow
{
    public int $created = 0;

    public int $updated = 0;

    public int $skipped = 0;

    public function startRow(): int
    {
        return 6;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + $this->startRow();
            $dateFrom = $this->parseDate($row[1] ?? null);
            $dateTo = $this->parseDate($row[2] ?? null);
            $title = trim((string) ($row[3] ?? ''));
            $rawType = trim((string) ($row[4] ?? ''));

            if (! $dateFrom && ! $dateTo && $title === '' && $rawType === '') {
                $this->skipped++;

                continue;
            }

            $errors = [];
            if (! $dateFrom) {
                $errors[] = 'Tanggal Mulai tidak valid';
            }
            if (! $dateTo) {
                $errors[] = 'Tanggal Selesai tidak valid';
            }
            if ($title === '') {
                $errors[] = 'Nama Kegiatan kosong';
            }
            if ($rawType === '') {
                $errors[] = 'Jenis Kegiatan kosong';
            }
            if ($dateFrom && $dateTo && $dateTo->lt($dateFrom)) {
                $errors[] = 'Tanggal Selesai lebih awal dari Tanggal Mulai';
            }

            if ($errors !== []) {
                throw ValidationException::withMessages([
                    'agenda_file' => 'Baris '.$rowNumber.': '.implode(', ', $errors).'.',
                ]);
            }

            $type = WorkCalendarEvent::normalizeType($rawType);
            $event = WorkCalendarEvent::firstOrNew([
                'date_from' => $dateFrom->toDateString(),
                'date_to' => $dateTo->toDateString(),
                'title' => $title,
            ]);
            $isNew = ! $event->exists;

            $event->fill([
                'type' => $type,
                'is_non_working' => WorkCalendarEvent::typeIsNonWorking($type),
            ])->save();

            $isNew ? $this->created++ : $this->updated++;
        }
    }

    public function summary(): array
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
        ];
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->startOfDay();
            }

            return Carbon::parse((string) $value)->startOfDay();
        } catch (Throwable) {
            return null;
        }
    }
}
