<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PegawaiTemplateExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithColumnFormatting, WithEvents
{
    use Exportable;

    public function collection()
    {
        return User::with(['roles', 'masterGuru'])
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'Siswa'))
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'user_id',
            'Nama Lengkap',
            'Email',
            'Role / Jabatan',
            'NIK',
            'NUPTK',
            'Kode Guru',
            'Jenis Kelamin (L/P)',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->roles->first()?->name ?? '',
            $user->masterGuru?->nik ?? '',
            $user->masterGuru?->nuptk ?? '',
            $user->masterGuru?->kode_guru ?? '',
            $user->masterGuru?->jenis_kelamin ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 30,
            'C' => 30,
            'D' => 20,
            'E' => 22,
            'F' => 22,
            'G' => 15,
            'H' => 20,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    foreach (['E', 'F'] as $col) {
                        $cell  = $sheet->getCell($col . $row);
                        $value = (string) ($cell->getValue() ?? '');
                        $cell->setValueExplicit($value, DataType::TYPE_STRING);
                    }
                }
            },
        ];
    }
}
