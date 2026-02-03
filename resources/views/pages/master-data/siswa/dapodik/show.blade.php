<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Data Dapodik Siswa
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $siswa->nama_lengkap }} - {{ $siswa->nis }}</p>
            </div>
            <div class="flex gap-2">
                @unlessrole('Waka Kesiswaan')
                <a href="{{ route('master-data.siswa.dapodik.edit', $siswa) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Data
                </a>
                @endunlessrole
                <a href="{{ route('master-data.siswa.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @php $dapodik = $siswa->dapodik; @endphp

            {{-- Data Pribadi --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Data Pribadi
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Nama</p>
                            <p class="font-medium text-gray-900">{{ $siswa->nama_lengkap ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">NIPD</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nipd ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">NISN</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nisn ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">NIK</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nik ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Jenis Kelamin</p>
                            <p class="font-medium text-gray-900">
                                {{ $dapodik->jenis_kelamin == 'L' ? 'Laki-laki' : ($dapodik->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                            </p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Tempat, Tanggal Lahir</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->tempat_lahir ?? '-' }},
                                {{ $dapodik->tanggal_lahir?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Agama</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->agama ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Rombel Saat Ini</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->rombel_saat_ini ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Sekolah Asal</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->sekolah_asal ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alamat --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Alamat & Kontak
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg md:col-span-2 lg:col-span-3">
                            <p class="text-xs text-gray-500 uppercase">Alamat</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->alamat ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">RT/RW</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->rt ?? '-' }}/{{ $dapodik->rw ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Dusun</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->dusun ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Kelurahan</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->kelurahan ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Kecamatan</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->kecamatan ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Kode Pos</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->kode_pos ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Jenis Tinggal</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->jenis_tinggal ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Alat Transportasi</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->alat_transportasi ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Telepon</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->telepon ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">HP</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->hp ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Email</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Ayah --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Data Ayah
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Nama Ayah</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nama_ayah ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Tahun Lahir</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->tahun_lahir_ayah ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">NIK Ayah</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nik_ayah ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Pendidikan</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->jenjang_pendidikan_ayah ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Pekerjaan</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->pekerjaan_ayah ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Penghasilan</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->penghasilan_ayah ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Ibu --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Data Ibu
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Nama Ibu</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nama_ibu ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Tahun Lahir</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->tahun_lahir_ibu ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">NIK Ibu</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nik_ibu ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Pendidikan</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->jenjang_pendidikan_ibu ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Pekerjaan</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->pekerjaan_ibu ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Penghasilan</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->penghasilan_ibu ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Wali --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Data Wali
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Nama Wali</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nama_wali ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Tahun Lahir</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->tahun_lahir_wali ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">NIK Wali</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nik_wali ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Pendidikan</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->jenjang_pendidikan_wali ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Pekerjaan</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->pekerjaan_wali ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Penghasilan</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->penghasilan_wali ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dokumen & Bantuan --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Dokumen & Bantuan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">SKHUN</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->skhun ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">No. Peserta Ujian Nasional</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->no_peserta_ujian_nasional ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">No. Seri Ijazah</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->no_seri_ijazah ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">No. Registrasi Akta Lahir</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->no_registrasi_akta_lahir ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">No. KK</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->no_kk ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Penerima KPS</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->penerima_kps ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">No. KPS</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->no_kps ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Penerima KIP</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->penerima_kip ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Nomor KIP</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nomor_kip ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Nama di KIP</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nama_di_kip ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Nomor KKS</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nomor_kks ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Layak PIP</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->layak_pip ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg md:col-span-2">
                            <p class="text-xs text-gray-500 uppercase">Alasan Layak PIP</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->alasan_layak_pip ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Lainnya --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Data Lainnya
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Bank</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->bank ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">No. Rekening</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->nomor_rekening_bank ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Atas Nama</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->rekening_atas_nama ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Kebutuhan Khusus</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->kebutuhan_khusus ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Anak ke-</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->anak_ke_berapa ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Jumlah Saudara</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->jumlah_saudara_kandung ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Berat Badan</p>
                            <p class="font-medium text-gray-900">
                                {{ $dapodik->berat_badan ? $dapodik->berat_badan . ' kg' : '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Tinggi Badan</p>
                            <p class="font-medium text-gray-900">
                                {{ $dapodik->tinggi_badan ? $dapodik->tinggi_badan . ' cm' : '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Lingkar Kepala</p>
                            <p class="font-medium text-gray-900">
                                {{ $dapodik->lingkar_kepala ? $dapodik->lingkar_kepala . ' cm' : '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Jarak ke Sekolah</p>
                            <p class="font-medium text-gray-900">
                                {{ $dapodik->jarak_rumah_ke_sekolah ? $dapodik->jarak_rumah_ke_sekolah . ' km' : '-' }}
                            </p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Koordinat (Lintang)</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->lintang ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Koordinat (Bujur)</p>
                            <p class="font-medium text-gray-900">{{ $dapodik->bujur ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>