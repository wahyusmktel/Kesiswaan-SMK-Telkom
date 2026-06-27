<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Review Jurnal Prakerin</h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $penempatan->siswa?->nama_lengkap ?? '-' }}</h3>
                    <p class="text-sm text-gray-500">{{ $penempatan->industri?->nama_industri ?? '-' }} - {{ $penempatan->rombelPkl?->nama_rombel ?? '-' }}</p>
                </div>
                <a href="{{ route('pembimbing-prakerin.monitoring.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-sm text-blue-900">
                <p class="font-bold">Panduan review</p>
                <p class="mt-1">Baca kegiatan dan kompetensi yang ditulis siswa, lalu beri catatan singkat jika perlu. Klik Sudah Ditinjau untuk menandai jurnal valid dan sudah dipantau oleh pembimbing sekolah.</p>
            </div>

            <div class="space-y-4">
                @forelse($jurnals as $jurnal)
                    @php
                        $statusClass = [
                            'menunggu' => 'bg-yellow-100 text-yellow-800',
                            'disetujui' => 'bg-emerald-100 text-emerald-800',
                            'revisi' => 'bg-red-100 text-red-800',
                        ][$jurnal->status_verifikasi] ?? 'bg-gray-100 text-gray-700';
                        $statusLabel = $jurnal->status_verifikasi === 'disetujui' ? 'Sudah Ditinjau' : Str::title($jurnal->status_verifikasi);
                    @endphp
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6" x-data="{ open: {{ $jurnal->status_verifikasi === 'menunggu' ? 'true' : 'false' }} }">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-bold text-lg text-gray-900">{{ \Carbon\Carbon::parse($jurnal->tanggal)->isoFormat('dddd, D MMMM Y') }}</p>
                                <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                                @if($jurnal->reviewed_at)
                                    <p class="mt-2 text-xs text-gray-500">Ditinjau pada {{ $jurnal->reviewed_at->format('d M Y H:i') }}</p>
                                @endif
                            </div>
                            <button type="button" @click="open = !open" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                <span x-show="!open">Review</span>
                                <span x-show="open">Tutup</span>
                            </button>
                        </div>

                        <div class="mt-5 grid gap-4 lg:grid-cols-2">
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-sm font-bold text-gray-900">Kegiatan</p>
                                <p class="mt-2 text-sm text-gray-700">{{ $jurnal->kegiatan_dilakukan }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-sm font-bold text-gray-900">Kompetensi</p>
                                <p class="mt-2 text-sm text-gray-700">{{ $jurnal->kompetensi_yang_didapat }}</p>
                            </div>
                        </div>

                        @if ($jurnal->foto_kegiatan)
                            <a href="{{ Storage::url($jurnal->foto_kegiatan) }}" target="_blank" class="mt-4 inline-flex rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50">Lihat Foto Kegiatan</a>
                        @endif

                        <div x-cloak x-show="open" x-collapse class="mt-5 border-t border-gray-100 pt-5">
                            <form method="POST" action="{{ route('pembimbing-prakerin.monitoring.updateJurnal', $jurnal) }}" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <label class="block space-y-1">
                                    <span class="text-sm font-semibold text-gray-700">Catatan pembimbing</span>
                                    <textarea name="catatan_pembimbing" rows="3" class="w-full rounded-xl border-gray-200" placeholder="Tulis apresiasi, arahan, atau catatan revisi untuk siswa.">{{ $jurnal->catatan_pembimbing }}</textarea>
                                </label>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <button name="status_verifikasi" value="revisi" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Minta Revisi</button>
                                    <button name="status_verifikasi" value="disetujui" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Sudah Ditinjau</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="rounded-2xl bg-white p-10 text-center text-gray-500 shadow-sm">Siswa ini belum mengisi jurnal.</p>
                @endforelse
            </div>

            <div>{{ $jurnals->links() }}</div>
        </div>
    </div>
</x-app-layout>
