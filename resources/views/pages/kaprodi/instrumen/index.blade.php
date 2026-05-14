<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Set Instrumen Penilaian UKK</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="instrumenIndex()" x-init="init()">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- TOAST --}}
            <div class="fixed top-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none" style="min-width:300px">
                <template x-for="toast in toasts" :key="toast.id">
                    <div class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-2xl border text-sm font-semibold"
                        :class="{
                            'bg-emerald-50 border-emerald-200 text-emerald-800': toast.type==='success',
                            'bg-red-50 border-red-200 text-red-800':            toast.type==='error',
                            'bg-amber-50 border-amber-200 text-amber-800':      toast.type==='warning',
                        }"
                        :style="'transition:opacity .35s,transform .35s;opacity:'+(toast.visible?1:0)+';transform:translateY('+(toast.visible?'0px':'-8px')+')'">
                        <span x-text="{success:'✅',error:'❌',warning:'⚠️'}[toast.type]"></span>
                        <span class="flex-1" x-text="toast.message"></span>
                        <button @click="removeToast(toast.id)" class="opacity-60 hover:opacity-100 text-lg leading-none">&times;</button>
                    </div>
                </template>
            </div>

            {{-- PAGE HEADER --}}
            <div class="bg-gradient-to-r from-violet-600 to-indigo-700 rounded-2xl p-6 mb-6 text-white shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-black">📊 Set Instrumen Penilaian</h3>
                        <p class="text-violet-200 text-sm mt-1">Kelola struktur penilaian pengetahuan &amp; keterampilan UKK</p>
                    </div>
                    <a href="{{ route('kaprodi.ukk.instrumen.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-violet-700 font-bold rounded-xl shadow-md hover:bg-violet-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Instrumen Baru
                    </a>
                </div>
            </div>

            {{-- CARDS --}}
            @forelse($instrumens as $ins)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-4 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-5">

                        {{-- Left: Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span class="text-lg font-black text-gray-900">{{ $ins->nama_instrumen }}</span>
                                <span class="px-2 py-0.5 rounded-lg bg-violet-50 text-violet-700 text-xs font-bold border border-violet-100">
                                    {{ $ins->ujian?->nama_ujian ?? '—' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mb-3">
                                {{ $ins->ujian?->tahunPelajaran?->tahun ?? '—' }}
                                &middot; {{ $ins->ujian?->jurusan ?? '—' }}
                            </p>

                            {{-- Bobot bar --}}
                            <div class="flex rounded-full overflow-hidden h-6 w-full max-w-xs border border-gray-200">
                                @php $bp = $ins->bobot_pengetahuan; $bk = 100 - $bp; @endphp
                                <div class="flex items-center justify-center text-[10px] font-black text-white bg-blue-500"
                                    style="width:{{ $bp }}%">
                                    @if($bp >= 15) Pengetahuan {{ $bp }}% @endif
                                </div>
                                <div class="flex items-center justify-center text-[10px] font-black text-white bg-emerald-500 flex-1">
                                    @if($bk >= 15) Keterampilan {{ $bk }}% @endif
                                </div>
                            </div>
                        </div>

                        {{-- Middle: Stats --}}
                        <div class="flex gap-4 shrink-0">
                            <div class="text-center">
                                <p class="text-2xl font-black text-blue-600">{{ $ins->soal_pengetahuan_count }}</p>
                                <p class="text-xs text-gray-400 font-semibold">Soal Pengetahuan</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-black text-emerald-600">{{ $ins->kategori_keterampilan_count }}</p>
                                <p class="text-xs text-gray-400 font-semibold">Kategori Keterampilan</p>
                            </div>
                        </div>

                        {{-- Right: Actions --}}
                        <div class="flex gap-2 shrink-0">
                            <a href="{{ route('kaprodi.ukk.instrumen.edit', $ins->id) }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-violet-50 text-violet-700 border border-violet-200 rounded-xl text-sm font-bold hover:bg-violet-600 hover:text-white hover:border-violet-600 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                            <button @click="deleteInstrumen({{ $ins->id }}, '{{ addslashes($ins->nama_instrumen) }}')"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-xl text-sm font-bold hover:bg-red-600 hover:text-white hover:border-red-600 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-16 text-center">
                    <p class="text-5xl mb-4">📊</p>
                    <p class="text-gray-500 font-semibold mb-3">Belum ada instrumen penilaian</p>
                    <a href="{{ route('kaprodi.ukk.instrumen.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 text-white font-bold rounded-xl hover:bg-violet-500 transition-all text-sm">
                        + Buat instrumen pertama
                    </a>
                </div>
            @endforelse

            @if($instrumens->hasPages())
                <div class="mt-4">{{ $instrumens->links() }}</div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    function instrumenIndex() {
        return {
            toasts: [], toastSeq: 0,
            init() {},
            async deleteInstrumen(id, nama) {
                if (!confirm('Hapus instrumen "' + nama + '"?\nSemua soal dan kategori akan ikut terhapus.')) return;
                try {
                    const res  = await fetch('/kaprodi/ukk/instrumen/' + id, {
                        method: 'DELETE',
                        headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    const data = await res.json();
                    if (!res.ok) { this.toast(data.message || 'Gagal.', 'error'); return; }
                    this.toast(data.message, 'success');
                    setTimeout(() => location.reload(), 800);
                } catch { this.toast('Koneksi gagal.', 'error'); }
            },
            toast(msg, type='success') {
                const id = ++this.toastSeq;
                this.toasts.push({ id, message: msg, type, visible: false });
                this.$nextTick(() => { const t = this.toasts.find(t=>t.id===id); if(t) t.visible=true; });
                setTimeout(() => this.removeToast(id), 4500);
            },
            removeToast(id) {
                const t = this.toasts.find(t=>t.id===id); if(t) t.visible=false;
                setTimeout(() => { this.toasts = this.toasts.filter(t=>t.id!==id); }, 400);
            },
        };
    }
    </script>
    @endpush
</x-app-layout>
