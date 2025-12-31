<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pengajuan Perubahan Data
            </h2>
            <a href="{{ route('operator.dapodik.submissions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition text-sm font-semibold">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ 
        showPreview: false, 
        previewUrl: '', 
        previewType: '',
        openPreview(url, type) {
            this.previewUrl = url;
            this.previewType = type;
            this.showPreview = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Data Card --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Perbandingan Data</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Informasi Field</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data Lama (Saat Ini)</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data Baru (Diajukan)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php $hasAtLeastOneChange = false; @endphp
                                        @foreach ($submission->new_data as $key => $value)
                                            @php
                                                $oldValue = $submission->old_data[$key] ?? null;
                                                
                                                // Fallback for nama_lengkap if missing in old_data
                                                if ($key === 'nama_lengkap' && !$oldValue) {
                                                    $oldValue = $submission->masterSiswa->nama_lengkap;
                                                }

                                                // Normalize for comparison
                                                $normOld = ($oldValue === '' || $oldValue === null) ? null : $oldValue;
                                                $normNew = ($value === '' || $value === null) ? null : $value;
                                                
                                                // Specific normalization for booleans/selects like PIP
                                                if (strpos($key, 'layak_pip') !== false || strpos($key, 'penerima') !== false) {
                                                    if ($normOld === 'Tidak' && $normNew === null) $normNew = 'Tidak';
                                                    if ($normNew === 'Tidak' && $normOld === null) $normOld = 'Tidak';
                                                }

                                                $isChanged = $normOld != $normNew;
                                                if($isChanged) $hasAtLeastOneChange = true;

                                                // Formatting for display
                                                $displayOld = $oldValue;
                                                $displayNew = $value;
                                                
                                                if (strpos($key, 'tanggal') !== false && $oldValue) {
                                                    try { $displayOld = \Carbon\Carbon::parse($oldValue)->format('d/m/Y'); } catch(\Exception $e) {}
                                                }
                                                if (strpos($key, 'tanggal') !== false && $value) {
                                                    try { $displayNew = \Carbon\Carbon::parse($value)->format('d/m/Y'); } catch(\Exception $e) {}
                                                }
                                            @endphp
                                            <tr class="{{ $isChanged ? 'bg-blue-50/50' : 'opacity-60' }}">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-700 capitalize">
                                                    {{ str_replace(['_', 'nik', 'nisn', 'nipd', 'rt', 'rw'], [' ', 'NIK', 'NISN', 'NIPD', 'RT', 'RW'], $key) }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500 italic">
                                                    {{ $displayOld ?: '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    @if($isChanged)
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded font-bold">
                                                            {{ $displayNew ?: '-' }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">
                                                            {{ $displayNew ?: '-' }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action & Attachments Card --}}
                <div class="space-y-6">
                    {{-- Action Card --}}
                    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 tracking-tight">Informasi Pengajuan</h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">Nama Siswa</p>
                                    <p class="font-bold text-gray-900">{{ $submission->masterSiswa->nama_lengkap }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">Waktu Pengajuan</p>
                                    <p class="font-bold text-gray-900">{{ $submission->submitted_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">Status Saat Ini</p>
                                    <p class="font-bold">
                                        @if ($submission->status == 'pending')
                                            <span class="text-yellow-600">Menunggu Verifikasi</span>
                                        @elseif ($submission->status == 'approved')
                                            <span class="text-green-600">Disetujui</span>
                                        @else
                                            <span class="text-red-600">Ditolak</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Summary of Changes --}}
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-tighter mb-2">Item Yang Diubah</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @php $changeCount = 0; @endphp
                                    @foreach ($submission->new_data as $key => $value)
                                        @php
                                            $oldValue = $submission->old_data[$key] ?? null;
                                            
                                            // Fallback for nama_lengkap
                                            if ($key === 'nama_lengkap' && !$oldValue) {
                                                $oldValue = $submission->masterSiswa->nama_lengkap;
                                            }

                                            $normOld = ($oldValue === '' || $oldValue === null) ? null : $oldValue;
                                            $normNew = ($value === '' || $value === null) ? null : $value;

                                            // PIP Normalization
                                            if (strpos($key, 'layak_pip') !== false || strpos($key, 'penerima') !== false) {
                                                if ($normOld === 'Tidak' && $normNew === null) $normNew = 'Tidak';
                                                if ($normNew === 'Tidak' && $normOld === null) $normOld = 'Tidak';
                                            }
                                        @endphp
                                        @if($normOld != $normNew)
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded-full border border-blue-100 uppercase tracking-tighter">
                                                {{ str_replace(['_', 'nik', 'nisn', 'nipd'], [' ', 'NIK', 'NISN', 'NIPD'], $key) }}
                                            </span>
                                            @php $changeCount++; @endphp
                                        @endif
                                    @endforeach
                                    @if($changeCount === 0)
                                        <span class="text-xs text-gray-500 italic">Tidak ada perubahan data teks</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($submission->status == 'pending')
                            <div class="mt-8 space-y-4">
                                <form action="{{ route('operator.dapodik.submissions.approve', $submission) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyetujui perubahan data ini?')" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-green-600 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:bg-green-700 transition shadow-md">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Setujui Perubahan
                                    </button>
                                </form>

                                <div class="relative py-2 flex items-center justify-center">
                                    <div class="border-t border-gray-200 w-full absolute"></div>
                                    <span class="bg-white px-3 text-xs text-gray-400 relative">ATAU</span>
                                </div>

                                <form action="{{ route('operator.dapodik.submissions.reject', $submission) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Alasan Penolakan</label>
                                        <textarea name="rejection_reason" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" rows="3" placeholder="Contoh: Lampiran tidak terbaca atau data tidak sesuai..."></textarea>
                                    </div>
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?')" class="w-full inline-flex justify-center items-center px-4 py-2 bg-white border-2 border-red-600 rounded-lg font-bold text-sm text-red-600 uppercase tracking-widest hover:bg-red-50 transition">
                                        Tolak Pengajuan
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <p class="text-xs text-gray-600">
                                    <span class="font-bold uppercase text-[10px]">Diproses oleh:</span><br>
                                    <span class="text-gray-900 font-medium">{{ $submission->operator ? $submission->operator->name : 'System' }}</span>
                                </p>
                                <p class="text-xs text-gray-600 mt-2">
                                    <span class="font-bold uppercase text-[10px]">Waktu:</span><br>
                                    <span class="text-gray-900 font-medium">{{ $submission->processed_at?->format('d M Y H:i') }}</span>
                                </p>
                                @if ($submission->status == 'rejected')
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <span class="font-bold text-red-600 text-[10px] uppercase">Alasan Penolakan:</span>
                                        <p class="text-sm text-gray-700 mt-1 italic leading-relaxed">"{{ $submission->rejection_reason }}"</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Attachments Card --}}
                    @if ($submission->attachments)
                        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                            <div class="p-6">
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    Dokumen Lampiran
                                </h3>
                                <div class="space-y-3">
                                    @foreach ($submission->attachments as $label => $path)
                                        @php 
                                            $url = Storage::url($path);
                                            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                        @endphp
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100 hover:border-blue-200 transition-colors">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                                    @if($isImage)
                                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z"/></svg>
                                                    @else
                                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-gray-900 capitalize">{{ str_replace(['doc_', '_'], ['', ' '], $label) }}</p>
                                                    <p class="text-[10px] text-gray-500">{{ strtoupper($ext) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button @click="openPreview('{{ $url }}', '{{ $isImage ? 'image' : 'pdf' }}')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Lihat">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                                <a href="{{ $url }}" download class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition" title="Unduh">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Preview --}}
        <div x-show="showPreview" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/75 backdrop-blur-sm" x-cloak>
            <div @click.outside="showPreview = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
                <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-900">Preview Dokumen</h3>
                    <div class="flex items-center gap-2">
                        <a :href="previewUrl" download class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            UNDUH
                        </a>
                        <button @click="showPreview = false" class="p-2 text-gray-400 hover:text-red-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
                <div class="flex-1 bg-gray-100 overflow-auto p-4 flex items-center justify-center">
                    <template x-if="previewType === 'image'">
                        <img :src="previewUrl" class="max-w-full h-auto rounded shadow-lg">
                    </template>
                    <template x-if="previewType === 'pdf'">
                        <iframe :src="previewUrl" class="w-full h-[70vh] rounded shadow-lg border-0"></iframe>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
