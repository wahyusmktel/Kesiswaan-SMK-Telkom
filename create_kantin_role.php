<?php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$role = Role::firstOrCreate(['name' => 'Kantin']);
$perm = Permission::firstOrCreate(['name' => 'view kantin dashboard']);
$role->givePermissionTo($perm);
echo "Role Kantin created successfully\n";
