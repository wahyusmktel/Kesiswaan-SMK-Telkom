<x-app-layout px="0">
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.24em] text-red-600">Super Admin</p>
                <h1 class="mt-1 text-3xl font-black text-slate-900">Repository Bahan Praktikum</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-600">Bagikan arsip, dokumen, installer, dan image disk melalui jalur LAN atau internet. File tersimpan privat dan hanya keluar sebagai unduhan.</p>
            </div>
            <div class="rounded-2xl border px-4 py-3 text-xs font-bold {{ config('repository.download_driver') === 'nginx' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-amber-200 bg-amber-50 text-amber-700' }}">
                Mode download: {{ config('repository.download_driver') === 'nginx' ? 'Nginx Direct' : 'Laravel Binary' }}
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
                <ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total File</p><p class="mt-2 text-3xl font-black text-slate-900">{{ number_format($stats['files']) }}</p></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Link Aktif</p><p class="mt-2 text-3xl font-black text-emerald-600">{{ number_format($stats['active']) }}</p></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Penyimpanan Terpakai</p><p class="mt-2 text-3xl font-black text-indigo-600">{{ $stats['human_bytes'] }}</p></div>
        </div>

        <div class="grid gap-6 lg:grid-cols-5">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-3">
                <div class="flex items-start justify-between gap-4">
                    <div><h2 class="text-lg font-black text-slate-900">Upload file besar</h2><p class="mt-1 text-sm text-slate-500">Upload dibagi otomatis menjadi chunk {{ number_format(config('repository.chunk_size') / 1048576, 0) }} MB dan retry jika jaringan terputus sesaat.</p></div>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-black text-indigo-700">Maks. {{ \App\Models\RepositoryFile::make(['size' => config('repository.max_file_size')])->human_size }}</span>
                </div>

                <form id="repositoryUploadForm" class="mt-6 space-y-4">
                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-600" for="repositoryFile">File</label>
                        <input id="repositoryFile" type="file" required class="mt-2 block w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:font-bold file:text-white">
                    </div>
                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-600" for="repositoryTitle">Judul</label>
                        <input id="repositoryTitle" type="text" required maxlength="255" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: VirtualBox dan Image Ubuntu TKJ">
                    </div>
                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-600" for="repositoryDescription">Keterangan</label>
                        <textarea id="repositoryDescription" rows="3" maxlength="2000" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Versi, kelas, atau petunjuk penggunaan (opsional)"></textarea>
                    </div>
                    <div id="uploadProgressWrap" class="hidden rounded-2xl bg-slate-50 p-4">
                        <div class="mb-2 flex justify-between text-xs font-bold text-slate-600"><span id="uploadStatus">Menyiapkan upload...</span><span id="uploadPercent">0%</span></div>
                        <div class="h-3 overflow-hidden rounded-full bg-slate-200"><div id="uploadProgress" class="h-full w-0 rounded-full bg-gradient-to-r from-red-600 to-indigo-600 transition-all"></div></div>
                    </div>
                    <div class="flex gap-3">
                        <button id="uploadButton" type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-red-200 hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50">Mulai Upload</button>
                        <button id="cancelUploadButton" type="button" class="hidden rounded-xl border border-slate-300 px-5 py-3 text-sm font-black text-slate-700 hover:bg-slate-50">Batalkan</button>
                    </div>
                </form>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-slate-900 p-6 text-white shadow-sm lg:col-span-2">
                <h2 class="text-lg font-black">Alamat Download</h2>
                <p class="mt-1 text-sm text-slate-300">Atur host yang mengarah ke server SISFO yang sama.</p>
                <form method="POST" action="{{ route('super-admin.repository.settings.update') }}" class="mt-6 space-y-4">
                    @csrf @method('PUT')
                    <div><label class="text-xs font-bold uppercase tracking-wider text-slate-300">URL lokal/LAN</label><input name="local_base_url" value="{{ old('local_base_url', $settings->local_base_url) }}" type="url" placeholder="http://192.168.1.10" class="mt-2 w-full rounded-xl border-slate-700 bg-slate-800 text-sm text-white placeholder:text-slate-500 focus:border-red-500 focus:ring-red-500"><p class="mt-1 text-[11px] text-slate-400">Gunakan IP lokal server yang dapat diakses komputer laboratorium.</p></div>
                    <div><label class="text-xs font-bold uppercase tracking-wider text-slate-300">URL publik</label><input name="public_base_url" value="{{ old('public_base_url', $settings->public_base_url ?: config('app.url')) }}" type="url" required placeholder="https://sisfo.smktelkom-lpg.id" class="mt-2 w-full rounded-xl border-slate-700 bg-slate-800 text-sm text-white placeholder:text-slate-500 focus:border-red-500 focus:ring-red-500"></div>
                    <button class="w-full rounded-xl bg-white px-5 py-3 text-sm font-black text-slate-900 hover:bg-slate-100">Simpan Alamat</button>
                </form>
                @if(!$settings->local_base_url)
                    <div class="mt-4 rounded-xl border border-amber-400/30 bg-amber-400/10 p-3 text-xs text-amber-200">URL LAN belum diisi. Link lokal baru tersedia setelah IP server disimpan.</div>
                @endif
            </section>
        </div>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-200 p-6 sm:flex-row sm:items-center sm:justify-between">
                <div><h2 class="text-lg font-black text-slate-900">File Repository</h2><p class="mt-1 text-sm text-slate-500">Salin link sesuai jaringan siswa.</p></div>
                <form method="GET" class="flex gap-2"><input name="search" value="{{ $search }}" placeholder="Cari file..." class="w-full rounded-xl border-slate-300 text-sm sm:w-64"><button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white">Cari</button></form>
            </div>

            @forelse($files as $file)
                <article class="border-b border-slate-100 p-6 last:border-b-0">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-black uppercase text-slate-600">{{ $file->extension ?: 'FILE' }}</span>
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-black {{ $file->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $file->is_active ? 'AKTIF' : 'NONAKTIF' }}</span>
                                <span class="text-xs font-bold text-slate-500">{{ $file->human_size }}</span>
                            </div>
                            <h3 class="mt-3 break-words text-lg font-black text-slate-900">{{ $file->title }}</h3>
                            <p class="mt-1 break-all text-xs text-slate-500">{{ $file->original_name }}</p>
                            @if($file->description)<p class="mt-3 max-w-3xl whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $file->description }}</p>@endif
                            <p class="mt-3 text-[11px] text-slate-400">Diunggah {{ $file->published_at?->format('d M Y H:i') }} oleh {{ $file->uploader?->name ?? 'Sistem' }}</p>
                        </div>

                        <div class="w-full space-y-3 xl:w-[420px]">
                            <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-3"><div class="mb-2 flex items-center justify-between"><span class="text-[11px] font-black uppercase text-indigo-700">Link Publik</span><button type="button" data-copy="{{ $links[$file->id]['public'] }}" class="copy-button text-xs font-black text-indigo-700">Salin</button></div><input readonly value="{{ $links[$file->id]['public'] }}" class="w-full rounded-lg border-indigo-200 bg-white text-xs text-slate-600"></div>
                            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3"><div class="mb-2 flex items-center justify-between"><span class="text-[11px] font-black uppercase text-emerald-700">Link Lokal/LAN</span>@if($links[$file->id]['local'])<button type="button" data-copy="{{ $links[$file->id]['local'] }}" class="copy-button text-xs font-black text-emerald-700">Salin</button>@endif</div><input readonly value="{{ $links[$file->id]['local'] ?? 'Atur URL lokal terlebih dahulu' }}" class="w-full rounded-lg border-emerald-200 bg-white text-xs text-slate-600"></div>
                            <div class="flex gap-2">
                                <details class="relative flex-1"><summary class="cursor-pointer list-none rounded-xl border border-slate-300 px-4 py-2.5 text-center text-xs font-black text-slate-700 hover:bg-slate-50">Edit</summary><form method="POST" action="{{ route('super-admin.repository.files.update', $file) }}" class="absolute right-0 z-20 mt-2 w-[min(90vw,380px)] space-y-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-2xl">@csrf @method('PATCH')<input name="title" value="{{ $file->title }}" required maxlength="255" class="w-full rounded-xl border-slate-300 text-sm"><textarea name="description" rows="3" maxlength="2000" class="w-full rounded-xl border-slate-300 text-sm">{{ $file->description }}</textarea><label class="flex items-center gap-2 text-sm font-bold text-slate-700"><input type="checkbox" name="is_active" value="1" @checked($file->is_active) class="rounded border-slate-300 text-red-600"> Link aktif</label><button class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white">Simpan</button></form></details>
                                <form method="POST" action="{{ route('super-admin.repository.files.destroy', $file) }}" onsubmit="return confirm('Hapus file ini secara permanen dari server?')">@csrf @method('DELETE')<button class="rounded-xl border border-red-200 px-4 py-2.5 text-xs font-black text-red-700 hover:bg-red-50">Hapus</button></form>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="p-12 text-center"><p class="text-base font-black text-slate-700">Belum ada file repository</p><p class="mt-1 text-sm text-slate-500">Upload bahan praktikum pertama melalui formulir di atas.</p></div>
            @endforelse

            @if($files->hasPages())<div class="border-t border-slate-200 p-5">{{ $files->links() }}</div>@endif
        </section>
    </div>

    @push('scripts')
    <script>
        (() => {
            const form = document.getElementById('repositoryUploadForm');
            const fileInput = document.getElementById('repositoryFile');
            const titleInput = document.getElementById('repositoryTitle');
            const descriptionInput = document.getElementById('repositoryDescription');
            const uploadButton = document.getElementById('uploadButton');
            const cancelButton = document.getElementById('cancelUploadButton');
            const progressWrap = document.getElementById('uploadProgressWrap');
            const progressBar = document.getElementById('uploadProgress');
            const progressText = document.getElementById('uploadPercent');
            const statusText = document.getElementById('uploadStatus');
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const initializeUrl = @json(route('super-admin.repository.uploads.initialize'));
            const chunkUrlTemplate = @json(route('super-admin.repository.uploads.chunk', ['upload' => '__UPLOAD__', 'index' => '__INDEX__']));
            const completeUrlTemplate = @json(route('super-admin.repository.uploads.complete', ['upload' => '__UPLOAD__']));
            const cancelUrlTemplate = @json(route('super-admin.repository.uploads.cancel', ['upload' => '__UPLOAD__']));
            let uploadId = null;
            let controller = null;

            fileInput.addEventListener('change', () => {
                const file = fileInput.files[0];
                if (file && !titleInput.value) titleInput.value = file.name.replace(/\.[^.]+$/, '');
            });

            document.querySelectorAll('.copy-button').forEach(button => button.addEventListener('click', async () => {
                await navigator.clipboard.writeText(button.dataset.copy);
                const original = button.textContent;
                button.textContent = 'Tersalin!';
                setTimeout(() => button.textContent = original, 1400);
            }));

            cancelButton.addEventListener('click', async () => {
                controller?.abort();
                if (uploadId) {
                    await fetch(cancelUrlTemplate.replace('__UPLOAD__', uploadId), { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf } }).catch(() => {});
                }
                resetUpload('Upload dibatalkan.');
            });

            form.addEventListener('submit', async event => {
                event.preventDefault();
                const file = fileInput.files[0];
                if (!file) return;

                controller = new AbortController();
                setBusy(true);

                try {
                    statusText.textContent = 'Membuat sesi upload...';
                    const initialization = await jsonFetch(initializeUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ title: titleInput.value, description: descriptionInput.value, original_name: file.name, size: file.size, mime_type: file.type }),
                        signal: controller.signal,
                    });

                    uploadId = initialization.upload_id;
                    for (let index = 0; index < initialization.total_chunks; index++) {
                        const start = index * initialization.chunk_size;
                        const chunk = file.slice(start, Math.min(start + initialization.chunk_size, file.size));
                        statusText.textContent = `Mengunggah bagian ${index + 1} dari ${initialization.total_chunks}...`;
                        await uploadChunkWithRetry(chunk, index);
                        const percent = Math.round(((index + 1) / initialization.total_chunks) * 100);
                        progressBar.style.width = `${percent}%`;
                        progressText.textContent = `${percent}%`;
                    }

                    statusText.textContent = 'Menerbitkan file...';
                    await jsonFetch(completeUrlTemplate.replace('__UPLOAD__', uploadId), { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }, signal: controller.signal });
                    statusText.textContent = 'Upload selesai. Memuat ulang daftar...';
                    window.location.reload();
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        statusText.textContent = error.message || 'Upload gagal.';
                        progressBar.classList.remove('from-red-600', 'to-indigo-600');
                        progressBar.classList.add('bg-red-600');
                        setBusy(false, true);
                    }
                }
            });

            async function uploadChunkWithRetry(chunk, index) {
                let lastError;
                for (let attempt = 1; attempt <= 3; attempt++) {
                    try {
                        return await jsonFetch(chunkUrlTemplate.replace('__UPLOAD__', uploadId).replace('__INDEX__', index), {
                            method: 'PUT', headers: { 'Content-Type': 'application/octet-stream', 'X-CSRF-TOKEN': csrf }, body: chunk, signal: controller.signal,
                        });
                    } catch (error) {
                        lastError = error;
                        if (error.name === 'AbortError') throw error;
                        statusText.textContent = `Bagian ${index + 1} gagal, mencoba lagi (${attempt}/3)...`;
                        await new Promise(resolve => setTimeout(resolve, attempt * 1000));
                    }
                }
                throw lastError;
            }

            async function jsonFetch(url, options) {
                const response = await fetch(url, { ...options, headers: { 'Accept': 'application/json', ...(options.headers || {}) } });
                const data = response.status === 204 ? {} : await response.json().catch(() => ({}));
                if (!response.ok) {
                    const validation = data.errors ? Object.values(data.errors).flat().join(' ') : null;
                    throw new Error(validation || data.message || `Permintaan gagal (${response.status}).`);
                }
                return data;
            }

            function setBusy(busy, keepProgress = false) {
                uploadButton.disabled = busy;
                fileInput.disabled = busy;
                titleInput.disabled = busy;
                descriptionInput.disabled = busy;
                progressWrap.classList.toggle('hidden', !busy && !keepProgress);
                cancelButton.classList.toggle('hidden', !busy);
            }

            function resetUpload(message) {
                uploadId = null;
                controller = null;
                setBusy(false, true);
                statusText.textContent = message;
            }
        })();
    </script>
    @endpush
</x-app-layout>
