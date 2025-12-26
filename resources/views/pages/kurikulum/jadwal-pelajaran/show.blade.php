<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Editor Jadwal: <span class="text-indigo-600">{{ $rombel->kelas->nama_kelas }}</span>
            </h2>
            <span
                class="text-sm font-bold text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200 shadow-sm">
                T.A. {{ $rombel->tahunPelajaran->tahun ?? '-' }} ({{ $rombel->tahunPelajaran->semester ?? '' }})
            </span>
        </div>
    </x-slot>

    <div class="py-20 w-full" x-data="jadwalEditor()">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <form action="{{ route('kurikulum.jadwal-pelajaran.store', $rombel->id) }}" method="POST">
                @csrf

                <div
                    :class="isSticky ? 'sticky top-20 z-30 shadow-xl border-indigo-100 ring-4 ring-indigo-50/50 bg-white/95 backdrop-blur-sm' : 'relative bg-white border-gray-200 shadow-md'"
                    class="rounded-2xl p-6 mb-8 transition-all duration-500 ease-in-out border">
                    
                    <div class="flex items-center justify-between mb-6 border-b border-gray-50 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </div>
                            <div>
                                <h3 class="font-black text-gray-900 tracking-tight">Konfigurasi Pengisian</h3>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Panel Kontrol Utama</p>
                            </div>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer group">
                            <input type="checkbox" x-model="isSticky" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <span class="ms-3 text-xs font-bold text-gray-500 group-hover:text-indigo-600 transition-colors uppercase tracking-widest">Panel Melayang</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end">

                        <div class="lg:col-span-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">1. Pilih Mata
                                Pelajaran</label>
                            <div class="relative">
                                <select x-model.number="selectedMapelId"
                                    class="block w-full pl-10 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-11 bg-gray-50 focus:bg-white transition-colors">
                                    <option value="">-- Pilih Mapel --</option>
                                    <template x-for="mapel in availableMapel" :key="mapel.id">
                                        <option :value="mapel.id"
                                            x-text="mapel.nama_mapel + ' (Sisa ' + mapel.sisa_jam + ' JP)'"></option>
                                    </template>
                                </select>

                                {{-- <template x-if="mataPelajaran.length === 0">
                                    <p class="text-xs text-red-500 mt-1">
                                        * Belum ada mata pelajaran untuk kelas ini. Silakan atur di Data Kurikulum.
                                    </p>
                                </template> --}}

                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">2. Pilih Guru
                                Pengajar</label>
                            <div class="relative">
                                <select x-model.number="selectedGuruId"
                                    class="block w-full pl-10 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-11 bg-gray-50 focus:bg-white transition-colors">
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach ($guru as $g)
                                        <option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-4 flex justify-end gap-3">
                            <a href="{{ route('kurikulum.jadwal-pelajaran.index') }}"
                                class="h-11 px-6 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                                Kembali
                            </a>
                            <button type="submit"
                                class="h-11 px-6 inline-flex items-center justify-center rounded-lg bg-indigo-600 text-white font-bold shadow-lg hover:bg-indigo-500 hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Simpan Jadwal
                            </button>
                        </div>
                    </div>

                    <div
                        class="mt-4 flex items-center gap-3 text-xs bg-indigo-50/50 p-4 rounded-xl border border-indigo-100/50 group">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 animate-pulse">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-gray-600 leading-relaxed font-medium">
                            <strong class="text-indigo-700">Cara Pengisian:</strong> 
                            1. Pilih <span class="font-bold underline decoration-indigo-300">Mata Pelajaran</span> & <span class="font-bold underline decoration-indigo-300">Guru</span> di panel ini. 
                            2. Geser layar ke bawah dan klik pada <span class="font-bold text-indigo-700">Kotak Jadwal</span> yang diinginkan. Klk lagi untuk menghapus.
                            3. Gunakan fitur <span class="font-bold italic text-indigo-700">"Panel Melayang"</span> di pojok kanan atas untuk mempermudah navigasi saat jadwal sangat panjang.
                        </span>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden mb-20">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th
                                        class="border-b border-r border-gray-200 p-4 w-32 font-bold text-center bg-gray-50 sticky left-0 z-20 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                        Waktu
                                    </th>
                                    @foreach ($days as $day)
                                        <th
                                            class="border-b border-gray-200 p-4 font-bold text-center min-w-[160px] {{ $day == 'Jumat' ? 'bg-emerald-50/50 text-emerald-700' : '' }}">
                                            {{ $day }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($jamSlots as $slot)
                                    @php
                                        // Ambil contoh data untuk kolom kiri (pilih yang ada/default)
                                        $repSlot = $jamLookup["{$slot->jam_ke}-Senin"] ?? $jamLookup["{$slot->jam_ke}-Jumat"] ?? null;
                                    @endphp
                                    <tr>
                                        <td
                                            class="border-r border-gray-100 p-4 bg-gray-50/80 sticky left-0 z-20 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] backdrop-blur-sm">
                                            <div class="flex flex-col items-center justify-center h-full">
                                                <span
                                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Jam
                                                    Ke-{{ $slot->jam_ke }}</span>
                                                @if($repSlot)
                                                    <span class="font-mono text-gray-800 font-bold text-lg">
                                                        {{ \Carbon\Carbon::parse($repSlot->jam_mulai)->format('H:i') }}
                                                    </span>
                                                    <span class="text-gray-300 text-xs my-0.5">-</span>
                                                    <span class="font-mono text-gray-500 font-medium">
                                                        {{ \Carbon\Carbon::parse($repSlot->jam_selesai)->format('H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        @foreach ($days as $day)
                                            @php
                                                $currentSlot = $jamLookup["{$slot->jam_ke}-{$day}"] ?? null;
                                                $isActivity = false;
                                                $activityName = '';
                                                
                                                if ($currentSlot && $currentSlot->tipe_kegiatan) {
                                                    if (in_array($currentSlot->tipe_kegiatan, ['istirahat', 'sholawat_pagi', 'ishoma'])) {
                                                        $isActivity = true;
                                                    } elseif ($currentSlot->tipe_kegiatan == 'upacara' && $day == 'Senin') {
                                                        $isActivity = true;
                                                    } elseif ($currentSlot->tipe_kegiatan == 'kegiatan_4r' && $day == 'Jumat') {
                                                        $isActivity = true;
                                                    }
                                                    $activityName = str_replace('_', ' ', $currentSlot->tipe_kegiatan);
                                                }
                                            @endphp

                                            <td class="border-r border-b border-gray-100 p-1 align-top h-32 relative group transition-all duration-200 {{ !$currentSlot ? 'bg-gray-100/50' : ($isActivity ? 'bg-amber-50/50' : 'bg-white hover:bg-gray-50/50') }}"
                                                :class="getCellClass('{{ $day }}', {{ $slot->jam_ke }})"
                                                data-cell="{{ $day }}-{{ $slot->jam_ke }}">
                                                
                                                @if(!$currentSlot)
                                                    <div class="flex items-center justify-center h-full opacity-25">
                                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                    </div>
                                                @elseif ($isActivity)
                                                    <div class="flex flex-col justify-center items-center h-full w-full p-2 select-none">
                                                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mb-1 shadow-sm border border-amber-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        </div>
                                                        <span class="text-[9px] font-black uppercase tracking-widest text-amber-700 text-center leading-tight">
                                                            {{ $activityName }}
                                                        </span>
                                                        <span class="text-[8px] font-mono text-amber-500 mt-0.5">
                                                            {{ \Carbon\Carbon::parse($currentSlot->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($currentSlot->jam_selesai)->format('H:i') }}
                                                        </span>
                                                        @if($currentSlot->keterangan)
                                                            <span class="text-[8px] text-amber-400 mt-1 italic text-center line-clamp-1 truncate px-1">{{ $currentSlot->keterangan }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <label
                                                        class="flex flex-col justify-center items-center h-full w-full cursor-pointer relative z-0 p-2 select-none">

                                                        <input type="checkbox" class="absolute opacity-0 w-0 h-0"
                                                            :disabled="isSlotDisabled('{{ $day }}', {{ $slot->jam_ke }})"
                                                            @change="toggleSlot('{{ $day }}', {{ $slot->jam_ke }}, $event)">

                                                        <div x-show="!jadwal['{{ $day }}-{{ $slot->jam_ke }}']"
                                                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <div
                                                                class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center shadow-md transform scale-50 group-hover:scale-100 transition-transform duration-200">
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                                </svg>
                                                            </div>
                                                        </div>

                                                        <div x-show="jadwal['{{ $day }}-{{ $slot->jam_ke }}']"
                                                            class="text-center w-full relative z-10">
                                                            <div class="font-bold text-sm leading-tight mb-1.5 break-words"
                                                                x-text="getMapelName('{{ $day }}', {{ $slot->jam_ke }})">
                                                            </div>
                                                            <div class="text-[10px] uppercase font-bold tracking-wide bg-white/60 px-2 py-1 rounded inline-block shadow-sm backdrop-blur-sm"
                                                                x-text="getGuruName('{{ $day }}', {{ $slot->jam_ke }})">
                                                            </div>
                                                        </div>

                                                        <template
                                                            x-if="jadwal['{{ $day }}-{{ $slot->jam_ke }}']">
                                                            <div>
                                                                <input type="hidden"
                                                                    name="jadwal[{{ $day }}][{{ $slot->jam_ke }}][mata_pelajaran_id]"
                                                                    :value="jadwal['{{ $day }}-{{ $slot->jam_ke }}']
                                                                        .mata_pelajaran_id">
                                                                <input type="hidden"
                                                                    name="jadwal[{{ $day }}][{{ $slot->jam_ke }}][master_guru_id]"
                                                                    :value="jadwal['{{ $day }}-{{ $slot->jam_ke }}']
                                                                        .master_guru_id">
                                                            </div>
                                                        </template>
                                                    </label>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function jadwalEditor() {
            return {
                mataPelajaran: @json($mataPelajaran),
                guru: @json($guru),
                jadwal: @json($jadwalFormatted),

                selectedMapelId: '',
                selectedGuruId: '',
                isSticky: true,

                init() {
                    // Init logic jika diperlukan
                },

                get availableMapel() {
                    // Filter mapel: Tampilkan jika sisa jam > 0 ATAU sedang dipilih
                    return this.mataPelajaran.filter(mapel => mapel.sisa_jam > 0 || this.selectedMapelId == mapel.id);
                },

                toggleSlot(day, jamKe, event) {
                    const key = `${day}-${jamKe}`;
                    const slotData = this.jadwal[key];

                    if (slotData) {
                        // HAPUS JADWAL (Klik pada slot terisi)
                        const mapelId = slotData.mata_pelajaran_id;
                        // Kembalikan sisa jam
                        const mapel = this.mataPelajaran.find(m => m.id == mapelId);
                        if (mapel) mapel.sisa_jam++;

                        this.jadwal[key] = null;
                        event.target.checked = false;
                    } else {
                        // TAMBAH JADWAL (Klik pada slot kosong)
                        if (!this.selectedMapelId || !this.selectedGuruId) {
                            alert('Silakan pilih Mata Pelajaran dan Guru terlebih dahulu di panel atas.');
                            event.target.checked = false;
                            return;
                        }

                        const selectedMapel = this.mataPelajaran.find(m => m.id == this.selectedMapelId);

                        if (selectedMapel.sisa_jam <= 0) {
                            alert('Kuota jam untuk mata pelajaran ini sudah habis.');
                            event.target.checked = false;
                            return;
                        }

                        // Simpan ke state
                        this.jadwal[key] = {
                            mata_pelajaran_id: this.selectedMapelId,
                            master_guru_id: this.selectedGuruId,
                            mata_pelajaran: selectedMapel,
                            guru: this.guru.find(g => g.id == this.selectedGuruId)
                        };

                        // Kurangi sisa jam
                        selectedMapel.sisa_jam--;
                    }
                },

                isSlotDisabled(day, jamKe) {
                    // Selalu enable checkbox agar bisa diklik untuk memicu alert atau toggle
                    return false;
                },

                getMapelName(day, jamKe) {
                    return this.jadwal[`${day}-${jamKe}`]?.mata_pelajaran?.nama_mapel || '';
                },

                getGuruName(day, jamKe) {
                    return this.jadwal[`${day}-${jamKe}`]?.guru?.nama_lengkap || '';
                },

                getCellClass(day, jamKe) {
                    const data = this.jadwal[`${day}-${jamKe}`];
                    if (!data) return ''; // Kosong

                    // Warna-warni pastel berdasarkan ID Mapel
                    const colors = [
                        'bg-blue-100 border-blue-200 text-blue-900',
                        'bg-emerald-100 border-emerald-200 text-emerald-900',
                        'bg-amber-100 border-amber-200 text-amber-900',
                        'bg-violet-100 border-violet-200 text-violet-900',
                        'bg-rose-100 border-rose-200 text-rose-900',
                        'bg-cyan-100 border-cyan-200 text-cyan-900',
                        'bg-fuchsia-100 border-fuchsia-200 text-fuchsia-900',
                        'bg-lime-100 border-lime-200 text-lime-900',
                    ];

                    return colors[data.mata_pelajaran_id % colors.length] + ' shadow-sm border-b-2';
                }
            }
        }
    </script>
</x-app-layout>
