<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrasi Siswa Baru - {{ $appSetting?->school_name ?? 'Sekolah' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 font-sans text-gray-900 antialiased">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                @if ($appSetting?->logo)
                    <img src="{{ asset('storage/' . $appSetting->logo) }}" class="h-11 w-11 object-contain" alt="Logo sekolah">
                @else
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-red-600 font-black text-white">S</div>
                @endif
                <div>
                    <p class="font-bold text-gray-900">{{ $appSetting?->school_name ?? 'Sekolah' }}</p>
                    <p class="text-xs text-gray-500">Penerimaan data siswa baru</p>
                </div>
            </a>
            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-red-600">Masuk SISFO</a>
        </div>
    </header>

    <main class="mx-auto grid max-w-6xl gap-6 px-4 py-8 sm:px-6 lg:grid-cols-[320px_minmax(0,1fr)] lg:py-12">
        <aside class="space-y-5">
            <div>
                <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold uppercase text-red-700">Jalur Cepat</span>
                <h1 class="mt-4 text-3xl font-black leading-tight text-gray-900">Registrasi Siswa Baru</h1>
                <p class="mt-3 text-sm leading-6 text-gray-600">Isi data dasar agar operasional sekolah dapat dimulai tanpa menunggu data Dapodik lengkap.</p>
            </div>
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-900">
                Data akan diperiksa oleh Operator atau Waka Kesiswaan. Setelah data resmi tersedia, identitas ini akan dicocokkan dengan Dapodik tanpa menghapus riwayat siswa.
            </div>
            <ol class="space-y-3 text-sm text-gray-600">
                <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-900 text-xs font-bold text-white">1</span><span>Isi identitas dasar dengan benar.</span></li>
                <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-900 text-xs font-bold text-white">2</span><span>Simpan nomor registrasi.</span></li>
                <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-900 text-xs font-bold text-white">3</span><span>Tunggu verifikasi petugas sekolah.</span></li>
            </ol>
        </aside>

        <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="text-lg font-bold">Formulir Data Dasar</h2>
                <p class="mt-1 text-sm text-gray-500">Kolom bertanda * wajib diisi.</p>
            </div>

            @if ($errors->any())
                <div id="validation-notification" x-data="{ show: true }" x-show="show" role="alert"
                    class="mx-6 mt-6 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-900 shadow-sm">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86A2 2 0 0020.66 16L13.73 4a2 2 0 00-3.46 0L3.34 16A2 2 0 005.07 19z" />
                        </svg>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold">Data belum dapat dikirim</p>
                            <p class="mt-1 text-red-800">Isian sebelumnya tetap tersimpan. Perbaiki kolom berwarna merah berikut:</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                        <button type="button" @click="show = false" class="text-red-500 hover:text-red-800" aria-label="Tutup notifikasi">&times;</button>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('student-registration.store') }}" class="p-6">
                @csrf
                @php
                    $inputClass = 'w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500';
                    $invalidClass = '!border-red-500 !bg-red-50 focus:!border-red-600 focus:!ring-red-500';
                @endphp
                <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Nama lengkap *</span><input name="nama_lengkap" value="{{ old('nama_lengkap') }}" required @class([$inputClass, $invalidClass => $errors->has('nama_lengkap')]) @error('nama_lengkap') aria-invalid="true" @enderror placeholder="Sesuai dokumen resmi"><x-input-error :messages="$errors->get('nama_lengkap')" class="mt-1.5" /></label>
                    <label><span class="mb-1 block text-sm font-semibold">NISN</span><input name="nisn" value="{{ old('nisn') }}" inputmode="numeric" maxlength="10" @class([$inputClass, $invalidClass => $errors->has('nisn')]) @error('nisn') aria-invalid="true" @enderror placeholder="10 digit jika sudah ada"><x-input-error :messages="$errors->get('nisn')" class="mt-1.5" /><span class="mt-1 block text-xs text-gray-500">Isi tepat 10 angka. Kosongkan jika belum memiliki NISN.</span></label>
                    <label><span class="mb-1 block text-sm font-semibold">NIK</span><input name="nik" value="{{ old('nik') }}" inputmode="numeric" maxlength="16" @class([$inputClass, $invalidClass => $errors->has('nik')]) @error('nik') aria-invalid="true" @enderror placeholder="16 digit"><x-input-error :messages="$errors->get('nik')" class="mt-1.5" /><span class="mt-1 block text-xs text-gray-500">Isi 16 angka sesuai Kartu Keluarga.</span></label>
                    <label><span class="mb-1 block text-sm font-semibold">Tempat lahir</span><input name="tempat_lahir" value="{{ old('tempat_lahir') }}" @class([$inputClass, $invalidClass => $errors->has('tempat_lahir')]) @error('tempat_lahir') aria-invalid="true" @enderror><x-input-error :messages="$errors->get('tempat_lahir')" class="mt-1.5" /></label>
                    <label><span class="mb-1 block text-sm font-semibold">Tanggal lahir *</span><input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" max="{{ now()->subDay()->format('Y-m-d') }}" required @class([$inputClass, $invalidClass => $errors->has('tanggal_lahir')]) @error('tanggal_lahir') aria-invalid="true" @enderror><x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-1.5" /><span class="mt-1 block text-xs text-gray-500">Pilih tanggal kelahiran siswa, bukan tanggal hari ini.</span></label>
                    <fieldset @class(['rounded-lg p-3 -m-3', 'border border-red-500 bg-red-50' => $errors->has('jenis_kelamin')])>
                        <legend class="mb-2 text-sm font-semibold">Jenis kelamin *</legend>
                        <div class="flex gap-5">
                            <label class="flex items-center gap-2 text-sm"><input type="radio" name="jenis_kelamin" value="L" @checked(old('jenis_kelamin') === 'L') required class="text-red-600 focus:ring-red-500">Laki-laki</label>
                            <label class="flex items-center gap-2 text-sm"><input type="radio" name="jenis_kelamin" value="P" @checked(old('jenis_kelamin') === 'P') required class="text-red-600 focus:ring-red-500">Perempuan</label>
                        </div>
                        <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-1.5" />
                    </fieldset>
                    <label><span class="mb-1 block text-sm font-semibold">Nomor HP siswa *</span><input type="number" name="nomor_hp" value="{{ old('nomor_hp') }}" inputmode="numeric" required @class([$inputClass, $invalidClass => $errors->has('nomor_hp')]) @error('nomor_hp') aria-invalid="true" @enderror placeholder="08xxxxxxxxxx"><x-input-error :messages="$errors->get('nomor_hp')" class="mt-1.5" /><span class="mt-1 block text-xs text-gray-500">Gunakan 9-15 angka tanpa spasi, +62, atau tanda hubung.</span></label>
                    <label><span class="mb-1 block text-sm font-semibold">Email</span><input type="email" name="email" value="{{ old('email') }}" @class([$inputClass, $invalidClass => $errors->has('email')]) @error('email') aria-invalid="true" @enderror><x-input-error :messages="$errors->get('email')" class="mt-1.5" /></label>
                    <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Alamat lengkap *</span><textarea name="alamat" rows="3" required @class([$inputClass, $invalidClass => $errors->has('alamat')]) @error('alamat') aria-invalid="true" @enderror>{{ old('alamat') }}</textarea><x-input-error :messages="$errors->get('alamat')" class="mt-1.5" /></label>
                    <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Sekolah asal</span><input name="sekolah_asal" value="{{ old('sekolah_asal') }}" @class([$inputClass, $invalidClass => $errors->has('sekolah_asal')]) @error('sekolah_asal') aria-invalid="true" @enderror><x-input-error :messages="$errors->get('sekolah_asal')" class="mt-1.5" /></label>
                    <label><span class="mb-1 block text-sm font-semibold">Nama orang tua/wali</span><input name="nama_orang_tua" value="{{ old('nama_orang_tua') }}" @class([$inputClass, $invalidClass => $errors->has('nama_orang_tua')]) @error('nama_orang_tua') aria-invalid="true" @enderror><x-input-error :messages="$errors->get('nama_orang_tua')" class="mt-1.5" /></label>
                    <label><span class="mb-1 block text-sm font-semibold">Nomor HP orang tua/wali</span><input type="number" name="nomor_hp_orang_tua" value="{{ old('nomor_hp_orang_tua') }}" inputmode="numeric" @class([$inputClass, $invalidClass => $errors->has('nomor_hp_orang_tua')]) @error('nomor_hp_orang_tua') aria-invalid="true" @enderror><x-input-error :messages="$errors->get('nomor_hp_orang_tua')" class="mt-1.5" /></label>
                </div>
                <label @class(['mt-6 flex items-start gap-3 rounded-lg border bg-gray-50 p-4 text-sm text-gray-700', 'border-gray-200' => !$errors->has('consent'), 'border-red-500 bg-red-50' => $errors->has('consent')])>
                    <input type="checkbox" name="consent" value="1" @checked(old('consent')) required class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span>Saya menyatakan data yang diisi benar dan bersedia data diperbarui menggunakan data resmi Dapodik.<x-input-error :messages="$errors->get('consent')" class="mt-1.5" /></span>
                </label>
                <div class="mt-6 flex justify-end">
                    <button class="rounded-lg bg-red-600 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-500">Kirim Registrasi</button>
                </div>
            </form>
        </section>
    </main>
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('validation-notification')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        </script>
    @endif
</body>
</html>
