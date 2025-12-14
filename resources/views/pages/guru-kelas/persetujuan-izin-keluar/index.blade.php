<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Persetujuan Izin Keluar</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="persetujuanGuruKelas()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if ($jadwalSaatIni)
                <div
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="relative flex h-3 w-3">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            <p class="text-xs font-bold uppercase tracking-wider text-blue-200">Sedang Mengajar</p>
                        </div>
                        <h3 class="text-2xl font-bold">{{ $jadwalSaatIni->rombel->kelas->nama_kelas }}</h3>
                        <p class="text-blue-100 text-sm">{{ $jadwalSaatIni->mataPelajaran->nama_mapel }}</p>
                    </div>
                    <div class="text-right">
                        <span class="block text-4xl font-black">{{ $pengajuanIzin->count() }}</span>
                        <span class="text-sm text-blue-200 uppercase font-bold">Permintaan Izin</span>
                    </div>
                </div>

                @if ($pengajuanIzin->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($pengajuanIzin as $izin)
                            <div
                                class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-10 w-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm border border-indigo-100">
                                                {{ substr($izin->siswa->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 leading-tight">
                                                    {{ $izin->siswa->name }}</h4>
                                                <p class="text-xs text-gray-500">{{ $izin->created_at->format('H:i') }}
                                                    WIB</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Tujuan</p>
                                            <p class="text-gray-800 font-medium text-sm">{{ $izin->tujuan }}</p>
                                        </div>

                                        @if ($izin->keterangan)
                                            <div>
                                                <p
                                                    class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                    Keterangan</p>
                                                <p class="text-gray-600 text-xs italic">"{{ $izin->keterangan }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex gap-3">
                                    <button @click="openRejectModal({{ json_encode($izin) }})"
                                        class="flex-1 py-2 bg-white border border-red-200 text-red-600 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-red-50 transition-colors">
                                        Tolak
                                    </button>

                                    <form action="{{ route('guru-kelas.persetujuan-izin-keluar.approve', $izin->id) }}"
                                        method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" onclick="confirmApprove(this)"
                                            class="w-full py-2 bg-green-600 text-white rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-green-500 shadow-sm transition-all transform hover:-translate-y-0.5">
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div
                        class="flex flex-col items-center justify-center py-12 bg-white rounded-2xl border border-dashed border-gray-300">
                        <div class="p-4 bg-green-50 rounded-full mb-3">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Kelas Aman!</h3>
                        <p class="text-gray-500">Tidak ada siswa yang meminta izin keluar saat ini.</p>
                    </div>
                @endif
            @else
                <div
                    class="flex flex-col items-center justify-center min-h-[400px] bg-white rounded-2xl border border-gray-200 shadow-sm text-center px-4">
                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486831.png" alt="No Class"
                        class="w-32 h-32 mb-6 opacity-75 grayscale">
                    <h3 class="text-xl font-bold text-gray-800">Sedang Tidak Mengajar</h3>
                    <p class="text-gray-500 max-w-md mt-2">Anda tidak memiliki jadwal aktif saat ini. Menu ini hanya
                        aktif saat jam pelajaran berlangsung.</p>
                    <a href="{{ route('guru-kelas.dashboard.index') }}"
                        class="mt-6 px-6 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-500 transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            @endif

        </div>

        <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="isOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
                @click="isOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="isOpen"
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                    <div class="bg-red-50 px-4 py-3 sm:px-6 border-b border-red-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold leading-6 text-red-800">Tolak Izin Siswa</h3>
                        <button @click="isOpen = false" class="text-red-400 hover:text-red-600 focus:outline-none"><svg
                                class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>

                    <form :action="rejectUrl" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="px-4 py-5 sm:p-6 space-y-4">
                            <div class="bg-white p-3 rounded-lg border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Siswa</p>
                                <p class="text-gray-900 font-medium" x-text="selectedItem.siswa?.name"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Alasan Penolakan</label>
                                <textarea name="alasan_penolakan" rows="3" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                    placeholder="Contoh: Terlalu sering keluar..."></textarea>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                                Tolak Izin
                            </button>
                            <button type="button" @click="isOpen = false"
                                class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmApprove(button) {
                Swal.fire({
                    title: 'Izinkan Keluar?',
                    text: "Siswa akan diarahkan ke Guru Piket.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a', // Green
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Izinkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            }

            function persetujuanGuruKelas() {
                return {
                    isOpen: false,
                    selectedItem: {},
                    rejectUrl: '',

                    openRejectModal(item) {
                        this.isOpen = true;
                        this.selectedItem = item;
                        // URL Builder
                        this.rejectUrl = '{{ url('guru-kelas/persetujuan-izin-keluar') }}/' + item.id + '/reject';
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
