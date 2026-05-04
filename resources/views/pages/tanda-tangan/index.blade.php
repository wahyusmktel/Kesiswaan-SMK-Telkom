<x-app-layout>
    @push('styles')
    <style>
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0.4); }
            50% { box-shadow: 0 0 0 8px rgba(99,102,241,0); }
        }
        .glow-pulse { animation: pulse-glow 2s infinite; }
        .ttd-preview { background: repeating-linear-gradient(45deg, #f8faff 0, #f8faff 10px, #eef2ff 10px, #eef2ff 20px); }
    </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Tanda Tangan Digital</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-3 rounded-xl text-sm font-semibold shadow-sm">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-3 rounded-xl text-sm font-semibold shadow-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Hero Banner --}}
            <div class="relative rounded-2xl bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-700 shadow-lg overflow-hidden p-7">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-2xl"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="text-white">
                        <p class="text-indigo-200 text-sm font-semibold uppercase tracking-widest mb-1">Sistem Tanda Tangan Digital</p>
                        <h3 class="text-2xl font-extrabold">Identitas Digital Saya</h3>
                        <p class="text-indigo-100 mt-1 text-sm max-w-lg">
                            Tanda tangani dokumen secara digital. Setiap dokumen terlindungi dengan kriptografi SHA-256 + HMAC dan dapat diverifikasi secara online.
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        @if($signature && $signature->isReady())
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-indigo-700 text-sm font-bold shadow">
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                </span>
                                Identitas Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/20 text-white text-sm font-bold border border-white/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Belum Disetup
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                {{-- ===== KOLOM KIRI: Setup ===== --}}
                <div class="xl:col-span-1 space-y-5">

                    {{-- Status Identitas --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h4 class="font-bold text-gray-800">Status Identitas Digital</h4>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Gambar Tanda Tangan</span>
                                @if($signature && $signature->ttd_image_path)
                                    <span class="text-green-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Tersedia</span>
                                @else
                                    <span class="text-gray-400 font-medium">Belum diunggah</span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">PIN Keamanan</span>
                                @if($signature && $signature->pin_hash)
                                    <span class="text-green-600 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Aktif</span>
                                @else
                                    <span class="text-red-400 font-medium">Belum diset</span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Dokumen Ditandatangani</span>
                                <span class="font-bold text-indigo-700">{{ $documents->total() }}</span>
                            </div>
                        </div>

                        @if($signature && $signature->ttd_image_path)
                            <div class="px-6 pb-5">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Preview Tanda Tangan</p>
                                <div class="ttd-preview rounded-xl p-3 border border-gray-200 flex justify-center items-center h-24">
                                    <img src="{{ asset('storage/' . $signature->ttd_image_path) }}"
                                        class="max-h-20 max-w-full object-contain" alt="TTD">
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Form Setup --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                            <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-800">Setup / Perbarui Identitas</h4>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('tanda-tangan.setup') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                                        Gambar Tanda Tangan
                                        <span class="font-normal text-gray-400 lowercase">(PNG transparan terbaik)</span>
                                    </label>
                                    <input type="file" name="ttd_image" accept="image/jpeg,image/png"
                                        class="w-full text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 rounded-xl cursor-pointer">
                                    @error('ttd_image')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                                        {{ $signature && $signature->pin_hash ? 'Ganti PIN' : 'Set PIN Keamanan' }}
                                    </p>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                                                PIN Baru <span class="font-normal text-gray-400 lowercase">(4–8 digit angka)</span>
                                            </label>
                                            <input type="password" name="pin" inputmode="numeric" maxlength="8"
                                                placeholder="••••••"
                                                class="w-full rounded-xl border-gray-200 text-sm font-mono tracking-widest focus:ring-indigo-500 focus:border-indigo-500">
                                            @error('pin')
                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Konfirmasi PIN</label>
                                            <input type="password" name="pin_confirmation" inputmode="numeric" maxlength="8"
                                                placeholder="••••••"
                                                class="w-full rounded-xl border-gray-200 text-sm font-mono tracking-widest focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition-all shadow-sm hover:shadow-md glow-pulse">
                                    Simpan Identitas Digital
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Info Keamanan --}}
                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-5 text-white">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Keamanan Sistem</p>
                        <ul class="space-y-2 text-sm text-slate-300">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Hash SHA-256 untuk setiap dokumen
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                HMAC-SHA256 verifikasi keaslian
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                PIN dienkripsi Bcrypt, tidak dapat dibaca
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Verifikasi publik via QR code
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- ===== KOLOM KANAN: Riwayat Dokumen ===== --}}
                <div class="xl:col-span-2">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h4 class="font-bold text-gray-800">Riwayat Dokumen Ditandatangani</h4>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $documents->total() }} dokumen total</p>
                        </div>

                        @if($documents->count())
                            <div class="divide-y divide-gray-50">
                                @foreach($documents as $doc)
                                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex items-start gap-3 min-w-0">
                                                <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center
                                                    {{ $doc->is_valid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                                    @if($doc->is_valid)
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $doc->document_title }}</p>
                                                    <p class="text-xs text-gray-400 mt-0.5">
                                                        <span class="bg-indigo-50 text-indigo-600 font-semibold px-2 py-0.5 rounded-md">{{ $doc->document_type }}</span>
                                                        &nbsp;·&nbsp;
                                                        {{ $doc->signed_at->translatedFormat('d F Y, H:i') }} WIB
                                                    </p>
                                                    <p class="text-xs text-gray-400 mt-0.5 font-mono truncate">Token: {{ $doc->token }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                @if($doc->is_valid)
                                                    <span class="text-xs font-bold text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-lg">Sah</span>
                                                    <a href="{{ route('verifikasi.dokumen', $doc->token) }}" target="_blank"
                                                        class="text-xs font-bold text-indigo-600 bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded-lg hover:bg-indigo-100 transition">
                                                        Verifikasi
                                                    </a>
                                                    {{-- Revoke Modal Trigger --}}
                                                    <button onclick="openRevokeModal('{{ $doc->token }}')"
                                                        class="text-xs font-bold text-red-500 hover:text-red-700 px-2 py-1 transition" title="Cabut tanda tangan">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                @else
                                                    <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-200 px-2.5 py-1 rounded-lg">Dicabut</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if(!$doc->is_valid && $doc->revoke_reason)
                                            <p class="mt-2 ml-13 text-xs text-red-500 italic">Alasan: {{ $doc->revoke_reason }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="px-6 py-4 border-t border-gray-100">
                                {{ $documents->links() }}
                            </div>
                        @else
                            <div class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <svg class="w-14 h-14 opacity-25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <p class="font-bold text-base text-gray-500">Belum ada dokumen ditandatangani</p>
                                    <p class="text-sm text-gray-400">Tanda tangan digital akan muncul di sini setelah Anda menandatangani dokumen.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Cabut Tanda Tangan --}}
    <div id="revokeModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" x-data>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Cabut Tanda Tangan</h3>
            <p class="text-sm text-gray-500 mb-4">Dokumen yang dicabut tidak lagi dianggap sah. Masukkan PIN untuk konfirmasi.</p>
            <form action="{{ route('tanda-tangan.revoke') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="token" id="revokeToken">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Alasan Pencabutan</label>
                    <input type="text" name="revoke_reason" placeholder="Opsional..."
                        class="w-full rounded-xl border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">PIN Keamanan</label>
                    <input type="password" name="pin" inputmode="numeric" maxlength="8" required
                        placeholder="••••••" autocomplete="off"
                        class="w-full rounded-xl border-gray-200 text-sm font-mono tracking-widest focus:ring-red-500 focus:border-red-500">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeRevokeModal()"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl text-sm hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-sm transition">
                        Cabut Tanda Tangan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openRevokeModal(token) {
            document.getElementById('revokeToken').value = token;
            document.getElementById('revokeModal').classList.remove('hidden');
            document.getElementById('revokeModal').classList.add('flex');
        }
        function closeRevokeModal() {
            document.getElementById('revokeModal').classList.add('hidden');
            document.getElementById('revokeModal').classList.remove('flex');
        }
        document.getElementById('revokeModal').addEventListener('click', function(e) {
            if (e.target === this) closeRevokeModal();
        });
    </script>
    @endpush
</x-app-layout>
