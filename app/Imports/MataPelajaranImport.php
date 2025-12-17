<?php

namespace App\Imports;

use App\Models\MataPelajaran;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MataPelajaranImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari ID Kelas berdasarkan Nama Kelas di Excel
        // Asumsi kolom di excel bernama 'kelas'
        $kelas = Kelas::where('nama_kelas', $row['kelas'])->first();

        // Jika kelas tidak ditemukan (misal typo di excel), bisa di-skip atau set null
        // Di sini saya paksa harus ada, jika tidak ketemu akan error di database (karena foreign key)
        // atau kita bisa lempar error validasi manual (opsional).

        return new MataPelajaran([
            'kode_mapel'  => $row['kode_mapel'],
            'nama_mapel'  => $row['nama_mapel'],
            'jumlah_jam'  => $row['jumlah_jam'],
            'kelas_id'    => $kelas ? $kelas->id : null,
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            'kode_mapel' => 'required|string|unique:mata_pelajarans,kode_mapel',
            'nama_mapel' => 'required|string',
            'jumlah_jam' => 'required|integer|min:1',
            'kelas'      => 'required|exists:kelas,nama_kelas', // Validasi nama kelas harus ada di DB
        ];
    }

    // Pesan error custom biar user paham
    public function customValidationMessages()
    {
        return [
            'kelas.exists' => 'Nama Kelas tidak ditemukan di database.',
        ];
    }
}
