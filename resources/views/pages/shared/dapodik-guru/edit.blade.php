<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dapodik-guru.show', $dapodikGuru) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Edit Data Dapodik</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $dapodikGuru->nama }}</p>
            </div>
        </div>
    </x-slot>

    @php
    $inp  = 'w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    $sel  = $inp . ' bg-white';
    $lab  = 'block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1';
    $old  = fn(string $field) => old($field, $dapodikGuru->$field ?? '');
    $date = fn(string $field) => old($field, $dapodikGuru->$field?->format('Y-m-d') ?? '');
    @endphp

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('dapodik-guru.update', $dapodikGuru) }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">

                    {{-- Data Pribadi --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                            <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Data Pribadi</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="sm:col-span-2 lg:col-span-3">
                                <label class="{{ $lab }}">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="nama" value="{{ $old('nama') }}" required class="{{ $inp }}">
                                @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $lab }}">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="{{ $sel }}">
                                    <option value="">--</option>
                                    <option value="L" {{ $old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ $old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="{{ $lab }}">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" value="{{ $old('tempat_lahir') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" value="{{ $date('tanggal_lahir') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Agama</label>
                                <select name="agama" class="{{ $sel }}">
                                    <option value="">--</option>
                                    @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag)
                                        <option value="{{ $ag }}" {{ $old('agama') === $ag ? 'selected' : '' }}>{{ $ag }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $lab }}">Kewarganegaraan</label>
                                <input type="text" name="kewarganegaraan" value="{{ $old('kewarganegaraan') }}" maxlength="5" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">NIK</label>
                                <input type="text" name="nik" value="{{ $old('nik') }}" maxlength="20" class="{{ $inp }}">
                                @error('nik') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $lab }}">No. KK</label>
                                <input type="text" name="no_kk" value="{{ $old('no_kk') }}" maxlength="20" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Nama Ibu Kandung</label>
                                <input type="text" name="nama_ibu_kandung" value="{{ $old('nama_ibu_kandung') }}" class="{{ $inp }}">
                            </div>
                        </div>
                    </div>

                    {{-- Data Kepegawaian --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                            <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Data Kepegawaian</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="{{ $lab }}">NUPTK</label>
                                <input type="text" name="nuptk" value="{{ $old('nuptk') }}" maxlength="20" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">NIP</label>
                                <input type="text" name="nip" value="{{ $old('nip') }}" maxlength="30" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Status Kepegawaian</label>
                                <input type="text" name="status_kepegawaian" value="{{ $old('status_kepegawaian') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Jenis PTK</label>
                                <input type="text" name="jenis_ptk" value="{{ $old('jenis_ptk') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Tugas Tambahan</label>
                                <input type="text" name="tugas_tambahan" value="{{ $old('tugas_tambahan') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Pangkat / Golongan</label>
                                <input type="text" name="pangkat_golongan" value="{{ $old('pangkat_golongan') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Sumber Gaji</label>
                                <input type="text" name="sumber_gaji" value="{{ $old('sumber_gaji') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Lembaga Pengangkatan</label>
                                <input type="text" name="lembaga_pengangkatan" value="{{ $old('lembaga_pengangkatan') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">SK Pengangkatan</label>
                                <input type="text" name="sk_pengangkatan" value="{{ $old('sk_pengangkatan') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">TMT Pengangkatan</label>
                                <input type="date" name="tmt_pengangkatan" value="{{ $date('tmt_pengangkatan') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">SK CPNS</label>
                                <input type="text" name="sk_cpns" value="{{ $old('sk_cpns') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Tanggal CPNS</label>
                                <input type="date" name="tanggal_cpns" value="{{ $date('tanggal_cpns') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">TMT PNS</label>
                                <input type="date" name="tmt_pns" value="{{ $date('tmt_pns') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">NUKS</label>
                                <input type="text" name="nuks" value="{{ $old('nuks') }}" maxlength="30" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Karpeg</label>
                                <input type="text" name="karpeg" value="{{ $old('karpeg') }}" maxlength="20" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Karis / Karsu</label>
                                <input type="text" name="karis_karsu" value="{{ $old('karis_karsu') }}" maxlength="20" class="{{ $inp }}">
                            </div>
                        </div>
                    </div>

                    {{-- Alamat & Kontak --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg></div>
                            <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Alamat & Kontak</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="sm:col-span-2 lg:col-span-3">
                                <label class="{{ $lab }}">Alamat Jalan</label>
                                <input type="text" name="alamat_jalan" value="{{ $old('alamat_jalan') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">RT</label>
                                <input type="text" name="rt" value="{{ $old('rt') }}" maxlength="5" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">RW</label>
                                <input type="text" name="rw" value="{{ $old('rw') }}" maxlength="5" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Nama Dusun</label>
                                <input type="text" name="nama_dusun" value="{{ $old('nama_dusun') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Desa / Kelurahan</label>
                                <input type="text" name="desa_kelurahan" value="{{ $old('desa_kelurahan') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Kecamatan</label>
                                <input type="text" name="kecamatan" value="{{ $old('kecamatan') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Kode Pos</label>
                                <input type="text" name="kode_pos" value="{{ $old('kode_pos') }}" maxlength="10" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Telepon</label>
                                <input type="text" name="telepon" value="{{ $old('telepon') }}" maxlength="20" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">HP</label>
                                <input type="text" name="hp" value="{{ $old('hp') }}" maxlength="20" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Email Dapodik</label>
                                <input type="email" name="email_dapodik" value="{{ $old('email_dapodik') }}" class="{{ $inp }}">
                                @error('email_dapodik') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $lab }}">Lintang</label>
                                <input type="text" name="lintang" value="{{ $old('lintang') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Bujur</label>
                                <input type="text" name="bujur" value="{{ $old('bujur') }}" class="{{ $inp }}">
                            </div>
                        </div>
                    </div>

                    {{-- Keluarga --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-pink-50 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div>
                            <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Data Keluarga</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="{{ $lab }}">Status Perkawinan</label>
                                <select name="status_perkawinan" class="{{ $sel }}">
                                    <option value="">--</option>
                                    @foreach(['Belum Kawin','Kawin','Cerai Hidup','Cerai Mati'] as $sp)
                                        <option value="{{ $sp }}" {{ $old('status_perkawinan') === $sp ? 'selected' : '' }}>{{ $sp }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $lab }}">Nama Suami/Istri</label>
                                <input type="text" name="nama_pasangan" value="{{ $old('nama_pasangan') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">NIP Suami/Istri</label>
                                <input type="text" name="nip_pasangan" value="{{ $old('nip_pasangan') }}" maxlength="30" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Pekerjaan Suami/Istri</label>
                                <input type="text" name="pekerjaan_pasangan" value="{{ $old('pekerjaan_pasangan') }}" class="{{ $inp }}">
                            </div>
                        </div>
                    </div>

                    {{-- Keuangan --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
                            <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Data Keuangan</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="{{ $lab }}">NPWP</label>
                                <input type="text" name="npwp" value="{{ $old('npwp') }}" maxlength="30" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Nama Wajib Pajak</label>
                                <input type="text" name="nama_wajib_pajak" value="{{ $old('nama_wajib_pajak') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Bank</label>
                                <input type="text" name="bank" value="{{ $old('bank') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">No. Rekening</label>
                                <input type="text" name="no_rekening" value="{{ $old('no_rekening') }}" maxlength="30" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lab }}">Rekening Atas Nama</label>
                                <input type="text" name="rekening_atas_nama" value="{{ $old('rekening_atas_nama') }}" class="{{ $inp }}">
                            </div>
                        </div>
                    </div>

                    {{-- Sertifikasi --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-violet-50 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg></div>
                            <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Sertifikasi & Keahlian</h3>
                        </div>
                        <div class="p-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @php
                            $certFields = [
                                ['name' => 'lisensi_kepala_sekolah', 'label' => 'Lisensi Kepala Sekolah'],
                                ['name' => 'diklat_kepengawasan', 'label' => 'Diklat Kepengawasan'],
                                ['name' => 'keahlian_braille', 'label' => 'Keahlian Braille'],
                                ['name' => 'keahlian_bahasa_isyarat', 'label' => 'Bahasa Isyarat'],
                            ];
                            @endphp
                            @foreach($certFields as $cf)
                                <div>
                                    <label class="{{ $lab }}">{{ $cf['label'] }}</label>
                                    <select name="{{ $cf['name'] }}" class="{{ $sel }}">
                                        <option value="">--</option>
                                        <option value="Ya" {{ $old($cf['name']) === 'Ya' ? 'selected' : '' }}>Ya</option>
                                        <option value="Tidak" {{ $old($cf['name']) === 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pb-6">
                        <a href="{{ route('dapodik-guru.show', $dapodikGuru) }}"
                            class="px-5 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-500 transition-all shadow-md">
                            Simpan Perubahan
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

</x-app-layout>
