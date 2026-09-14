<?php

use App\Models\OkrUnit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::findOrCreate('KAUR LAB', 'web');

        if (! Schema::hasTable('okr_units')) {
            return;
        }

        $unit = OkrUnit::query()->firstOrNew(['code' => 'LAB']);
        $unit->fill([
            'name' => 'Laboratorium',
            'role_names' => collect($unit->role_names ?? [])->push('KAUR LAB')->unique()->values()->all(),
            'sort_order' => 10,
            'is_active' => true,
        ])->save();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Data unit, target OKR, dan penetapan role dipertahankan agar rollback
        // kode tidak menghapus histori kinerja maupun akses pengguna.
    }
};
