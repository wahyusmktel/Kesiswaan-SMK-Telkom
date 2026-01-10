<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Detail Keterlambatan</h2>
            <a href="{{ route('monitoring-keterlambatan.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Student Information Card --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200">
                        <div class="p-8 text-center border-b border-gray-100 bg-gray-50/50">
                            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-red-100 text-red-600 mb-4 border-4 border-white shadow-sm">
                                <span class="text-3xl font-black">{{ substr($keterlambatan->siswa->user->name, 0, 1) }}</span>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 leading-tight">{{ $keterlambatan->siswa->user->name }}</h3>
                            <p class="text-sm text-gray-500 font-medium">NIS: {{ $keterlambatan->siswa->nis }}</p>
                            <div class="mt-4 inline-flex items-center px-3 py-1 bg-red-50 text-red-700 rounded-full text-xs font-black uppercase tracking-wider border border-red-100 italic">
                                Terlambat
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none">Kelas</span>
                                <span class="text-sm font-black text-gray-700 leading-none">{{ $keterlambatan->siswa->rombels->first()?->kelas->nama_kelas ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none">Wali Kelas</span>
                                <span class="text-sm font-black text-gray-700 leading-none text-right">{{ $keterlambatan->siswa->rombels->first()?->waliKelas->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none">Total Terlambat</span>
                                <span class="text-sm font-black text-red-600 leading-none">{{ \App\Models\Keterlambatan::where('master_siswa_id', $keterlambatan->master_siswa_id)->count() }} Kali</span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Card --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200 p-6">
                        <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-red-500 rounded-full"></span>
                            Aksi Tersedia
                        </h4>
                        <div class="space-y-3">
                            <a href="{{ route('monitoring-keterlambatan.print-slip', $keterlambatan->id) }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-600 border border-transparent rounded-xl font-black text-sm text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 gap-2 shadow-lg shadow-red-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                Cetak Surat Izin
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Record Details Card --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200">
                        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="text-lg font-black text-gray-900">Kronologi Kedatangan</h3>
                            <span class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs font-mono font-bold text-gray-500">REF: #{{ str_pad($keterlambatan->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="p-8">
                            <div class="relative pl-8 border-l-2 border-red-100 space-y-12">
                                {{-- Security Entry --}}
                                <div class="relative">
                                    <div class="absolute -left-[41px] top-0 w-[18px] h-[18px] bg-white border-4 border-red-500 rounded-full z-10"></div>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg font-black text-gray-900">{{ $keterlambatan->waktu_dicatat_security->format('H:i') }}</span>
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-black uppercase tracking-widest border border-gray-200">
                                                Dicatat {{ $keterlambatan->security->hasRole('Security') ? 'Security' : 'Piket' }}
                                            </span>
                                        </div>
                                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                            <div class="flex items-start gap-4">
                                                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-400 font-black">S</div>
                                                <div>
                                                    <p class="text-sm font-black text-gray-900">{{ $keterlambatan->security->hasRole('Security') ? 'Petugas' : 'Pencatat' }}: {{ $keterlambatan->security->name }}</p>
                                                    <p class="text-sm text-gray-600 mt-2 leading-relaxed">
                                                        <span class="font-bold text-gray-400 uppercase text-[10px] tracking-widest block mb-1">Alasan Siswa:</span>
                                                        "{{ $keterlambatan->alasan_siswa }}"
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Piket Verification --}}
                                <div class="relative">
                                    <div class="absolute -left-[41px] top-0 w-[18px] h-[18px] bg-white border-4 border-amber-500 rounded-full z-10"></div>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg font-black text-gray-900">{{ $keterlambatan->waktu_verifikasi_piket ? $keterlambatan->waktu_verifikasi_piket->format('H:i') : '--:--' }}</span>
                                            <span class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded text-[10px] font-black uppercase tracking-widest border border-amber-100 text-right">Verifikasi Piket</span>
                                        </div>
                                        @if($keterlambatan->waktu_verifikasi_piket)
                                            <div class="bg-amber-50/30 rounded-2xl p-6 border border-amber-100">
                                                <div class="flex items-start gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-white border border-amber-200 flex items-center justify-center text-amber-500 font-bold uppercase tracking-widest text-[10px]">P</div>
                                                    <div>
                                                        <p class="text-sm font-black text-gray-900">Guru Piket: {{ $keterlambatan->guruPiket->name ?? '-' }}</p>
                                                        <div class="mt-4 grid grid-cols-2 gap-4">
                                                            <div>
                                                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Status:</span>
                                                                <span class="text-sm font-bold text-gray-700">{{ str_replace('_', ' ', strtoupper($keterlambatan->status)) }}</span>
                                                            </div>
                                                            @if($keterlambatan->tindak_lanjut_piket)
                                                            <div>
                                                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Tindak Lanjut:</span>
                                                                <span class="text-sm font-bold text-gray-700">{{ $keterlambatan->tindak_lanjut_piket }}</span>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-gray-50 rounded-2xl p-6 border border-dashed border-gray-300 text-center italic text-sm text-gray-400">
                                                Menunggu verifikasi dari Guru Piket.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Guru Kelas Entry --}}
                                <div class="relative">
                                    <div class="absolute -left-[41px] top-0 w-[18px] h-[18px] bg-white border-4 border-indigo-500 rounded-full z-10"></div>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg font-black text-gray-900">{{ $keterlambatan->waktu_verifikasi_guru_kelas ? $keterlambatan->waktu_verifikasi_guru_kelas->format('H:i') : '--:--' }}</span>
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-[10px] font-black uppercase tracking-widest border border-indigo-100 italic">Masuk Kelas</span>
                                        </div>
                                        @if($keterlambatan->waktu_verifikasi_guru_kelas)
                                            <div class="bg-indigo-50/30 rounded-2xl p-6 border border-indigo-100">
                                                <p class="text-sm font-black text-gray-900 leading-none">Diverifikasi oleh: {{ $keterlambatan->guruKelasVerifier->name ?? 'Guru Mengajar' }}</p>
                                                <p class="text-xs text-gray-500 mt-1 font-medium italic">Siswa telah sampai di kelas dan surat izin telah di-scan oleh guru yang sedang mengajar.</p>
                                            </div>
                                        @else
                                            <div class="bg-gray-50 rounded-2xl p-6 border border-dashed border-gray-300 text-center italic text-sm text-gray-400">
                                                Menunggu scan QR dari Guru di kelas.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Wali Kelas Mentoring --}}
                                <div class="relative">
                                    <div class="absolute -left-[41px] top-0 w-[18px] h-[18px] bg-white border-4 border-purple-500 rounded-full z-10"></div>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg font-black text-gray-900">{{ $keterlambatan->waktu_pendampingan_wali_kelas ? $keterlambatan->waktu_pendampingan_wali_kelas->format('H:i') : '--:--' }}</span>
                                            <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded text-[10px] font-black uppercase tracking-widest border border-purple-100 italic">Pendampingan Wali Kelas</span>
                                        </div>
                                        @if($keterlambatan->waktu_pendampingan_wali_kelas)
                                            <div class="bg-purple-50/30 rounded-2xl p-6 border border-purple-100">
                                                <p class="text-sm font-black text-gray-900 leading-none">Wali Kelas: {{ $keterlambatan->siswa->rombels->first()?->waliKelas->name ?? '-' }}</p>
                                                <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                                    <div>
                                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Catatan Coaching:</span>
                                                        <span class="text-sm font-bold text-gray-700 block italic">"{{ $keterlambatan->catatan_wali_kelas }}"</span>
                                                    </div>
                                                    @if(Auth::user()->hasRole('Wali Kelas') || Auth::user()->hasRole('Guru BK') || Auth::user()->hasRole('Waka Kesiswaan'))
                                                    <a href="{{ route('wali-kelas.keterlambatan.coaching-pdf', $keterlambatan->id) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white text-[10px] font-black rounded-lg hover:bg-red-700 transition uppercase tracking-widest shadow-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        Cetak Lembar Coaching
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($keterlambatan->status === 'pendampingan_wali_kelas' && Auth::user()->hasRole('Wali Kelas') && $keterlambatan->siswa->rombels->first()?->wali_kelas_id === Auth::id())
                                            <div class="bg-purple-50 rounded-2xl p-6 border border-purple-200">
                                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                                                    <div>
                                                        <h5 class="text-xs font-black text-purple-800 uppercase tracking-widest">Sesi Pendampingan Wali Kelas</h5>
                                                        <p class="text-[10px] text-purple-600 font-medium italic mt-1">Gunakan Model GROW untuk melakukan coaching kedisiplinan kepada siswa.</p>
                                                    </div>
                                                    <button type="button" @click="$dispatch('open-modal', 'grow-coaching-modal')" class="px-4 py-2 bg-purple-600 text-white text-[10px] font-black rounded-lg hover:bg-purple-700 transition uppercase tracking-widest shadow-sm">
                                                        Mulai Sesi Coaching
                                                    </button>
                                                </div>

                                                <x-modal name="grow-coaching-modal" :show="$errors->isNotEmpty()" focusable>
                                                    <form method="post" action="{{ route('wali-kelas.keterlambatan.mentoring', $keterlambatan->id) }}" class="p-8">
                                                        @csrf
                                                        <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                                                            <div>
                                                                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Lembar Coaching Kedisiplinan</h2>
                                                                <p class="text-xs text-gray-500 font-medium mt-1 italic">Metode GROW (Goal, Reality, Options, Will)</p>
                                                            </div>
                                                            <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                        </div>

                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tanggal Coaching</label>
                                                                <input type="date" name="tanggal_coaching" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 text-sm font-bold shadow-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Lokasi Coaching</label>
                                                                <select name="lokasi" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 text-sm font-bold shadow-sm">
                                                                    <option value="langsung">Dibina Secara Langsung (Luring)</option>
                                                                    <option value="online">Dibina Melalui Online (Daring)</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="space-y-6">
                                                            <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100/50">
                                                                <h3 class="font-black text-indigo-700 text-sm uppercase tracking-wide mb-3 flex items-center gap-2">
                                                                    <span class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px]">G</span>
                                                                    Goal (Tujuan)
                                                                </h3>
                                                                <p class="text-xs text-indigo-900/60 italic mb-3 font-medium">"Apa tujuanmu datang tepat waktu ke sekolah? Apa dampaknya bagimu jika masuk tepat waktu?"</p>
                                                                <textarea name="goal_response" rows="2" class="w-full rounded-xl border-indigo-100 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Respon / Catatan Siswa..."></textarea>
                                                            </div>

                                                            <div class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100/50">
                                                                <h3 class="font-black text-blue-700 text-sm uppercase tracking-wide mb-3 flex items-center gap-2">
                                                                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px]">R</span>
                                                                    Reality (Kenyataan)
                                                                </h3>
                                                                <p class="text-xs text-blue-900/60 italic mb-3 font-medium">"Ceritakan apa yang biasanya terjadi di pagi hari sehingga kamu terlambat? Apa kendala utamanya?"</p>
                                                                <textarea name="reality_response" rows="2" class="w-full rounded-xl border-blue-100 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Respon / Catatan Siswa..."></textarea>
                                                            </div>

                                                            <div class="bg-amber-50/50 p-6 rounded-2xl border border-amber-100/50">
                                                                <h3 class="font-black text-amber-700 text-sm uppercase tracking-wide mb-3 flex items-center gap-2">
                                                                    <span class="w-6 h-6 rounded-full bg-amber-600 text-white flex items-center justify-center text-[10px]">O</span>
                                                                    Options (Pilihan)
                                                                </h3>
                                                                <p class="text-xs text-amber-900/60 italic mb-3 font-medium">"Apa saja hal yang bisa kamu ubah agar tidak terlambat lagi? (Misal: tidur lebih awal, dsb)"</p>
                                                                <textarea name="options_response" rows="2" class="w-full rounded-xl border-amber-100 focus:border-amber-500 focus:ring-amber-500 text-sm" placeholder="Respon / Catatan Siswa..."></textarea>
                                                            </div>

                                                            <div class="bg-green-50/50 p-6 rounded-2xl border border-green-100/50">
                                                                <h3 class="font-black text-green-700 text-sm uppercase tracking-wide mb-3 flex items-center gap-2">
                                                                    <span class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center text-[10px]">W</span>
                                                                    Will (Tindakan)
                                                                </h3>
                                                                <p class="text-xs text-green-900/60 italic mb-3 font-medium">"Dari pilihan tadi, mana yang akan kamu lakukan mulai besok? Siapa yang bisa membantumu?"</p>
                                                                <textarea name="will_response" rows="2" class="w-full rounded-xl border-green-100 focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Respon / Catatan Siswa..."></textarea>
                                                            </div>

                                                            <div class="pt-4 border-t border-gray-100">
                                                                <div class="mb-4">
                                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Rencana Aksi & Komitmen Siswa</label>
                                                                    <p class="text-[10px] text-gray-400 italic mb-2 font-medium">"Saya berkomitmen untuk melakukan perubahan sebagai berikut: (Tuliskan poin-poinnya)"</p>
                                                                    <textarea name="rencana_aksi" rows="4" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 text-sm" placeholder="1. Bangun lebih pagi&#10;2. Menyiapkan perlengkapan sekolah malam hari..."></textarea>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Konsekuensi Logis</label>
                                                                    <p class="text-[10px] text-gray-400 italic mb-2 font-medium">"Jika saya terlambat lagi, saya bersedia untuk: (Disepakati bersama)"</p>
                                                                    <input type="text" name="konsekuensi_logis" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 text-sm" placeholder="Contoh: Membantu piket kebersihan pagi selama 3 hari">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mt-8 flex items-center justify-end gap-3">
                                                            <button type="button" @click="$dispatch('close')" class="px-6 py-3 bg-gray-100 text-gray-600 text-xs font-black rounded-xl hover:bg-gray-200 transition uppercase tracking-widest">Batal</button>
                                                            <button type="submit" class="px-6 py-3 bg-purple-600 text-white text-xs font-black rounded-xl hover:bg-purple-700 transition uppercase tracking-widest shadow-lg shadow-purple-200">Simpan & Selesaikan Sesi</button>
                                                        </div>
                                                    </form>
                                                </x-modal>
                                            </div>
                                        @else
                                            <div class="bg-gray-50 rounded-2xl p-6 border border-dashed border-gray-300 text-center italic text-sm text-gray-400">
                                                {{ $keterlambatan->waktu_verifikasi_guru_kelas ? 'Menunggu pendampingan dari Wali Kelas.' : 'Siswa harus masuk kelas terlebih dahulu.' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- BK Pembinaan (Conditional) --}}
                                @if($keterlambatan->status === 'pembinaan_bk' || $keterlambatan->waktu_pembinaan_bk)
                                <div class="relative">
                                    <div class="absolute -left-[41px] top-0 w-[18px] h-[18px] bg-white border-4 border-blue-500 rounded-full z-10"></div>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg font-black text-gray-900">{{ $keterlambatan->waktu_pembinaan_bk ? $keterlambatan->waktu_pembinaan_bk->format('H:i') : '--:--' }}</span>
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-black uppercase tracking-widest border border-blue-100 italic">Pembinaan BK</span>
                                        </div>
                                        @if($keterlambatan->waktu_pembinaan_bk)
                                            <div class="bg-blue-50/30 rounded-2xl p-6 border border-blue-100">
                                                <p class="text-sm font-black text-gray-900 leading-none">Guru BK: {{ $keterlambatan->bkProcessor->name ?? '-' }}</p>
                                                <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                                    <div>
                                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Hasil Pembinaan:</span>
                                                        <span class="text-sm font-bold text-gray-700 block italic">"{{ $keterlambatan->catatan_bk }}"</span>
                                                    </div>
                                                    @if(Auth::user()->hasRole('Guru BK') || Auth::user()->hasRole('Wali Kelas') || Auth::user()->hasRole('Waka Kesiswaan'))
                                                    <a href="{{ route('bk.keterlambatan.coaching-pdf', $keterlambatan->id) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white text-[10px] font-black rounded-lg hover:bg-blue-700 transition uppercase tracking-widest shadow-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        Cetak Kontrak Perilaku
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif(Auth::user()->hasRole('Guru BK'))
                                            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200">
                                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                                                    <div>
                                                        <h5 class="text-xs font-black text-blue-800 uppercase tracking-widest">Konseling & Kontrak Perilaku BK</h5>
                                                        <p class="text-[10px] text-blue-600 font-medium italic mt-1">Lakukan identifikasi akar masalah dan buat kontrak perilaku formal bersama siswa.</p>
                                                    </div>
                                                    <button type="button" @click="$dispatch('open-modal', 'bk-coaching-modal')" class="px-4 py-2 bg-blue-600 text-white text-[10px] font-black rounded-lg hover:bg-blue-700 transition uppercase tracking-widest shadow-sm">
                                                        Mulai Konseling BK
                                                    </button>
                                                </div>

                                                <x-modal name="bk-coaching-modal" :show="$errors->isNotEmpty()" focusable>
                                                    <form method="post" action="{{ route('bk.keterlambatan.pembinaan', $keterlambatan->id) }}" class="p-8" x-data="{ showHpLimit: false }">
                                                        @csrf
                                                        <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                                                            <div>
                                                                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Konseling Mendalam (Deep Dive)</h2>
                                                                <p class="text-xs text-gray-500 font-medium mt-1 italic">Intervensi Perilaku & Kontrak Formal</p>
                                                            </div>
                                                            <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                        </div>

                                                        <div class="mb-8 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                                            <div class="w-full">
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tanggal Konseling</label>
                                                                <input type="date" name="tanggal_konseling" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm font-bold shadow-sm">
                                                            </div>
                                                        </div>

                                                        <div class="space-y-6">
                                                            {{-- II. Deep Dive --}}
                                                            <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100/50">
                                                                <h3 class="font-black text-indigo-700 text-sm uppercase tracking-wide mb-4 flex items-center gap-2">II. Identifikasi Akar Masalah</h3>
                                                                
                                                                <div class="space-y-4">
                                                                    <div>
                                                                        <label class="text-xs text-indigo-900/80 font-bold mb-1 block">Evaluasi Sebelumnya: Mengapa komitmen dengan Wali Kelas belum berhasil?</label>
                                                                        <textarea name="evaluasi_sebelumnya" rows="2" class="w-full rounded-xl border-indigo-100 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Respon siswa..."></textarea>
                                                                    </div>
                                                                    <div>
                                                                        <label class="text-xs text-indigo-900/80 font-bold mb-1 block">Faktor Penghambat: Kendala teknis atau masalah psikologis?</label>
                                                                        <textarea name="faktor_penghambat" rows="2" class="w-full rounded-xl border-indigo-100 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Respon siswa..."></textarea>
                                                                    </div>
                                                                    <div>
                                                                        <label class="text-xs text-indigo-900/80 font-bold mb-1 block">Analisis Dampak: Kerugian terbesar yang dirasakan siswa?</label>
                                                                        <textarea name="analisis_dampak" rows="2" class="w-full rounded-xl border-indigo-100 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Respon siswa..."></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- III. Solution Bridge --}}
                                                            <div class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100/50">
                                                                <h3 class="font-black text-blue-700 text-sm uppercase tracking-wide mb-4 flex items-center gap-2">III. Intervensi Perilaku (The Solution Bridge)</h3>
                                                                
                                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                                                    <div>
                                                                        <label class="text-[10px] font-black text-blue-900/50 uppercase tracking-widest mb-1 block">Jam Bangun</label>
                                                                        <input type="time" name="jam_bangun" class="w-full rounded-xl border-blue-100 focus:border-blue-500 focus:ring-blue-500 text-sm font-bold">
                                                                    </div>
                                                                    <div>
                                                                        <label class="text-[10px] font-black text-blue-900/50 uppercase tracking-widest mb-1 block">Jam Berangkat</label>
                                                                        <input type="time" name="jam_berangkat" class="w-full rounded-xl border-blue-100 focus:border-blue-500 focus:ring-blue-500 text-sm font-bold">
                                                                    </div>
                                                                    <div>
                                                                        <label class="text-[10px] font-black text-blue-900/50 uppercase tracking-widest mb-1 block">Estimasi Perjalanan (Min)</label>
                                                                        <input type="number" name="durasi_perjalanan" class="w-full rounded-xl border-blue-100 focus:border-blue-500 focus:ring-blue-500 text-sm font-bold">
                                                                    </div>
                                                                </div>

                                                                <div class="space-y-3">
                                                                    <label class="text-xs text-blue-900/80 font-bold block mb-2">Strategi Pendukung (Checklist):</label>
                                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                        <label class="flex items-center gap-3 bg-white p-3 rounded-xl border border-blue-50 cursor-pointer hover:bg-blue-50 transition shadow-sm">
                                                                            <input type="checkbox" name="strategi_pendukung[]" value="alarm" class="rounded text-blue-600 focus:ring-blue-500">
                                                                            <span class="text-xs font-medium text-gray-700">Alarm Ganda (5 menit)</span>
                                                                        </label>
                                                                        <label class="flex items-center gap-3 bg-white p-3 rounded-xl border border-blue-50 cursor-pointer hover:bg-blue-50 transition shadow-sm">
                                                                            <input type="checkbox" name="strategi_pendukung[]" value="prep" class="rounded text-blue-600 focus:ring-blue-500">
                                                                            <span class="text-xs font-medium text-gray-700">Persiapan H-1 Malam</span>
                                                                        </label>
                                                                        <label class="flex items-center gap-3 bg-white p-3 rounded-xl border border-blue-50 cursor-pointer hover:bg-blue-50 transition shadow-sm">
                                                                            <input type="checkbox" name="strategi_pendukung[]" value="hp" class="rounded text-blue-600 focus:ring-blue-500" @change="showHpLimit = $event.target.checked">
                                                                            <span class="text-xs font-medium text-gray-700">Batas Penggunaan HP</span>
                                                                        </label>
                                                                        <label class="flex items-center gap-3 bg-white p-3 rounded-xl border border-blue-50 cursor-pointer hover:bg-blue-50 transition shadow-sm">
                                                                            <input type="checkbox" name="strategi_pendukung[]" value="help" class="rounded text-blue-600 focus:ring-blue-500">
                                                                            <span class="text-xs font-medium text-gray-700">Bantuan Pihak Ketiga</span>
                                                                        </label>
                                                                    </div>
                                                                    <div x-show="showHpLimit" class="mt-3">
                                                                        <label class="text-[10px] font-black text-blue-900/50 uppercase tracking-widest mb-1 block">Maksimal HP Jam:</label>
                                                                        <input type="time" name="hp_limit_time" class="w-full rounded-xl border-blue-100 focus:border-blue-500 focus:ring-blue-500 text-sm font-bold">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- IV. Behavioral Contract --}}
                                                            <div class="bg-red-50/50 p-6 rounded-2xl border border-red-100/50">
                                                                <h3 class="font-black text-red-700 text-sm uppercase tracking-wide mb-4 flex items-center gap-2">IV. Kontrak Perilaku (Behavioral Contract)</h3>
                                                                <p class="text-xs text-red-900/60 italic mb-4 font-medium">
                                                                    "Jika saya terlambat untuk yang ke-4 kalinya, maka saya bersedia menerima sanksi tegas..."
                                                                </p>
                                                                <textarea name="sanksi_disepakati" rows="2" class="w-full rounded-xl border-red-100 focus:border-red-500 focus:ring-red-500 text-sm font-bold bg-white" placeholder="Contoh: Panggilan Orang Tua, Skorsing Terbatas, atau Sanksi Lainnya..."></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="mt-8 flex items-center justify-end gap-3">
                                                            <button type="button" @click="$dispatch('close')" class="px-6 py-3 bg-gray-100 text-gray-600 text-xs font-black rounded-xl hover:bg-gray-200 transition uppercase tracking-widest">Batal</button>
                                                            <button type="submit" class="px-6 py-3 bg-blue-600 text-white text-xs font-black rounded-xl hover:bg-blue-700 transition uppercase tracking-widest shadow-lg shadow-blue-200">Simpan & Selesaikan Konseling</button>
                                                        </div>
                                                    </form>
                                                </x-modal>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                {{-- Completion --}}
                                <div class="relative">
                                    <div class="absolute -left-[41px] top-0 w-[18px] h-[18px] bg-white border-4 {{ $keterlambatan->status == 'selesai' ? 'border-green-500' : 'border-gray-200' }} rounded-full z-10"></div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg font-black text-gray-900 italic">Selesai</span>
                                        </div>
                                        @if($keterlambatan->status == 'selesai')
                                            <div class="text-sm font-bold text-green-600 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                                Siswa telah diproses sepenuhnya melalui alur pembinaan.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
