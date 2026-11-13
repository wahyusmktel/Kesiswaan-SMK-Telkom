<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Daftar Pemilih Tetap (DPT) &amp; Barcode</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola data pemilih, generate token barcode unik 1D fisik, dan cetak kartu pemilih</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('osis.pilketos.dpt.print', request()->query()) }}" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Kartu Berbarcode</span>
                </a>
                <button type="button" onclick="document.getElementById('modal-generate-dpt').classList.remove('hidden')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Generate dari Siswa</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Summary Badges -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total DPT Terdaftar</span>
                    <div class="text-3xl font-black text-gray-800 mt-1 font-mono">{{ number_format($totalDpt) }}</div>
                    <div class="text-[11px] text-gray-500 mt-1">Pemilih sah terdaftar dalam sistem</div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Sudah Memilih</span>
                    <div class="text-3xl font-black text-emerald-600 mt-1 font-mono">{{ number_format($totalSudah) }}</div>
                    <div class="text-[11px] text-gray-500 mt-1">
                        {{ $totalDpt > 0 ? round(($totalSudah / $totalDpt) * 100, 1) : 0 }}% Partisipasi pemilih
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Belum Memilih</span>
                    <div class="text-3xl font-black text-amber-600 mt-1 font-mono">{{ number_format($totalBelum) }}</div>
                    <div class="text-[11px] text-gray-500 mt-1">Belum melakukan scan barcode di bilik</div>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <form method="GET" action="{{ route('osis.pilketos.dpt.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, atau barcode..." class="w-full px-4 py-2 rounded-xl border-gray-200 text-xs focus:border-rose-500 focus:ring focus:ring-rose-200">
                    </div>
                    <div>
                        <select name="status" class="w-full px-4 py-2 rounded-xl border-gray-200 text-xs focus:border-rose-500 focus:ring focus:ring-rose-200">
                            <option value="">Semua Status Memilih</option>
                            <option value="belum_memilih" {{ request('status') === 'belum_memilih' ? 'selected' : '' }}>Belum Memilih</option>
                            <option value="sudah_memilih" {{ request('status') === 'sudah_memilih' ? 'selected' : '' }}>Sudah Memilih</option>
                        </select>
                    </div>
                    <div>
                        <select name="kelas" class="w-full px-4 py-2 rounded-xl border-gray-200 text-xs focus:border-rose-500 focus:ring focus:ring-rose-200">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $k)
                                @if($k)
                                    <option value="{{ $k }}" {{ request('kelas') === $k ? 'selected' : '' }}>{{ $k }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-bold transition-colors">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'kelas']))
                            <a href="{{ route('osis.pilketos.dpt.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table DPT -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider font-bold">
                            <tr>
                                <th class="px-6 py-4">No</th>
                                <th class="px-6 py-4">Pemilih (Siswa)</th>
                                <th class="px-6 py-4">Kelas</th>
                                <th class="px-6 py-4">Barcode Fisik (Code 128)</th>
                                <th class="px-6 py-4">Status Memilih</th>
                                <th class="px-6 py-4">Waktu &amp; Bilik</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($dpts as $index => $dpt)
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="px-6 py-4 font-mono text-gray-400">
                                        {{ $dpts->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $dpt->nama_pemilih }}</div>
                                        <div class="text-[11px] text-gray-400 font-mono">NIS: {{ $dpt->nis }} &bull; {{ $dpt->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-700">
                                        <span class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 font-mono text-[11px]">
                                            {{ $dpt->kelas }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="bg-white p-2 rounded-xl border border-gray-200 inline-block">
                                            <div class="w-36 overflow-hidden">
                                                {!! $dpt->barcode_svg !!}
                                            </div>
                                            <div class="text-[10px] font-mono text-gray-500 font-bold text-center mt-1">
                                                {{ $dpt->barcode_token }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($dpt->status_memilih === 'sudah_memilih')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Sudah Memilih
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Belum Memilih
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-mono text-[11px] text-gray-500">
                                        @if($dpt->status_memilih === 'sudah_memilih')
                                            <div>{{ $dpt->waktu_memilih?->format('d M Y, H:i') ?? '-' }} WIB</div>
                                            <div class="text-rose-600 font-bold">{{ $dpt->bilik_id ?? 'Bilik Digital' }}</div>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($dpt->status_memilih === 'sudah_memilih')
                                                <form action="{{ route('osis.pilketos.dpt.reset', $dpt) }}" method="POST" onsubmit="return confirm('Reset status memilih siswa ini kembali ke Belum Memilih?')">
                                                    @csrf
                                                    <button type="submit" class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 transition-colors" title="Reset Status">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('osis.pilketos.dpt.destroy', $dpt) }}" method="POST" onsubmit="return confirm('Hapus siswa ini dari DPT?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg bg-gray-100 hover:bg-rose-50 text-gray-500 hover:text-rose-600 transition-colors" title="Hapus DPT">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        <p class="font-medium text-sm">Belum ada data DPT dalam periode pemilihan ini.</p>
                                        <p class="text-xs text-gray-400 mt-1">Klik tombol <strong>"Generate dari Siswa"</strong> di atas untuk mengimpor pemilih.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($dpts->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $dpts->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Modal Generate DPT -->
    <div id="modal-generate-dpt" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-base text-gray-800">Generate DPT Otomatis</h3>
                <button type="button" onclick="document.getElementById('modal-generate-dpt').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <p class="text-xs text-gray-500">
                Sistem akan membuatkan akun pemilih beserta Barcode Fisik Unik 1D (Code 128) untuk setiap siswa aktif SMK Telkom Lampung yang belum terdaftar di DPT.
            </p>

            <form action="{{ route('osis.pilketos.dpt.generate') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Pilih Sumber Siswa:</label>
                    <div class="space-y-2 text-xs">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="source" value="all" checked class="text-rose-600 focus:ring-rose-500" onchange="document.getElementById('rombel-select-wrapper').classList.add('hidden')">
                            <div>
                                <div class="font-bold text-gray-800">Seluruh Siswa Aktif</div>
                                <div class="text-gray-400 text-[11px]">Generate otomatis untuk semua siswa di sekolah</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="source" value="rombel" class="text-rose-600 focus:ring-rose-500" onchange="document.getElementById('rombel-select-wrapper').classList.remove('hidden')">
                            <div>
                                <div class="font-bold text-gray-800">Per Kelas / Rombel Tertentu</div>
                                <div class="text-gray-400 text-[11px]">Generate hanya untuk rombel yang dipilih</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="rombel-select-wrapper" class="hidden">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Rombel / Kelas</label>
                    <select name="rombel_id" class="w-full px-3 py-2 rounded-xl border-gray-200 text-xs">
                        <option value="">-- Pilih Rombel --</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}">{{ $r->nama_rombel }} ({{ $r->kelas?->nama_kelas ?? 'Kelas' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-generate-dpt').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-500/20">
                        Proses Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
