<?php

namespace Database\Seeders;

use App\Models\OkrUnit;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class OkrUnitRoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->units() as $index => $unitData) {
            Role::findOrCreate($unitData['unit_role'], 'web');

            $unit = OkrUnit::query()->firstOrCreate(
                ['code' => $unitData['code']],
                [
                    'name' => $unitData['name'],
                    'role_names' => $unitData['role_names'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );

            $roles = collect($unit->role_names ?? [])
                ->push($unitData['unit_role'])
                ->merge($unitData['role_names'])
                ->unique()
                ->values()
                ->all();

            $unit->update([
                'name' => $unitData['name'],
                'role_names' => $roles,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->command?->info('Role pengelola untuk 9 unit OKR berhasil disinkronkan.');
    }

    private function units(): array
    {
        return [
            ['code' => 'QMR', 'name' => 'QMR', 'unit_role' => 'QMR', 'role_names' => ['QMR', 'Kepala Sekolah', 'Super Admin']],
            ['code' => 'KURIKULUM', 'name' => 'Kurikulum', 'unit_role' => 'Kurikulum', 'role_names' => ['Kurikulum', 'Kaprodi', 'Guru Kelas', 'Wali Kelas']],
            ['code' => 'KESISWAAN', 'name' => 'Kesiswaan', 'unit_role' => 'Kesiswaan', 'role_names' => ['Kesiswaan', 'Waka Kesiswaan', 'Guru BK', 'Guru Piket', 'Petugas UKS']],
            ['code' => 'HUBIN-SINERGI', 'name' => 'Hubin Sinergi UP dan Alumni', 'unit_role' => 'Hubin Sinergi UP dan Alumni', 'role_names' => ['Hubin Sinergi UP dan Alumni', 'Koordinator Prakerin']],
            ['code' => 'HUBIN-PPDB', 'name' => 'Hubin PPDB', 'unit_role' => 'Hubin PPDB', 'role_names' => ['Hubin PPDB', 'Operator']],
            ['code' => 'HUMAN-CAPITAL', 'name' => 'Human Capital', 'unit_role' => 'Human Capital', 'role_names' => ['Human Capital', 'KAUR SDM']],
            ['code' => 'KEUANGAN', 'name' => 'Keuangan', 'unit_role' => 'Keuangan', 'role_names' => ['Keuangan', 'Tata Usaha', 'Kantin']],
            ['code' => 'SARPRAS', 'name' => 'Sarana dan Prasarana', 'unit_role' => 'Sarana dan Prasarana', 'role_names' => ['Sarana dan Prasarana', 'KAUR SARPRA', 'Security']],
            ['code' => 'IT', 'name' => 'IT', 'unit_role' => 'IT', 'role_names' => ['IT', 'Super Admin']],
        ];
    }
}
