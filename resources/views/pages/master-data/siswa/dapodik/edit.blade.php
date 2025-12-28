<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Data Dapodik Siswa
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $siswa->nama_lengkap }} - {{ $siswa->nis }}</p>
            </div>
            <a href="{{ route('master-data.siswa.dapodik.show', $siswa) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php $dapodik = $siswa->dapodik; @endphp

            <form action="{{ route('master-data.siswa.dapodik.update', $siswa) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Data Pribadi --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Data Pribadi
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIPD</label>
                                <input type="text" name="nipd" value="{{ old('nipd', $dapodik->nipd) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
                                <input type="text" name="nisn" value="{{ old('nisn', $dapodik->nisn) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                                <input type="text" name="nik" value="{{ old('nik', $dapodik->nik) }}" maxlength="20" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin', $dapodik->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $dapodik->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $dapodik->tempat_lahir) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $dapodik->tanggal_lahir?->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                                <select name="agama" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" {{ old('agama', $dapodik->agama) == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rombel Saat Ini</label>
                                <input type="text" name="rombel_saat_ini" value="{{ old('rombel_saat_ini', $dapodik->rombel_saat_ini) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sekolah Asal</label>
                                <input type="text" name="sekolah_asal" value="{{ old('sekolah_asal', $dapodik->sekolah_asal) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Alamat & Kontak --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Alamat & Kontak
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="md:col-span-2 lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                <textarea name="alamat" rows="2" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">{{ old('alamat', $dapodik->alamat) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">RT</label>
                                <input type="text" name="rt" value="{{ old('rt', $dapodik->rt) }}" maxlength="5" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                                <input type="text" name="rw" value="{{ old('rw', $dapodik->rw) }}" maxlength="5" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dusun</label>
                                <input type="text" name="dusun" value="{{ old('dusun', $dapodik->dusun) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                                <input type="text" name="kelurahan" value="{{ old('kelurahan', $dapodik->kelurahan) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                                <input type="text" name="kecamatan" value="{{ old('kecamatan', $dapodik->kecamatan) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                                <input type="text" name="kode_pos" value="{{ old('kode_pos', $dapodik->kode_pos) }}" maxlength="10" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Tinggal</label>
                                <input type="text" name="jenis_tinggal" value="{{ old('jenis_tinggal', $dapodik->jenis_tinggal) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alat Transportasi</label>
                                <input type="text" name="alat_transportasi" value="{{ old('alat_transportasi', $dapodik->alat_transportasi) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                                <input type="text" name="telepon" value="{{ old('telepon', $dapodik->telepon) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">HP</label>
                                <input type="text" name="hp" value="{{ old('hp', $dapodik->hp) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $dapodik->email) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Ayah --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Data Ayah
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
                                <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $dapodik->nama_ayah) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Lahir</label>
                                <input type="text" name="tahun_lahir_ayah" value="{{ old('tahun_lahir_ayah', $dapodik->tahun_lahir_ayah) }}" maxlength="4" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIK Ayah</label>
                                <input type="text" name="nik_ayah" value="{{ old('nik_ayah', $dapodik->nik_ayah) }}" maxlength="20" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                                <input type="text" name="jenjang_pendidikan_ayah" value="{{ old('jenjang_pendidikan_ayah', $dapodik->jenjang_pendidikan_ayah) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                                <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $dapodik->pekerjaan_ayah) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penghasilan</label>
                                <input type="text" name="penghasilan_ayah" value="{{ old('penghasilan_ayah', $dapodik->penghasilan_ayah) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Ibu --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Data Ibu
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
                                <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $dapodik->nama_ibu) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Lahir</label>
                                <input type="text" name="tahun_lahir_ibu" value="{{ old('tahun_lahir_ibu', $dapodik->tahun_lahir_ibu) }}" maxlength="4" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIK Ibu</label>
                                <input type="text" name="nik_ibu" value="{{ old('nik_ibu', $dapodik->nik_ibu) }}" maxlength="20" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                                <input type="text" name="jenjang_pendidikan_ibu" value="{{ old('jenjang_pendidikan_ibu', $dapodik->jenjang_pendidikan_ibu) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                                <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $dapodik->pekerjaan_ibu) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penghasilan</label>
                                <input type="text" name="penghasilan_ibu" value="{{ old('penghasilan_ibu', $dapodik->penghasilan_ibu) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Wali --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Data Wali
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Wali</label>
                                <input type="text" name="nama_wali" value="{{ old('nama_wali', $dapodik->nama_wali) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Lahir</label>
                                <input type="text" name="tahun_lahir_wali" value="{{ old('tahun_lahir_wali', $dapodik->tahun_lahir_wali) }}" maxlength="4" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIK Wali</label>
                                <input type="text" name="nik_wali" value="{{ old('nik_wali', $dapodik->nik_wali) }}" maxlength="20" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                                <input type="text" name="jenjang_pendidikan_wali" value="{{ old('jenjang_pendidikan_wali', $dapodik->jenjang_pendidikan_wali) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                                <input type="text" name="pekerjaan_wali" value="{{ old('pekerjaan_wali', $dapodik->pekerjaan_wali) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penghasilan</label>
                                <input type="text" name="penghasilan_wali" value="{{ old('penghasilan_wali', $dapodik->penghasilan_wali) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dokumen & Bantuan --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Dokumen & Bantuan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SKHUN</label>
                                <input type="text" name="skhun" value="{{ old('skhun', $dapodik->skhun) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Peserta Ujian Nasional</label>
                                <input type="text" name="no_peserta_ujian_nasional" value="{{ old('no_peserta_ujian_nasional', $dapodik->no_peserta_ujian_nasional) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Seri Ijazah</label>
                                <input type="text" name="no_seri_ijazah" value="{{ old('no_seri_ijazah', $dapodik->no_seri_ijazah) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Registrasi Akta Lahir</label>
                                <input type="text" name="no_registrasi_akta_lahir" value="{{ old('no_registrasi_akta_lahir', $dapodik->no_registrasi_akta_lahir) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. KK</label>
                                <input type="text" name="no_kk" value="{{ old('no_kk', $dapodik->no_kk) }}" maxlength="20" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penerima KPS</label>
                                <select name="penerima_kps" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                                    <option value="">-- Pilih --</option>
                                    <option value="Ya" {{ old('penerima_kps', $dapodik->penerima_kps) == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    <option value="Tidak" {{ old('penerima_kps', $dapodik->penerima_kps) == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. KPS</label>
                                <input type="text" name="no_kps" value="{{ old('no_kps', $dapodik->no_kps) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penerima KIP</label>
                                <select name="penerima_kip" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                                    <option value="">-- Pilih --</option>
                                    <option value="Ya" {{ old('penerima_kip', $dapodik->penerima_kip) == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    <option value="Tidak" {{ old('penerima_kip', $dapodik->penerima_kip) == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor KIP</label>
                                <input type="text" name="nomor_kip" value="{{ old('nomor_kip', $dapodik->nomor_kip) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama di KIP</label>
                                <input type="text" name="nama_di_kip" value="{{ old('nama_di_kip', $dapodik->nama_di_kip) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor KKS</label>
                                <input type="text" name="nomor_kks" value="{{ old('nomor_kks', $dapodik->nomor_kks) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Layak PIP</label>
                                <select name="layak_pip" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                                    <option value="">-- Pilih --</option>
                                    <option value="Ya" {{ old('layak_pip', $dapodik->layak_pip) == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    <option value="Tidak" {{ old('layak_pip', $dapodik->layak_pip) == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Layak PIP</label>
                                <textarea name="alasan_layak_pip" rows="2" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">{{ old('alasan_layak_pip', $dapodik->alasan_layak_pip) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Lainnya --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Data Lainnya
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bank</label>
                                <input type="text" name="bank" value="{{ old('bank', $dapodik->bank) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Rekening</label>
                                <input type="text" name="nomor_rekening_bank" value="{{ old('nomor_rekening_bank', $dapodik->nomor_rekening_bank) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Atas Nama</label>
                                <input type="text" name="rekening_atas_nama" value="{{ old('rekening_atas_nama', $dapodik->rekening_atas_nama) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kebutuhan Khusus</label>
                                <input type="text" name="kebutuhan_khusus" value="{{ old('kebutuhan_khusus', $dapodik->kebutuhan_khusus) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Anak ke-</label>
                                <input type="number" name="anak_ke_berapa" value="{{ old('anak_ke_berapa', $dapodik->anak_ke_berapa) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Saudara Kandung</label>
                                <input type="number" name="jumlah_saudara_kandung" value="{{ old('jumlah_saudara_kandung', $dapodik->jumlah_saudara_kandung) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Berat Badan (kg)</label>
                                <input type="number" name="berat_badan" value="{{ old('berat_badan', $dapodik->berat_badan) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi Badan (cm)</label>
                                <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $dapodik->tinggi_badan) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lingkar Kepala (cm)</label>
                                <input type="number" name="lingkar_kepala" value="{{ old('lingkar_kepala', $dapodik->lingkar_kepala) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jarak ke Sekolah (km)</label>
                                <input type="number" step="0.01" name="jarak_rumah_ke_sekolah" value="{{ old('jarak_rumah_ke_sekolah', $dapodik->jarak_rumah_ke_sekolah) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Koordinat (Lintang)</label>
                                <input type="text" name="lintang" value="{{ old('lintang', $dapodik->lintang) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Koordinat (Bujur)</label>
                                <input type="text" name="bujur" value="{{ old('bujur', $dapodik->bujur) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('master-data.siswa.dapodik.show', $siswa) }}"
                        class="inline-flex items-center px-6 py-3 bg-gray-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
