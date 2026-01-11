<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Izin Guru</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #666; }
        .meta { margin-bottom: 15px; }
        .meta table { width: 100%; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .footer { margin-top: 30px; text-align: right; }
        .signature { margin-top: 50px; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 5px; background: #eee; border-radius: 3px; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekapitulasi Izin Guru</h1>
        <p>Laporan Kehadiran & Perizinan Tenaga Pendidik</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td style="border:none; width: 150px;">Periode Laporan</td>
                <td style="border:none;">: 
                    @if($start_date && $end_date)
                        {{ \Carbon\Carbon::parse($start_date)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->translatedFormat('d F Y') }}
                    @else
                        Semua Waktu
                    @endif
                </td>
            </tr>
            <tr>
                <td style="border:none;">Tanggal Cetak</td>
                <td style="border:none;">: {{ now()->translatedFormat('d F Y, H:i') }}</td>
            </tr>
            <tr>
                <td style="border:none;">Dicetak Oleh</td>
                <td style="border:none;">: {{ $sdm_name }} (KAUR SDM)</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30" class="text-center">No</th>
                <th>Nama Guru</th>
                <th>Waktu Izin</th>
                <th>Kategori</th>
                <th>Jenis</th>
                <th>Alasan / Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($izins as $index => $izin)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $izin->guru->nama_lengkap }}</strong><br>
                        <small>NIP: {{ $izin->guru->nip ?? '-' }}</small>
                    </td>
                    <td>
                        @if($izin->tanggal_mulai->isSameDay($izin->tanggal_selesai))
                            {{ $izin->tanggal_mulai->translatedFormat('d F Y') }}
                        @else
                            {{ $izin->tanggal_mulai->translatedFormat('d/m/Y') }} - {{ $izin->tanggal_selesai->translatedFormat('d/m/Y') }}
                        @endif
                    </td>
                    <td><span class="badge">{{ $izin->kategori_penyetujuan === 'sekolah' ? 'SEKOLAH' : ($izin->kategori_penyetujuan === 'terlambat' ? 'TERLAMBAT' : 'LUAR') }}</span></td>
                    <td><span class="badge">{{ strtoupper($izin->jenis_izin) }}</span></td>
                    <td>{{ $izin->deskripsi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data pengajuan dalam periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <div class="signature">
            <p>Admin KAUR SDM,</p>
            <br><br><br>
            <p><strong>{{ $sdm_name }}</strong></p>
        </div>
    </div>
</body>
</html>
