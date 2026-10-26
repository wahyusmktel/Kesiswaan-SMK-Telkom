@php
    $barcodeImg = $barcode ?? null;
    if (!$barcodeImg) {
        try {
            $gen = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcodeImg = base64_encode($gen->getBarcode($siswa->nis, $gen::TYPE_CODE_128, 2, 45));
        } catch (\Throwable $e) {
            $barcodeImg = null;
        }
    }

    $tempat = $siswa->tempat_lahir ?? $siswa->dapodik?->tempat_lahir ?? '-';
    $tgl = $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->isoFormat('D MMM Y') : ($siswa->dapodik?->tanggal_lahir ? \Carbon\Carbon::parse($siswa->dapodik->tanggal_lahir)->isoFormat('D MMM Y') : '-');
    $jk = $siswa->jenis_kelamin ?? $siswa->dapodik?->jenis_kelamin;
    $jkText = ($jk === 'L' || strtolower($jk) === 'laki-laki') ? 'Laki-laki' : (($jk === 'P' || strtolower($jk) === 'perempuan') ? 'Perempuan' : ($jk ?? '-'));
    $agama = $siswa->dapodik?->agama ?? $siswa->studentRegistration?->agama ?? 'Islam';
    $alamat = $siswa->alamat ?? $siswa->dapodik?->alamat ?? '-';

    // Kalkulasi dinamis font & padding berdasarkan panjang alamat agar sejajar presisi dengan kotak foto
    $lenAlamat = strlen($alamat);
    if ($lenAlamat > 65) {
        $fontSizeTable = '4.5pt';
        $paddingRow = '0.22mm 0';
        $lineHeightTable = '1.05';
        $fontSizeNama = '6.4pt';
    } elseif ($lenAlamat > 38) {
        $fontSizeTable = '4.9pt';
        $paddingRow = '0.35mm 0';
        $lineHeightTable = '1.12';
        $fontSizeNama = '6.8pt';
    } else {
        $fontSizeTable = '5.4pt';
        $paddingRow = '0.45mm 0';
        $lineHeightTable = '1.18';
        $fontSizeNama = '7.2pt';
    }
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,700&family=JetBrains+Mono:wght@600;700;800&display=swap');

    .card-standar-ts-modern {
        position: relative;
        width: 85.6mm;
        height: 54mm;
        background: #ffffff;
        box-sizing: border-box;
        overflow: hidden;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }

    .card-standar-ts-modern * {
        box-sizing: border-box;
    }
</style>

<div class="card-standar-ts-modern" style="position: relative; width: 85.6mm; height: 54mm; background: #ffffff; overflow: hidden; border: 1px solid #e2e8f0;">

    {{-- EFEK GARIS SIRKUIT ELEKTRONIK (CIRCUIT BOARD) GRADASI TRANSPARAN --}}
    <svg style="position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1;" viewBox="0 0 85.6 54" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="circuitGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#b91c1c" stop-opacity="0.14"/>
                <stop offset="35%" stop-color="#64748b" stop-opacity="0.09"/>
                <stop offset="70%" stop-color="#b91c1c" stop-opacity="0.10"/>
                <stop offset="100%" stop-color="#0f172a" stop-opacity="0.08"/>
            </linearGradient>
        </defs>

        <g stroke="url(#circuitGrad)" stroke-width="0.35" stroke-linecap="round" stroke-linejoin="round">
            <!-- Jalur Atas Kiri -->
            <path d="M 0 16 L 15 16 L 22 23 L 38 23 L 42 19 L 55 19" />
            <circle cx="55" cy="19" r="0.7" fill="#b91c1c" fill-opacity="0.18" />

            <!-- Jalur Tengah -->
            <path d="M 23 27 L 35 27 L 40 32 L 60 32 L 64 36 L 70 36" />
            <circle cx="23" cy="27" r="0.6" fill="#b91c1c" fill-opacity="0.16" />
            <circle cx="70" cy="36" r="0.7" fill="#0f172a" fill-opacity="0.15" />

            <!-- Jalur Bawah Kiri ke Tengah -->
            <path d="M 2 35 L 12 35 L 18 41 L 32 41 L 36 37 L 50 37" />
            <circle cx="2" cy="35" r="0.6" fill="#b91c1c" fill-opacity="0.15" />
            <circle cx="50" cy="37" r="0.6" fill="#64748b" fill-opacity="0.15" />

            <!-- Jalur Menuju Barcode & Bawah -->
            <path d="M 5 47 L 20 47 L 26 53 L 45 53" />
            <circle cx="5" cy="47" r="0.6" fill="#b91c1c" fill-opacity="0.15" />

            <!-- Jalur Kanan Atas -->
            <path d="M 85.6 15 L 72 15 L 66 21 L 66 28 L 61 33" />
            <circle cx="72" cy="15" r="0.7" fill="#b91c1c" fill-opacity="0.18" />
            <circle cx="61" cy="33" r="0.6" fill="#64748b" fill-opacity="0.15" />

            <!-- Jalur Kanan Bawah -->
            <path d="M 85.6 38 L 78 38 L 73 43 L 64 43" />
            <circle cx="78" cy="38" r="0.6" fill="#b91c1c" fill-opacity="0.15" />
            <circle cx="64" cy="43" r="0.6" fill="#0f172a" fill-opacity="0.15" />

            <!-- IC Microprocessor Nodes di Latar Belakang Tengah -->
            <rect x="52" y="24" width="4" height="4" rx="0.5" stroke="url(#circuitGrad)" stroke-width="0.3" fill="none" opacity="0.4"/>
            <line x1="50.5" y1="25" x2="52" y2="25"/>
            <line x1="50.5" y1="27" x2="52" y2="27"/>
            <line x1="56" y1="25" x2="57.5" y2="25"/>
            <line x1="56" y1="27" x2="57.5" y2="27"/>
        </g>
    </svg>

    {{-- HEADER KARTU --}}
    <div style="position: relative; height: 11.5mm; width: 100%; background: linear-gradient(135deg, #b91c1c 0%, #dc2626 55%, #e11d48 100%); display: flex; align-items: center; justify-content: space-between; padding: 0 3.5mm; z-index: 10;">
        
        {{-- Aksen Geometris Header --}}
        <div style="position: absolute; right: 0; top: 0; bottom: 0; width: 35mm; overflow: hidden; pointer-events: none;">
            <div style="position: absolute; right: -5mm; top: -5mm; width: 25mm; height: 25mm; background: rgba(15, 23, 42, 0.15); transform: rotate(25deg);"></div>
            <div style="position: absolute; right: 9mm; top: -5mm; width: 3.5mm; height: 25mm; background: rgba(255, 255, 255, 0.12); transform: rotate(25deg);"></div>
        </div>

        {{-- Sisi Kiri Header: Logo Telkom Schools Baru Putih (Tanpa background) --}}
        <div style="display: flex; align-items: center; position: relative; z-index: 2;">
            <img src="{{ asset('images/kartu-akses/telkom-schools-new-white.png') }}" alt="Telkom Schools"
                style="height: 7.2mm; width: auto; object-fit: contain; display: block;"
                onerror="this.src='{{ asset('images/kartu-akses/telkom-schools-new.webp') }}'">
        </div>

        {{-- Sisi Kanan Header: Teks "KARTU PELAJAR" (Dipindahkan ke posisi ini) --}}
        <div style="position: relative; z-index: 2; display: flex; align-items: center;">
            <span style="color: #ffffff; font-size: 8.5pt; font-weight: 900; letter-spacing: 0.8px; line-height: 1; text-transform: uppercase;">
                KARTU PELAJAR
            </span>
        </div>
    </div>

    {{-- KONTEN UTAMA KARTU --}}
    <div style="position: relative; height: 41mm; padding: 1.8mm 3mm 1mm 3mm; z-index: 5;">
        
        {{-- BARIS ATAS: Foto Siswa + Identitas (Sejajar Tinggi) --}}
        <div style="display: flex; gap: 2.8mm; height: 26mm;">
            
            {{-- Foto Siswa (Tinggi 26mm) --}}
            <div style="width: 20mm; height: 26mm; border-radius: 1.8mm; overflow: hidden; background: #f1f5f9; border: 1px solid #cbd5e1; box-shadow: 0 1px 2px rgba(0,0,0,0.06); display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;">
                @if($siswa->foto_url)
                    <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama_lengkap }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                @else
                    <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; color: #94a3b8;">
                        <svg style="width: 8.5mm; height: 8.5mm; fill: currentColor;" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        <span style="font-size: 3pt; font-weight: 700; color: #94a3b8; margin-top: 0.4mm;">NO PHOTO</span>
                    </div>
                @endif
            </div>

            {{-- Biodata Identitas Siswa: Teks Dinamis Menyesuaikan Panjang Alamat, Sejajar Rata dengan Kotak Foto --}}
            <div style="flex: 1; min-width: 0; height: 26mm; max-height: 26mm; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; padding-right: 21mm;">
                
                {{-- Nama Siswa (Elegan & Menonjol) --}}
                <div style="padding-bottom: 0.4mm; border-bottom: 1.2px solid #e11d48; margin-bottom: 0.3mm;">
                    <div style="font-size: {{ $fontSizeNama }}; font-weight: 900; color: #0f172a; text-transform: uppercase; line-height: 1.1; letter-spacing: 0.1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $siswa->nama_lengkap }}
                    </div>
                </div>

                {{-- Tabel Identitas (Font & Spasi Dinamis Otomatis Menyesuaikan Panjang Alamat) --}}
                <table style="width: 100%; border-collapse: collapse; font-size: {{ $fontSizeTable }}; line-height: {{ $lineHeightTable }}; flex: 1; margin: auto 0;">
                    <tr>
                        <td style="width: 22mm; color: #475569; font-weight: 700; white-space: nowrap; padding: {{ $paddingRow }}; vertical-align: top;">NIS</td>
                        <td style="width: 2mm; color: #94a3b8; padding: {{ $paddingRow }}; vertical-align: top;">:</td>
                        <td style="font-family: 'JetBrains Mono', monospace; font-weight: 800; color: #0f172a; padding: {{ $paddingRow }}; vertical-align: top;">
                            {{ $siswa->nis }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #475569; font-weight: 700; white-space: nowrap; padding: {{ $paddingRow }}; vertical-align: top;">Tempat, Tgl Lahir</td>
                        <td style="color: #94a3b8; padding: {{ $paddingRow }}; vertical-align: top;">:</td>
                        <td style="font-weight: 700; color: #1e293b; padding: {{ $paddingRow }}; vertical-align: top; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 32mm;">
                            {{ $tempat }}, {{ $tgl }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #475569; font-weight: 700; white-space: nowrap; padding: {{ $paddingRow }}; vertical-align: top;">Jenis Kelamin</td>
                        <td style="color: #94a3b8; padding: {{ $paddingRow }}; vertical-align: top;">:</td>
                        <td style="font-weight: 700; color: #1e293b; padding: {{ $paddingRow }}; vertical-align: top;">
                            {{ $jkText }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #475569; font-weight: 700; white-space: nowrap; padding: {{ $paddingRow }}; vertical-align: top;">Agama</td>
                        <td style="color: #94a3b8; padding: {{ $paddingRow }}; vertical-align: top;">:</td>
                        <td style="font-weight: 700; color: #1e293b; padding: {{ $paddingRow }}; vertical-align: top;">
                            {{ $agama }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #475569; font-weight: 700; white-space: nowrap; padding: {{ $paddingRow }}; vertical-align: top;">Alamat</td>
                        <td style="color: #94a3b8; padding: {{ $paddingRow }}; vertical-align: top;">:</td>
                        <td style="font-weight: 600; color: #334155; padding: {{ $paddingRow }}; vertical-align: top; word-break: break-word; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; max-height: 5.5mm;">
                            {{ $alamat }}
                        </td>
                    </tr>
                </table>

            </div>
        </div>

        {{-- BARIS BAWAH: BARCODE LEBIH PANJANG HINGGA MENDEKATI AREA TANDA TANGAN (TANPA BORDER OUTLINE) --}}
        <div style="position: absolute; left: 3mm; bottom: 1.8mm; width: 56.5mm; background: transparent; border: none; padding: 0.4mm 0; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            @if($barcodeImg)
                <img src="data:image/png;base64,{{ $barcodeImg }}" alt="Barcode" style="width: 100%; height: 7mm; object-fit: fill; display: block;">
            @endif
            <div style="font-family: 'JetBrains Mono', 'Consolas', monospace; font-size: 4.8pt; font-weight: 800; color: #0f172a; letter-spacing: 2px; line-height: 1; margin-top: 0.5mm;">
                {{ $siswa->nis }}
            </div>
        </div>

        {{-- POJOK KANAN BAWAH: TANDA TANGAN QR CODE KEPALA SEKOLAH (Rapat & Presisi) --}}
        <div style="position: absolute; right: 2.5mm; bottom: 1.8mm; width: 21mm; display: flex; flex-direction: column; align-items: center; text-align: center; z-index: 10;">
            <div style="font-size: 3.8pt; font-weight: 600; color: #475569; line-height: 1.1; margin-bottom: 0.3mm;">
                Pringsewu,<br>
                <span style="font-weight: 800; color: #0f172a;">Kepala SMK Telkom</span>
            </div>

            {{-- Box QR Code Tanda Tangan --}}
            <div style="margin: 0.3mm 0; display: flex; justify-content: center; align-items: center;">
                @if(!empty($kepsekData['has_signature']) && !empty($qrCodeKepsek))
                    <div style="padding: 0.5mm; background: #ffffff; border: 0.8px solid #cbd5e1; border-radius: 1mm; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <img src="{{ $qrCodeKepsek }}" alt="TTD QR Code" style="width: 12.5mm; height: 12.5mm; display: block;">
                    </div>
                @else
                    <div style="width: 12.5mm; height: 12.5mm; border: 0.6px dashed #cbd5e1; border-radius: 1mm; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 3pt; color: #94a3b8; line-height: 1.1; background: #f8fafc;">
                        <span>TTD DIGITAL</span>
                        <span>BELUM AKTIF</span>
                    </div>
                @endif
            </div>

            <div style="margin-top: 0.3mm; width: 100%;">
                <div style="font-size: 4.5pt; font-weight: 800; text-decoration: underline; text-underline-offset: 1px; color: #0f172a; line-height: 1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 21mm;">
                    {{ $kepsekData['nama'] }}
                </div>
                <div style="font-size: 3.8pt; font-weight: 600; color: #64748b; line-height: 1.1; margin-top: 0.2mm; font-family: 'JetBrains Mono', monospace;">
                    NIP - {{ $kepsekData['nip'] }}
                </div>
            </div>
        </div>

    </div>

    {{-- FOOTER STRIP KARTU --}}
    <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 1.2mm; background: linear-gradient(90deg, #dc2626 0%, #b91c1c 40%, #0f172a 100%);"></div>

</div>
