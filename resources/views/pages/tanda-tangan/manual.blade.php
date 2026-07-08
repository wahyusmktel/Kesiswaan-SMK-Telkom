<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Tanda Tangani Dokumen PDF Manual</h2>
                <p class="text-sm text-gray-500 mt-1">Upload PDF, tentukan posisi QR, lalu arsipkan dokumen bertanda tangan digital.</p>
            </div>
            <a href="{{ route('tanda-tangan.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-3 text-sm font-semibold text-green-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-semibold text-red-800">{{ session('error') }}</div>
            @endif

            @unless($signature && $signature->isReady())
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-800">
                    Setup PIN tanda tangan digital terlebih dahulu sebelum menandatangani dokumen PDF manual.
                </div>
            @endunless

            <div class="grid grid-cols-1 xl:grid-cols-[420px_1fr] gap-6" x-data="manualPdfSigner()">
                <div class="space-y-6">
                    <form action="{{ route('tanda-tangan.manual.store') }}" method="POST" enctype="multipart/form-data" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-5">
                        @csrf
                        <div>
                            <h3 class="text-lg font-black text-gray-900">Upload & Posisi QR</h3>
                            <p class="mt-1 text-sm text-gray-500">PDF maksimal 50 MB. Dokumen multi halaman didukung, pilih halaman yang akan diberi QR.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Judul Dokumen</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Surat Keputusan...">
                            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">File PDF</label>
                            <input type="file" name="pdf_file" accept="application/pdf,.pdf" required @change="loadPdf($event)"
                                class="w-full rounded-xl border border-gray-200 p-3 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-indigo-700">
                            @error('pdf_file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Halaman QR</label>
                                <input type="number" name="signed_page" min="1" x-model.number="page" value="{{ old('signed_page', 1) }}" required class="w-full rounded-xl border-gray-200 text-sm">
                                @error('signed_page') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Ukuran QR (mm)</label>
                                <input type="number" name="qr_size_mm" min="18" max="60" step="1" x-model.number="size" value="{{ old('qr_size_mm', 28) }}" required class="w-full rounded-xl border-gray-200 text-sm">
                                @error('qr_size_mm') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-4 rounded-2xl bg-gray-50 p-4">
                            <div>
                                <div class="flex items-center justify-between text-xs font-black uppercase tracking-widest text-gray-500">
                                    <span>Posisi X dari kiri</span><span x-text="x + ' mm'"></span>
                                </div>
                                <input type="range" min="0" max="210" step="1" x-model.number="x" class="mt-2 w-full">
                                <input type="hidden" name="qr_x_mm" :value="x">
                            </div>
                            <div>
                                <div class="flex items-center justify-between text-xs font-black uppercase tracking-widest text-gray-500">
                                    <span>Posisi Y dari atas</span><span x-text="y + ' mm'"></span>
                                </div>
                                <input type="range" min="0" max="297" step="1" x-model.number="y" class="mt-2 w-full">
                                <input type="hidden" name="qr_y_mm" :value="y">
                            </div>
                            <p class="text-xs font-semibold text-gray-500">Koordinat memakai satuan milimeter dari pojok kiri atas halaman. Sistem otomatis membatasi QR agar tetap berada di dalam halaman PDF.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">PIN Tanda Tangan Digital</label>
                            <input type="password" name="pin" inputmode="numeric" maxlength="8" required class="w-full rounded-xl border-gray-200 text-sm font-mono tracking-widest" placeholder="••••••">
                            @error('pin') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button @disabled(!($signature && $signature->isReady())) class="w-full rounded-xl bg-indigo-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-indigo-100 hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                            Tanda Tangani & Arsipkan
                        </button>
                    </form>

                    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-sm text-blue-900">
                        <p class="font-black">Panduan cepat</p>
                        <p class="mt-1 leading-6">Gunakan preview di kanan untuk melihat dokumen. Atur halaman, posisi X/Y, dan ukuran QR. Setelah diproses, QR akan mengarah ke halaman verifikasi online resmi aplikasi.</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-black text-gray-900">Preview PDF</p>
                            <p class="text-xs text-gray-500">Preview dari browser. Kotak QR adalah estimasi posisi.</p>
                        </div>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-600" x-text="'Hal. ' + page"></span>
                    </div>
                    <div class="relative h-[760px] overflow-hidden rounded-xl bg-gray-100">
                        <template x-if="pdfUrl">
                            <iframe :src="pdfUrl + '#page=' + page" class="h-full w-full border-0"></iframe>
                        </template>
                        <template x-if="!pdfUrl">
                            <div class="flex h-full items-center justify-center text-center text-sm font-semibold text-gray-400">
                                Pilih file PDF untuk melihat preview.
                            </div>
                        </template>
                        <div class="pointer-events-none absolute rounded-lg border-2 border-indigo-600 bg-indigo-600/10"
                            :style="markerStyle()">
                            <div class="grid h-full w-full place-items-center text-[10px] font-black text-indigo-700">QR</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-black text-gray-900">Arsip Dokumen PDF Manual</h3>
                    <p class="text-sm text-gray-500 mt-1">Dokumen yang sudah diberi QR digital dapat diunduh ulang dan diverifikasi online.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400">Dokumen</th>
                                <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400">Posisi</th>
                                <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-widest text-gray-400">Token</th>
                                <th class="px-6 py-3 text-right text-xs font-black uppercase tracking-widest text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($documents as $document)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-gray-900">{{ $document->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $document->original_file_name }} · {{ $document->page_count }} halaman · {{ number_format($document->file_size / 1024 / 1024, 2) }} MB</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        Hal. {{ $document->signed_page }} · X {{ $document->qr_x_mm }}mm · Y {{ $document->qr_y_mm }}mm
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($document->digitalDocument)
                                            <a href="{{ route('verifikasi.dokumen', $document->digitalDocument->token) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                                {{ $document->digitalDocument->token }}
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('tanda-tangan.manual.download', $document) }}" class="inline-flex rounded-xl bg-gray-900 px-4 py-2 text-xs font-black text-white hover:bg-black">
                                            Unduh PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-sm font-semibold text-gray-400">Belum ada dokumen manual yang ditandatangani.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100">{{ $documents->links() }}</div>
            </div>
        </div>
    </div>

    <script>
        function manualPdfSigner() {
            return {
                pdfUrl: null,
                page: Number(@js(old('signed_page', 1))),
                x: Number(@js(old('qr_x_mm', 15))),
                y: Number(@js(old('qr_y_mm', 15))),
                size: Number(@js(old('qr_size_mm', 28))),
                loadPdf(event) {
                    const file = event.target.files?.[0];
                    if (!file) return;
                    if (this.pdfUrl) URL.revokeObjectURL(this.pdfUrl);
                    this.pdfUrl = URL.createObjectURL(file);
                },
                markerStyle() {
                    const scaleX = 100 / 210;
                    const scaleY = 100 / 297;
                    return `left:${this.x * scaleX}%;top:${this.y * scaleY}%;width:${this.size * scaleX}%;height:${this.size * scaleY}%;`;
                },
            }
        }
    </script>
</x-app-layout>
