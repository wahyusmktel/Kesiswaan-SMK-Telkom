<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            📜 Change Log
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Release Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-2xl"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-white/20 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest border border-white/30">Stable Release</span>
                        <span class="text-blue-100 text-xs font-bold">{{ date('F d, Y') }}</span>
                    </div>
                    <h3 class="text-4xl font-black tracking-tight">Version 1.0.0</h3>
                    <p class="mt-2 text-blue-100 font-medium text-lg italic">"Initial Launch - Empowering School Management"</p>
                </div>
            </div>

            {{-- Change Log Content --}}
            <div class="space-y-6">
                
                {{-- New Features --}}
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 hover:shadow-md transition-shadow">
                    <h4 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-3">
                        <span class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">✨</span>
                        Fitur Utama (New Features)
                    </h4>
                    <ul class="space-y-4">
                        <li class="flex gap-4">
                            <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-blue-500 mt-2"></span>
                            <div>
                                <h5 class="font-bold text-gray-800">Sistem Perizinan Terpadu</h5>
                                <p class="text-sm text-gray-500">Mendukung izin tidak masuk (sakit/izin) dan izin keluar kelas real-time dengan alur persetujuan berjenjang (Guru Kelas, Wali Kelas, Guru Piket).</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-blue-500 mt-2"></span>
                            <div>
                                <h5 class="font-bold text-gray-800">Manajemen Poin & Tata Tertib</h5>
                                <p class="text-sm text-gray-500">Sistem pencatatan pelanggaran, prestasi, dan pemutihan poin siswa yang terintegrasi secara global.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-blue-500 mt-2"></span>
                            <div>
                                <h5 class="font-bold text-gray-800">Monitoring Absensi Guru</h5>
                                <p class="text-sm text-gray-500">Pelacakan kehadiran guru di kelas secara real-time oleh Waka Kurikulum dan Piket, lengkap dengan statistik performa.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- Enhancements --}}
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 hover:shadow-md transition-shadow">
                    <h4 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-3">
                        <span class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">🚀</span>
                        Peningkatan (Enhancements)
                    </h4>
                    <ul class="space-y-4">
                        <li class="flex gap-4">
                            <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-indigo-500 mt-2"></span>
                            <div>
                                <h5 class="font-bold text-gray-800">Dashboard Interaktif Across Roles</h5>
                                <p class="text-sm text-gray-500">Desain dashboard baru dengan widget "Kegiatan Saat Ini" yang adaptif untuk Siswa, Guru, Piket, dan Kurikulum.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-indigo-500 mt-2"></span>
                            <div>
                                <h5 class="font-bold text-gray-800">Flexible Lesson Hours</h5>
                                <p class="text-sm text-gray-500">Mendukung jam pelajaran yang berbeda antar hari (Override Hari) untuk mengakomodasi jadwal khusus seperti Jumat Rapi/Bersih.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-indigo-500 mt-2"></span>
                            <div>
                                <h5 class="font-bold text-gray-800">Eksport PDF Master Jadwal</h5>
                                <p class="text-sm text-gray-500">Fitur unduh jadwal seluruh rombel dalam satu file PDF landscape yang rapi dengan legenda mata pelajaran.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- Technical Fixes --}}
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 hover:shadow-md transition-shadow">
                    <h4 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-3">
                        <span class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">🛠️</span>
                        Perbaikan Teknis (Bug Fixes)
                    </h4>
                    <ul class="space-y-4">
                        <li class="flex gap-4">
                            <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2"></span>
                            <p class="text-sm text-gray-600 font-medium italic">Optimasi database query pada perhitungan poin siswa.</p>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2"></span>
                            <p class="text-sm text-gray-600 font-medium italic">Perbaikan validasi tumpang tindih (overlap) jadwal pada input jam pelajaran.</p>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2"></span>
                            <p class="text-sm text-gray-600 font-medium italic">Peningkatan keamanan pada akses route berbasis role (403 fix).</p>
                        </li>
                    </ul>
                </div>

            </div>

            {{-- Footer Info --}}
            <div class="text-center pb-12">
                <p class="text-gray-400 text-sm font-medium">SMK Telkom Lampung - Version 1.0.0-stable</p>
                <div class="flex justify-center gap-4 mt-2">
                    <span class="w-2 h-2 rounded-full bg-blue-200"></span>
                    <span class="w-2 h-2 rounded-full bg-indigo-200"></span>
                    <span class="w-2 h-2 rounded-full bg-purple-200"></span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
