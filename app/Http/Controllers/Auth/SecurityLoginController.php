<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SecurityLoginController extends Controller
{
    /**
     * Show the specialized security login form.
     */
    public function showLoginForm()
    {
        // If already logged in as security, go straight to terminal
        if (Auth::check() && Auth::user()->hasRole('Security')) {
            return redirect()->route('security.terminal.index');
        }

        return view('auth.security-login');
    }

    /**
     * Handle the security login attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $user = Auth::user();

            // Check if user has security role
            if ($user->hasRole('Security')) {
                $request->session()->regenerate();
                
                toast('Selamat Datang di Gate Terminal, ' . $user->name, 'success');
                return redirect()->route('security.terminal.index');
            }

            // If not security, log them out and error
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akses ditolak. Akun ini tidak memiliki akses khusus Security.',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Logout from security session.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('security.login');
    }
}
