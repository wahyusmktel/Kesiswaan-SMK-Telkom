<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $executivePermission = Permission::firstOrCreate([
            'name' => 'view executive dashboard',
            'guard_name' => 'web',
        ]);
        $eraporPermission = Permission::firstOrCreate([
            'name' => 'view erapor',
            'guard_name' => 'web',
        ]);

        Role::query()
            ->where('name', 'Kepala Sekolah')
            ->where('guard_name', 'web')
            ->first()
            ?->syncPermissions([$executivePermission, $eraporPermission]);

        Role::query()
            ->where('name', 'Super Admin')
            ->where('guard_name', 'web')
            ->first()
            ?->givePermissionTo($executivePermission);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission = Permission::query()
            ->where('name', 'view executive dashboard')
            ->where('guard_name', 'web')
            ->first();

        if ($permission) {
            $permission->roles()->detach();
            $permission->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
