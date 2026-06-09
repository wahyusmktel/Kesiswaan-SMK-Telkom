<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PetugasUksRoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view uks dashboard',
            'manage uks medical records',
            'view uks reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        Role::firstOrCreate(['name' => 'Petugas UKS'])->syncPermissions($permissions);
    }
}
