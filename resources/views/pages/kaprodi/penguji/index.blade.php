<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Set Penguji UKK</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="pengujiIndex()" x-init="init()" @show-toast.window="toast($event.detail.msg, $event.detail.type)">
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
            <div class="bg-gradient-to-r from-orange-500 to-amber-600 rounded-2xl p-6 mb-6 text-white shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-black">👨‍🏫 Set Penguji UKK</h3>
                        <p class="text-orange-100 text-sm mt-1">
                            Mapping penguji untuk setiap ujian UKK
                            @if($tahunAktif)
                                &middot; Tahun Pelajaran <span class="font-bold">{{ $tahunAktif->tahun }}</span>
                                Semester <span class="font-bold">{{ $tahunAktif->semester }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2 bg-white/20 rounded-xl px-4 py-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-white font-bold text-sm">{{ $guruKelas->count() }} Guru Tersedia</span>
                    </div>
                </div>
            </div>

            @if(!$tahunAktif)
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center mb-6">
                    <p class="text-4xl mb-3">⚠️</p>
                    <p class="text-amber-800 font-bold">Tidak ada tahun pelajaran aktif.</p>
                    <p class="text-amber-600 text-sm mt-1">Aktifkan tahun pelajaran terlebih dahulu.</p>
                </div>
            @endif

            @forelse($ujians as $ujian)
                {{-- UJIAN CARD --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-5 overflow-hidden hover:shadow-md transition-shadow"
                    x-data="ujianCard({{ $ujian->id }}, {{ json_encode($ujian->penguji->map(fn($u) => ['id' => $u->id, 'nama' => $u->masterGuru?->nama_lengkap ?? $u->name, 'kode' => $u->masterGuru?->kode_guru ?? '—', 'avatar' => $u->avatar, 'peran' => $u->pivot?->peran ?? 'semua'])->values()->all()) }}, {{ $ujian->is_bilingual ? 'true' : 'false' }})">

                    {{-- Card header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-slate-50">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="text-base font-black text-gray-900">{{ $ujian->nama_ujian }}</span>
                                <span class="px-2 py-0.5 rounded-lg bg-orange-50 text-orange-700 text-xs font-bold border border-orange-100">
                                    {{ $ujian->jurusan }}
                                </span>
                                @if($ujian->is_bilingual)
                                    <span class="px-2 py-0.5 rounded-lg bg-purple-100 text-purple-800 text-xs font-black border border-purple-200">
                                        🌐 Wawancara Bilingual (EN)
                                    </span>
                                @endif
                                @if($ujian->tanggal_pelaksanaan)
                                    <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                                        📅 {{ $ujian->tanggal_pelaksanaan->format('d M Y') }}
                                    </span>
                                @endif
                            </div>
                            @if($ujian->nama_project)
                                <p class="text-xs text-gray-500">🔧 {{ $ujian->nama_project }}</p>
                            @endif
                        </div>
                        <div class="shrink-0">
                            <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 text-xs font-bold border border-amber-200">
                                <span x-text="penguji.length"></span> Penguji
                            </span>
                        </div>
                    </div>

                    {{-- Penguji chips --}}
                    <div class="p-5">
                        <div class="flex flex-wrap gap-2.5 mb-4 min-h-[2.5rem] items-center">
                            <template x-if="penguji.length === 0">
                                <span class="text-sm text-gray-400 italic">Belum ada penguji yang ditambahkan.</span>
                            </template>
                            <template x-for="p in penguji" :key="p.id">
                                <div class="flex items-center gap-2 pl-1.5 pr-2 py-1.5 rounded-2xl border transition-all"
                                    :class="{
                                        'bg-purple-50/70 border-purple-200 text-purple-900': p.peran === 'pengetahuan',
                                        'bg-amber-50/70 border-amber-200 text-amber-900':   p.peran === 'keterampilan',
                                        'bg-slate-50 border-slate-200 text-slate-800':       p.peran === 'semua' || !p.peran
                                    }">
                                    {{-- Avatar --}}
                                    <div class="w-8 h-8 rounded-full overflow-hidden bg-white shadow-sm shrink-0 flex items-center justify-center">
                                        <template x-if="p.avatar">
                                            <img :src="p.avatar" :alt="p.nama" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!p.avatar">
                                            <span class="text-slate-700 text-xs font-black" x-text="p.nama.charAt(0).toUpperCase()"></span>
                                        </template>
                                    </div>
                                    <div class="leading-tight">
                                        <span class="text-xs font-black block" x-text="p.nama"></span>
                                        <span class="text-[10px] text-gray-400 block" x-text="p.kode"></span>
                                    </div>

                                    {{-- Dropdown / Select Peran --}}
                                    <select :value="p.peran || 'semua'" @change="changePeran(p, $event.target.value)"
                                        class="text-[11px] font-bold py-1 px-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-1 focus:ring-orange-400 cursor-pointer bg-white"
                                        :class="{
                                            'text-purple-700 font-extrabold border-purple-300': p.peran === 'pengetahuan',
                                            'text-amber-700 font-extrabold border-amber-300':   p.peran === 'keterampilan',
                                            'text-slate-700 font-bold':                         p.peran === 'semua' || !p.peran
                                        }">
                                        <option value="pengetahuan">🌐 Pengetahuan (Bilingual)</option>
                                        <option value="keterampilan">🛠️ Keterampilan</option>
                                        <option value="semua">👥 Semua Aspek</option>
                                    </select>

                                    <button @click="removePenguji(p.id)"
                                        class="w-6 h-6 rounded-full bg-black/5 hover:bg-red-500 hover:text-white flex items-center justify-center text-xs font-black transition-colors"
                                        title="Hapus penguji">
                                        &times;
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Add penguji button --}}
                        <button @click="openModal()"
                            class="inline-flex items-center gap-2 px-4 py-2 border-2 border-dashed border-orange-300 rounded-xl text-sm font-bold text-orange-600 hover:bg-orange-50 hover:border-orange-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Penguji
                        </button>
                    </div>

                    {{-- ── MODAL PILIH PENGUJI ── --}}
                    <div x-show="modalOpen" style="display:none"
                        class="fixed inset-0 z-[1000] bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
                        @click.self="modalOpen = false">

                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[85vh]"
                            @click.stop>

                            {{-- Modal header --}}
                            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-amber-50 rounded-t-2xl shrink-0">
                                <div>
                                    <p class="font-black text-gray-900">Pilih Penguji UKK</p>
                                    <p class="text-xs text-gray-500">Pilih guru dan tentukan perannya pada ujian ini</p>
                                </div>
                                <button @click="modalOpen = false"
                                    class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 font-bold transition-colors">
                                    &times;
                                </button>
                            </div>

                            {{-- Role Selector for new penguji --}}
                            <div class="px-5 py-3 border-b border-gray-100 bg-slate-50/50 shrink-0">
                                <label class="block text-xs font-black text-gray-700 mb-2">Pilih Tugas / Peran Penguji:</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <label class="flex flex-col items-center justify-center p-2 rounded-xl border-2 text-center cursor-pointer transition-all text-xs"
                                        :class="selectedPeran === 'pengetahuan' ? 'bg-purple-50 border-purple-500 text-purple-900 font-black shadow-sm' : 'bg-white border-gray-200 text-gray-600 font-semibold hover:border-gray-300'">
                                        <input type="radio" value="pengetahuan" x-model="selectedPeran" class="sr-only">
                                        <span class="text-base mb-0.5">🌐</span>
                                        <span>Pengetahuan</span>
                                        <span class="text-[10px] text-purple-600 font-normal">(Bilingual / B. Inggris)</span>
                                    </label>
                                    <label class="flex flex-col items-center justify-center p-2 rounded-xl border-2 text-center cursor-pointer transition-all text-xs"
                                        :class="selectedPeran === 'keterampilan' ? 'bg-amber-50 border-amber-500 text-amber-900 font-black shadow-sm' : 'bg-white border-gray-200 text-gray-600 font-semibold hover:border-gray-300'">
                                        <input type="radio" value="keterampilan" x-model="selectedPeran" class="sr-only">
                                        <span class="text-base mb-0.5">🛠️</span>
                                        <span>Keterampilan</span>
                                        <span class="text-[10px] text-amber-600 font-normal">(Praktik Produktif)</span>
                                    </label>
                                    <label class="flex flex-col items-center justify-center p-2 rounded-xl border-2 text-center cursor-pointer transition-all text-xs"
                                        :class="selectedPeran === 'semua' ? 'bg-emerald-50 border-emerald-500 text-emerald-900 font-black shadow-sm' : 'bg-white border-gray-200 text-gray-600 font-semibold hover:border-gray-300'">
                                        <input type="radio" value="semua" x-model="selectedPeran" class="sr-only">
                                        <span class="text-base mb-0.5">👥</span>
                                        <span>Semua Aspek</span>
                                        <span class="text-[10px] text-emerald-600 font-normal">(P &amp; K)</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Search --}}
                            <div class="px-4 py-3 border-b border-gray-100 shrink-0">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                    </svg>
                                    <input x-model="search" type="text" placeholder="Cari nama guru (termasuk guru bahasa inggris)…"
                                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 bg-gray-50">
                                </div>
                            </div>

                            {{-- Guru list --}}
                            <div class="overflow-y-auto flex-1 p-3 flex flex-col gap-1.5">
                                <template x-for="g in filteredGuru" :key="g.id">
                                    <button @click="assignPenguji(g)"
                                        :disabled="isAssigned(g.id) || assigning"
                                        class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-left transition-all"
                                        :class="isAssigned(g.id)
                                            ? 'bg-emerald-50 border border-emerald-200 cursor-default opacity-70'
                                            : 'bg-gray-50 hover:bg-orange-50 hover:border-orange-200 border border-transparent'">

                                        {{-- Avatar --}}
                                        <div class="w-9 h-9 rounded-full overflow-hidden bg-orange-100 shrink-0 flex items-center justify-center">
                                            <template x-if="g.avatar">
                                                <img :src="g.avatar" :alt="g.nama" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!g.avatar">
                                                <span class="text-orange-600 font-black text-sm" x-text="g.nama.charAt(0).toUpperCase()"></span>
                                            </template>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-800 truncate" x-text="g.nama"></p>
                                            <p class="text-xs text-gray-400" x-text="g.kode"></p>
                                        </div>

                                        <template x-if="isAssigned(g.id)">
                                            <span class="text-xs font-bold text-emerald-600 shrink-0">✓ Sudah Ditugaskan</span>
                                        </template>
                                        <template x-if="!isAssigned(g.id)">
                                            <span class="text-xs font-black text-orange-600 shrink-0">+ Tugaskan</span>
                                        </template>
                                    </button>
                                </template>

                                <div x-show="filteredGuru.length === 0" class="text-center py-8 text-gray-400 text-sm italic">
                                    Tidak ada guru yang cocok.
                                </div>
                            </div>

                            {{-- Modal footer --}}
                            <div class="px-5 py-3 border-t border-gray-100 shrink-0 flex justify-end">
                                <button @click="modalOpen = false"
                                    class="px-5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-sm font-bold text-gray-700 transition-colors">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-16 text-center">
                    <p class="text-5xl mb-4">📋</p>
                    <p class="text-gray-500 font-semibold mb-2">Belum ada ujian UKK pada tahun pelajaran aktif</p>
                    <a href="{{ route('kaprodi.ukk.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-500 text-white font-bold rounded-xl hover:bg-orange-400 transition-all text-sm">
                        + Buat Ujian UKK
                    </a>
                </div>
            @endforelse

        </div>
    </div>

    @push('scripts')
    <script>
    const ALL_GURU = @json($guruKelas);

    function pengujiIndex() {
        return {
            toasts: [], toastSeq: 0,
            init() {},
            toast(msg, type = 'success') {
                const id = ++this.toastSeq;
                this.toasts.push({ id, message: msg, type, visible: false });
                this.$nextTick(() => { const t = this.toasts.find(t => t.id === id); if (t) t.visible = true; });
                setTimeout(() => this.removeToast(id), type === 'success' ? 3500 : 5000);
            },
            removeToast(id) {
                const t = this.toasts.find(t => t.id === id); if (t) t.visible = false;
                setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 400);
            },
        };
    }

    function ujianCard(ujianId, initialPenguji, isBilingual = false) {
        return {
            ujianId,
            penguji: initialPenguji,
            modalOpen: false,
            search: '',
            assigning: false,
            selectedPeran: isBilingual ? 'pengetahuan' : 'semua',

            get filteredGuru() {
                const q = this.search.toLowerCase().trim();
                if (!q) return ALL_GURU;
                return ALL_GURU.filter(g => g.nama.toLowerCase().includes(q) || g.kode.toLowerCase().includes(q));
            },

            isAssigned(userId) {
                return this.penguji.some(p => p.id === userId);
            },

            openModal() {
                this.search = '';
                this.selectedPeran = isBilingual ? 'pengetahuan' : 'semua';
                this.modalOpen = true;
            },

            async assignPenguji(guru) {
                if (this.isAssigned(guru.id) || this.assigning) return;
                this.assigning = true;
                try {
                    const res = await fetch('/kaprodi/ukk/penguji/' + this.ujianId, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ user_id: guru.id, peran: this.selectedPeran }),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        window.dispatchEvent(new CustomEvent('show-toast', {detail:{msg: data.message || 'Gagal.', type:'error'}}));
                        return;
                    }
                    this.penguji.push(data.penguji);
                    window.dispatchEvent(new CustomEvent('show-toast', {detail:{msg: data.message, type:'success'}}));
                } catch {
                    window.dispatchEvent(new CustomEvent('show-toast', {detail:{msg:'Koneksi gagal.', type:'error'}}));
                } finally {
                    this.assigning = false;
                }
            },

            async changePeran(pengujiItem, newPeran) {
                try {
                    const res = await fetch('/kaprodi/ukk/penguji/' + this.ujianId + '/' + pengujiItem.id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ peran: newPeran }),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        window.dispatchEvent(new CustomEvent('show-toast', {detail:{msg: data.message || 'Gagal mengubah peran.', type:'error'}}));
                        return;
                    }
                    pengujiItem.peran = newPeran;
                    window.dispatchEvent(new CustomEvent('show-toast', {detail:{msg: data.message, type:'success'}}));
                } catch {
                    window.dispatchEvent(new CustomEvent('show-toast', {detail:{msg:'Koneksi gagal.', type:'error'}}));
                }
            },

            async removePenguji(userId) {
                if (!confirm('Hapus penguji ini dari ujian?')) return;
                try {
                    const res = await fetch('/kaprodi/ukk/penguji/' + this.ujianId + '/' + userId, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        window.dispatchEvent(new CustomEvent('show-toast', {detail:{msg: data.message || 'Gagal.', type:'error'}}));
                        return;
                    }
                    this.penguji = this.penguji.filter(p => p.id !== userId);
                    window.dispatchEvent(new CustomEvent('show-toast', {detail:{msg: data.message, type:'success'}}));
                } catch {
                    window.dispatchEvent(new CustomEvent('show-toast', {detail:{msg:'Koneksi gagal.', type:'error'}}));
                }
            },
        };
    }
    </script>
    @endpush
</x-app-layout>
