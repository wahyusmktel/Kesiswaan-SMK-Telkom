<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSsoDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('sso.enforce_domain') || ! $request->is('oauth/*')) {
            return $next($request);
        }

        $expectedHost = parse_url(config('sso.url'), PHP_URL_HOST);

        if (! $expectedHost || strcasecmp($request->getHost(), $expectedHost) === 0) {
            return $next($request);
        }

        if ($request->isMethodSafe()) {
            $target = config('sso.url').'/'.ltrim($request->getRequestUri(), '/');

            return redirect()->away($target, 302);
        }

        abort(404);
    }
}
