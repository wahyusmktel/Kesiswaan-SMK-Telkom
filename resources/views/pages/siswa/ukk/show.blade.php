<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('siswa.ukk.index') }}"
                class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-lg text-gray-800 leading-tight">{{ $ujian->nama_ujian }}</h2>
                <p class="text-xs text-gray-500">Lembar Jawaban Soal Esai Pengetahuan UKK</p>
            </div>
        </div>
    </x-slot>

    <div x-data="siswaUkkForm()" x-init="init()" class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Info Card --}}
            <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center text-xl font-black shadow-md shadow-indigo-100 shrink-0">
                        ✍️
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-black px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {{ $ujian->jurusan }}
                            </span>
                            @if($ujian->tahunPelajaran)
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-lg bg-gray-100 text-gray-600">
                                    Tahun Pelajaran {{ $ujian->tahunPelajaran->tahun }}
                                </span>
                            @endif
                        </div>
                        <h3 class="text-base font-black text-gray-900 mt-1">
                            {{ $siswa->nama_lengkap }} &middot; <span class="text-gray-500 font-normal">NIS: {{ $siswa->nis }}</span>
                        </h3>
                    </div>
                </div>

                {{-- Action save button and status --}}
                <div class="flex items-center gap-3 shrink-0">
                    <span x-show="lastSavedText" class="text-xs text-emerald-600 font-semibold bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl flex items-center gap-1.5" x-text="'✓ ' + lastSavedText"></span>
                    <button @click="simpan()" :disabled="saving"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-indigo-100 disabled:opacity-50">
                        <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="saving ? 'Menyimpan…' : '💾 Simpan Jawaban'"></span>
                    </button>
                </div>
            </div>

            {{-- Petunjuk Pengisian --}}
            <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-4 text-xs text-blue-900 flex items-start gap-3">
                <span class="text-base shrink-0">💡</span>
                <div class="leading-relaxed">
                    <strong>Petunjuk:</strong> Jawablah pertanyaan esai di bawah ini dengan jelas dan lengkap. Anda dapat menekan tombol <strong>"Simpan Jawaban"</strong> sewaktu-waktu agar draft jawaban tersimpan di sistem. Jawaban Anda akan dinilai langsung oleh Penguji UKK.
                </div>
            </div>

            @if(!empty($hasBilingualPart))
                <div class="bg-purple-50/80 border border-purple-200/90 rounded-2xl p-4 text-xs text-purple-950 flex items-start gap-3 shadow-sm">
                    <span class="text-base shrink-0">🗣️</span>
                    <div class="leading-relaxed">
                        <strong class="font-black text-purple-900">Catatan Bagian Bilingual:</strong> Ujian UKK Anda juga mencakup <strong>Penilaian Wawancara Lisan Bilingual (English)</strong> yang akan diujikan dan dinilai secara tatap muka langsung oleh Guru Penguji Bahasa Inggris. Butir-butir soal di bawah ini adalah bagian <strong>Esai Tertulis</strong> yang dikerjakan secara mandiri.
                    </div>
                </div>
            @endif

            {{-- Instrumen Sections --}}
            @php $globalNo = 1; @endphp
            @foreach($ujian->instrumens as $ins)
                @if($ins->soalPengetahuan->isNotEmpty())
                <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
                    {{-- Instrumen Header --}}
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-indigo-50/40 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-xs">
                                {{ $loop->iteration }}
                            </span>
                            <div>
                                <h4 class="font-black text-gray-900 text-sm">{{ $ins->nama_instrumen }}</h4>
                                <p class="text-[11px] text-gray-500">Bobot Aspek Pengetahuan: {{ $ins->bobot_pengetahuan }}%</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-gray-400">
                            {{ $ins->soalPengetahuan->count() }} Soal Esai
                        </span>
                    </div>

                    {{-- Soal List --}}
                    <div class="p-6 space-y-6">
                        @foreach($ins->soalPengetahuan as $soal)
                        @php
                            $st = $statusMap[$soal->id] ?? null;
                            $currentNo = $globalNo++;
                        @endphp
                        <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200/80 hover:border-indigo-200 transition-all space-y-3">
                            {{-- Soal Header --}}
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-lg bg-slate-800 text-white font-black text-xs flex items-center justify-center shrink-0 mt-0.5">
                                        {{ $currentNo }}
                                    </span>
                                    <p class="font-bold text-gray-900 text-sm sm:text-base leading-relaxed">
                                        {{ $soal->pertanyaan }}
                                    </p>
                                </div>

                                {{-- Status Badge --}}
                                <div class="shrink-0">
                                    @if($st && $st['is_graded'])
                                        @if($st['nilai'] === 1)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                ✓ Dinilai: Benar
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black bg-rose-100 text-rose-800 border border-rose-300">
                                                ✗ Dinilai: Salah
                                            </span>
                                        @endif
                                    @else
                                        <template x-if="jawaban[{{ $soal->id }}] && jawaban[{{ $soal->id }}].trim().length > 0">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                                ✓ Sudah Diisi
                                            </span>
                                        </template>
                                        <template x-if="!jawaban[{{ $soal->id }}] || jawaban[{{ $soal->id }}].trim().length === 0">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-500">
                                                Belum Diisi
                                            </span>
                                        </template>
                                    @endif
                                </div>
                            </div>

                            {{-- Textarea Jawaban Siswa --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                    Tuliskan Jawaban Anda:
                                </label>
                                <textarea
                                    x-model="jawaban[{{ $soal->id }}]"
                                    @input="isDirty = true"
                                    rows="4"
                                    placeholder="Ketikkan jawaban esai Anda secara mendalam dan jelas di sini…"
                                    class="w-full text-sm text-gray-800 bg-white border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-xl p-3.5 leading-relaxed resize-y transition-all"
                                    :disabled="{{ ($st && $st['is_graded']) ? 'true' : 'false' }}"
                                ></textarea>
                                <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1 px-1">
                                    <span x-text="(jawaban[{{ $soal->id }}] ? jawaban[{{ $soal->id }}].length : 0) + ' karakter'"></span>
                                    @if($st && $st['waktu_submit'])
                                        <span>Terakhir disimpan: {{ $st['waktu_submit'] }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Catatan Penguji jika ada --}}
                            @if($st && !empty($st['catatan']))
                            <div class="mt-2 p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900">
                                <span class="font-bold flex items-center gap-1">💬 Catatan Penguji:</span>
                                <p class="mt-0.5 text-gray-700 italic">{{ $st['catatan'] }}</p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach

            {{-- Bottom Save Card --}}
            <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h4 class="font-bold text-gray-800 text-sm">Selesai Menjawab Soal?</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Pastikan seluruh soal esai telah terjawab sebelum penguji melakukan penilaian.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('siswa.ukk.index') }}"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition-colors">
                        Kembali ke Daftar
                    </a>
                    <button @click="simpan()" :disabled="saving"
                        class="inline-flex items-center gap-2 px-7 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold rounded-xl text-xs transition-all shadow-md shadow-indigo-100 disabled:opacity-50">
                        <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="saving ? 'Menyimpan…' : '💾 Simpan Jawaban'"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- Toast Notification --}}
    <div id="toast-siswa" class="fixed bottom-6 right-6 z-50 transform transition-all duration-300 translate-y-24 opacity-0 pointer-events-none">
        <div class="bg-gray-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 text-sm font-semibold">
            <span id="toast-icon">✓</span>
            <span id="toast-msg">Jawaban berhasil disimpan.</span>
        </div>
    </div>

    @push('scripts')
    <script>
    function siswaUkkForm() {
        return {
            jawaban: @json($jawabanMap),
            saving: false,
            isDirty: false,
            lastSavedText: '',

            init() {
                // Warning on leaving if dirty
                window.addEventListener('beforeunload', (e) => {
                    if (this.isDirty) {
                        e.preventDefault();
                        e.returnValue = '';
                    }
                });
            },

            async simpan() {
                this.saving = true;
                const url = '{{ route("siswa.ukk.simpan", $ujian->id) }}';

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type':     'application/json',
                            'Accept':           'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            jawaban: this.jawaban,
                        }),
                    });

                    const data = await res.json();
                    if (res.ok) {
                        this.isDirty = false;
                        this.lastSavedText = 'Tersimpan ' + (data.waktu || '');
                        this.showToast('✓ ' + (data.message || 'Jawaban berhasil disimpan.'));
                    } else {
                        this.showToast('❌ Gagal menyimpan jawaban. Silakan coba lagi.');
                    }
                } catch {
                    this.showToast('❌ Terjadi kesalahan jaringan.');
                } finally {
                    this.saving = false;
                }
            },

            showToast(msg) {
                const el = document.getElementById('toast-siswa');
                const msgEl = document.getElementById('toast-msg');
                if (!el || !msgEl) return;
                msgEl.innerText = msg;
                el.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
                el.classList.add('translate-y-0', 'opacity-100');
                setTimeout(() => {
                    el.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
                    el.classList.remove('translate-y-0', 'opacity-100');
                }, 3000);
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
