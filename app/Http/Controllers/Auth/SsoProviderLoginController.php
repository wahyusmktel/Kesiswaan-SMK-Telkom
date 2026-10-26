<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SsoApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SsoProviderLoginController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->intended(config('sso.url'));
        }

        return view('auth.sso.login', [
            'application' => $this->intendedApplication($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $loginData = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email atau NIS akun SISFO wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim((string) $loginData['email']);
        $resolvedEmail = $loginInput;

        if (! filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $siswa = \App\Models\MasterSiswa::active()->where('nis', $loginInput)->first()
                ?? \App\Models\MasterSiswa::where('nis', $loginInput)->first();
            if ($siswa && $siswa->user) {
                $resolvedEmail = $siswa->user->email;
            }
        }

        $credentials = [
            'email' => $resolvedEmail,
            'password' => $loginData['password'],
        ];

        $key = 'sso-login:'.Str::lower($loginInput).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }

        if (! Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'Email/NIS atau kata sandi SISFO tidak sesuai.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(config('sso.url'));
    }

    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $driver = Socialite::driver('google')
            ->redirectUrl(config('sso.google_redirect_url'))
            ->scopes(['openid', 'profile', 'email']);

        if ($domain = config('sso.google_workspace_domain')) {
            $driver->with(['hd' => $domain]);
        }

        return $driver->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(config('sso.google_redirect_url'))
                ->user();

            $email = Str::lower((string) $googleUser->getEmail());
            $allowedDomain = Str::lower((string) config('sso.google_workspace_domain'));

            if ($allowedDomain && ! Str::endsWith($email, '@'.$allowedDomain)) {
                return redirect()->route('sso.login')->with('error', 'Gunakan akun Google Workspace sekolah.');
            }

            $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

            if (! $user) {
                return redirect()->route('sso.login')->with('error', 'Email Google belum terdaftar sebagai akun SISFO.');
            }

            $user->forceFill(array_filter([
                'google_id' => $user->google_id ?: $googleUser->getId(),
                'avatar' => $user->avatar ?: $googleUser->getAvatar(),
            ]))->save();

            Auth::guard('web')->login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(config('sso.url'));
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('sso.login')->with('error', 'Login Google Workspace gagal. Silakan coba kembali.');
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('sso.login');
    }

    private function intendedApplication(Request $request): ?SsoApplication
    {
        $intended = (string) $request->session()->get('url.intended', '');
        parse_str((string) parse_url($intended, PHP_URL_QUERY), $query);

        return empty($query['client_id'])
            ? null
            : SsoApplication::with('client')->where('passport_client_id', $query['client_id'])->first();
    }
}
