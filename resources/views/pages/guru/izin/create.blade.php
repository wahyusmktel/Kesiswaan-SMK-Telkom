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
                                    <option value="sekolah">Lingkungan Sekolah (Rapat, dsb)</option>
                                    <option value="luar" selected>Luar Sekolah / Tidak Masuk</option>
                                    <option value="terlambat">Terlambat (Datang Terlambat)</option>
                                </select>
                                <p class="text-[10px] text-gray-500 mt-2">
                                    * <strong>Sekolah</strong>: Hanya butuh persetujuan Piket.<br>
                                    * <strong>Luar Sekolah</strong>: Piket → Kurikulum → SDM.<br>
                                    * <strong>Terlambat</strong>: Hanya butuh persetujuan <strong>KAUR SDM</strong>.
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
                                    <div class="space-y-3">
                                        <label class="relative flex items-center p-4 rounded-xl border border-gray-200 hover:border-indigo-200 hover:bg-indigo-50/50 cursor-pointer transition-all"
                                               :class="selectedIds.includes(schedule.id.toString()) ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-100' : ''">
                                            <input type="checkbox" name="jadwal_ids[]" :value="schedule.id" 
                                                   x-model="selectedIds"
                                                   @change="handleScheduleToggle(schedule)"
                                                   class="rounded text-indigo-600 focus:ring-indigo-500 mr-4 transition-all">
                                            <div class="flex-1">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="font-bold text-gray-900" x-text="schedule.rombel.kelas.nama_kelas"></span>
                                                    <span class="text-xs font-black text-indigo-600 uppercase tracking-widest" x-text="'Jam ' + schedule.jam_ke"></span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-sm text-gray-600" x-text="schedule.mata_pelajaran.nama_mapel"></span>
                                                    <span class="text-xs font-mono text-gray-400" x-text="formatTime(schedule.jam_mulai) + ' - ' + formatTime(schedule.jam_selesai)"></span>
                                                </div>
                                            </div>
                                        </label>

                                        {{-- LMS Resource Selectors (Conditional) --}}
                                        <div x-show="selectedIds.includes(schedule.id.toString())" 
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 -translate-y-2"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             class="ml-8 p-5 bg-gray-50 rounded-2xl border border-gray-100 space-y-4 shadow-sm">
                                            
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="p-1.5 bg-indigo-100 text-indigo-700 rounded-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                </div>
                                                <h5 class="text-xs font-black text-gray-900 uppercase tracking-widest">Penugasan & Materi (Wajib)</h5>
                                            </div>

                                            <p class="text-[10px] text-gray-500 leading-normal mb-3">
                                                * Anda wajib melampirkan minimal satu materi atau tugas untuk setiap jam pelajaran yang ditinggalkan.
                                            </p>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="space-y-1.5">
                                                    <label class="text-[10px] font-bold text-gray-400 uppercase">Materi Pelajaran</label>
                                                    <select :name="'lms_material_ids[' + schedule.id + ']'" 
                                                        x-model="selectedLms[schedule.id + '_material']"
                                                        class="w-full text-xs rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 bg-white shadow-sm">
                                                        <option value="">-- Pilih Materi --</option>
                                                        <template x-for="material in lmsData[schedule.id]?.materials || []" :key="material.id">
                                                            <option :value="material.id" x-text="material.title"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                                <div class="space-y-1.5">
                                                    <label class="text-[10px] font-bold text-gray-400 uppercase">Tugas & PR</label>
                                                    <select :name="'lms_assignment_ids[' + schedule.id + ']'" 
                                                        x-model="selectedLms[schedule.id + '_assignment']"
                                                        class="w-full text-xs rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 bg-white shadow-sm">
                                                        <option value="">-- Pilih Tugas --</option>
                                                        <template x-for="assignment in lmsData[schedule.id]?.assignments || []" :key="assignment.id">
                                                            <option :value="assignment.id" x-text="assignment.title"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>

                                            <div x-show="lmsData[schedule.id] && lmsData[schedule.id].materials.length === 0 && lmsData[schedule.id].assignments.length === 0" 
                                                 class="p-3 bg-red-50 border border-red-100 rounded-xl flex items-center gap-2">
                                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <p class="text-[10px] text-red-600 font-bold">
                                                    Data LSM belum tersedia untuk kelas ini. Silakan buat materi/tugas di Ruang Belajar terlebih dahulu.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
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
                                <p class="text-xs font-medium opacity-80">Anda wajib memilih minimal satu Materi Pelajaran atau Tugas & PR untuk setiap jam pelajaran yang dipilih sebelum mengirim pengajuan.</p>
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
                lmsData: {}, // Map of scheduleId -> {materials, assignments}
                selectedLms: {}, // Map of scheduleId_material/scheduleId_assignment -> value
                loading: false,

                async fetchSchedules() {
                    if (!this.startDate) return;
                    this.loading = true;
                    try {
                        const response = await fetch(`{{ route('guru.izin.schedules') }}?tanggal=${this.startDate}`);
                        this.schedules = await response.json();
                        
                        // Auto-selection logic
                        this.autoSelectOverlappingSchedules();
                    } catch (error) {
                        console.error('Failed to fetch schedules', error);
                        Swal.fire('Error', 'Gagal memuat jadwal.', 'error');
                    }
                    this.loading = false;
                },

                async handleScheduleToggle(schedule) {
                    const id = schedule.id.toString();
                    if (this.selectedIds.includes(id)) {
                        // Fetch LMS resources if not already loaded
                        if (!this.lmsData[schedule.id]) {
                            try {
                                const response = await fetch(`/guru/izin/lms-resources/${schedule.id}`);
                                this.lmsData[schedule.id] = await response.json();
                            } catch (error) {
                                console.error('Failed to fetch LMS resources', error);
                            }
                        }
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
                            this.handleScheduleToggle(schedule);
                        }
                    });
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

                    // Check if LMS resources are selected for all checked schedules
                    if (this.selectedIds.length > 0 && !this.isLmsValid()) {
                        Swal.fire({
                            title: 'Penugasan Belum Lengkap',
                            text: 'Anda wajib memilih minimal satu Materi atau Tugas untuk setiap jam pelajaran yang dipilih.',
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
                    // Check if every selected schedule has at least one material or assignment
                    for (const scheduleId of this.selectedIds) {
                        const materialKey = scheduleId + '_material';
                        const assignmentKey = scheduleId + '_assignment';
                        const hasMaterial = this.selectedLms[materialKey] && this.selectedLms[materialKey] !== '';
                        const hasAssignment = this.selectedLms[assignmentKey] && this.selectedLms[assignmentKey] !== '';
                        
                        if (!hasMaterial && !hasAssignment) {
                            return false;
                        }
                    }
                    return true;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
