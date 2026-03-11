<section class="space-y-6">
    <header>
        <h3 class="text-lg font-bold text-gray-900">
            {{ __('Login dengan Fingerprint') }}
        </h3>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Gunakan sensor biometrik perangkat Anda untuk login lebih cepat dan aman tanpa memasukkan email dan password.') }}
        </p>
    </header>

    <div x-data="fingerprintSetup()" class="space-y-4">
        {{-- Status and Info --}}
        <template x-if="!supported">
            <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <p class="text-sm font-bold text-amber-800">Browser Tidak Mendukung</p>
                    <p class="text-xs text-amber-700">Browser Anda atau perangkat Anda tidak memiliki fitur autentikasi biometrik yang diperlukan.</p>
                </div>
            </div>
        </template>

        {{-- Registered Devices List --}}
        <div class="space-y-3">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider">{{ __('Perangkat Terdaftar') }}</h4>
            
            @if($user->webAuthnCredentials->count() > 0)
                <div class="grid gap-3">
                    @foreach($user->webAuthnCredentials as $credential)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 group hover:border-indigo-100 hover:bg-white transition-all">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 21a10.003 10.003 0 008.384-4.51m-2.408-4.69L11 11m-1 8L7 11V9a5 5 0 0110 0v2l-3 4" /></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $credential->alias ?? 'Security Key / Fingerprint' }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">Ditambahkan pada {{ $credential->created_at->translatedFormat('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <button 
                                @click="unregister('{{ $credential->id }}')"
                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all opacity-0 group-hover:opacity-100"
                                title="Hapus perangkat"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl p-8 text-center">
                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 21a10.003 10.003 0 008.384-4.51m-2.408-4.69L11 11m-1 8L7 11V9a5 5 0 0110 0v2l-3 4" /></svg>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">Belum ada perangkat biometrik terdaftar.</p>
                </div>
            @endif
        </div>

        {{-- Action Button --}}
        <div class="mt-6 flex justify-end">
            <button 
                x-show="supported"
                @click="register()"
                :disabled="loading"
                class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-bold transition-all shadow-lg shadow-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <div x-show="loading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span x-text="loading ? 'Memproses...' : 'Daftarkan Fingerprint'"></span>
            </button>
        </div>
    </div>
</section>

@push('scripts')
<script src="{{ asset('vendor/webauthn/webauthn.js') }}"></script>
<script>
    function fingerprintSetup() {
        return {
            supported: WebAuthn.supportsWebAuthn(),
            loading: false,
            webauthn: new WebAuthn(),

            async register() {
                this.loading = true;
                try {
                    await this.webauthn.register();
                    window.location.reload();
                } catch (error) {
                    console.error('Registration failed:', error);
                    let message = 'Pendaftaran gagal. Pastikan perangkat Anda mendukung biometrik dan Anda telah memberikan akses.';
                    if (error.name) {
                        message += `\n\nDetail: ${error.name} - ${error.message}`;
                    }
                    alert(message);
                } finally {
                    this.loading = false;
                }
            },

            async unregister(id) {
                if (!confirm('Apakah Anda yakin ingin menghapus perangkat ini?')) return;
                
                try {
                    const response = await fetch(`/webauthn/unregister/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Unregistration failed:', error);
                }
            }
        }
    }
</script>
@endpush
