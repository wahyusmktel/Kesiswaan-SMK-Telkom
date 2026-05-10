<?php

namespace App\Imports;

use App\Models\MasterGuru;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Spatie\Permission\Models\Role;

class PegawaiImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public array $errors   = [];
    public int   $updated  = 0;
    public int   $skipped  = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            $userId = trim($row['user_id'] ?? '');
            if (!$userId) {
                $this->errors[] = "Baris {$rowNum}: kolom user_id kosong.";
                $this->skipped++;
                continue;
            }

            $user = User::with(['roles', 'masterGuru'])->find($userId);
            if (!$user) {
                $this->errors[] = "Baris {$rowNum}: user_id {$userId} tidak ditemukan.";
                $this->skipped++;
                continue;
            }

            $name  = trim($row['nama_lengkap'] ?? '');
            $email = trim($row['email'] ?? '');
            $role  = trim($row['role_jabatan'] ?? '');

            if (!$name || !$email) {
                $this->errors[] = "Baris {$rowNum}: Nama atau Email tidak boleh kosong.";
                $this->skipped++;
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = "Baris {$rowNum}: format email tidak valid ({$email}).";
                $this->skipped++;
                continue;
            }

            // Check email uniqueness (skip own row)
            $emailExists = User::where('email', $email)->where('id', '!=', $user->id)->exists();
            if ($emailExists) {
                $this->errors[] = "Baris {$rowNum}: email {$email} sudah digunakan akun lain.";
                $this->skipped++;
                continue;
            }

            $nik      = trim($row['nik'] ?? '') ?: null;
            $nuptk    = trim($row['nuptk'] ?? '') ?: null;
            $kodeGuru = trim($row['kode_guru'] ?? '') ?: null;
            $jk       = strtoupper(trim($row['jenis_kelamin_lp'] ?? ''));
            $jk       = in_array($jk, ['L', 'P']) ? $jk : null;

            // Check NIK uniqueness if provided
            if ($nik) {
                $nikExists = MasterGuru::where('nik', $nik)
                    ->where('user_id', '!=', $user->id)
                    ->exists();
                if ($nikExists) {
                    $this->errors[] = "Baris {$rowNum}: NIK {$nik} sudah digunakan pegawai lain.";
                    $this->skipped++;
                    continue;
                }
            }

            // Check NUPTK uniqueness if provided
            if ($nuptk) {
                $nuptkExists = MasterGuru::where('nuptk', $nuptk)
                    ->where('user_id', '!=', $user->id)
                    ->exists();
                if ($nuptkExists) {
                    $this->errors[] = "Baris {$rowNum}: NUPTK {$nuptk} sudah digunakan pegawai lain.";
                    $this->skipped++;
                    continue;
                }
            }

            // Update user
            $user->update(['name' => $name, 'email' => $email]);

            // Update role
            if ($role && Role::where('name', $role)->exists()) {
                $user->syncRoles([$role]);
            }

            // Update or create MasterGuru
            MasterGuru::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_lengkap'  => $name,
                    'nik'           => $nik,
                    'nuptk'         => $nuptk,
                    'kode_guru'     => $kodeGuru,
                    'jenis_kelamin' => $jk ?? ($user->masterGuru?->jenis_kelamin ?? 'L'),
                ]
            );

            $this->updated++;
        }
    }
}
