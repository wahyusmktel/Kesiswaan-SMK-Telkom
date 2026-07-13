<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Registrasi - {{ $appSetting?->school_name ?? 'Sekolah' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 font-sans text-gray-900 antialiased">
    <main class="mx-auto flex min-h-screen max-w-xl items-center px-4 py-10">
        <section class="w-full rounded-lg border border-gray-200 bg-white p-7 text-center shadow-sm">
            @php
                $statusMap = [
                    'pending' => ['Menunggu Verifikasi', 'bg-amber-100 text-amber-800', 'Data sudah diterima dan sedang menunggu pemeriksaan petugas sekolah.'],
                    'approved' => ['Disetujui', 'bg-blue-100 text-blue-800', 'Data sementara sudah dibuat. Hubungi petugas untuk penempatan rombel dan akun SISFO.'],
                    'mapped' => ['Terhubung Dapodik', 'bg-green-100 text-green-800', 'Identitas telah diperbarui dan terhubung dengan data resmi Dapodik.'],
                    'rejected' => ['Perlu Perbaikan', 'bg-red-100 text-red-800', $registration->notes ?: 'Silakan hubungi petugas sekolah untuk memperbaiki data.'],
                ];
                [$label, $class, $message] = $statusMap[$registration->status];
            @endphp
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-900 text-white">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h1 class="mt-5 text-2xl font-black">Registrasi Tercatat</h1>
            <p class="mt-2 text-sm text-gray-500">{{ $registration->nama_lengkap }}</p>
            <div class="mx-auto mt-6 max-w-sm rounded-lg border border-dashed border-gray-300 bg-gray-50 p-5">
                <p class="text-xs font-bold uppercase text-gray-500">Nomor Registrasi</p>
                <p class="mt-1 font-mono text-xl font-black tracking-wider text-gray-900">{{ $registration->registration_number }}</p>
            </div>
            <span class="mt-5 inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $class }}">{{ $label }}</span>
            <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-gray-600">{{ $message }}</p>
            <div class="mt-7 flex justify-center gap-3">
                <a href="{{ route('student-registration.create') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">Registrasi Lain</a>
                <a href="{{ url('/') }}" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-bold text-white hover:bg-gray-700">Kembali</a>
            </div>
        </section>
    </main>
</body>
</html>
