<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Dapodik Saya</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $guru->nama_lengkap }} — {{ $guru->nuptk ?? 'NUPTK belum diisi' }}</p>
            </div>
            <div class="flex gap-2">
                @unless($pendingSubmission)
                    <a href="{{ route('guru.dapodik.edit') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Ajukan Perubahan
                    </a>
                @endunless
                <a href="{{ route('guru.dapodik.history') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Riwayat Pengajuan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg text-sm">{{ session('warning') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            {{-- Pending submission banner --}}
            @if($pendingSubmission)
                <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-300 rounded-xl">
                    <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-amber-800">Pengajuan Sedang Diproses</p>
                        <p class="text-xs text-amber-700 mt-0.5">
                            Pengajuan perubahan data Anda sedang menunggu verifikasi Operator
                            (dikirim {{ $pendingSubmission->submitted_at->diffForHumans() }}).
                            Anda tidak dapat mengajukan perubahan baru hingga pengajuan ini selesai diproses.
                        </p>
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach(array_keys($pendingSubmission->new_data) as $field)
                                <span class="text-xs px-2 py-0.5 bg-amber-100 text-amber-800 rounded font-medium">{{ $field }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Rejected submission banner --}}
            @if($latestRejected)
                <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-300 rounded-xl">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-red-800">Pengajuan Terakhir Ditolak</p>
                        <p class="text-xs text-red-700 mt-0.5"><strong>Alasan:</strong> {{ $latestRejected->rejection_reason }}</p>
                        <p class="text-xs text-red-500 mt-1">Silakan perbaiki dan ajukan ulang.</p>
                    </div>
                </div>
            @endif

            @php $d = $guru->dapodikGuru; @endphp

            {{-- Data Pribadi --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Data Pribadi
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $personalFields = [
                                'Nama Lengkap'    => $d->nama ?? $guru->nama_lengkap,
                                'NIK'             => $d->nik ?? null,
                                'NUPTK'           => $d->nuptk ?? $guru->nuptk,
                                'NIP'             => $d->nip ?? null,
                                'Jenis Kelamin'   => $d?->jenis_kelamin_label ?? '-',
                                'Tempat Lahir'    => $d->tempat_lahir ?? null,
                                'Tanggal Lahir'   => $d->tanggal_lahir?->translatedFormat('d F Y') ?? null,
                                'Agama'           => $d->agama ?? null,
                                'Kewarganegaraan' => $d->kewarganegaraan ?? null,
                                'Status Perkawinan' => $d->status_perkawinan ?? null,
                                'Nama Ibu Kandung'  => $d->nama_ibu_kandung ?? null,
                            ];
                        @endphp
                        @foreach($personalFields as $label => $value)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase tracking-wider">{{ $label }}</p>
                                <p class="font-medium text-gray-900 mt-0.5">{{ $value ?: '—' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Kepegawaian --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Data Kepegawaian
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $employFields = [
                                'Status Kepegawaian' => $d->status_kepegawaian ?? null,
                                'Jenis PTK'          => $d->jenis_ptk ?? null,
                                'Tugas Tambahan'     => $d->tugas_tambahan ?? null,
                                'Pangkat/Golongan'   => $d->pangkat_golongan ?? null,
                                'TMT Pengangkatan'   => $d->tmt_pengangkatan?->translatedFormat('d F Y') ?? null,
                                'Lembaga Pengangkatan' => $d->lembaga_pengangkatan ?? null,
                                'Sumber Gaji'        => $d->sumber_gaji ?? null,
                                'SK Pengangkatan'    => $d->sk_pengangkatan ?? null,
                                'TMT PNS'            => $d->tmt_pns?->translatedFormat('d F Y') ?? null,
                            ];
                        @endphp
                        @foreach($employFields as $label => $value)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase tracking-wider">{{ $label }}</p>
                                <p class="font-medium text-gray-900 mt-0.5">{{ $value ?: '—' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Alamat & Kontak --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        Alamat & Kontak
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $addrFields = [
                                'Alamat Jalan'   => $d->alamat_jalan ?? null,
                                'RT/RW'          => ($d->rt && $d->rw) ? $d->rt . '/' . $d->rw : null,
                                'Dusun'          => $d->nama_dusun ?? null,
                                'Desa/Kelurahan' => $d->desa_kelurahan ?? null,
                                'Kecamatan'      => $d->kecamatan ?? null,
                                'Kode Pos'       => $d->kode_pos ?? null,
                                'Telepon'        => $d->telepon ?? null,
                                'HP'             => $d->hp ?? null,
                                'Email Dapodik'  => $d->email_dapodik ?? null,
                            ];
                        @endphp
                        @foreach($addrFields as $label => $value)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase tracking-wider">{{ $label }}</p>
                                <p class="font-medium text-gray-900 mt-0.5">{{ $value ?: '—' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Keuangan --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Data Keuangan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $finFields = [
                                'NPWP'              => $d->npwp ?? null,
                                'Nama Wajib Pajak'  => $d->nama_wajib_pajak ?? null,
                                'Bank'              => $d->bank ?? null,
                                'No. Rekening'      => $d->no_rekening ?? null,
                                'Rekening a/n'      => $d->rekening_atas_nama ?? null,
                                'Karpeg'            => $d->karpeg ?? null,
                            ];
                        @endphp
                        @foreach($finFields as $label => $value)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase tracking-wider">{{ $label }}</p>
                                <p class="font-medium text-gray-900 mt-0.5">{{ $value ?: '—' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
