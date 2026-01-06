<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Absensi Guru Mengajar') }}
        </h2>
    </x-slot>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <style>
            .ts-control {
                border-radius: 0.5rem;
                padding: 0.5rem 0.75rem;
                border-color: #d1d5db;
                min-width: 200px;
            }
            .ts-control.focus {
                border-color: #ef4444;
                box-shadow: 0 0 0 1px #ef4444;
            }
            .ts-dropdown {
                border-radius: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }
        </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Header Info --}}
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">{{ $namaHari }}, {{ now()->translatedFormat('d F Y') }}</h3>
                        <p class="text-red-100 mt-1">Monitoring Kehadiran Guru Mengajar</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-black">{{ $totalJadwal }}</div>
                        <div class="text-sm text-red-100">Total Jadwal Hari Ini</div>
                    </div>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $totalHadir }}</div>
                            <div class="text-xs text-gray-500 font-medium">Hadir</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $totalTerlambat }}</div>
                            <div class="text-xs text-gray-500 font-medium">Terlambat</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $totalTidakHadir }}</div>
                            <div class="text-xs text-gray-500 font-medium">Tidak Hadir</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $totalBelumDicatat }}</div>
                            <div class="text-xs text-gray-500 font-medium">Belum Dicatat</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Schedule List --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h3 class="text-lg font-bold text-gray-900">Jadwal Mengajar Hari Ini</h3>
                    <div class="flex items-center gap-2">
                        <label for="filterJamKe" class="text-sm font-medium text-gray-700 whitespace-nowrap">Filter Jam Ke:</label>
                        <select id="filterJamKe" onchange="filterScheduleByJam(this.value)" class="text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            <option value="all">Semua Jam Pelajaran</option>
                            @foreach($listJamKe as $item)
                                <option value="{{ $item->jam_ke }}">Jam Ke-{{ $item->jam_ke }} ({{ $item->waktu }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($jadwalGrouped as $waktu => $jadwals)
                        <div class="p-6 schedule-group" data-jam-ke="{{ $jadwals->first()->jam_ke }}">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-16 h-16 bg-red-50 rounded-xl flex items-center justify-center">
                                    <span class="text-red-600 font-black text-lg">{{ $waktu }}</span>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">Jam Ke-{{ $jadwals->first()->jam_ke }}</div>
                                    <div class="text-sm text-gray-500">{{ $jadwals->count() }} Kelas</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($jadwals as $jadwal)
                                    <div class="border border-gray-200 rounded-xl p-4 hover:border-red-300 transition-colors">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-900">{{ $jadwal->guru->nama_lengkap ?? 'Guru Tidak Tersedia' }}</div>
                                                <div class="text-sm text-gray-600 mt-1">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $jadwal->rombel->kelas->nama_kelas ?? $jadwal->rombel->nama_rombel }}</div>
                                            </div>
                                            @if($jadwal->status_absensi == 'hadir')
                                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Hadir</span>
                                            @elseif($jadwal->status_absensi == 'terlambat')
                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">Terlambat</span>
                                            @elseif($jadwal->status_absensi == 'tidak_hadir')
                                                <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Tidak Hadir</span>
                                            @elseif($jadwal->status_absensi == 'izin')
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">Izin</span>
                                            @else
                                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-full">Belum Dicatat</span>
                                            @endif
                                        </div>

                                        @if($jadwal->status_absensi == 'belum_dicatat')
                                            <div class="flex gap-2 mt-3">
                                                <button onclick="confirmHadir({{ $jadwal->id }}, '{{ addslashes($jadwal->guru->nama_lengkap ?? 'Guru') }}')" class="flex-1 px-3 py-2 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition-colors">Hadir</button>
                                                <button onclick="showReasonModal({{ $jadwal->id }}, 'terlambat', '{{ addslashes($jadwal->guru->nama_lengkap ?? 'Guru') }}')" class="flex-1 px-3 py-2 bg-yellow-600 text-white text-xs font-bold rounded-lg hover:bg-yellow-700 transition-colors">Terlambat</button>
                                                <button onclick="showReasonModal({{ $jadwal->id }}, 'tidak_hadir', '{{ addslashes($jadwal->guru->nama_lengkap ?? 'Guru') }}')" class="px-3 py-2 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-colors">Tidak Hadir</button>
                                                <button onclick="showReasonModal({{ $jadwal->id }}, 'izin', '{{ addslashes($jadwal->guru->nama_lengkap ?? 'Guru') }}')" class="px-3 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-colors">Izin</button>
                                            </div>
                                        @else
                                            <div class="mt-3 text-xs text-gray-500">
                                                @if($jadwal->absensi->waktu_absen)
                                                    Dicatat: {{ $jadwal->absensi->waktu_absen->format('H:i') }}
                                                @else
                                                    Dicatat: {{ $jadwal->absensi->created_at->format('H:i') }} (Auto)
                                                @endif
                                                @if($jadwal->absensi->keterangan)
                                                    <br>Ket: {{ $jadwal->absensi->keterangan }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-gray-500 font-medium">Tidak ada jadwal mengajar untuk hari ini</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Form for Submission --}}
    <form id="absensiForm" action="{{ route('piket.absensi-guru.store') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="jadwal_pelajaran_id" id="jadwal_id">
        <input type="hidden" name="status" id="status">
        <input type="hidden" name="keterangan" id="keterangan">
    </form>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#filterJamKe", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        });

        function confirmHadir(jadwalId, namaGuru) {
            Swal.fire({
                title: 'Konfirmasi Kehadiran',
                html: `Tandai <strong>${namaGuru}</strong> sebagai <span class="text-green-600 font-bold">HADIR</span>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hadir',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAbsensi(jadwalId, 'hadir', '');
                }
            });
        }

        function showReasonModal(jadwalId, status, namaGuru) {
            let title = '';
            let placeholder = '';
            let confirmButtonColor = '';
            let confirmButtonText = '';

            switch(status) {
                case 'terlambat':
                    title = 'Alasan Keterlambatan';
                    placeholder = 'Contoh: Terlambat 15 menit karena macet...';
                    confirmButtonColor = '#ca8a04';
                    confirmButtonText = 'Simpan Terlambat';
                    break;
                case 'tidak_hadir':
                    title = 'Alasan Tidak Hadir';
                    placeholder = 'Contoh: Sakit, ada keperluan mendadak...';
                    confirmButtonColor = '#dc2626';
                    confirmButtonText = 'Simpan Tidak Hadir';
                    break;
                case 'izin':
                    title = 'Alasan Izin';
                    placeholder = 'Contoh: Mengikuti pelatihan, rapat dinas...';
                    confirmButtonColor = '#2563eb';
                    confirmButtonText = 'Simpan Izin';
                    break;
            }

            Swal.fire({
                title: title,
                html: `<div class="text-left mb-3"><strong>${namaGuru}</strong></div>`,
                input: 'textarea',
                inputPlaceholder: placeholder,
                inputAttributes: {
                    'aria-label': 'Masukkan alasan',
                    'rows': 4
                },
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan harus diisi!';
                    }
                    if (value.length < 5) {
                        return 'Alasan minimal 5 karakter!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAbsensi(jadwalId, status, result.value);
                }
            });
        }

        function submitAbsensi(jadwalId, status, keterangan) {
            document.getElementById('jadwal_id').value = jadwalId;
            document.getElementById('status').value = status;
            document.getElementById('keterangan').value = keterangan;
            document.getElementById('absensiForm').submit();
        }

        function filterScheduleByJam(selectedJam) {
            const groups = document.querySelectorAll('.schedule-group');
            let hasVisible = false;
            
            groups.forEach(group => {
                if (selectedJam === 'all' || group.getAttribute('data-jam-ke') === selectedJam) {
                    group.style.display = 'block';
                    hasVisible = true;
                } else {
                    group.style.display = 'none';
                }
            });

            // Handle empty state if filter hides everything (though shouldn't happen with dynamic list)
            let emptyMsg = document.getElementById('empty-filter-msg');
            if (!hasVisible) {
                if (!emptyMsg) {
                    const container = document.querySelector('.divide-y');
                    emptyMsg = document.createElement('div');
                    emptyMsg.id = 'empty-filter-msg';
                    emptyMsg.className = 'p-12 text-center';
                    emptyMsg.innerHTML = `
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <p class="text-gray-500 font-medium">Tidak ada jadwal untuk jam ke-${selectedJam}</p>
                    `;
                    container.appendChild(emptyMsg);
                } else {
                    emptyMsg.style.display = 'block';
                    emptyMsg.querySelector('p').innerText = `Tidak ada jadwal untuk jam ke-${selectedJam}`;
                }
            } else if (emptyMsg) {
                emptyMsg.style.display = 'none';
            }
        }
    </script>
    @endpush
</x-app-layout>
