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

        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Kepala Sekolah']);
        Role::create(['name' => 'Waka Kesiswaan']);
        Role::create(['name' => 'Guru BK']);
        Role::create(['name' => 'Wali Kelas']);
        Role::create(['name' => 'Guru Kelas']);
        Role::create(['name' => 'Guru Piket']);
        Role::create(['name' => 'Security']);
        Role::create(['name' => 'Siswa']);
    }
}
