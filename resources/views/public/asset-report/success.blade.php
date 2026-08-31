<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Berhasil Dikirim</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans antialiased">
    <main class="flex min-h-screen items-center justify-center p-4">
        <section class="w-full max-w-lg overflow-hidden rounded-3xl bg-white text-center shadow-2xl">
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 px-8 py-10 text-white">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-white/20 ring-8 ring-white/10">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 12 4 4L19 6"/></svg>
                </div>
                <h1 class="mt-6 text-3xl font-black">Terima kasih!</h1>
                <p class="mt-2 text-sm text-emerald-50">Laporanmu sudah tercatat dan siap ditindaklanjuti.</p>
            </div>
            <div class="p-8">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Nomor laporan</p>
                <p class="mt-2 rounded-2xl bg-slate-100 px-4 py-3 font-mono text-xl font-black text-slate-900">{{ $report->ticket_number }}</p>
                <div class="mt-6 rounded-2xl border border-slate-200 p-4 text-left text-sm">
                    <p class="font-extrabold text-slate-900">{{ $report->asset_name }}</p>
                    <p class="mt-1 text-slate-500">{{ $location->name }} · {{ $location->building->name }}</p>
                </div>
                <p class="mt-6 text-xs leading-5 text-slate-400">Simpan nomor laporan jika diperlukan. Hindari mengirim laporan yang sama berulang kali.</p>
                <a href="{{ route('asset-report.public.create', $location) }}" class="mt-6 inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-bold text-slate-700 hover:bg-slate-50">Buat laporan lain</a>
            </div>
        </section>
    </main>
</body>
</html>
