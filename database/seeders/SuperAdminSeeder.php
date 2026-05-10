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
            ['email' => 'superadmin@smktelkom-lpg.id'],
            [
                'name'     => 'Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('10Juli1995?'),
            ]
        );

        if (!$user->hasRole('Super Admin')) {
            $user->assignRole('Super Admin');
        }

        \App\Models\AppSetting::firstOrCreate(
            ['id' => 1],
            ['school_name' => 'SMK Telkom Lampung']
        );
    }
}
