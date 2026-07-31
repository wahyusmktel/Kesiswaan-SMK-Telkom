<x-app-layout>
    <div x-data="ssoApplicationManager()" class="mx-auto max-w-[1600px] space-y-6">
        <section class="overflow-hidden rounded-lg bg-slate-950 p-6 text-white shadow-xl lg:p-8">
            <div class="flex flex-col justify-between gap-5 lg:flex-row lg:items-center">
                <div>
                    <p class="mb-2 text-xs font-black uppercase text-red-400">Identity Provider</p>
                    <h1 class="text-2xl font-black sm:text-3xl">Aplikasi SSO SISFO</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-300">Daftarkan aplikasi yang diizinkan menggunakan akun SISFO melalui OAuth 2.0 Authorization Code dan PKCE.</p>
                </div>
                <button type="button" @click="createOpen = true" class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-red-600 px-4 text-sm font-extrabold hover:bg-red-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Daftarkan Aplikasi
                </button>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
        @endif

        @if(session('sso_credentials'))
            @php($credentials = session('sso_credentials'))
            <section class="rounded-lg border-2 border-amber-300 bg-amber-50 p-5" x-data="{ visible: true }" x-show="visible">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-black text-amber-900">Simpan kredensial sekarang</h2>
                        <p class="mt-1 text-sm text-amber-800">Client secret hanya ditampilkan pada halaman ini dan tidak dapat dilihat kembali.</p>
                    </div>
                    <button type="button" @click="visible = false" class="p-1 text-amber-700" aria-label="Tutup">&times;</button>
                </div>
                <div class="mt-4 grid gap-3 lg:grid-cols-2">
                    <div class="bg-white p-3"><p class="text-[11px] font-bold uppercase text-slate-500">Client ID</p><div class="mt-1 flex items-center gap-2"><code class="min-w-0 flex-1 break-all text-xs">{{ $credentials['client_id'] }}</code><button type="button" @click="copy(@js($credentials['client_id']))" class="rounded p-2 text-slate-500 hover:bg-slate-100" title="Salin Client ID">Salin</button></div></div>
                    @if($credentials['client_secret'])
                        <div class="bg-white p-3"><p class="text-[11px] font-bold uppercase text-slate-500">Client Secret</p><div class="mt-1 flex items-center gap-2"><code class="min-w-0 flex-1 break-all text-xs">{{ $credentials['client_secret'] }}</code><button type="button" @click="copy(@js($credentials['client_secret']))" class="rounded p-2 text-slate-500 hover:bg-slate-100" title="Salin Client Secret">Salin</button></div></div>
                    @else
                        <div class="bg-white p-3"><p class="text-[11px] font-bold uppercase text-slate-500">Mode</p><p class="mt-1 text-sm font-bold text-slate-800">Public Client + PKCE (tanpa secret)</p></div>
                    @endif
                </div>
            </section>
        @endif

        <section class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-white p-5"><p class="text-xs font-bold uppercase text-slate-500">Aplikasi Terdaftar</p><p class="mt-2 text-3xl font-black">{{ $applications->total() }}</p></div>
            <div class="rounded-lg border border-slate-200 bg-white p-5"><p class="text-xs font-bold uppercase text-slate-500">Authorization URL</p><code class="mt-3 block break-all text-xs text-red-600">{{ config('sso.url') }}/oauth/authorize</code></div>
            <div class="rounded-lg border border-slate-200 bg-white p-5"><p class="text-xs font-bold uppercase text-slate-500">UserInfo URL</p><code class="mt-3 block break-all text-xs text-red-600">{{ config('sso.url') }}/api/sso/user</code></div>
        </section>

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4"><h2 class="font-extrabold">Registrasi Aplikasi</h2><p class="mt-1 text-xs text-slate-500">Nonaktifkan aplikasi untuk langsung mencabut seluruh token aksesnya.</p></div>
            @if($applications->isEmpty())
                <div class="px-6 py-16 text-center"><h3 class="font-bold text-slate-800">Belum ada aplikasi SSO</h3><p class="mt-1 text-sm text-slate-500">Daftarkan aplikasi pertama untuk memperoleh Client ID.</p></div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase text-slate-500"><tr><th class="px-5 py-3">Aplikasi</th><th class="px-5 py-3">Client</th><th class="px-5 py-3">Redirect URI</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($applications as $application)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-5 py-4"><div class="flex items-center gap-3">@if($application->logo_url)<img src="{{ $application->logo_url }}" alt="" class="h-10 w-10 rounded object-contain">@else<span class="flex h-10 w-10 items-center justify-center rounded bg-slate-100 font-black text-slate-600">{{ strtoupper(substr($application->client->name, 0, 1)) }}</span>@endif<div><p class="font-bold text-slate-900">{{ $application->client->name }}</p><p class="max-w-[260px] truncate text-xs text-slate-500">{{ $application->homepage_url ?: 'Tanpa homepage' }}</p></div></div></td>
                                    <td class="px-5 py-4"><code class="block max-w-[220px] truncate text-xs">{{ $application->passport_client_id }}</code><span class="mt-1 inline-block rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-600">{{ $application->client->confidential() ? 'Confidential' : 'PKCE Public' }}</span></td>
                                    <td class="px-5 py-4"><p class="max-w-[320px] truncate text-xs text-slate-600">{{ $application->client->redirect_uris[0] ?? '-' }}</p><p class="mt-1 text-[11px] text-slate-400">{{ count($application->client->redirect_uris) }} alamat terdaftar</p></td>
                                    <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $application->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">{{ $application->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                    <td class="px-5 py-4"><div class="flex justify-end gap-1">
                                        <button type="button" title="Edit" @click="openEdit(@js(['name' => $application->client->name, 'description' => $application->description, 'homepage_url' => $application->homepage_url, 'redirect_uris' => $application->client->redirect_uris, 'update_url' => route('super-admin.sso-applications.update', $application)]))" class="rounded-md p-2 text-slate-500 hover:bg-amber-50 hover:text-amber-600">Edit</button>
                                        @if($application->client->confidential())<form method="POST" action="{{ route('super-admin.sso-applications.secret', $application) }}" onsubmit="return confirm('Buat secret baru? Secret lama langsung tidak berlaku.')">@csrf<button class="rounded-md p-2 text-slate-500 hover:bg-blue-50 hover:text-blue-600" title="Buat ulang secret">Secret</button></form>@endif
                                        <form method="POST" action="{{ route('super-admin.sso-applications.toggle', $application) }}">@csrf @method('PATCH')<button class="rounded-md p-2 {{ $application->is_active ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}">{{ $application->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
                                        <form method="POST" action="{{ route('super-admin.sso-applications.destroy', $application) }}" onsubmit="return confirm('Hapus registrasi aplikasi dan cabut seluruh token?')">@csrf @method('DELETE')<button class="rounded-md p-2 text-red-600 hover:bg-red-50">Hapus</button></form>
                                    </div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-5 py-4">{{ $applications->links() }}</div>
            @endif
        </section>

        <div x-cloak x-show="createOpen" class="fixed inset-0 z-[80] flex items-center justify-center p-4" @keydown.escape.window="createOpen = false">
            <div class="absolute inset-0 bg-slate-950/70" @click="createOpen = false"></div>
            <form method="POST" enctype="multipart/form-data" action="{{ route('super-admin.sso-applications.store') }}" class="relative max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white shadow-2xl">@csrf
                <div class="sticky top-0 z-10 flex items-center justify-between border-b bg-white px-6 py-4"><div><h2 class="text-lg font-black">Daftarkan Aplikasi</h2><p class="text-xs text-slate-500">PKCE direkomendasikan untuk aplikasi baru.</p></div><button type="button" @click="createOpen = false" class="p-2 text-xl">&times;</button></div>
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    @include('pages.admin.sso-applications.partials.form', ['mode' => 'create'])
                </div>
                <div class="sticky bottom-0 flex justify-end gap-2 border-t bg-white px-6 py-4"><button type="button" @click="createOpen = false" class="h-10 border px-4 text-sm font-bold">Batal</button><button class="h-10 bg-red-600 px-5 text-sm font-bold text-white">Daftarkan</button></div>
            </form>
        </div>

        <div x-cloak x-show="editOpen" class="fixed inset-0 z-[80] flex items-center justify-center p-4" @keydown.escape.window="editOpen = false">
            <div class="absolute inset-0 bg-slate-950/70" @click="editOpen = false"></div>
            <form method="POST" enctype="multipart/form-data" :action="edit.update_url" class="relative max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white shadow-2xl">@csrf @method('PUT')
                <div class="sticky top-0 z-10 flex items-center justify-between border-b bg-white px-6 py-4"><h2 class="text-lg font-black">Edit Aplikasi SSO</h2><button type="button" @click="editOpen = false" class="p-2 text-xl">&times;</button></div>
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    @include('pages.admin.sso-applications.partials.form', ['mode' => 'edit'])
                </div>
                <div class="sticky bottom-0 flex justify-end gap-2 border-t bg-white px-6 py-4"><button type="button" @click="editOpen = false" class="h-10 border px-4 text-sm font-bold">Batal</button><button class="h-10 bg-red-600 px-5 text-sm font-bold text-white">Simpan</button></div>
            </form>
        </div>
    </div>

    <script>
        function ssoApplicationManager() {
            return {
                createOpen: {{ $errors->any() ? 'true' : 'false' }}, editOpen: false,
                createUris: @js(old('redirect_uris', [''])),
                edit: { name: '', description: '', homepage_url: '', redirect_uris: [''], update_url: '' },
                openEdit(data) { this.edit = data; this.edit.redirect_uris = data.redirect_uris.length ? data.redirect_uris : ['']; this.editOpen = true; },
                copy(value) { navigator.clipboard.writeText(value); },
            }
        }
    </script>
</x-app-layout>
