<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-red-600">
                    <a href="{{ route('rab.index') }}" class="hover:underline">Anggaran & Pengadaan</a>
                    <span>/</span>
                    <a href="{{ route('rab.show', $rab) }}" class="hover:underline">Dokumen RAB</a>
                    <span>/</span>
                    <a href="{{ route('rab.realisasi.show', $rab) }}" class="hover:underline">Laporan Realisasi</a>
                    <span>/</span>
                    <span>Kelola</span>
                </div>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900">
                    Kelola Laporan Realisasi & Bukti Eviden
                </h1>
                <p class="text-sm text-slate-500">
                    {{ $rab->judul_pengajuan }} &bull; TA {{ $rab->tahun_anggaran }} &bull; Nomor: {{ $realisasi->nomor_realisasi }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('rab.realisasi.show', $rab) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    Batal / Kembali
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $initialItems = $realisasi->items->map(function ($item) {
            return [
                'tanggal' => $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : now()->format('Y-m-d'),
                'uraian' => $item->uraian,
                'penerimaan' => (float) $item->penerimaan,
                'pengeluaran' => (float) $item->pengeluaran,
                'keterangan' => $item->keterangan ?? '-',
            ];
        })->toArray();

        if (empty($initialItems)) {
            $initialItems = [
                [
                    'tanggal' => now()->format('Y-m-d'),
                    'uraian' => 'Realisasi Dana ' . $rab->judul_pengajuan,
                    'penerimaan' => (float) $rab->total_anggaran,
                    'pengeluaran' => 0,
                    'keterangan' => '-',
                ]
            ];
        }
    @endphp

    <div class="py-8" x-data="realisasiEditor({{ Js::from($initialItems) }})">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('rab.realisasi.update', $rab) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                @if($errors->any())
                    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
                        <p class="font-bold text-sm">Harap periksa kembali isian formulir:</p>
                        <ul class="mt-2 list-inside list-disc text-xs space-y-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Informasi Dokumen & Pejabat Pengesah --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <h2 class="text-base font-black text-slate-900 border-b border-slate-100 pb-3">
                        1. Informasi Dokumen & Pejabat Pengesah
                    </h2>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700">Nomor Realisasi</label>
                            <input type="text" name="nomor_realisasi" value="{{ old('nomor_realisasi', $realisasi->nomor_realisasi) }}"
                                   class="mt-1 block w-full rounded-xl border-slate-200 text-xs font-mono focus:border-red-500 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700">Status Laporan</label>
                            <select name="status" class="mt-1 block w-full rounded-xl border-slate-200 text-xs font-semibold focus:border-red-500 focus:ring-red-500">
                                <option value="draft" {{ old('status', $realisasi->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="diajukan" {{ old('status', $realisasi->status) === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                <option value="diverifikasi_keuangan" {{ old('status', $realisasi->status) === 'diverifikasi_keuangan' ? 'selected' : '' }}>Diverifikasi Keuangan</option>
                                <option value="disetujui" {{ old('status', $realisasi->status) === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ old('status', $realisasi->status) === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700">Catatan Dokumen</label>
                            <input type="text" name="catatan" value="{{ old('catatan', $realisasi->catatan ?? '-') }}"
                                   class="mt-1 block w-full rounded-xl border-slate-200 text-xs focus:border-red-500 focus:ring-red-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 pt-4 border-t border-slate-100 lg:grid-cols-3">
                        {{-- Pembuat --}}
                        <div class="space-y-3 rounded-2xl bg-slate-50 p-4 border border-slate-100">
                            <span class="text-xs font-black uppercase text-slate-700">Dibuat Oleh</span>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500">Nama Lengkap</label>
                                <input type="text" name="pembuat_nama" value="{{ old('pembuat_nama', $realisasi->pembuat_nama) }}"
                                       class="mt-0.5 block w-full rounded-lg border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500">NIK</label>
                                <input type="text" name="pembuat_nik" value="{{ old('pembuat_nik', $realisasi->pembuat_nik) }}"
                                       class="mt-0.5 block w-full rounded-lg border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500">Jabatan</label>
                                <input type="text" name="pembuat_jabatan" value="{{ old('pembuat_jabatan', $realisasi->pembuat_jabatan) }}"
                                       class="mt-0.5 block w-full rounded-lg border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500">Tanggal Buat</label>
                                <input type="date" name="tanggal_dibuat" value="{{ old('tanggal_dibuat', $realisasi->tanggal_dibuat?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                                       class="mt-0.5 block w-full rounded-lg border-slate-200 text-xs">
                            </div>
                        </div>

                        {{-- Pemeriksa (Kaur Keuangan) --}}
                        <div class="space-y-3 rounded-2xl bg-slate-50 p-4 border border-slate-100">
                            <span class="text-xs font-black uppercase text-slate-700">Diperiksa & Disetujui Oleh</span>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500">Nama Pemeriksa</label>
                                <input type="text" name="pemeriksa_nama" value="{{ old('pemeriksa_nama', $realisasi->pemeriksa_nama ?: 'Mevita Yollanda') }}"
                                       class="mt-0.5 block w-full rounded-lg border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500">NIK</label>
                                <input type="text" name="pemeriksa_nik" value="{{ old('pemeriksa_nik', $realisasi->pemeriksa_nik ?: '25960023') }}"
                                       class="mt-0.5 block w-full rounded-lg border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500">Jabatan</label>
                                <input type="text" name="pemeriksa_jabatan" value="{{ old('pemeriksa_jabatan', $realisasi->pemeriksa_jabatan ?: 'Kaur Keuangan') }}"
                                       class="mt-0.5 block w-full rounded-lg border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500">Tanggal Periksa</label>
                                <input type="date" name="tanggal_diperiksa" value="{{ old('tanggal_diperiksa', $realisasi->tanggal_diperiksa?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                                       class="mt-0.5 block w-full rounded-lg border-slate-200 text-xs">
                            </div>
                        </div>

                        {{-- Penyetuju (Kepala Sekolah) --}}
                        <div class="space-y-3 rounded-2xl bg-slate-50 p-4 border border-slate-100">
                            <span class="text-xs font-black uppercase text-slate-700">Disetujui Realisasi (Kepsek)</span>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500">Nama Penyetuju</label>
                                <input type="text" name="penyetuju_nama" value="{{ old('penyetuju_nama', $realisasi->penyetuju_nama ?: 'Dedi Eko Cahyono') }}"
                                       class="mt-0.5 block w-full rounded-lg border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500">NIK</label>
                                <input type="text" name="penyetuju_nik" value="{{ old('penyetuju_nik', $realisasi->penyetuju_nik ?: '22880011') }}"
                                       class="mt-0.5 block w-full rounded-lg border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500">Jabatan</label>
                                <input type="text" name="penyetuju_jabatan" value="{{ old('penyetuju_jabatan', $realisasi->penyetuju_jabatan ?: 'Kepala Sekolah') }}"
                                       class="mt-0.5 block w-full rounded-lg border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500">Tanggal Setujui</label>
                                <input type="date" name="tanggal_disetujui" value="{{ old('tanggal_disetujui', $realisasi->tanggal_disetujui?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                                       class="mt-0.5 block w-full rounded-lg border-slate-200 text-xs">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Rincian Transaksi Realisasi --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-base font-black text-slate-900">
                                2. Rincian Transaksi Realisasi Anggaran
                            </h2>
                            <p class="text-xs text-slate-500">
                                Catat setiap penerimaan dana kas dan pengeluaran kegiatan secara kronologis.
                            </p>
                        </div>
                        <button type="button" @click="addRow()"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">
                            + Tambah Baris Transaksi
                        </button>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-left text-xs text-slate-800 border-collapse">
                            <thead>
                                <tr class="bg-slate-100 font-bold uppercase text-[11px] border-b border-slate-200 text-slate-700">
                                    <th class="px-2 py-3 text-center w-10">No</th>
                                    <th class="px-3 py-3 w-36">Tanggal</th>
                                    <th class="px-3 py-3">Uraian Transaksi</th>
                                    <th class="px-3 py-3 w-36">Penerimaan (Rp)</th>
                                    <th class="px-3 py-3 w-36">Pengeluaran (Rp)</th>
                                    <th class="px-3 py-3 w-36 text-right">Saldo (Rp)</th>
                                    <th class="px-3 py-3 w-32">Keterangan</th>
                                    <th class="px-2 py-3 text-center w-12">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(row, idx) in rows" :key="idx">
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-2 py-2 text-center font-bold text-slate-400" x-text="idx + 1"></td>
                                        <td class="px-2 py-2">
                                            <input type="date" :name="`items[${idx}][tanggal]`" x-model="row.tanggal" required
                                                   class="w-full rounded-lg border-slate-200 text-xs p-1.5 focus:border-red-500 focus:ring-red-500">
                                        </td>
                                        <td class="px-2 py-2">
                                            <textarea :name="`items[${idx}][uraian]`" x-model="row.uraian" rows="2" required
                                                      placeholder="Misal: Pembayaran Langganan Software..."
                                                      class="w-full rounded-lg border-slate-200 text-xs p-1.5 focus:border-red-500 focus:ring-red-500"></textarea>
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" step="any" min="0" :name="`items[${idx}][penerimaan]`" x-model.number="row.penerimaan"
                                                   @input="recalc()"
                                                   placeholder="0"
                                                   class="w-full rounded-lg border-slate-200 text-xs text-right p-1.5 focus:border-red-500 focus:ring-red-500">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" step="any" min="0" :name="`items[${idx}][pengeluaran]`" x-model.number="row.pengeluaran"
                                                   @input="recalc()"
                                                   placeholder="0"
                                                   class="w-full rounded-lg border-slate-200 text-xs text-right p-1.5 focus:border-red-500 focus:ring-red-500">
                                        </td>
                                        <td class="px-3 py-2 text-right font-mono font-bold text-slate-900"
                                            x-text="formatRupiah(calcRowSaldo(idx))">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="text" :name="`items[${idx}][keterangan]`" x-model="row.keterangan"
                                                   placeholder="-"
                                                   class="w-full rounded-lg border-slate-200 text-xs p-1.5 focus:border-red-500 focus:ring-red-500">
                                        </td>
                                        <td class="px-2 py-2 text-center">
                                            <button type="button" @click="removeRow(idx)"
                                                    :disabled="rows.length <= 1"
                                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 disabled:opacity-30">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>

                                {{-- Baris Total --}}
                                <tr class="bg-slate-100 font-black text-xs border-t-2 border-slate-300">
                                    <td colspan="3" class="px-4 py-3 text-center uppercase tracking-wider text-slate-700">
                                        JUMLAH REKAPITULASI
                                    </td>
                                    <td class="px-3 py-3 text-right text-emerald-700 font-mono" x-text="formatRupiah(totalPenerimaan)"></td>
                                    <td class="px-3 py-3 text-right text-rose-700 font-mono" x-text="formatRupiah(totalPengeluaran)"></td>
                                    <td class="px-3 py-3 text-right font-mono"
                                        :class="totalSaldo >= 0 ? 'text-blue-700' : 'text-red-700'"
                                        x-text="formatRupiah(totalSaldo)"></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Unggah Berkas Bukti Eviden Tambahan --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-black text-slate-900">
                            3. Unggah Berkas Bukti Eviden (Nota, Kuitansi, Dokumen PDF)
                        </h2>
                        <p class="text-xs text-slate-500">
                            Anda dapat memilih beberapa file sekaligus (format Gambar: JPG, PNG, WEBP atau PDF hingga 10MB per berkas).
                        </p>
                    </div>

                    <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50/50 p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="mt-2 text-xs font-bold text-slate-700">Pilih berkas bukti eviden tambahan</p>
                        <input type="file" name="evidences[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf"
                               class="mt-3 block w-full max-w-md mx-auto text-xs text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-red-600 file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-red-700">
                    </div>

                    {{-- Daftar Bukti Eviden yang Sudah Ada --}}
                    @if($realisasi->evidences->isNotEmpty())
                        <div class="pt-4 border-t border-slate-100 space-y-3">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-700">
                                Berkas Eviden Tersimpan ({{ $realisasi->evidences->count() }})
                            </h3>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($realisasi->evidences as $ev)
                                    <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-3">
                                        <div class="flex items-center gap-2 overflow-hidden">
                                            @if($ev->isImage())
                                                <img src="{{ asset('storage/' . $ev->file_path) }}" class="h-9 w-9 rounded-lg object-cover flex-shrink-0">
                                            @else
                                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-100 text-xs font-black text-red-700 flex-shrink-0">PDF</span>
                                            @endif
                                            <div class="truncate">
                                                <p class="text-xs font-bold text-slate-800 truncate">{{ $ev->judul }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $ev->file_name }}</p>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . $ev->file_path) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:underline ml-2 flex-shrink-0">
                                            Lihat
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Action Bar --}}
                <div class="flex items-center justify-end gap-3 sticky bottom-4 z-20 rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-lg backdrop-blur">
                    <a href="{{ route('rab.realisasi.show', $rab) }}"
                       class="rounded-xl border border-slate-200 px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="rounded-xl bg-red-600 px-6 py-2.5 text-xs font-black text-white shadow-md transition hover:bg-red-700">
                        Simpan Laporan Realisasi & Eviden
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function realisasiEditor(initialRows) {
            return {
                rows: initialRows.length ? initialRows : [
                    { tanggal: '{{ date("Y-m-d") }}', uraian: '', penerimaan: 0, pengeluaran: 0, keterangan: '-' }
                ],
                totalPenerimaan: 0,
                totalPengeluaran: 0,
                totalSaldo: 0,

                init() {
                    this.recalc();
                },

                addRow() {
                    this.rows.push({
                        tanggal: '{{ date("Y-m-d") }}',
                        uraian: '',
                        penerimaan: 0,
                        pengeluaran: 0,
                        keterangan: '-'
                    });
                    this.recalc();
                },

                removeRow(idx) {
                    if (this.rows.length > 1) {
                        this.rows.splice(idx, 1);
                        this.recalc();
                    }
                },

                calcRowSaldo(targetIdx) {
                    let s = 0;
                    for (let i = 0; i <= targetIdx; i++) {
                        s += (parseFloat(this.rows[i].penerimaan) || 0) - (parseFloat(this.rows[i].pengeluaran) || 0);
                    }
                    return s;
                },

                recalc() {
                    let p = 0;
                    let k = 0;
                    this.rows.forEach(r => {
                        p += (parseFloat(r.penerimaan) || 0);
                        k += (parseFloat(r.pengeluaran) || 0);
                    });
                    this.totalPenerimaan = p;
                    this.totalPengeluaran = k;
                    this.totalSaldo = p - k;
                },

                formatRupiah(num) {
                    num = parseFloat(num) || 0;
                    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
                }
            };
        }
    </script>
</x-app-layout>
