<x-app-layout>
<div class="max-w-6xl mx-auto space-y-6" x-data="rabForm({
    aiEndpoint: '{{ route('rab.ai-generate') }}',
    csrfToken: '{{ csrf_token() }}',
    initialData: {
        jenis_pengajuan: '{{ old('jenis_pengajuan', $defaults['jenis_pengajuan'] ?? 'rab') }}',
        judul_pengajuan: '{{ old('judul_pengajuan', $defaults['judul_pengajuan']) }}',
        tahun_anggaran: '{{ old('tahun_anggaran', $defaults['tahun_anggaran']) }}',
        nomor_akun: '{{ old('nomor_akun', $defaults['nomor_akun']) }}',
        nama_akun: '{{ old('nama_akun', $defaults['nama_akun']) }}',
        drk: '{{ old('drk', $defaults['drk']) }}',
        kebutuhan_waktu: '{{ old('kebutuhan_waktu', $defaults['kebutuhan_waktu']) }}',
        status: '{{ old('status', $defaults['status']) }}',
        pembuat_nama: '{{ old('pembuat_nama', $defaults['pembuat_nama']) }}',
        pembuat_nik: '{{ old('pembuat_nik', $defaults['pembuat_nik']) }}',
        pembuat_jabatan: '{{ old('pembuat_jabatan', $defaults['pembuat_jabatan']) }}',
        tanggal_dibuat: '{{ old('tanggal_dibuat', $defaults['tanggal_dibuat']) }}',
        catatan_anggaran: '{{ old('catatan_anggaran', $defaults['catatan_anggaran']) }}',
        pemeriksa_nama: '{{ old('pemeriksa_nama', $defaults['pemeriksa_nama']) }}',
        pemeriksa_nik: '{{ old('pemeriksa_nik', $defaults['pemeriksa_nik']) }}',
        pemeriksa_jabatan: '{{ old('pemeriksa_jabatan', $defaults['pemeriksa_jabatan']) }}',
        tanggal_diperiksa: '{{ old('tanggal_diperiksa', $defaults['tanggal_diperiksa']) }}',
        penyetuju_nama: '{{ old('penyetuju_nama', $defaults['penyetuju_nama']) }}',
        penyetuju_nik: '{{ old('penyetuju_nik', $defaults['penyetuju_nik']) }}',
        penyetuju_jabatan: '{{ old('penyetuju_jabatan', $defaults['penyetuju_jabatan']) }}',
        tanggal_disetujui: '{{ old('tanggal_disetujui', $defaults['tanggal_disetujui']) }}',
        catatan: '{{ old('catatan', $defaults['catatan']) }}',
        items: {{ json_encode(old('items', [
            [
                'uraian_kegiatan' => '',
                'spesifikasi' => '',
                'satuan' => 'unit',
                'volume' => 1,
                'harga_satuan' => 0,
                'catatan' => ''
            ]
        ])) }}
    }
})">

        {{-- Header Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div>
                <a href="{{ route('rab.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-red-600 mb-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Kembali ke Daftar RAB</span>
                </a>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                    Buat Pengajuan RAB Baru
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Isi detail pengadaan anggaran belanja, gunakan AI untuk auto-generate, dan unduh PDF resmi.
                </p>
            </div>

            {{-- AI Trigger Button --}}
            <button type="button" @click="openAiModal = true"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-sm shadow-lg shadow-purple-500/25 transition-all">
                <span class="text-lg">✨</span>
                <span>Bantu Buat dengan AI</span>
            </button>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 space-y-1">
                <div class="font-bold text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Terdapat kesalahan pada input formulir:
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 ml-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('rab.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Section 1: Metadata Anggaran --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center text-xs font-black">1</span>
                        <h2 class="font-bold text-slate-900 text-base">Identitas & Metadata Pengadaan</h2>
                    </div>
                    <span class="text-xs font-semibold text-slate-400">Kop & Judul Dokumen</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    {{-- Opsi Pilihan: RAB vs Reimburse --}}
                    <div class="md:col-span-12">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Pilih Jenis Pengajuan <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Opsi 1: RAB --}}
                            <label :class="formData.jenis_pengajuan === 'rab' ? 'border-red-600 bg-red-50/40 ring-2 ring-red-500/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                class="relative flex items-center p-3.5 rounded-2xl border-2 cursor-pointer transition-all">
                                <input type="radio" name="jenis_pengajuan" value="rab" x-model="formData.jenis_pengajuan" class="sr-only">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center mr-3 shrink-0"
                                    :class="formData.jenis_pengajuan === 'rab' ? 'border-red-600 bg-red-600' : 'border-slate-300'">
                                    <div class="w-2 h-2 rounded-full bg-white" x-show="formData.jenis_pengajuan === 'rab'"></div>
                                </div>
                                <div>
                                    <div class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                                        <span>Rencana Anggaran Biaya (RAB)</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">RAB</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">Pengajuan rencana pengadaan anggaran sebelum kegiatan belanja dilaksanakan.</p>
                                </div>
                            </label>

                            {{-- Opsi 2: Reimburse --}}
                            <label :class="formData.jenis_pengajuan === 'reimburse' ? 'border-amber-600 bg-amber-50/40 ring-2 ring-amber-500/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                class="relative flex items-center p-3.5 rounded-2xl border-2 cursor-pointer transition-all">
                                <input type="radio" name="jenis_pengajuan" value="reimburse" x-model="formData.jenis_pengajuan" class="sr-only">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center mr-3 shrink-0"
                                    :class="formData.jenis_pengajuan === 'reimburse' ? 'border-amber-600 bg-amber-600' : 'border-slate-300'">
                                    <div class="w-2 h-2 rounded-full bg-white" x-show="formData.jenis_pengajuan === 'reimburse'"></div>
                                </div>
                                <div>
                                    <div class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                                        <span>Pengajuan Reimburse</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">REIMBURSE</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">Pengajuan penggantian biaya/dana yang telah ditalangi sebelumnya.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Judul Pengajuan --}}
                    <div class="md:col-span-8">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Judul Pengajuan Kegiatan / Pengadaan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="judul_pengajuan" x-model="formData.judul_pengajuan" required
                            placeholder="Contoh: LANGGANAN WHATSAPP BUSINESS DAN CHAT GPT"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold uppercase focus:border-red-500 focus:ring-red-500/20">
                        <p class="text-[11px] text-slate-400 mt-1">Gunakan huruf kapital singkat dan jelas sebagai judul utama RAB.</p>
                    </div>

                    {{-- Tahun Anggaran --}}
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Tahun Anggaran <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="tahun_anggaran" x-model="formData.tahun_anggaran" required
                            placeholder="2026"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:border-red-500 focus:ring-red-500/20">
                    </div>

                    {{-- Nomor Akun --}}
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nomor Akun <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomor_akun" x-model="formData.nomor_akun" required
                            placeholder="5611104"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:border-red-500 focus:ring-red-500/20">
                    </div>

                    {{-- Nama Akun --}}
                    <div class="md:col-span-8">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Akun <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_akun" x-model="formData.nama_akun" required
                            placeholder="BPP Alat Pengolah Data"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:border-red-500 focus:ring-red-500/20">
                    </div>

                    {{-- DRK --}}
                    <div class="md:col-span-8">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            DRK (Deskripsi Rencana Kerja) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="drk" x-model="formData.drk" required
                            placeholder="Peningkatan Kapasitas dan Modernisasi Infrastruktur Pendidikan"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:border-red-500 focus:ring-red-500/20">
                    </div>

                    {{-- Kebutuhan Waktu --}}
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Kebutuhan Waktu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kebutuhan_waktu" x-model="formData.kebutuhan_waktu" required
                            placeholder="April 2026"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:border-red-500 focus:ring-red-500/20">
                    </div>
                </div>
            </div>

            {{-- Section 2: Tabel Rincian Anggaran (Items) --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center text-xs font-black">2</span>
                        <h2 class="font-bold text-slate-900 text-base">Rincian Anggaran Biaya</h2>
                    </div>
                    <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Item</span>
                    </button>
                </div>

                {{-- Dynamic Items Table --}}
                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-100/70 text-[11px] uppercase tracking-wider text-slate-600 font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-3 text-center w-12">NO</th>
                                <th class="p-3 w-1/4">URAIAN KEGIATAN</th>
                                <th class="p-3 w-1/5">SPESIFIKASI</th>
                                <th class="p-3 w-20 text-center">SATUAN</th>
                                <th class="p-3 w-16 text-center">VOL</th>
                                <th class="p-3 w-32 text-right">HARGA (RP)</th>
                                <th class="p-3 w-32 text-right">JUMLAH (RP)</th>
                                <th class="p-3 w-32">CATATAN</th>
                                <th class="p-3 w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <template x-for="(item, index) in formData.items" :key="index">
                                <tr class="hover:bg-slate-50/50">
                                    <td class="p-2.5 text-center font-bold text-slate-500 text-xs" x-text="index + 1"></td>
                                    
                                    {{-- Uraian --}}
                                    <td class="p-2.5">
                                        <textarea :name="`items[${index}][uraian_kegiatan]`" x-model="item.uraian_kegiatan" required rows="2"
                                            placeholder="Nama barang / jasa"
                                            class="w-full p-2 text-xs rounded-lg border border-slate-200 focus:border-red-500 focus:ring-red-500/20"></textarea>
                                    </td>

                                    {{-- Spesifikasi --}}
                                    <td class="p-2.5">
                                        <textarea :name="`items[${index}][spesifikasi]`" x-model="item.spesifikasi" rows="2"
                                            placeholder="Spesifikasi teknis / detail"
                                            class="w-full p-2 text-xs rounded-lg border border-slate-200 focus:border-red-500 focus:ring-red-500/20"></textarea>
                                    </td>

                                    {{-- Satuan --}}
                                    <td class="p-2.5">
                                        <input type="text" :name="`items[${index}][satuan]`" x-model="item.satuan" required
                                            placeholder="bulan/unit"
                                            class="w-full p-2 text-xs text-center rounded-lg border border-slate-200 focus:border-red-500 focus:ring-red-500/20">
                                    </td>

                                    {{-- Volume --}}
                                    <td class="p-2.5">
                                        <input type="number" step="any" min="0.01" :name="`items[${index}][volume]`" x-model.number="item.volume" required
                                            class="w-full p-2 text-xs text-center rounded-lg border border-slate-200 focus:border-red-500 focus:ring-red-500/20">
                                    </td>

                                    {{-- Harga Satuan --}}
                                    <td class="p-2.5">
                                        <input type="number" step="any" min="0" :name="`items[${index}][harga_satuan]`" x-model.number="item.harga_satuan" required
                                            class="w-full p-2 text-xs text-right font-semibold rounded-lg border border-slate-200 focus:border-red-500 focus:ring-red-500/20">
                                    </td>

                                    {{-- Subtotal --}}
                                    <td class="p-2.5 text-right font-black text-slate-800 text-xs">
                                        <span x-text="formatRupiah((item.volume || 0) * (item.harga_satuan || 0))"></span>
                                    </td>

                                    {{-- Catatan --}}
                                    <td class="p-2.5">
                                        <input type="text" :name="`items[${index}][catatan]`" x-model="item.catatan"
                                            placeholder="Catatan / keterangan"
                                            class="w-full p-2 text-xs rounded-lg border border-slate-200 focus:border-red-500 focus:ring-red-500/20">
                                    </td>

                                    {{-- Remove Button --}}
                                    <td class="p-2.5 text-center">
                                        <button type="button" @click="removeItem(index)" x-show="formData.items.length > 1"
                                            class="p-1 text-slate-400 hover:text-rose-600 rounded transition-colors" title="Hapus Baris">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-300 font-bold">
                            <tr>
                                <td colspan="6" class="p-3 text-right uppercase tracking-wider text-xs font-black text-slate-700">
                                    TOTAL ANGGARAN
                                </td>
                                <td class="p-3 text-right font-black text-base text-red-600">
                                    <span x-text="formatRupiah(totalAnggaran)"></span>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Section 3: Data Penandatangan & Persetujuan --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-5" x-data="{ expandedSignatures: true }">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 cursor-pointer" @click="expandedSignatures = !expandedSignatures">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center text-xs font-black">3</span>
                        <h2 class="font-bold text-slate-900 text-base">Alur Persetujuan & Penandatangan</h2>
                    </div>
                    <button type="button" class="text-xs font-bold text-slate-500 hover:text-slate-800">
                        <span x-text="expandedSignatures ? 'Sembunyikan ▲' : 'Tampilkan Detail ▼'"></span>
                    </button>
                </div>

                <div x-show="expandedSignatures" class="space-y-6 pt-2">
                    {{-- 1. Dibuat oleh --}}
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-slate-500"></span> Dibuat Oleh (Pemohon)
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 uppercase">Nama Lengkap</label>
                                <input type="text" name="pembuat_nama" x-model="formData.pembuat_nama" required
                                    class="w-full mt-1 p-2 text-xs rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 uppercase">NIK / NIP</label>
                                <input type="text" name="pembuat_nik" x-model="formData.pembuat_nik"
                                    class="w-full mt-1 p-2 text-xs rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 uppercase">Jabatan</label>
                                <input type="text" name="pembuat_jabatan" x-model="formData.pembuat_jabatan"
                                    class="w-full mt-1 p-2 text-xs rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 uppercase">Tanggal</label>
                                <input type="date" name="tanggal_dibuat" x-model="formData.tanggal_dibuat"
                                    class="w-full mt-1 p-2 text-xs rounded-lg border border-slate-200">
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Anggaran --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Catatan Anggaran
                        </label>
                        <textarea name="catatan_anggaran" x-model="formData.catatan_anggaran" rows="2"
                            placeholder="Catatan dari bagian keuangan / pembuat anggaran (gunakan '-' jika kosong)"
                            class="w-full p-2.5 text-xs rounded-xl border border-slate-200"></textarea>
                    </div>

                    {{-- 2. Diperiksa & Disetujui oleh (Kaur Keuangan) --}}
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Diperiksa & Disetujui Oleh (Kaur Keuangan)
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 uppercase">Nama Pemeriksa</label>
                                <input type="text" name="pemeriksa_nama" x-model="formData.pemeriksa_nama"
                                    class="w-full mt-1 p-2 text-xs rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 uppercase">NIK Pemeriksa</label>
                                <input type="text" name="pemeriksa_nik" x-model="formData.pemeriksa_nik"
                                    class="w-full mt-1 p-2 text-xs rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 uppercase">Jabatan</label>
                                <input type="text" name="pemeriksa_jabatan" x-model="formData.pemeriksa_jabatan"
                                    class="w-full mt-1 p-2 text-xs rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 uppercase">Tanggal Periksa</label>
                                <input type="date" name="tanggal_diperiksa" x-model="formData.tanggal_diperiksa"
                                    class="w-full mt-1 p-2 text-xs rounded-lg border border-slate-200">
                            </div>
                        </div>
                    </div>

                    {{-- 3. Diperiksa & Disetujui Realisasi (Kepala Sekolah) --}}
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Diperiksa & Disetujui Realisasi (Kepala Sekolah)
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 uppercase">Nama Kepala Sekolah</label>
                                <input type="text" name="penyetuju_nama" x-model="formData.penyetuju_nama"
                                    class="w-full mt-1 p-2 text-xs rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 uppercase">NIK Kepala Sekolah</label>
                                <input type="text" name="penyetuju_nik" x-model="formData.penyetuju_nik"
                                    class="w-full mt-1 p-2 text-xs rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 uppercase">Jabatan</label>
                                <input type="text" name="penyetuju_jabatan" x-model="formData.penyetuju_jabatan"
                                    class="w-full mt-1 p-2 text-xs rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 uppercase">Tanggal Setuju</label>
                                <input type="date" name="tanggal_disetujui" x-model="formData.tanggal_disetujui"
                                    class="w-full mt-1 p-2 text-xs rounded-lg border border-slate-200">
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Penutup --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Catatan Penutup / Syarat Realisasi
                        </label>
                        <textarea name="catatan" x-model="formData.catatan" rows="2"
                            placeholder="Catatan tambahan di bagian bawah dokumen (gunakan '-' jika tidak ada)"
                            class="w-full p-2.5 text-xs rounded-xl border border-slate-200"></textarea>
                    </div>
                </div>
            </div>

            {{-- Action Submit Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('rab.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-100 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-lg shadow-red-500/25 transition-all">
                    Simpan Dokumen RAB
                </button>
            </div>
        </form>

        {{-- Stella AI Modal Prompt --}}
        <div x-show="openAiModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="!aiLoading && (openAiModal = false)"
                class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-purple-100 space-y-4">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-base">✨</span>
                        <div>
                            <h3 class="font-black text-slate-900 text-base">Stella AI — Asisten Penyusun RAB</h3>
                            <p class="text-xs text-slate-500">Tuliskan kebutuhan pengadaan dalam bahasa sehari-hari</p>
                        </div>
                    </div>
                    <button type="button" @click="!aiLoading && (openAiModal = false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Prompt Form --}}
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Deskripsi Pengadaan Barang / Kegiatan:
                    </label>
                    <textarea x-model="aiPrompt" rows="4" :disabled="aiLoading"
                        placeholder="Contoh: Kami membutuhkan pengadaan alat praktikum IoT untuk jurusan RPL: 10 Arduino Uno, 10 modul ESP32, kabel jumper 5 pack, timah solder 2 roll, dan 5 multimeter digital untuk persiapan UKK bulan Mei 2026."
                        class="w-full p-3 text-sm rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-purple-500/20"></textarea>

                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <span class="text-[11px] font-bold text-slate-400">Contoh Cepat:</span>
                        <button type="button" @click="aiPrompt = 'Langganan WhatsApp Business API dan ChatGPT Plus Gemini selama 6 bulan untuk operasional sekolah tahun 2026'"
                            class="text-[11px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded-lg transition-colors">
                            Langganan WhatsApp & AI
                        </button>
                        <button type="button" @click="aiPrompt = 'Pengadaan 20 unit router Mikrotik hEX dan 2 roll kabel LAN Cat6 untuk lab komputer bulan Mei 2026'"
                            class="text-[11px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded-lg transition-colors">
                            Alat Jaringan Lab TKJ
                        </button>
                    </div>

                    {{-- Loading Indicator --}}
                    <div x-show="aiLoading" class="p-4 rounded-xl bg-purple-50 border border-purple-100 flex items-center gap-3">
                        <svg class="animate-spin w-5 h-5 text-purple-600 shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <div class="text-xs text-purple-900 font-semibold">
                            Stella AI sedang menganalisis kebutuhan, nomor akun, DRK, dan rincian harga wajar...
                        </div>
                    </div>

                    {{-- Error Message --}}
                    <div x-show="aiError" class="p-3 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-semibold" x-text="aiError"></div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="openAiModal = false" :disabled="aiLoading"
                        class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="generateWithAi()" :disabled="aiLoading || !aiPrompt.trim()"
                        class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-lg shadow-purple-500/20 disabled:opacity-50 transition-all flex items-center gap-1.5">
                        <span>✨ Generate Draft RAB</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

<script>
function rabForm(config) {
    return {
        formData: config.initialData,
        openAiModal: false,
        aiPrompt: '',
        aiLoading: false,
        aiError: null,

        get totalAnggaran() {
            return (this.formData.items || []).reduce((sum, item) => {
                const vol = parseFloat(item.volume) || 0;
                const harga = parseFloat(item.harga_satuan) || 0;
                return sum + (vol * harga);
            }, 0);
        },

        addItem() {
            this.formData.items.push({
                uraian_kegiatan: '',
                spesifikasi: '',
                satuan: 'unit',
                volume: 1,
                harga_satuan: 0,
                catatan: ''
            });
        },

        removeItem(index) {
            if (this.formData.items.length > 1) {
                this.formData.items.splice(index, 1);
            }
        },

        formatRupiah(number) {
            return 'Rp ' + (new Intl.NumberFormat('id-ID').format(Math.round(number || 0)));
        },

        async generateWithAi() {
            if (!this.aiPrompt.trim()) return;
            this.aiLoading = true;
            this.aiError = null;

            try {
                const response = await fetch(config.aiEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ prompt: this.aiPrompt })
                });

                const res = await response.json();
                if (!response.ok || !res.success) {
                    throw new Error(res.message || 'Gagal generate AI.');
                }

                const data = res.data;
                // Auto-fill form
                if (data.judul_pengajuan) this.formData.judul_pengajuan = data.judul_pengajuan;
                if (data.tahun_anggaran) this.formData.tahun_anggaran = data.tahun_anggaran;
                if (data.nomor_akun) this.formData.nomor_akun = data.nomor_akun;
                if (data.nama_akun) this.formData.nama_akun = data.nama_akun;
                if (data.drk) this.formData.drk = data.drk;
                if (data.kebutuhan_waktu) this.formData.kebutuhan_waktu = data.kebutuhan_waktu;
                if (data.catatan_anggaran) this.formData.catatan_anggaran = data.catatan_anggaran;

                if (Array.isArray(data.items) && data.items.length > 0) {
                    this.formData.items = data.items.map(i => ({
                        uraian_kegiatan: i.uraian_kegiatan || '',
                        spesifikasi: i.spesifikasi || '',
                        satuan: i.satuan || 'unit',
                        volume: parseFloat(i.volume) || 1,
                        harga_satuan: parseFloat(i.harga_satuan) || 0
                    }));
                }

                this.openAiModal = false;
                this.aiPrompt = '';
            } catch (err) {
                this.aiError = err.message || 'Terjadi kesalahan saat menghubungi Stella AI.';
            } finally {
                this.aiLoading = false;
            }
        }
    };
}
</script>
</x-app-layout>
