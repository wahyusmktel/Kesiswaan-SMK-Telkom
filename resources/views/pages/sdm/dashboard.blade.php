<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard KAUR SDM</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">
            {{-- Welcome & Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Welcome Card --}}
                <div class="lg:col-span-2 relative overflow-hidden bg-gradient-to-br from-indigo-600 to-blue-700 rounded-3xl p-8 text-white shadow-xl">
                    <div class="absolute right-0 top-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
                    <div class="relative z-10">
                        <h3 class="text-3xl font-black mb-2">Selamat Datang, {{ Auth::user()->name }}</h3>
                        <p class="text-indigo-100 font-medium">Panel Manajemen Sumber Daya Manusia & Kepegawaian.</p>
                        
                        <div class="mt-8 flex gap-6 border-t border-white/20 pt-6">
                            <div>
                                <span class="text-3xl font-black block">{{ $stats['total_pending'] }}</span>
                                <span class="text-xs font-bold uppercase tracking-widest text-indigo-200">Menunggu Validasi</span>
                            </div>
                            <div class="border-l border-white/20 pl-6">
                                <span class="text-3xl font-black block">{{ $stats['total_approved'] }}</span>
                                <span class="text-xs font-bold uppercase tracking-widest text-indigo-200">Izin Disetujui</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fast Actions --}}
                <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-sm flex flex-col justify-between">
                    <h4 class="font-bold text-gray-800 mb-4">Aksi Cepat</h4>
                    <div class="space-y-3">
                        <a href="{{ route('sdm.persetujuan-izin-guru.index') }}" class="flex items-center justify-between p-4 rounded-2xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-all group">
                            <span class="font-bold">Review Izin Guru</span>
                            <svg class="w-5 h-5 transform group-hover:translate-x-1 transitions-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-dashed border-gray-200 text-gray-400 text-sm text-center">
                            Menu lainnya segera hadir
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Latest Requests --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h4 class="font-bold text-gray-800">Pengajuan Izin Terbaru</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px] tracking-widest">
                            <tr>
                                <th class="px-6 py-4">Nama Guru</th>
                                <th class="px-6 py-4">Jenis Izin</th>
                                <th class="px-6 py-4">Deskripsi</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($stats['latest_requests'] as $req)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $req->guru->nama_lengkap }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $req->jenis_izin }}</td>
                                    <td class="px-6 py-4 text-gray-500 italic text-[10px] truncate max-w-[150px]" title="{{ $req->deskripsi }}">
                                        "{{ $req->deskripsi }}"
                                    </td>
                                    <td class="px-6 py-4 text-xs">{{ $req->tanggal_mulai->translatedFormat('d F Y') }}</td>
                                    <td class="px-6 py-4">
                                        <x-status-badge-izin :status="$req->status_sdm" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">Belum ada pengajuan terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
