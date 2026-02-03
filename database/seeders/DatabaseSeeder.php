<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,

            SuperAdminSeeder::class,
            OperatorRoleSeeder::class,
            KaurSdmRoleSeeder::class,
            KurikulumRoleSeeder::class,
            WakaKesiswaanSeeder::class,
            KoordinatorPrakerinRoleSeeder::class,

            JamPelajaranSeeder::class,
        ]);
    }
}
