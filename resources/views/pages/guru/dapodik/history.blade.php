<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Pengajuan Dapodik</h2>
                <p class="text-sm text-gray-500 mt-1">Pantau status pengajuan perubahan data Dapodik Anda.</p>
            </div>
            <a href="{{ route('guru.dapodik.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition shadow-sm">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Field Diubah</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($submissions as $sub)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $sub->submitted_at->translatedFormat('d M Y, H:i') }}
                                        <p class="text-xs text-gray-400">{{ $sub->submitted_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach(array_keys($sub->new_data) as $field)
                                                <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded font-medium">{{ $field }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($sub->status === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-700 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Menunggu
                                            </span>
                                        @elseif($sub->status === 'approved')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-full">Disetujui</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-700 rounded-full">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        @if($sub->status === 'approved')
                                            <span class="text-green-600">Diproses {{ $sub->processed_at?->translatedFormat('d M Y') }}</span>
                                        @elseif($sub->status === 'rejected')
                                            <span class="text-red-600">{{ $sub->rejection_reason }}</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="font-medium">Belum ada riwayat pengajuan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($submissions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">{{ $submissions->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
