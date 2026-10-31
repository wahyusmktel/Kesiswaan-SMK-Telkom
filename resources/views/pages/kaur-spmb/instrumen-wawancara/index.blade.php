<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Setting Instrumen Wawancara SPMB</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="instrumenWawancaraPage()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- FLASH NOTIFICATIONS --}}
            @if(session('success'))
                <div class="flex items-center justify-between p-3.5 text-xs font-medium text-emerald-800 bg-emerald-50 rounded-lg border border-emerald-200 shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center justify-between p-3.5 text-xs font-medium text-red-800 bg-red-50 rounded-lg border border-red-200 shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
                </div>
            @endif

            {{-- KPI STATS CARDS --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-sm">
                    <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Instrumen Siswa</p>
                    <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $totalSiswa }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Pertanyaan calon siswa</p>
                </div>

                <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-sm">
                    <p class="text-[11px] font-medium text-blue-600 uppercase tracking-wider">Instrumen Orang Tua</p>
                    <h3 class="text-xl font-bold text-blue-700 mt-0.5">{{ $totalOrtu }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Pertanyaan orang tua/wali</p>
                </div>

                <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-sm">
                    <p class="text-[11px] font-medium text-emerald-600 uppercase tracking-wider">Pertanyaan Aktif</p>
                    <h3 class="text-xl font-bold text-emerald-700 mt-0.5">{{ $totalAktif }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Kategori {{ $kategori === 'siswa' ? 'Siswa' : 'Orang Tua' }}</p>
                </div>

                <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-sm">
                    <p class="text-[11px] font-medium text-rose-600 uppercase tracking-wider">Total Biaya Pendidikan</p>
                    <h3 class="text-base font-bold text-rose-700 mt-0.5">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $spmbFees->count() }} Komponen Biaya SPMB</p>
                </div>
            </div>

            {{-- TOOLBAR & TABS --}}
            <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    {{-- Tab Switcher --}}
                    <div class="inline-flex rounded-md border border-slate-300 bg-slate-100 p-0.5">
                        <a href="{{ route('kaur-spmb.administrasi.instrumen-wawancara.index', ['kategori' => 'siswa', 'search' => $search]) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded {{ $kategori === 'siswa' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                            <svg class="w-3.5 h-3.5 {{ $kategori === 'siswa' ? 'text-red-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Wawancara Calon Siswa ({{ $totalSiswa }})
                        </a>
                        <a href="{{ route('kaur-spmb.administrasi.instrumen-wawancara.index', ['kategori' => 'orang_tua', 'search' => $search]) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded {{ $kategori === 'orang_tua' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                            <svg class="w-3.5 h-3.5 {{ $kategori === 'orang_tua' ? 'text-blue-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Wawancara Orang Tua ({{ $totalOrtu }})
                        </a>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Search --}}
                        <form method="GET" action="{{ route('kaur-spmb.administrasi.instrumen-wawancara.index') }}" class="flex items-center gap-1">
                            <input type="hidden" name="kategori" value="{{ $kategori }}">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari pertanyaan..."
                                class="text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 w-44">
                            @if($search)
                                <a href="{{ route('kaur-spmb.administrasi.instrumen-wawancara.index', ['kategori' => $kategori]) }}"
                                    class="p-1.5 text-slate-400 hover:text-red-600 rounded bg-slate-100">
                                    &times;
                                </a>
                            @endif
                        </form>

                        {{-- Tombol Tambah Pertanyaan --}}
                        <button type="button" @click="openAddModal('{{ $kategori }}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Pertanyaan Baru
                        </button>
                    </div>
                </div>

                {{-- INFORMASI BIAYA PENDIDIKAN REFERENCE (KHUSUS TAB ORTU) --}}
                @if($kategori === 'orang_tua')
                    <div class="mt-3 p-3.5 rounded-lg bg-blue-50 border border-blue-200 text-xs text-blue-900 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold flex items-center gap-1.5 text-blue-950">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Sinkronisasi Rincian Biaya Pendidikan Calon Siswa (Terkoneksi ke Master Super Admin)
                            </span>
                            <span class="font-bold text-blue-800 text-[11px] bg-white px-2 py-0.5 rounded border border-blue-200">
                                Total: Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                            </span>
                        </div>
                        <p class="text-[11px] text-blue-800">
                            Tabel rincian biaya ini otomatis tampil di bagian atas formulir wawancara orang tua yang diakses guru piket:
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
                            @foreach($spmbFees as $fee)
                                <div class="bg-white p-2 rounded border border-blue-100">
                                    <div class="text-[10px] text-slate-500">{{ $fee->name }}</div>
                                    <div class="font-bold text-slate-800 mt-0.5">Rp {{ number_format($fee->amount, 0, ',', '.') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- TABEL INSTRUMEN PERTANYAAN --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-slate-700">
                        <thead class="text-[11px] font-bold uppercase tracking-wider text-slate-600 bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                                <th scope="col" class="px-4 py-3">Pertanyaan &amp; Petunjuk</th>
                                <th scope="col" class="px-4 py-3 text-center w-28">Tipe Input</th>
                                <th scope="col" class="px-4 py-3">Pilihan Opsi / Jawaban</th>
                                <th scope="col" class="px-4 py-3 text-center w-20">Wajib</th>
                                <th scope="col" class="px-4 py-3 text-center w-20">Status</th>
                                <th scope="col" class="px-4 py-3 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($pertanyaans as $p)
                                <tr class="hover:bg-slate-50/80 transition-colors {{ !$p->is_active ? 'opacity-60 bg-slate-50/50' : '' }}">
                                    <td class="px-4 py-3 text-center font-bold text-slate-500">
                                        {{ $p->nomor_urut }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900 leading-snug">
                                            {{ $p->pertanyaan }}
                                        </div>
                                        @if($p->petunjuk)
                                            <div class="text-[11px] text-slate-500 italic mt-0.5">
                                                {{ $p->petunjuk }}
                                            </div>
                                        @endif
                                        @if($p->is_readonly)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9.5px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 mt-1">
                                                Otomatis / Readonly
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @if($p->tipe_input === 'radio')
                                            <span class="px-2 py-0.5 rounded text-[10.5px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Radio</span>
                                        @elseif($p->tipe_input === 'checkbox')
                                            <span class="px-2 py-0.5 rounded text-[10.5px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">Checkbox</span>
                                        @elseif($p->tipe_input === 'scale_1_5')
                                            <span class="px-2 py-0.5 rounded text-[10.5px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">Skala 1-5</span>
                                        @elseif($p->tipe_input === 'file_upload')
                                            <span class="px-2 py-0.5 rounded text-[10.5px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Upload Foto</span>
                                        @elseif($p->tipe_input === 'textarea')
                                            <span class="px-2 py-0.5 rounded text-[10.5px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Uraian / Esai</span>
                                        @elseif($p->tipe_input === 'date')
                                            <span class="px-2 py-0.5 rounded text-[10.5px] font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200">Tanggal</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10.5px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">Teks</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($p->pilihan_opsi && is_array($p->pilihan_opsi))
                                            <div class="flex flex-wrap gap-1 max-w-md">
                                                @foreach(array_slice($p->pilihan_opsi, 0, 5) as $opsi)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700 border border-slate-200">
                                                        {{ $opsi }}
                                                    </span>
                                                @endforeach
                                                @if(count($p->pilihan_opsi) > 5)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-slate-200 text-slate-600 font-semibold">
                                                        +{{ count($p->pilihan_opsi) - 5 }} opsi lainnya
                                                    </span>
                                                @endif
                                                @if($p->has_other)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-amber-50 text-amber-700 border border-amber-200 font-medium">
                                                        + Other / Lainnya
                                                    </span>
                                                @endif
                                            </div>
                                        @elseif($p->tipe_input === 'scale_1_5')
                                            <span class="text-[11px] text-slate-500 italic">Nilai skor rating 1 (Rendah) s.d 5 (Sangat Tinggi)</span>
                                        @elseif($p->tipe_input === 'file_upload')
                                            <span class="text-[11px] text-slate-500 italic">Maksimal file 10 MB (JPG, PNG)</span>
                                        @else
                                            <span class="text-[11px] text-slate-400 italic">Input bebas</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @if($p->is_required)
                                            <span class="font-bold text-red-600">Wajib (*)</span>
                                        @else
                                            <span class="text-slate-400">Opsional</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <form method="POST" action="{{ route('kaur-spmb.administrasi.instrumen-wawancara.toggle', $p) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="px-2 py-0.5 rounded text-[10.5px] font-semibold {{ $p->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' }}"
                                                title="Klik untuk toggle aktif/nonaktif">
                                                {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1">
                                            {{-- Edit --}}
                                            <button type="button" @click="openEditModal({{ json_encode($p) }})"
                                                class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded transition-colors"
                                                title="Edit Pertanyaan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            {{-- Hapus (Kecuali jika readonly bawaan) --}}
                                            @if(!$p->is_readonly)
                                                <form method="POST" action="{{ route('kaur-spmb.administrasi.instrumen-wawancara.destroy', $p) }}"
                                                    onsubmit="return confirm('Hapus pertanyaan nomor {{ $p->nomor_urut }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                                                        title="Hapus Pertanyaan">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                        Tidak ada pertanyaan instrumen yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- MODAL TAMBAH / EDIT PERTANYAAN --}}
        <template x-teleport="body">
            <div x-show="modal.open"
                @click="modal.open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-[9999]"
                style="display: none;">

                <div class="bg-white rounded-lg max-w-xl w-full shadow-2xl overflow-hidden" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-slate-200 bg-slate-50">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900" x-text="modal.isEdit ? 'Edit Pertanyaan Instrumen' : 'Tambah Pertanyaan Instrumen Baru'"></h3>
                            <p class="text-xs text-slate-500" x-text="'Kategori: Wawancara ' + (modal.form.kategori === 'siswa' ? 'Calon Siswa' : 'Calon Orang Tua')"></p>
                        </div>
                        <button type="button" @click="modal.open = false" class="text-slate-400 hover:text-red-600 text-lg">&times;</button>
                    </div>

                    <form :action="modal.actionUrl" method="POST" class="p-5 space-y-4">
                        @csrf
                        <template x-if="modal.isEdit">
                            @method('PUT')
                        </template>

                        <input type="hidden" name="kategori" x-model="modal.form.kategori">

                        {{-- Teks Pertanyaan --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Bunyi Pertanyaan / Pernyataan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="pertanyaan" x-model="modal.form.pertanyaan" required rows="2"
                                placeholder="Contoh: Cita-cita calon siswa di masa mendatang:"
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500"></textarea>
                        </div>

                        {{-- Petunjuk Pengisian --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Petunjuk Pengisian (Opsional)
                            </label>
                            <input type="text" name="petunjuk" x-model="modal.form.petunjuk"
                                placeholder="Contoh: Boleh memilih lebih dari satu jawaban"
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            {{-- Tipe Input --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Tipe Input Pertanyaan <span class="text-red-500">*</span>
                                </label>
                                <select name="tipe_input" x-model="modal.form.tipe_input" required
                                    class="w-full text-xs border border-slate-300 rounded-md py-2 px-3 focus:border-red-500 focus:ring-red-500">
                                    <option value="text">Teks Singkat</option>
                                    <option value="textarea">Uraian / Esai (Teks Panjang)</option>
                                    <option value="radio">Pilihan Tunggal (Radio)</option>
                                    <option value="checkbox">Pilihan Ganda (Checkbox)</option>
                                    <option value="scale_1_5">Skala Penilaian (1 - 5)</option>
                                    <option value="file_upload">Upload Foto / Berkas</option>
                                    <option value="date">Tanggal</option>
                                </select>
                            </div>

                            {{-- Bobot Skor --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Bobot Skor Nilai
                                </label>
                                <input type="number" min="0" max="100" name="bobot" x-model="modal.form.bobot"
                                    class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>

                        {{-- Pilihan Opsi (Jika Radio / Checkbox) --}}
                        <div x-show="['radio', 'checkbox'].includes(modal.form.tipe_input)">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Daftar Pilihan Opsi Jawaban (1 Opsi Per Baris)
                            </label>
                            <textarea name="pilihan_opsi" x-model="modal.form.pilihan_opsi" rows="4"
                                placeholder="PNS&#10;TNI&#10;Polri&#10;Wiraswasta&#10;Karyawan Swasta"
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 font-mono"></textarea>
                            <p class="text-[10px] text-slate-400 mt-1">Tekan enter untuk setiap opsi jawaban baru.</p>
                        </div>

                        {{-- Checkbox Toggles --}}
                        <div class="space-y-2 pt-1 border-t border-slate-200">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_required" value="1" x-model="modal.form.is_required"
                                    class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                <span class="text-xs font-medium text-slate-700">Wajib Diisi (Required)</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer" x-show="['radio', 'checkbox'].includes(modal.form.tipe_input)">
                                <input type="checkbox" name="has_other" value="1" x-model="modal.form.has_other"
                                    class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                <span class="text-xs font-medium text-slate-700">Sediakan opsi input bebas "Other / Lainnya"</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                            <button type="button" @click="modal.open = false"
                                class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-md">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm">
                                Simpan Pertanyaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>

    {{-- ALPINE JAVASCRIPT --}}
    <script>
        function instrumenWawancaraPage() {
            return {
                modal: {
                    open: false,
                    isEdit: false,
                    actionUrl: '',
                    form: {
                        kategori: 'siswa',
                        pertanyaan: '',
                        petunjuk: '',
                        tipe_input: 'radio',
                        pilihan_opsi: '',
                        has_other: false,
                        is_required: true,
                        bobot: 5
                    }
                },

                openAddModal(kategori) {
                    this.modal.isEdit = false;
                    this.modal.actionUrl = '{{ route("kaur-spmb.administrasi.instrumen-wawancara.store") }}';
                    this.modal.form = {
                        kategori: kategori,
                        pertanyaan: '',
                        petunjuk: '',
                        tipe_input: 'radio',
                        pilihan_opsi: '',
                        has_other: false,
                        is_required: true,
                        bobot: 5
                    };
                    this.modal.open = true;
                },

                openEditModal(p) {
                    this.modal.isEdit = true;
                    this.modal.actionUrl = '{{ url("kaur-spmb/administrasi/instrumen-wawancara") }}/' + p.id;
                    let opsiStr = '';
                    if (Array.isArray(p.pilihan_opsi)) {
                        opsiStr = p.pilihan_opsi.join('\n');
                    }
                    this.modal.form = {
                        kategori: p.kategori,
                        pertanyaan: p.pertanyaan,
                        petunjuk: p.petunjuk || '',
                        tipe_input: p.tipe_input,
                        pilihan_opsi: opsiStr,
                        has_other: Boolean(p.has_other),
                        is_required: Boolean(p.is_required),
                        bobot: p.bobot || 5
                    };
                    this.modal.open = true;
                }
            };
        }
    </script>
</x-app-layout>
