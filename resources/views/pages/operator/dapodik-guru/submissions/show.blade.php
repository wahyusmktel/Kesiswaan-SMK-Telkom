<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Pengajuan Dapodik Guru</h2>
        <p class="text-sm text-gray-500 mt-1">Tinjau dan verifikasi pengajuan perubahan data Dapodik guru.</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4">
                <a href="{{ route('operator.dapodik-guru.submissions.index') }}"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Pengajuan
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm font-medium">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm font-medium">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Sidebar Info --}}
                <div class="space-y-4">

                    {{-- Guru info --}}
                    <div class="bg-white shadow-sm rounded-xl p-5 space-y-3">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Guru</p>
                            <p class="font-bold text-gray-800 mt-1">{{ $submission->masterGuru->nama_lengkap }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">NUPTK: {{ $submission->masterGuru->nuptk ?? '—' }}</p>
                            <p class="text-xs text-gray-500">NIK: {{ $submission->masterGuru->nik ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Diajukan</p>
                            <p class="text-sm text-gray-700 mt-1">{{ $submission->submitted_at->translatedFormat('d M Y, H:i') }}</p>
                            <p class="text-xs text-gray-400">{{ $submission->submitted_at->diffForHumans() }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status</p>
                            <div class="mt-1">
                                @if($submission->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-700 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($submission->status === 'approved')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-full">Disetujui</span>
                                    <p class="text-xs text-gray-400 mt-1">
                                        oleh {{ $submission->operator->name ?? '—' }}
                                        — {{ $submission->processed_at?->translatedFormat('d M Y, H:i') }}
                                    </p>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-700 rounded-full">Ditolak</span>
                                    <p class="text-xs text-gray-400 mt-1">
                                        oleh {{ $submission->operator->name ?? '—' }}
                                        — {{ $submission->processed_at?->translatedFormat('d M Y, H:i') }}
                                    </p>
                                    @if($submission->rejection_reason)
                                        <p class="text-xs text-red-700 mt-2 p-2 bg-red-50 rounded-lg">
                                            <strong>Alasan:</strong> {{ $submission->rejection_reason }}
                                        </p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Actions (pending only) --}}
                    @if($submission->isPending())
                        <div class="bg-white shadow-sm rounded-xl p-5 space-y-3">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tindakan</p>

                            <form method="POST" action="{{ route('operator.dapodik-guru.submissions.approve', $submission) }}"
                                onsubmit="return confirm('Setujui pengajuan ini? Data Dapodik guru akan langsung diperbarui.')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl text-sm transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Setujui & Perbarui Data
                                </button>
                            </form>

                            <div x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-red-200 text-red-600 hover:bg-red-50 font-bold rounded-xl text-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Tolak dengan Alasan
                                </button>

                                <div x-show="open" x-transition class="mt-3">
                                    <form method="POST" action="{{ route('operator.dapodik-guru.submissions.reject', $submission) }}" class="space-y-3">
                                        @csrf
                                        @method('PATCH')
                                        <textarea name="rejection_reason" rows="3" required
                                            class="w-full rounded-xl border-gray-200 text-sm focus:ring-red-500 focus:border-red-500"
                                            placeholder="Tuliskan alasan penolakan yang jelas..."></textarea>
                                        <button type="submit"
                                            class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-sm transition shadow-sm">
                                            Kirim Penolakan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Comparison Table --}}
                <div class="lg:col-span-2 bg-white shadow-sm rounded-xl overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-900">Perbandingan Data</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Kiri: data saat ini. Kanan: data yang diajukan.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/4">Field</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data Lama</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data Baru</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($submission->new_data as $field => $newVal)
                                    @php
                                        $oldVal = $submission->old_data[$field] ?? null;
                                        $normOld = ($oldVal === '' || $oldVal === null) ? null : $oldVal;
                                        $normNew = ($newVal === '' || $newVal === null) ? null : $newVal;
                                        $changed = $normOld !== $normNew;
                                    @endphp
                                    <tr class="{{ $changed ? 'bg-yellow-50/40' : '' }}">
                                        <td class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">{{ str_replace('_', ' ', $field) }}</td>
                                        <td class="px-4 py-3 text-sm {{ $changed ? 'text-red-700 line-through' : 'text-gray-500' }}">
                                            {{ $normOld ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm {{ $changed ? 'text-green-700 font-semibold' : 'text-gray-700' }}">
                                            {{ $normNew ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
