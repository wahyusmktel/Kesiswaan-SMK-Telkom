<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Cloud Storage</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola file pribadi lokal dan Google Drive dari akun Anda sendiri.</p>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ uploadModalOpen: false, uploadTarget: '{{ $driveConnection ? 'google_drive' : 'local' }}' }">
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

        <div x-show="uploadModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 p-4 backdrop-blur-sm">
            <div class="flex min-h-full items-center justify-center">
                <form action="{{ route('cloud-files.store') }}" method="POST" enctype="multipart/form-data" class="w-full max-w-xl overflow-hidden rounded-3xl bg-white shadow-2xl">
                    @csrf
                    <div class="border-b border-gray-100 p-6">
                        <h3 class="text-xl font-black text-gray-900">Upload File</h3>
                        <p class="mt-1 text-sm text-gray-500">Pilih penyimpanan tujuan, lalu unggah banyak file sekaligus.</p>
                    </div>
                    <div class="space-y-5 p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="rounded-2xl border p-4 cursor-pointer transition" :class="uploadTarget === 'local' ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white'">
                                <input type="radio" name="storage_target" value="local" x-model="uploadTarget" class="sr-only">
                                <span class="block text-sm font-black text-gray-900">Storage Lokal</span>
                                <span class="mt-1 block text-xs text-gray-500">File tersimpan di server aplikasi.</span>
                            </label>
                            <label class="rounded-2xl border p-4 cursor-pointer transition {{ $driveConnection ? '' : 'opacity-50' }}" :class="uploadTarget === 'google_drive' ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white'">
                                <input type="radio" name="storage_target" value="google_drive" x-model="uploadTarget" class="sr-only" @disabled(!$driveConnection)>
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
                                <input id="file-upload" name="files[]" type="file" class="sr-only" multiple required>
                            </label>
                            <p class="mt-2 text-xs font-semibold text-gray-400">Maksimal 50MB per file</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 bg-gray-50 p-6">
                        <button type="button" @click="uploadModalOpen = false" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">Batal</button>
                        <button class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-black text-white hover:bg-red-700">Upload Sekarang</button>
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
</x-app-layout>
