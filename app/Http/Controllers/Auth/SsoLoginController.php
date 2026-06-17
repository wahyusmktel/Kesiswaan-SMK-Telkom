<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\DashboardRedirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SsoLoginController extends Controller
{
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('sso_state', $state);

        return redirect()->away(config('sso.base_url').'/authorize?'.http_build_query([
            'client_id' => config('sso.client_id'),
            'redirect_uri' => config('sso.callback_url'),
            'state' => $state,
        ]));
    }

    public function callback(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
            'state' => ['required', 'string'],
        ]);

        if (! hash_equals((string) $request->session()->pull('sso_state'), (string) $request->state)) {
            return redirect()->route('login')->with('error', 'Sesi SSO tidak valid. Silakan coba lagi.');
        }

        $response = Http::asForm()->post(config('sso.base_url').'/api/token', [
            'client_id' => config('sso.client_id'),
            'client_secret' => config('sso.client_secret'),
            'code' => $request->code,
            'redirect_uri' => config('sso.callback_url'),
        ]);

        if (! $response->successful()) {
            return redirect()->route('login')->with('error', $response->json('message') ?? 'Login SSO gagal.');
        }

        $ssoUser = $response->json('user');
        $user = User::where('email', $ssoUser['email'] ?? null)->first();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Email SSO tidak terdaftar di SISFO. Hubungi administrator.');
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        $routeName = DashboardRedirector::routeNameForUser($user) ?? 'dashboard';

        return redirect()->intended(route($routeName));
    }
}
