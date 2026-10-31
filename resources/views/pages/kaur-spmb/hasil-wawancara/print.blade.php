<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Hasil Wawancara SPMB - {{ $pesertaSeleksi->nama_lengkap }} ({{ $pesertaSeleksi->nomor_pendaftaran }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Standar Ukuran Kertas A4: 210mm x 297mm */
        @page {
            size: A4 portrait;
            margin: 0; /* Zero margin agar Kop Surat menempel penuh edge-to-edge */
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica Neue', Helvetica, sans-serif;
            color: #111827;
            background-color: #cbd5e1;
            margin: 0;
            padding: 0;
        }

        /* Lembar Kertas A4 Nyata */
        .page-sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 24px auto;
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        @media screen and (max-width: 840px) {
            .page-sheet {
                width: 100%;
                min-height: auto;
            }
            .screen-toolbar {
                width: 100% !important;
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }
            html, body {
                width: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .page-sheet {
                width: 210mm !important;
                min-height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                page-break-after: always;
            }
            .page-sheet:last-child {
                page-break-after: auto;
            }
            .kop-banner {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .kop-banner img {
                width: 100% !important;
                display: block !important;
            }
            .doc-body {
                padding-left: 18mm !important;
                padding-right: 18mm !important;
                padding-top: 3mm !important;
                padding-bottom: 8mm !important;
            }
            table.table-doc {
                page-break-inside: auto;
            }
            table.table-doc tr {
                page-break-inside: avoid;
            }
            table.table-doc th, table.table-doc td {
                padding: 3px 5px !important;
                font-size: 8pt !important;
                line-height: 1.2 !important;
            }
        }

        /* Bordered table styling */
        table.table-doc {
            border-collapse: collapse;
            width: 100%;
        }
        table.table-doc th, table.table-doc td {
            border: 1px solid #000000;
            padding: 3.5px 6px;
            font-size: 8.5pt;
            line-height: 1.25;
            vertical-align: middle;
        }
        table.table-doc th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body class="min-h-screen py-6 print:py-0">

    {{-- SCREEN TOOLBAR (HIDDEN ON PRINT) --}}
    <div class="no-print screen-toolbar mx-auto mb-4 bg-white border border-slate-300 rounded-lg p-3.5 shadow-sm" style="width: 210mm; max-width: 95%;">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('kaur-spmb.administrasi.hasil-wawancara.show', $pesertaSeleksi) }}"
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md border border-slate-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Detail Resume
                </a>
                <span class="text-xs text-slate-500 font-medium">
                    Calon Siswa: <strong class="text-slate-800">{{ $pesertaSeleksi->nama_lengkap }}</strong> ({{ $pesertaSeleksi->nomor_pendaftaran }})
                </span>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Lembar Wawancara (A4)
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- LEMBAR 1: LEMBAR HASIL UJI WAWANCARA RESMI               --}}
    {{-- ======================================================== --}}
    <div class="page-sheet">

        {{-- KOP SURAT (FULL EDGE-TO-EDGE, NO MARGIN) --}}
        <div class="kop-banner w-full flex-shrink-0">
            @if(!empty($kopBase64))
                <img src="{{ $kopBase64 }}" alt="Kop Surat" class="w-full block" style="width: 100%; display: block; margin: 0; padding: 0;">
            @else
                <div class="relative w-full pt-3 pb-2 px-8 border-b-2 border-red-600 bg-white">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-shrink-0 flex items-center gap-2">
                            <div class="w-12 h-12 bg-red-600 rounded flex flex-col items-center justify-center text-white font-bold tracking-tight text-center shadow-sm">
                                <span class="text-[11px] uppercase leading-none font-extrabold">Telkom</span>
                                <span class="text-[8.5px] uppercase leading-none font-semibold mt-0.5">Schools</span>
                            </div>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="text-[9pt] font-semibold tracking-wider text-slate-800 uppercase">YAYASAN PENDIDIKAN TELKOM</div>
                            <div class="text-[14pt] font-extrabold text-slate-900 tracking-wide uppercase leading-tight">SMK Telkom Lampung</div>
                            <div class="text-[7.5pt] text-slate-700 font-semibold mt-0.5">
                                Kompetensi Keahlian : (1) Teknik Jaringan Akses Telekomunikasi (2) Rekayasa Perangkat Lunak (3) Teknik Komputer Dan Jaringan (4) Animasi
                            </div>
                            <div class="text-[7pt] text-slate-600 mt-0.5">
                                Alamat: Jl. Raya Gadingrejo, Kec. Gading Rejo, Kab. Pringsewu, Lampung 35372 &bull; Telp: 0721 5621 547
                            </div>
                        </div>
                        <div class="w-12 flex-shrink-0"></div>
                    </div>
                </div>
            @endif
        </div>

        {{-- DOKUMEN BODY --}}
        <div class="doc-body flex-1 flex flex-col justify-between px-8 py-3" style="font-size: 8.5pt; line-height: 1.35;">
            <div class="space-y-3">
                
                {{-- TITLE --}}
                <div class="text-center">
                    <h3 class="font-bold text-[11pt] text-slate-900 uppercase tracking-wide underline">
                        LEMBAR HASIL UJI WAWANCARA SELEKSI PENERIMAAN MURID BARU
                    </h3>
                    <p class="text-[8pt] text-slate-700 mt-0.5 font-semibold">
                        Tahun Ajaran {{ $pesertaSeleksi->tahun_ajaran }} &bull; Tanggal Uji: {{ $hariTanggalStr }}
                    </p>
                </div>

                {{-- 1. IDENTITAS CALON SISWA & ORANG TUA --}}
                <div class="border border-slate-900 p-2.5 rounded-sm bg-slate-50/40">
                    <table class="w-full text-[8pt]" style="line-height: 1.4;">
                        <tr>
                            <td class="w-36 font-bold text-slate-700">Nomor Pendaftaran / Tes</td>
                            <td class="w-3 text-center">:</td>
                            <td class="font-mono font-bold text-slate-900">{{ $pesertaSeleksi->nomor_pendaftaran }}</td>
                            <td class="w-32 font-bold text-slate-700">Nama Orang Tua / Wali</td>
                            <td class="w-3 text-center">:</td>
                            <td class="font-bold text-slate-900">{{ $pesertaSeleksi->nama_orang_tua ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold text-slate-700">Nama Lengkap Siswa</td>
                            <td class="text-center">:</td>
                            <td class="font-bold text-slate-900">{{ $pesertaSeleksi->nama_lengkap }}</td>
                            <td class="font-bold text-slate-700">No. HP / WhatsApp Wali</td>
                            <td class="text-center">:</td>
                            <td class="text-slate-900">{{ $pesertaSeleksi->no_wa_orang_tua ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold text-slate-700">Jenis Kelamin / Asal Sekolah</td>
                            <td class="text-center">:</td>
                            <td class="text-slate-900">{{ $pesertaSeleksi->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }} &bull; {{ $pesertaSeleksi->asal_sekolah ?: '-' }}</td>
                            <td class="font-bold text-slate-700">Jurusan Pilihan 1 / 2</td>
                            <td class="text-center">:</td>
                            <td class="text-slate-900 font-semibold">{{ $pesertaSeleksi->jurusan_pilihan ?: '-' }} / {{ $pesertaSeleksi->jurusan_pilihan_2 ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold text-slate-700">No. WhatsApp Siswa</td>
                            <td class="text-center">:</td>
                            <td class="text-slate-900">{{ $pesertaSeleksi->no_wa_siswa ?: '-' }}</td>
                            <td class="font-bold text-slate-700">Alamat Tempat Tinggal</td>
                            <td class="text-center">:</td>
                            <td class="text-slate-900 truncate max-w-[200px]">{{ $pesertaSeleksi->alamat ?: '-' }}</td>
                        </tr>
                    </table>
                </div>

                {{-- 2. RESUME EVALUASI WAWANCARA SISWA --}}
                <div class="border border-slate-900 rounded-sm overflow-hidden">
                    <div class="bg-slate-200 px-3 py-1 font-bold text-[8.5pt] flex items-center justify-between border-b border-slate-900">
                        <span>I. HASIL EVALUASI WAWANCARA CALON SISWA</span>
                        <span>Predikat: <strong>{{ $sesi?->hasil_wawancara_siswa ?: '-' }}</strong> (Skor: {{ $sesi?->skor_siswa !== null ? number_format($sesi->skor_siswa, 1) : '-' }}/100)</span>
                    </div>
                    <div class="p-2.5 text-[8pt] space-y-1.5">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="font-bold text-slate-700">Petugas Pewawancara:</span>
                                <span class="text-slate-900 ml-1">{{ $sesi?->pewawancara_siswa_nama ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="font-bold text-slate-700">Waktu Pelaksanaan:</span>
                                <span class="text-slate-900 ml-1">{{ $sesi?->waktu_wawancara_siswa ? \Carbon\Carbon::parse($sesi->waktu_wawancara_siswa)->format('d/m/Y H:i') . ' WIB' : '-' }}</span>
                            </div>
                        </div>

                        {{-- Catatan Observasi Siswa --}}
                        <div class="pt-1">
                            <span class="font-bold text-slate-700 block">Catatan Karakter, Minat & Observasi Calon Siswa:</span>
                            <div class="p-1.5 rounded bg-slate-50 border border-slate-300 min-h-[38px] text-slate-800 italic">
                                {{ $sesi?->catatan_pewawancara_siswa ?: 'Tidak ada catatan khusus.' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. RESUME EVALUASI WAWANCARA ORANG TUA / WALI & FINANSIAL --}}
                <div class="border border-slate-900 rounded-sm overflow-hidden">
                    <div class="bg-slate-200 px-3 py-1 font-bold text-[8.5pt] flex items-center justify-between border-b border-slate-900">
                        <span>II. HASIL EVALUASI WAWANCARA ORANG TUA / WALI & KESIAPAN PEMBIAYAAN</span>
                        <span>Predikat: <strong>{{ $sesi?->hasil_wawancara_ortu ?: '-' }}</strong> (Skor: {{ $sesi?->skor_ortu !== null ? number_format($sesi->skor_ortu, 1) : '-' }}/100)</span>
                    </div>
                    <div class="p-2.5 text-[8pt] space-y-1.5">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="font-bold text-slate-700">Petugas Pewawancara:</span>
                                <span class="text-slate-900 ml-1">{{ $sesi?->pewawancara_ortu_nama ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="font-bold text-slate-700">Waktu Pelaksanaan:</span>
                                <span class="text-slate-900 ml-1">{{ $sesi?->waktu_wawancara_ortu ? \Carbon\Carbon::parse($sesi->waktu_wawancara_ortu)->format('d/m/Y H:i') . ' WIB' : '-' }}</span>
                            </div>
                        </div>

                        {{-- Catatan Komitmen Finansial --}}
                        <div class="pt-1">
                            <span class="font-bold text-slate-700 block">Catatan Kesepakatan Biaya Pendidikan & Pendampingan:</span>
                            <div class="p-1.5 rounded bg-slate-50 border border-slate-300 min-h-[38px] text-slate-800 italic">
                                {{ $sesi?->catatan_pewawancara_ortu ?: 'Orang tua menyetujui ketentuan pembiayaan resmi SPMB SMK Telkom Lampung.' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. KEPUTUSAN & REKOMENDASI KAUR SPMB --}}
                <div class="border-2 border-slate-900 rounded-sm overflow-hidden">
                    <div class="bg-slate-900 text-white px-3 py-1 font-bold text-[8.5pt] flex items-center justify-between">
                        <span>III. REKOMENDASI AKHIR & PENETAPAN KELULUSAN KAUR SPMB</span>
                        <span class="bg-white text-slate-900 px-2 py-0.2 rounded text-[8pt] font-black uppercase">
                            {{ $sesi?->rekomendasi_akhir ?: 'BELUM DIPUTUSKAN' }}
                        </span>
                    </div>
                    <div class="p-2.5 text-[8pt] space-y-1">
                        <span class="font-bold text-slate-700 block">Keterangan Resmi Penetapan:</span>
                        <p class="text-slate-800 italic font-medium">
                            {{ $sesi?->catatan_akhir ?: 'Berdasarkan akumulasi hasil wawancara calon siswa dan orang tua, yang bersangkutan dinyatakan memenuhi kriteria seleksi SPMB SMK Telkom Lampung.' }}
                        </p>
                    </div>
                </div>

            </div>

            {{-- 5. SIGNATURE SECTION --}}
            <div class="pt-3">
                <table class="w-full text-center text-[8pt]" style="line-height: 1.25;">
                    <tr>
                        <td class="w-1/3">
                            <p class="text-slate-600">Pewawancara Siswa,</p>
                            <div class="h-14"></div>
                            <p class="font-bold text-slate-900 underline">{{ $sesi?->pewawancara_siswa_nama ?: '( ........................................ )' }}</p>
                            <p class="text-[7pt] text-slate-500">Petugas Piket SPMB</p>
                        </td>
                        <td class="w-1/3">
                            <p class="text-slate-600">Pewawancara Orang Tua,</p>
                            <div class="h-14"></div>
                            <p class="font-bold text-slate-900 underline">{{ $sesi?->pewawancara_ortu_nama ?: '( ........................................ )' }}</p>
                            <p class="text-[7pt] text-slate-500">Petugas Piket SPMB</p>
                        </td>
                        <td class="w-1/3">
                            <p class="text-slate-600">Pringsewu, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
                            <p class="text-slate-600 font-semibold">Kaur SPMB SMK Telkom Lampung,</p>
                            <div class="h-11"></div>
                            <p class="font-bold text-slate-900 underline">{{ $namaKaur }}</p>
                            <p class="text-[7pt] text-slate-500">Kaur SPMB</p>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- LEMBAR 2: DOKUMENTASI & RINGKASAN TRANSKRIP JAWABAN      --}}
    {{-- ======================================================== --}}
    <div class="page-sheet">
        {{-- KOP BANNER LEMBAR 2 --}}
        <div class="kop-banner w-full flex-shrink-0">
            @if(!empty($kopBase64))
                <img src="{{ $kopBase64 }}" alt="Kop Surat" class="w-full block" style="width: 100%; display: block; margin: 0; padding: 0;">
            @endif
        </div>

        <div class="doc-body flex-1 px-8 py-4 space-y-4" style="font-size: 8pt; line-height: 1.3;">
            <div class="border-b border-slate-900 pb-1.5 flex items-center justify-between">
                <h4 class="font-bold text-[9.5pt] uppercase text-slate-900">
                    LAMPIRAN I: DOKUMENTASI & RESUME BUTIR WAWANCARA
                </h4>
                <span class="font-mono text-[8pt] text-slate-600">{{ $pesertaSeleksi->nomor_pendaftaran }} - {{ $pesertaSeleksi->nama_lengkap }}</span>
            </div>

            {{-- FOTO DOKUMENTASI JIKA ADA --}}
            @if($sesi?->foto_siswa || $sesi?->foto_ortu)
                <div class="grid grid-cols-2 gap-4">
                    <div class="border border-slate-300 rounded p-2 text-center bg-slate-50">
                        <span class="font-bold text-[8pt] text-slate-700 block mb-1">Dokumentasi Wawancara Siswa</span>
                        @if($sesi?->foto_siswa)
                            <img src="{{ $sesi->foto_siswa_url }}" alt="Wawancara Siswa" class="h-44 mx-auto object-cover rounded border border-slate-200">
                        @else
                            <div class="h-44 flex items-center justify-center text-slate-400 italic text-[8pt]">Tidak ada dokumentasi foto</div>
                        @endif
                    </div>
                    <div class="border border-slate-300 rounded p-2 text-center bg-slate-50">
                        <span class="font-bold text-[8pt] text-slate-700 block mb-1">Dokumentasi Wawancara Orang Tua</span>
                        @if($sesi?->foto_ortu)
                            <img src="{{ $sesi->foto_ortu_url }}" alt="Wawancara Orang Tua" class="h-44 mx-auto object-cover rounded border border-slate-200">
                        @else
                            <div class="h-44 flex items-center justify-center text-slate-400 italic text-[8pt]">Tidak ada dokumentasi foto</div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- HIGHLIGHT JAWABAN PENTING --}}
            <div class="space-y-2">
                <span class="font-bold text-[8.5pt] text-slate-900 block border-b border-slate-300 pb-1">Ringkasan Butir Pertanyaan Pokok:</span>
                <table class="table-doc">
                    <thead>
                        <tr>
                            <th class="w-8">No</th>
                            <th class="w-32">Kategori</th>
                            <th>Pertanyaan / Indikator</th>
                            <th class="w-56">Jawaban Terpilih</th>
                            <th class="w-16">Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $selectedKeys = [
                                'jurusan_pilihan', 'cita_cita', 'kelebihan', 'sikap_emosional', 'merokok',
                                'pekerjaan_orang_tua', 'penghasilan_orang_tua', 'dukungan_jurusan', 'kesiapan_dsp'
                            ];
                            $noUrut = 1;
                        @endphp
                        @foreach($pertanyaansSiswa->concat($pertanyaansOrtu) as $pt)
                            @if(in_array($pt->field_key, $selectedKeys) || $pt->bobot >= 7)
                                @php
                                    $jw = $jawabansMap->get($pt->id);
                                    $textJawaban = '-';
                                    if ($jw) {
                                        if ($pt->tipe_input === 'checkbox' && is_array($jw->jawaban_pilihan)) {
                                            $textJawaban = implode(', ', $jw->jawaban_pilihan);
                                        } elseif ($pt->tipe_input === 'scale_1_5') {
                                            $textJawaban = "Skala {$jw->jawaban_skala}/5";
                                        } else {
                                            $textJawaban = $jw->jawaban_teks ?: ($jw->jawaban_other ?: '-');
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center font-bold">{{ $noUrut++ }}</td>
                                    <td class="font-semibold uppercase text-[7.5pt] {{ $pt->kategori === 'siswa' ? 'text-red-700' : 'text-purple-700' }}">
                                        {{ $pt->kategori === 'siswa' ? 'Calon Siswa' : 'Orang Tua' }}
                                    </td>
                                    <td>{{ $pt->pertanyaan }}</td>
                                    <td class="font-medium">{{ \Illuminate\Support\Str::limit($textJawaban, 60) }}</td>
                                    <td class="text-center font-bold">{{ $jw && $jw->skor_diperoleh > 0 ? '+' . $jw->skor_diperoleh : '-' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="text-[7.5pt] text-slate-500 italic text-right pt-2">
                Dokumen resmi sistem informasi SPMB SMK Telkom Lampung &bull; Dicetak secara otomatis pada {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>

</body>
</html>
