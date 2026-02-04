<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-layout-dashboard">
                    <rect width="7" height="9" x="3" y="3" rx="1" />
                    <rect width="7" height="5" x="14" y="3" rx="1" />
                    <rect width="7" height="9" x="14" y="12" rx="1" />
                    <rect width="7" height="5" x="3" y="16" rx="1" />
                </svg>
            </div>
            <h2 class="font-black text-2xl text-gray-900 tracking-tight">Dashboard Tata Usaha</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            <!-- Welcome Banner -->
            <div
                class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-blue-600 to-indigo-800 rounded-[2.5rem] p-10 shadow-2xl shadow-indigo-200 group">
                <div
                    class="absolute top-0 right-0 -m-20 w-80 h-80 bg-white/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700">
                </div>
                <div
                    class="absolute bottom-0 left-0 -m-20 w-60 h-60 bg-indigo-400/20 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700">
                </div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-4xl font-black text-white mb-2 tracking-tight">Halo, Admin TU! 👋</h1>
                        <p class="text-indigo-100 text-lg font-medium max-w-md">Siap melayani kebutuhan persuratan
                            sekolah hari ini? Tetap semangat dan teliti!</p>
                    </div>
                    <div
                        class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/20 text-center md:text-right">
                        <p class="text-white/80 text-sm font-black uppercase tracking-widest mb-1">
                            {{ now()->translatedFormat('l, d F Y') }}</p>
                        <p class="text-white text-3xl font-black tabular-nums tracking-tighter"
                            x-data="{ time: '{{ now()->format('H:i:s') }}' }"
                            x-init="setInterval(() => time = new Date().toLocaleTimeString('en-GB'), 1000)"
                            x-text="time"></p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Surat Masuk -->
                <div
                    class="bg-white rounded-[2rem] p-8 shadow-soft border border-gray-50 hover:shadow-xl transition-all duration-300 group">
                    <div class="flex flex-col gap-4">
                        <div
                            class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-mail">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Surat Masuk Hari
                                Ini</p>
                            <h3 class="text-4xl font-black text-gray-900 tracking-tighter">
                                {{ $stats['surat_masuk_today'] }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Surat Keluar -->
                <div
                    class="bg-white rounded-[2rem] p-8 shadow-soft border border-gray-50 hover:shadow-xl transition-all duration-300 group">
                    <div class="flex flex-col gap-4">
                        <div
                            class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-file-text">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                <path d="M10 9H8" />
                                <path d="M16 13H8" />
                                <path d="M16 17H8" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Surat Keluar Hari
                                Ini</p>
                            <h3 class="text-4xl font-black text-gray-900 tracking-tighter">
                                {{ $stats['surat_keluar_today'] }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Pending Requests -->
                <div
                    class="bg-white rounded-[2rem] p-8 shadow-soft border border-gray-50 hover:shadow-xl transition-all duration-300 group">
                    <div class="flex flex-col gap-4">
                        <div
                            class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-clock">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Antrean Nomor</p>
                            <h3 class="text-4xl font-black text-gray-900 tracking-tighter">
                                {{ $stats['pending_requests'] }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Total Kode -->
                <div
                    class="bg-white rounded-[2rem] p-8 shadow-soft border border-gray-50 hover:shadow-xl transition-all duration-300 group">
                    <div class="flex flex-col gap-4">
                        <div
                            class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-archive">
                                <rect width="20" height="5" x="2" y="3" rx="1" />
                                <path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                                <path d="M10 12h4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Klasifikasi Surat
                            </p>
                            <h3 class="text-4xl font-black text-gray-900 tracking-tighter">{{ $stats['total_codes'] }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Requests Panel -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white rounded-[2.5rem] shadow-soft border border-gray-50 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-clipboard-list">
                                        <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                                        <path
                                            d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                        <path d="M12 11h4" />
                                        <path d="M12 16h4" />
                                        <path d="M8 11h.01" />
                                        <path d="M8 16h.01" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-black text-gray-900">Antrean Perhomonan Nomor</h3>
                            </div>
                            <a href="{{ route('tu.requests.index') }}"
                                class="text-xs font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-wider">Lihat
                                Semua</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead
                                    class="bg-gray-50/50 text-[10px] uppercase font-black text-gray-400 tracking-widest">
                                    <tr>
                                        <th class="px-8 py-4">Pemohon</th>
                                        <th class="px-8 py-4">Perihal</th>
                                        <th class="px-8 py-4">Kode</th>
                                        <th class="px-8 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($pendingRequests as $req)
                                        <tr class="hover:bg-gray-50/50 transition-colors group">
                                            <td class="px-8 py-5">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-black text-xs">
                                                        {{ substr($req->user->name, 0, 1) }}
                                                    </div>
                                                    <span
                                                        class="text-sm font-bold text-gray-900 truncate max-w-[120px]">{{ $req->user->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5">
                                                <p
                                                    class="text-sm font-medium text-gray-600 line-clamp-1 truncate max-w-[200px]">
                                                    {{ $req->subject }}</p>
                                            </td>
                                            <td class="px-8 py-5">
                                                <span
                                                    class="px-3 py-1 rounded-lg bg-gray-100 text-[10px] font-black text-gray-600 uppercase">{{ $req->letterCode->code }}</span>
                                            </td>
                                            <td class="px-8 py-5 text-right">
                                                <form action="{{ route('tu.requests.approve', $req) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black transition-all">Setujui</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-8 py-12 text-center">
                                                <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Antrean
                                                    Kosong</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions / Shortcuts -->
                <div class="space-y-8">
                    <div class="bg-white rounded-[2.5rem] shadow-soft border border-gray-50 p-8">
                        <h3 class="text-xl font-black text-gray-900 mb-6">Aksi Cepat</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <a href="{{ route('tu.incoming.index') }}"
                                class="flex items-center gap-4 p-4 rounded-3xl bg-blue-50 hover:bg-blue-100 transition-colors group">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-white text-blue-600 flex items-center justify-center shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-mail-plus">
                                        <path d="M22 13V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h8" />
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                                        <path d="M19 16v6" />
                                        <path d="M16 19h6" />
                                    </svg>
                                </div>
                                <span class="font-bold text-blue-900">Catat Surat Masuk</span>
                            </a>
                            <a href="{{ route('tu.outgoing.index') }}"
                                class="flex items-center gap-4 p-4 rounded-3xl bg-indigo-50 hover:bg-indigo-100 transition-colors group">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-white text-indigo-600 flex items-center justify-center shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-plus-circle">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M8 12h8" />
                                        <path d="M12 8v8" />
                                    </svg>
                                </div>
                                <span class="font-bold text-indigo-900">Terbitkan Nomor Surat</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .shadow-soft {
                box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04), 0 6px 10px -6px rgba(0, 0, 0, 0.04);
            }
        </style>
    @endpush
</x-app-layout>