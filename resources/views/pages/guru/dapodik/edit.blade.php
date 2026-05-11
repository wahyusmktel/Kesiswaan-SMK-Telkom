<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajukan Perubahan Data Dapodik</h2>
        <p class="text-sm text-gray-500 mt-1">Ubah hanya field yang perlu diperbarui, lalu kirim untuk diverifikasi Operator.</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4">
                <a href="{{ route('guru.dapodik.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Data Dapodik
                </a>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-300 rounded-xl">
                    <p class="font-bold text-red-700 text-sm mb-2">Terdapat kesalahan pada pengisian form:</p>
                    <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php $d = $guru->dapodikGuru; @endphp

            <form method="POST" action="{{ route('guru.dapodik.store-submission') }}">
                @csrf

                {{-- Data Pribadi --}}
                <div class="bg-white shadow-sm rounded-xl mb-6">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Data Pribadi
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">NIK</label>
                            <input type="text" name="nik" value="{{ old('nik', $d->nik ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="16 digit NIK" maxlength="20">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">NUPTK</label>
                            <input type="text" name="nuptk" value="{{ old('nuptk', $d->nuptk ?? $guru->nuptk ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="16 digit NUPTK" maxlength="20">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">— Pilih —</option>
                                <option value="L" {{ old('jenis_kelamin', $d->jenis_kelamin ?? $guru->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $d->jenis_kelamin ?? $guru->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Agama</label>
                            <select name="agama" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">— Pilih —</option>
                                @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $agama)
                                    <option value="{{ $agama }}" {{ old('agama', $d->agama ?? '') === $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $d->tempat_lahir ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Kota/Kabupaten">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $d->tanggal_lahir?->format('Y-m-d') ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Status Perkawinan</label>
                            <select name="status_perkawinan" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">— Pilih —</option>
                                @foreach(['Belum Kawin','Kawin','Cerai Hidup','Cerai Mati'] as $sp)
                                    <option value="{{ $sp }}" {{ old('status_perkawinan', $d->status_perkawinan ?? '') === $sp ? 'selected' : '' }}>{{ $sp }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Nama Ibu Kandung</label>
                            <input type="text" name="nama_ibu_kandung" value="{{ old('nama_ibu_kandung', $d->nama_ibu_kandung ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Nama ibu kandung">
                        </div>

                    </div>
                </div>

                {{-- Data Pasangan --}}
                <div class="bg-white shadow-sm rounded-xl mb-6">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Data Pasangan & KK
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Nama Pasangan</label>
                            <input type="text" name="nama_pasangan" value="{{ old('nama_pasangan', $d->nama_pasangan ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">NIP Pasangan</label>
                            <input type="text" name="nip_pasangan" value="{{ old('nip_pasangan', $d->nip_pasangan ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Pekerjaan Pasangan</label>
                            <input type="text" name="pekerjaan_pasangan" value="{{ old('pekerjaan_pasangan', $d->pekerjaan_pasangan ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">No. KK</label>
                            <input type="text" name="no_kk" value="{{ old('no_kk', $d->no_kk ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="16 digit No. KK" maxlength="30">
                        </div>

                    </div>
                </div>

                {{-- Alamat & Kontak --}}
                <div class="bg-white shadow-sm rounded-xl mb-6">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            Alamat & Kontak
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Alamat Jalan</label>
                            <input type="text" name="alamat_jalan" value="{{ old('alamat_jalan', $d->alamat_jalan ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Nama jalan, nomor rumah">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">RT</label>
                            <input type="text" name="rt" value="{{ old('rt', $d->rt ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="001" maxlength="5">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">RW</label>
                            <input type="text" name="rw" value="{{ old('rw', $d->rw ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="001" maxlength="5">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Dusun</label>
                            <input type="text" name="nama_dusun" value="{{ old('nama_dusun', $d->nama_dusun ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Desa/Kelurahan</label>
                            <input type="text" name="desa_kelurahan" value="{{ old('desa_kelurahan', $d->desa_kelurahan ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Kecamatan</label>
                            <input type="text" name="kecamatan" value="{{ old('kecamatan', $d->kecamatan ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Kode Pos</label>
                            <input type="text" name="kode_pos" value="{{ old('kode_pos', $d->kode_pos ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="35xxx" maxlength="10">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Telepon</label>
                            <input type="text" name="telepon" value="{{ old('telepon', $d->telepon ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="0721xxxxxx" maxlength="20">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">HP</label>
                            <input type="text" name="hp" value="{{ old('hp', $d->hp ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="08xxxxxxxxxx" maxlength="20">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Email Dapodik</label>
                            <input type="email" name="email_dapodik" value="{{ old('email_dapodik', $d->email_dapodik ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="email@contoh.com">
                        </div>

                    </div>
                </div>

                {{-- Keuangan --}}
                <div class="bg-white shadow-sm rounded-xl mb-6">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Data Keuangan
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">NPWP</label>
                            <input type="text" name="npwp" value="{{ old('npwp', $d->npwp ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="xx.xxx.xxx.x-xxx.xxx" maxlength="30">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Nama Wajib Pajak</label>
                            <input type="text" name="nama_wajib_pajak" value="{{ old('nama_wajib_pajak', $d->nama_wajib_pajak ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Bank</label>
                            <input type="text" name="bank" value="{{ old('bank', $d->bank ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="cth. BRI, BNI, Mandiri">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">No. Rekening</label>
                            <input type="text" name="no_rekening" value="{{ old('no_rekening', $d->no_rekening ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                maxlength="50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Rekening a/n</label>
                            <input type="text" name="rekening_atas_nama" value="{{ old('rekening_atas_nama', $d->rekening_atas_nama ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Karpeg</label>
                            <input type="text" name="karpeg" value="{{ old('karpeg', $d->karpeg ?? '') }}"
                                class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                maxlength="50">
                        </div>

                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('guru.dapodik.index') }}"
                        class="px-6 py-2.5 border border-gray-300 text-gray-600 font-semibold rounded-lg text-sm hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition shadow-sm">
                        Kirim Pengajuan
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
