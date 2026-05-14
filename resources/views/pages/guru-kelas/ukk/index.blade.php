<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Penilaian UKK</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- Page Header --}}
            <div class="bg-gradient-to-r from-orange-500 to-amber-600 rounded-2xl p-6 mb-6 text-white shadow-xl">
                <h3 class="text-xl font-black">📋 Daftar UKK yang Harus Dinilai</h3>
                <p class="text-orange-100 text-sm mt-1">Anda terdaftar sebagai penguji pada ujian berikut</p>
            </div>

            @forelse($ujians as $ujian)
                @php
                    $pct     = $ujian->total_siswa > 0 ? round($ujian->sudah_dinilai / $ujian->total_siswa * 100) : 0;
                    $selesai = $ujian->sudah_dinilai >= $ujian->total_siswa && $ujian->total_siswa > 0;
                @endphp
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-4 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-5">

                        {{-- Status badge --}}
                        <div class="flex-shrink-0 w-14 h-14 rounded-2xl flex items-center justify-center text-2xl
                            {{ $selesai ? 'bg-emerald-100' : 'bg-orange-100' }}">
                            {{ $selesai ? '✅' : '📝' }}
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-black text-gray-900 text-base">{{ $ujian->nama_ujian }}</span>
                                <span class="px-2 py-0.5 rounded-lg bg-orange-50 text-orange-700 text-xs font-bold border border-orange-100">
                                    {{ $ujian->jurusan }}
                                </span>
                                @if($selesai)
                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                                        ✓ Selesai
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 mb-3">
                                {{ $ujian->tahunPelajaran?->tahun ?? '—' }}
                                @if($ujian->tanggal_pelaksanaan)
                                    &middot; 📅 {{ \Carbon\Carbon::parse($ujian->tanggal_pelaksanaan)->isoFormat('D MMMM Y') }}
                                @endif
                            </p>

                            {{-- Progress bar --}}
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500
                                        {{ $selesai ? 'bg-emerald-500' : 'bg-orange-400' }}"
                                        style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-xs font-bold {{ $selesai ? 'text-emerald-600' : 'text-orange-600' }} shrink-0">
                                    {{ $ujian->sudah_dinilai }}/{{ $ujian->total_siswa }} siswa
                                </span>
                            </div>
                        </div>

                        {{-- Action --}}
                        <div class="shrink-0">
                            <a href="{{ route('guru-kelas.penilaian-ukk.show', $ujian->id) }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm
                                {{ $selesai
                                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-600 hover:text-white hover:border-emerald-600'
                                    : 'bg-orange-500 text-white hover:bg-orange-400' }}">
                                {{ $selesai ? '📊 Lihat Hasil' : '▶ Mulai Menilai' }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-16 text-center">
                    <p class="text-5xl mb-4">📋</p>
                    <p class="text-gray-500 font-semibold">Anda belum ditugaskan sebagai penguji UKK manapun.</p>
                    <p class="text-gray-400 text-sm mt-1">Hubungi Kaprodi untuk informasi lebih lanjut.</p>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
