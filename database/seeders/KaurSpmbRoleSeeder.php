<?php

namespace Database\Seeders;

use App\Models\MasterGuru;
use App\Models\SpmbPiketKelompok;
use App\Models\SpmbPiketPetugas;
use App\Models\SpmbPiketTanggal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class KaurSpmbRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Roles
        $roleTitle = Role::firstOrCreate(['name' => 'Kaur SPMB', 'guard_name' => 'web']);
        $roleUpper = Role::firstOrCreate(['name' => 'KAUR SPMB', 'guard_name' => 'web']);
        $roleSuper = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        // 3. Permissions
        $permissions = [
            'view spmb piket',
            'manage spmb piket schedule',
            'print spmb piket schedule',
            'view spmb administrasi',
            'manage spmb administrasi',
        ];

        foreach ($permissions as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            $roleTitle->givePermissionTo($perm);
            $roleUpper->givePermissionTo($perm);
            $roleSuper->givePermissionTo($perm);
        }

        // 4. Assign role to super admin users or first admin user
        $superAdmins = User::role('Super Admin')->get();
        foreach ($superAdmins as $admin) {
            if (! $admin->hasRole('Kaur SPMB')) {
                $admin->assignRole('Kaur SPMB');
            }
        }

        // Enable auto_sign_surat_tugas_spmb for Kepala Sekolah digital signature
        \App\Models\UserDigitalSignature::whereIn('user_id', User::role('Kepala Sekolah')->pluck('id'))
            ->update(['auto_sign_surat_tugas_spmb' => true]);

        // Also if user with id 1 exists
        $userOne = User::find(1);
        if ($userOne && ! $userOne->hasRole('Kaur SPMB')) {
            $userOne->assignRole('Kaur SPMB');
        }

        // 5. Populate Schedule from Official SK 2027/2028
        $tahunAjaran = '2027/2028';

        $kelompokData = [
            [
                'nama' => 'Kelompok 1',
                'urutan' => 1,
                'waktu' => '07.30 - 12.00',
                'keterangan' => 'Piket SPMB Hari Sabtu Putaran 1',
                'petugas' => [
                    'Imam Mahmudi, S.Pd.',
                    'Suntoro, M.Acc.',
                    'Shinta Amelia Wardhani, S.Pd.',
                    'Tamara Azhar Azizah, S.Pd',
                    'Astri Damayanti, S.Pd.',
                    'Hari Hartoyo, S.Pd.',
                    'Imam Suhendri, S.Kom.',
                    'Rendi Syaputra, S.Kom',
                    'Ahmad Ikhsan, M.Kom.',
                    'Surya Aditia Pratama, S.Pd.I',
                ],
                'tanggals' => [
                    '2026-09-26',
                    '2026-10-24',
                    '2026-11-21',
                    '2026-12-19',
                    '2027-01-23',
                    '2027-02-27',
                    '2027-04-03',
                    '2027-05-08',
                    '2027-06-05',
                    '2027-07-03',
                ],
            ],
            [
                'nama' => 'Kelompok 2',
                'urutan' => 2,
                'waktu' => '07.30 - 12.00',
                'keterangan' => 'Piket SPMB Hari Sabtu Putaran 2',
                'petugas' => [
                    'Azhar Mustofa, S.Kom.',
                    'Desy Nur Anggita, S.Kom.',
                    'Nifia Anda Ningrum, S.Pd',
                    'Yuni Marlina, S.Pd',
                    'Retno Ayu Ningsih, S.Pd',
                    'Khafidh Febriansyah, S.Pd.',
                    'Aji Sakti Kurniawan, S.Kom.',
                    'Irfan Novra Desando, S.Pd',
                    'Budi Safta Nugraha, S.Kom',
                    'Faalih Qowiy, M.Pd',
                ],
                'tanggals' => [
                    '2026-10-03',
                    '2026-10-31',
                    '2026-11-28',
                    '2026-12-26',
                    '2027-01-30',
                    '2027-03-06',
                    '2027-04-10',
                    '2027-05-15',
                    '2027-06-12',
                ],
            ],
            [
                'nama' => 'Kelompok 3',
                'urutan' => 3,
                'waktu' => '07.30 - 12.00',
                'keterangan' => 'Piket SPMB Hari Sabtu Putaran 3',
                'petugas' => [
                    'Fatriade Saputra, S.Kom.',
                    'Mevita Yollanda, S.E.',
                    'Dita Safitri, S.Pd.',
                    'Muhammad Galih Febrian, S.Pd',
                    'Nur Cahyana Aminuallah, S.Kom',
                    'Wahyu Rahmat Hidayat, S.Kom.',
                    'Evie Tyaswati, S.Pd',
                    'Hermawan Rijal Arasy, S.Kom.',
                    'Ihwan Hamid Huazain, S.Kom.',
                    'Anang Esa Sulistiawan, S.Kom',
                ],
                'tanggals' => [
                    '2026-10-10',
                    '2026-11-07',
                    '2026-12-05',
                    '2027-01-09',
                    '2027-02-13',
                    '2027-03-20',
                    '2027-04-17',
                    '2027-05-22',
                    '2027-06-19',
                ],
            ],
            [
                'nama' => 'Kelompok 4',
                'urutan' => 4,
                'waktu' => '07.30 - 12.00',
                'keterangan' => 'Piket SPMB Hari Sabtu Putaran 4',
                'petugas' => [
                    'Siti Khairunnisa, S.Pd.',
                    'Hanifah Sahhan, S.Kom',
                    'Rizki Nurmansyah, S.Pd',
                    'Meliya Trisna Wahyuni, S.Pd',
                    'Sri Lastari, S.Pd',
                    'Arohman, S.Kom.',
                    'Dany Rahmatullah, S.Kom.',
                    'Mirza Abdi Wiguna, S.Pd',
                    'Hafizh Pubiando, S.Pd',
                    'Asep Perdiansyah, M.Pd',
                    'Erwin Romel, S.Kom.',
                ],
                'tanggals' => [
                    '2026-10-17',
                    '2026-11-14',
                    '2026-12-12',
                    '2027-01-16',
                    '2027-02-20',
                    '2027-03-27',
                    '2027-04-24',
                    '2027-05-29',
                    '2027-06-26',
                ],
            ],
        ];

        $allGurus = MasterGuru::all();
        $allUsers = User::all();

        foreach ($kelompokData as $kData) {
            $kelompok = SpmbPiketKelompok::updateOrCreate(
                [
                    'tahun_ajaran' => $tahunAjaran,
                    'nama_kelompok' => $kData['nama'],
                ],
                [
                    'waktu' => $kData['waktu'],
                    'urutan' => $kData['urutan'],
                    'keterangan' => $kData['keterangan'],
                ]
            );

            // Seed Petugas
            foreach ($kData['petugas'] as $index => $nama) {
                // Try to find guru in MasterGuru
                $baseName = trim(explode(',', $nama)[0]);
                $matchedGuru = $allGurus->first(function ($g) use ($baseName) {
                    return stripos($g->nama_lengkap, $baseName) !== false;
                });

                $matchedUser = null;
                if ($matchedGuru && $matchedGuru->user_id) {
                    $matchedUser = $allUsers->firstWhere('id', $matchedGuru->user_id);
                } else {
                    $matchedUser = $allUsers->first(function ($u) use ($baseName) {
                        return stripos($u->name, $baseName) !== false;
                    });
                }

                SpmbPiketPetugas::updateOrCreate(
                    [
                        'spmb_piket_kelompok_id' => $kelompok->id,
                        'nama_petugas' => $nama,
                    ],
                    [
                        'master_guru_id' => $matchedGuru?->id,
                        'user_id' => $matchedUser?->id,
                        'urutan' => $index + 1,
                        'jabatan' => 'Petugas Piket SPMB',
                    ]
                );
            }

            // Seed Tanggals
            foreach ($kData['tanggals'] as $tgl) {
                SpmbPiketTanggal::updateOrCreate(
                    [
                        'spmb_piket_kelompok_id' => $kelompok->id,
                        'tanggal' => $tgl,
                    ],
                    [
                        'hari' => 'Sabtu',
                        'waktu' => $kData['waktu'],
                        'status' => 'Terjadwal',
                    ]
                );
            }
        }
    }
}
