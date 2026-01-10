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
                                                <div class="mt-4">
                                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Catatan Coaching:</span>
                                                    <span class="text-sm font-bold text-gray-700 block bg-white p-3 rounded-lg border border-purple-100">"{{ $keterlambatan->catatan_wali_kelas }}"</span>
                                                </div>
                                            </div>
                                        @elseif($keterlambatan->status === 'pendampingan_wali_kelas' && Auth::user()->hasRole('Wali Kelas') && $keterlambatan->siswa->rombels->first()?->wali_kelas_id === Auth::id())
                                            <div class="bg-purple-50 rounded-2xl p-6 border border-purple-200">
                                                <h5 class="text-xs font-black text-purple-800 uppercase tracking-widest mb-3">Input Catatan Pendampingan</h5>
                                                <form action="{{ route('wali-kelas.keterlambatan.mentoring', $keterlambatan->id) }}" method="POST">
                                                    @csrf
                                                    <textarea name="catatan_wali_kelas" rows="3" class="w-full rounded-xl border-purple-100 focus:border-purple-500 focus:ring-purple-500 text-sm mb-3" placeholder="Tuliskan hasil coaching/pendampingan terhadap siswa..."></textarea>
                                                    <button type="submit" class="w-full bg-purple-600 text-white text-xs font-black py-2 rounded-lg hover:bg-purple-700 transition uppercase tracking-widest">Simpan Pendampingan</button>
                                                </form>
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
                                                <div class="mt-4">
                                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Hasil Pembinaan:</span>
                                                    <span class="text-sm font-bold text-gray-700 block bg-white p-3 rounded-lg border border-blue-100">"{{ $keterlambatan->catatan_bk }}"</span>
                                                </div>
                                            </div>
                                        @elseif(Auth::user()->hasRole('Guru BK'))
                                            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200">
                                                <h5 class="text-xs font-black text-blue-800 uppercase tracking-widest mb-3">Input Hasil Pembinaan BK</h5>
                                                <form action="{{ route('bk.keterlambatan.pembinaan', $keterlambatan->id) }}" method="POST">
                                                    @csrf
                                                    <textarea name="catatan_bk" rows="3" class="w-full rounded-xl border-blue-100 focus:border-blue-500 focus:ring-blue-500 text-sm mb-3" placeholder="Tuliskan hasil pembinaan lanjutan oleh BK..."></textarea>
                                                    <button type="submit" class="w-full bg-blue-600 text-white text-xs font-black py-2 rounded-lg hover:bg-blue-700 transition uppercase tracking-widest">Simpan Pembinaan BK</button>
                                                </form>
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
