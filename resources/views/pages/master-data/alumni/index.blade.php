<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Data Alumni</h2>
            <p class="text-sm text-gray-500">Arsip siswa yang telah lulus dan tidak lagi ditampilkan pada Data Siswa aktif.</p>
        </div>
    </x-slot>

    <div class="w-full py-6">
        <div class="w-full space-y-5 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">{{ session('success') }}</div>
            @endif

            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-900">
                Alumni tercatat otomatis ketika kelas XII diluluskan melalui fitur Kenaikan Kelas. Gunakan <strong>Kembalikan ke Siswa Aktif</strong> hanya untuk memperbaiki kesalahan kelulusan, lalu tempatkan kembali siswa melalui menu Kelola Siswa pada rombel aktif.
            </div>

            <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <form method="GET" action="{{ route('master-data.alumni.index') }}" class="grid gap-3 md:grid-cols-[minmax(240px,1fr)_280px_auto]">
                        <div class="relative">
                            <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, atau NISN..."
                                class="w-full rounded-lg border-gray-300 py-2 pl-10 text-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <select name="graduation_tahun_pelajaran_id" class="rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            <option value="">Semua tahun kelulusan</option>
                            @foreach ($graduationPeriods as $period)
                                <option value="{{ $period->id }}" @selected((string) request('graduation_tahun_pelajaran_id') === (string) $period->id)>
                                    {{ $period->tahun }} - {{ $period->semester }}
                                </option>
                            @endforeach
                        </select>
                        <button class="rounded-lg bg-gray-900 px-5 py-2 text-sm font-bold text-white hover:bg-gray-700">Terapkan</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-600">
                            <tr>
                                <th class="px-6 py-4 font-bold">NIS / NISN</th>
                                <th class="px-6 py-4 font-bold">Nama Alumni</th>
                                <th class="px-6 py-4 font-bold">Rombel Terakhir</th>
                                <th class="px-6 py-4 font-bold">Tahun Lulus</th>
                                <th class="px-6 py-4 font-bold">Tanggal Lulus</th>
                                <th class="px-6 py-4 text-right font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($alumni as $item)
                                @php
                                    $lastRombel = $item->rombels->first();
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-mono font-semibold text-gray-900">{{ $item->nis }}</div>
                                        <div class="text-xs text-gray-500">NISN {{ $item->dapodik?->nisn ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $item->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">{{ $lastRombel?->kelas?->nama_kelas ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-bold text-green-700">
                                            {{ $item->graduationTahunPelajaran?->tahun ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $item->graduated_at?->translatedFormat('d F Y') ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button type="button"
                                                @click="$dispatch('edit-alumni', {{ Illuminate\Support\Js::from([
                                                    'name' => $item->nama_lengkap,
                                                    'graduated_at' => $item->graduated_at?->format('Y-m-d'),
                                                    'period_id' => $item->graduation_tahun_pelajaran_id,
                                                    'notes' => $item->graduation_notes,
                                                    'url' => route('master-data.alumni.update', $item),
                                                ]) }})"
                                                class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-100">Edit</button>
                                            <form method="POST" action="{{ route('master-data.alumni.reactivate', $item) }}" class="reactivate-form">
                                                @csrf @method('PATCH')
                                                <button type="button" onclick="confirmReactivate(this)"
                                                    class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700 hover:bg-amber-100">
                                                    Aktifkan Kembali
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-14 text-center text-gray-500">Data alumni tidak ditemukan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 px-6 py-3">{{ $alumni->withQueryString()->links() }}</div>
            </section>
        </div>
    </div>

    <div x-data="{ open: false, form: {} }" @edit-alumni.window="form = $event.detail; open = true" x-show="open" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg overflow-hidden rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <div><h3 class="font-bold text-gray-900">Perbarui Data Alumni</h3><p class="text-sm text-gray-500" x-text="form.name"></p></div>
                    <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-700" aria-label="Tutup">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form :action="form.url" method="POST">
                    @csrf @method('PUT')
                    <div class="space-y-4 p-6">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-gray-700">Tanggal Kelulusan</label>
                            <input type="date" name="graduated_at" x-model="form.graduated_at" required class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-gray-700">Tahun Pelajaran Kelulusan</label>
                            <select name="graduation_tahun_pelajaran_id" x-model="form.period_id" required class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                @foreach ($graduationPeriods as $period)
                                    <option value="{{ $period->id }}">{{ $period->tahun }} - {{ $period->semester }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-gray-700">Catatan</label>
                            <textarea name="graduation_notes" x-model="form.notes" rows="4" maxlength="2000" class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Catatan kelulusan (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50 px-6 py-4">
                        <button type="button" @click="open = false" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-100">Batal</button>
                        <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-500">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmReactivate(button) {
                Swal.fire({
                    title: 'Kembalikan menjadi siswa aktif?',
                    text: 'Data kelulusan akan dikosongkan. Setelah itu siswa harus dimasukkan ke rombel aktif.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, aktifkan kembali',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) button.closest('form').submit();
                });
            }
        </script>
    @endpush
</x-app-layout>
