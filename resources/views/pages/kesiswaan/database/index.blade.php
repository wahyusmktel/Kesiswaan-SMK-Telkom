<x-app-layout>
    @push('styles')
        <style>
            @keyframes gradient-xy {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient-xy 6s ease infinite;
            }
            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    @endpush

    <x-slot name="header">
        <div class="w-full flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Maintenance Database</h2>
                <p class="text-sm text-gray-500 mt-1">Backup, restore, dan kelola salinan basis data aplikasi.</p>
            </div>
            <div class="flex gap-2">
                <form action="{{ route('kesiswaan.database.backup') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Backup Database
                    </button>
                </form>
                <button onclick="document.getElementById('uploadRestoreModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Upload SQL
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Widget Statistik --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="rounded-xl p-5 border border-red-200 shadow-sm relative overflow-hidden animate-gradient bg-gradient-to-br from-red-50 via-white to-red-100">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <p class="text-xs font-bold text-red-800 uppercase tracking-wider">Total Backup</p>
                            <h3 class="mt-1 text-2xl font-black text-gray-800">{{ count($backups) }}</h3>
                        </div>
                        <div class="p-2 bg-white/60 backdrop-blur rounded-lg text-red-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl p-5 border border-amber-200 shadow-sm relative overflow-hidden animate-gradient bg-gradient-to-br from-amber-50 via-white to-amber-100">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <p class="text-xs font-bold text-amber-800 uppercase tracking-wider">Aktifitas Sukses</p>
                            <h3 class="mt-1 text-2xl font-black text-gray-800">{{ $activities->where('status', 'success')->count() }}</h3>
                        </div>
                        <div class="p-2 bg-white/60 backdrop-blur rounded-lg text-amber-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl p-5 border border-gray-200 shadow-sm relative overflow-hidden animate-gradient bg-gradient-to-br from-gray-50 via-white to-gray-200">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Total Tabel</p>
                            <h3 class="mt-1 text-2xl font-black text-gray-800">{{ count($tableList) }}</h3>
                        </div>
                        <div class="p-2 bg-white/60 backdrop-blur rounded-lg text-gray-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl p-5 border border-red-200 shadow-sm relative overflow-hidden animate-gradient bg-gradient-to-br from-red-50 via-white to-red-100">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <p class="text-xs font-bold text-red-800 uppercase tracking-wider">Terakhir Aktifitas</p>
                            <h3 class="mt-1 text-sm font-black text-gray-800">{{ $activities->first() ? $activities->first()->created_at->diffForHumans() : '-' }}</h3>
                        </div>
                        <div class="p-2 bg-white/60 backdrop-blur rounded-lg text-red-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Riwayat Aktifitas --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center gap-2 mb-4 px-1">
                        <div class="w-1.5 h-6 bg-red-600 rounded-full shadow-sm"></div>
                        <h3 class="text-lg font-bold text-gray-800 tracking-tight">Riwayat Aktifitas</h3>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr class="text-left bg-gray-50/50 border-b border-gray-200">
                                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe</th>
                                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Filename</th>
                                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($activities as $activity)
                                        <tr class="hover:bg-red-50/30 transition-colors group">
                                            <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                                                {{ $activity->created_at->format('d/m/y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm font-bold text-gray-800">
                                                {{ $activity->user->name }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $activity->type == 'backup' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-purple-50 text-purple-700 border-purple-200' }}">
                                                    {{ strtoupper($activity->type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-[150px]" title="{{ $activity->filename }}">
                                                {{ $activity->filename }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if ($activity->status == 'success')
                                                    <span class="text-green-600 flex items-center font-bold text-xs">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        SUKSES
                                                    </span>
                                                @else
                                                    <span class="text-red-600 flex items-center font-bold text-xs" title="{{ $activity->error_message }}">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        GAGAL
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <button onclick="showActivityDetail({{ json_encode($activity) }})" class="text-gray-400 group-hover:text-red-600 transition-colors">
                                                    <span class="text-xs font-bold underline decoration-dotted">Detail</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                                Belum ada riwayat aktifitas database.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 bg-gray-50">
                            {{ $activities->links() }}
                        </div>
                    </div>
                </div>

                {{-- Daftar File Backup --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-4 px-1">
                        <div class="w-1.5 h-6 bg-amber-500 rounded-full shadow-sm"></div>
                        <h3 class="text-lg font-bold text-gray-800 tracking-tight">Daftar Salinan</h3>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                        <div class="p-4 border-b border-gray-50 flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">File Backup Lokal</span>
                            <span class="text-[10px] text-gray-400 italic">storage/app/backups</span>
                        </div>
                        <div class="divide-y divide-gray-50 max-h-[500px] overflow-y-auto custom-scrollbar">
                            @forelse ($backups as $backup)
                                <div class="p-4 hover:bg-gray-50 transition-all flex items-center justify-between group">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 p-2 bg-blue-100/50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-black text-gray-800 truncate max-w-[150px]" title="{{ $backup['filename'] }}">
                                                {{ $backup['filename'] }}
                                            </h4>
                                            <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">
                                                {{ number_format($backup['size'] / 1024, 2) }} KB • {{ \Carbon\Carbon::createFromTimestamp($backup['last_modified'])->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('kesiswaan.database.download', $backup['filename']) }}" class="p-1.5 bg-green-100 text-green-700 rounded-md hover:bg-green-700 hover:text-white transition-all shadow-sm" title="Unduh">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('kesiswaan.database.restore') }}" method="POST" onsubmit="return confirm('PERINGATAN! Restore database akan menimpa data saat ini. Lanjutkan?')">
                                            @csrf
                                            <input type="hidden" name="filename" value="{{ $backup['filename'] }}">
                                            <button type="submit" class="p-1.5 bg-amber-100 text-amber-700 rounded-md hover:bg-amber-700 hover:text-white transition-all shadow-sm" title="Restore Data">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('kesiswaan.database.destroy', $backup['filename']) }}" method="POST" onsubmit="return confirm('Hapus file backup ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-red-100 text-red-700 rounded-md hover:bg-red-700 hover:text-white transition-all shadow-sm" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-400 italic text-sm">
                                    Belum ada file backup tersedia.
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    {{-- Informasi Database --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <h4 class="font-bold text-gray-800 mb-4 text-xs uppercase tracking-widest text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informasi Database
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500">Koneksi</span>
                                <span class="font-bold text-gray-800">{{ config('database.default') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500">Database</span>
                                <span class="font-bold text-gray-800">{{ config('database.connections.mysql.database') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500">Host</span>
                                <span class="font-bold text-gray-800">{{ config('database.connections.mysql.host') }}</span>
                            </div>
                            <div class="pt-3 mt-3 border-t border-gray-100">
                                <h5 class="text-[10px] font-black text-gray-400 uppercase mb-2">Daftar Tabel ({{ count($tableList) }})</h5>
                                <div class="flex flex-wrap gap-1 max-h-32 overflow-y-auto custom-scrollbar pr-1">
                                    @foreach ($tableList as $table)
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-medium">{{ $table }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail Aktifitas --}}
    <div id="activityDetailModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="hideActivityDetail()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b border-gray-100">
                    <h3 class="text-lg font-black text-gray-800 uppercase tracking-tighter" id="modal-title">Rincian Aktifitas Database</h3>
                    <button onclick="hideActivityDetail()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="bg-white px-6 py-6 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Informasi Dasar</p>
                            <div class="mt-2 space-y-2">
                                <div class="flex justify-between text-sm italic">
                                    <span class="text-gray-400">Tipe Aktifitas:</span>
                                    <span id="detailType" class="font-bold text-gray-800"></span>
                                </div>
                                <div class="flex justify-between text-sm italic">
                                    <span class="text-gray-400">Dilakukan Oleh:</span>
                                    <span id="detailUser" class="font-bold text-gray-800"></span>
                                </div>
                                <div class="flex justify-between text-sm italic">
                                    <span class="text-gray-400">Ukuran File:</span>
                                    <span id="detailSize" class="font-bold text-gray-800"></span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu & Status</p>
                            <div class="mt-2 space-y-2">
                                <div class="flex justify-between text-sm italic">
                                    <span class="text-gray-400">Waktu Eksekusi:</span>
                                    <span id="detailTime" class="font-bold text-gray-800"></span>
                                </div>
                                <div class="flex justify-between text-sm italic">
                                    <span class="text-gray-400">Total Tabel:</span>
                                    <span id="detailTableCount" class="font-bold text-gray-800"></span>
                                </div>
                                <div class="flex justify-between text-sm italic">
                                    <span class="text-gray-400">Status:</span>
                                    <span id="detailStatus" class="font-bold"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="errorSection" class="hidden">
                        <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-2">Pesan Error</p>
                        <div class="p-4 bg-red-50 border border-red-100 rounded-xl text-xs text-red-700 font-mono overflow-x-auto" id="detailError"></div>
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tabel-tabel Terkait</p>
                        <div class="flex flex-wrap gap-1.5 p-4 bg-gray-50 rounded-2xl border border-gray-100 max-h-48 overflow-y-auto custom-scrollbar" id="detailTables">
                            {{-- Tables JS injection --}}
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button onclick="hideActivityDetail()" class="px-5 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all shadow-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Upload Restore --}}
    <div id="uploadRestoreModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('uploadRestoreModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form action="{{ route('kesiswaan.database.restore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b border-gray-100">
                        <h3 class="text-lg font-black text-gray-800 uppercase tracking-tighter">Upload SQL untuk Restore</h3>
                        <button type="button" onclick="document.getElementById('uploadRestoreModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="bg-white px-6 py-6 space-y-4">
                        <div class="p-4 bg-amber-50 rounded-xl border border-amber-100 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-black text-amber-800 uppercase italic">Peringatan Penting</h3>
                                    <div class="mt-2 text-xs text-amber-700">
                                        <p>Proses restore akan menimpa data yang ada saat ini. Pastikan file SQL valid dan berasal dari sumber terpercaya.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Pilih File SQL (.sql)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-2xl hover:border-red-400 transition-colors bg-gray-50/50">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 items-center justify-center">
                                        <label for="backup_file" class="relative cursor-pointer bg-white rounded-md font-bold text-red-600 hover:text-red-500 focus-within:outline-none px-2">
                                            <span>Klik untuk Upload</span>
                                            <input id="backup_file" name="backup_file" type="file" class="sr-only" accept=".sql" required onchange="updateFileName(this)">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-400" id="selectedFileName">Maksimal 10MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('uploadRestoreModal').classList.add('hidden')" class="px-5 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all shadow-sm">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-red-600 rounded-xl text-sm font-bold text-white hover:bg-red-700 transition-all shadow-md">Jalankan Restore</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function showActivityDetail(activity) {
                document.getElementById('detailType').innerText = activity.type.toUpperCase();
                document.getElementById('detailUser').innerText = activity.user.name;
                document.getElementById('detailSize').innerText = activity.file_size ? (activity.file_size / 1024).toFixed(2) + ' KB' : '-';
                document.getElementById('detailTime').innerText = new Date(activity.created_at).toLocaleString('id-ID');
                document.getElementById('detailTableCount').innerText = activity.tables_count || '-';
                
                const statusSpan = document.getElementById('detailStatus');
                statusSpan.innerText = activity.status.toUpperCase();
                statusSpan.className = 'font-bold ' + (activity.status === 'success' ? 'text-green-600' : 'text-red-600');

                const errorSection = document.getElementById('errorSection');
                if (activity.status === 'failed') {
                    errorSection.classList.remove('hidden');
                    document.getElementById('detailError').innerText = activity.error_message || 'Tidak ada rincian kesalahan.';
                } else {
                    errorSection.classList.add('hidden');
                }

                const tablesContainer = document.getElementById('detailTables');
                tablesContainer.innerHTML = '';
                if (activity.details && activity.details.tables) {
                    activity.details.tables.forEach(table => {
                        const span = document.createElement('span');
                        span.className = 'px-2 py-1 bg-white border border-gray-100 text-gray-600 rounded-lg text-xs font-bold shadow-sm';
                        span.innerText = table;
                        tablesContainer.appendChild(span);
                    });
                } else {
                    tablesContainer.innerHTML = '<span class="text-gray-400 italic text-xs">Data tabel tidak tersedia.</span>';
                }

                document.getElementById('activityDetailModal').classList.remove('hidden');
            }

            function hideActivityDetail() {
                document.getElementById('activityDetailModal').classList.add('hidden');
            }

            function updateFileName(input) {
                const display = document.getElementById('selectedFileName');
                if (input.files.length > 0) {
                    display.innerText = input.files[0].name;
                    display.className = 'text-xs font-bold text-green-600';
                } else {
                    display.innerText = 'Maksimal 10MB';
                    display.className = 'text-xs text-gray-400';
                }
            }
        </script>
    @endpush
</x-app-layout>
