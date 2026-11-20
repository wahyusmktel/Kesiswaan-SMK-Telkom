<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Ujian Kompetensi Keahlian (UKK)</h2>
                <p class="text-xs text-gray-500 mt-0.5">Kerjakan soal esai penilaian pengetahuan UKK secara mandiri.</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                Tahun Ajaran Aktif
            </span>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Banner --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-blue-700 via-indigo-700 to-violet-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl">
                <div class="relative z-10 max-w-2xl">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-bold uppercase tracking-wider text-blue-100 mb-3">
                        🎓 Lembar Pengetahuan UKK
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black tracking-tight">Halo, {{ $siswa->nama_lengkap }}!</h3>
                    <p class="text-blue-100 text-sm mt-2 leading-relaxed">
                        Silakan jawab soal esai UKK di bawah ini dengan sungguh-sungguh. Jawaban kamu akan diperiksa dan dikoreksi langsung oleh Penguji UKK sekolah.
                    </p>
                </div>
                <div class="absolute -right-8 -bottom-10 opacity-15 text-9xl select-none pointer-events-none">
                    📝
                </div>
            </div>

            {{-- Exam Cards List --}}
            @if($ujians->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center border border-gray-200/80 shadow-sm">
                    <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 rounded-2xl flex items-center justify-center text-3xl">
                        📋
                    </div>
                    <h4 class="text-lg font-bold text-gray-800">Belum Ada Ujian UKK yang Tersedia</h4>
                    <p class="text-sm text-gray-500 mt-1 max-w-md mx-auto">
                        Saat ini belum ada jadwal Ujian UKK yang ditugaskan untuk rombel kelas kamu. Silakan hubungi Kaprodi untuk informasi lebih lanjut.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($ujians as $ujian)
                    @php
                        $isComplete = $ujian->total_soal > 0 && $ujian->soal_dijawab >= $ujian->total_soal;
                        $isGraded   = $ujian->total_soal > 0 && $ujian->soal_dinilai >= $ujian->total_soal;
                        $pctDijawab = $ujian->total_soal > 0 ? round($ujian->soal_dijawab / $ujian->total_soal * 100) : 0;
                    @endphp
                    <div class="bg-white rounded-3xl border border-gray-200/80 hover:border-indigo-300 shadow-sm hover:shadow-md transition-all p-6 flex flex-col justify-between">
                        <div>
                            {{-- Top Header Info --}}
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div>
                                    <span class="inline-block px-2.5 py-0.5 rounded-lg text-[11px] font-black bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $ujian->jurusan }}
                                    </span>
                                    @if($ujian->tahunPelajaran)
                                        <span class="inline-block px-2.5 py-0.5 rounded-lg text-[11px] font-semibold bg-gray-100 text-gray-600">
                                            TP {{ $ujian->tahunPelajaran->tahun }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Status Badge --}}
                                @if($isGraded)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ✓ Sudah Dinilai
                                    </span>
                                @elseif($isComplete)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        ⏳ Menunggu Penilaian
                                    </span>
                                @elseif($ujian->soal_dijawab > 0)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        ✍️ Sedang Dikerjakan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                        Belum Dikerjakan
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg font-black text-gray-900 leading-snug">
                                {{ $ujian->nama_ujian }}
                            </h3>

                            @if($ujian->nama_project)
                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                                    <span class="font-semibold text-gray-700">Project:</span> {{ $ujian->nama_project }}
                                </p>
                            @endif

                            @if($ujian->tanggal_pelaksanaan)
                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                                    <span class="font-semibold text-gray-700">Pelaksanaan:</span>
                                    {{ \Carbon\Carbon::parse($ujian->tanggal_pelaksanaan)->isoFormat('D MMMM Y') }}
                                </p>
                            @endif

                            {{-- Progress Pengerjaan --}}
                            <div class="mt-5 p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                    <span class="text-gray-600">Progres Jawaban Soal Esai</span>
                                    <span class="text-indigo-600">{{ $ujian->soal_dijawab }} / {{ $ujian->total_soal }} Soal ({{ $pctDijawab }}%)</span>
                                </div>
                                <div class="w-full h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full transition-all duration-500" style="width: {{ $pctDijawab }}%"></div>
                                </div>
                                @if($isGraded && $ujian->skor_p !== null)
                                    <div class="mt-2.5 pt-2 border-t border-slate-200/80 flex items-center justify-between text-xs">
                                        <span class="text-gray-500 font-medium">Skor Pengetahuan Akhir:</span>
                                        <span class="font-black text-emerald-600 text-sm">{{ $ujian->skor_p }} / 100</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-gray-400">
                                {{ $ujian->instrumens->count() }} Instrumen Penilaian
                            </span>
                            <a href="{{ route('siswa.ukk.show', $ujian->id) }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm">
                                <span>{{ $isGraded ? 'Lihat Hasil & Jawaban' : ($ujian->soal_dijawab > 0 ? 'Lanjutkan Pengerjaan' : 'Mulai Kerjakan') }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
