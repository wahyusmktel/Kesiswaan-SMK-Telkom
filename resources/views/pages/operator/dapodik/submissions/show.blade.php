<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pengajuan Perubahan Data
            </h2>
            <a href="{{ route('operator.dapodik.submissions.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
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
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data Lama</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data Baru</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($submission->new_data as $key => $value)
                                            @php
                                                $oldValue = $submission->old_data[$key] ?? '-';
                                                $isChanged = $oldValue != $value;
                                            @endphp
                                            <tr class="{{ $isChanged ? 'bg-yellow-50' : '' }}">
                                                <td class="px-4 py-2 text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $key) }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $oldValue ?: '-' }}</td>
                                                <td class="px-4 py-2 text-sm font-bold {{ $isChanged ? 'text-blue-600' : 'text-gray-900' }}">{{ $value ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Attachments Card --}}
                    @if ($submission->attachments)
                        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    Dokumen Lampiran
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach ($submission->attachments as $label => $path)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 bg-white rounded shadow-sm">
                                                    @php $ext = pathinfo($path, PATHINFO_EXTENSION); @endphp
                                                    @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z"/></svg>
                                                    @else
                                                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900 capitalize">{{ str_replace('doc_', '', $label) }}</p>
                                                    <p class="text-xs text-gray-500">{{ strtoupper($ext) }} File</p>
                                                </div>
                                            </div>
                                            <a href="{{ Storage::url($path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-semibold flex items-center gap-1">
                                                <span>Lihat</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Action Card --}}
                <div class="space-y-6">
                    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Pengajuan</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Siswa</p>
                                <p class="font-medium text-gray-900">{{ $submission->masterSiswa->nama_lengkap }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Waktu Pengajuan</p>
                                <p class="font-medium text-gray-900">{{ $submission->submitted_at->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Status</p>
                                <p class="font-medium">
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

                        @if ($submission->status == 'pending')
                            <div class="mt-8 space-y-4">
                                <form action="{{ route('operator.dapodik.submissions.approve', $submission) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyetujui perubahan data ini?')" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:bg-green-700 transition">
                                        Setujui Perubahan
                                    </button>
                                </form>

                                <hr class="my-4">

                                <form action="{{ route('operator.dapodik.submissions.reject', $submission) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Alasan Penolakan</label>
                                        <textarea name="rejection_reason" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Berikan alasan penolakan..."></textarea>
                                    </div>
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?')" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:bg-red-700 transition">
                                        Tolak Pengajuan
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">
                                    <span class="font-bold">Diproses oleh:</span> {{ $submission->operator ? $submission->operator->name : 'System' }}<br>
                                    <span class="font-bold">Waktu:</span> {{ $submission->processed_at?->format('d M Y H:i') }}
                                </p>
                                @if ($submission->status == 'rejected')
                                    <div class="mt-2 text-sm">
                                        <span class="font-bold text-red-600">Alasan Penolakan:</span><br>
                                        {{ $submission->rejection_reason }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
