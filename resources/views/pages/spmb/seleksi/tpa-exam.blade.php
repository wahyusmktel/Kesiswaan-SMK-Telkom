<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ujian {{ $kategori->nama }} | CBT SPMB SMK Telkom Lampung</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .tpa-content img {
            display: inline-block;
            max-height: 10rem;
            max-width: 100%;
            vertical-align: middle;
            margin: 0.25rem 0.125rem;
            border-radius: 0.375rem;
            background-color: #ffffff;
        }
        .tpa-content table {
            border-collapse: collapse;
            margin: 0.5rem 0;
            font-size: 0.75rem;
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            display: block;
        }
        .tpa-content table, .tpa-content th, .tpa-content td {
            border: 1px solid #cbd5e1;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-100 flex flex-col justify-between select-none"
    x-data="tpaExamEngine({{ json_encode($soals) }}, {{ json_encode($existingAnswers) }}, {{ $sisaDetik }})">

    {{-- TOP STICKY CBT HEADER --}}
    <header class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 py-2.5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('seleksi.dashboard') }}" 
                    onclick="return confirm('Kembali ke beranda? Jawaban yang telah Anda pilih tersimpan aman.')"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="px-2 py-0.2 rounded text-[10px] font-extrabold uppercase tracking-wider
                            {{ $mapel === 'mtk' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $mapel === 'bing' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $mapel === 'bindo' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $mapel === 'tik' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                            {{ $kategori->kode }}
                        </span>
                        <h1 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-tight leading-none">
                            {{ $kategori->nama }}
                        </h1>
                    </div>
                    <p class="text-[11px] font-semibold text-slate-500 mt-0.5">
                        Soal <span x-text="currentIdx + 1"></span> dari <span x-text="totalSoal"></span>
                    </p>
                </div>
            </div>

            {{-- TIMER & QUESTION PALETTE TOGGLE --}}
            <div class="flex items-center gap-2">
                {{-- Countdown Timer Box --}}
                <div class="flex items-center gap-1 px-3 py-1.5 rounded-xl border font-mono font-bold text-xs"
                    :class="timerSeconds < 300 ? (timerSeconds < 60 ? 'bg-rose-100 border-rose-300 text-rose-700 animate-pulse' : 'bg-amber-50 border-amber-300 text-amber-800') : 'bg-slate-50 border-slate-200 text-slate-800'">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-text="formatTimer()"></span>
                </div>

                {{-- Palette Toggle --}}
                <button type="button" @click="showPalette = !showPalette"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 transition-colors">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span class="hidden sm:inline">Nomor</span>
                    <span class="px-1.5 py-0.5 text-[10px] rounded-md bg-white font-mono shadow-xs text-red-600 font-bold"
                        x-text="answeredCount + '/' + totalSoal"></span>
                </button>
            </div>
        </div>
    </header>

    {{-- MAIN EXAM VIEWPORT --}}
    <main class="flex-1 max-w-4xl mx-auto w-full p-4 sm:p-6 pb-28">

        {{-- FORM SUBMISSION --}}
        <form id="tpaForm" method="POST" action="{{ route('seleksi.tpa.submit', $mapel) }}">
            @csrf

            {{-- Hidden inputs for all answers --}}
            <template x-for="(val, key) in answers" :key="key">
                <input type="hidden" :name="'jawaban[' + key + ']'" :value="val">
            </template>

            {{-- ACTIVE QUESTION CARD --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6 space-y-4">
                
                {{-- Question Header --}}
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-lg text-xs font-black text-white tracking-wider uppercase
                            {{ $mapel === 'mtk' ? 'bg-blue-600' : '' }}
                            {{ $mapel === 'bing' ? 'bg-purple-600' : '' }}
                            {{ $mapel === 'bindo' ? 'bg-red-600' : '' }}
                            {{ $mapel === 'tik' ? 'bg-emerald-600' : '' }}"
                            x-text="'NOMOR ' + (currentIdx + 1)"></span>
                        <span class="text-xs text-slate-400 font-medium hidden sm:inline" x-text="'Mata Uji: ' + '{{ $kategori->nama }}'"></span>
                    </div>
                    <span class="text-xs text-slate-400 font-mono" x-text="'Bobot: ' + (currentSoal.bobot || 1) + ' Poin'"></span>
                </div>

                {{-- Teks Pertanyaan (HTML Rendered for Formulas, Images, Tables) --}}
                <div class="text-sm sm:text-base text-slate-900 leading-relaxed font-medium tpa-content"
                    x-html="formatTpaContent(currentSoal.pertanyaan_html || currentSoal.pertanyaan)">
                </div>

                {{-- Standalone Image (If question has separate image) --}}
                <template x-if="currentSoal.gambar && !currentSoal.pertanyaan.includes('<img')">
                    <div class="flex justify-center p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <img :src="currentSoal.gambar.startsWith('http') || currentSoal.gambar.startsWith('/images') || currentSoal.gambar.startsWith('images') ? (currentSoal.gambar.startsWith('/') ? currentSoal.gambar : '/' + currentSoal.gambar) : '/storage/' + currentSoal.gambar" 
                            alt="Gambar Soal" 
                            class="max-h-60 sm:max-h-72 w-auto object-contain rounded-xl shadow-sm">
                    </div>
                </template>

                {{-- Pilihan Opsi Jawaban (A, B, C, D, E) --}}
                <div class="space-y-2.5 pt-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Pilih Jawaban Anda:
                    </label>

                    <div class="space-y-2.5">
                        <template x-for="abjad in ['A', 'B', 'C', 'D', 'E']" :key="abjad">
                            <template x-if="currentSoal['opsi_' + abjad.toLowerCase()]">
                                <label class="flex items-start gap-3.5 p-3.5 sm:p-4 rounded-xl border-2 cursor-pointer transition-all active:scale-[0.99] tpa-content"
                                    :class="answers[currentSoal.id] === abjad ? 'border-red-600 bg-red-50/50 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                    
                                    <input type="radio" 
                                        :name="'radio_' + currentSoal.id" 
                                        :value="abjad" 
                                        @change="selectAnswer(currentSoal.id, abjad)"
                                        :checked="answers[currentSoal.id] === abjad"
                                        class="w-4 h-4 text-red-600 focus:ring-red-500 mt-0.5 flex-shrink-0">
                                    
                                    <div class="text-xs sm:text-sm text-slate-800 leading-snug flex-1">
                                        <span class="font-extrabold text-slate-900 mr-1" x-text="abjad + '.'"></span>
                                        <span x-html="formatTpaContent(currentSoal['opsi_' + abjad.toLowerCase()])"></span>
                                    </div>
                                </label>
                            </template>
                        </template>
                    </div>
                </div>

            </div>
        </form>

    </main>

    {{-- BOTTOM STICKY NAVIGATION BAR (MOBILE THUMB FRIENDLY) --}}
    <div class="fixed bottom-0 inset-x-0 bg-white border-t border-slate-200/80 shadow-lg p-3 z-30">
        <div class="max-w-4xl mx-auto flex items-center justify-between gap-2">
            
            {{-- Tombol Sebelumnya --}}
            <button type="button" @click="prevQuestion()" :disabled="currentIdx === 0"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 transition-all disabled:opacity-30 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span class="hidden xs:inline">Sebelumnya</span>
            </button>

            {{-- Quick Info --}}
            <div class="text-center">
                <span class="text-xs font-extrabold text-slate-800" x-text="(currentIdx + 1) + ' / ' + totalSoal"></span>
                <p class="text-[10px] text-slate-400">Terjawab: <strong class="text-red-600" x-text="answeredCount"></strong></p>
            </div>

            {{-- Tombol Selanjutnya / Selesai Mapel --}}
            <div>
                <button type="button" x-show="currentIdx < totalSoal - 1" @click="nextQuestion()"
                    class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 shadow-sm transition-all">
                    <span>Selanjutnya</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>

                <button type="button" x-show="currentIdx === totalSoal - 1" @click="openConfirmModal()"
                    class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-md shadow-red-600/20 transition-all">
                    <span>Selesai {{ $kategori->nama }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL / DRAWER PALETTE NOMOR SOAL --}}
    <div x-show="showPalette" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="showPalette = false"></div>
            
            <div class="inline-block w-full max-w-sm p-5 text-left bg-white rounded-2xl shadow-xl transform transition-all relative z-10">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Daftar Nomor Soal</h3>
                        <p class="text-[11px] text-slate-500">{{ $kategori->nama }}</p>
                    </div>
                    <button type="button" @click="showPalette = false" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>

                <div class="grid grid-cols-5 gap-2.5 my-4 max-h-72 overflow-y-auto p-1">
                    <template x-for="(s, idx) in soals" :key="s.id">
                        <button type="button" @click="jumpTo(idx)"
                            class="h-10 text-xs font-bold rounded-xl border flex items-center justify-center transition-all"
                            :class="currentIdx === idx ? 'border-red-600 bg-red-600 text-white shadow-sm ring-2 ring-red-300' : (answers[s.id] ? 'border-emerald-300 bg-emerald-50 text-emerald-800 font-black' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100')">
                            <span x-text="idx + 1"></span>
                        </button>
                    </template>
                </div>

                <div class="flex items-center justify-between text-[11px] text-slate-500 pt-3 border-t border-slate-100">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-emerald-500"></span> Sudah dijawab</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-slate-200"></span> Belum dijawab</span>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI SELESAI MAPEL --}}
    <div x-show="showConfirm" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="showConfirm = false"></div>
            
            <div class="inline-block w-full max-w-md p-6 text-left bg-white rounded-2xl shadow-2xl transform transition-all relative z-10 space-y-4">
                <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>

                <div class="text-center">
                    <h3 class="text-base font-bold text-slate-900">Selesaikan Ujian {{ $kategori->nama }}?</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Anda telah menjawab <strong class="text-slate-800" x-text="answeredCount"></strong> dari <strong class="text-slate-800" x-text="totalSoal"></strong> butir soal.
                        <template x-if="answeredCount < totalSoal">
                            <span class="text-rose-600 block font-semibold mt-1">
                                Peringatan: Masih terdapat <span x-text="totalSoal - answeredCount"></span> butir soal yang belum dijawab!
                            </span>
                        </template>
                    </p>
                    <p class="text-[11px] text-slate-400 mt-2">
                        Jawaban akan disimpan permanen dan Anda akan diarahkan ke mata pelajaran berikutnya.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="showConfirm = false"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                        Periksa Kembali
                    </button>
                    <button type="button" @click="submitFinal()"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-md shadow-red-600/20 transition-all">
                        Ya, Selesaikan
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function formatTpaContent(text) {
            if (!text) return '';
            let res = String(text).replace(/\\r\\n|\\n|\\r/g, "\n");
            if (!/<[a-z][\s\S]*>/i.test(res)) {
                res = res.replace(/\r\n|\n|\r/g, '<br>');
            }
            return res;
        }

        function tpaExamEngine(soalList, existingAnswers, initialSeconds) {
            return {
                soals: soalList,
                answers: existingAnswers || {},
                currentIdx: 0,
                timerSeconds: initialSeconds,
                showPalette: false,
                showConfirm: false,
                timerInterval: null,

                init() {
                    this.startTimer();
                },

                get totalSoal() {
                    return this.soals.length;
                },

                get currentSoal() {
                    return this.soals[this.currentIdx] || {};
                },

                get answeredCount() {
                    let count = 0;
                    for (let id in this.answers) {
                        if (this.answers[id] !== null && this.answers[id] !== '') {
                            count++;
                        }
                    }
                    return count;
                },

                selectAnswer(soalId, val) {
                    this.answers[soalId] = val;
                },

                prevQuestion() {
                    if (this.currentIdx > 0) {
                        this.currentIdx--;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                nextQuestion() {
                    if (this.currentIdx < this.totalSoal - 1) {
                        this.currentIdx++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                jumpTo(idx) {
                    this.currentIdx = idx;
                    this.showPalette = false;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                openConfirmModal() {
                    this.showConfirm = true;
                },

                submitFinal() {
                    if (this.timerInterval) {
                        clearInterval(this.timerInterval);
                    }
                    document.getElementById('tpaForm').submit();
                },

                startTimer() {
                    this.timerInterval = setInterval(() => {
                        if (this.timerSeconds > 0) {
                            this.timerSeconds--;
                        } else {
                            clearInterval(this.timerInterval);
                            alert('Waktu ujian telah habis! Jawaban Anda akan otomatis dikirim.');
                            this.submitFinal();
                        }
                    }, 1000);
                },

                formatTimer() {
                    let m = Math.floor(this.timerSeconds / 60);
                    let s = this.timerSeconds % 60;
                    return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
                }
            }
        }
    </script>
    @endpush

</body>
</html>
