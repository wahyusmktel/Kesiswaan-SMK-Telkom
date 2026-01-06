<?php

namespace App\Exports\Sheets;

use App\Models\AbsensiGuru;
use App\Models\MasterGuru;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TeacherAnalysisSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $tahunPelajaranId;

    public function __construct($tahunPelajaranId)
    {
        $this->tahunPelajaranId = $tahunPelajaranId;
    }

    public function collection()
    {
        $gurus = MasterGuru::all();
        $data = [];

        foreach ($gurus as $index => $guru) {
            $absensi = AbsensiGuru::whereHas('jadwalPelajaran', function($q) use ($guru) {
                $q->where('master_guru_id', $guru->id);
            })->whereHas('jadwalPelajaran.rombel', function($q) {
                $q->where('tahun_pelajaran_id', $this->tahunPelajaranId);
            })->get();

            $total = $absensi->count();
            if ($total == 0) continue;

            $hadir = $absensi->where('status', 'hadir')->count();
            $terlambat = $absensi->where('status', 'terlambat')->count();
            $izin = $absensi->where('status', 'izin')->count();
            $alpa = $absensi->where('status', 'tidak_hadir')->count();

            $data[] = [
                $index + 1,
                $guru->nama_lengkap,
                $total,
                $hadir,
                $terlambat,
                $izin,
                $alpa,
                round(($hadir / $total) * 100, 1) . '%'
            ];
        }

        return collect($data);
    }

    public function title(): string
    {
        return 'Analisa Per Guru';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Guru',
            'Total Jadwal',
            'Tepat Waktu',
            'Terlambat',
            'Izin',
            'Alpa',
            'Persentase Kehadiran'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        if ($lastRow > 1) {
            $sheet->getStyle("A1:H{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
        }

        return [];
    }
}
