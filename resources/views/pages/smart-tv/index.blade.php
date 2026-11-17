<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Layanan Smart TV &amp; Mading Digital</span>
                </h2>
                <p class="text-xs text-gray-500 mt-1">Kelola tayangan QR Code Presensi, Mading Informasi, Slide Pelajaran, dan Running Text Ticker</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('tv.jadwal') }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Buka Layar Smart TV</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full" x-data="{
        activeTab: 'media',
        isUploadModalOpen: false,
        isEditModalOpen: false,
        editData: {},
        previewUrl: null,
        previewType: null,
        openEdit(item) {
            this.editData = item;
            this.isEditModalOpen = true;
        },
        openPreview(url, type) {
            this.previewUrl = url;
            this.previewType = type;
        },
        closePreview() {
            this.previewUrl = null;
            this.previewType = null;
        }
    }">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ══ KARTU RINGKASAN & STATUS AKTIF HARI INI ══ --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 text-white rounded-2xl p-5 shadow-lg relative overflow-hidden">
                    <div class="relative z-10">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-200">Waktu Server TV</span>
                        <div class="text-2xl font-black mt-1 font-mono">{{ $now->format('H:i') }} <span class="text-xs font-normal text-indigo-200">WIB</span></div>
                        <p class="text-xs text-indigo-200 mt-1">{{ $now->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-semibold block">Tayangan Eksklusif Aktif</span>
                        <div class="text-xl font-extrabold text-gray-900 mt-0.5">
                            {{ $activeExclusiveCount }} <span class="text-xs font-medium text-gray-400">Media</span>
                        </div>
                        <span class="text-[11px] {{ $activeExclusiveCount > 0 ? 'text-emerald-600 font-bold' : 'text-gray-400' }}">
                            {{ $activeExclusiveCount > 0 ? '● Layar Takeover (QR/Khusus)' : '○ Mode Jadwal Reguler' }}
                        </span>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-semibold block">Mading/Slide Kombinasi</span>
                        <div class="text-xl font-extrabold text-gray-900 mt-0.5">
                            {{ $activeKombinasiCount }} <span class="text-xs font-medium text-gray-400">Media</span>
                        </div>
                        <span class="text-[11px] text-gray-400">Ikut berputar dalam slide</span>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-semibold block">Running Text Ticker</span>
                        <div class="text-xl font-extrabold text-gray-900 mt-0.5">
                            {{ $tickers->where('is_active', true)->count() }} <span class="text-xs font-medium text-gray-400">Aktif</span>
                        </div>
                        <span class="text-[11px] text-gray-400">Dari {{ $tickers->count() }} teks tersimpan</span>
                    </div>
                </div>
            </div>

            {{-- ══ NAVIGATION TAB ══ --}}
            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                <div class="flex items-center gap-3">
                    <button type="button" @click="activeTab = 'media'"
                        :class="activeTab === 'media' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <span>Media Tayang Terjadwal (QR &amp; Mading)</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20">{{ $mediaList->count() }}</span>
                    </button>
                    <button type="button" @click="activeTab = 'ticker'"
                        :class="activeTab === 'ticker' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        <span>Running Text (Ticker Informasi)</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20">{{ $tickers->count() }}</span>
                    </button>
                </div>

                <div x-show="activeTab === 'media'">
                    <button type="button" @click="isUploadModalOpen = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Unggah Media Baru</span>
                    </button>
                </div>
            </div>

            {{-- ══ TAB 1: MEDIA TAYANG TERJADWAL ══ --}}
            <div x-show="activeTab === 'media'" class="space-y-4">
                @if($mediaList->isEmpty())
                    <div class="bg-white border-2 border-dashed border-gray-200 rounded-3xl p-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-800">Belum Ada Media Tayang Terjadwal</h3>
                        <p class="text-xs text-gray-500 max-w-md mx-auto mt-1">Unggah file PDF atau Gambar yang berisikan QR Presensi, Mading Digital, atau Pengumuman untuk dijadwalkan otomatis pada jam tertentu.</p>
                        <button type="button" @click="isUploadModalOpen = true"
                            class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Unggah Media Sekarang</span>
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($mediaList as $item)
                            @php
                                $isActiveNow = $item->isScheduleActiveNow($now);
                            @endphp
                            <div class="bg-white rounded-2xl border {{ $isActiveNow ? 'border-indigo-400 ring-2 ring-indigo-200' : 'border-gray-200' }} shadow-xs overflow-hidden flex flex-col justify-between transition hover:shadow-md">
                                <div>
                                    {{-- Header Card --}}
                                    <div class="p-4 border-b border-gray-100 flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-2.5">
                                            @if($item->tipe === 'qr_hadir')
                                                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 font-bold">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                                </span>
                                            @elseif($item->tipe === 'qr_pulang')
                                                <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 font-bold">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                                </span>
                                            @elseif($item->tipe === 'mading')
                                                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 font-bold">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                                </span>
                                            @else
                                                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 font-bold">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                                </span>
                                            @endif
                                            <div>
                                                <h4 class="font-bold text-sm text-gray-900 leading-tight line-clamp-1">{{ $item->judul }}</h4>
                                                <span class="text-[11px] font-semibold text-gray-400">{{ $item->tipe_label }}</span>
                                            </div>
                                        </div>

                                        {{-- Badge Mode Tayang --}}
                                        @if($item->mode_tayang === 'eksklusif')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700 tracking-wider uppercase">
                                                Eksklusif (Takeover)
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-700 tracking-wider uppercase">
                                                Kombinasi (Slide)
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Body Card Preview & Detail --}}
                                    <div class="p-4 space-y-3">
                                        {{-- Preview Box --}}
                                        <div class="w-full h-36 bg-gray-900 rounded-xl overflow-hidden relative group cursor-pointer flex items-center justify-center"
                                            @click="openPreview('{{ $item->file_url }}', '{{ $item->file_type }}')">
                                            @if($item->file_type === 'image')
                                                <img src="{{ $item->file_url }}" alt="{{ $item->judul }}" class="w-full h-full object-contain">
                                            @else
                                                <div class="flex flex-col items-center justify-center text-gray-300 space-y-1">
                                                    <svg class="w-10 h-10 text-rose-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                                    <span class="text-[11px] font-bold">Dokumen PDF (Klik Preview)</span>
                                                </div>
                                            @endif
                                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Lihat Media</span>
                                            </div>
                                        </div>

                                        {{-- Jadwal Info --}}
                                        <div class="space-y-1.5 text-xs">
                                            <div class="flex items-center justify-between text-gray-600">
                                                <span class="flex items-center gap-1.5 text-gray-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Jam Tayang:</span>
                                                </span>
                                                <span class="font-bold text-gray-900 font-mono">{{ $item->jam_mulai_formatted }} - {{ $item->jam_selesai_formatted }} WIB</span>
                                            </div>

                                            <div class="flex items-center justify-between text-gray-600">
                                                <span class="flex items-center gap-1.5 text-gray-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span>Hari:</span>
                                                </span>
                                                <span class="font-semibold text-gray-700 text-[11px]">
                                                    {{ !empty($item->hari) ? implode(', ', $item->hari) : 'Setiap Hari' }}
                                                </span>
                                            </div>

                                            @if($item->mode_tayang === 'kombinasi')
                                                <div class="flex items-center justify-between text-gray-600">
                                                    <span class="text-gray-400">Durasi Slide:</span>
                                                    <span class="font-bold text-gray-700">{{ $item->durasi_detik }} detik</span>
                                                </div>
                                            @endif

                                            @if($item->keterangan)
                                                <p class="text-[11px] text-gray-500 italic pt-1 border-t border-gray-100">{{ $item->keterangan }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer Actions --}}
                                <div class="p-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-2">
                                    <form action="{{ route('smart-tv.media.toggle', $item) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $item->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-gray-200 text-gray-600 hover:bg-gray-300' }}">
                                            <span class="w-2 h-2 rounded-full {{ $item->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                            <span>{{ $item->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                                        </button>
                                    </form>

                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="openEdit({{ json_encode($item) }})"
                                            class="p-1.5 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-white transition" title="Edit Jadwal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        <form action="{{ route('smart-tv.media.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus media ini dari jadwal tayang Smart TV?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:text-rose-600 hover:bg-white transition" title="Hapus Media">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ══ TAB 2: RUNNING TEXT (TICKER INFORMASI) ══ --}}
            <div x-show="activeTab === 'ticker'" class="space-y-6">
                {{-- Form Tambah Ticker --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs">
                    <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Teks Berjalan Baru</span>
                    </h3>
                    <form action="{{ route('smart-tv.ticker.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <div class="flex-1">
                            <input type="text" name="konten" required maxlength="500"
                                placeholder="Tuliskan pengumuman atau info penting untuk berjalan di layar TV..."
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-xs">
                        </div>
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            <span>Tayangkan</span>
                        </button>
                    </form>
                </div>

                {{-- Tabel Ticker --}}
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="py-3 px-4">Teks Berjalan</th>
                                <th class="py-3 px-4 w-32">Status</th>
                                <th class="py-3 px-4 w-40">Dibuat Oleh</th>
                                <th class="py-3 px-4 w-28 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tickers as $ticker)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="py-3 px-4 font-medium text-gray-900 leading-relaxed">
                                        {{ $ticker->konten }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <form action="{{ route('smart-tv.ticker.toggle', $ticker) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $ticker->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $ticker->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                                <span>{{ $ticker->is_active ? 'Aktif Tayang' : 'Non-Aktif' }}</span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-3 px-4 text-gray-500 text-[11px]">
                                        {{ $ticker->creator?->name ?? 'Admin' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <form action="{{ route('smart-tv.ticker.destroy', $ticker) }}" method="POST" onsubmit="return confirm('Hapus running text ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400 text-xs italic">
                                        Belum ada teks berjalan yang ditambahkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- ══ MODAL UNGGAH MEDIA BARU ══ --}}
        <div x-show="isUploadModalOpen" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl overflow-hidden border border-gray-100"
                @click.away="isUploadModalOpen = false">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-violet-600 text-white flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm">Unggah Media Tayang Smart TV</h3>
                        <p class="text-[11px] text-indigo-100">Upload PDF QR Code Presensi, Mading, atau Pamflet</p>
                    </div>
                    <button type="button" @click="isUploadModalOpen = false" class="text-white/80 hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('smart-tv.media.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Judul / Label Tayangan *</label>
                        <input type="text" name="judul" required placeholder="Contoh: QR Presensi Hadir Pagi Siswa"
                            class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Kategori Media *</label>
                            <select name="tipe" required class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="qr_hadir">QR Presensi Hadir</option>
                                <option value="qr_pulang">QR Presensi Pulang</option>
                                <option value="mading">Mading Digital</option>
                                <option value="pengumuman">Pengumuman Resmi</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Mode Penayangan *</label>
                            <select name="mode_tayang" required class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="eksklusif">Eksklusif (Takeover Fullscreen tanpa jadwal)</option>
                                <option value="kombinasi">Kombinasi (Disisipkan ke Slideshow Jadwal)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Berkas Media (PDF / JPG / PNG / WEBP) *</label>
                        <input type="file" name="file" required accept=".pdf,image/png,image/jpeg,image/webp"
                            class="w-full text-xs text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-xl p-1">
                        <p class="text-[10px] text-gray-400 mt-1">Maksimal 25 MB. Dokumen PDF akan dirender fullscreen dengan tajam dan jernih.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Jam Mulai Tayang *</label>
                            <input type="time" name="jam_mulai" required value="06:00"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Jam Selesai Tayang *</label>
                            <input type="time" name="jam_selesai" required value="08:00"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Hari Tayang (Biarkan kosong jika setiap hari)</label>
                        <div class="grid grid-cols-4 sm:grid-cols-7 gap-2 pt-1">
                            @foreach($days as $day)
                                <label class="flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="hari[]" value="{{ $day }}" class="rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="text-[11px]">{{ substr($day, 0, 3) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Durasi Slide (Detik)</label>
                            <input type="number" name="durasi_detik" min="5" max="300" value="20"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="text-[10px] text-gray-400">Khusus mode kombinasi slide</span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Urutan Prioritas</label>
                            <input type="number" name="urutan" min="0" max="999" value="0"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="text-[10px] text-gray-400">Nilai lebih kecil tampil lebih awal</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea name="keterangan" rows="2" placeholder="Catatan internal..."
                            class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="isUploadModalOpen = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                            Simpan &amp; Jadwalkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ══ MODAL EDIT MEDIA ══ --}}
        <div x-show="isEditModalOpen" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl overflow-hidden border border-gray-100"
                @click.away="isEditModalOpen = false">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-violet-600 text-white flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm">Edit Pengaturan Media Tayang</h3>
                        <p class="text-[11px] text-indigo-100" x-text="editData.judul"></p>
                    </div>
                    <button type="button" @click="isEditModalOpen = false" class="text-white/80 hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'{{ url('smart-tv/media') }}/' + editData.id" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Judul / Label Tayangan *</label>
                        <input type="text" name="judul" required x-model="editData.judul"
                            class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Kategori Media *</label>
                            <select name="tipe" required x-model="editData.tipe" class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="qr_hadir">QR Presensi Hadir</option>
                                <option value="qr_pulang">QR Presensi Pulang</option>
                                <option value="mading">Mading Digital</option>
                                <option value="pengumuman">Pengumuman Resmi</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Mode Penayangan *</label>
                            <select name="mode_tayang" required x-model="editData.mode_tayang" class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="eksklusif">Eksklusif (Takeover Fullscreen tanpa jadwal)</option>
                                <option value="kombinasi">Kombinasi (Disisipkan ke Slideshow Jadwal)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Ganti Berkas Media (Kosongkan jika tetap)</label>
                        <input type="file" name="file" accept=".pdf,image/png,image/jpeg,image/webp"
                            class="w-full text-xs text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-xl p-1">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Jam Mulai Tayang *</label>
                            <input type="time" name="jam_mulai" required :value="editData.jam_mulai ? editData.jam_mulai.substring(0,5) : '06:00'"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Jam Selesai Tayang *</label>
                            <input type="time" name="jam_selesai" required :value="editData.jam_selesai ? editData.jam_selesai.substring(0,5) : '08:00'"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Hari Tayang (Biarkan kosong jika setiap hari)</label>
                        <div class="grid grid-cols-4 sm:grid-cols-7 gap-2 pt-1">
                            @foreach($days as $day)
                                <label class="flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="hari[]" value="{{ $day }}"
                                        :checked="editData.hari && editData.hari.includes('{{ $day }}')"
                                        class="rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="text-[11px]">{{ substr($day, 0, 3) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Durasi Slide (Detik)</label>
                            <input type="number" name="durasi_detik" min="5" max="300" x-model="editData.durasi_detik"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Urutan Prioritas</label>
                            <input type="number" name="urutan" min="0" max="999" x-model="editData.urutan"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea name="keterangan" rows="2" x-model="editData.keterangan"
                            class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="isEditModalOpen = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                            Perbarui Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ══ MODAL PREVIEW MEDIA ══ --}}
        <div x-show="previewUrl" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
            <div class="bg-gray-900 rounded-3xl max-w-4xl w-full h-[85vh] shadow-2xl overflow-hidden flex flex-col relative border border-gray-800"
                @click.away="closePreview()">
                <div class="p-3 bg-gray-950 text-white flex items-center justify-between border-b border-gray-800">
                    <span class="text-xs font-bold text-gray-300 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Pratinjau Media Layar TV</span>
                    </span>
                    <button type="button" @click="closePreview()" class="text-gray-400 hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex-1 bg-black overflow-hidden flex items-center justify-center p-2">
                    <template x-if="previewType === 'image'">
                        <img :src="previewUrl" class="max-w-full max-h-full object-contain rounded-lg">
                    </template>
                    <template x-if="previewType === 'pdf'">
                        <iframe :src="previewUrl" class="w-full h-full rounded-lg border-0 bg-white"></iframe>
                    </template>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
