<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Kantin') }}
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Hero Card --}}
            <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-[32px] p-8 text-white relative overflow-hidden shadow-2xl shadow-orange-500/20">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute right-10 bottom-0 w-32 h-32 bg-yellow-300/20 rounded-full blur-2xl pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h1 class="text-3xl font-black mb-2 font-outfit tracking-tight">Kantin Online - SMK Telkom</h1>
                        <p class="text-orange-100 font-medium">Kelola pesanan makanan siswa secara real-time. Fitur ini masih dalam tahap *mockup* pengembangan.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="bg-white/20 hover:bg-white/30 backdrop-blur-md px-6 py-3 rounded-2xl font-bold transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Menu Baru
                        </button>
                    </div>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Stat 1 --}}
                <div class="bg-white rounded-[24px] p-6 shadow-xl border border-gray-100 relative overflow-hidden group hover:shadow-2xl transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <span class="text-xs font-bold text-blue-500 bg-blue-50 px-3 py-1 rounded-full">+12 Hari Ini</span>
                    </div>
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">Pesanan Aktif</h3>
                    <p class="text-3xl font-black text-gray-900">24</p>
                    <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-blue-500/5 rounded-full blur-xl group-hover:bg-blue-500/10 transition-colors pointer-events-none"></div>
                </div>

                {{-- Stat 2 --}}
                <div class="bg-white rounded-[24px] p-6 shadow-xl border border-gray-100 relative overflow-hidden group hover:shadow-2xl transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full">Rp 450.000</span>
                    </div>
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">Pendapatan Hari Ini</h3>
                    <p class="text-3xl font-black text-gray-900">Rp 1.250K</p>
                    <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-colors pointer-events-none"></div>
                </div>

                {{-- Stat 3 --}}
                <div class="bg-white rounded-[24px] p-6 shadow-xl border border-gray-100 relative overflow-hidden group hover:shadow-2xl transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-amber-500 bg-amber-50 px-3 py-1 rounded-full">3 Perlu Diproses</span>
                    </div>
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">Menunggu Konfirmasi</h3>
                    <p class="text-3xl font-black text-gray-900">5</p>
                    <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-amber-500/5 rounded-full blur-xl group-hover:bg-amber-500/10 transition-colors pointer-events-none"></div>
                </div>
            </div>

            {{-- Recent Orders Table --}}
            <div class="bg-white rounded-[24px] shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Pesanan Masuk (Mockup)</h3>
                        <p class="text-sm text-gray-500 font-medium">Daftar pesanan siswa hari ini.</p>
                    </div>
                    <button class="text-sm font-bold text-orange-600 hover:text-orange-700">Lihat Semua →</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="text-xs text-gray-400 uppercase bg-gray-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider">ID Pesanan</th>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider">Siswa (Kelas)</th>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider">Pesanan</th>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider">Pembayaran</th>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-4 text-right font-bold tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Row 1 --}}
                            <tr class="bg-white border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900">#ORD-001</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">Ahmad Budi</div>
                                    <div class="text-xs text-gray-400">XI RPL 1</div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-600">
                                    2x Nasi Goreng Spesial<br>
                                    1x Es Teh Manis
                                </td>
                                <td class="px-6 py-4 font-black text-gray-900">Rp 35.000</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Qris (Lunas)
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Menyiapkan
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-orange-500 hover:text-orange-700 font-bold px-3 py-1 rounded-lg hover:bg-orange-50 transition-colors">Selesai</button>
                                </td>
                            </tr>
                            
                            {{-- Row 2 --}}
                            <tr class="bg-white border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900">#ORD-002</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">Siti Aminah</div>
                                    <div class="text-xs text-gray-400">X TKJ 2</div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-600">
                                    1x Mie Ayam Pangsit<br>
                                    1x Es Jeruk
                                </td>
                                <td class="px-6 py-4 font-black text-gray-900">Rp 20.000</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        Tunai di Tempat
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Pesanan Baru
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-blue-500 hover:text-blue-700 font-bold px-3 py-1 rounded-lg hover:bg-blue-50 transition-colors">Proses</button>
                                </td>
                            </tr>

                            {{-- Row 3 --}}
                            <tr class="bg-white border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900">#ORD-003</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">Dimas Pratama</div>
                                    <div class="text-xs text-gray-400">XII TJA 1</div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-600">
                                    1x Ayam Geprek Level 3
                                </td>
                                <td class="px-6 py-4 font-black text-gray-900">Rp 18.000</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Saldo Siswa (Lunas)
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Selesai
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-gray-400 font-bold px-3 py-1">-</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
