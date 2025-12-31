<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ajukan Perubahan Data Dapodik
                </h2>
                <p class="text-sm text-gray-500 mt-1">Lengkapi data dan lampirkan dokumen pendukung untuk verifikasi.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('siswa.dapodik.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition shadow-sm">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{
        form: {
            nama_lengkap: '{{ $siswa->nama_lengkap }}',
            nipd: '{{ $siswa->dapodik->nipd ?? '' }}',
            nisn: '{{ $siswa->dapodik->nisn ?? '' }}',
            nik: '{{ $siswa->dapodik->nik ?? '' }}',
            jenis_kelamin: '{{ $siswa->dapodik->jenis_kelamin ?? '' }}',
            tempat_lahir: '{{ $siswa->dapodik->tempat_lahir ?? '' }}',
            tanggal_lahir: '{{ $siswa->dapodik->tanggal_lahir?->format('Y-m-d') ?? '' }}',
            agama: '{{ $siswa->dapodik->agama ?? '' }}',
            sekolah_asal: '{{ $siswa->dapodik->sekolah_asal ?? '' }}',
            alamat: '{{ $siswa->dapodik->alamat ?? '' }}',
            rt: '{{ $siswa->dapodik->rt ?? '' }}',
            rw: '{{ $siswa->dapodik->rw ?? '' }}',
            dusun: '{{ $siswa->dapodik->dusun ?? '' }}',
            kelurahan: '{{ $siswa->dapodik->kelurahan ?? '' }}',
            kecamatan: '{{ $siswa->dapodik->kecamatan ?? '' }}',
            kode_pos: '{{ $siswa->dapodik->kode_pos ?? '' }}',
            jenis_tinggal: '{{ $siswa->dapodik->jenis_tinggal ?? '' }}',
            alat_transportasi: '{{ $siswa->dapodik->alat_transportasi ?? '' }}',
            telepon: '{{ $siswa->dapodik->telepon ?? '' }}',
            hp: '{{ $siswa->dapodik->hp ?? '' }}',
            email: '{{ $siswa->dapodik->email ?? '' }}',
            nama_ayah: '{{ $siswa->dapodik->nama_ayah ?? '' }}',
            tahun_lahir_ayah: '{{ $siswa->dapodik->tahun_lahir_ayah ?? '' }}',
            nik_ayah: '{{ $siswa->dapodik->nik_ayah ?? '' }}',
            jenjang_pendidikan_ayah: '{{ $siswa->dapodik->jenjang_pendidikan_ayah ?? '' }}',
            pekerjaan_ayah: '{{ $siswa->dapodik->pekerjaan_ayah ?? '' }}',
            penghasilan_ayah: '{{ $siswa->dapodik->penghasilan_ayah ?? '' }}',
            nama_ibu: '{{ $siswa->dapodik->nama_ibu ?? '' }}',
            tahun_lahir_ibu: '{{ $siswa->dapodik->tahun_lahir_ibu ?? '' }}',
            nik_ibu: '{{ $siswa->dapodik->nik_ibu ?? '' }}',
            jenjang_pendidikan_ibu: '{{ $siswa->dapodik->jenjang_pendidikan_ibu ?? '' }}',
            pekerjaan_ibu: '{{ $siswa->dapodik->pekerjaan_ibu ?? '' }}',
            penghasilan_ibu: '{{ $siswa->dapodik->penghasilan_ibu ?? '' }}',
            nama_wali: '{{ $siswa->dapodik->nama_wali ?? '' }}',
            tahun_lahir_wali: '{{ $siswa->dapodik->tahun_lahir_wali ?? '' }}',
            nik_wali: '{{ $siswa->dapodik->nik_wali ?? '' }}',
            jenjang_pendidikan_wali: '{{ $siswa->dapodik->jenjang_pendidikan_wali ?? '' }}',
            pekerjaan_wali: '{{ $siswa->dapodik->pekerjaan_wali ?? '' }}',
            penghasilan_wali: '{{ $siswa->dapodik->penghasilan_wali ?? '' }}',
            no_seri_ijazah: '{{ $siswa->dapodik->no_seri_ijazah ?? '' }}',
            no_registrasi_akta_lahir: '{{ $siswa->dapodik->no_registrasi_akta_lahir ?? '' }}',
            no_kk: '{{ $siswa->dapodik->no_kk ?? '' }}',
            penerima_kps: '{{ $siswa->dapodik->penerima_kps ?? '' }}',
            no_kps: '{{ $siswa->dapodik->no_kps ?? '' }}',
            penerima_kip: '{{ $siswa->dapodik->penerima_kip ?? '' }}',
            nomor_kip: '{{ $siswa->dapodik->nomor_kip ?? '' }}',
            nama_di_kip: '{{ $siswa->dapodik->nama_di_kip ?? '' }}',
            nomor_kks: '{{ $siswa->dapodik->nomor_kks ?? '' }}',
            bank: '{{ $siswa->dapodik->bank ?? '' }}',
            nomor_rekening_bank: '{{ $siswa->dapodik->nomor_rekening_bank ?? '' }}',
            rekening_atas_nama: '{{ $siswa->dapodik->rekening_atas_nama ?? '' }}',
            anak_ke_berapa: '{{ $siswa->dapodik->anak_ke_berapa ?? '' }}',
            jumlah_saudara_kandung: '{{ $siswa->dapodik->jumlah_saudara_kandung ?? '' }}',
        },
        original: {
            nama_lengkap: '{{ $siswa->nama_lengkap }}',
            nipd: '{{ $siswa->dapodik->nipd ?? '' }}',
            nisn: '{{ $siswa->dapodik->nisn ?? '' }}',
            nik: '{{ $siswa->dapodik->nik ?? '' }}',
            jenis_kelamin: '{{ $siswa->dapodik->jenis_kelamin ?? '' }}',
            tempat_lahir: '{{ $siswa->dapodik->tempat_lahir ?? '' }}',
            tanggal_lahir: '{{ $siswa->dapodik->tanggal_lahir?->format('Y-m-d') ?? '' }}',
            agama: '{{ $siswa->dapodik->agama ?? '' }}',
            sekolah_asal: '{{ $siswa->dapodik->sekolah_asal ?? '' }}',
            alamat: '{{ $siswa->dapodik->alamat ?? '' }}',
            rt: '{{ $siswa->dapodik->rt ?? '' }}',
            rw: '{{ $siswa->dapodik->rw ?? '' }}',
            dusun: '{{ $siswa->dapodik->dusun ?? '' }}',
            kelurahan: '{{ $siswa->dapodik->kelurahan ?? '' }}',
            kecamatan: '{{ $siswa->dapodik->kecamatan ?? '' }}',
            kode_pos: '{{ $siswa->dapodik->kode_pos ?? '' }}',
            jenis_tinggal: '{{ $siswa->dapodik->jenis_tinggal ?? '' }}',
            alat_transportasi: '{{ $siswa->dapodik->alat_transportasi ?? '' }}',
            telepon: '{{ $siswa->dapodik->telepon ?? '' }}',
            hp: '{{ $siswa->dapodik->hp ?? '' }}',
            email: '{{ $siswa->dapodik->email ?? '' }}',
            nama_ayah: '{{ $siswa->dapodik->nama_ayah ?? '' }}',
            tahun_lahir_ayah: '{{ $siswa->dapodik->tahun_lahir_ayah ?? '' }}',
            nik_ayah: '{{ $siswa->dapodik->nik_ayah ?? '' }}',
            jenjang_pendidikan_ayah: '{{ $siswa->dapodik->jenjang_pendidikan_ayah ?? '' }}',
            pekerjaan_ayah: '{{ $siswa->dapodik->pekerjaan_ayah ?? '' }}',
            penghasilan_ayah: '{{ $siswa->dapodik->penghasilan_ayah ?? '' }}',
            nama_ibu: '{{ $siswa->dapodik->nama_ibu ?? '' }}',
            tahun_lahir_ibu: '{{ $siswa->dapodik->tahun_lahir_ibu ?? '' }}',
            nik_ibu: '{{ $siswa->dapodik->nik_ibu ?? '' }}',
            jenjang_pendidikan_ibu: '{{ $siswa->dapodik->jenjang_pendidikan_ibu ?? '' }}',
            pekerjaan_ibu: '{{ $siswa->dapodik->pekerjaan_ibu ?? '' }}',
            penghasilan_ibu: '{{ $siswa->dapodik->penghasilan_ibu ?? '' }}',
            nama_wali: '{{ $siswa->dapodik->nama_wali ?? '' }}',
            tahun_lahir_wali: '{{ $siswa->dapodik->tahun_lahir_wali ?? '' }}',
            nik_wali: '{{ $siswa->dapodik->nik_wali ?? '' }}',
            jenjang_pendidikan_wali: '{{ $siswa->dapodik->jenjang_pendidikan_wali ?? '' }}',
            pekerjaan_wali: '{{ $siswa->dapodik->pekerjaan_wali ?? '' }}',
            penghasilan_wali: '{{ $siswa->dapodik->penghasilan_wali ?? '' }}',
            no_seri_ijazah: '{{ $siswa->dapodik->no_seri_ijazah ?? '' }}',
            no_registrasi_akta_lahir: '{{ $siswa->dapodik->no_registrasi_akta_lahir ?? '' }}',
            no_kk: '{{ $siswa->dapodik->no_kk ?? '' }}',
            penerima_kps: '{{ $siswa->dapodik->penerima_kps ?? '' }}',
            no_kps: '{{ $siswa->dapodik->no_kps ?? '' }}',
            penerima_kip: '{{ $siswa->dapodik->penerima_kip ?? '' }}',
            nomor_kip: '{{ $siswa->dapodik->nomor_kip ?? '' }}',
            nama_di_kip: '{{ $siswa->dapodik->nama_di_kip ?? '' }}',
            nomor_kks: '{{ $siswa->dapodik->nomor_kks ?? '' }}',
            bank: '{{ $siswa->dapodik->bank ?? '' }}',
            nomor_rekening_bank: '{{ $siswa->dapodik->nomor_rekening_bank ?? '' }}',
            rekening_atas_nama: '{{ $siswa->dapodik->rekening_atas_nama ?? '' }}',
            anak_ke_berapa: '{{ $siswa->dapodik->anak_ke_berapa ?? '' }}',
            jumlah_saudara_kandung: '{{ $siswa->dapodik->jumlah_saudara_kandung ?? '' }}',
        },
        changed(keys) {
            return keys.some(key => this.form[key] != this.original[key]);
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('siswa.dapodik.store-submission') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                        <p class="font-bold mb-1">Terjadi kesalahan:</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Data Pribadi --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6 border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Data Pribadi
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="md:col-span-2 lg:col-span-3">
                                <label class="block text-xs text-gray-500 uppercase font-bold">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" x-model="form.nama_lengkap" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">NIPD</label>
                                <input type="text" name="nipd" x-model="form.nipd" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">NISN</label>
                                <input type="text" name="nisn" x-model="form.nisn" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">NIK</label>
                                <input type="text" name="nik" x-model="form.nik" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" x-model="form.jenis_kelamin" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">Pilih</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" x-model="form.tempat_lahir" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" x-model="form.tanggal_lahir" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Agama</label>
                                <input type="text" name="agama" x-model="form.agama" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold text-gray-400">Rombel Saat Ini (Tidak dapat diubah)</label>
                                <input type="text" value="{{ $siswa->dapodik->rombel_saat_ini ?? '-' }}" readonly class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 sm:text-sm cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Sekolah Asal</label>
                                <input type="text" name="sekolah_asal" x-model="form.sekolah_asal" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="mt-4 p-4 rounded-lg border transition-all" :class="changed(['nama_lengkap', 'nipd', 'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'sekolah_asal']) ? 'bg-red-50 border-red-100' : 'bg-blue-50 border-blue-100'">
                            <label class="block text-sm font-bold" :class="changed(['nama_lengkap', 'nipd', 'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'sekolah_asal']) ? 'text-red-700' : 'text-blue-700'">
                                Lampiran : Ijazah SMP/MTs/Sederajat (PDF/Gambar)
                                <template x-if="changed(['nama_lengkap', 'nipd', 'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'sekolah_asal'])">
                                    <span class="ml-1 text-xs px-2 py-0.5 bg-red-600 text-white rounded-full uppercase">Wajib</span>
                                </template>
                            </label>
                            <input type="file" name="doc_ijazah" :required="changed(['nama_lengkap', 'nipd', 'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'sekolah_asal'])" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                            <p class="mt-1 text-xs italic" :class="changed(['nama_lengkap', 'nipd', 'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'sekolah_asal']) ? 'text-red-600' : 'text-blue-600'">*Lengkapi lampiran ijazah jenjang sebelumnya (SMP/MTs/Sederajat) apabila terdapat perubahan data di atas.</p>
                        </div>
                    </div>
                </div>

                {{-- Alamat & Kontak --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6 border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Alamat & Kontak
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="md:col-span-2 lg:col-span-3">
                                <label class="block text-xs text-gray-500 uppercase font-bold">Alamat</label>
                                <textarea name="alamat" rows="2" x-model="form.alamat" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">RT/RW</label>
                                <div class="flex gap-2">
                                    <input type="text" name="rt" placeholder="RT" x-model="form.rt" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <input type="text" name="rw" placeholder="RW" x-model="form.rw" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Dusun</label>
                                <input type="text" name="dusun" x-model="form.dusun" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Kelurahan</label>
                                <input type="text" name="kelurahan" x-model="form.kelurahan" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Kecamatan</label>
                                <input type="text" name="kecamatan" x-model="form.kecamatan" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Kode Pos</label>
                                <input type="text" name="kode_pos" x-model="form.kode_pos" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Jenis Tinggal</label>
                                <input type="text" name="jenis_tinggal" x-model="form.jenis_tinggal" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Alat Transportasi</label>
                                <input type="text" name="alat_transportasi" x-model="form.alat_transportasi" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Telepon</label>
                                <input type="text" name="telepon" x-model="form.telepon" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">HP</label>
                                <input type="text" name="hp" x-model="form.hp" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Email</label>
                                <input type="email" name="email" x-model="form.email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Orang Tua & Wali --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    {{-- Ayah --}}
                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
                        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center">Data Ayah</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase font-bold">Nama Ayah</label>
                                <input type="text" name="nama_ayah" x-model="form.nama_ayah" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase font-bold">NIK & Tahun Lahir</label>
                                <div class="flex gap-2">
                                    <input type="text" name="nik_ayah" placeholder="NIK" x-model="form.nik_ayah" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <input type="text" name="tahun_lahir_ayah" placeholder="Tahun" x-model="form.tahun_lahir_ayah" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase font-bold">Pekerjaan & Penghasilan</label>
                                <input type="text" name="pekerjaan_ayah" placeholder="Pekerjaan" x-model="form.pekerjaan_ayah" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <input type="text" name="penghasilan_ayah" placeholder="Penghasilan" x-model="form.penghasilan_ayah" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    {{-- Ibu --}}
                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
                        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center">Data Ibu</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase font-bold">Nama Ibu</label>
                                <input type="text" name="nama_ibu" x-model="form.nama_ibu" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase font-bold">NIK & Tahun Lahir</label>
                                <div class="flex gap-2">
                                    <input type="text" name="nik_ibu" placeholder="NIK" x-model="form.nik_ibu" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <input type="text" name="tahun_lahir_ibu" placeholder="Tahun" x-model="form.tahun_lahir_ibu" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase font-bold">Pekerjaan & Penghasilan</label>
                                <input type="text" name="pekerjaan_ibu" placeholder="Pekerjaan" x-model="form.pekerjaan_ibu" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <input type="text" name="penghasilan_ibu" placeholder="Penghasilan" x-model="form.penghasilan_ibu" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    {{-- Wali --}}
                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
                        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center">Data Wali</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase font-bold">Nama Wali</label>
                                <input type="text" name="nama_wali" x-model="form.nama_wali" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase font-bold">NIK & Tahun Lahir</label>
                                <div class="flex gap-2">
                                    <input type="text" name="nik_wali" placeholder="NIK" x-model="form.nik_wali" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <input type="text" name="tahun_lahir_wali" placeholder="Tahun" x-model="form.tahun_lahir_wali" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase font-bold">Pekerjaan & Penghasilan</label>
                                <input type="text" name="pekerjaan_wali" placeholder="Pekerjaan" x-model="form.pekerjaan_wali" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <input type="text" name="penghasilan_wali" placeholder="Penghasilan" x-model="form.penghasilan_wali" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lampiran KK --}}
                <div class="overflow-hidden shadow-sm rounded-xl mb-6 border p-6 transition-all" :class="changed(['alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos', 'nama_ayah', 'nama_ibu', 'nama_wali', 'no_kk', 'anak_ke_berapa', 'jumlah_saudara_kandung']) ? 'bg-red-50 border-red-100' : 'bg-indigo-50 border-indigo-100'">
                    <label class="block text-sm font-bold" :class="changed(['alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos', 'nama_ayah', 'nama_ibu', 'nama_wali', 'no_kk', 'anak_ke_berapa', 'jumlah_saudara_kandung']) ? 'text-red-700' : 'text-indigo-700'">
                        Lampiran : Kartu Keluarga (KK) (PDF/Gambar)
                        <template x-if="changed(['alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos', 'nama_ayah', 'nama_ibu', 'nama_wali', 'no_kk', 'anak_ke_berapa', 'jumlah_saudara_kandung'])">
                            <span class="ml-1 text-xs px-2 py-0.5 bg-red-600 text-white rounded-full uppercase">Wajib</span>
                        </template>
                    </label>
                    <input type="file" name="doc_kk" :required="changed(['alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos', 'nama_ayah', 'nama_ibu', 'nama_wali', 'no_kk', 'anak_ke_berapa', 'jumlah_saudara_kandung'])" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                    <p class="mt-1 text-xs italic" :class="changed(['alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos', 'nama_ayah', 'nama_ibu', 'nama_wali', 'no_kk', 'anak_ke_berapa', 'jumlah_saudara_kandung']) ? 'text-red-600' : 'text-indigo-600'">*Lengkapi lampiran kartu keluarga apabila terdapat perubahan pada data keluarga, alamat, anak/saudara, atau No. KK.</p>
                </div>

                {{-- Dokumen & Bantuan --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6 border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Dokumen & Bantuan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">No. Seri Ijazah</label>
                                <input type="text" name="no_seri_ijazah" x-model="form.no_seri_ijazah" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <div class="mt-2 p-2 bg-gray-50 rounded border text-[10px] text-gray-500">
                                    <span class="font-bold flex items-center" :class="changed(['no_seri_ijazah']) ? 'text-red-600 font-black' : ''">
                                        Lampiran: Ijazah SMP/MTs / Sederajat
                                        <template x-if="changed(['no_seri_ijazah'])">
                                            <span class="ml-1 text-[8px] bg-red-600 text-white rounded px-1 uppercase">Wajib</span>
                                        </template>
                                    </span>
                                    <input type="file" name="doc_ijazah_extra" :required="changed(['no_seri_ijazah'])" class="mt-1 block w-full">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">No. Registrasi Akta Lahir</label>
                                <input type="text" name="no_registrasi_akta_lahir" x-model="form.no_registrasi_akta_lahir" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <div class="mt-2 p-2 bg-gray-50 rounded border text-[10px] text-gray-500">
                                    <span class="font-bold flex items-center" :class="changed(['no_registrasi_akta_lahir']) ? 'text-red-600 font-black' : ''">
                                        Lampiran: Akta Kelahiran
                                        <template x-if="changed(['no_registrasi_akta_lahir'])">
                                            <span class="ml-1 text-[8px] bg-red-600 text-white rounded px-1 uppercase">Wajib</span>
                                        </template>
                                    </span>
                                    <input type="file" name="doc_akta" :required="changed(['no_registrasi_akta_lahir'])" class="mt-1 block w-full">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">No. KK</label>
                                <input type="text" name="no_kk" x-model="form.no_kk" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <div class="mt-2 p-2 bg-gray-50 rounded border text-[10px] text-gray-500">
                                    <span class="font-bold flex items-center" :class="changed(['no_kk']) ? 'text-red-600 font-black' : ''">
                                        Lampiran: Kartu Keluarga
                                        <template x-if="changed(['no_kk'])">
                                            <span class="ml-1 text-[8px] bg-red-600 text-white rounded px-1 uppercase">Wajib</span>
                                        </template>
                                    </span>
                                    <input type="file" name="doc_kk_extra" :required="changed(['no_kk'])" class="mt-1 block w-full">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Penerima KPS</label>
                                <input type="text" name="penerima_kps" x-model="form.penerima_kps" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <label class="block text-[10px] font-bold mt-1">No. KPS</label>
                                <input type="text" name="no_kps" x-model="form.no_kps" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <div class="mt-2 p-2 bg-gray-50 rounded border text-[10px] text-gray-500">
                                    <span class="font-bold flex items-center" :class="changed(['penerima_kps', 'no_kps']) ? 'text-red-600 font-black' : ''">
                                        Lampiran: Kartu KPS
                                        <template x-if="changed(['penerima_kps', 'no_kps'])">
                                            <span class="ml-1 text-[8px] bg-red-600 text-white rounded px-1 uppercase">Wajib</span>
                                        </template>
                                    </span>
                                    <input type="file" name="doc_kps" :required="changed(['penerima_kps', 'no_kps'])" class="mt-1 block w-full">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Penerima KIP</label>
                                <input type="text" name="penerima_kip" x-model="form.penerima_kip" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <label class="block text-[10px] font-bold mt-1">Nomor KIP & Nama</label>
                                <input type="text" name="nomor_kip" x-model="form.nomor_kip" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="No KIP">
                                <input type="text" name="nama_di_kip" x-model="form.nama_di_kip" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm mt-1" placeholder="Nama di KIP">
                                <div class="mt-2 p-2 bg-gray-50 rounded border text-[10px] text-gray-500">
                                    <span class="font-bold flex items-center" :class="changed(['penerima_kip', 'nomor_kip', 'nama_di_kip']) ? 'text-red-600 font-black' : ''">
                                        Lampiran: Kartu KIP
                                        <template x-if="changed(['penerima_kip', 'nomor_kip', 'nama_di_kip'])">
                                            <span class="ml-1 text-[8px] bg-red-600 text-white rounded px-1 uppercase">Wajib</span>
                                        </template>
                                    </span>
                                    <input type="file" name="doc_kip" :required="changed(['penerima_kip', 'nomor_kip', 'nama_di_kip'])" class="mt-1 block w-full">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Nomor KKS</label>
                                <input type="text" name="nomor_kks" x-model="form.nomor_kks" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <div class="mt-2 p-2 bg-gray-50 rounded border text-[10px] text-gray-500">
                                    <span class="font-bold flex items-center" :class="changed(['nomor_kks']) ? 'text-red-600 font-black' : ''">
                                        Lampiran: Kartu KKS
                                        <template x-if="changed(['nomor_kks'])">
                                            <span class="ml-1 text-[8px] bg-red-600 text-white rounded px-1 uppercase">Wajib</span>
                                        </template>
                                    </span>
                                    <input type="file" name="doc_kks" :required="changed(['nomor_kks'])" class="mt-1 block w-full">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold text-gray-400">Layak PIP (Info Saja)</label>
                                <input type="text" name="layak_pip" x-model="form.layak_pip" readonly class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 sm:text-sm cursor-not-allowed">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs text-gray-500 uppercase font-bold text-gray-400">Alasan Layak PIP (Info Saja)</label>
                                <input type="text" name="alasan_layak_pip" x-model="form.alasan_layak_pip" readonly class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 sm:text-sm cursor-not-allowed">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Lainnya --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6 border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Data Lainnya & Tabungan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Bank</label>
                                <input type="text" name="bank" x-model="form.bank" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">No. Rekening</label>
                                <input type="text" name="nomor_rekening_bank" x-model="form.nomor_rekening_bank" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Atas Nama</label>
                                <input type="text" name="rekening_atas_nama" x-model="form.rekening_atas_nama" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <div class="p-2 bg-purple-50 rounded border text-[10px] text-purple-700">
                                    <span class="font-bold flex items-center" :class="changed(['bank', 'nomor_rekening_bank', 'rekening_atas_nama']) ? 'text-red-600' : ''">
                                        Lampiran: Rekening Bank
                                        <template x-if="changed(['bank', 'nomor_rekening_bank', 'rekening_atas_nama'])">
                                            <span class="ml-1 text-[8px] bg-red-600 text-white rounded px-1 uppercase">Wajib</span>
                                        </template>
                                    </span>
                                    <input type="file" name="doc_rekening" :required="changed(['bank', 'nomor_rekening_bank', 'rekening_atas_nama'])" class="mt-1 block w-full">
                                    <p class="mt-1 italic text-[8px] text-purple-500">*Wajib melampirkan foto buku tabungan apabila mengubah data bank.</p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Anak ke-</label>
                                <input type="number" name="anak_ke_berapa" x-model="form.anak_ke_berapa" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Jumlah Saudara</label>
                                <input type="number" name="jumlah_saudara_kandung" x-model="form.jumlah_saudara_kandung" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Berat Badan (kg)</label>
                                <input type="number" step="0.1" name="berat_badan" value="{{ $siswa->dapodik->berat_badan ?? '' }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Tinggi Badan (cm)</label>
                                <input type="number" step="0.1" name="tinggi_badan" value="{{ $siswa->dapodik->tinggi_badan ?? '' }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Lingkar Kepala (cm)</label>
                                <input type="number" step="0.1" name="lingkar_kepala" value="{{ $siswa->dapodik->lingkar_kepala ?? '' }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase font-bold">Jarak ke Sekolah (km)</label>
                                <input type="number" step="0.1" name="jarak_rumah_ke_sekolah" value="{{ $siswa->dapodik->jarak_rumah_ke_sekolah ?? '' }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sticky bottom-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-8 py-4 bg-blue-600 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:bg-blue-700 transition shadow-2xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Kirim Pengajuan Perubahan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
