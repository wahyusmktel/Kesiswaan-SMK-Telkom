<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HandleActiveRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $roles = $user->getRoleNames(); // Spatie method

            if ($roles->isNotEmpty()) {
                $activeRole = session('active_role');

                // If session doesn't exist or is invalid for this user
                if (!$activeRole || !$roles->contains($activeRole)) {
                    // Set default to first role
                    session(['active_role' => $roles->first()]);
                }
            }
        }

        return $next($request);
    }
}
