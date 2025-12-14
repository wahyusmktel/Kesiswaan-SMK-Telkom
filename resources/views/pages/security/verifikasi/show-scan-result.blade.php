<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Detail Izin Siswa</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">

                <div class="bg-gray-800 p-6 text-center">
                    <div
                        class="mx-auto h-20 w-20 rounded-full bg-white flex items-center justify-center text-3xl font-bold text-gray-800 mb-3 border-4 border-gray-600">
                        {{ substr($izin->siswa->name, 0, 1) }}
                    </div>
                    <h2 class="text-2xl font-bold text-white">{{ $izin->siswa->name }}</h2>
                    <p class="text-gray-400">
                        {{ $izin->siswa->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? 'Kelas Tidak Diketahui' }}
                    </p>
                </div>

                <div class="p-8 space-y-6">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tujuan Keluar</p>
                        <p class="text-lg font-bold text-gray-800">{{ $izin->tujuan }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Status Saat Ini
                            </p>
                            @php
                                $statusColor = match ($izin->status) {
                                    'disetujui_guru_piket' => 'text-blue-600',
                                    'diverifikasi_security' => 'text-orange-600',
                                    'selesai' => 'text-green-600',
                                    default => 'text-gray-600',
                                };
                            @endphp
                            <p class="font-bold {{ $statusColor }} text-sm uppercase">
                                {{ str_replace('_', ' ', $izin->status) }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Waktu Keluar</p>
                            <p class="font-mono font-bold text-gray-800">
                                {{ $izin->waktu_keluar_sebenarnya ? \Carbon\Carbon::parse($izin->waktu_keluar_sebenarnya)->format('H:i') : '--:--' }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        @if ($izin->status == 'disetujui_guru_piket')
                            <form action="{{ route('security.verifikasi.keluar', $izin->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 flex flex-col items-center justify-center gap-1 group">
                                    <span class="text-lg font-black uppercase tracking-wider">IZINKAN KELUAR</span>
                                    <span class="text-xs text-blue-200 group-hover:text-white">Klik untuk mencatat jam
                                        keluar</span>
                                </button>
                            </form>
                        @elseif ($izin->status == 'diverifikasi_security')
                            <form action="{{ route('security.verifikasi.kembali', $izin->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full py-4 bg-green-600 hover:bg-green-500 text-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 flex flex-col items-center justify-center gap-1 group">
                                    <span class="text-lg font-black uppercase tracking-wider">SISWA KEMBALI</span>
                                    <span class="text-xs text-green-200 group-hover:text-white">Klik untuk mencatat
                                        kepulangan</span>
                                </button>
                            </form>
                        @else
                            <div class="text-center p-4 bg-gray-100 rounded-xl text-gray-500 font-medium">
                                Tidak ada aksi yang diperlukan.
                            </div>
                        @endif
                    </div>

                    <div class="text-center">
                        <a href="{{ route('security.verifikasi.scan') }}"
                            class="text-indigo-600 font-bold hover:underline">
                            &larr; Scan Lagi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
