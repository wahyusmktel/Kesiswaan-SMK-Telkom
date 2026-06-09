<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'Kepala Sekolah']);
        Role::firstOrCreate(['name' => 'Waka Kesiswaan']);
        Role::firstOrCreate(['name' => 'Guru BK']);
        Role::firstOrCreate(['name' => 'Wali Kelas']);
        Role::firstOrCreate(['name' => 'Guru Kelas']);
        Role::firstOrCreate(['name' => 'Guru Piket']);
        Role::firstOrCreate(['name' => 'Security']);
        Role::firstOrCreate(['name' => 'Siswa']);
        Role::firstOrCreate(['name' => 'Petugas UKS']);
    }
}
