<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        $user->assignRole('Super Admin');

        \App\Models\AppSetting::firstOrCreate(
            ['id' => 1],
            ['school_name' => 'SMK Telkom Malang']
        );
    }
}
