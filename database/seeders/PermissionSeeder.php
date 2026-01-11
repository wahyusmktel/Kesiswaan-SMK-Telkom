<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'export users',

            // Role & Permission Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'manage permissions',

            // Master Data
            'view master data',
            'manage kelas',
            'manage siswa',
            'manage rombel',
            'manage tahun pelajaran',
            'manage mata pelajaran',
            'manage guru',
            'manage jam pelajaran',

            // Kesiswaan Features
            'view kesiswaan dashboard',
            'monitoring izin',
            'manage poin pelanggaran',
            'manage poin prestasi',
            'manage pemutihan poin',
            'manage panggilan ortu',
            'manage database maintenance',
            'manage kartu akses',
            'manage dispensasi',

            // BK Features
            'view bk dashboard',
            'manage pembinaan rutin',
            'manage jadwal konsultasi',
            'view chat bk',

            // Wali Kelas Features
            'view wali kelas dashboard',
            'manage perizinan wali kelas',
            'manage mentoring wali kelas',

            // Operator Features
            'view operator dashboard',
            'manage dapodik',

            // Piket Features
            'view piket dashboard',
            'manage perizinan siswa',
            'manage penanganan terlambat',
            'manage absensi guru',

            // Kurikulum Features
            'view kurikulum dashboard',
            'manage jadwal pelajaran',
            'manage monitoring absensi guru',
            'manage distribusi mapel',
            'view analisa kurikulum',

            // Security Features
            'view security dashboard',
            'manage verifikasi izin',
            'manage pendataan terlambat',
            'manage gate terminal',

            // SDM Features
            'view sdm dashboard',
            'manage perizinan guru',
            'view rekapitulasi sdm',
            'manage nde referensi',

            // Prakerin Features
            'manage prakerin',
            'monitor prakerin',

            // LMS Features
            'manage lms',

            // Shared Features
            'view coaching analytics',

            // Setting
            'manage settings',

            'view monitoring keterlambatan',
            'view guru kelas dashboard',
            'manage pengaduan ortu',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to Super Admin
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->syncPermissions(Permission::all());
        }

        // Default permissions for Wali Kelas
        $waliKelasRole = Role::where('name', 'Wali Kelas')->first();
        if ($waliKelasRole) {
            $waliKelasRole->syncPermissions([
                'view wali kelas dashboard',
                'manage perizinan wali kelas',
                'manage mentoring wali kelas',
                'view coaching analytics',
                'view monitoring keterlambatan',
            ]);
        }

        // Default permissions for Guru BK
        $bkRole = Role::where('name', 'Guru BK')->first();
        if ($bkRole) {
            $bkRole->syncPermissions([
                'view bk dashboard',
                'manage pembinaan rutin',
                'manage jadwal konsultasi',
                'view chat bk',
                'manage poin pelanggaran',
                'manage poin prestasi',
                'view coaching analytics',
                'view monitoring keterlambatan',
                'manage pengaduan ortu',
            ]);
        }

        // Default permissions for Guru Piket
        $piketRole = Role::where('name', 'Guru Piket')->first();
        if ($piketRole) {
            $piketRole->syncPermissions([
                'view piket dashboard',
                'manage perizinan siswa',
                'manage penanganan terlambat',
                'manage absensi guru',
                'monitoring izin',
                'view monitoring keterlambatan',
            ]);
        }

        // Default permissions for Guru Kelas
        $guruKelasRole = Role::where('name', 'Guru Kelas')->first();
        if ($guruKelasRole) {
            $guruKelasRole->syncPermissions([
                'view guru kelas dashboard',
                'manage lms',
                'manage perizinan siswa',
                'manage dispensasi',
                'view monitoring keterlambatan',
            ]);
        }

        // Default permissions for Kurikulum
        $kurikulumRole = Role::where('name', 'Kurikulum')->first();
        if ($kurikulumRole) {
            $kurikulumRole->syncPermissions([
                'view kurikulum dashboard',
                'manage mata pelajaran',
                'manage guru',
                'manage jadwal pelajaran',
                'manage monitoring absensi guru',
                'manage distribusi mapel',
                'view analisa kurikulum',
            ]);
        }

        // Default permissions for Security
        $securityRole = Role::where('name', 'Security')->first();
        if ($securityRole) {
            $securityRole->syncPermissions([
                'view security dashboard',
                'manage verifikasi izin',
                'manage pendataan terlambat',
                'manage gate terminal',
            ]);
        }

        // Default permissions for Operator
        $operatorRole = Role::where('name', 'Operator')->first();
        if ($operatorRole) {
            $operatorRole->syncPermissions([
                'view operator dashboard',
                'manage dapodik',
                'view master data',
                'manage kelas',
                'manage siswa',
                'manage rombel',
            ]);
        }

        // Default permissions for KAUR SDM
        $sdmRole = Role::where('name', 'KAUR SDM')->first();
        if ($sdmRole) {
            $sdmRole->syncPermissions([
                'view sdm dashboard',
                'manage perizinan guru',
                'view rekapitulasi sdm',
                'manage nde referensi',
            ]);
        }

        // Default permissions for Koordinator Prakerin
        $prakerinRole = Role::where('name', 'Koordinator Prakerin')->first();
        if ($prakerinRole) {
            $prakerinRole->syncPermissions([
                'manage prakerin',
                'monitor prakerin',
            ]);
        }

        // Default permissions for Waka Kesiswaan
        $wakaRole = Role::where('name', 'Waka Kesiswaan')->first();
        if ($wakaRole) {
            $wakaRole->syncPermissions([
                'view users',
                'view roles',
                'view master data',
                'manage kelas',
                'manage siswa',
                'manage rombel',
                'manage tahun pelajaran',
                'view kesiswaan dashboard',
                'monitoring izin',
                'manage poin pelanggaran',
                'manage poin prestasi',
                'manage pemutihan poin',
                'manage panggilan ortu',
                'manage database maintenance',
                'manage kartu akses',
                'manage dispensasi',
                'manage pembinaan rutin',
                'manage jadwal konsultasi',
                'manage penanganan terlambat',
                'view coaching analytics',
                'view monitoring keterlambatan',
            ]);
        }
    }
}
