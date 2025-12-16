<?php

namespace App\Imports;

use App\Models\MasterSiswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Permission\Models\Role;

class SiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Validasi data dasar (skip jika kosong)
        if (!isset($row['nis']) || !isset($row['nama_lengkap'])) {
            return null;
        }

        // HANYA UPDATE/CREATE DATA SISWA (Tanpa User Account)
        return MasterSiswa::updateOrCreate(
            ['nis' => $row['nis']], // Kunci pencarian (agar tidak duplikat)
            [
                'nama_lengkap' => $row['nama_lengkap'],
                'jenis_kelamin' => strtoupper($row['jenis_kelamin'] ?? 'L'),
                'tanggal_lahir' => $this->transformDate($row['tanggal_lahir'] ?? null),
                'alamat' => $row['alamat'] ?? null,
                'tempat_lahir' => $row['tempat_lahir'] ?? null,
            ]
        );
    }

    /**
     * Helper untuk konversi tanggal (tetap dipakai)
     */
    private function transformDate($value)
    {
        if (!$value) return null;
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
    // public function model(array $row)
    // {
    //     // Validasi data dasar (jika kosong, skip)
    //     if (!isset($row['nis']) || !isset($row['nama_lengkap'])) {
    //         return null;
    //     }

    //     // 1. Buat/Update Master Siswa
    //     // Menggunakan updateOrCreate agar kalau NIS sudah ada, dia update, kalau belum, dia create.
    //     $siswa = MasterSiswa::updateOrCreate(
    //         ['nis' => $row['nis']], // Kunci pencarian
    //         [
    //             'nama_lengkap' => $row['nama_lengkap'],
    //             'jenis_kelamin' => strtoupper($row['jenis_kelamin'] ?? 'L'), // Default L
    //             'tanggal_lahir' => $this->transformDate($row['tanggal_lahir'] ?? null),
    //             'alamat' => $row['alamat'] ?? null,
    //             'tempat_lahir' => $row['tempat_lahir'] ?? null,
    //         ]
    //     );

    //     // 2. Otomatis Buat Akun Login (Opsional, tapi sangat membantu)
    //     // Cek apakah siswa ini sudah punya akun user
    //     if (!$siswa->user_id) {
    //         $email = $row['nis'] . '@smktelkom-lpg.sch.id';

    //         // Cek user by email
    //         $user = User::firstOrCreate(
    //             ['email' => $email],
    //             [
    //                 'name' => $row['nama_lengkap'],
    //                 'password' => Hash::make('smktelkom'), // Password default
    //             ]
    //         );

    //         // Assign Role Siswa
    //         $roleSiswa = Role::findByName('Siswa');
    //         if ($roleSiswa) {
    //             $user->assignRole($roleSiswa);
    //         }

    //         // Link user ke siswa
    //         $siswa->update(['user_id' => $user->id]);
    //     }

    //     return $siswa;
    // }

    /**
     * Konversi format tanggal Excel (Serial Number) atau String ke Y-m-d MySQL
     */
    // private function transformDate($value)
    // {
    //     if (!$value) return null;

    //     try {
    //         // Skenario 1: Jika formatnya Angka (Serial Date Excel, misal: 44562)
    //         if (is_numeric($value)) {
    //             return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
    //         }

    //         // Skenario 2: Jika formatnya Teks (misal: "2007-05-12" atau "12/05/2007")
    //         return \Carbon\Carbon::parse($value)->format('Y-m-d');
    //     } catch (\Exception $e) {
    //         // Jika data benar-benar kacau, kembalikan null biar tidak error 500
    //         return null;
    //     }
    // }
}
