<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $role;
    protected $rowNumber = 0;

    public function __construct($role = null)
    {
        $this->role = $role;
    }

    public function collection()
    {
        $query = User::with('roles')->latest();

        if ($this->role) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->role);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'Email',
            'Role (Peran)',
            'Tanggal Dibuat',
        ];
    }

    public function map($user): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $user->name,
            $user->email,
            $user->getRoleNames()->implode(', ') ?: 'Tidak ada peran',
            $user->created_at->format('d/m/Y H:i'),
        ];
    }

    public function title(): string
    {
        return $this->role ? "Daftar Pengguna - {$this->role}" : 'Daftar Semua Pengguna';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 30,
            'C' => 35,
            'D' => 25,
            'E' => 18,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Header Styling
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC2626'], // Red-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Data rows styling
        if ($lastRow > 1) {
            $sheet->getStyle("A2:E{$lastRow}")->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);

            // Center align No column
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Alternate row colors for better readability
            for ($row = 2; $row <= $lastRow; $row++) {
                if ($row % 2 == 0) {
                    $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FEF2F2'], // Red-50
                        ],
                    ]);
                }
            }
        }

        // Add title row above header
        $sheet->insertNewRowBefore(1, 2);

        $roleText = $this->role ? "Role: {$this->role}" : "Semua Role";
        $sheet->setCellValue('A1', 'DAFTAR PENGGUNA SISTEM');
        $sheet->setCellValue('A2', "Filter: {$roleText} | Diekspor: " . now()->format('d/m/Y H:i'));

        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'DC2626'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => '6B7280'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);

        return [];
    }
}
