<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('master-data.rombel.index') }}"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                title="Kembali ke Data Rombel">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Kenaikan Kelas & Kelulusan</h2>
                <p class="text-sm text-gray-500">Pindahkan siswa ke tingkat berikutnya tanpa menghapus riwayat rombel.</p>
            </div>
        </div>
    </x-slot>

    <div class="w-full py-6">
        <div class="w-full space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="font-bold text-gray-900">Persiapan Kenaikan Kelas</h3>
                        <p class="mt-1 text-sm text-gray-500">Pilih semester Genap terakhir sebagai sumber. Semester tujuan selalu mengikuti tahun pelajaran aktif.</p>
                    </div>

                    <div class="grid gap-4 border-b border-gray-200 bg-gray-50 px-6 py-5 md:grid-cols-2">
                        <form method="GET" action="{{ route('master-data.rombel.promotion.index') }}" class="space-y-2">
                            <label for="source_tahun_pelajaran_id" class="text-sm font-semibold text-gray-700">Semester sumber</label>
                            <div class="flex gap-2">
                                <select id="source_tahun_pelajaran_id" name="source_tahun_pelajaran_id"
                                    class="block min-w-0 flex-1 rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                    @foreach ($sources as $period)
                                        <option value="{{ $period->id }}" @selected($source?->id === $period->id)>
                                            {{ $period->tahun }} - {{ $period->semester }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                                    Tampilkan
                                </button>
                            </div>
                        </form>

                        <div class="space-y-2">
                            <span class="text-sm font-semibold text-gray-700">Semester tujuan aktif</span>
                            <div class="flex h-[42px] items-center rounded-lg border px-3 text-sm font-semibold {{ $target ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-700' }}">
                                {{ $target ? $target->tahun . ' - ' . $target->semester : 'Belum ada tahun pelajaran aktif' }}
                            </div>
                        </div>
                    </div>

                    @if ($previewError)
                        <div class="m-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ $previewError }}</div>
                    @elseif (!$source || !$target)
                        <div class="px-6 py-12 text-center text-sm text-gray-500">Lengkapi semester sumber dan aktifkan semester tujuan untuk melihat pratinjau.</div>
                    @elseif ($preview)
                        @if ($preview['errors']->isNotEmpty())
                            <div class="m-6 rounded-lg border border-amber-200 bg-amber-50 p-4">
                                <p class="text-sm font-bold text-amber-900">Data belum siap diproses</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-amber-800">
                                    @foreach ($preview['errors'] as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-600">
                                    <tr>
                                        <th class="px-6 py-3 font-bold">Rombel Sumber</th>
                                        <th class="px-6 py-3 font-bold">Proses</th>
                                        <th class="px-6 py-3 text-center font-bold">Siswa</th>
                                        <th class="px-6 py-3 font-bold">Rombel Tujuan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($preview['rows'] as $row)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-gray-900">{{ $row['source']->kelas?->nama_kelas }}</div>
                                                <div class="text-xs text-gray-500">{{ $row['source']->waliKelas?->name ?? 'Tanpa wali kelas' }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $row['target_class'] ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700' }}">
                                                    {{ $row['action'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center font-bold text-gray-900">{{ $row['student_count'] }}</td>
                                            <td class="px-6 py-4 text-gray-600">
                                                @if ($row['target_class'])
                                                    {{ $row['target_rombel'] ? 'Rombel tersedia' : 'Akan dibuat otomatis' }}
                                                @else
                                                    Masuk arsip alumni
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">Belum ada rombel pada semester sumber.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <aside class="space-y-5">
                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-5">
                        <div class="flex gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-blue-900">
                                <p class="font-bold">Apa yang akan terjadi?</p>
                                <p class="mt-2 leading-6">Kelas X naik ke XI, kelas XI naik ke XII, dan kelas XII menjadi alumni. Rombel kelas X pada tahun baru dibuat kosong untuk siswa baru. Riwayat rombel lama tetap tersimpan.</p>
                                <p class="mt-2 leading-6">Wali kelas lama disalin sebagai nilai awal pada rombel baru dan dapat diperbarui setelah proses selesai.</p>
                            </div>
                        </div>
                    </div>

                    @if ($preview)
                        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                            <h3 class="font-bold text-gray-900">Ringkasan Proses</h3>
                            <dl class="mt-4 space-y-3 text-sm">
                                <div class="flex justify-between"><dt class="text-gray-500">Naik kelas</dt><dd class="font-bold text-gray-900">{{ $preview['promoted_count'] }} siswa</dd></div>
                                <div class="flex justify-between"><dt class="text-gray-500">Menjadi alumni</dt><dd class="font-bold text-gray-900">{{ $preview['graduated_count'] }} siswa</dd></div>
                                <div class="flex justify-between"><dt class="text-gray-500">Rombel baru dibuat</dt><dd class="font-bold text-gray-900">{{ $preview['rombel_to_create_count'] }}</dd></div>
                            </dl>

                            @if ($preview['already_processed'])
                                <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm font-semibold text-green-800">
                                    Sudah diproses {{ $preview['already_processed']->created_at->translatedFormat('d F Y H:i') }}.
                                </div>
                            @else
                                <form id="promotion-form" method="POST" action="{{ route('master-data.rombel.promotion.store') }}" class="mt-5">
                                    @csrf
                                    <input type="hidden" name="source_tahun_pelajaran_id" value="{{ $source?->id }}">
                                    <button type="button" onclick="confirmPromotion()"
                                        @disabled($preview['errors']->isNotEmpty() || $preview['rows']->isEmpty())
                                        class="w-full rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-500 disabled:cursor-not-allowed disabled:bg-gray-300">
                                        Proses Kenaikan Kelas
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </aside>
            </div>

            <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4"><h3 class="font-bold text-gray-900">Riwayat Proses</h3></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-600">
                            <tr>
                                <th class="px-6 py-3">Waktu</th><th class="px-6 py-3">Periode</th><th class="px-6 py-3">Naik</th><th class="px-6 py-3">Lulus</th><th class="px-6 py-3">Diproses Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($history as $item)
                                <tr>
                                    <td class="px-6 py-4 text-gray-600">{{ $item->created_at->translatedFormat('d M Y H:i') }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->sourceTahunPelajaran?->tahun }} Genap → {{ $item->targetTahunPelajaran?->tahun }} Ganjil</td>
                                    <td class="px-6 py-4">{{ $item->promoted_count }} siswa</td>
                                    <td class="px-6 py-4">{{ $item->graduated_count }} siswa</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $item->processor?->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada proses kenaikan kelas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 px-6 py-3">{{ $history->withQueryString()->links() }}</div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmPromotion() {
                Swal.fire({
                    title: 'Proses kenaikan kelas?',
                    text: 'Pastikan pratinjau sudah benar. Proses ini hanya dapat dijalankan satu kali untuk periode yang sama.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, proses sekarang',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) document.getElementById('promotion-form').submit();
                });
            }
        </script>
    @endpush
</x-app-layout>
