<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Database\Seeders\KaurSarpraRoleSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KaurSarpraRoleProvisioningController extends Controller
{
    public function __invoke(Request $request, KaurSarpraRoleSeeder $seeder)
    {
        $seeder->run();

        Log::info('Role KAUR SARPRA disinkronkan melalui halaman Super Admin.', [
            'user_id' => $request->user()->id,
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Role KAUR SARPRA berhasil disiapkan. Role sekarang dapat diberikan melalui Manajemen Pengguna.');
    }
}
