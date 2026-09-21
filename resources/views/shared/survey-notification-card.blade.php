@php
    $currentUser = Auth::user();
    $isDashboard = $currentUser && (
        request()->routeIs('dashboard', '*.dashboard.*', '*dashboard*')
        || request()->is('dashboard', '*/dashboard')
        || (($userDashboardRoute = \App\Support\DashboardRedirector::routeNameForUser($currentUser)) && request()->routeIs($userDashboardRoute))
    );
    $pendingSurveys = $isDashboard ? \App\Models\Survey::getPendingSurveysForUser($currentUser) : collect();
@endphp

@if($pendingSurveys->isNotEmpty())
    @php
        $surveyKey = 'survey_alert_dismissed_' . $currentUser->id . '_' . $pendingSurveys->pluck('id')->sort()->join('_');
        $singleSurvey = $pendingSurveys->count() === 1 ? $pendingSurveys->first() : null;
    @endphp

    <div x-data="{
            show: !sessionStorage.getItem('{{ $surveyKey }}'),
            dismiss() {
                this.show = false;
                sessionStorage.setItem('{{ $surveyKey }}', 'true');
            }
        }"
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="w-full max-w-7xl mx-auto mb-6"
    >
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 p-0.5 shadow-lg shadow-red-500/10">
            <div class="rounded-[15px] bg-white p-4 sm:p-5">
                <div class="flex items-start gap-3.5 sm:gap-4">
                    {{-- Icon with pulsing indicator --}}
                    <div class="relative flex-shrink-0">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center text-red-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <span class="absolute -top-1 -right-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
                        </span>
                    </div>

                    {{-- Content details --}}
                    <div class="flex-1 min-w-0 pt-0.5">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-red-100 text-red-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Survei Kepuasan
                            </span>
                            @if($pendingSurveys->count() > 1)
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700">
                                    {{ $pendingSurveys->count() }} Survei Tersedia
                                </span>
                            @endif
                        </div>

                        <h4 class="text-base sm:text-lg font-black text-gray-900 leading-snug">
                            Anda memiliki survei yang harus di isi
                        </h4>

                        @if($singleSurvey)
                            <p class="text-xs sm:text-sm text-gray-600 mt-1">
                                Silakan isi survei <strong class="text-gray-900 font-bold">"{{ $singleSurvey->title }}"</strong>. Partisipasi dan masukan Anda sangat penting untuk evaluasi dan peningkatan layanan sekolah.
                            </p>
                            @if($singleSurvey->end_at)
                                <div class="flex items-center gap-1.5 mt-2 text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg w-fit border border-amber-200">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Batas Pengisian: {{ $singleSurvey->end_at->translatedFormat('d F Y, H:i') }} WIB</span>
                                </div>
                            @endif
                        @else
                            <p class="text-xs sm:text-sm text-gray-600 mt-1">
                                Terdapat <strong class="text-gray-900 font-bold">{{ $pendingSurveys->count() }} survei</strong> aktif yang ditujukan kepada Anda. Mohon luangkan waktu Anda untuk melengkapinya.
                            </p>
                            <div class="mt-2.5 flex flex-wrap gap-2">
                                @foreach($pendingSurveys->take(3) as $srv)
                                    <a href="{{ route('surveys.show', $srv->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 hover:bg-red-50 border border-gray-200 hover:border-red-200 rounded-lg text-xs font-semibold text-gray-700 hover:text-red-700 transition">
                                        <span>{{ Str::limit($srv->title, 35) }}</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @endforeach
                                @if($pendingSurveys->count() > 3)
                                    <span class="px-2 py-1 text-xs font-medium text-gray-500">+{{ $pendingSurveys->count() - 3 }} lainnya</span>
                                @endif
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            @if($singleSurvey)
                                <a href="{{ route('surveys.show', $singleSurvey->id) }}"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-red-500/20 hover:shadow-lg hover:shadow-red-500/30 transition-all">
                                    <span>Isi Survei Sekarang</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('surveys.index') }}"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-red-500/20 hover:shadow-lg hover:shadow-red-500/30 transition-all">
                                    <span>Lihat Semua Survei ({{ $pendingSurveys->count() }})</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            @endif

                            <button type="button" @click="dismiss()"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-xl text-xs sm:text-sm font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-colors">
                                Nanti Saja
                            </button>
                        </div>
                    </div>

                    {{-- Close Button (X) --}}
                    <div class="flex-shrink-0">
                        <button type="button" @click="dismiss()"
                            class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500"
                            title="Tutup Notifikasi" aria-label="Tutup Notifikasi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
