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

                            <div x-show="!loading && schedules.length > 0" class="grid grid-cols-1 gap-3">
                                <template x-for="schedule in schedules" :key="schedule.id">
                                    <label class="relative flex items-center p-4 rounded-xl border border-gray-200 hover:border-indigo-200 hover:bg-indigo-50/50 cursor-pointer transition-all"
                                           :class="selectedIds.includes(schedule.id.toString()) ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-100' : ''">
                                        <input type="checkbox" name="jadwal_ids[]" :value="schedule.id" 
                                               x-model="selectedIds"
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
                                </template>
                            </div>

                            <div x-show="!loading && schedules.length === 0 && startDate" class="text-center py-10 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                                <p class="text-gray-500 font-medium">Tidak ada jadwal ditemukan untuk tanggal ini.</p>
                            </div>

                            <div x-show="!startDate" class="text-center py-10 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                                <p class="text-gray-400 font-medium">Pilih tanggal mulai terlebih dahulu untuk melihat jadwal.</p>
                            </div>
                        </div>

                        <div class="pt-6 flex gap-3">
                            <a href="{{ route('guru.izin.index') }}" class="flex-1 py-3 text-center rounded-xl font-bold bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit" class="flex-1 py-3 px-6 rounded-xl font-bold bg-indigo-600 text-white hover:bg-indigo-500 shadow-md transform active:scale-95 transition-all">
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

                autoSelectOverlappingSchedules() {
                    if (!this.startDate || !this.endDate) return;

                    const permitStart = new Date(this.startDate);
                    const permitEnd = new Date(this.endDate);
                    
                    // Convert to only time strings for comparison HH:mm
                    const pStartTime = permitStart.getHours().toString().padStart(2, '0') + ':' + permitStart.getMinutes().toString().padStart(2, '0');
                    const pEndTime = permitEnd.getHours().toString().padStart(2, '0') + ':' + permitEnd.getMinutes().toString().padStart(2, '0');

                    this.selectedIds = [];
                    this.schedules.forEach(schedule => {
                        const sStart = schedule.jam_mulai.substring(0, 5);
                        const sEnd = schedule.jam_selesai.substring(0, 5);

                        // Overlap condition: startA < endB AND endA > startB
                        if (pStartTime < sEnd && pEndTime > sStart) {
                            this.selectedIds.push(schedule.id.toString());
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

                    // If everything is valid, submit the form via the reference
                    this.$refs.form.submit();
                },

                formatTime(time) {
                    return time.substring(0, 5);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
