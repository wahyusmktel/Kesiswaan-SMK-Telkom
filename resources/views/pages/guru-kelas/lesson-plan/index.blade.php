<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">📚 Rencana Pembelajaran</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="text-muted mb-0">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <a href="{{ route('guru-kelas.lesson-plan.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Buat RPP Baru
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- KOLOM KIRI: Hari Ini & Besok --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- HARI INI --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-blue-50/50">
                            <h5 class="mb-0 font-bold text-gray-800">🗓️ Hari Ini — {{ now()->translatedFormat('d F Y') }}</h5>
                        </div>
                        <div class="p-6">
                            @if($todayPlan)
                                <x-lesson-plan-card :plan="$todayPlan" />
                            @else
                                <div class="text-center py-4 text-gray-400">
                                    <p>Belum ada rencana pembelajaran hari ini.</p>
                                    <a href="{{ route('guru-kelas.lesson-plan.create', ['date' => today()->toDateString()]) }}" class="text-blue-500 hover:text-blue-700 underline text-sm mt-2 inline-block">Buat Sekarang</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- BESOK --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-amber-50/50">
                            <h5 class="mb-0 font-bold text-gray-800">⏭️ Besok — {{ now()->addDay()->translatedFormat('d F Y') }}</h5>
                        </div>
                        <div class="p-6">
                            @if($tomorrowPlan)
                                <x-lesson-plan-card :plan="$tomorrowPlan" />
                            @else
                                <div class="text-center py-4 text-gray-400">
                                    <p>Belum ada rencana untuk besok. Mulai rencanakan sekarang!</p>
                                    <a href="{{ route('guru-kelas.lesson-plan.create', ['date' => today()->addDay()->toDateString()]) }}" class="text-amber-500 hover:text-amber-700 underline text-sm mt-2 inline-block">Rencanakan Besok</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- MINGGUAN OVERVIEW --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <h5 class="mb-0 font-bold text-gray-800">📅 7 Hari Ke Depan</h5>
                            <a href="{{ route('guru-kelas.lesson-plan.calendar') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Lihat Kalender</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                                    <tr>
                                        <th class="px-6 py-3">Tanggal</th>
                                        <th class="px-6 py-3">Kelas</th>
                                        <th class="px-6 py-3">Mata Pelajaran</th>
                                        <th class="px-6 py-3">Topik</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($weekPlans as $wp)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-3 whitespace-nowrap">{{ $wp->teach_date->translatedFormat('D, d M') }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $wp->class->nama_kelas ?? '-' }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $wp->subject->nama_mapel ?? '-' }}</td>
                                        <td class="px-6 py-3 text-gray-900 font-medium">{{ Str::limit($wp->topic, 30) }}</td>
                                        <td class="px-6 py-3">{!! $wp->statusBadge() !!}</td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ route('guru-kelas.lesson-plan.show', $wp->id) }}" class="text-blue-600 hover:text-blue-900 text-xs font-semibold">Detail</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-400">Tidak ada rencana minggu ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- KOLOM KANAN: To-Do List --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm sticky top-6">
                        <div class="px-6 py-4 border-b border-gray-100 bg-red-50/50">
                            <h5 class="mb-0 font-bold text-gray-800 flex items-center gap-2">✅ To-Do Persiapan Mengajar</h5>
                            <small class="text-red-600">{{ $pendingTodos->count() }} item belum selesai</small>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                            @forelse($pendingTodos as $todo)
                            <div class="p-4 hover:bg-gray-50 transition-colors flex items-start gap-3">
                                <div class="mt-0.5">
                                    <input type="checkbox" class="todo-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" data-id="{{ $todo->id }}" {{ $todo->is_done ? 'checked' : '' }}>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900 {{ $todo->is_done ? 'line-through text-gray-400' : '' }}">{{ $todo->todo_text }}</div>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($todo->category) }}</span>
                                        @if($todo->lessonPlan)
                                        <span class="text-xs text-gray-500">
                                            {{ $todo->lessonPlan->teach_date->translatedFormat('d M') }} · {{ Str::limit($todo->lessonPlan->subject->nama_mapel ?? '', 15) }}
                                        </span>
                                        @endif
                                    </div>
                                    @if($todo->due_before)
                                    <div class="text-xs text-red-500 mt-1">
                                        Sebeleum {{ $todo->due_before->format('H:i, d M') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="p-8 text-center text-green-500">
                                <p class="mb-0 font-medium">Semua persiapan sudah selesai! 🎉</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.querySelectorAll('.todo-checkbox').forEach(cb => {
        cb.addEventListener('change', async function () {
            const id  = this.dataset.id;
            const res = await fetch(`/guru-kelas/rencana-pembelajaran/todo/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            const textEl = this.closest('.flex').querySelector('.text-sm.font-medium');
            textEl.classList.toggle('line-through', data.is_done);
            textEl.classList.toggle('text-gray-400', data.is_done);
            textEl.classList.toggle('text-gray-900', !data.is_done);
        });
    });
    </script>
    @endpush
</x-app-layout>
