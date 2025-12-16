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
        // Validasi data dasar (jika kosong, skip)
        if (!isset($row['nis']) || !isset($row['nama_lengkap'])) {
            return null;
        }

        // 1. Buat/Update Master Siswa
        // Menggunakan updateOrCreate agar kalau NIS sudah ada, dia update, kalau belum, dia create.
        $siswa = MasterSiswa::updateOrCreate(
            ['nis' => $row['nis']], // Kunci pencarian
            [
                'nama_lengkap' => $row['nama_lengkap'],
                'jenis_kelamin' => strtoupper($row['jenis_kelamin'] ?? 'L'), // Default L
                'tanggal_lahir' => $this->transformDate($row['tanggal_lahir'] ?? null),
                'alamat' => $row['alamat'] ?? null,
                'tempat_lahir' => $row['tempat_lahir'] ?? null,
            ]
        );

        // 2. Otomatis Buat Akun Login (Opsional, tapi sangat membantu)
        // Cek apakah siswa ini sudah punya akun user
        if (!$siswa->user_id) {
            $email = $row['nis'] . '@smktelkom-lpg.sch.id';

            // Cek user by email
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $row['nama_lengkap'],
                    'password' => Hash::make('smktelkom'), // Password default
                ]
            );

            // Assign Role Siswa
            $roleSiswa = Role::findByName('Siswa');
            if ($roleSiswa) {
                $user->assignRole($roleSiswa);
            }

            // Link user ke siswa
            $siswa->update(['user_id' => $user->id]);
        }

        return $siswa;
    }

    /**
     * Konversi format tanggal Excel ke Y-m-d MySQL
     */
    private function transformDate($value)
    {
        if (!$value) return null;
        try {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null; // Jika format kacau, biarkan null
        }
    }
}
