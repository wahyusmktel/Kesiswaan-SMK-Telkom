<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Hasil Survei: {{ $survey->title }}</h2>
    </x-slot>

    <div class="p-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-1">
                    <a href="{{ route('surveys.index') }}"
                        class="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-slate-900 line-clamp-1">{{ $survey->title }}</h1>
                </div>
                <p class="text-sm text-slate-500 ml-12">Analisis mendalam hasil feedback responden.</p>
            </div>
            <div class="flex space-x-2 ml-12 md:ml-0">
                <a href="{{ route('surveys.export.excel', $survey) }}"
                    class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 font-bold rounded-xl border border-emerald-100 hover:bg-emerald-100 transition-colors text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Excel
                </a>
                <a href="{{ route('surveys.export.pdf', $survey) }}"
                    class="inline-flex items-center px-4 py-2 bg-rose-50 text-rose-700 font-bold rounded-xl border border-rose-100 hover:bg-rose-100 transition-colors text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    PDF
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Responden</p>
                <h2 class="text-3xl font-black text-slate-900">{{ $survey->responses->count() }}</h2>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Jumlah Pertanyaan</p>
                <h2 class="text-3xl font-black text-slate-900">{{ $survey->questions->count() }}</h2>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Status</p>
                <span
                    class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-sm font-bold rounded-full">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    {{ $survey->is_active ? 'Aktif' : 'Draft' }}
                </span>
            </div>
        </div>

        <!-- Charts & Analysis -->
        <div class="space-y-8">
            @foreach($survey->questions as $index => $question)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                        <div class="flex items-center">
                            <span
                                class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm mr-4">{{ $index + 1 }}</span>
                            <h3 class="font-bold text-slate-800">{{ $question->question_text }}</h3>
                        </div>
                        <span
                            class="text-[10px] font-black uppercase tracking-tighter text-slate-400 px-2 py-1 bg-slate-50 rounded italic">
                            {{ $question->type === 'multiple_choice' ? 'Pilihan Ganda' : 'Esai' }}
                        </span>
                    </div>

                    <div class="p-8">
                        @if($question->type === 'multiple_choice')
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                                <div id="chart-{{ $question->id }}" class="min-h-[300px]"></div>
                                <div class="space-y-4">
                                    <h4 class="text-sm font-bold text-slate-700 mb-4">Rangkuman Jawaban</h4>
                                    @php $qData = $analysis[$question->id] ?? ['labels' => [], 'values' => []]; @endphp
                                    @foreach($qData['labels'] as $labelIndex => $label)
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-slate-600 font-medium">{{ $label }}</span>
                                                <span class="text-slate-900 font-bold">{{ $qData['values'][$labelIndex] }}
                                                    ({{ $survey->responses->count() > 0 ? round(($qData['values'][$labelIndex] / $survey->responses->count()) * 100) : 0 }}%)</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-blue-500 h-full rounded-full"
                                                    style="width: {{ $survey->responses->count() > 0 ? ($qData['values'][$labelIndex] / $survey->responses->count()) * 100 : 0 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="space-y-4 max-h-[400px] overflow-y-auto pr-4 scrollbar-thin">
                                @forelse($question->answers as $answer)
                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $answer->response->respondent->name }}</span>
                                            <span
                                                class="text-[10px] text-slate-400">{{ $answer->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <p class="text-sm text-slate-700 leading-relaxed">{{ $answer->answer_value }}</p>
                                    </div>
                                @empty
                                    <p class="text-center py-10 text-slate-400 italic text-sm">Belum ada jawaban untuk pertanyaan
                                        ini.</p>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Respondents Table -->
        <div class="mt-12">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Daftar Responden
            </h2>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold">
                        <tr>
                            <th class="px-6 py-4">Nama Responden</th>
                            <th class="px-6 py-4 text-center">Waktu Mengisi</th>
                            <th class="px-6 py-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($survey->responses as $response)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $response->respondent->name }}</td>
                                <td class="px-6 py-4 text-center text-slate-500">
                                    {{ $response->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span
                                        class="px-2 py-1 bg-green-50 text-green-600 text-[10px] font-bold rounded-full uppercase">Selesai</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-slate-400 italic">Belum ada responden.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @foreach($survey->questions as $question)
                    @if($question->type === 'multiple_choice')
                        @php $qData = $analysis[$question->id]; @endphp
                        new ApexCharts(document.querySelector("#chart-{{ $question->id }}"), {
                            series: {!! json_encode($qData['values']) !!},
                            chart: {
                                type: 'donut',
                                height: 300,
                                fontFamily: 'Inter, sans-serif'
                            },
                            labels: {!! json_encode($qData['labels']) !!},
                            colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                            legend: {
                                position: 'bottom'
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function (val) {
                                    return val.toFixed(0) + "%"
                                }
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '70%',
                                        labels: {
                                            show: true,
                                            total: {
                                                show: true,
                                                label: 'Total Jawaban',
                                                formatter: function (w) {
                                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }).render();
                    @endif
                @endforeach
        });
        </script>
    @endpush
</x-app-layout>