<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Sertifikat IQ | Aplikasi Izin Stella</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 flex items-center justify-center min-h-screen">
    
    <div class="max-w-md w-full mx-auto px-4 sm:px-0">
        <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-gray-100">
            <!-- Header Banner -->
            <div class="bg-green-600 px-6 py-8 text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_bottom_left,_var(--tw-gradient-stops))] from-white to-green-900 pointer-events-none"></div>
                
                <div class="relative z-10">
                    <div class="mx-auto w-20 h-20 mb-4 bg-white rounded-full flex items-center justify-center shadow-md">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-1 tracking-tight">Sertifikat Valid</h1>
                    <p class="text-green-100 text-sm">Dokumen ini otentik dan tercatat di sistem.</p>
                </div>
            </div>

            <!-- Content Area -->
            <div class="px-6 py-8">
                <div class="text-center mb-8">
                    <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-1">Diberikan Kepada</p>
                    <h2 class="text-2xl font-extrabold text-gray-900 uppercase">
                        {{ $result->user->name }}
                    </h2>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <span class="text-sm font-medium text-gray-500">Skor IQ</span>
                        <span class="text-lg font-bold text-indigo-600">{{ $result->iq_score }}</span>
                    </div>

                    <div class="flex justify-between items-center bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <span class="text-sm font-medium text-gray-500">Kategori</span>
                        <span class="text-sm font-bold text-gray-900">
                            @if($result->iq_score >= 130) Sangat Superior
                            @elseif($result->iq_score >= 120) Superior
                            @elseif($result->iq_score >= 110) Di Atas Rata-rata
                            @elseif($result->iq_score >= 90) Rata-rata Normal
                            @else Di Bawah Rata-rata
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between items-center bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <span class="text-sm font-medium text-gray-500">Tanggal Selesai</span>
                        <span class="text-sm font-bold text-gray-900">{{ $result->created_at->format('d M Y') }}</span>
                    </div>

                    <div class="flex justify-between items-center bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <span class="text-sm font-medium text-gray-500">Nomor Registrasi</span>
                        <span class="text-xs font-mono font-bold text-gray-600">{{ $result->certificate_code }}</span>
                    </div>
                </div>

                <div class="mt-8 text-center border-t border-gray-100 pt-6">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo Stella" class="h-8 mx-auto grayscale opacity-50 mb-2" onerror="this.onerror=null; this.remove();">
                    <p class="text-xs text-gray-400">Penerbitan Sertifikat Didukung oleh <br><span class="font-bold">Aplikasi Izin Stella</span></p>
                </div>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <a href="/" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">Ke Beranda Aplikasi &rarr;</a>
        </div>
    </div>

</body>
</html>
