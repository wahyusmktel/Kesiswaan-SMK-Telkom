<?php

namespace App\Imports;

use App\Models\DapodikGuru;
use App\Models\MasterGuru;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DapodikGuruImport implements ToCollection, WithStartRow, SkipsEmptyRows
{
    public int   $created  = 0;
    public int   $updated  = 0;
    public int   $skipped  = 0;
    public array $errors   = [];

    public function startRow(): int
    {
        return 6;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 6;

            $nik  = $this->str($row[44] ?? null);
            $nama = $this->str($row[1] ?? null);

            if (!$nama) {
                $this->skipped++;
                continue;
            }

            if (!$nik) {
                $this->errors[] = "Baris {$rowNum} ({$nama}): kolom NIK kosong, baris dilewati.";
                $this->skipped++;
                continue;
            }

            // Find linked master_guru by NIK
            $masterGuru   = MasterGuru::where('nik', $nik)->first();
            $masterGuruId = $masterGuru?->id;

            $data = [
                'master_guru_id'           => $masterGuruId,
                'nama'                     => $nama,
                'nuptk'                    => $this->str($row[2] ?? null),
                'jenis_kelamin'            => $this->str($row[3] ?? null),
                'tempat_lahir'             => $this->str($row[4] ?? null),
                'tanggal_lahir'            => $this->parseDate($row[5] ?? null),
                'nip'                      => $this->str($row[6] ?? null),
                'status_kepegawaian'       => $this->str($row[7] ?? null),
                'jenis_ptk'               => $this->str($row[8] ?? null),
                'agama'                    => $this->str($row[9] ?? null),
                'alamat_jalan'             => $this->str($row[10] ?? null),
                'rt'                       => $this->str($row[11] ?? null),
                'rw'                       => $this->str($row[12] ?? null),
                'nama_dusun'               => $this->str($row[13] ?? null),
                'desa_kelurahan'           => $this->str($row[14] ?? null),
                'kecamatan'                => $this->str($row[15] ?? null),
                'kode_pos'                 => $this->str($row[16] ?? null),
                'telepon'                  => $this->str($row[17] ?? null),
                'hp'                       => $this->str($row[18] ?? null),
                'email_dapodik'            => $this->str($row[19] ?? null),
                'tugas_tambahan'           => $this->str($row[20] ?? null),
                'sk_cpns'                  => $this->str($row[21] ?? null),
                'tanggal_cpns'             => $this->parseDate($row[22] ?? null),
                'sk_pengangkatan'          => $this->str($row[23] ?? null),
                'tmt_pengangkatan'         => $this->parseDate($row[24] ?? null),
                'lembaga_pengangkatan'     => $this->str($row[25] ?? null),
                'pangkat_golongan'         => $this->str($row[26] ?? null),
                'sumber_gaji'              => $this->str($row[27] ?? null),
                'nama_ibu_kandung'         => $this->str($row[28] ?? null),
                'status_perkawinan'        => $this->str($row[29] ?? null),
                'nama_pasangan'            => $this->str($row[30] ?? null),
                'nip_pasangan'             => $this->str($row[31] ?? null),
                'pekerjaan_pasangan'       => $this->str($row[32] ?? null),
                'tmt_pns'                  => $this->parseDate($row[33] ?? null),
                'lisensi_kepala_sekolah'   => $this->str($row[34] ?? null),
                'diklat_kepengawasan'      => $this->str($row[35] ?? null),
                'keahlian_braille'         => $this->str($row[36] ?? null),
                'keahlian_bahasa_isyarat'  => $this->str($row[37] ?? null),
                'npwp'                     => $this->str($row[38] ?? null),
                'nama_wajib_pajak'         => $this->str($row[39] ?? null),
                'kewarganegaraan'          => $this->str($row[40] ?? null) ?: 'ID',
                'bank'                     => $this->str($row[41] ?? null),
                'no_rekening'              => $this->str($row[42] ?? null),
                'rekening_atas_nama'       => $this->str($row[43] ?? null),
                'no_kk'                    => $this->str($row[45] ?? null),
                'karpeg'                   => $this->str($row[46] ?? null),
                'karis_karsu'              => $this->str($row[47] ?? null),
                'lintang'                  => $this->str($row[48] ?? null),
                'bujur'                    => $this->str($row[49] ?? null),
                'nuks'                     => $this->str($row[50] ?? null),
            ];

            $existing = DapodikGuru::where('nik', $nik)->first();

            if ($existing) {
                $existing->update($data);
                $this->updated++;
            } else {
                DapodikGuru::create(array_merge($data, ['nik' => $nik]));
                $this->created++;
            }
        }
    }

    private function str($value): ?string
    {
        if ($value === null || $value === '') return null;
        return trim((string) $value);
    }

    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') return null;
        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            }
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
