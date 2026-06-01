<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Nilai Ujian Akhir Semester</h2>
    </x-slot>

    <div class="py-6" x-data="{ detail: null }">
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
                @if ($selectedUjian)
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('kesiswaan.ujian-semester.export', request()->only(['ujian_id', 'ujian_mapel_id', 'kelas', 'sort'])) }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg font-semibold text-sm shadow-sm">
                            Export Excel
                        </a>
                        @if (request('ujian_mapel_id'))
                            <a href="{{ route('kesiswaan.ujian-semester.report-pdf', request()->only(['ujian_id', 'ujian_mapel_id', 'kelas', 'sort'])) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg font-semibold text-sm shadow-sm">
                                Print PDF
                            </a>
                        @endif
                    </div>
                @endif
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
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 xl:col-span-2" x-data="ujianForm({{ Js::from($mapelOptions) }})">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-lg bg-red-600 text-white flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Tambah Ujian dan Mata Pelajaran</h3>
                            <p class="text-xs text-gray-500">Dropdown import hanya memakai mapel yang didaftarkan di sini.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('kesiswaan.ujian-semester.store') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
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
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Daftar Mata Pelajaran Ujian</label>
                            <div class="space-y-3">
                                <template x-for="(row, index) in rows" :key="row.key">
                                    <div class="grid grid-cols-1 lg:grid-cols-[1fr_140px_44px] gap-3 items-start">
                                        <div>
                                            <input type="text" :name="`mapels[${index}][nama_mapel]`" x-model="row.query"
                                                placeholder="Ketik nama mata pelajaran..."
                                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" required>

                                            <div x-show="hasMatches(row)" class="mt-2 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                                                Mata pelajaran sudah tersedia, silakan pilih di bawah ini.
                                            </div>

                                            <div x-show="suggestions(row).length" class="mt-2 flex flex-wrap gap-2">
                                                <template x-for="option in suggestions(row)" :key="option.nama">
                                                    <button type="button" @click="select(row, option)"
                                                        class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 hover:border-red-300 hover:bg-red-50 text-xs font-semibold text-gray-700">
                                                        <span x-text="option.label"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                        <input type="number" min="1" max="500" :name="`mapels[${index}][jumlah_soal]`"
                                            placeholder="Jumlah soal"
                                            class="rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" required>
                                        <button type="button" @click="remove(index)" x-show="rows.length > 1"
                                            class="h-10 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 font-bold">-</button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="add()" class="mt-3 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm">
                                + Tambah Baris Mapel
                            </button>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="2" placeholder="Catatan ujian..."
                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">{{ old('keterangan') }}</textarea>
                        </div>

                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-500 text-white rounded-lg font-semibold shadow-sm transition-colors" {{ !$tahunAktif ? 'disabled' : '' }}>
                            Simpan Ujian
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 mb-1">Import Nilai Excel</h3>
                    <p class="text-xs text-gray-500 mb-5">Pilih detail ujian dulu, lalu upload file LMS untuk mapel terdaftar.</p>

                    @if ($selectedUjian)
                        <form method="POST" action="{{ route('kesiswaan.ujian-semester.import') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="hidden" name="ujian_semester_id" value="{{ $selectedUjian->id }}">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Ujian</label>
                                <div class="px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm font-semibold text-gray-700">
                                    {{ $selectedUjian->nama_ujian }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Mata Pelajaran</label>
                                <select name="ujian_semester_mapel_id" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" required>
                                    <option value="">Pilih mata pelajaran ujian</option>
                                    @foreach ($allowedMapels as $ujianMapel)
                                        <option value="{{ $ujianMapel->id }}" {{ request('ujian_mapel_id') == $ujianMapel->id ? 'selected' : '' }}>
                                            {{ $ujianMapel->nama_mapel }} ({{ $ujianMapel->jumlah_soal }} soal)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">File Nilai</label>
                                <input type="file" name="file_nilai" accept=".xls,.xlsx,.csv"
                                    class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none file:mr-4 file:py-2.5 file:px-4 file:rounded-l-lg file:border-0 file:bg-gray-100 file:text-gray-700 file:font-semibold hover:file:bg-gray-200" required>
                            </div>
                            <button type="submit" class="w-full px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-lg font-semibold shadow-sm transition-colors">
                                Import Nilai
                            </button>
                        </form>
                    @else
                        <div class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500">
                            Klik tombol Detail pada daftar ujian untuk membuka form import sesuai ujian.
                        </div>
                    @endif
                </div>
            </div>

            @if ($selectedUjian)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data="ujianForm({{ Js::from($mapelOptions) }})">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                        <div>
                            <h3 class="font-bold text-gray-900">Mapel Terdaftar: {{ $selectedUjian->nama_ujian }}</h3>
                            <p class="text-xs text-gray-500">Tambah mapel jika ada mata pelajaran lain untuk ujian ini.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($allowedMapels as $ujianMapel)
                                <span class="px-3 py-1 rounded-full bg-red-50 text-red-700 border border-red-100 text-xs font-bold">
                                    {{ $ujianMapel->nama_mapel }}: {{ $ujianMapel->jumlah_soal }} soal
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <form method="POST" action="{{ route('kesiswaan.ujian-semester.mapel.store', $selectedUjian) }}" class="space-y-3">
                        @csrf
                        <template x-for="(row, index) in rows" :key="row.key">
                            <div class="grid grid-cols-1 lg:grid-cols-[1fr_140px_44px] gap-3 items-start">
                                <div>
                                    <input type="text" :name="`mapels[${index}][nama_mapel]`" x-model="row.query"
                                        placeholder="Ketik nama mata pelajaran..."
                                        class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" required>

                                    <div x-show="hasMatches(row)" class="mt-2 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                                        Mata pelajaran sudah tersedia, silakan pilih di bawah ini.
                                    </div>

                                    <div x-show="suggestions(row).length" class="mt-2 flex flex-wrap gap-2">
                                        <template x-for="option in suggestions(row)" :key="option.nama">
                                            <button type="button" @click="select(row, option)"
                                                class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 hover:border-red-300 hover:bg-red-50 text-xs font-semibold text-gray-700">
                                                <span x-text="option.label"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <input type="number" min="1" max="500" :name="`mapels[${index}][jumlah_soal]`" placeholder="Jumlah soal"
                                    class="rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" required>
                                <button type="button" @click="remove(index)" x-show="rows.length > 1" class="h-10 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 font-bold">-</button>
                            </div>
                        </template>
                        <div class="flex gap-2">
                            <button type="button" @click="add()" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm">+ Tambah Baris</button>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-500 text-white font-semibold text-sm">Simpan Mapel</button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Daftar Ujian Semester</h3>
                    <p class="text-xs text-gray-500">Pilih detail untuk import dan melihat data nilai.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-6 py-3 text-left font-bold">Ujian</th>
                                <th class="px-6 py-3 text-left font-bold">Tahun/Semester</th>
                                <th class="px-6 py-3 text-left font-bold">Mapel</th>
                                <th class="px-6 py-3 text-left font-bold">Nilai</th>
                                <th class="px-6 py-3 text-right font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($ujians as $ujian)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $ujian->nama_ujian }}</div>
                                        <div class="text-xs text-gray-500">{{ $ujian->tanggal_ujian?->format('d/m/Y') ?: '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $ujian->tahunPelajaran?->tahun }} - {{ $ujian->semester }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $ujian->ujianMapels->count() }} mapel</td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $ujian->nilai_count }} data</div>
                                        <div class="text-xs text-gray-500">{{ $ujian->nilai_max_imported_at ? \Carbon\Carbon::parse($ujian->nilai_max_imported_at)->format('d/m/Y H:i') : '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('kesiswaan.ujian-semester.index', ['ujian_id' => $ujian->id]) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs">Detail</a>
                                            <form action="{{ route('kesiswaan.ujian-semester.destroy', $ujian) }}" method="POST" onsubmit="return confirm('Hapus ujian dan semua nilai importnya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-xs">Hapus</button>
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
                <div class="grid grid-cols-1 md:grid-cols-4 xl:grid-cols-7 gap-4">
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><div class="text-xs font-bold uppercase text-gray-500">Total</div><div class="text-2xl font-bold text-gray-900 mt-1">{{ $nilaiStats['total'] }}</div></div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><div class="text-xs font-bold uppercase text-gray-500">Mapel</div><div class="text-2xl font-bold text-gray-900 mt-1">{{ $nilaiStats['mapel'] }}</div></div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><div class="text-xs font-bold uppercase text-gray-500">Cocok</div><div class="text-2xl font-bold text-green-700 mt-1">{{ $nilaiStats['matched'] }}</div></div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><div class="text-xs font-bold uppercase text-gray-500">Belum Cocok</div><div class="text-2xl font-bold text-red-700 mt-1">{{ $nilaiStats['unmatched'] }}</div></div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><div class="text-xs font-bold uppercase text-gray-500">Terbesar</div><div class="text-2xl font-bold text-gray-900 mt-1">{{ $nilaiStats['nilai_terbesar'] !== null ? number_format($nilaiStats['nilai_terbesar'], 2, ',', '.') : '-' }}</div></div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><div class="text-xs font-bold uppercase text-gray-500">Terkecil</div><div class="text-2xl font-bold text-gray-900 mt-1">{{ $nilaiStats['nilai_terkecil'] !== null ? number_format($nilaiStats['nilai_terkecil'], 2, ',', '.') : '-' }}</div></div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><div class="text-xs font-bold uppercase text-gray-500">Rata-rata</div><div class="text-2xl font-bold text-gray-900 mt-1">{{ $nilaiStats['rata_rata'] !== null ? number_format($nilaiStats['rata_rata'], 2, ',', '.') : '-' }}</div></div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex flex-col gap-4">
                            <div>
                                <h3 class="font-bold text-gray-900">Detail Nilai: {{ $selectedUjian->nama_ujian }}</h3>
                                <p class="text-xs text-gray-500">{{ $selectedUjian->tahunPelajaran?->tahun }} - {{ $selectedUjian->semester }}</p>
                            </div>
                            <form method="GET" action="{{ route('kesiswaan.ujian-semester.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                                <input type="hidden" name="ujian_id" value="{{ $selectedUjian->id }}">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIS, nama, kelas..." class="rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                                <select name="ujian_mapel_id" class="rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                                    <option value="">Semua mapel ujian</option>
                                    @foreach ($allowedMapels as $ujianMapel)
                                        <option value="{{ $ujianMapel->id }}" {{ request('ujian_mapel_id') == $ujianMapel->id ? 'selected' : '' }}>
                                            {{ $ujianMapel->nama_mapel }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="kelas" class="rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                                    <option value="">Semua kelas</option>
                                    @foreach ($kelasOptions as $kelas)
                                        <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                                    @endforeach
                                </select>
                                <select name="sort" class="rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                                    <option value="kelas" {{ request('sort', 'kelas') == 'kelas' ? 'selected' : '' }}>Urut kelas/no</option>
                                    <option value="nilai_desc" {{ request('sort') == 'nilai_desc' ? 'selected' : '' }}>Nilai terbesar</option>
                                    <option value="nilai_asc" {{ request('sort') == 'nilai_asc' ? 'selected' : '' }}>Nilai terkecil</option>
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
                                    <th class="px-6 py-3 text-left font-bold">NIS</th>
                                    <th class="px-6 py-3 text-left font-bold">Nama Lengkap</th>
                                    <th class="px-6 py-3 text-left font-bold">Kelas</th>
                                    <th class="px-6 py-3 text-left font-bold">Mapel</th>
                                    <th class="px-6 py-3 text-center font-bold">Benar</th>
                                    <th class="px-6 py-3 text-right font-bold">Nilai Akhir</th>
                                    <th class="px-6 py-3 text-left font-bold">Status</th>
                                    <th class="px-6 py-3 text-right font-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($nilai as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 text-gray-500">{{ $item->nomor_urut ?? '-' }}</td>
                                        <td class="px-6 py-3 font-mono text-gray-700">{{ $item->kode_peserta }}</td>
                                        <td class="px-6 py-3 font-semibold text-gray-900">{{ $item->nama_lengkap }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $item->kelas ?: '-' }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $item->nama_mapel ?: $item->ujianMapel?->nama_mapel ?: '-' }}</td>
                                        <td class="px-6 py-3 text-center font-semibold">{{ $item->jumlah_benar ?? '-' }}/{{ $item->jumlah_soal ?? '-' }}</td>
                                        <td class="px-6 py-3 text-right font-bold text-gray-900">{{ $item->nilai_akhir !== null ? number_format((float) $item->nilai_akhir, 2, ',', '.') : '-' }}</td>
                                        <td class="px-6 py-3">
                                            @if ($item->master_siswa_id)
                                                <span class="inline-flex px-2 py-1 rounded-full bg-green-50 text-green-700 border border-green-100 text-xs font-bold">Cocok</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 rounded-full bg-red-50 text-red-700 border border-red-100 text-xs font-bold">NIS belum cocok</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <button type="button"
                                                @click="detail = {{ Js::from([
                                                    'nis' => $item->kode_peserta,
                                                    'nama' => $item->nama_lengkap,
                                                    'kelas' => $item->kelas,
                                                    'mapel' => $item->nama_mapel ?: $item->ujianMapel?->nama_mapel,
                                                    'benar' => $item->jumlah_benar,
                                                    'soal' => $item->jumlah_soal,
                                                    'nilai' => $item->nilai_akhir !== null ? number_format((float) $item->nilai_akhir, 2, ',', '.') : '-',
                                                    'baris' => $item->baris_excel,
                                                    'file' => $item->nama_file,
                                                    'imported' => $item->imported_at?->format('d/m/Y H:i'),
                                                    'status' => $item->master_siswa_id ? 'Cocok master siswa' : 'NIS belum cocok',
                                                ]) }}"
                                                class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-10 text-center text-gray-500">Belum ada nilai untuk filter ini.</td>
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

        <div x-show="detail" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 w-full max-w-lg" @click.outside="detail = null">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900">Detail Nilai Siswa</h3>
                    <button type="button" @click="detail = null" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                </div>
                <div class="p-6 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><span class="text-gray-500">NIS</span><span class="font-semibold text-gray-900" x-text="detail?.nis"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-gray-500">Nama</span><span class="font-semibold text-gray-900 text-right" x-text="detail?.nama"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-gray-500">Kelas</span><span class="font-semibold text-gray-900" x-text="detail?.kelas"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-gray-500">Mata Pelajaran</span><span class="font-semibold text-gray-900 text-right" x-text="detail?.mapel"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-gray-500">Jumlah Benar</span><span class="font-semibold text-gray-900"><span x-text="detail?.benar"></span>/<span x-text="detail?.soal"></span></span></div>
                    <div class="flex justify-between gap-4"><span class="text-gray-500">Nilai Akhir</span><span class="font-bold text-red-700" x-text="detail?.nilai"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-gray-500">Baris Excel</span><span class="font-semibold text-gray-900" x-text="detail?.baris"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-gray-500">File</span><span class="font-semibold text-gray-900 text-right" x-text="detail?.file"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-gray-500">Waktu Import</span><span class="font-semibold text-gray-900" x-text="detail?.imported"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-gray-500">Status</span><span class="font-semibold text-gray-900" x-text="detail?.status"></span></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function ujianForm(mapelOptions = []) {
            return {
                options: mapelOptions,
                rows: [{ key: Date.now(), query: '' }],
                add() {
                    this.rows.push({ key: Date.now() + Math.random(), query: '' });
                },
                remove(index) {
                    this.rows.splice(index, 1);
                },
                normalized(value) {
                    return (value || '').toString().trim().toLowerCase();
                },
                suggestions(row) {
                    const query = this.normalized(row.query);

                    if (query.length < 3) {
                        return [];
                    }

                    return this.options
                        .filter((option) => option.search.includes(query) || this.normalized(option.nama) === query)
                        .slice(0, 8);
                },
                hasMatches(row) {
                    const query = this.normalized(row.query);

                    return query.length >= 3 && this.options.some((option) => this.normalized(option.nama) === query);
                },
                select(row, option) {
                    row.query = option.nama;
                }
            };
        }
    </script>
</x-app-layout>
