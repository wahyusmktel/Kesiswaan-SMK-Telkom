<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lapor Aset — {{ $location->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-900 antialiased">
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-x-0 top-0 h-80 bg-gradient-to-br from-red-700 via-red-600 to-orange-500"></div>
        <div class="absolute -right-24 top-16 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        <main class="relative mx-auto max-w-3xl px-4 py-8 sm:py-12">
            <header class="mb-6 text-white">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-widest backdrop-blur">
                    <span class="h-2 w-2 rounded-full bg-emerald-300"></span> Layanan Fasilitas Sekolah
                </div>
                <h1 class="text-3xl font-black sm:text-4xl">Ada aset yang perlu diperbaiki?</h1>
                <p class="mt-2 max-w-xl text-sm leading-6 text-red-50 sm:text-base">Laporkan di sini. Informasi Anda membantu sekolah menjaga fasilitas tetap aman dan nyaman.</p>
            </header>

            <section class="overflow-hidden rounded-3xl bg-white shadow-2xl shadow-black/30">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5 sm:px-8">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l7-4 7 4v14M9 10h.01M9 14h.01M15 10h.01M15 14h.01"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-wider text-red-600">Lokasi terdeteksi</p>
                            <h2 class="mt-1 text-xl font-black text-slate-900">{{ $location->name }}</h2>
                            <p class="text-sm text-slate-500">{{ $location->building->name }}{{ $location->floor ? ' · '.$location->floor : '' }} · Kode {{ $location->code }}</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('asset-report.public.store', $location) }}" enctype="multipart/form-data" class="space-y-7 p-6 sm:p-8">
                    @csrf
                    <input name="website" class="hidden" tabindex="-1" autocomplete="off">

                    @if($errors->any())
                        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <p class="font-extrabold">Mohon periksa kembali laporan:</p>
                            <ul class="mt-2 list-inside list-disc space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <fieldset>
                        <legend class="text-base font-black text-slate-900">1. Identitas pelapor</legend>
                        <p class="mt-1 text-xs text-slate-500">Digunakan petugas bila membutuhkan informasi tambahan.</p>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <label class="text-sm font-bold text-slate-700">Nama lengkap *
                                <input name="reporter_name" value="{{ old('reporter_name') }}" required maxlength="120" class="mt-2 w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" placeholder="Nama pelapor">
                            </label>
                            <label class="text-sm font-bold text-slate-700">Jenis pelapor *
                                <select name="reporter_type" required class="mt-2 w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500">
                                    <option value="siswa" @selected(old('reporter_type') === 'siswa')>Siswa</option>
                                    <option value="guru_karyawan" @selected(old('reporter_type') === 'guru_karyawan')>Guru/Karyawan</option>
                                    <option value="tamu" @selected(old('reporter_type') === 'tamu')>Tamu</option>
                                </select>
                            </label>
                            <label class="text-sm font-bold text-slate-700">NIS/NIP <span class="font-normal text-slate-400">(opsional)</span>
                                <input name="reporter_identifier" value="{{ old('reporter_identifier') }}" maxlength="80" class="mt-2 w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" placeholder="Nomor identitas">
                            </label>
                            <label class="text-sm font-bold text-slate-700">Nomor WhatsApp <span class="font-normal text-slate-400">(opsional)</span>
                                <input name="contact" value="{{ old('contact') }}" maxlength="80" inputmode="tel" class="mt-2 w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" placeholder="08xxxxxxxxxx">
                            </label>
                        </div>
                    </fieldset>

                    <div class="border-t border-slate-200"></div>

                    <fieldset>
                        <legend class="text-base font-black text-slate-900">2. Detail aset atau fasilitas</legend>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <label class="text-sm font-bold text-slate-700 sm:col-span-2">Nama aset/peralatan *
                                <input name="asset_name" value="{{ old('asset_name') }}" required maxlength="160" class="mt-2 w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Keran wastafel, lampu, kursi, AC">
                            </label>
                            <label class="text-sm font-bold text-slate-700">Jenis masalah *
                                <select name="category" required class="mt-2 w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500">
                                    <option value="">Pilih masalah</option>
                                    @foreach(\App\Models\AssetReport::CATEGORIES as $value => $label)
                                        <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="text-sm font-bold text-slate-700">Tingkat urgensi *
                                <select name="urgency" required class="mt-2 w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500">
                                    <option value="rendah" @selected(old('urgency') === 'rendah')>Rendah — masih dapat digunakan</option>
                                    <option value="normal" @selected(old('urgency', 'normal') === 'normal')>Normal — perlu diperbaiki</option>
                                    <option value="tinggi" @selected(old('urgency') === 'tinggi')>Tinggi — mengganggu kegiatan</option>
                                    <option value="darurat" @selected(old('urgency') === 'darurat')>Darurat — berisiko keselamatan</option>
                                </select>
                            </label>
                            <label class="text-sm font-bold text-slate-700 sm:col-span-2">Ceritakan kondisi yang ditemukan *
                                <textarea name="description" required minlength="10" maxlength="3000" rows="5" class="mt-2 w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" placeholder="Jelaskan kerusakan, posisi aset, dan kondisi saat ditemukan...">{{ old('description') }}</textarea>
                            </label>
                            <label class="rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-5 text-center sm:col-span-2">
                                <svg class="mx-auto h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16l4-4a2 2 0 012.8 0L13 15l2-2a2 2 0 012.8 0L21 16M8 8h.01M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span class="mt-2 block text-sm font-extrabold text-slate-700">Tambahkan foto kondisi <span class="font-normal text-slate-400">(opsional)</span></span>
                                <span class="mt-1 block text-xs text-slate-500">JPG, PNG, atau WEBP · Maksimal 5 MB</span>
                                <input name="photo" type="file" accept="image/jpeg,image/png,image/webp" capture="environment" class="mt-3 block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-red-600 file:px-4 file:py-2 file:font-bold file:text-white">
                            </label>
                        </div>
                    </fieldset>

                    <button class="flex h-14 w-full items-center justify-center gap-2 rounded-2xl bg-red-600 text-base font-black text-white shadow-lg shadow-red-600/25 transition hover:bg-red-500 focus:outline-none focus:ring-4 focus:ring-red-200">
                        Kirim Laporan
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg>
                    </button>
                    <p class="text-center text-xs leading-5 text-slate-400">Laporan akan diteruskan kepada pengelola sarana dan prasarana sekolah.</p>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
