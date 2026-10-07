<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Pengajuan Izin Guru</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="permitForm()" x-init="if (startDate) fetchSchedules()">
        <div class="w-full px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <form x-ref="form" action="{{ route('guru.izin.store') }}" method="POST" enctype="multipart/form-data" @submit.prevent="validateAndSubmit()">
                    @csrf
                    <input type="hidden" name="work_schedule_validation_enabled" value="1">
                    @if(session('schedule_warnings'))
                        <input type="hidden" name="confirm_work_schedule_warning" value="{{ session('schedule_warning_token') }}">
                    @endif
                    <div class="p-8 space-y-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-1">Informasi Izin</h3>
                            <p class="text-sm text-gray-500">Lengkapi detail alasan dan waktu izin Anda.</p>
                        </div>

                        @if(session('error'))
                            <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3 text-red-700 animate-pulse">
                                <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-black">Gagal Mengajukan Izin</p>
                                    <p class="text-xs font-medium opacity-80">{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif

                        @if(session('schedule_warnings'))
                            <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900">
                                <p class="text-sm font-black">Peringatan Waktu Izin</p>
                                <p class="mt-1 text-xs">Rentang yang dipilih berada di luar kewajiban kerja atau pada hari libur. Periksa kembali, lalu tekan Kirim Pengajuan jika memang benar.</p>
                                <ul class="mt-2 list-disc pl-5 text-xs font-medium space-y-1">
                                    @foreach(session('schedule_warnings') as $warning)
                                        <li>{{ $warning }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Jenis Izin</label>
                                <select name="jenis_izin" required x-model="jenisIzin" @change="handleJenisIzinChange()"
                                    class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih Jenis</option>
                                    <option value="Sakit" {{ old('jenis_izin') === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="Dinas" {{ old('jenis_izin') === 'Dinas' ? 'selected' : '' }}>Dinas Out</option>
                                    <option value="Keperluan Pribadi" {{ old('jenis_izin') === 'Keperluan Pribadi' ? 'selected' : '' }}>Keperluan Pribadi</option>
                                    <option value="Lainnya" {{ old('jenis_izin') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('jenis_izin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Kategori Penyetujuan</label>
                                <select name="kategori_penyetujuan" required
                                    class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="tidak_masuk" {{ old('kategori_penyetujuan', 'tidak_masuk') === 'tidak_masuk' ? 'selected' : '' }}>Izin Tidak Masuk</option>
                                    <option value="luar" {{ old('kategori_penyetujuan') === 'luar' ? 'selected' : '' }}>Luar Sekolah</option>
                                    <option value="sekolah" {{ old('kategori_penyetujuan') === 'sekolah' ? 'selected' : '' }}>Lingkungan Sekolah (Rapat, dsb)</option>
                                    <option value="terlambat" {{ old('kategori_penyetujuan') === 'terlambat' ? 'selected' : '' }}>Terlambat (Datang Terlambat)</option>
                                </select>
                                <p class="text-[10px] text-gray-500 mt-2">
                                    * <strong>Izin Tidak Masuk</strong>: Langsung ke KAUR SDM (dan Kepsek bagi Pegawai Tetap).<br>
                                    * <strong>Sekolah</strong>: Hanya butuh persetujuan Piket.<br>
                                    * <strong>Luar Sekolah</strong>: Piket → Kurikulum → SDM.
                                </p>
                            </div>

                            {{-- Pilihan Kondisi Sakit Khusus Izin Sakit --}}
                            <div x-show="jenisIzin === 'Sakit'" x-transition class="md:col-span-2 space-y-3 p-5 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">Kategori Kondisi Sakit</h4>
                                        <p class="text-xs text-gray-500">Pilih kategori sakit sesuai kondisi riil dan dokumen medis Anda.</p>
                                    </div>
                                    <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider rounded-full bg-indigo-100 text-indigo-700">Regulasi Medis</span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition-all hover:bg-white"
                                        :class="tipeSakit === 'ringan' ? 'bg-white border-indigo-600 ring-2 ring-indigo-500/20 shadow-sm' : 'border-gray-200 bg-white/60'">
                                        <div class="flex items-center gap-2 mb-1">
                                            <input type="radio" name="tipe_sakit" value="ringan" x-model="tipeSakit" @change="handleTipeSakitChange()"
                                                class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-xs font-bold text-gray-900">Sakit Ringan</span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 pl-6 leading-relaxed">
                                            Maks. <strong>1 Hari</strong> tanpa surat dokter (flu, demam, pusing).
                                        </p>
                                        <span class="mt-2 inline-block text-[10px] text-emerald-600 font-bold pl-6">Surat dokter opsional</span>
                                    </label>

                                    <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition-all hover:bg-white"
                                        :class="tipeSakit === 'surat_dokter' ? 'bg-white border-indigo-600 ring-2 ring-indigo-500/20 shadow-sm' : 'border-gray-200 bg-white/60'">
                                        <div class="flex items-center gap-2 mb-1">
                                            <input type="radio" name="tipe_sakit" value="surat_dokter" x-model="tipeSakit" @change="handleTipeSakitChange()"
                                                class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-xs font-bold text-gray-900">Surat Keterangan Dokter</span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 pl-6 leading-relaxed">
                                            Otomatis <strong>3 Hari</strong>. Wajib upload surat keterangan resmi faskes/klinik.
                                        </p>
                                        <span class="mt-2 inline-block text-[10px] text-amber-600 font-bold pl-6">Wajib Surat Keterangan</span>
                                    </label>

                                    <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition-all hover:bg-white"
                                        :class="tipeSakit === 'rawat_inap' ? 'bg-white border-indigo-600 ring-2 ring-indigo-500/20 shadow-sm' : 'border-gray-200 bg-white/60'">
                                        <div class="flex items-center gap-2 mb-1">
                                            <input type="radio" name="tipe_sakit" value="rawat_inap" x-model="tipeSakit" @change="handleTipeSakitChange()"
                                                class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-xs font-bold text-gray-900">Rawat Inap RS</span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 pl-6 leading-relaxed">
                                            Durasi <strong>fleksibel (terbuka)</strong> selama masa perawatan di RS.
                                        </p>
                                        <span class="mt-2 inline-block text-[10px] text-purple-600 font-bold pl-6">Wajib Surat RS</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Tanggal & Waktu Mulai</label>
                                <input type="datetime-local" name="tanggal_mulai" required x-model="startDate" @change="handleStartDateChange()"
                                    class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('tanggal_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-bold text-gray-700">Tanggal & Waktu Selesai</label>
                                    <template x-if="jenisIzin === 'Sakit' && tipeSakit === 'surat_dokter'">
                                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                            🔒 Terkunci 3 Hari (Regulasi)
                                        </span>
                                    </template>
                                    <template x-if="jenisIzin === 'Sakit' && tipeSakit === 'ringan'">
                                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                                            Maksimal 1 Hari
                                        </span>
                                    </template>
                                </div>

                                {{-- Jika Rawat Inap RS: Durasi fleksibel, tidak ada input waktu selesai --}}
                                <div x-show="jenisIzin === 'Sakit' && tipeSakit === 'rawat_inap'" class="rounded-xl border border-purple-200 bg-purple-50/70 p-3 text-purple-900">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm">🏥</span>
                                        <span class="text-xs font-bold text-purple-800">Masa Perawatan RS (Fleksibel)</span>
                                    </div>
                                    <p class="text-[11px] text-purple-700 mt-1 leading-relaxed">
                                        Tanggal selesai tidak dibatasi karena menyesuaikan kondisi pemulihan atau rawat inap di Rumah Sakit.
                                    </p>
                                </div>

                                {{-- Jika bukan Rawat Inap RS: Tampilkan input tanggal selesai --}}
                                <div x-show="!(jenisIzin === 'Sakit' && tipeSakit === 'rawat_inap')">
                                    <input type="datetime-local" name="tanggal_selesai" x-model="endDate" @change="fetchSchedules()"
                                        :required="!(jenisIzin === 'Sakit' && tipeSakit === 'rawat_inap')"
                                        :readonly="jenisIzin === 'Sakit' && tipeSakit === 'surat_dokter'"
                                        :class="jenisIzin === 'Sakit' && tipeSakit === 'surat_dokter' ? 'bg-gray-100 cursor-not-allowed text-gray-600' : ''"
                                        class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('tanggal_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Upload Eviden --}}
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 flex items-center gap-2">
                                    <span x-text="jenisIzin === 'Sakit' ? (tipeSakit === 'ringan' ? 'Eviden Foto Obat / Keterangan Mandiri (Opsional)' : (tipeSakit === 'surat_dokter' ? 'Surat Keterangan Dokter / Faskes (Wajib)' : 'Surat Keterangan Rawat Inap RS (Wajib)')) : 'Eviden / Surat Pendukung (Opsional)'"></span>
                                    <span x-show="jenisIzin === 'Sakit' && (tipeSakit === 'surat_dokter' || tipeSakit === 'rawat_inap')" class="text-red-500 text-xs">*Wajib</span>
                                </label>
                                <input type="file" name="dokumen_eviden" x-ref="evidenceFile" @change="handleFileChange($event)"
                                    accept=".pdf,image/png,image/jpeg,image/jpg"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 rounded-xl p-2 cursor-pointer focus:outline-none focus:border-indigo-500 bg-white">
                                <p class="text-[11px] text-gray-400">
                                    Format berkas: <strong>PDF, JPG, JPEG, PNG</strong> (Ukuran maksimal 5MB).
                                </p>
                                @error('dokumen_eviden') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700">Alasan / Deskripsi</label>
                                <textarea name="deskripsi" rows="3" required
                                    class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Jelaskan alasan izin atau rincian keluhan sakit Anda...">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        <div class="space-y-4">
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Pilih Jam Pelajaran</h4>
                                <p class="text-sm text-gray-500">Jam pelajaran yang akan Anda tinggalkan berdasarkan jadwal.</p>
                            </div>

                            <div x-show="loading" class="flex justify-center py-6">
                                <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>

                            <div x-show="!loading && schedules.length > 0" class="grid grid-cols-1 gap-4">
                                <template x-for="schedule in schedules" :key="schedule.id">
                                    <label class="relative flex cursor-pointer items-center rounded-xl border border-gray-200 p-4 transition-all hover:border-indigo-200 hover:bg-indigo-50/50"
                                        :class="selectedIds.includes(schedule.id.toString()) ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-100' : ''">
                                        <input type="checkbox" name="jadwal_ids[]" :value="schedule.id"
                                            x-model="selectedIds" @change="refreshLmsResources()"
                                            class="mr-4 rounded text-indigo-600 transition-all focus:ring-indigo-500">
                                        <div class="flex-1">
                                            <div class="mb-1 flex items-center justify-between">
                                                <span class="font-bold text-gray-900" x-text="schedule.rombel.kelas.nama_kelas"></span>
                                                <span class="text-xs font-black uppercase tracking-widest text-indigo-600" x-text="'Jam ' + schedule.jam_ke"></span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-600" x-text="schedule.mata_pelajaran.nama_mapel"></span>
                                                <span class="font-mono text-xs text-gray-400" x-text="formatTime(schedule.jam_mulai) + ' - ' + formatTime(schedule.jam_selesai)"></span>
                                            </div>
                                        </div>
                                    </label>
                                </template>
                            </div>

                            {{-- One LMS resource selection applies to every selected lesson period. --}}
                            <div x-show="selectedIds.length > 0" x-transition
                                class="rounded-2xl border border-indigo-100 bg-indigo-50/50 p-5 shadow-sm">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <div class="rounded-lg bg-indigo-100 p-1.5 text-indigo-700">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                            </div>
                                            <h5 class="text-xs font-black uppercase tracking-widest text-gray-900">Materi atau Tugas Bersama</h5>
                                        </div>
                                        <p class="mt-2 text-xs leading-5 text-gray-600">
                                            Pilih satu kali. Materi atau tugas ini otomatis digunakan untuk seluruh
                                            <strong x-text="selectedIds.length"></strong> jam pelajaran yang dipilih.
                                        </p>
                                    </div>
                                    <span class="w-fit rounded-full bg-indigo-100 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-indigo-700"
                                        x-text="selectedIds.length + ' jam terdampak'"></span>
                                </div>

                                <div x-show="lmsLoading" class="mt-5 flex items-center gap-2 text-xs font-bold text-indigo-600">
                                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Memuat materi dan tugas LMS...
                                </div>

                                <div x-show="!lmsLoading" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold uppercase text-gray-500">Materi Pelajaran</label>
                                        <select name="lms_material_id" x-model="selectedMaterialId"
                                            class="w-full rounded-xl border-gray-200 bg-white text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Materi --</option>
                                            <template x-for="material in lmsData.materials" :key="material.id">
                                                <option :value="material.id" x-text="material.title"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold uppercase text-gray-500">Tugas & PR</label>
                                        <select name="lms_assignment_id" x-model="selectedAssignmentId"
                                            class="w-full rounded-xl border-gray-200 bg-white text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Tugas --</option>
                                            <template x-for="assignment in lmsData.assignments" :key="assignment.id">
                                                <option :value="assignment.id" x-text="assignment.title"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <div x-show="!lmsLoading && lmsData.materials.length === 0 && lmsData.assignments.length === 0"
                                    class="mt-4 flex items-center gap-2 rounded-xl border border-red-100 bg-red-50 p-3">
                                    <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    <p class="text-[10px] font-bold text-red-600">Data LMS belum tersedia. Silakan buat materi atau tugas di Ruang Belajar terlebih dahulu.</p>
                                </div>
                            </div>

                            <div x-show="!loading && schedules.length === 0 && startDate" class="text-center py-10 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                                <p class="text-gray-500 font-medium">Tidak ada jadwal ditemukan untuk tanggal ini.</p>
                            </div>

                            <div x-show="!startDate" class="text-center py-10 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                                <p class="text-gray-400 font-medium">Pilih tanggal mulai terlebih dahulu untuk melihat jadwal.</p>
                            </div>
                        </div>

                        {{-- LMS Validation Warning --}}
                        <div x-show="selectedIds.length > 0 && !isLmsValid()" 
                             x-transition
                             class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3 text-amber-700">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-black">Penugasan Belum Lengkap</p>
                                <p class="text-xs font-medium opacity-80">Pilih satu Materi Pelajaran atau Tugas & PR. Pilihan tersebut berlaku untuk seluruh jam pelajaran yang terdampak.</p>
                            </div>
                        </div>

                        <div class="pt-6 flex gap-3">
                            <a href="{{ route('guru.izin.index') }}" class="flex-1 py-3 text-center rounded-xl font-bold bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit" 
                                    :disabled="selectedIds.length > 0 && !isLmsValid()"
                                    :class="(selectedIds.length > 0 && !isLmsValid()) ? 'bg-gray-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-500 shadow-md transform active:scale-95'"
                                    class="flex-1 py-3 px-6 rounded-xl font-bold text-white transition-all">
                                Kirim Pengajuan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function permitForm() {
            return {
                startDate: @js(old('tanggal_mulai', '')),
                endDate: @js(old('tanggal_selesai', '')),
                jenisIzin: @js(old('jenis_izin', '')),
                tipeSakit: @js(old('tipe_sakit', 'surat_dokter')),
                hasEvidenceFile: false,
                schedules: [],
                selectedIds: [],
                lmsData: { materials: [], assignments: [] },
                selectedMaterialId: '',
                selectedAssignmentId: '',
                resourceScheduleId: null,
                lmsRequestNumber: 0,
                loading: false,
                lmsLoading: false,

                formatLocalDateTime(date, timeString = '16:00') {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}T${timeString}`;
                },

                handleJenisIzinChange() {
                    if (this.jenisIzin === 'Sakit') {
                        this.handleSickDurationSync();
                    }
                },

                handleTipeSakitChange() {
                    this.handleSickDurationSync();
                },

                handleStartDateChange() {
                    if (this.jenisIzin === 'Sakit') {
                        this.handleSickDurationSync();
                    }
                    this.fetchSchedules();
                },

                handleSickDurationSync() {
                    if (this.tipeSakit === 'rawat_inap') {
                        this.endDate = '';
                        return;
                    }
                    if (!this.startDate) return;
                    const start = new Date(this.startDate);
                    if (isNaN(start.getTime())) return;

                    if (this.tipeSakit === 'ringan') {
                        this.endDate = this.formatLocalDateTime(start, '16:00');
                    } else if (this.tipeSakit === 'surat_dokter') {
                        const end = new Date(start);
                        end.setDate(end.getDate() + 2);
                        this.endDate = this.formatLocalDateTime(end, '16:00');
                    }
                },

                handleFileChange(event) {
                    this.hasEvidenceFile = event.target.files && event.target.files.length > 0;
                },

                async fetchSchedules() {
                    if (!this.startDate) return;
                    this.loading = true;
                    try {
                        const response = await fetch(`{{ route('guru.izin.schedules') }}?tanggal=${this.startDate}`);
                        this.schedules = await response.json();
                        this.selectedIds = [];
                        this.lmsRequestNumber++;
                        this.resourceScheduleId = null;
                        this.lmsData = { materials: [], assignments: [] };
                        this.selectedMaterialId = '';
                        this.selectedAssignmentId = '';
                        this.lmsLoading = false;

                        // Auto-selection logic
                        this.autoSelectOverlappingSchedules();
                    } catch (error) {
                        console.error('Failed to fetch schedules', error);
                        Swal.fire('Error', 'Gagal memuat jadwal.', 'error');
                    }
                    this.loading = false;
                },

                async refreshLmsResources() {
                    const reference = this.schedules.find(schedule => this.selectedIds.includes(schedule.id.toString()));
                    if (!reference) {
                        this.lmsRequestNumber++;
                        this.resourceScheduleId = null;
                        this.lmsData = { materials: [], assignments: [] };
                        this.selectedMaterialId = '';
                        this.selectedAssignmentId = '';
                        this.lmsLoading = false;
                        return;
                    }

                    if (this.resourceScheduleId === reference.id) return;

                    const requestNumber = ++this.lmsRequestNumber;
                    this.resourceScheduleId = reference.id;
                    this.selectedMaterialId = '';
                    this.selectedAssignmentId = '';
                    this.lmsLoading = true;
                    try {
                        const response = await fetch(`/guru/izin/lms-resources/${reference.id}`);
                        if (!response.ok) throw new Error('LMS response failed');
                        const data = await response.json();
                        if (requestNumber === this.lmsRequestNumber) {
                            this.lmsData = {
                                materials: data.materials || [],
                                assignments: data.assignments || [],
                            };
                        }
                    } catch (error) {
                        if (requestNumber === this.lmsRequestNumber) {
                            this.lmsData = { materials: [], assignments: [] };
                            console.error('Failed to fetch LMS resources', error);
                        }
                    } finally {
                        if (requestNumber === this.lmsRequestNumber) this.lmsLoading = false;
                    }
                },

                autoSelectOverlappingSchedules() {
                    if (!this.startDate) return;

                    const permitStart = new Date(this.startDate);
                    const permitEnd = this.endDate ? new Date(this.endDate) : new Date(permitStart.getTime() + 10 * 60 * 60 * 1000);
                    
                    const pStartTime = permitStart.getHours().toString().padStart(2, '0') + ':' + permitStart.getMinutes().toString().padStart(2, '0');
                    const pEndTime = permitEnd.getHours().toString().padStart(2, '0') + ':' + permitEnd.getMinutes().toString().padStart(2, '0');

                    this.selectedIds = [];
                    this.schedules.forEach(schedule => {
                        const sStart = schedule.jam_mulai.substring(0, 5);
                        const sEnd = schedule.jam_selesai.substring(0, 5);

                        if (pStartTime < sEnd && pEndTime > sStart) {
                            this.selectedIds.push(schedule.id.toString());
                        }
                    });
                    this.resourceScheduleId = null;
                    this.refreshLmsResources();
                },

                validateAndSubmit() {
                    const isRawatInap = this.jenisIzin === 'Sakit' && this.tipeSakit === 'rawat_inap';

                    // Pre-validation checking if times are filled
                    if (!this.startDate || (!isRawatInap && !this.endDate)) {
                        Swal.fire({
                            title: 'Data Tidak Lengkap',
                            text: 'Mohon lengkapi tanggal dan waktu izin Anda.',
                            icon: 'warning',
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
                    }

                    // Sick leave specific validation
                    if (this.jenisIzin === 'Sakit') {
                        if (this.tipeSakit === 'surat_dokter' || this.tipeSakit === 'rawat_inap') {
                            const fileInput = this.$refs.evidenceFile;
                            const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
                            if (!hasFile) {
                                Swal.fire({
                                    title: 'Eviden Wajib Diunggah',
                                    text: this.tipeSakit === 'surat_dokter'
                                        ? 'Untuk pengajuan dengan Surat Keterangan Dokter (3 Hari), Anda wajib mengunggah surat keterangan resmi dari dokter atau faskes.'
                                        : 'Untuk pengajuan Rawat Inap RS, Anda wajib mengunggah surat keterangan rawat inap dari Rumah Sakit.',
                                    icon: 'warning',
                                    confirmButtonColor: '#4f46e5'
                                });
                                return;
                            }
                        } else if (this.tipeSakit === 'ringan') {
                            const pStart = new Date(this.startDate);
                            const pEnd = new Date(this.endDate);
                            if (pStart.toDateString() !== pEnd.toDateString()) {
                                Swal.fire({
                                    title: 'Durasi Melebihi Batas',
                                    text: 'Izin sakit ringan tanpa surat dokter hanya berlaku maksimal 1 hari pada hari yang sama.',
                                    icon: 'warning',
                                    confirmButtonColor: '#4f46e5'
                                });
                                return;
                            }
                        }
                    }

                    // Re-calculate overlapping count to be sure
                    const permitStart = new Date(this.startDate);
                    const permitEnd = new Date(this.endDate);
                    
                    const pStartTime = permitStart.getHours().toString().padStart(2, '0') + ':' + permitStart.getMinutes().toString().padStart(2, '0') + ':00';
                    const pEndTime = permitEnd.getHours().toString().padStart(2, '0') + ':' + permitEnd.getMinutes().toString().padStart(2, '0') + ':00';

                    let overlappingCount = 0;
                    this.schedules.forEach(schedule => {
                        // Using explicit string comparison for time
                        const sStart = schedule.jam_mulai; // Assuming format HH:mm:ss from backend
                        const sEnd = schedule.jam_selesai;

                        if (pStartTime < sEnd && pEndTime > sStart) {
                            overlappingCount++;
                        }
                    });

                    // If there are schedules that overlap but NONE are selected, block submission
                    if (overlappingCount > 0 && this.selectedIds.length === 0) {
                        Swal.fire({
                            title: 'Verifikasi Jadwal',
                            text: 'Sistem mendeteksi Anda memiliki jam mengajar pada waktu tersebut. Silakan centang jam pelajaran yang akan Anda tinggalkan.',
                            icon: 'warning',
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
                    }

                    // One LMS resource selection covers all checked schedules.
                    if (this.selectedIds.length > 0 && !this.isLmsValid()) {
                        Swal.fire({
                            title: 'Penugasan Belum Lengkap',
                            text: 'Anda wajib memilih satu Materi atau Tugas untuk seluruh jam pelajaran yang dipilih.',
                            icon: 'warning',
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
                    }

                    // If everything is valid, submit the form via the reference
                    this.$refs.form.submit();
                },

                formatTime(time) {
                    return time.substring(0, 5);
                },

                isLmsValid() {
                    return (this.selectedMaterialId && this.selectedMaterialId !== '')
                        || (this.selectedAssignmentId && this.selectedAssignmentId !== '');
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
