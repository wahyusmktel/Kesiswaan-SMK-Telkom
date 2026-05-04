<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Dokumen Digital — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        .float-anim { animation: float 3s ease-in-out infinite; }
        @keyframes scan { 0% { top: 0; } 100% { top: 100%; } }
        .scan-line { animation: scan 2s linear infinite; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Navbar minimal --}}
    <header class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-extrabold text-gray-800 leading-none">{{ config('app.name') }}</p>
                    <p class="text-[10px] text-gray-400 font-medium">Sistem Verifikasi Dokumen Digital</p>
                </div>
            </div>
            <a href="{{ url('/') }}" class="text-sm text-indigo-600 font-semibold hover:underline">← Kembali ke Sistem</a>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-12">

        @if($status === 'not_found')
            {{-- ===== TOKEN TIDAK DITEMUKAN ===== --}}
            <div class="text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-5 float-anim">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-800 mb-2">Dokumen Tidak Ditemukan</h1>
                <p class="text-gray-500">Token verifikasi tidak valid atau dokumen belum pernah ditandatangani melalui sistem ini.</p>
            </div>

        @elseif($status === 'revoked')
            {{-- ===== DICABUT ===== --}}
            <div class="bg-red-50 border-2 border-red-200 rounded-2xl overflow-hidden">
                <div class="bg-red-600 px-8 py-6 text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-extrabold text-white">Tanda Tangan Dicabut</h1>
                    <p class="text-red-200 mt-1 text-sm">Dokumen ini tidak lagi dianggap sah secara digital</p>
                </div>
                <div class="px-8 py-6 space-y-3 divide-y divide-red-100">
                    @foreach([
                        ['Judul Dokumen', $doc->document_title],
                        ['Jenis', $doc->document_type],
                        ['Ditandatangani Oleh', $doc->signer_name . ($doc->signer_nip ? ' (NIP. '.$doc->signer_nip.')' : '')],
                        ['Jabatan', $doc->signer_role],
                        ['Waktu Tanda Tangan', $doc->signed_at->translatedFormat('l, d F Y — H:i') . ' WIB'],
                        ['Dicabut Pada', $doc->revoked_at->translatedFormat('l, d F Y — H:i') . ' WIB'],
                    ] as [$label, $val])
                    <div class="pt-3 first:pt-0">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ $label }}</p>
                        <p class="font-semibold text-gray-800 mt-0.5">{{ $val }}</p>
                    </div>
                    @endforeach
                    @if($doc->revoke_reason)
                    <div class="pt-3">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Alasan Pencabutan</p>
                        <p class="font-semibold text-red-700 mt-0.5">{{ $doc->revoke_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>

        @elseif($status === 'tampered')
            {{-- ===== DIPALSUKAN / DIMANIPULASI ===== --}}
            <div class="bg-orange-50 border-2 border-orange-300 rounded-2xl overflow-hidden">
                <div class="bg-orange-600 px-8 py-6 text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-extrabold text-white">Peringatan: Integritas Diragukan</h1>
                    <p class="text-orange-100 mt-1 text-sm">Tanda tangan ditemukan, namun verifikasi HMAC gagal — dokumen mungkin telah dimanipulasi.</p>
                </div>
                <div class="px-8 py-6 space-y-3">
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Judul Dokumen</p>
                        <p class="font-semibold text-gray-800 mt-0.5">{{ $doc->document_title }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Token</p>
                        <p class="font-mono text-xs text-gray-600 mt-0.5 break-all">{{ $doc->token }}</p>
                    </div>
                </div>
            </div>

        @else
            {{-- ===== VALID ===== --}}
            <div class="bg-white border-2 border-green-300 rounded-2xl overflow-hidden shadow-lg">

                {{-- Header hijau --}}
                <div class="relative bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-7 text-center overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="scan-line absolute left-0 right-0 h-0.5 bg-white"></div>
                    </div>
                    <div class="relative">
                        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-11 h-11 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">DOKUMEN SAH</h1>
                        <p class="text-green-100 mt-1 text-sm font-medium">Tanda tangan digital terverifikasi &amp; integritas dokumen terjaga</p>
                    </div>
                </div>

                {{-- Detail dokumen --}}
                <div class="px-8 py-6 space-y-0 divide-y divide-gray-100">
                    <div class="grid grid-cols-2 gap-4 py-3">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Judul Dokumen</p>
                            <p class="font-bold text-gray-800 mt-0.5">{{ $doc->document_title }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Jenis Dokumen</p>
                            <p class="font-bold text-indigo-700 mt-0.5">{{ $doc->document_type }}</p>
                        </div>
                    </div>
                    <div class="py-3">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Ditandatangani Oleh</p>
                        <p class="font-extrabold text-gray-800 text-lg mt-0.5">{{ $doc->signer_name }}</p>
                        @if($doc->signer_nip)
                            <p class="text-sm text-gray-500">NIP. {{ $doc->signer_nip }}</p>
                        @endif
                        <p class="text-sm text-indigo-600 font-semibold">{{ $doc->signer_role }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 py-3">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Waktu Tanda Tangan</p>
                            <p class="font-semibold text-gray-800 text-sm mt-0.5">
                                {{ $doc->signed_at->translatedFormat('d F Y') }}<br>
                                <span class="font-mono text-indigo-600">{{ $doc->signed_at->format('H:i:s') }} WIB</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Verifikasi Terakhir</p>
                            <p class="font-semibold text-gray-800 text-sm mt-0.5">
                                {{ now()->translatedFormat('d F Y') }}<br>
                                <span class="font-mono text-green-600">{{ now()->format('H:i:s') }} WIB</span>
                            </p>
                        </div>
                    </div>
                    <div class="py-3">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Token Dokumen</p>
                        <p class="font-mono text-xs text-gray-500 break-all bg-gray-50 rounded-lg px-3 py-2">{{ $doc->token }}</p>
                    </div>
                    <div class="py-3">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Hash Dokumen (SHA-256)</p>
                        <p class="font-mono text-xs text-gray-500 break-all bg-gray-50 rounded-lg px-3 py-2">{{ $doc->document_hash }}</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-green-50 px-8 py-4 border-t border-green-200 text-center">
                    <p class="text-xs text-green-700 font-medium">
                        Dokumen ini sah dan dikeluarkan oleh <strong>{{ config('app.name') }}</strong>.
                        Verifikasi dilakukan secara otomatis menggunakan HMAC-SHA256.
                    </p>
                    <p class="text-[10px] text-green-600 mt-1 font-mono">{{ url()->current() }}</p>
                </div>
            </div>
        @endif

        {{-- Footer Universal --}}
        <div class="mt-8 text-center text-xs text-gray-400 space-y-1">
            <p>Sistem Verifikasi Dokumen Digital &bull; {{ config('app.name') }}</p>
            <p>Halaman ini dapat diakses siapa saja untuk memverifikasi keaslian dokumen.</p>
        </div>
    </main>
</body>
</html>
