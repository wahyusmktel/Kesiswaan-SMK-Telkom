<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Ambil user yang baru login
        $user = $request->user();

        // Cek Role dan Redirect sesuai Dashboard masing-masing
        if ($user->hasRole('Siswa')) {
            return redirect()->intended(route('siswa.dashboard.index', absolute: false));
        }

        if ($user->hasRole('Guru Kelas')) {
            return redirect()->intended(route('guru-kelas.dashboard.index', absolute: false));
        }

        if ($user->hasRole('Wali Kelas')) {
            return redirect()->intended(route('wali-kelas.dashboard.index', absolute: false));
        }

        if ($user->hasRole('Guru BK')) {
            return redirect()->intended(route('bk.dashboard.index', absolute: false));
        }

        if ($user->hasRole('Guru Piket')) {
            return redirect()->intended(route('piket.dashboard.index', absolute: false));
        }

        if ($user->hasRole('Waka Kesiswaan')) {
            return redirect()->intended(route('kesiswaan.dashboard.index', absolute: false));
        }

        if ($user->hasRole('Kurikulum')) {
            return redirect()->intended(route('kurikulum.dashboard.index', absolute: false));
        }

        if ($user->hasRole('Operator')) {
            return redirect()->intended(route('operator.dashboard.index', absolute: false));
        }

        if ($user->hasRole('Security')) {
            return redirect()->intended(route('security.dashboard.index', absolute: false));
        }

        if ($user->hasRole('KAUR SDM')) {
            return redirect()->intended(route('sdm.dashboard.index', absolute: false));
        }

        if ($user->hasRole('Super Admin')) {
            return redirect()->intended(route('super-admin.dashboard.index', absolute: false));
        }

        // Default untuk Kepala Sekolah / Role lain yang tidak didefinisikan
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
