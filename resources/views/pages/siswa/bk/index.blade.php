<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bimbingan & Konseling') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-3xl p-8 shadow-xl shadow-red-100 text-white relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-center md:text-left">
                        <h3 class="text-3xl font-black mb-2">Butuh Teman Cerita?</h3>
                        <p class="text-red-100 font-medium max-w-lg">
                            Guru BK kami siap mendengarkan dan membantu setiap masalah kesiswaan, karir, maupun masalah pribadi kamu. Rahasia kamu aman bersama kami.
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <button onclick="document.getElementById('modalJadwal').classList.remove('hidden')" 
                            class="bg-white text-red-600 px-8 py-4 rounded-2xl font-black text-lg hover:bg-red-50 transition-all shadow-xl active:scale-95">
                            Jadwalkan Konsultasi
                        </button>
                    </div>
                </div>
            </div>

            <!-- List Jadwal -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                    <h4 class="text-xl font-bold text-gray-900 leading-none">Riwayat Konsultasi</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-5 text-sm font-bold text-gray-600 uppercase tracking-wider">Perihal</th>
                                <th class="px-8 py-5 text-sm font-bold text-gray-600 uppercase tracking-wider">Waktu & Tempat</th>
                                <th class="px-8 py-5 text-sm font-bold text-gray-600 uppercase tracking-wider">Guru BK</th>
                                <th class="px-8 py-5 text-sm font-bold text-gray-600 uppercase tracking-wider text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($jadwals as $j)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="text-base font-bold text-gray-900">{{ $j->perihal }}</div>
                                    <div class="text-xs text-gray-400 mt-1">Diajukan: {{ $j->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-sm text-gray-700 font-medium">
                                        {{ \Carbon\Carbon::parse($j->tanggal_rencana)->translatedFormat('l, d F Y') }}<br>
                                        Pukul {{ date('H:i', strtotime($j->jam_rencana)) }} WIB
                                    </div>
                                    @if($j->tempat)
                                        <div class="mt-1 flex items-center gap-1 text-red-600">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span class="text-xs font-bold">{{ $j->tempat }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold text-xs">
                                            {{ substr($j->guruBK->name ?? '?', 0, 1) }}
                                        </div>
                                        <div class="text-sm font-bold text-gray-700">{{ $j->guruBK->name ?? 'Belum Ditentukan' }}</div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest
                                            {{ $j->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                            {{ $j->status == 'approved' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                            {{ $j->status == 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $j->status == 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                        ">
                                            {{ $j->status }}
                                        </span>
                                        
                                        @if($j->status == 'approved')
                                            <a href="{{ route('siswa.bk.konsultasi.print-jadwal', $j->id) }}" target="_blank" class="text-[10px] font-bold text-indigo-600 hover:underline flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1.5m1.5 0H13m-4 4h1.5m1.5 0H13m-4 4h1.5m1.5 0H13"/></svg>
                                                Unduh Jadwal
                                            </a>
                                        @endif

                                        @if($j->status == 'completed')
                                            <a href="{{ route('siswa.bk.konsultasi.print-jadwal', $j->id) }}" target="_blank" class="text-[10px] font-bold text-gray-600 hover:underline flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1.5m1.5 0H13m-4 4h1.5m1.5 0H13m-4 4h1.5m1.5 0H13"/></svg>
                                                Unduh Jadwal
                                            </a>
                                            <a href="{{ route('siswa.bk.konsultasi.print-report', $j->id) }}" target="_blank" class="text-[10px] font-bold text-green-600 hover:underline flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Berita Acara
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <span class="text-sm font-bold text-gray-400">Belum ada riwayat konsultasi</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Jadwal -->
            <div id="modalJadwal" class="fixed inset-0 z-[100] hidden overflow-y-auto px-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen">
                    <div class="fixed inset-0 bg-gray-900/60 transition-opacity" onclick="document.getElementById('modalJadwal').classList.add('hidden')"></div>
                    <div class="relative bg-white rounded-[40px] shadow-2xl overflow-hidden w-full max-w-xl animate-in zoom-in duration-300">
                        <form action="{{ route('siswa.bk.jadwal.store') }}" method="POST" class="p-10">
                            @csrf
                            <div class="flex justify-between items-start mb-8">
                                <div>
                                    <h3 class="text-2xl font-black text-gray-900 leading-tight">Buat Jadwal Konsultasi</h3>
                                    <p class="text-sm text-gray-500 mt-1">Lengkapi form berikut untuk mengajukan bimbingan.</p>
                                </div>
                                <button type="button" onclick="document.getElementById('modalJadwal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors p-2 bg-gray-50 rounded-2xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="space-y-6 text-left">
                                <div>
                                    <label class="block text-sm font-black text-gray-900 mb-2 uppercase tracking-wider">Perihal / Masalah</label>
                                    <textarea name="perihal" rows="2" required 
                                        class="w-full bg-gray-50 border-0 rounded-2xl focus:ring-2 focus:ring-red-500 p-4 text-sm font-medium" 
                                        placeholder="Tuliskan alasan ingin berkonsultasi secara singkat..."></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-black text-gray-900 mb-2 uppercase tracking-wider">Rencana Tanggal</label>
                                        <input type="date" name="tanggal_rencana" required min="{{ date('Y-m-d') }}"
                                            class="w-full bg-gray-50 border-0 rounded-2xl focus:ring-2 focus:ring-red-500 p-4 text-sm font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-black text-gray-900 mb-2 uppercase tracking-wider">Jam Rencana</label>
                                        <input type="time" name="jam_rencana" required
                                            class="w-full bg-gray-50 border-0 rounded-2xl focus:ring-2 focus:ring-red-500 p-4 text-sm font-medium">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-black text-gray-900 mb-2 uppercase tracking-wider">Pilih Guru BK (Opsional)</label>
                                    <select name="guru_bk_id" class="w-full bg-gray-50 border-0 rounded-2xl focus:ring-2 focus:ring-red-500 p-4 text-sm font-medium">
                                        <option value="">Biarkan BK yang tentukan</option>
                                        @foreach($gurusBK as $guru)
                                            <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-10">
                                <button type="submit" class="w-full bg-red-600 text-white py-5 rounded-2xl font-black text-lg hover:bg-red-700 transition-all shadow-xl shadow-red-100 flex items-center justify-center gap-2">
                                    <span>Kirim Pengajuan</span>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
