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

            // Piket Features
            'view piket dashboard',
            'manage perizinan siswa',
            'manage penanganan terlambat',
            'manage absensi guru',

            // Kurikulum Features
            'view kurikulum dashboard',
            'manage jadwal pelajaran',
            'manage monitoring absensi guru',

            // Security Features
            'view security dashboard',
            'manage verifikasi izin',
            'manage pendataan terlambat',

            // Setting
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to Super Admin
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->syncPermissions(Permission::all());
        }

        // Default permissions for Waka Kesiswaan
        $wakaRole = Role::where('name', 'Waka Kesiswaan')->first();
        if ($wakaRole) {
            $wakaRole->syncPermissions([
                'view users',
                'view roles',
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
            ]);
        }
    }
}
