<div x-show="isFormOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">

    {{-- Backdrop Overlay: Sengaja TIDAK ada @click agar HANYA bisa ditutup via tombol exit (X) atau tutup --}}
    <div x-show="isFormOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        {{-- Modal Dialog Panel: Sengaja TIDAK ada @click.outside --}}
        <div x-show="isFormOpen" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100">

            {{-- Header Modal --}}
            <div class="bg-gradient-to-r from-indigo-50 via-slate-50 to-purple-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 leading-tight"
                            x-text="isEdit ? 'Edit Pengajuan Izin' : 'Ajukan Izin Baru'"></h3>
                        <p class="text-xs text-gray-500 font-medium">Lengkapi formulir perizinan siswa dengan benar</p>
                    </div>
                </div>
                {{-- Tombol Exit (X) --}}
                <button type="button" @click="closeFormModal" 
                    class="rounded-xl p-2 text-gray-400 hover:text-gray-600 hover:bg-white transition-all focus:outline-none cursor-pointer"
                    title="Tutup Modal (X)">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form :action="formAction" method="POST" enctype="multipart/form-data">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">

                    {{-- 1. INFORMASI DETAIL SISWA (READONLY) --}}
                    <div class="bg-gradient-to-br from-slate-50 to-gray-50 rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                        <div class="flex items-center gap-2 mb-3 pb-2 border-b border-slate-200/60">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-xs font-black uppercase tracking-wider text-slate-700">Data Pemohon (Siswa)</span>
                            <span class="ml-auto text-[10px] font-bold px-2 py-0.5 bg-slate-200 text-slate-600 rounded-full">Readonly</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-left">
                            <div class="space-y-0.5">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Siswa</span>
                                <span class="block text-xs sm:text-sm font-black text-slate-800 truncate" title="{{ $siswa->nama_lengkap ?? Auth::user()->name }}">
                                    {{ $siswa->nama_lengkap ?? Auth::user()->name }}
                                </span>
                            </div>
                            <div class="space-y-0.5">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIS</span>
                                <span class="block text-xs sm:text-sm font-mono font-bold text-slate-800">
                                    {{ $siswa->nis ?? '-' }}
                                </span>
                            </div>
                            <div class="space-y-0.5">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas</span>
                                <span class="block text-xs sm:text-sm font-black text-indigo-600">
                                    {{ $rombel?->kelas?->nama_kelas ?? '-' }}
                                </span>
                            </div>
                            <div class="space-y-0.5">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Wali Kelas</span>
                                <span class="block text-xs sm:text-sm font-bold text-slate-800 truncate" title="{{ $waliKelas?->name ?? '-' }}">
                                    {{ $waliKelas?->name ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. FIELD TANGGAL IZIN --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">
                            Tanggal Izin <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_izin" x-model="form.tanggal_izin" required
                            class="block w-full rounded-xl border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium">
                        <p class="mt-1 text-xs text-gray-500">Pilih tanggal mulai izin atau ketidakhadiran Anda.</p>
                    </div>

                    {{-- 3. FIELD KATEGORI PERIZINAN (SELECT) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">
                            Kategori Perizinan <span class="text-red-500">*</span>
                        </label>
                        <select name="kategori_izin" x-model="form.kategori_izin" required
                            class="block w-full rounded-xl border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-sm font-semibold">
                            <option value="">-- Pilih Kategori Perizinan --</option>
                            <option value="sakit">🏥 Sakit</option>
                            <option value="acara_keluarga">👨‍👩‍👧‍👦 Izin - Acara / Urusan Keluarga Inti</option>
                            <option value="duka_cita">🕊️ Izin - Takziah / Duka Cita Keluarga</option>
                            <option value="kepentingan_mendesak">⚠️ Izin - Kepentingan Pribadi Mendesak</option>
                            <option value="lainnya">📝 Izin - Keperluan Lainnya</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Pilih alasan utama pengajuan izin yang sesuai dengan keadaan Anda.</p>
                    </div>

                    {{-- 4. KONDISI KHUSUS: KATEGORI SAKIT --}}
                    <div x-show="form.kategori_izin === 'sakit'" x-transition class="space-y-4 pt-2">
                        
                        {{-- Radio Button Pilihan Durasi / Kondisi Sakit (3 Opsi) --}}
                        <div class="bg-orange-50/60 border border-orange-200/80 rounded-2xl p-4">
                            <label class="block text-xs font-black text-orange-900 uppercase tracking-wider mb-2">
                                Durasi / Kondisi Izin Sakit <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                {{-- Opsi 1: 1 Hari --}}
                                <label class="flex items-center gap-3 p-3 bg-white rounded-xl border cursor-pointer transition-all"
                                    :class="form.durasi_sakit === '1_hari' ? 'border-orange-500 ring-2 ring-orange-200 shadow-xs' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" name="durasi_sakit" value="1_hari" x-model="form.durasi_sakit" class="text-orange-600 focus:ring-orange-500">
                                    <div>
                                        <span class="block text-sm font-bold text-gray-800">1 Hari</span>
                                        <span class="block text-[11px] text-gray-500">Foto obat / resep faskes</span>
                                    </div>
                                </label>

                                {{-- Opsi 2: Lebih dari 1 Hari --}}
                                <label class="flex items-center gap-3 p-3 bg-white rounded-xl border cursor-pointer transition-all"
                                    :class="form.durasi_sakit === 'lebih_dari_1_hari' ? 'border-orange-500 ring-2 ring-orange-200 shadow-xs' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" name="durasi_sakit" value="lebih_dari_1_hari" x-model="form.durasi_sakit" class="text-orange-600 focus:ring-orange-500">
                                    <div>
                                        <span class="block text-sm font-bold text-gray-800">Lebih dari 1 Hari</span>
                                        <span class="block text-[11px] text-gray-500">Maks. 3 Hari (Surat Faskes)</span>
                                    </div>
                                </label>

                                {{-- Opsi 3: Sakit Berat / Rawat Inap --}}
                                <label class="flex items-center gap-3 p-3 bg-white rounded-xl border cursor-pointer transition-all"
                                    :class="form.durasi_sakit === 'rawat_inap' ? 'border-red-500 ring-2 ring-red-200 shadow-xs bg-red-50/20' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" name="durasi_sakit" value="rawat_inap" x-model="form.durasi_sakit" class="text-red-600 focus:ring-red-500">
                                    <div>
                                        <span class="block text-sm font-bold text-gray-800">Sakit Berat</span>
                                        <span class="block text-[11px] text-gray-500">Rawat Inap / Opname RS</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Kotak Ketentuan & Aturan Izin Sakit: Disesuaikan Berdasarkan Pilihan --}}
                        
                        {{-- Info untuk 1 Hari --}}
                        <div x-show="form.durasi_sakit === '1_hari'" x-transition class="bg-amber-50 border border-amber-200/80 rounded-2xl p-4 text-xs text-amber-900 space-y-1.5">
                            <div class="flex items-center gap-1.5 font-bold text-amber-800">
                                <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Ketentuan Izin Sakit (1 Hari):</span>
                            </div>
                            <ul class="list-disc list-inside space-y-1 pl-1 text-[11px] leading-relaxed text-amber-800/90 font-medium">
                                <li>Izin sakit 1 hari diperuntukkan bagi sakit ringan yang memerlukan istirahat mandiri di rumah.</li>
                                <li>Siswa <b>wajib mengunggah bukti foto obat</b> (kemasan/resep bertuliskan nama siswa) atau surat keterangan dokter/kesehatan setempat.</li>
                                <li>File unggahan khusus berupa <b>file foto/gambar</b> (JPG, PNG).</li>
                            </ul>
                        </div>

                        {{-- Info untuk Lebih dari 1 Hari --}}
                        <div x-show="form.durasi_sakit === 'lebih_dari_1_hari'" x-transition class="bg-amber-50 border border-amber-200/80 rounded-2xl p-4 text-xs text-amber-900 space-y-1.5">
                            <div class="flex items-center gap-1.5 font-bold text-amber-800">
                                <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Ketentuan Izin Sakit (Lebih dari 1 Hari):</span>
                            </div>
                            <ul class="list-disc list-inside space-y-1 pl-1 text-[11px] leading-relaxed text-amber-800/90 font-medium">
                                <li>Izin sakit mandiri maksimal berlaku selama <b>3 hari</b>.</li>
                                <li>Apabila dalam 3 hari siswa belum sembuh, maka siswa <b>wajib mengajukan izin perpanjangan kembali</b>.</li>
                                <li>Siswa <b>wajib mengunggah surat keterangan sakit resmi</b> yang dikeluarkan oleh lembaga kesehatan (Puskesmas / Rumah Sakit / Klinik Dokter) dalam bentuk foto atau file PDF.</li>
                            </ul>
                        </div>

                        {{-- Info untuk Sakit Berat / Rawat Inap --}}
                        <div x-show="form.durasi_sakit === 'rawat_inap'" x-transition class="bg-rose-50 border border-rose-200/80 rounded-2xl p-4 text-xs text-rose-950 space-y-1.5">
                            <div class="flex items-center gap-1.5 font-bold text-rose-800">
                                <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Ketentuan Khusus Sakit Berat / Rawat Inap (Opname):</span>
                            </div>
                            <ul class="list-disc list-inside space-y-1 pl-1 text-[11px] leading-relaxed text-rose-900/90 font-medium">
                                <li>Khusus bagi siswa yang sedang menjalani rawat inap (opname) di rumah sakit atau kondisi sakit berat.</li>
                                <li>Cukup mengunggah <b>Surat Keterangan Rawat Inap / Surat Keterangan Sakit resmi dari Rumah Sakit tempat dirawat</b> (Foto / PDF).</li>
                                <li><span class="font-bold underline">Penting:</span> Opsi ini <b>memerlukan komunikasi langsung</b> antara pihak orang tua/wali murid dengan <b>Wali Kelas</b> agar pihak sekolah dapat memantau kondisi siswa dengan baik.</li>
                            </ul>
                        </div>

                        {{-- Area Upload Khusus Sakit: 1 Hari --}}
                        <div x-show="form.durasi_sakit === '1_hari'" x-transition>
                            <label class="block text-sm font-bold text-gray-800 mb-1">
                                Bukti Foto Obat / Surat Dokter Setempat <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-2">Unggah bukti foto kemasan obat resep atas nama siswa atau surat keterangan pemeriksaan dari faskes setempat (Format: JPG, JPEG, PNG, Maks. 5MB).</p>
                        </div>

                        {{-- Area Upload Khusus Sakit: Lebih dari 1 Hari --}}
                        <div x-show="form.durasi_sakit === 'lebih_dari_1_hari'" x-transition>
                            <label class="block text-sm font-bold text-gray-800 mb-1">
                                Surat Keterangan Sakit dari Lembaga Kesehatan (Puskesmas / RS / Klinik) <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-2">Wajib mengunggah surat keterangan istirahat/sakit resmi dari lembaga kesehatan berwenang (Format: JPG, PNG, atau PDF, Maks. 5MB).</p>
                        </div>

                        {{-- Area Upload Khusus Sakit: Rawat Inap --}}
                        <div x-show="form.durasi_sakit === 'rawat_inap'" x-transition>
                            <label class="block text-sm font-bold text-gray-800 mb-1">
                                Surat Keterangan Rawat Inap / Sakit dari Rumah Sakit <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-2">Wajib mengunggah surat keterangan rawat inap resmi yang diterbitkan oleh rumah sakit tempat siswa dirawat (Format: JPG, PNG, atau PDF, Maks. 5MB).</p>
                        </div>
                    </div>

                    {{-- 5. KONDISI KATEGORI SELAIN SAKIT --}}
                    <div x-show="form.kategori_izin && form.kategori_izin !== 'sakit'" x-transition class="space-y-2">
                        <div class="bg-blue-50 border border-blue-200/70 rounded-2xl p-3.5 flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-xs text-blue-800 leading-relaxed font-medium">
                                <span class="font-bold">Informasi:</span> Izin untuk kategori ini <b>otomatis berlaku untuk 1 hari</b>. Dokumen pendukung bersifat <b>opsional</b> (boleh dilampirkan jika ada surat undangan/surat tugas), foto kegiatan atau foto kejadian.
                            </div>
                        </div>
                        <label class="block text-sm font-bold text-gray-800 pt-1">
                            Dokumen Pendukung <span class="text-xs font-normal text-gray-400">(Opsional)</span>
                        </label>
                        <p class="text-xs text-gray-500">Lampirkan foto surat undangan, surat keterangan, atau surat tugas, foto kegiatan atau foto kejadian jika ada (Format: JPG, PNG, PDF, Maks. 5MB).</p>
                    </div>

                    {{-- 6. INTERACTIVE UPLOAD BOX WITH PREVIEW --}}
                    <div x-show="form.kategori_izin" x-transition>
                        {{-- Dropzone / Upload Area (Tampil jika belum pilih file) --}}
                        <div x-show="!filePreview && !fileName" class="relative">
                            <label for="dokumen_pendukung" 
                                class="flex flex-col items-center justify-center w-full h-36 px-4 transition bg-white border-2 border-dashed rounded-2xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/20"
                                :class="form.kategori_izin === 'sakit' ? 'border-orange-300' : 'border-gray-300'">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                                    <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mb-1 text-xs sm:text-sm font-bold text-gray-700">
                                        <span class="text-indigo-600 underline">Klik untuk memilih file</span> atau seret file ke sini
                                    </p>
                                    <p class="text-[11px] text-gray-400">
                                        <span x-show="form.kategori_izin === 'sakit' && form.durasi_sakit === '1_hari'">Format: JPG, JPEG, PNG (Maks 5MB)</span>
                                        <span x-show="form.kategori_izin !== 'sakit' || form.durasi_sakit !== '1_hari'">Format: JPG, PNG, atau PDF (Maks 5MB)</span>
                                    </p>
                                </div>
                                <input type="file" name="dokumen_pendukung" id="dokumen_pendukung"
                                    @change="handleFileUpload($event)"
                                    :accept="form.kategori_izin === 'sakit' && form.durasi_sakit === '1_hari' ? 'image/*' : 'image/*,application/pdf'"
                                    :required="!isEdit && form.kategori_izin === 'sakit'"
                                    class="hidden">
                            </label>
                        </div>

                        {{-- Live Preview Box: Jika Gambar Terpilih --}}
                        <div x-show="filePreview" x-transition class="p-3 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <img :src="filePreview" alt="Pratinjau Dokumen" class="w-16 h-16 object-cover rounded-xl border border-gray-200 shadow-xs flex-shrink-0">
                                <div class="overflow-hidden">
                                    <p class="text-xs font-bold text-gray-800 truncate" x-text="fileName"></p>
                                    <p class="text-[10px] text-green-600 font-bold flex items-center gap-1 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Foto berhasil dipilih
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="removeFile()" 
                                class="px-3 py-1.5 text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition-all flex-shrink-0 cursor-pointer">
                                Ganti File
                            </button>
                        </div>

                        {{-- Preview Box: Jika File PDF Terpilih --}}
                        <div x-show="isPdf && !filePreview" x-transition class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-xl flex items-center justify-center font-black text-xs flex-shrink-0">
                                    PDF
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-xs font-bold text-gray-800 truncate" x-text="fileName"></p>
                                    <p class="text-[10px] text-green-600 font-bold flex items-center gap-1 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Dokumen PDF siap diunggah
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="removeFile()" 
                                class="px-3 py-1.5 text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition-all flex-shrink-0 cursor-pointer">
                                Ganti File
                            </button>
                        </div>
                    </div>

                    {{-- 7. CATATAN / KETERANGAN TAMBAHAN --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">
                            Catatan / Keterangan Tambahan <span class="text-xs font-normal text-gray-400">(Opsional)</span>
                        </label>
                        <textarea name="catatan" rows="3" x-model="form.catatan"
                            class="block w-full rounded-xl border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            placeholder="Contoh: Mengalami demam dan pusing sejak kemarin malam, atau keperluan menghadiri pernikahan kerabat di luar kota..."></textarea>
                        <p class="mt-1 text-xs text-gray-500">Tuliskan keterangan pendukung atau gejala sakit secara sopan dan jelas.</p>
                    </div>

                </div>

                {{-- Footer Modal --}}
                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100 gap-3">
                    <button type="submit"
                        class="inline-flex w-full justify-center items-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-indigo-100 hover:bg-indigo-700 sm:w-auto transition-all cursor-pointer">
                        <span x-text="isEdit ? 'Simpan Perubahan' : 'Kirim Pengajuan Izin'"></span>
                    </button>
                    {{-- Tombol Tutup / Batal --}}
                    <button type="button" @click="closeFormModal"
                        class="mt-3 inline-flex w-full justify-center items-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-gray-700 shadow-xs ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-all cursor-pointer">
                        Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
