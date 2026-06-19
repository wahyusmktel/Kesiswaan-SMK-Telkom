<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">📅 Kalender Rencana Mengajar</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex justify-between items-center mb-4">
                <a href="{{ route('guru-kelas.lesson-plan.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                    &larr; Kembali ke Dashboard
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-7 border-b border-gray-100">
                    @php
                        $startOfWeek = now()->startOfWeek();
                    @endphp
                    @for($i = 0; $i < 7; $i++)
                        @php
                            $date = $startOfWeek->copy()->addDays($i);
                            $isToday = $date->isToday();
                            $dayPlans = $plans->filter(fn($p) => $p->teach_date->isSameDay($date));
                        @endphp
                        <div class="p-4 {{ $isToday ? 'bg-blue-50' : 'bg-white' }} border-r border-gray-100 last:border-r-0 min-h-[150px]">
                            <div class="text-center mb-3">
                                <div class="text-xs font-bold text-gray-500 uppercase">{{ $date->translatedFormat('D') }}</div>
                                <div class="text-xl font-black {{ $isToday ? 'text-blue-600' : 'text-gray-900' }}">{{ $date->format('d') }}</div>
                            </div>
                            
                            <div class="space-y-2">
                                @forelse($dayPlans as $plan)
                                    <a href="{{ route('guru-kelas.lesson-plan.show', $plan->id) }}" class="block p-2 rounded-lg bg-white border border-gray-200 shadow-sm hover:border-blue-300 hover:shadow-md transition">
                                        <div class="text-xs font-bold text-gray-800 mb-1 truncate">{{ $plan->class->nama_kelas ?? '' }}</div>
                                        <div class="text-[10px] text-gray-500 truncate">{{ $plan->subject->nama_mapel ?? '' }}</div>
                                        <div class="mt-1">{!! $plan->statusBadge() !!}</div>
                                    </a>
                                @empty
                                    <div class="text-center text-[10px] text-gray-400 p-2 border border-dashed rounded-lg">
                                        Kosong
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
