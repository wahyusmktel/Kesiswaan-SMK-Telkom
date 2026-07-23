<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-gray-800">Kalender SDM</h2>
            <p class="mt-0.5 text-sm text-gray-500">Kelola agenda sekolah dan hari tidak efektif dalam satu kalender.</p>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="workCalendarPage()">
        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                <p class="font-bold">Data belum dapat diproses.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-gray-100 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-black uppercase text-gray-400">Kalender Kerja</p>
                    <h3 class="mt-1 text-2xl font-black text-gray-900">{{ $month->translatedFormat('F Y') }}</h3>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('sdm.calendar.index', ['month' => $previousMonth]) }}"
                        title="Bulan sebelumnya"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 transition hover:border-red-300 hover:bg-red-50 hover:text-red-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                        </svg>
                        <span class="sr-only">Bulan sebelumnya</span>
                    </a>

                    <form method="GET" action="{{ route('sdm.calendar.index') }}" class="flex items-center gap-2">
                        <input type="month" name="month" value="{{ $month->format('Y-m') }}"
                            aria-label="Pilih bulan kalender"
                            class="h-10 rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        <button class="h-10 rounded-lg bg-gray-900 px-4 text-sm font-bold text-white transition hover:bg-red-600">
                            Tampilkan
                        </button>
                    </form>

                    <a href="{{ route('sdm.calendar.index', ['month' => $nextMonth]) }}"
                        title="Bulan berikutnya"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 transition hover:border-red-300 hover:bg-red-50 hover:text-red-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                        </svg>
                        <span class="sr-only">Bulan berikutnya</span>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <div class="min-w-[900px]">
                    <div class="grid grid-cols-7 border-b border-gray-100 bg-gray-50">
                        @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
                            <div class="px-3 py-3 text-center text-xs font-black uppercase text-gray-400">{{ $dayName }}</div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-7">
                        @foreach ($days as $day)
                            @php
                                $dayEvents = $day['events'];
                                $hasNonWorking = $dayEvents->where('is_non_working', true)->isNotEmpty();
                                $cellClass = $day['in_month'] ? 'bg-white' : 'bg-gray-50/70';
                                if ($day['is_weekend']) {
                                    $cellClass = 'bg-slate-50';
                                }
                                if ($hasNonWorking) {
                                    $cellClass = 'bg-red-50/70';
                                }
                            @endphp
                            <div class="min-h-32 border-b border-r border-gray-100 p-3 {{ $cellClass }}">
                                <div class="flex items-center justify-between">
                                    <span @class([
                                        'flex h-8 w-8 items-center justify-center rounded-full text-sm font-black',
                                        'bg-red-600 text-white' => $day['date']->isToday(),
                                        'text-gray-800' => !$day['date']->isToday() && $day['in_month'],
                                        'text-gray-300' => !$day['in_month'],
                                    ])>
                                        {{ $day['date']->day }}
                                    </span>
                                    @if ($day['is_weekend'])
                                        <span class="text-[10px] font-black uppercase text-slate-400">Weekend</span>
                                    @endif
                                </div>

                                <div class="mt-3 space-y-1.5">
                                    @foreach ($dayEvents->take(3) as $event)
                                        @php
                                            $eventClass = match ($event->type) {
                                                'academic_activity' => 'bg-blue-100 text-blue-700',
                                                'assessment' => 'bg-amber-100 text-amber-800',
                                                'report_distribution' => 'bg-emerald-100 text-emerald-700',
                                                default => $event->is_non_working
                                                    ? 'bg-red-100 text-red-700'
                                                    : 'bg-gray-100 text-gray-700',
                                            };
                                        @endphp
                                        <button type="button"
                                            @click="editEvent(@js([
                                                'id' => $event->id,
                                                'title' => $event->title,
                                                'type' => $event->type,
                                                'date_from' => $event->date_from->format('Y-m-d'),
                                                'date_to' => $event->date_to->format('Y-m-d'),
                                                'description' => $event->description,
                                                'is_non_working' => $event->is_non_working,
                                                'update_url' => route('sdm.calendar.update', $event),
                                            ]))"
                                            class="block w-full truncate rounded-md px-2 py-1 text-left text-[11px] font-black {{ $eventClass }}"
                                            title="{{ $event->title }}">
                                            {{ $event->title }}
                                        </button>
                                    @endforeach
                                    @if ($dayEvents->count() > 3)
                                        <div class="text-[11px] font-bold text-gray-400">+{{ $dayEvents->count() - 3 }} agenda</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-bold text-gray-900">Agenda {{ $month->translatedFormat('F Y') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">Agenda yang berlangsung pada bulan kalender yang sedang ditampilkan.</p>
                </div>
                <span class="text-sm font-bold text-gray-500">{{ $monthEvents->count() }} agenda</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-black uppercase text-gray-500">Tanggal</th>
                            <th class="px-5 py-3 text-left text-xs font-black uppercase text-gray-500">Agenda</th>
                            <th class="px-5 py-3 text-left text-xs font-black uppercase text-gray-500">Jenis</th>
                            <th class="px-5 py-3 text-left text-xs font-black uppercase text-gray-500">Absensi</th>
                            <th class="px-5 py-3 text-right text-xs font-black uppercase text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($monthEvents as $event)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-gray-700">
                                    {{ $event->date_from->translatedFormat('d M Y') }}
                                    @if (!$event->date_from->isSameDay($event->date_to))
                                        <span class="text-gray-400">-</span> {{ $event->date_to->translatedFormat('d M Y') }}
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900">{{ $event->title }}</div>
                                    @if ($event->description)
                                        <div class="mt-1 max-w-xl text-xs text-gray-500">{{ $event->description }}</div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-700">
                                        {{ $event->type_label }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                        'bg-red-50 text-red-700' => $event->is_non_working,
                                        'bg-emerald-50 text-emerald-700' => !$event->is_non_working,
                                    ])>
                                        {{ $event->is_non_working ? 'Tidak wajib hadir' : 'Tetap berlaku' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button"
                                            @click="editEvent(@js([
                                                'id' => $event->id,
                                                'title' => $event->title,
                                                'type' => $event->type,
                                                'date_from' => $event->date_from->format('Y-m-d'),
                                                'date_to' => $event->date_to->format('Y-m-d'),
                                                'description' => $event->description,
                                                'is_non_working' => $event->is_non_working,
                                                'update_url' => route('sdm.calendar.update', $event),
                                            ]))"
                                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-bold text-gray-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('sdm.calendar.destroy', $event) }}"
                                            onsubmit="return confirm('Hapus agenda {{ addslashes($event->title) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-500">
                                    Belum ada agenda pada {{ $month->translatedFormat('F Y') }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <form method="POST" action="{{ route('sdm.calendar.store') }}"
                class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
                x-data="{
                    type: @js(old('type', 'academic_activity')),
                    nonWorking: {{ old('is_non_working') ? 'true' : 'false' }},
                    syncAttendanceRule() {
                        this.nonWorking = ['holiday', 'collective_leave', 'national_holiday', 'school_break', 'religious_holiday'].includes(this.type);
                    }
                }">
                @csrf
                <div class="border-b border-gray-100 px-5 py-4">
                    <h3 class="font-bold text-gray-900">Tambah Agenda</h3>
                    <p class="mt-1 text-sm text-gray-500">Tambahkan satu kegiatan ke kalender sekolah.</p>
                </div>
                <div class="space-y-4 p-5">
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase text-gray-500">Nama Kegiatan</label>
                        <input name="title" value="{{ old('title') }}" required
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="Contoh: Asesmen Sumatif Semester">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase text-gray-500">Jenis Kegiatan</label>
                        <select name="type" x-model="type" @change="syncAttendanceRule()" required
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            @foreach ($typeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-xs font-black uppercase text-gray-500">Tanggal Mulai</label>
                            <input type="date" name="date_from" value="{{ old('date_from') }}" required
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-black uppercase text-gray-500">Tanggal Selesai</label>
                            <input type="date" name="date_to" value="{{ old('date_to') }}" required
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                    </div>
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <input type="checkbox" name="is_non_working" value="1" x-model="nonWorking"
                            class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span>
                            <span class="block text-sm font-bold text-gray-800">Tidak mewajibkan absensi fingerprint</span>
                            <span class="mt-1 block text-xs leading-5 text-gray-500">Aktifkan hanya untuk hari libur, cuti bersama, atau agenda yang membebaskan pegawai dari kewajiban hadir.</span>
                        </span>
                    </label>
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase text-gray-500">Keterangan</label>
                        <textarea name="description" rows="3"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="Opsional">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="flex justify-end border-t border-gray-100 bg-gray-50 px-5 py-4">
                    <button class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">
                        Simpan Agenda
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('sdm.calendar.import') }}" enctype="multipart/form-data"
                class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                @csrf
                <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h3 class="font-bold text-gray-900">Import Agenda Excel</h3>
                    <p class="mt-1 text-sm text-gray-500">Tambahkan banyak agenda sekaligus dari file Excel.</p>
                </div>
                <div class="space-y-5 p-5">
                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                        <p class="font-bold">Format file yang didukung</p>
                        <p class="mt-2 leading-6">Data dibaca mulai baris 6: kolom A No., kolom B Tanggal Mulai, kolom C Tanggal Selesai, kolom D Nama Kegiatan, dan kolom E Jenis Kegiatan.</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase text-gray-500">File Agenda</label>
                        <input type="file" name="agenda_file" accept=".xlsx,.xls" required
                            class="block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-700 file:mr-4 file:border-0 file:bg-gray-900 file:px-4 file:py-3 file:font-bold file:text-white hover:file:bg-red-600">
                        <p class="mt-2 text-xs text-gray-500">Maksimal 10 MB. Agenda dengan nama dan rentang tanggal yang sama akan diperbarui, bukan digandakan.</p>
                    </div>
                </div>
                <div class="flex justify-end border-t border-gray-100 bg-gray-50 px-5 py-4">
                    <button class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-600">
                        Import Excel
                    </button>
                </div>
            </form>
        </div>

        <div x-cloak x-show="editOpen" @keydown.escape.window="editOpen = false"
            class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="edit-calendar-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div x-show="editOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/60" @click="editOpen = false"></div>
                <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>

                <div x-show="editOpen" x-transition
                    class="relative inline-block w-full max-w-xl overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl sm:my-8 sm:align-middle">
                    <form :action="editForm.update_url" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_editing_id" :value="editForm.id">
                        <div class="flex items-start justify-between border-b border-gray-100 px-5 py-4">
                            <div>
                                <h3 id="edit-calendar-title" class="font-bold text-gray-900">Edit Agenda</h3>
                                <p class="mt-1 text-sm text-gray-500">Perbarui informasi kegiatan kalender.</p>
                            </div>
                            <button type="button" @click="editOpen = false" title="Tutup"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" />
                                </svg>
                                <span class="sr-only">Tutup</span>
                            </button>
                        </div>
                        <div class="space-y-4 p-5">
                            <div>
                                <label class="mb-2 block text-xs font-black uppercase text-gray-500">Nama Kegiatan</label>
                                <input name="title" x-model="editForm.title" required
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-black uppercase text-gray-500">Jenis Kegiatan</label>
                                <select name="type" x-model="editForm.type" required
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                    @foreach ($typeOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-xs font-black uppercase text-gray-500">Tanggal Mulai</label>
                                    <input type="date" name="date_from" x-model="editForm.date_from" required
                                        class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                </div>
                                <div>
                                    <label class="mb-2 block text-xs font-black uppercase text-gray-500">Tanggal Selesai</label>
                                    <input type="date" name="date_to" x-model="editForm.date_to" required
                                        class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                </div>
                            </div>
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <input type="checkbox" name="is_non_working" value="1" x-model="editForm.is_non_working"
                                    class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span>
                                    <span class="block text-sm font-bold text-gray-800">Tidak mewajibkan absensi fingerprint</span>
                                    <span class="mt-1 block text-xs text-gray-500">Agenda ini dianggap sebagai hari tidak wajib hadir.</span>
                                </span>
                            </label>
                            <div>
                                <label class="mb-2 block text-xs font-black uppercase text-gray-500">Keterangan</label>
                                <textarea name="description" rows="3" x-model="editForm.description"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50 px-5 py-4">
                            <button type="button" @click="editOpen = false"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-100">
                                Batal
                            </button>
                            <button class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>[x-cloak] { display: none !important; }</style>
    @endpush

    @push('scripts')
        <script>
            function workCalendarPage() {
                return {
                    editOpen: false,
                    editForm: {
                        id: null,
                        title: '',
                        type: 'academic_activity',
                        date_from: '',
                        date_to: '',
                        description: '',
                        is_non_working: false,
                        update_url: '',
                    },
                    editEvent(event) {
                        this.editForm = {
                            ...event,
                            description: event.description || '',
                            is_non_working: Boolean(event.is_non_working),
                        };
                        this.editOpen = true;
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
