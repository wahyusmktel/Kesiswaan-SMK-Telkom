<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengajuan Perubahan Dapodik Guru</h2>
                <p class="text-sm text-gray-500 mt-1">Review dan validasi pengajuan perubahan data Dapodik dari guru.</p>
            </div>
            @if($pendingCount > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-800 text-xs font-bold rounded-full">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ $pendingCount }} Menunggu
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm font-medium">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm font-medium">{{ session('error') }}</div>
            @endif

            {{-- Filter tabs --}}
            <div class="flex gap-2 flex-wrap">
                @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $key => $label)
                    <a href="{{ route('operator.dapodik-guru.submissions.index', ['status' => $key]) }}"
                        class="px-4 py-2 rounded-lg text-sm font-semibold transition
                            {{ $status === $key
                                ? 'bg-blue-600 text-white shadow-sm'
                                : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-100">
                    <p class="text-xs text-gray-400">{{ $submissions->total() }} pengajuan ditemukan</p>
                </div>

                @if($submissions->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Guru</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Field Diubah</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Diajukan</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($submissions as $sub)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-800 text-sm">{{ $sub->masterGuru->nama_lengkap ?? '—' }}</p>
                                            <p class="text-xs text-gray-400">{{ $sub->masterGuru->nuptk ?? 'NUPTK belum ada' }}</p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(array_keys($sub->new_data) as $field)
                                                    <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-600 rounded font-medium">{{ $field }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="text-sm text-gray-600">{{ $sub->submitted_at->translatedFormat('d M Y') }}</span>
                                            <p class="text-xs text-gray-400">{{ $sub->submitted_at->diffForHumans() }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-center">
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
                                        <td class="px-4 py-4 text-center">
                                            <a href="{{ route('operator.dapodik-guru.submissions.show', $sub) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition shadow-sm">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100">{{ $submissions->links() }}</div>
                @else
                    <div class="py-20 text-center">
                        <svg class="w-14 h-14 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="font-bold text-gray-400">Tidak ada pengajuan ditemukan.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
