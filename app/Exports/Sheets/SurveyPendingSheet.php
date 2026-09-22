<?php

namespace App\Exports\Sheets;

use App\Models\Survey;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SurveyPendingSheet implements FromCollection, WithTitle, WithStyles, ShouldAutoSize
{
    protected Survey $survey;

    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
    }

    public function title(): string
    {
        return 'Daftar Belum Mengisi';
    }

    public function collection()
    {
        $rows = collect();

        // Get target users and submitted response user IDs
        $responseUserIds = $this->survey->responses()->pluck('user_id')->all();

        $targetUsers = $this->survey->targets()
            ->with(['masterSiswa.rombels.kelas', 'roles'])
            ->get();

        $pendingUsers = $targetUsers->filter(fn ($u) => !in_array($u->id, $responseUserIds))->values();

        // Banner info
        $rows->push(['DAFTAR PESERTA YANG BELUM MENGISI SURVEI']);
        $rows->push(['Survei: ' . $this->survey->title . ' | Belum Mengisi: ' . $pendingUsers->count() . ' orang dari ' . $targetUsers->count() . ' target']);
        $rows->push(['']);

        // Headers
        $headers = [
            'No',
            'Nama Lengkap',
            'NIS',
            'Kelas / Unit',
            'Peran',
            'Email',
            'Status',
        ];
        $rows->push($headers);

        $no = 1;
        foreach ($pendingUsers as $user) {
            $isStudent = $user->hasRole('Siswa') || $user->masterSiswa !== null;
            $nis = $user->masterSiswa?->nis ?? '-';
            $rombel = $user->masterSiswa?->rombels->first();
            $kelas = $rombel?->kelas?->nama_kelas ?? ($isStudent ? 'Siswa' : ($user->roles->pluck('name')->first() ?? 'Pengguna'));
            $roles = $user->roles->pluck('name')->implode(', ') ?: 'Pengguna';

            $rows->push([
                $no++,
                $user->name,
                $nis,
                $kelas,
                $roles,
                $user->email ?: '-',
                'Belum Mengisi',
            ]);
        }

        if ($pendingUsers->isEmpty()) {
            $rows->push([
                '1',
                'Semua target peserta telah mengisi survei ini.',
                '-',
                '-',
                '-',
                '-',
                'Lengkap 100%',
            ]);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $headerRow = 4;

        // Title styling
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => '991B1B']],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => false, 'size' => 10, 'color' => ['rgb' => '64748B']],
        ]);

        // Table Header Styling
        $sheet->getStyle("A{$headerRow}:G{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '991B1B']], // Deep Red / Warning
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '7F1D1D']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(26);

        // Data Rows Styling
        if ($lastRow > $headerRow) {
            $sheet->getStyle("A5:G{$lastRow}")->applyFromArray([
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);

            $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C5:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G5:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Highlight status badge
            for ($r = 5; $r <= $lastRow; $r++) {
                $statusVal = (string) $sheet->getCell("G{$r}")->getValue();
                $sheet->getRowDimension($r)->setRowHeight(22);

                if ($statusVal === 'Belum Mengisi') {
                    $sheet->getStyle("G{$r}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '92400E'], 'size' => 9],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']], // Soft Amber
                    ]);
                } elseif ($statusVal === 'Lengkap 100%') {
                    $sheet->getStyle("G{$r}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '065F46'], 'size' => 9],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
                    ]);
                }
            }
        }

        $sheet->freezePane('A5');
        $sheet->setAutoFilter("A{$headerRow}:G{$lastRow}");

        return [];
    }
}
