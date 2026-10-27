<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Persetujuan Izin Keluar</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="persetujuanPiket()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Action Header Bar --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Persetujuan Izin Meninggalkan Kelas</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola dan verifikasi permohonan izin keluar atau izin dalam lingkungan siswa</p>
                </div>
                <a href="{{ route('piket.persetujuan-izin-keluar.create') }}" 
                    class="inline-flex items-center px-4 py-2.5 bg-red-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-wider hover:bg-red-700 active:bg-red-900 shadow-md shadow-red-200 transition-all gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Input Izin Siswa
                </a>
            </div>

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div
                    class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <h4 class="font-bold text-gray-700 text-sm">Daftar Pengajuan Izin</h4>
                        <span class="text-xs text-gray-400 font-medium">Menampilkan data izin siswa</span>
                    </div>

                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text"
                            class="pl-10 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                            placeholder="Cari siswa...">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Siswa</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Kelas</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Tujuan</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Tanggal Izin</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Jenis</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($daftarIzin as $izin)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs border border-indigo-200">
                                                {{ substr($izin->siswa->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $izin->siswa->name }}</div>
                                                <div class="text-xs text-gray-500">NIS:
                                                    {{ $izin->siswa->masterSiswa->nis ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $izin->siswa->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="block truncate max-w-[200px]" title="{{ $izin->tujuan }}">
                                            {{ $izin->tujuan }}
                                        </span>
                                        <span class="text-[11px] text-gray-400">Kembali: {{ \Carbon\Carbon::parse($izin->estimasi_kembali)->format('H:i') }} WIB</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">
                                        <div class="font-bold text-gray-900">
                                            {{ \Carbon\Carbon::parse($izin->created_at)->isoFormat('D MMM Y') }}
                                        </div>
                                        <div class="font-mono text-gray-500 text-[11px] mt-0.5">
                                            {{ \Carbon\Carbon::parse($izin->created_at)->format('H:i') }} WIB
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">
                                        @if(($izin->jenis_izin ?? 'keluar_sekolah') === 'keluar_sekolah')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-50 text-red-700 border border-red-200">Keluar</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">Internal</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusInfo = match ($izin->status) {
                                                'disetujui_guru_kelas' => [
                                                    'bg' => 'bg-yellow-50',
                                                    'text' => 'text-yellow-700',
                                                    'border' => 'border-yellow-200',
                                                    'label' => 'Menunggu Piket',
                                                ],
                                                'disetujui_guru_piket' => [
                                                    'bg' => 'bg-green-50',
                                                    'text' => 'text-green-700',
                                                    'border' => 'border-green-200',
                                                    'label' => 'Disetujui Piket',
                                                ],
                                                'diverifikasi_security' => [
                                                    'bg' => 'bg-indigo-50',
                                                    'text' => 'text-indigo-700',
                                                    'border' => 'border-indigo-200',
                                                    'label' => 'Di Luar Sekolah',
                                                ],
                                                'selesai' => [
                                                    'bg' => 'bg-emerald-50',
                                                    'text' => 'text-emerald-700',
                                                    'border' => 'border-emerald-200',
                                                    'label' => 'Selesai',
                                                ],
                                                'ditolak' => [
                                                    'bg' => 'bg-red-50',
                                                    'text' => 'text-red-700',
                                                    'border' => 'border-red-200',
                                                    'label' => 'Ditolak',
                                                ],
                                                default => [
                                                    'bg' => 'bg-gray-50',
                                                    'text' => 'text-gray-700',
                                                    'border' => 'border-gray-200',
                                                    'label' => $izin->status,
                                                ],
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }} {{ $statusInfo['border'] }}">
                                            {{ $statusInfo['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2 flex-wrap">

                                            @if ($izin->status == 'disetujui_guru_kelas')
                                                <button @click="openRejectModal({{ json_encode($izin) }})"
                                                    class="inline-flex items-center px-3 py-1.5 bg-white text-red-600 rounded-lg hover:bg-red-50 border border-red-200 text-xs font-semibold transition-colors shadow-sm">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Tolak
                                                </button>

                                                <form
                                                    action="{{ route('piket.persetujuan-izin-keluar.approve', $izin->id) }}"
                                                    method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" onclick="confirmApprove(this)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-500 border border-transparent text-xs font-bold transition-colors shadow-md">
                                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Setujui
                                                    </button>
                                                </form>
                                            @elseif (in_array($izin->status, ['disetujui_guru_piket', 'diverifikasi_security', 'selesai']))
                                                @php
                                                    $rowNis = $izin->siswa->masterSiswa?->nis;
                                                    $rowBarcode = null;
                                                    if ($rowNis) {
                                                        $bGen = new \Picqer\Barcode\BarcodeGeneratorPNG();
                                                        $rowBarcode = 'data:image/png;base64,' . base64_encode($bGen->getBarcode($rowNis, $bGen::TYPE_CODE_128, 2, 50));
                                                    }
                                                @endphp
                                                {{-- Tombol Tunjukkan QR Izin Digital --}}
                                                <button type="button" 
                                                    @click="openQrModal({
                                                        nama: '{{ addslashes($izin->siswa->name) }}',
                                                        nis: '{{ $rowNis ?? '-' }}',
                                                        barcodeImage: '{{ $rowBarcode }}',
                                                        kelas: '{{ $izin->siswa->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? '-' }}',
                                                        tujuan: '{{ addslashes($izin->tujuan) }}',
                                                        jenis: '{{ $izin->jenis_izin }}',
                                                        status: '{{ $izin->status }}',
                                                        estimasi: '{{ \Carbon\Carbon::parse($izin->estimasi_kembali)->format('H:i') }} WIB',
                                                        qrImage: 'data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(220)->margin(1)->generate(route('security.verifikasi.process-scan', $izin->uuid))) }}',
                                                        printUrl: '{{ route('piket.persetujuan-izin-keluar.print', $izin->id) }}'
                                                    })"
                                                    class="inline-flex items-center px-2.5 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 border border-emerald-200 text-xs font-semibold transition-colors shadow-sm gap-1"
                                                    title="Tunjukkan QR Izin Digital untuk Security">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                                    Tunjukkan QR
                                                </button>

                                                {{-- Tombol Cetak Surat PDF --}}
                                                <a href="{{ route('piket.persetujuan-izin-keluar.print', $izin->id) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 border border-indigo-200 text-xs font-semibold transition-colors shadow-sm gap-1">
                                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Cetak Surat
                                                </a>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-base font-medium">Tidak ada pengajuan izin yang perlu diproses.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
                    {{ $daftarIzin->withQueryString()->links('vendor.pagination.compact-light') }}
                </div>
            </div>
        </div>

        {{-- Modal Tolak Izin --}}
        <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="isOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
                @click="isOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="isOpen"
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                    <div class="bg-red-50 px-4 py-3 sm:px-6 border-b border-red-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold leading-6 text-red-800">Tolak Izin Siswa</h3>
                        <button @click="isOpen = false"
                            class="text-red-400 hover:text-red-600 focus:outline-none"><svg class="h-6 w-6"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                                    placeholder="Contoh: Jam pelajaran masih panjang..."></textarea>
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

        {{-- Modal QR Code Surat Izin Digital --}}
        <div x-show="isQrOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-qr-title" role="dialog" aria-modal="true">
            <div x-show="isQrOpen" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                @click="isQrOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="isQrOpen"
                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
                    
                    {{-- Header Modal --}}
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 bg-gradient-to-r from-emerald-50 to-teal-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-extrabold text-gray-900 leading-tight">Surat Izin Keluar Sekolah</h4>
                                <p class="text-xs text-gray-500 font-medium">Verifikasi Security Gerbang</p>
                            </div>
                        </div>
                        <button type="button" 
                            @click="isQrOpen = false" 
                            class="rounded-xl p-2 text-gray-400 hover:text-gray-600 hover:bg-white transition-all focus:outline-none cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Isi Modal --}}
                    <div class="p-6 text-center">
                        <div class="mb-4 bg-gray-50 rounded-2xl p-4 border border-gray-100 text-left space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 font-bold uppercase">Nama Siswa</span>
                                <span class="text-sm font-black text-gray-900" x-text="qrData.nama"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 font-bold uppercase">NIS / Kelas</span>
                                <span class="text-xs font-bold text-gray-700" x-text="(qrData.nis || '-') + ' • ' + (qrData.kelas || '-')"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 font-bold uppercase">Tujuan / Keperluan</span>
                                <span class="text-xs font-bold text-indigo-600 text-right max-w-[200px] truncate" x-text="qrData.tujuan"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 font-bold uppercase">Estimasi Kembali</span>
                                <span class="text-xs font-black text-red-600" x-text="qrData.estimasi"></span>
                            </div>
                        </div>

                        {{-- QR Code & Barcode Box --}}
                        <div class="inline-block p-4 sm:p-5 bg-white rounded-2xl border-2 border-dashed border-emerald-300 shadow-sm mb-4 w-full max-w-sm">
                            <div class="w-44 h-44 sm:w-48 sm:h-48 flex items-center justify-center mx-auto overflow-hidden">
                                <img :src="qrData.qrImage" alt="QR Code Security" class="w-full h-full object-contain">
                            </div>

                            <template x-if="qrData.barcodeImage">
                                <div class="mt-4 pt-3 border-t border-dashed border-gray-200 flex flex-col items-center">
                                    <img :src="qrData.barcodeImage" alt="Barcode NIPD" class="h-11 w-auto max-w-full object-contain">
                                    <span class="font-mono font-bold text-xs text-gray-700 tracking-widest mt-1" x-text="`*${qrData.nis}*`"></span>
                                </div>
                            </template>

                            <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-black uppercase tracking-wider border border-emerald-100">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Scan Security Gerbang (QR / Barcode)
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 leading-relaxed">
                            Arahkan kamera (QR Code) atau alat scanner barcode PC petugas Security ke kode ini untuk memproses verifikasi siswa secara otomatis.
                        </p>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex gap-3 border-t border-gray-100">
                        <a :href="qrData.printUrl" target="_blank"
                            class="flex-1 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-50 transition text-center flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak PDF
                        </a>
                        <button type="button" @click="isQrOpen = false"
                            class="flex-1 py-2.5 bg-gray-900 text-white font-bold text-sm rounded-xl hover:bg-gray-800 transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmApprove(button) {
                Swal.fire({
                    title: 'Setujui Izin?',
                    text: "Siswa akan diizinkan meninggalkan lingkungan sekolah.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a', // Green
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Setujui!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            }

            function persetujuanPiket() {
                return {
                    isOpen: false,
                    selectedItem: {},
                    rejectUrl: '',

                    // State QR Modal
                    isQrOpen: false,
                    qrData: {},

                    openRejectModal(item) {
                        this.isOpen = true;
                        this.selectedItem = item;
                        this.rejectUrl = '{{ url('piket/persetujuan-izin-keluar') }}/' + item.id + '/reject';
                    },

                    openQrModal(data) {
                        this.qrData = data;
                        this.isQrOpen = true;
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
