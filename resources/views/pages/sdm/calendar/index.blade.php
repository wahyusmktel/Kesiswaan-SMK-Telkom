<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Kalender SDM</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola hari libur dan cuti bersama untuk perhitungan absensi fingerprint.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Kalender Kerja</p>
                        <h3 class="mt-1 text-2xl font-black text-gray-900">{{ $month->translatedFormat('F Y') }}</h3>
                    </div>
                    <form method="GET" action="{{ route('sdm.calendar.index') }}" class="flex gap-2">
                        <input type="month" name="month" value="{{ $month->format('Y-m') }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-bold text-white hover:bg-red-600">Tampilkan</button>
                    </form>
                </div>

                <div class="grid grid-cols-7 border-b border-gray-100 bg-gray-50">
                    @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
                        <div class="px-3 py-3 text-center text-xs font-black uppercase tracking-widest text-gray-400">{{ $dayName }}</div>
                    @endforeach
                </div>

                <div class="grid grid-cols-7">
                    @foreach($days as $day)
                        @php
                            $dayEvents = $day['events'];
                            $hasHoliday = $dayEvents->where('type', 'holiday')->isNotEmpty();
                            $hasLeave = $dayEvents->where('type', 'collective_leave')->isNotEmpty();
                            $cellClass = $day['in_month'] ? 'bg-white' : 'bg-gray-50/70 text-gray-300';
                            if ($day['is_weekend']) $cellClass = 'bg-slate-50';
                            if ($hasHoliday) $cellClass = 'bg-red-50';
                            if ($hasLeave) $cellClass = 'bg-amber-50';
                        @endphp
                        <div class="min-h-28 border-r border-b border-gray-100 p-3 {{ $cellClass }}">
                            <div class="flex items-center justify-between">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-black {{ $day['date']->isToday() ? 'bg-red-600 text-white' : 'text-gray-800' }}">
                                    {{ $day['date']->day }}
                                </span>
                                @if($day['is_weekend'])
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Weekend</span>
                                @endif
                            </div>
                            <div class="mt-3 space-y-1.5">
                                @foreach($dayEvents->take(2) as $event)
                                    <div class="truncate rounded-lg px-2 py-1 text-[11px] font-black {{ $event->type === 'holiday' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $event->title }}
                                    </div>
                                @endforeach
                                @if($dayEvents->count() > 2)
                                    <div class="text-[11px] font-bold text-gray-400">+{{ $dayEvents->count() - 2 }} event</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <form method="POST" action="{{ route('sdm.calendar.store') }}" class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                    @csrf
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900">Tambah Kalender</h3>
                        <p class="mt-1 text-sm text-gray-500">Tanggal ini tidak dihitung wajib hadir di fingerprint.</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Nama Event</label>
                            <input name="title" value="{{ old('title') }}" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Idul Fitri / Cuti Bersama">
                            @error('title') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Jenis</label>
                            <select name="type" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                <option value="holiday" {{ old('type') === 'holiday' ? 'selected' : '' }}>Hari Libur</option>
                                <option value="collective_leave" {{ old('type') === 'collective_leave' ? 'selected' : '' }}>Cuti Bersama</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Mulai</label>
                                <input type="date" name="date_from" value="{{ old('date_from') }}" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                @error('date_from') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Sampai</label>
                                <input type="date" name="date_to" value="{{ old('date_to') }}" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                @error('date_to') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Keterangan</label>
                            <textarea name="description" rows="3" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Opsional">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                        <button class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">Simpan</button>
                    </div>
                </form>

                <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900">Event Terdekat</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($upcomingEvents as $event)
                            <div class="p-5 flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-bold text-gray-900">{{ $event->title }}</div>
                                    <div class="mt-1 text-xs font-semibold text-gray-400">
                                        {{ $event->date_from->format('d M Y') }} - {{ $event->date_to->format('d M Y') }}
                                    </div>
                                    <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-[11px] font-black {{ $event->type === 'holiday' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $event->type_label }}
                                    </span>
                                </div>
                                <form method="POST" action="{{ route('sdm.calendar.destroy', $event) }}" onsubmit="return confirm('Hapus event kalender ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-bold text-gray-500 hover:bg-red-50 hover:text-red-600">Hapus</button>
                                </form>
                            </div>
                        @empty
                            <div class="p-8 text-center text-sm text-gray-500">Belum ada hari libur atau cuti bersama.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
