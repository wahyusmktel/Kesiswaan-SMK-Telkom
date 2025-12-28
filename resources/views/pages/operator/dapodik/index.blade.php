<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Data Dapodik
            </h2>
            <div class="flex gap-2">
                <button @click="$dispatch('open-import-modal')"
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 transition gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Import Excel
                </button>
                <form action="{{ route('operator.dapodik.sync') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 transition gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Sync ke Master Siswa
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            
            @if (session('failed_records') && count(session('failed_records')) > 0)
                <div class="mb-6 bg-white border border-red-200 shadow-sm rounded-xl overflow-hidden">
                    <div class="px-6 py-4 bg-red-50 border-b border-red-200 flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h3 class="font-semibold text-red-800">Data Gagal Import ({{ count(session('failed_records')) }} baris)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-red-50 border-b border-red-100">
                                <tr>
                                    <th class="px-4 py-3 w-16">Baris</th>
                                    <th class="px-4 py-3 w-28">NIPD</th>
                                    <th class="px-4 py-3">Nama</th>
                                    <th class="px-4 py-3">Error</th>
                                    <th class="px-4 py-3">Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-red-100">
                                @foreach (session('failed_records') as $record)
                                    <tr class="bg-white hover:bg-red-50/50">
                                        <td class="px-4 py-3 text-center font-mono text-red-600 font-bold">{{ $record['row'] }}</td>
                                        <td class="px-4 py-3 font-mono text-gray-900">{{ $record['nipd'] }}</td>
                                        <td class="px-4 py-3 text-gray-900">{{ $record['nama'] }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                {{ Str::limit($record['error'], 50) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 text-xs">
                                            <div class="flex items-start gap-2">
                                                <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $record['recommendation'] }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-3 bg-red-50 border-t border-red-200 text-sm text-red-700">
                        <strong>Tips:</strong> Perbaiki data pada file Excel Anda sesuai rekomendasi di atas, lalu import ulang.
                    </div>
                </div>
            @endif
            
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Data Dapodik</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_dapodik']) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Master Siswa</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_siswa']) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Sync Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['synced_today'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Data Table --}}
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="font-semibold text-gray-900">Data Dapodik</h3>
                            <form action="{{ route('operator.dapodik.index') }}" method="GET" class="w-64 relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="pl-10 block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Cari NIPD, NISN, Nama...">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </form>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-6 py-3">NIPD</th>
                                        <th class="px-6 py-3">Nama</th>
                                        <th class="px-6 py-3">NISN</th>
                                        <th class="px-6 py-3">Rombel</th>
                                        <th class="px-6 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse ($dapodik as $item)
                                        <tr class="bg-white hover:bg-gray-50">
                                            <td class="px-6 py-4 font-mono text-gray-900">{{ $item->nipd ?? '-' }}</td>
                                            <td class="px-6 py-4">
                                                <div class="font-medium text-gray-900">{{ $item->masterSiswa?->nama_lengkap ?? '-' }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->agama ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-600">{{ $item->nisn ?? '-' }}</td>
                                            <td class="px-6 py-4 text-gray-600">{{ $item->rombel_saat_ini ?? '-' }}</td>
                                            <td class="px-6 py-4">
                                                @if ($item->masterSiswa)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                        Terhubung
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                        Belum Terhubung
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <p>Belum ada data Dapodik. Silakan import file Excel.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-white px-4 py-3 border-t">
                            {{ $dapodik->withQueryString()->links() }}
                        </div>
                    </div>
                </div>

                {{-- Sync History --}}
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-semibold text-gray-900">Riwayat Sinkronisasi</h3>
                        </div>
                        <div class="divide-y max-h-96 overflow-y-auto">
                            @forelse ($syncHistory as $history)
                                <div class="p-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $history->type == 'import' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $history->type == 'import' ? 'Import' : 'Sync' }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $history->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="text-green-600">+{{ $history->inserted_count }}</span> baru,
                                        <span class="text-blue-600">{{ $history->updated_count }}</span> diperbarui
                                        @if ($history->failed_count > 0)
                                            , <span class="text-red-600">{{ $history->failed_count }} gagal</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ $history->user?->name }}</p>
                                </div>
                            @empty
                                <div class="p-6 text-center text-gray-500">
                                    <p class="text-sm">Belum ada riwayat sinkronisasi</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <div x-data="{ isOpen: false }" @open-import-modal.window="isOpen = true" x-show="isOpen" style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div x-show="isOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="isOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="isOpen" class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full">
                <div class="bg-emerald-600 px-4 py-3 rounded-t-xl flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Import Data Dapodik</h3>
                    <button @click="isOpen = false" class="text-emerald-100 hover:text-white">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form action="{{ route('operator.dapodik.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-3 text-sm text-emerald-800">
                            <p class="font-bold mb-1">Format File Excel:</p>
                            <p>Pastikan file Excel memiliki header sesuai format Dapodik, contoh:</p>
                            <p class="font-mono text-xs mt-1 bg-white p-1 rounded border">Nama, NIPD, JK, NISN, Tempat Lahir, ...</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih File Excel</label>
                            <input type="file" name="file_import" required accept=".xlsx,.xls,.csv"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50">
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <a href="{{ route('operator.dapodik.template') }}" class="text-emerald-600 hover:text-emerald-700 font-medium hover:underline">
                                Unduh Template Import Excel
                            </a>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse rounded-b-xl border-t">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500 sm:ml-3">
                            Upload & Import
                        </button>
                        <button type="button" @click="isOpen = false" class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 hover:bg-gray-50">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
