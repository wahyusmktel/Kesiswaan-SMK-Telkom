<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Bimbingan Laporan Prakerin</h2>
    </x-slot>

    @php($isStudent = auth()->user()->masterSiswa !== null)

    <div class="py-8" x-data="{ previewOpen: false, previewUrl: '', previewTitle: '' }">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-2xl border border-purple-100 bg-purple-50 p-5 text-sm text-purple-900">
                <p class="font-bold">Panduan bimbingan laporan</p>
                <p class="mt-1">{{ $isStudent ? 'Unggah draf laporan PKL agar pembimbing internal dapat memberi catatan revisi atau menandai laporan sudah ditinjau.' : 'Tinjau draf laporan siswa, beri catatan yang jelas, dan ubah status agar siswa tahu langkah perbaikan berikutnya.' }}</p>
            </div>

            @if($isStudent && $penempatan)
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-900">Ajukan Draf Laporan</h3>
                    <form method="POST" action="{{ route('siswa.prakerin-laporan.store') }}" enctype="multipart/form-data" class="mt-4 grid gap-4 lg:grid-cols-[1fr_1fr_auto]">
                        @csrf
                        <input name="judul" class="rounded-xl border-gray-200" placeholder="Judul atau versi laporan" required>
                        <input type="file" name="file_laporan" accept=".pdf,.doc,.docx" class="rounded-xl border border-gray-200 px-3 py-2 text-sm">
                        <button class="rounded-xl bg-red-600 px-5 py-2 font-semibold text-white hover:bg-red-700">Kirim</button>
                    </form>
                    <p class="mt-2 text-xs text-gray-500">Format PDF/DOC/DOCX, maksimal 20 MB.</p>
                </div>
            @endif

            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900">Riwayat Bimbingan Laporan</h3>
                    <p class="text-sm text-gray-500">Pantau status dan catatan pembimbing untuk setiap draf laporan.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Laporan</th>
                                @unless($isStudent)<th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Siswa</th>@endunless
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Catatan</th>
                                @unless($isStudent)<th class="px-6 py-3 text-right text-xs font-bold uppercase text-gray-500">Review</th>@endunless
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($laporans as $laporan)
                                <tr class="align-top">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $laporan->judul }}</p>
                                        <p class="text-xs text-gray-500">{{ $laporan->created_at->format('d M Y H:i') }}</p>
                                        @if($laporan->file_path)
                                            @php
                                                $fileRoute = $isStudent
                                                    ? route('siswa.prakerin-laporan.file', $laporan)
                                                    : route('pembimbing-prakerin.laporan.file', $laporan);
                                            @endphp
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                <button
                                                    type="button"
                                                    @click="previewOpen = true; previewUrl = '{{ $fileRoute }}?preview=1'; previewTitle = @js($laporan->judul)"
                                                    class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                                    Preview
                                                </button>
                                                <a href="{{ $fileRoute }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200">Unduh file</a>
                                            </div>
                                        @endif
                                    </td>
                                    @unless($isStudent)
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <p class="font-semibold">{{ $laporan->penempatan?->siswa?->nama_lengkap ?? '-' }}</p>
                                            <p class="text-gray-500">{{ $laporan->penempatan?->rombelPkl?->nama_rombel ?? '-' }}</p>
                                        </td>
                                    @endunless
                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-bold uppercase {{ $laporan->status === 'ditinjau' ? 'bg-emerald-100 text-emerald-800' : ($laporan->status === 'revisi' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ $laporan->status }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $laporan->catatan_pembimbing ?? '-' }}</td>
                                    @unless($isStudent)
                                        <td class="px-6 py-4">
                                            <form method="POST" action="{{ route('pembimbing-prakerin.laporan.update', $laporan) }}" class="space-y-2">
                                                @csrf
                                                @method('PATCH')
                                                <textarea name="catatan_pembimbing" rows="2" class="w-full min-w-[260px] rounded-xl border-gray-200 text-sm" placeholder="Catatan pembimbing">{{ $laporan->catatan_pembimbing }}</textarea>
                                                <div class="flex justify-end gap-2">
                                                    <button name="status" value="revisi" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white">Revisi</button>
                                                    <button name="status" value="ditinjau" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white">Ditinjau</button>
                                                </div>
                                            </form>
                                        </td>
                                    @endunless
                                </tr>
                            @empty
                                <tr><td colspan="{{ $isStudent ? 3 : 5 }}" class="px-6 py-10 text-center text-gray-500">Belum ada data bimbingan laporan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($laporans, 'links'))
                    <div class="border-t border-gray-100 p-6">{{ $laporans->links() }}</div>
                @endif
            </div>
        </div>

        <div x-cloak x-show="previewOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4">
            <div @click.outside="previewOpen = false; previewUrl = ''" class="flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-6 py-4">
                    <div>
                        <h3 class="font-bold text-gray-900" x-text="previewTitle || 'Preview laporan'"></h3>
                        <p class="text-sm text-gray-500">PDF akan tampil langsung. File DOC/DOCX dapat tetap diunduh jika browser tidak mendukung preview.</p>
                    </div>
                    <button type="button" @click="previewOpen = false; previewUrl = ''" class="rounded-full p-2 text-gray-500 hover:bg-gray-100">x</button>
                </div>
                <iframe :src="previewUrl" class="h-[72vh] w-full border-0 bg-gray-50"></iframe>
                <div class="flex justify-end border-t border-gray-100 bg-gray-50 px-6 py-4">
                    <a :href="previewUrl.replace('?preview=1', '')" class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">Unduh</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
