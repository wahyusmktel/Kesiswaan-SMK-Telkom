<?php

namespace App\Exports\Sheets;

use App\Models\AbsensiGuru;
use App\Models\TahunPelajaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SemesterSummarySheet implements FromCollection, WithTitle, WithHeadings, WithStyles
{
    protected $tahunPelajaranId;

    public function __construct($tahunPelajaranId)
    {
        $this->tahunPelajaranId = $tahunPelajaranId;
    }

    public function collection()
    {
        $absensi = AbsensiGuru::whereHas('jadwalPelajaran.rombel', function($q) {
            $q->where('tahun_pelajaran_id', $this->tahunPelajaranId);
        })->get();

        $total = $absensi->count();
        $hadir = $absensi->where('status', 'hadir')->count();
        $terlambat = $absensi->where('status', 'terlambat')->count();
        $izin = $absensi->where('status', 'izin')->count();
        $alpa = $absensi->where('status', 'tidak_hadir')->count();

        return collect([
            ['Metrik', 'Jumlah', 'Persentase'],
            ['Total Record', $total, $total > 0 ? '100%' : '0%'],
            ['Hadir Tepat Waktu', $hadir, $total > 0 ? round(($hadir/$total)*100, 2).'%' : '0%'],
            ['Terlambat', $terlambat, $total > 0 ? round(($terlambat/$total)*100, 2).'%' : '0%'],
            ['Izin/Sakit', $izin, $total > 0 ? round(($izin/$total)*100, 2).'%' : '0%'],
            ['Alpa/Tanpa Keterangan', $alpa, $total > 0 ? round(($alpa/$total)*100, 2).'%' : '0%'],
        ]);
    }

    public function title(): string
    {
        return 'Ringkasan Eksekutif';
    }

    public function headings(): array
    {
        $tp = TahunPelajaran::find($this->tahunPelajaranId);
        return [
            ['LAPORAN AUDIT KEHADIRAN GURU'],
            ['TAHUN PELAJARAN: ' . ($tp->tahun_ajaran ?? '-') . ' (' . ($tp->semester ?? '-') . ')'],
            [''],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        
        $sheet->getStyle('A1:A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->getStyle('A4:C4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF']
            ]
        ]);

        $sheet->getStyle('A4:C9')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        return [];
    }
}
