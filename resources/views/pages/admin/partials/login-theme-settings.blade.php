<section class="col-span-1 md:col-span-2 border-t border-gray-100 pt-6"
    x-data="{ loginTheme: @js(old('login_theme', $setting->login_theme ?? 'classic')) }">
    <div class="mb-4">
        <h3 class="text-base font-bold text-gray-900">Tampilan Halaman Login</h3>
        <p class="mt-1 text-xs leading-5 text-gray-500">Pilih tema untuk halaman masuk SISFO. Tema login terpisah dari tema beranda dan dapat diganti kembali kapan saja.</p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="cursor-pointer overflow-hidden rounded-xl border-2 bg-white transition-colors" :class="loginTheme === 'classic' ? 'border-red-500' : 'border-gray-200'">
            <div class="flex h-36 overflow-hidden bg-gray-50" aria-hidden="true">
                <div class="flex w-1/2 items-center justify-center bg-gradient-to-br from-red-600 to-red-900 text-xl font-black text-white">SISFO TS</div>
                <div class="flex w-1/2 flex-col justify-center gap-2 p-6"><span class="text-xs font-bold text-gray-800">Selamat Datang</span><div class="h-4 rounded border bg-white"></div><div class="h-4 rounded border bg-white"></div><div class="h-4 rounded bg-red-600"></div></div>
            </div>
            <div class="flex items-center gap-3 p-4"><input type="radio" name="login_theme" value="classic" x-model="loginTheme" @checked(old('login_theme', $setting->login_theme ?? 'classic') === 'classic') class="text-red-600 focus:ring-red-500"><span><strong class="block text-sm text-gray-900">Klasik SISFO</strong><span class="text-xs text-gray-500">Tema merah yang sudah digunakan.</span></span></div>
        </label>
        <label class="cursor-pointer overflow-hidden rounded-xl border-2 bg-white transition-colors" :class="loginTheme === 'owl' ? 'border-red-500' : 'border-gray-200'">
            <div class="relative flex h-36 justify-end overflow-hidden bg-gray-100" aria-hidden="true">
                <img src="{{ $setting->login_background ? Storage::disk('public')->url($setting->login_background) : asset('images/login/sisfo-owl-background.png') }}" class="absolute inset-0 h-full w-full object-contain object-left" alt="">
                <div class="relative my-3 mr-4 flex w-2/5 flex-col justify-center gap-1.5 rounded-xl border border-white bg-white/95 p-3 shadow-sm"><img src="{{ $setting->login_logo ? Storage::disk('public')->url($setting->login_logo) : asset('images/login/sisfo-logo.png') }}" class="mx-auto h-10 w-10 object-contain" alt=""><div class="h-3 rounded border bg-gray-50"></div><div class="h-3 rounded border bg-gray-50"></div><div class="h-3 rounded bg-red-600"></div></div>
            </div>
            <div class="flex items-center gap-3 p-4"><input type="radio" name="login_theme" value="owl" x-model="loginTheme" @checked(old('login_theme', $setting->login_theme ?? 'classic') === 'owl') class="text-red-600 focus:ring-red-500"><span><strong class="block text-sm text-gray-900">SISFO Owl</strong><span class="text-xs text-gray-500">Latar ilustrasi dan kartu login modern.</span></span></div>
        </label>
    </div>
    @error('login_theme') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
    <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-gray-500">Tema aktif: <strong>{{ ($setting->login_theme ?? 'classic') === 'owl' ? 'SISFO Owl' : 'Klasik SISFO' }}</strong></p>
        <a href="{{ route('super-admin.settings.login-preview', ['theme' => old('login_theme', $setting->login_theme ?? 'classic')]) }}"
            :href="@js(route('super-admin.settings.login-preview')) + '?theme=' + loginTheme"
            target="_blank" rel="noopener" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-bold text-red-700 hover:bg-red-50">Pratinjau tema ↗</a>
    </div>
    <div x-show="loginTheme === 'owl'" class="mt-5 grid gap-4 rounded-xl border border-red-100 bg-red-50/40 p-4 sm:grid-cols-2">
        <div class="sm:col-span-2 text-xs leading-5 text-gray-600">Gambar dan logo bawaan sudah tersedia. Unggah gambar sendiri untuk menggantinya. Pratinjau menggunakan gambar dan teks yang terakhir disimpan.</div>
        @foreach(['login_background' => 'Background layar penuh', 'login_logo' => 'Logo SISFO'] as $field => $label)
            <div>
                <label for="{{ $field }}" class="block text-xs font-bold text-gray-700">{{ $label }}</label>
                <input type="file" id="{{ $field }}" name="{{ $field }}" accept="image/png,image/jpeg,image/webp" class="mt-2 block w-full text-xs text-gray-600">
                <p class="mt-2 text-[11px] text-gray-500">PNG, JPG, atau WebP. Maksimal {{ $field === 'login_background' ? '8' : '4' }} MB. {{ $field === 'login_background' ? 'Ilustrasi vertikal ditampilkan utuh di sisi kiri.' : 'PNG transparan disarankan.' }}</p>
                @if($setting->$field)
                    <label class="mt-2 flex items-center gap-2 text-xs text-gray-600"><input type="checkbox" name="reset_{{ $field }}" value="1" class="rounded text-red-600">Gunakan kembali gambar bawaan</label>
                @endif
                @error($field) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        @endforeach
        <div>
            <label for="login_heading" class="block text-xs font-bold text-gray-700">Judul sambutan</label>
            <input id="login_heading" name="login_heading" maxlength="100" value="{{ old('login_heading', $setting->login_heading) }}" placeholder="Senang bertemu lagi!" class="mt-2 w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
            @error('login_heading') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="login_description" class="block text-xs font-bold text-gray-700">Deskripsi sambutan</label>
            <textarea id="login_description" name="login_description" maxlength="255" rows="2" placeholder="Satu akun untuk terhubung dengan seluruh aktivitas sekolah." class="mt-2 w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">{{ old('login_description', $setting->login_description) }}</textarea>
            @error('login_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
</section>
