<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTemporaryPasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->must_change_password
            && ! $request->routeIs('profile.edit', 'password.update', 'logout')) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Demi keamanan, silakan ganti password sementara sebelum menggunakan SISFO.');
        }

        return $next($request);
    }
}
