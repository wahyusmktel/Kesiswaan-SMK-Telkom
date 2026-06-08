<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Cloud Storage</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola file pribadi lokal dan Google Drive dari akun Anda sendiri.</p>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="cloudStorageUpload('{{ route('cloud-files.store') }}', '{{ csrf_token() }}', '{{ $driveConnection ? 'google_drive' : 'local' }}', {{ $driveConnection ? 'true' : 'false' }})">
        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
                <p class="font-black">Upload belum berhasil</p>
                <ul class="mt-2 list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error') || $driveError)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-amber-800">
                <p class="font-black">Google Drive perlu perhatian</p>
                <p class="mt-1 text-sm">{{ session('error') ?: $driveError }}</p>
            </div>
        @endif

        <div class="rounded-3xl bg-gradient-to-r from-gray-950 via-slate-900 to-red-950 p-6 text-white shadow-sm overflow-hidden relative">
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-red-500/20 blur-2xl"></div>
            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-red-200">Google Workspace Drive</p>
                    <h3 class="mt-2 text-2xl font-black">Storage pribadi pegawai, langsung dari aplikasi</h3>
                    <p class="mt-2 max-w-3xl text-sm leading-relaxed text-white/70">
                        Hubungkan akun Google Drive sekolah untuk mengunggah, melihat, mengunduh, dan menghapus file Drive yang dapat dikelola aplikasi.
                    </p>
                    @if($driveConnection)
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-full bg-emerald-400/15 px-3 py-1.5 text-xs font-black text-emerald-100">Terhubung: {{ $driveConnection->email }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-black text-white/80">Sejak {{ $driveConnection->connected_at?->format('d M Y H:i') }}</span>
                        </div>
                    @else
                        <p class="mt-4 text-sm font-semibold text-red-100">Google Drive belum terhubung. Upload tetap bisa memakai penyimpanan lokal aplikasi.</p>
                    @endif
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    @if($driveConnection)
                        <a href="{{ route('cloud-files.google-drive.connect') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-black text-gray-900 hover:bg-gray-100">Hubungkan Ulang</a>
                        <form method="POST" action="{{ route('cloud-files.google-drive.disconnect') }}" onsubmit="return confirm('Putuskan koneksi Google Drive?')">
                            @csrf
                            @method('DELETE')
                            <button class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-black text-white hover:bg-white/15">Putuskan</button>
                        </form>
                    @else
                        <a href="{{ route('cloud-files.google-drive.connect') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-black text-gray-900 hover:bg-gray-100">Hubungkan Google Drive</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <form action="{{ route('cloud-files.index') }}" method="GET" class="w-full lg:max-w-md">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" class="block w-full rounded-2xl border-gray-200 bg-white py-3 pl-12 pr-4 text-sm shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Cari file lokal dan Google Drive...">
                </div>
            </form>

            <button @click="uploadModalOpen = true" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-black text-white shadow-sm hover:bg-red-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0L8 8m4-4l4 4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>
                </svg>
                Upload File
            </button>
        </div>

        <div x-show="uploadModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 p-4 backdrop-blur-sm" @mousemove.window="drag($event)" @mouseup.window="stopDrag()" @keydown.escape.window="closeUploadModal()">
            <div class="flex min-h-full items-center justify-center">
                <form @submit.prevent="startUpload()" enctype="multipart/form-data"
                    class="overflow-hidden rounded-3xl bg-white shadow-2xl transition-all duration-200"
                    :class="isMaximized ? 'fixed inset-4 max-w-none' : 'relative w-full max-w-4xl'"
                    :style="isMaximized ? '' : `transform: translate(${dragX}px, ${dragY}px)`">
                    @csrf
                    <div class="cursor-move select-none border-b border-gray-100 p-6" @mousedown="startDrag($event)">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-black text-gray-900">Upload File</h3>
                                <p class="mt-1 text-sm text-gray-500">Pilih penyimpanan tujuan, lalu unggah banyak file sekaligus.</p>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click.stop="toggleMaximize()" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 hover:bg-gray-50" title="Maximize">
                                    <svg x-show="!isMaximized" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3M3 16v3a2 2 0 002 2h3m8 0h3a2 2 0 002-2v-3"/>
                                    </svg>
                                    <svg x-show="isMaximized" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9H5V5m10 4h4V5M9 15H5v4m10-4h4v4"/>
                                    </svg>
                                </button>
                                <button type="button" @click.stop="closeUploadModal()" :disabled="isUploading" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50" title="Tutup">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="max-h-[calc(100vh-220px)] space-y-5 overflow-y-auto p-6" :class="isMaximized ? 'max-h-[calc(100vh-170px)]' : ''">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="rounded-2xl border p-4 cursor-pointer transition" :class="uploadTarget === 'local' ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white'">
                                <input type="radio" name="storage_target" value="local" x-model="uploadTarget" class="sr-only" :disabled="isUploading">
                                <span class="block text-sm font-black text-gray-900">Storage Lokal</span>
                                <span class="mt-1 block text-xs text-gray-500">File tersimpan di server aplikasi.</span>
                            </label>
                            <label class="rounded-2xl border p-4 cursor-pointer transition {{ $driveConnection ? '' : 'opacity-50' }}" :class="uploadTarget === 'google_drive' ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white'">
                                <input type="radio" name="storage_target" value="google_drive" x-model="uploadTarget" class="sr-only" :disabled="isUploading || !hasDrive">
                                <span class="block text-sm font-black text-gray-900">Google Drive</span>
                                <span class="mt-1 block text-xs text-gray-500">{{ $driveConnection ? 'Upload ke Drive akun Anda.' : 'Hubungkan Drive terlebih dahulu.' }}</span>
                            </label>
                        </div>

                        <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 px-6 py-8 text-center hover:border-red-300">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0-9l-3 3m3-3l3 3"/>
                            </svg>
                            <label for="file-upload" class="mt-4 inline-flex cursor-pointer rounded-xl bg-white px-4 py-2 text-sm font-black text-red-600 ring-1 ring-gray-200 hover:bg-red-50">
                                Pilih file
                                <input id="file-upload" name="files[]" type="file" class="sr-only" multiple required :disabled="isUploading" @change="selectFiles($event)">
                            </label>
                            <p class="mt-2 text-xs font-semibold text-gray-400">Maksimal 50MB per file</p>
                        </div>

                        <div x-show="selectedFiles.length" class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-5">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-black text-gray-900">File yang akan diunggah</p>
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-black text-gray-600" x-text="selectedFiles.length + ' file'"></span>
                                </div>

                                <template x-for="file in selectedFiles" :key="file.id">
                                    <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                                        <div class="flex gap-3">
                                            <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-gray-100 ring-1 ring-gray-200">
                                                <template x-if="file.previewUrl">
                                                    <img :src="file.previewUrl" class="h-full w-full object-cover" alt="">
                                                </template>
                                                <template x-if="!file.previewUrl">
                                                    <div class="flex h-full w-full items-center justify-center" :class="file.iconClass">
                                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="file.iconPath"/>
                                                        </svg>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="min-w-0">
                                                        <p class="truncate text-sm font-black text-gray-900" x-text="file.name"></p>
                                                        <p class="mt-1 text-xs font-semibold text-gray-400" x-text="file.sizeText + ' - ' + file.typeLabel"></p>
                                                    </div>
                                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-widest"
                                                        :class="file.status === 'done' ? 'bg-emerald-50 text-emerald-700' : (file.status === 'error' ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-600')"
                                                        x-text="file.statusText"></span>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="mb-1 flex items-center justify-between text-xs font-bold text-gray-500">
                                                        <span x-text="file.speedText"></span>
                                                        <span x-text="file.progress + '%'"></span>
                                                    </div>
                                                    <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                                        <div class="h-full rounded-full bg-gradient-to-r from-red-600 to-rose-500 transition-all duration-200" :style="`width: ${file.progress}%`"></div>
                                                    </div>
                                                    <p x-show="file.error" class="mt-2 text-xs font-semibold text-red-600" x-text="file.error"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-950 p-4 text-white">
                                <p class="text-xs font-black uppercase tracking-widest text-red-200">Kecepatan Upload</p>
                                <div class="mt-3 flex items-end justify-between gap-3">
                                    <div>
                                        <p class="text-3xl font-black" x-text="currentSpeedText"></p>
                                        <p class="text-xs font-bold text-gray-400">Mbps saat ini</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-black" x-text="totalProgress + '%'"></p>
                                        <p class="text-xs font-bold text-gray-400">Total</p>
                                    </div>
                                </div>
                                <div class="mt-4 h-32">
                                    <canvas id="uploadSpeedChart"></canvas>
                                </div>
                                <div class="mt-4 h-3 overflow-hidden rounded-full bg-white/10">
                                    <div class="h-full rounded-full bg-gradient-to-r from-red-500 via-rose-400 to-emerald-400 transition-all duration-200" :style="`width: ${totalProgress}%`"></div>
                                </div>
                                <p class="mt-3 text-xs font-semibold text-gray-400" x-text="uploadMessage"></p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 bg-gray-50 p-6">
                        <button type="button" @click="closeUploadModal()" :disabled="isUploading" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50">Batal</button>
                        <button :disabled="isUploading || !selectedFiles.length" class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-black text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                            <svg x-show="isUploading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span x-text="isUploading ? `Sedang mengunggah ${totalProgress}%` : 'Upload Sekarang'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.15fr_0.85fr] gap-6">
            <section class="rounded-3xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="font-black text-gray-900">Google Drive Saya</h3>
                            <p class="mt-1 text-sm text-gray-500">File dari Drive akun yang terhubung.</p>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-black text-emerald-700">{{ count($driveFiles) }} file</span>
                    </div>
                </div>

                @if($driveConnection && count($driveFiles))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6">
                        @foreach($driveFiles as $driveFile)
                            @php
                                $mime = $driveFile['mimeType'] ?? '';
                                $isImage = str_starts_with($mime, 'image/');
                                $size = isset($driveFile['size']) ? \Illuminate\Support\Number::fileSize((int) $driveFile['size']) : 'Google file';
                            @endphp
                            <div class="group rounded-2xl border border-gray-100 bg-gray-50 p-4 transition hover:border-red-200 hover:bg-white hover:shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white ring-1 ring-gray-100">
                                        @if($isImage && !empty($driveFile['thumbnailLink']))
                                            <img src="{{ $driveFile['thumbnailLink'] }}" class="h-full w-full object-cover" alt="">
                                        @else
                                            <img src="{{ $driveFile['iconLink'] ?? '' }}" class="h-6 w-6" alt="">
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-black text-gray-900" title="{{ $driveFile['name'] ?? 'File Drive' }}">{{ $driveFile['name'] ?? 'File Drive' }}</p>
                                        <p class="mt-1 text-xs font-semibold text-gray-400">{{ $size }} - {{ !empty($driveFile['modifiedTime']) ? \Carbon\Carbon::parse($driveFile['modifiedTime'])->format('d M Y H:i') : '-' }}</p>
                                    </div>
                                </div>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @if(!empty($driveFile['webViewLink']))
                                        <a href="{{ $driveFile['webViewLink'] }}" target="_blank" class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-black text-gray-700 hover:bg-gray-50">Buka</a>
                                    @endif
                                    <a href="{{ route('cloud-files.google-drive.download', $driveFile['id']) }}" class="rounded-xl bg-gray-900 px-3 py-2 text-xs font-black text-white hover:bg-red-600">Download</a>
                                    <form method="POST" action="{{ route('cloud-files.google-drive.destroy', $driveFile['id']) }}" onsubmit="return confirm('Hapus file ini dari Google Drive?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-xl bg-red-50 px-3 py-2 text-xs font-black text-red-700 hover:bg-red-100">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif($driveConnection)
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h10a4 4 0 10-.2-7.995A5.5 5.5 0 003.5 10.5"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-black text-gray-900">Belum ada file Drive yang terlihat</h3>
                        <p class="mt-1 text-sm text-gray-500">Upload file ke Google Drive melalui aplikasi ini agar tampil di sini.</p>
                    </div>
                @else
                    <div class="px-6 py-16 text-center">
                        <h3 class="text-lg font-black text-gray-900">Hubungkan Google Drive</h3>
                        <p class="mt-1 text-sm text-gray-500">Setelah terhubung, pegawai dapat mengelola file Drive pribadi dari halaman ini.</p>
                        <a href="{{ route('cloud-files.google-drive.connect') }}" class="mt-5 inline-flex rounded-xl bg-red-600 px-4 py-2.5 text-sm font-black text-white hover:bg-red-700">Hubungkan Sekarang</a>
                    </div>
                @endif
            </section>

            <section class="rounded-3xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="font-black text-gray-900">Storage Lokal</h3>
                            <p class="mt-1 text-sm text-gray-500">File lama yang tersimpan di server aplikasi.</p>
                        </div>
                        <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-black text-gray-600">{{ $files->total() }} file</span>
                    </div>
                </div>

                @if($files->count())
                    <div class="divide-y divide-gray-100">
                        @foreach($files as $file)
                            <div class="flex items-center gap-3 px-6 py-4 hover:bg-gray-50">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gray-100 text-gray-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-black text-gray-900">{{ $file->name }}</p>
                                    <p class="text-xs font-semibold text-gray-400">{{ $file->size_for_humans }} - {{ $file->created_at->format('d M Y') }}</p>
                                </div>
                                <a href="{{ route('cloud-files.download', $file) }}" class="rounded-xl bg-gray-900 px-3 py-2 text-xs font-black text-white hover:bg-red-600">Unduh</a>
                                <form action="{{ route('cloud-files.destroy', $file) }}" method="POST" onsubmit="return confirm('Hapus file lokal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-xl bg-red-50 px-3 py-2 text-xs font-black text-red-700 hover:bg-red-100">Hapus</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    @if($files->hasPages())
                        <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">{{ $files->links() }}</div>
                    @endif
                @else
                    <div class="px-6 py-16 text-center">
                        <h3 class="text-lg font-black text-gray-900">Belum ada file lokal</h3>
                        <p class="mt-1 text-sm text-gray-500">Upload ke storage lokal jika ingin file tetap tersimpan di server aplikasi.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>

    <script>
        function cloudStorageUpload(uploadUrl, csrfToken, defaultTarget, hasDrive) {
            return {
                uploadModalOpen: false,
                uploadTarget: defaultTarget,
                hasDrive: hasDrive,
                isMaximized: false,
                isDragging: false,
                dragX: 0,
                dragY: 0,
                dragStartX: 0,
                dragStartY: 0,
                initialDragX: 0,
                initialDragY: 0,
                selectedFiles: [],
                isUploading: false,
                totalProgress: 0,
                currentSpeedMbps: 0,
                currentSpeedText: '0.00',
                uploadMessage: 'Menunggu file dipilih.',
                speedChart: null,
                speedLabels: [],
                speedValues: [],

                toggleMaximize() {
                    this.isMaximized = !this.isMaximized;
                    if (this.isMaximized) {
                        this.dragX = 0;
                        this.dragY = 0;
                    }
                    this.$nextTick(() => this.ensureSpeedChart());
                },

                startDrag(event) {
                    if (this.isMaximized || this.isUploading || event.target.closest('button')) return;
                    this.isDragging = true;
                    this.dragStartX = event.clientX;
                    this.dragStartY = event.clientY;
                    this.initialDragX = this.dragX;
                    this.initialDragY = this.dragY;
                },

                drag(event) {
                    if (!this.isDragging) return;
                    this.dragX = this.initialDragX + event.clientX - this.dragStartX;
                    this.dragY = this.initialDragY + event.clientY - this.dragStartY;
                },

                stopDrag() {
                    this.isDragging = false;
                },

                closeUploadModal() {
                    if (this.isUploading) return;
                    this.uploadModalOpen = false;
                    this.isMaximized = false;
                    this.dragX = 0;
                    this.dragY = 0;
                },

                selectFiles(event) {
                    this.revokePreviews();
                    this.selectedFiles = Array.from(event.target.files || []).map((file, index) => {
                        const ext = (file.name.split('.').pop() || '').toLowerCase();
                        const isImage = file.type.startsWith('image/');
                        const meta = this.fileIconMeta(ext, file.type);

                        return {
                            id: `${Date.now()}-${index}-${file.name}`,
                            raw: file,
                            name: file.name,
                            size: file.size,
                            sizeText: this.formatBytes(file.size),
                            typeLabel: ext ? ext.toUpperCase() : (file.type || 'FILE'),
                            previewUrl: isImage ? URL.createObjectURL(file) : null,
                            iconClass: meta.class,
                            iconPath: meta.path,
                            progress: 0,
                            speedText: 'Menunggu upload',
                            status: 'waiting',
                            statusText: 'Menunggu',
                            error: null,
                            uploadedBytes: 0,
                        };
                    });

                    this.totalProgress = 0;
                    this.currentSpeedMbps = 0;
                    this.currentSpeedText = '0.00';
                    this.uploadMessage = this.selectedFiles.length ? 'Siap mengunggah ' + this.selectedFiles.length + ' file.' : 'Menunggu file dipilih.';
                    this.resetSpeedChart();
                },

                async startUpload() {
                    if (this.isUploading || !this.selectedFiles.length) return;

                    this.isUploading = true;
                    this.totalProgress = 0;
                    this.uploadMessage = 'Mengunggah file pertama...';
                    this.resetSpeedChart();
                    this.$nextTick(() => this.ensureSpeedChart());

                    for (let index = 0; index < this.selectedFiles.length; index++) {
                        const file = this.selectedFiles[index];
                        file.status = 'uploading';
                        file.statusText = 'Upload';
                        file.error = null;
                        this.uploadMessage = `Mengunggah ${index + 1} dari ${this.selectedFiles.length}: ${file.name}`;

                        try {
                            await this.uploadSingleFile(file);
                            file.progress = 100;
                            file.status = 'done';
                            file.statusText = 'Selesai';
                            file.speedText = 'Selesai';
                        } catch (error) {
                            file.status = 'error';
                            file.statusText = 'Gagal';
                            file.error = error.message || 'Upload gagal.';
                            this.isUploading = false;
                            this.uploadMessage = 'Upload berhenti karena ada file yang gagal.';
                            return;
                        }

                        this.updateTotalProgress();
                    }

                    this.uploadMessage = 'Semua file berhasil diunggah. Memuat ulang daftar file...';
                    this.totalProgress = 100;
                    setTimeout(() => window.location.reload(), 700);
                },

                uploadSingleFile(file) {
                    return new Promise((resolve, reject) => {
                        const xhr = new XMLHttpRequest();
                        const formData = new FormData();
                        formData.append('_token', csrfToken);
                        formData.append('storage_target', this.uploadTarget);
                        formData.append('files[]', file.raw);

                        let lastLoaded = 0;
                        let lastTime = performance.now();

                        xhr.upload.addEventListener('progress', (event) => {
                            if (!event.lengthComputable) return;

                            const now = performance.now();
                            const elapsedSeconds = Math.max((now - lastTime) / 1000, 0.001);
                            const loadedDelta = Math.max(event.loaded - lastLoaded, 0);
                            const speedMbps = (loadedDelta * 8) / elapsedSeconds / 1000000;

                            lastLoaded = event.loaded;
                            lastTime = now;
                            file.uploadedBytes = event.loaded;
                            file.progress = Math.min(100, Math.round((event.loaded / event.total) * 100));
                            if (file.progress >= 100) {
                                file.statusText = this.uploadTarget === 'google_drive' ? 'Menyimpan' : 'Memproses';
                            }
                            file.speedText = speedMbps.toFixed(2) + ' Mbps';
                            this.currentSpeedMbps = speedMbps;
                            this.currentSpeedText = speedMbps.toFixed(2);
                            this.updateTotalProgress();
                            this.pushSpeedPoint(speedMbps);
                        });

                        xhr.addEventListener('load', () => {
                            if (xhr.status >= 200 && xhr.status < 300) {
                                resolve();
                                return;
                            }

                            let message = 'Upload gagal dengan status ' + xhr.status + '.';
                            try {
                                const payload = JSON.parse(xhr.responseText);
                                const errors = Object.values(payload.errors || {}).flat();
                                message = payload.message || errors[0] || message;
                            } catch (error) {
                                // Response is not JSON.
                            }
                            reject(new Error(message));
                        });

                        xhr.addEventListener('error', () => reject(new Error('Koneksi upload bermasalah.')));
                        xhr.addEventListener('abort', () => reject(new Error('Upload dibatalkan.')));
                        xhr.open('POST', uploadUrl, true);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.setRequestHeader('Accept', 'application/json');
                        xhr.send(formData);
                    });
                },

                updateTotalProgress() {
                    const totalBytes = this.selectedFiles.reduce((sum, file) => sum + file.size, 0);
                    const uploadedBytes = this.selectedFiles.reduce((sum, file) => {
                        if (file.status === 'done') return sum + file.size;
                        return sum + Math.min(file.uploadedBytes || 0, file.size);
                    }, 0);

                    this.totalProgress = totalBytes ? Math.min(100, Math.round((uploadedBytes / totalBytes) * 100)) : 0;
                },

                ensureSpeedChart() {
                    const canvas = document.getElementById('uploadSpeedChart');
                    if (!canvas || typeof Chart === 'undefined' || this.speedChart) return;

                    this.speedChart = new Chart(canvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: this.speedLabels,
                            datasets: [{
                                label: 'Mbps',
                                data: this.speedValues,
                                borderColor: '#fb7185',
                                backgroundColor: 'rgba(251, 113, 133, 0.18)',
                                fill: true,
                                tension: 0.38,
                                pointRadius: 0,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#111827',
                                    callbacks: {
                                        label: (context) => `${Number(context.parsed.y || 0).toFixed(2)} Mbps`,
                                    },
                                },
                            },
                            scales: {
                                x: { display: false },
                                y: {
                                    beginAtZero: true,
                                    ticks: { color: '#9ca3af', callback: (value) => value + ' Mbps' },
                                    grid: { color: 'rgba(255,255,255,.08)' },
                                },
                            },
                        },
                    });
                },

                pushSpeedPoint(speedMbps) {
                    this.ensureSpeedChart();
                    const label = new Date().toLocaleTimeString('id-ID', { minute: '2-digit', second: '2-digit' });
                    this.speedLabels.push(label);
                    this.speedValues.push(Number(speedMbps.toFixed(2)));

                    if (this.speedLabels.length > 28) {
                        this.speedLabels.shift();
                        this.speedValues.shift();
                    }

                    if (this.speedChart) {
                        this.speedChart.update('none');
                    }
                },

                resetSpeedChart() {
                    this.speedLabels = [];
                    this.speedValues = [];
                    if (this.speedChart) {
                        this.speedChart.destroy();
                        this.speedChart = null;
                    }
                },

                revokePreviews() {
                    this.selectedFiles.forEach((file) => {
                        if (file.previewUrl) URL.revokeObjectURL(file.previewUrl);
                    });
                },

                formatBytes(bytes) {
                    if (!bytes) return '0 B';
                    const units = ['B', 'KB', 'MB', 'GB'];
                    const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
                    return (bytes / Math.pow(1024, index)).toFixed(index === 0 ? 0 : 2) + ' ' + units[index];
                },

                fileIconMeta(ext, mimeType) {
                    if (mimeType === 'application/pdf' || ext === 'pdf') {
                        return {
                            class: 'text-red-600',
                            path: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                        };
                    }
                    if (['xls', 'xlsx', 'csv'].includes(ext)) {
                        return {
                            class: 'text-emerald-600',
                            path: 'M9 17v-2m3 2v-4m3 4v-6M7 3h6l5 5v11a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z',
                        };
                    }
                    if (['doc', 'docx'].includes(ext)) {
                        return {
                            class: 'text-blue-600',
                            path: 'M7 3h6l5 5v11a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z',
                        };
                    }
                    if (['zip', 'rar', '7z'].includes(ext)) {
                        return {
                            class: 'text-amber-600',
                            path: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
                        };
                    }
                    return {
                        class: 'text-gray-500',
                        path: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                    };
                },
            };
        }
    </script>
</x-app-layout>
