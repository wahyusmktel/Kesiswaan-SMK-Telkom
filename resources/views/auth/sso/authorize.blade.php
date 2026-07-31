<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Persetujuan Akses SISFO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    @php($registeredApp = \App\Models\SsoApplication::where('passport_client_id', $client->id)->first())
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-lg overflow-hidden border border-slate-200 bg-white shadow-xl">
            <div class="h-1 bg-red-600"></div>
            <div class="p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center bg-red-600 font-black text-white">TS</div>
                        <div><p class="font-extrabold">SISFO TS</p><p class="text-xs text-slate-500">Pusat Identitas Sekolah</p></div>
                    </div>
                    <form method="POST" action="{{ route('sso.logout') }}">@csrf<button class="text-xs font-bold text-slate-500 hover:text-red-600">Ganti akun</button></form>
                </div>

                <div class="my-8 text-center">
                    @if($registeredApp?->logo_url)
                        <img src="{{ $registeredApp->logo_url }}" alt="" class="mx-auto h-16 w-16 object-contain">
                    @else
                        <div class="mx-auto flex h-16 w-16 items-center justify-center bg-slate-100 text-2xl font-black text-slate-600">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
                    @endif
                    <h1 class="mt-4 text-2xl font-black">{{ $client->name }}</h1>
                    <p class="mt-2 text-sm text-slate-500">meminta izin mengakses akun SISFO milik <strong class="text-slate-700">{{ $user->email }}</strong>.</p>
                </div>

                <div class="border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-black uppercase text-slate-500">Data yang dapat dibaca</p>
                    <ul class="mt-3 space-y-3">
                        @forelse($scopes as $scope)
                            <li class="flex gap-3 text-sm">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ $scope->description }}</span>
                            </li>
                        @empty
                            <li class="text-sm text-slate-600">Identitas dasar akun SISFO.</li>
                        @endforelse
                    </ul>
                </div>

                <p class="mt-5 text-xs leading-5 text-slate-500">Aplikasi tidak akan menerima kata sandi Anda. Akses dapat dicabut oleh administrator kapan saja.</p>

                <div class="mt-7 grid gap-3 sm:grid-cols-2">
                    <form method="POST" action="{{ route('passport.authorizations.deny') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="state" value="{{ $request->state }}">
                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                        <input type="hidden" name="auth_token" value="{{ $authToken }}">
                        <button type="submit" class="h-12 w-full border border-slate-300 bg-white text-sm font-extrabold hover:bg-slate-50">Batalkan</button>
                    </form>
                    <form method="POST" action="{{ route('passport.authorizations.approve') }}">
                        @csrf
                        <input type="hidden" name="state" value="{{ $request->state }}">
                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                        <input type="hidden" name="auth_token" value="{{ $authToken }}">
                        <button type="submit" class="h-12 w-full bg-red-600 text-sm font-extrabold text-white hover:bg-red-700">Izinkan Akses</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
