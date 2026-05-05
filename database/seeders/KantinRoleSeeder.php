<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class KantinRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the permission
        $permission = Permission::firstOrCreate(['name' => 'view kantin dashboard']);

        // Create the role and assign the permission
        $role = Role::firstOrCreate(['name' => 'Kantin']);
        $role->givePermissionTo($permission);
        
        $this->command->info('Kantin role and permissions seeded successfully!');
    }
}
