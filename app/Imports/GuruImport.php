<?php

namespace App\Imports;

use App\Models\MasterGuru; // Pastikan namespace model Anda benar
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GuruImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new MasterGuru([
            'nuptk'         => $row['nuptk'],
            'nama_lengkap'  => $row['nama_lengkap'],
            'jenis_kelamin'  => $row['jenis_kelamin'],
        ]);
    }

    // Membaca file Excel mulai dari baris pertama sebagai header
    public function headingRow(): int
    {
        return 1;
    }

    // Menambahkan aturan validasi untuk setiap baris di file Excel
    public function rules(): array
    {
        return [
            // 'nuptk' harus diisi, unik (tidak boleh ada duplikat di DB), dan 16 digit
            'nuptk' => 'required|string|unique:gurus,nuptk|digits:16',

            // 'nama_lengkap' harus diisi
            'nama_lengkap' => 'required|string|max:255',
        ];
    }

    // (Opsional) Custom message untuk validasi
    public function customValidationMessages()
    {
        return [
            'nuptk.unique' => 'NUPTK ini sudah terdaftar.',
            'nuptk.digits' => 'NUPTK harus terdiri dari 16 angka.',
        ];
    }
}
