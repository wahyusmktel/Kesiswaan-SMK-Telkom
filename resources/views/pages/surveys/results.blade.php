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
            <div class="flex flex-wrap items-center gap-2 ml-12 md:ml-0">
                @php
                    $user = auth()->user();
                    $canEdit = $survey->canEdit($user);
                    $canManageShares = $survey->canManageShares($user);
                @endphp
                @if($canManageShares)
                    <button type="button" @click="$dispatch('open-survey-share', { surveyId: {{ $survey->id }} })"
                        class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 font-bold rounded-xl border border-indigo-200 hover:bg-indigo-100 transition-colors text-sm shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                        Bagikan
                    </button>
                @endif
                @if($canEdit)
                    <a href="{{ route('surveys.edit', $survey) }}"
                        class="inline-flex items-center px-4 py-2 bg-amber-50 text-amber-700 font-bold rounded-xl border border-amber-200 hover:bg-amber-100 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.243 3.757a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17.657 3.757z" />
                        </svg>
                        Edit Survei
                    </a>
                    <form action="{{ route('surveys.duplicate', $survey) }}" method="POST" class="inline"
                        onsubmit="return confirm('Duplikat survei ini sebagai draft?')">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-slate-50 text-slate-700 font-bold rounded-xl border border-slate-200 hover:bg-slate-100 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Duplikat
                        </button>
                    </form>
                @endif
                <a href="{{ route('surveys.export.excel', $survey) }}"
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 shadow-sm transition-colors text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
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

        <!-- Stats Overview Widgets -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Card 1: Total Target -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Target Peserta</p>
                    <h2 class="text-2xl font-black text-slate-900">{{ number_format($totalTarget) }}</h2>
                    <p class="text-[11px] text-slate-400 font-medium mt-1">Partisipan terdaftar</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            <!-- Card 2: Sudah Mengisi -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Sudah Mengisi</p>
                    <h2 class="text-2xl font-black text-emerald-600">{{ number_format($totalSubmitted) }}</h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 mt-1 border border-emerald-200">
                        {{ $completionRate }}% selesai
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Card 3: Belum Mengisi -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Belum Mengisi</p>
                    <h2 class="text-2xl font-black text-amber-600">{{ number_format($totalPending) }}</h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 mt-1 border border-amber-200">
                        {{ $totalTarget > 0 ? round(($totalPending / $totalTarget) * 100, 1) : 0 }}% tersisa
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Card 4: Tingkat Partisipasi -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tingkat Partisipasi</p>
                    <span class="text-sm font-extrabold text-indigo-600">{{ $completionRate }}%</span>
                </div>
                <div class="my-2">
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $completionRate }}%"></div>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 font-medium">
                    {{ $totalSubmitted }} dari {{ $totalTarget }} responden
                </p>
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
                                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $answer->response->respondent->name ?? 'Pengguna' }}</span>
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

        <!-- Respondents Table with Filter & 10-Item Pagination -->
        <div class="mt-12">
            <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Daftar Responden & Status Partisipasi
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Pantau siswa dan peserta yang sudah maupun belum mengisi survei beserta kelasnya.
                    </p>
                </div>

                <!-- Filter Form -->
                <form method="GET" action="{{ route('surveys.results', $survey) }}" class="flex flex-wrap items-center gap-2.5">
                    <input type="hidden" name="status" value="{{ $statusFilter }}">

                    <!-- Class Filter Dropdown -->
                    @if($availableClasses->count() > 1)
                        <div class="min-w-[150px]">
                            <select name="kelas" onchange="this.form.submit()"
                                class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2">
                                <option value="">Semua Kelas ({{ $availableClasses->count() }})</option>
                                @foreach($availableClasses as $cls)
                                    <option value="{{ $cls }}" {{ $kelasFilter === $cls ? 'selected' : '' }}>{{ $cls }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Search Input -->
                    <div class="relative min-w-[220px]">
                        <input type="text" name="search" value="{{ $searchFilter }}" placeholder="Cari nama, NIS, atau kelas..."
                            class="w-full text-xs rounded-xl border-slate-200 bg-white shadow-sm pl-8 pr-8 py-2 focus:border-indigo-500 focus:ring-indigo-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        @if($searchFilter !== '' || $kelasFilter !== '')
                            <a href="{{ route('surveys.results', array_merge(['survey' => $survey->id], $statusFilter !== 'all' ? ['status' => $statusFilter] : [])) }}"
                                class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600 text-xs font-bold" title="Reset Filter">
                                &times;
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Status Tabs -->
            <div class="flex items-center space-x-2 mb-4 overflow-x-auto pb-1">
                <a href="{{ route('surveys.results', array_merge(['survey' => $survey->id, 'status' => 'all'], $kelasFilter ? ['kelas' => $kelasFilter] : [], $searchFilter ? ['search' => $searchFilter] : [])) }}"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1.5 {{ $statusFilter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>Semua Responden</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'all' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $totalTarget }}</span>
                </a>

                <a href="{{ route('surveys.results', array_merge(['survey' => $survey->id, 'status' => 'pending'], $kelasFilter ? ['kelas' => $kelasFilter] : [], $searchFilter ? ['search' => $searchFilter] : [])) }}"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1.5 {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-amber-700 hover:bg-amber-50 border border-amber-200' }}">
                    <span>Belum Mengisi</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'pending' ? 'bg-amber-700 text-white' : 'bg-amber-100 text-amber-800 font-bold' }}">{{ $totalPending }}</span>
                </a>

                <a href="{{ route('surveys.results', array_merge(['survey' => $survey->id, 'status' => 'submitted'], $kelasFilter ? ['kelas' => $kelasFilter] : [], $searchFilter ? ['search' => $searchFilter] : [])) }}"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1.5 {{ $statusFilter === 'submitted' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-emerald-700 hover:bg-emerald-50 border border-emerald-200' }}">
                    <span>Sudah Mengisi</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'submitted' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-800 font-bold' }}">{{ $totalSubmitted }}</span>
                </a>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 w-14 text-center">No</th>
                                <th class="px-6 py-4">Nama Responden</th>
                                <th class="px-6 py-4">NIS</th>
                                <th class="px-6 py-4">Kelas / Unit</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Waktu Mengisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($respondentsPaginator as $index => $respondent)
                                <tr class="hover:bg-slate-50/60 transition-colors {{ !$respondent->has_responded ? 'bg-amber-50/20' : '' }}">
                                    <!-- No -->
                                    <td class="px-6 py-4 text-center font-bold text-xs text-slate-400">
                                        {{ $respondentsPaginator->firstItem() + $index }}
                                    </td>

                                    <!-- Nama Responden -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $respondent->has_responded ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                {{ strtoupper(substr($respondent->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800">{{ $respondent->name }}</p>
                                                <p class="text-[11px] text-slate-400 font-medium">{{ $respondent->email ?: '-' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- NIS -->
                                    <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-600">
                                        {{ $respondent->nis !== '-' ? $respondent->nis : '—' }}
                                    </td>

                                    <!-- Kelas / Unit -->
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $respondent->is_student ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $respondent->kelas }}
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 text-center">
                                        @if($respondent->has_responded)
                                            <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded-full border border-emerald-200">
                                                <svg class="w-3 h-3 mr-1 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Sudah Mengisi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 text-[11px] font-bold rounded-full border border-amber-200">
                                                <svg class="w-3 h-3 mr-1 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                Belum Mengisi
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Waktu Mengisi -->
                                    <td class="px-6 py-4 text-right text-xs font-medium text-slate-500">
                                        @if($respondent->has_responded && $respondent->submitted_at)
                                            {{ $respondent->submitted_at->translatedFormat('d M Y, H:i') }}
                                        @else
                                            <span class="text-slate-300 font-mono font-bold">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-xs font-bold text-slate-600">Tidak ada data responden ditemukan</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Coba sesuaikan filter atau kata kunci pencarian Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer (10 per page) -->
                @if($respondentsPaginator->hasPages() || $respondentsPaginator->total() > 0)
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <p class="text-xs text-slate-500 font-medium">
                            Menampilkan <span class="font-bold text-slate-700">{{ $respondentsPaginator->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-700">{{ $respondentsPaginator->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-700">{{ $respondentsPaginator->total() }}</span> responden
                        </p>
                        <div>
                            {{ $respondentsPaginator->links() }}
                        </div>
                    </div>
                @endif
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

    @include('pages.surveys.partials.share-modal')
</x-app-layout>