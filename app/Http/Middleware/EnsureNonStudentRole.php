<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureNonStudentRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        abort_unless($user, 401);

        $activeRole = session('active_role') ?: $user->getRoleNames()->first();
        abort_if(in_array(Str::lower((string) $activeRole), ['siswa', 'student'], true), 403);

        return $next($request);
    }
}
