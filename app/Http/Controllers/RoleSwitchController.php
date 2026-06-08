<?php

namespace App\Http\Controllers;

use App\Support\DashboardRedirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSwitchController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $user = Auth::user();
        $requestedRole = $request->role;

        if ($user->hasRole($requestedRole)) {
            session(['active_role' => $requestedRole]);

            return redirect()->route(DashboardRedirector::routeNameForUser($user) ?? 'dashboard')
                ->with('success', 'Berhasil beralih ke role ' . $requestedRole);
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki akses ke role tersebut.');
    }
}
