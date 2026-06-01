<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Nilai Ujian Akhir Semester</h2>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kelola Nilai Ujian Semester</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Tahun pelajaran aktif:
                        <span class="font-semibold text-gray-800">
                            {{ $tahunAktif ? $tahunAktif->tahun . ' - ' . $tahunAktif->semester : 'Belum diset' }}
                        </span>
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-red-50 text-red-700 border border-red-100 text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h13M9 5h13M5 5h.01M5 11h.01M5 17h.01" />
                    </svg>
                    Import format LMS
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm">
                    <div class="font-bold mb-1">Validasi gagal</div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-lg bg-red-600 text-white flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Tambah Ujian</h3>
                            <p class="text-xs text-gray-500">Tahun dan semester otomatis dari data aktif.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('kesiswaan.ujian-semester.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Ujian</label>
                            <input type="text" name="nama_ujian" value="{{ old('nama_ujian') }}" placeholder="SAS Genap 2026"
                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" required>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Ujian</label>
                                <input type="text" name="kode_ujian" value="{{ old('kode_ujian') }}" placeholder="SAS-2026"
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                                <input type="date" name="tanggal_ujian" value="{{ old('tanggal_ujian') }}"
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="3" placeholder="Catatan ujian..."
                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">{{ old('keterangan') }}</textarea>
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-500 text-white rounded-lg font-semibold shadow-sm transition-colors" {{ !$tahunAktif ? 'disabled' : '' }}>
                            Simpan Ujian
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 xl:col-span-2">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-lg bg-gray-900 text-white flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 4v12m0 0l-4-4m4 4l4-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Import Nilai Excel</h3>
                            <p class="text-xs text-gray-500">Upload file dari LMS tanpa mengubah format. Header dibaca dari baris 7, data mulai baris 8.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('kesiswaan.ujian-semester.import') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-end">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Ujian</label>
                            <select name="ujian_semester_id" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" required>
                                <option value="">Pilih ujian</option>
                                @foreach ($ujianOptions as $ujian)
                                    <option value="{{ $ujian->id }}" {{ old('ujian_semester_id', request('ujian_id')) == $ujian->id ? 'selected' : '' }}>
                                        {{ $ujian->nama_ujian }} - {{ $ujian->tahunPelajaran?->tahun }} {{ $ujian->semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Mata Pelajaran</label>
                            <select name="mata_pelajaran_id" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" required>
                                <option value="">Pilih mata pelajaran</option>
                                @foreach ($mataPelajaran as $mapel)
                                    <option value="{{ $mapel->id }}" {{ old('mata_pelajaran_id', request('mata_pelajaran_id')) == $mapel->id ? 'selected' : '' }}>
                                        {{ $mapel->nama_mapel }}{{ $mapel->kelas ? ' - ' . $mapel->kelas->nama_kelas : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">File Nilai</label>
                            <input type="file" name="file_nilai" accept=".xls,.xlsx,.csv"
                                class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none file:mr-4 file:py-2.5 file:px-4 file:rounded-l-lg file:border-0 file:bg-gray-100 file:text-gray-700 file:font-semibold hover:file:bg-gray-200" required>
                        </div>
                        <div class="lg:col-span-2 flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-lg font-semibold shadow-sm transition-colors">
                                Import Nilai
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-gray-900">Daftar Ujian Semester</h3>
                        <p class="text-xs text-gray-500">Pilih detail untuk melihat data nilai yang sudah diimport.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-6 py-3 text-left font-bold">Ujian</th>
                                <th class="px-6 py-3 text-left font-bold">Tahun/Semester</th>
                                <th class="px-6 py-3 text-left font-bold">Tanggal</th>
                                <th class="px-6 py-3 text-left font-bold">Nilai</th>
                                <th class="px-6 py-3 text-right font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($ujians as $ujian)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $ujian->nama_ujian }}</div>
                                        <div class="text-xs text-gray-500">{{ $ujian->kode_ujian ?: '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $ujian->tahunPelajaran?->tahun }} - {{ $ujian->semester }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $ujian->tanggal_ujian?->format('d/m/Y') ?: '-' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $ujian->nilai_count }} data</div>
                                        <div class="text-xs text-gray-500">Import terakhir: {{ $ujian->nilai_max_imported_at ? \Carbon\Carbon::parse($ujian->nilai_max_imported_at)->format('d/m/Y H:i') : '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('kesiswaan.ujian-semester.index', ['ujian_id' => $ujian->id]) }}"
                                                class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs transition-colors">
                                                Detail
                                            </a>
                                            <form action="{{ route('kesiswaan.ujian-semester.destroy', $ujian) }}" method="POST" onsubmit="return confirm('Hapus ujian dan semua nilai importnya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-xs transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada data ujian semester.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($ujians->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">{{ $ujians->links() }}</div>
                @endif
            </div>

            @if ($selectedUjian)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div class="text-xs font-bold uppercase text-gray-500">Total Nilai</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ $nilaiStats['total'] }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div class="text-xs font-bold uppercase text-gray-500">Mata Pelajaran</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ $nilaiStats['mapel'] }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div class="text-xs font-bold uppercase text-gray-500">Cocok Master</div>
                        <div class="text-2xl font-bold text-green-700 mt-1">{{ $nilaiStats['matched'] }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div class="text-xs font-bold uppercase text-gray-500">Belum Cocok</div>
                        <div class="text-2xl font-bold text-red-700 mt-1">{{ $nilaiStats['unmatched'] }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-gray-900">Detail Nilai: {{ $selectedUjian->nama_ujian }}</h3>
                                <p class="text-xs text-gray-500">{{ $selectedUjian->tahunPelajaran?->tahun }} - {{ $selectedUjian->semester }}</p>
                            </div>
                            <form method="GET" action="{{ route('kesiswaan.ujian-semester.index') }}" class="flex flex-col sm:flex-row gap-3">
                                <input type="hidden" name="ujian_id" value="{{ $selectedUjian->id }}">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIS, nama, kelas..."
                                    class="rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                                <select name="mata_pelajaran_id" class="rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                                    <option value="">Semua mapel</option>
                                    @foreach ($mataPelajaran as $mapel)
                                        <option value="{{ $mapel->id }}" {{ request('mata_pelajaran_id') == $mapel->id ? 'selected' : '' }}>
                                            {{ $mapel->nama_mapel }}{{ $mapel->kelas ? ' - ' . $mapel->kelas->nama_kelas : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg font-semibold text-sm">Filter</button>
                            </form>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-6 py-3 text-left font-bold">No</th>
                                    <th class="px-6 py-3 text-left font-bold">Kode Peserta/NIS</th>
                                    <th class="px-6 py-3 text-left font-bold">Nama Lengkap</th>
                                    <th class="px-6 py-3 text-left font-bold">Kelas</th>
                                    <th class="px-6 py-3 text-left font-bold">Mapel</th>
                                    <th class="px-6 py-3 text-right font-bold">Nilai</th>
                                    <th class="px-6 py-3 text-left font-bold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($nilai as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 text-gray-500">{{ $item->nomor_urut ?? '-' }}</td>
                                        <td class="px-6 py-3 font-mono text-gray-700">{{ $item->kode_peserta }}</td>
                                        <td class="px-6 py-3 font-semibold text-gray-900">{{ $item->nama_lengkap }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $item->kelas ?: '-' }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $item->mataPelajaran?->nama_mapel ?: '-' }}</td>
                                        <td class="px-6 py-3 text-right font-bold text-gray-900">{{ $item->nilai !== null ? number_format((float) $item->nilai, 2, ',', '.') : '-' }}</td>
                                        <td class="px-6 py-3">
                                            @if ($item->master_siswa_id)
                                                <span class="inline-flex px-2 py-1 rounded-full bg-green-50 text-green-700 border border-green-100 text-xs font-bold">Cocok</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 rounded-full bg-red-50 text-red-700 border border-red-100 text-xs font-bold">NIS belum cocok</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">Belum ada nilai untuk filter ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($nilai && $nilai->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100">{{ $nilai->links() }}</div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
