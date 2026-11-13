<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Pengaturan Periode &amp; Timeline Pilketos</h2>
                <p class="text-xs text-gray-500 mt-1">Konfigurasi status pemilu dan tahapan jadwal pemilihan calon ketua OSIS</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('public.osis.index') }}#pilketos" target="_blank" class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-1.5 transition-colors">
                    <span>Lihat di Web Publik</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- 1. Form Pengaturan Periode -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-6">
                <h3 class="font-bold text-lg text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                    <span>⚙️</span> Konfigurasi Periode Pemilihan
                </h3>

                <form action="{{ route('osis.pilketos.periode.update', $periode) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Pemilihan</label>
                            <input type="text" name="nama_periode" value="{{ old('nama_periode', $periode->nama_periode) }}" required
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tahun Pelajaran</label>
                            <input type="text" name="tahun_pelajaran" value="{{ old('tahun_pelajaran', $periode->tahun_pelajaran) }}" placeholder="Contoh: 2026/2027" required
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status Pemilihan</label>
                            <select name="status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 font-bold">
                                <option value="draft" {{ $periode->status === 'draft' ? 'selected' : '' }}>DRAFT (Persiapan Awal)</option>
                                <option value="aktif" {{ $periode->status === 'aktif' ? 'selected' : '' }}>AKTIF (Sosialisasi &amp; Kampanye)</option>
                                <option value="voting_berlangsung" {{ $periode->status === 'voting_berlangsung' ? 'selected' : '' }}>VOTING BERLANGSUNG (Bilik Suara Dibuka)</option>
                                <option value="selesai" {{ $periode->status === 'selesai' ? 'selected' : '' }}>SELESAI (Perhitungan &amp; Penetapan)</option>
                            </select>
                            <span class="text-[11px] text-gray-400 mt-1 block">Saat berstatus "Voting Berlangsung", bilik suara siap menerima scan barcode pemilih.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Mulai Voting</label>
                            <input type="datetime-local" name="tgl_mulai_voting" value="{{ old('tgl_mulai_voting', $periode->tgl_mulai_voting ? $periode->tgl_mulai_voting->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Deskripsi / Pengumuman Pemilu</label>
                            <textarea name="deskripsi" rows="3"
                                      class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-800">{{ old('deskripsi', $periode->deskripsi) }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-500/20">
                            Simpan Pengaturan Periode
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. Timeline & Tahapan Jadwal Pemilu -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                        <span>🗓️</span> Jadwal &amp; Timeline Tahapan Pemilu
                    </h3>
                </div>

                <!-- Form Tambah Tahapan Baru -->
                <form action="{{ route('osis.pilketos.timeline.store', $periode) }}" method="POST" class="p-5 rounded-2xl bg-gray-50 border border-gray-200 space-y-4">
                    @csrf
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider block">+ Tambah Tahapan Baru</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="lg:col-span-2">
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Judul Tahapan</label>
                            <input type="text" name="judul_tahap" placeholder="Contoh: Masa Kampanye Terbuka" required
                                   class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" required value="{{ date('Y-m-d') }}"
                                   class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai"
                                   class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800">
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Deskripsi Singkat</label>
                            <input type="text" name="deskripsi" placeholder="Keterangan tahapan"
                                   class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Status</label>
                            <select name="status_tahap" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800">
                                <option value="akan_datang">Akan Datang</option>
                                <option value="berlangsung">Sedang Berlangsung</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full py-2 bg-gray-900 hover:bg-black text-white rounded-xl text-xs font-bold transition-colors">
                                + Tambah ke Timeline
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Daftar Tahapan yang sudah ada -->
                <div class="space-y-3">
                    @forelse($timelines as $tl)
                        <div class="p-4 rounded-2xl border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-gray-300 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-700">
                                    {{ $loop->iteration }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-sm text-gray-800">{{ $tl->judul_tahap }}</h4>
                                    <div class="text-xs text-rose-600 font-medium">
                                        {{ $tl->tanggal_mulai->format('d M Y') }}
                                        @if($tl->tanggal_selesai) &bull; s/d {{ $tl->tanggal_selesai->format('d M Y') }} @endif
                                    </div>
                                    @if($tl->deskripsi)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $tl->deskripsi }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                    {{ $tl->status_tahap === 'berlangsung' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200 animate-pulse' : ($tl->status_tahap === 'selesai' ? 'bg-gray-100 text-gray-600' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $tl->status_tahap }}
                                </span>

                                <form action="{{ route('osis.pilketos.timeline.destroy', $tl) }}" method="POST" onsubmit="return confirm('Hapus tahapan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-6">Belum ada tahapan jadwal pemilu.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
