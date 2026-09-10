<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Pengajuan Izin Guru</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="permitForm()">
        <div class="w-full px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <form x-ref="form" action="{{ route('guru.izin.store') }}" method="POST" @submit.prevent="validateAndSubmit()">
                    @csrf
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Tanggal & Waktu Mulai</label>
                                <input type="datetime-local" name="tanggal_mulai" required x-model="startDate" @change="fetchSchedules()"
                                    class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('tanggal_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Tanggal & Waktu Selesai</label>
                                <input type="datetime-local" name="tanggal_selesai" required x-model="endDate" @change="fetchSchedules()"
                                    class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('tanggal_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Jenis Izin</label>
                                <select name="jenis_izin" required
                                    class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih Jenis</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Dinas">Dinas Out</option>
                                    <option value="Keperluan Pribadi">Keperluan Pribadi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Kategori Penyetujuan</label>
                                <select name="kategori_penyetujuan" required
                                    class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="sekolah" {{ old('kategori_penyetujuan') === 'sekolah' ? 'selected' : '' }}>Lingkungan Sekolah (Rapat, dsb)</option>
                                    <option value="luar" {{ old('kategori_penyetujuan', 'luar') === 'luar' ? 'selected' : '' }}>Luar Sekolah</option>
                                    <option value="tidak_masuk" {{ old('kategori_penyetujuan') === 'tidak_masuk' ? 'selected' : '' }}>Izin Tidak Masuk</option>
                                    <option value="terlambat" {{ old('kategori_penyetujuan') === 'terlambat' ? 'selected' : '' }}>Terlambat (Datang Terlambat)</option>
                                </select>
                                <p class="text-[10px] text-gray-500 mt-2">
                                    * <strong>Sekolah</strong>: Hanya butuh persetujuan Piket.<br>
                                    * <strong>Luar Sekolah</strong>: Piket → Kurikulum → SDM.<br>
                                    * <strong>Izin Tidak Masuk</strong>: Langsung ke KAUR SDM.<br>
                                    * <strong>Terlambat</strong>: Langsung ke KAUR SDM.<br>
                                    * Khusus <strong>Pegawai Tetap</strong>, Luar Sekolah, Izin Tidak Masuk, dan Terlambat dilanjutkan ke Kepala Sekolah.
                                </p>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700">Alasan / Deskripsi</label>
                                <textarea name="deskripsi" rows="3" required
                                    class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Jelaskan alasan izin Anda..."></textarea>
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
                startDate: '',
                endDate: '',
                schedules: [],
                selectedIds: [],
                lmsData: { materials: [], assignments: [] },
                selectedMaterialId: '',
                selectedAssignmentId: '',
                resourceScheduleId: null,
                lmsRequestNumber: 0,
                loading: false,
                lmsLoading: false,

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
                    if (!this.startDate || !this.endDate) return;

                    const permitStart = new Date(this.startDate);
                    const permitEnd = new Date(this.endDate);
                    
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
                    // Pre-validation checking if times are filled
                    if (!this.startDate || !this.endDate) {
                        Swal.fire({
                            title: 'Data Tidak Lengkap',
                            text: 'Mohon lengkapi tanggal dan waktu mulai serta selesai izin Anda.',
                            icon: 'warning',
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
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
