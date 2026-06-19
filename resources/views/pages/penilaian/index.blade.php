<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Penilaian Semester</h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success') || session('info') || session('error'))
            <div class="rounded-xl border px-4 py-3 text-sm font-semibold {{ session('error') ? 'bg-red-50 border-red-200 text-red-700' : 'bg-green-50 border-green-200 text-green-700' }}">
                {{ session('success') ?? session('info') ?? session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <p class="text-xs font-black text-red-600 uppercase tracking-widest">Periode Aktif</p>
                    <h3 class="text-lg font-extrabold text-gray-900">{{ $period?->title ?? 'Belum ada periode aktif' }}</h3>
                    @if($period)
                        <p class="text-sm text-gray-500">{{ $period->tahunPelajaran?->tahun }} - Semester {{ $period->semester }} | {{ $period->start_at->format('d M Y H:i') }} s.d. {{ $period->end_at->format('d M Y H:i') }}</p>
                    @endif
                </div>
                @if($period)
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $period->isOpen() ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $period->isOpen() ? 'Sedang Berjalan' : 'Di luar jadwal' }}
                    </span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Tugas Penilaian Saya</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($tasks as $task)
                    <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $task['instrument']->type_label }}</p>
                            <p class="text-sm text-gray-500">Target: <span class="font-semibold text-gray-700">{{ $task['target_name'] }}</span></p>
                        </div>
                        @if($task['done'])
                            <span class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-gray-100 text-gray-600 text-xs font-bold">Sudah Diisi</span>
                        @else
                            <a href="{{ route('penilaian.take', [$task['instrument'], $task['target_type'], $task['target']->id]) }}"
                               class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-bold hover:bg-red-700">
                                Isi Penilaian
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="p-10 text-center text-sm text-gray-500">Tidak ada penilaian yang perlu diisi saat ini.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
