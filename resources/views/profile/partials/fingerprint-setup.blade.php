<section class="space-y-6">
    <header>
        <h3 class="text-lg font-bold text-gray-900">
            {{ __('Login dengan Fingerprint / Biometrik') }}
        </h3>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Gunakan sensor biometrik perangkat Anda untuk login lebih cepat dan aman tanpa memasukkan email dan password.') }}
        </p>
    </header>

    <div x-data="fingerprintSetup()" class="space-y-4">

        {{-- Browser not supported --}}
        <template x-if="!supported">
            <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="text-sm font-bold text-amber-800">Browser Tidak Mendukung</p>
                    <p class="text-xs text-amber-700 mt-0.5">Browser atau perangkat Anda tidak memiliki fitur autentikasi biometrik (WebAuthn). Gunakan Chrome/Safari versi terbaru.</p>
                </div>
            </div>
        </template>

        {{-- Error message --}}
        <template x-if="errorMsg">
            <div class="p-4 bg-red-50 rounded-xl border border-red-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-bold text-red-700" x-text="errorMsg"></p>
                    <button @click="errorMsg = ''" class="text-xs text-red-500 underline mt-1">Tutup</button>
                </div>
            </div>
        </template>

        {{-- Registered Devices --}}
        <div class="space-y-3">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Perangkat Terdaftar</h4>

            @if($user->webAuthnCredentials->count() > 0)
                <div class="grid gap-3">
                    @foreach($user->webAuthnCredentials as $credential)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 group hover:border-indigo-100 hover:bg-white transition-all">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 21a10.003 10.003 0 008.384-4.51m-2.408-4.69L11 11m-1 8L7 11V9a5 5 0 0110 0v2l-3 4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $credential->alias ?? 'Fingerprint / Biometrik' }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">Ditambahkan: {{ $credential->created_at->translatedFormat('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <button @click="unregister('{{ $credential->id }}')"
                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all opacity-0 group-hover:opacity-100"
                                title="Hapus perangkat">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl p-8 text-center">
                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 21a10.003 10.003 0 008.384-4.51m-2.408-4.69L11 11m-1 8L7 11V9a5 5 0 0110 0v2l-3 4"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm font-semibold">Belum ada perangkat biometrik terdaftar.</p>
                    <p class="text-gray-400 text-xs mt-1">Klik tombol di bawah untuk mendaftarkan fingerprint Anda.</p>
                </div>
            @endif
        </div>

        {{-- Tips --}}
        @if($user->webAuthnCredentials->count() === 0)
        <div class="p-3 bg-blue-50 rounded-xl border border-blue-100">
            <p class="text-xs font-semibold text-blue-700 mb-1">Persyaratan:</p>
            <ul class="text-xs text-blue-600 space-y-0.5 list-disc list-inside">
                <li>Perangkat harus memiliki sensor fingerprint / Face ID</li>
                <li>Browser: Chrome 67+, Safari 14+, Edge 18+, Firefox 60+</li>
                <li>Koneksi HTTPS diperlukan</li>
                <li>Berikan izin biometrik saat diminta browser</li>
            </ul>
        </div>
        @endif

        {{-- Register Button --}}
        <div class="mt-4 flex justify-end">
            <button x-show="supported" @click="register()" :disabled="loading"
                class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <div x-show="loading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span x-text="loading ? 'Memproses...' : '{{ $user->webAuthnCredentials->count() > 0 ? 'Tambah Perangkat' : 'Daftarkan Fingerprint' }}'"></span>
            </button>
        </div>

    </div>
</section>

@push('scripts')
<script src="{{ asset('vendor/webauthn/webauthn.js') }}"></script>
<script>
    // PENTING: Instance WebAuthn dibuat di LUAR Alpine.js reactive context.
    // Alpine.js membungkus semua property dalam Proxy untuk reaktivitas.
    // Private class fields (#field) tidak bisa diakses melalui Proxy → TypeError.
    // Solusi: simpan instance di variable biasa di luar fungsi Alpine.
    let _fpWebAuthn = null;
    try {
        _fpWebAuthn = new WebAuthn();
    } catch (initErr) {
        console.warn('[WebAuthn] Inisialisasi gagal:', initErr.message);
    }

    function fingerprintSetup() {
        return {
            supported: WebAuthn.supportsWebAuthn(),
            loading:   false,
            errorMsg:  '',

            async register() {
                if (!_fpWebAuthn) {
                    this.errorMsg = 'WebAuthn tidak dapat diinisialisasi. Pastikan halaman menggunakan HTTPS dan browser mendukung WebAuthn.';
                    return;
                }

                this.loading  = true;
                this.errorMsg = '';

                try {
                    await _fpWebAuthn.register();
                    window.location.reload();
                } catch (err) {
                    console.error('[WebAuthn] Registrasi gagal:', err);
                    this.errorMsg = this.translateError(err, 'registrasi');
                } finally {
                    this.loading = false;
                }
            },

            async unregister(id) {
                if (!confirm('Hapus perangkat ini? Anda tidak dapat login dengan fingerprint dari perangkat ini lagi.')) return;

                try {
                    const res = await fetch(`/webauthn/unregister/${id}`, {
                        method:  'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept':       'application/json',
                        },
                    });
                    if (res.ok) {
                        window.location.reload();
                    } else {
                        this.errorMsg = 'Gagal menghapus perangkat. Coba lagi.';
                    }
                } catch (err) {
                    console.error('[WebAuthn] Unregister gagal:', err);
                    this.errorMsg = 'Gagal menghapus perangkat: ' + err.message;
                }
            },

            translateError(err, action) {
                // Server HTTP error (Response object)
                if (err instanceof Response || (err && err.status)) {
                    const code = err.status || '?';
                    if (code === 422) return `${action === 'registrasi' ? 'Pendaftaran' : 'Login'} ditolak server (422). Pastikan akun Anda sudah login dan sesi tidak expired.`;
                    if (code === 403) return 'Akses ditolak (403). Anda tidak memiliki izin.';
                    return `Server error (${code}). Coba lagi atau hubungi admin.`;
                }

                const name = err?.name || '';
                const msg  = err?.message || '';

                if (name === 'NotAllowedError')   return 'Permintaan biometrik dibatalkan atau ditolak. Pastikan Anda mengizinkan akses fingerprint/biometrik.';
                if (name === 'NotSupportedError') return 'Perangkat tidak mendukung autentikasi ini. Coba perangkat atau browser lain.';
                if (name === 'SecurityError')     return 'Kesalahan keamanan. Pastikan situs menggunakan HTTPS.';
                if (name === 'AbortError')        return 'Proses dibatalkan. Coba lagi.';
                if (name === 'InvalidStateError') return 'Perangkat ini sudah terdaftar sebelumnya.';
                if (name === 'TimeoutError')      return 'Waktu habis. Pastikan jari menyentuh sensor dan coba lagi.';

                return `${action === 'registrasi' ? 'Pendaftaran' : 'Login'} gagal: ${msg || 'Kesalahan tidak diketahui.'}`;
            },
        }
    }
</script>
@endpush
