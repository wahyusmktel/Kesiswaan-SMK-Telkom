<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SsoApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Passport\ClientRepository;

class SsoApplicationController extends Controller
{
    public function __construct(private readonly ClientRepository $clients) {}

    public function index(): View
    {
        return view('pages.admin.sso-applications.index', [
            'applications' => SsoApplication::with(['client', 'creator'])
                ->latest()
                ->paginate(12),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $redirectUris = $this->redirectUris($data['redirect_uris']);
        $confidential = $data['client_type'] === 'confidential';
        $logoPath = $request->file('logo')?->store('sso-applications', 'public');

        try {
            [$application, $client] = DB::transaction(function () use ($data, $redirectUris, $confidential, $logoPath, $request) {
                $client = $this->clients->createAuthorizationCodeGrantClient(
                    name: $data['name'],
                    redirectUris: $redirectUris,
                    confidential: $confidential,
                );

                $application = SsoApplication::create([
                    'passport_client_id' => $client->id,
                    'description' => $data['description'] ?? null,
                    'homepage_url' => $data['homepage_url'] ?? null,
                    'logo_path' => $logoPath,
                    'is_active' => true,
                    'created_by' => $request->user()->id,
                ]);

                return [$application, $client];
            });
        } catch (\Throwable $exception) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            throw $exception;
        }

        return redirect()->route('super-admin.sso-applications.index')
            ->with('success', 'Aplikasi '.$application->client->name.' berhasil didaftarkan.')
            ->with('sso_credentials', [
                'client_id' => $client->id,
                'client_secret' => $client->plainSecret,
                'type' => $confidential ? 'confidential' : 'public_pkce',
            ]);
    }

    public function update(Request $request, SsoApplication $ssoApplication): RedirectResponse
    {
        $data = $this->validated($request, false);
        $redirectUris = $this->redirectUris($data['redirect_uris']);
        $oldLogo = $ssoApplication->logo_path;
        $newLogo = $request->file('logo')?->store('sso-applications', 'public');

        try {
            DB::transaction(function () use ($data, $redirectUris, $ssoApplication, $newLogo) {
                $this->clients->update($ssoApplication->client, $data['name'], $redirectUris);
                $ssoApplication->update([
                    'description' => $data['description'] ?? null,
                    'homepage_url' => $data['homepage_url'] ?? null,
                    'logo_path' => $newLogo ?: $ssoApplication->logo_path,
                ]);
            });
        } catch (\Throwable $exception) {
            if ($newLogo) {
                Storage::disk('public')->delete($newLogo);
            }

            throw $exception;
        }

        if ($newLogo && $oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        return back()->with('success', 'Konfigurasi aplikasi SSO berhasil diperbarui.');
    }

    public function toggle(SsoApplication $ssoApplication): RedirectResponse
    {
        if ($ssoApplication->is_active) {
            $this->clients->delete($ssoApplication->client);
            $ssoApplication->update(['is_active' => false]);
            $message = 'Aplikasi dinonaktifkan dan seluruh access token-nya dicabut.';
        } else {
            $ssoApplication->client->forceFill(['revoked' => false])->save();
            $ssoApplication->update(['is_active' => true]);
            $message = 'Aplikasi SSO berhasil diaktifkan kembali.';
        }

        return back()->with('success', $message);
    }

    public function regenerateSecret(SsoApplication $ssoApplication): RedirectResponse
    {
        abort_unless($ssoApplication->client->confidential(), 422, 'Aplikasi PKCE publik tidak menggunakan client secret.');

        $this->clients->regenerateSecret($ssoApplication->client);

        return back()
            ->with('success', 'Client secret baru berhasil dibuat. Secret lama langsung tidak berlaku.')
            ->with('sso_credentials', [
                'client_id' => $ssoApplication->client->id,
                'client_secret' => $ssoApplication->client->plainSecret,
                'type' => 'confidential',
            ]);
    }

    public function destroy(SsoApplication $ssoApplication): RedirectResponse
    {
        $this->clients->delete($ssoApplication->client);

        if ($ssoApplication->logo_path) {
            Storage::disk('public')->delete($ssoApplication->logo_path);
        }

        $ssoApplication->delete();

        return back()->with('success', 'Registrasi aplikasi dihapus dan seluruh token akses dicabut.');
    }

    private function validated(Request $request, bool $creating = true): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'homepage_url' => ['nullable', 'url:http,https', 'max:2048'],
            'redirect_uris' => ['required', 'array', 'min:1', 'max:10'],
            'redirect_uris.*' => ['required', 'string', 'max:2048', function (string $attribute, mixed $value, \Closure $fail) {
                if (! $this->validRedirectUri((string) $value)) {
                    $fail('Redirect URI harus HTTPS. HTTP hanya diizinkan untuk localhost saat pengembangan.');
                }
            }],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ];

        if ($creating) {
            $rules['client_type'] = ['required', 'in:public_pkce,confidential'];
        }

        return $request->validate($rules, [
            'redirect_uris.required' => 'Minimal satu Redirect URI wajib diisi.',
            'redirect_uris.*.required' => 'Redirect URI tidak boleh kosong.',
        ]);
    }

    private function redirectUris(array $uris): array
    {
        $uris = array_values(array_unique(array_map(static fn ($uri) => trim($uri), $uris)));

        if (count($uris) === 0) {
            throw ValidationException::withMessages(['redirect_uris' => 'Minimal satu Redirect URI wajib diisi.']);
        }

        return $uris;
    }

    private function validRedirectUri(string $uri): bool
    {
        $parts = parse_url(trim($uri));
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        return $scheme === 'https'
            || ($scheme === 'http' && in_array($host, ['localhost', '127.0.0.1', '::1'], true));
    }
}
