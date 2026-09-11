<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-gray-800">Jadwal Tugas Guru Piket</h2>
            <p class="mt-1 text-sm text-gray-500">Atur dua Guru Kelas yang bertugas setiap hari Senin sampai Jumat.</p>
        </div>
    </x-slot>

    <div class="w-full py-6" x-data="picketSchedule(@js(old('schedules', $schedule)))">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-black">Jadwal belum dapat disimpan.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                <form method="POST" action="{{ route('guru-piket-schedules.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    @foreach(\App\Models\GuruPiketSchedule::WEEKDAYS as $dayIndex => $day)
                        <section class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                            <div class="flex items-center justify-between border-b border-gray-100 bg-gradient-to-r from-slate-50 to-white px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-sm font-black text-white">{{ $dayIndex + 1 }}</span>
                                    <div>
                                        <h3 class="font-black text-gray-900">{{ $day }}</h3>
                                        <p class="text-xs text-gray-500">Dua guru bertugas sepanjang hari sekolah.</p>
                                    </div>
                                </div>
                                <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest"
                                    :class="isComplete('{{ $day }}') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                                    x-text="isComplete('{{ $day }}') ? 'Lengkap' : 'Belum lengkap'"></span>
                            </div>
                            <div class="grid gap-4 p-6 md:grid-cols-2">
                                @foreach([0, 1] as $slot)
                                    <div>
                                        <label class="mb-2 block text-xs font-black uppercase tracking-wider text-gray-500">Guru Piket {{ $slot + 1 }}</label>
                                        <select name="schedules[{{ $day }}][]" x-model="schedule['{{ $day }}'][{{ $slot }}]" required
                                            class="w-full rounded-2xl border-gray-200 text-sm font-semibold text-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Pilih Guru Kelas</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" :disabled="usedInOtherSlot('{{ $day }}', {{ $slot }}, '{{ $teacher->id }}')">
                                                    {{ $teacher->masterGuru?->nama_lengkap ?: $teacher->name }}{{ $teacher->masterGuru?->kode_guru ? ' · '.$teacher->masterGuru->kode_guru : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach

                    <div class="sticky bottom-4 flex items-center justify-between gap-4 rounded-2xl border border-slate-700 bg-slate-900 p-4 text-white shadow-2xl">
                        <div>
                            <p class="font-black">Jadwal Mingguan</p>
                            <p class="text-xs text-slate-300"><span x-text="filledSlots"></span> dari 10 slot telah diisi.</p>
                        </div>
                        <button type="submit" class="rounded-xl bg-red-600 px-5 py-3 text-xs font-black uppercase tracking-wider transition hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50" :disabled="!allComplete">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>

                <aside class="space-y-5 lg:sticky lg:top-6 lg:self-start">
                    <div class="rounded-3xl bg-gradient-to-br from-indigo-700 to-slate-900 p-6 text-white shadow-xl">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H2v-2a4 4 0 017-3.87m8-5.13a4 4 0 11-8 0 4 4 0 018 0zM9 9a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <h3 class="mt-5 text-xl font-black">Ketentuan Jadwal</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-relaxed text-indigo-100">
                            <li>• Setiap hari wajib diisi tepat dua guru.</li>
                            <li>• Kedua petugas pada hari yang sama harus berbeda.</li>
                            <li>• Hanya Guru Kelas aktif yang dapat dipilih.</li>
                            <li>• Jadwal berlaku mingguan sampai diperbarui kembali.</li>
                        </ul>
                    </div>

                    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Ketersediaan</p>
                        <p class="mt-2 text-3xl font-black text-gray-900">{{ $teachers->count() }}</p>
                        <p class="text-sm text-gray-500">Guru Kelas aktif dapat dijadwalkan.</p>
                        @if($teachers->count() < 2)
                            <p class="mt-4 rounded-xl bg-red-50 p-3 text-xs font-bold text-red-700">Minimal dua akun Guru Kelas aktif diperlukan untuk menyusun jadwal.</p>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function picketSchedule(initialSchedule) {
                return {
                    schedule: initialSchedule,
                    usedInOtherSlot(day, slot, teacherId) {
                        return this.schedule[day].some((selected, index) => index !== slot && String(selected) === String(teacherId));
                    },
                    isComplete(day) {
                        const selected = this.schedule[day].filter(Boolean);
                        return selected.length === 2 && new Set(selected.map(String)).size === 2;
                    },
                    get filledSlots() {
                        return Object.values(this.schedule).flat().filter(Boolean).length;
                    },
                    get allComplete() {
                        return Object.keys(this.schedule).length === 5 && Object.keys(this.schedule).every(day => this.isComplete(day));
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
