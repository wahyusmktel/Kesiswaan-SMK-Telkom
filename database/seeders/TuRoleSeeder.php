<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TuRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'view tu dashboard',
            'manage tu letter codes',
            'manage tu incoming letters',
            'manage tu outgoing letters',
            'manage tu letter requests',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create role and assign permissions
        $role = Role::findOrCreate('Tata Usaha', 'web');
        $role->syncPermissions($permissions);

        // Also ensure common roles can request letters
        $requesterPermissions = ['manage tu letter requests'];
        foreach (['Kepala Sekolah', 'Waka Kesiswaan', 'Guru BK', 'Wali Kelas', 'Guru Kelas', 'Guru Piket'] as $roleName) {
            $requesterRole = Role::findByName($roleName, 'web');
            if ($requesterRole) {
                $requesterRole->givePermissionTo('manage tu letter requests');
            }
        }
    }
}
