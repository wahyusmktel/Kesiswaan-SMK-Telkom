<?php

namespace App\Exports;

use App\Exports\Sheets\UkkDetailInstrumenSheet;
use App\Exports\Sheets\UkkRingkasanNilaiSheet;
use App\Models\UkkUjian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekapNilaiUkkExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        private readonly UkkUjian $ujian,
        private readonly Collection $siswas,
        private readonly Collection $rekap,
        private readonly Collection $allNilaiP,
        private readonly Collection $allNilaiK,
        private readonly ?string $rombelLabel = null,
        private readonly ?Collection $allNilaiBilingual = null
    ) {}

    public function sheets(): array
    {
        $sheets = [];
        $rombelText = $this->rombelLabel ?: 'Semua Kelas';
        $instrumens = $this->ujian->instrumens;

        if ($instrumens->count() <= 1) {
            $ins = $instrumens->first();
            if ($ins) {
                $sheets[] = new UkkDetailInstrumenSheet(
                    $this->ujian,
                    $ins,
                    $this->siswas,
                    $this->allNilaiP,
                    $this->allNilaiK,
                    $rombelText,
                    'Detail Nilai & Rekap UKK',
                    $this->allNilaiBilingual
                );
            }
            $sheets[] = new UkkRingkasanNilaiSheet(
                $this->ujian,
                $this->rekap,
                $rombelText,
                'Ringkasan Nilai Akhir',
                $this->allNilaiBilingual
            );
        } else {
            // Multi-instrument: summary first, then detailed sheets
            $sheets[] = new UkkRingkasanNilaiSheet(
                $this->ujian,
                $this->rekap,
                $rombelText,
                'Ringkasan Nilai Akhir',
                $this->allNilaiBilingual
            );

            foreach ($instrumens as $idx => $ins) {
                $sheets[] = new UkkDetailInstrumenSheet(
                    $this->ujian,
                    $ins,
                    $this->siswas,
                    $this->allNilaiP,
                    $this->allNilaiK,
                    $rombelText,
                    'Detail - ' . mb_substr($ins->nama_instrumen, 0, 20),
                    $this->allNilaiBilingual
                );
            }
        }

        return $sheets;
    }
}
