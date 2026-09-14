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
        $units = [
            [
                'code' => 'KAPRODI-PPLGIM-ANIMASI',
                'name' => 'Kaprodi PPLGIM ANIMASI',
                'role' => 'Kaprodi PPLGIM ANIMASI',
                'sort_order' => 11,
            ],
            [
                'code' => 'KAPRODI-TJKT',
                'name' => 'Kaprodi TJKT',
                'role' => 'Kaprodi TJKT',
                'sort_order' => 12,
            ],
        ];

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($units as $unitData) {
            Role::findOrCreate($unitData['role'], 'web');

            if (! Schema::hasTable('okr_units')) {
                continue;
            }

            $unit = OkrUnit::query()->firstOrNew(['code' => $unitData['code']]);
            $unit->fill([
                'name' => $unitData['name'],
                'role_names' => collect($unit->role_names ?? [])->push($unitData['role'])->unique()->values()->all(),
                'sort_order' => $unitData['sort_order'],
                'is_active' => true,
            ])->save();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Unit, target OKR, dan role dipertahankan agar rollback kode tidak
        // menghapus histori kinerja maupun penetapan akses pengguna.
    }
};
