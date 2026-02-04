<?php

namespace Database\Seeders;

use App\Models\TuLetterCode;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TuLetterCodeSeeder extends Seeder
{
    public function run(): void
    {
        $file = base_path('assets/SURAT DOKUMEN SMK TELKOM LAMPUNG.xlsx');

        if (!file_exists($file)) {
            $this->command->error("Excel file not found at $file");
            return;
        }

        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getSheetByName('KODE SURAT');

        if (!$sheet) {
            $this->command->error("Sheet 'KODE SURAT' not found.");
            return;
        }

        $data = $sheet->toArray(null, true, true, true);

        $currentUnit = null;

        foreach ($data as $index => $row) {
            // Skip header (row 1)
            if ($index == 1)
                continue;

            $unit = $row['A'];
            $code = $row['B'];
            $desc = $row['C'];

            // Handle merged units logic (if A is null, use currentUnit)
            if (!empty($unit)) {
                $currentUnit = $unit;
            }

            if (!empty($code) && !empty($desc)) {
                TuLetterCode::updateOrCreate(
                    ['code' => $code],
                    [
                        'unit' => $currentUnit,
                        'description' => $desc
                    ]
                );
            }
        }
    }
}
