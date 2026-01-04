<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Izin Masuk Kelas</title>
    <style>
        @page {
            /* Standard A5 size usually works well for slips, or half-letter. 
               We'll use auto size or standard layout. 
               Dompdf default is usually A4 portrait. We'll make it fit nicely. */
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }
        .container {
            border: 1px solid #000;
            padding: 20px 30px;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            margin-bottom: 25px;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            color: #000;
        }
        .header p {
            margin: 3px 0 0;
            font-size: 10pt;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .intro {
            margin-bottom: 20px;
            font-style: italic;
            font-size: 9pt;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table.data td {
            padding: 6px 4px;
            vertical-align: top;
            border-bottom: 1px solid #eee;
        }
        table.data tr:last-child td {
            border-bottom: none;
        }
        table.data td.label {
            width: 140px;
            font-weight: bold;
            color: #444;
        }
        table.data td.value {
            color: #000;
        }
        .highlight {
            font-weight: bold;
        }

        .footer-table {
            width: 100%;
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }
        .qr-cell {
            width: 50%;
            vertical-align: top;
        }
        .sig-cell {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding-left: 20px;
        }
        
        .qr-wrapper {
            display: inline-block;
            text-align: center;
            margin-right: 15px;
            vertical-align: top;
        }
        .qr-img {
            width: 60px;
            height: 60px;
            padding: 4px;
            border: 1px solid #ddd;
            background: #fff;
        }
        .qr-caption {
            font-size: 6pt;
            margin-top: 5px;
            color: #666;
            text-transform: uppercase;
        }

        .date-line {
            margin-bottom: 5px;
            font-size: 9pt;
        }
        .role-line {
            font-weight: bold;
            margin-bottom: 50px;
            font-size: 9pt;
        }
        .name-line {
            font-weight: bold;
            text-decoration: underline;
            font-size: 10pt;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Surat Izin Masuk Kelas</h1>
            <p>SMK Telkom Lampung</p>
        </div>

        <div class="intro">
            Dengan ini menerangkan bahwa siswa berikut telah dicatat keterlambatannya dan diizinkan untuk mengikuti pelajaran:
        </div>

        <table class="data">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="value highlight">{{ $keterlambatan->siswa->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="label">Kelas / Jurusan</td>
                <td class="value">{{ $keterlambatan->siswa->rombels->first()?->kelas->nama_kelas ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Waktu Kedatangan</td>
                <td class="value">{{ \Carbon\Carbon::parse($keterlambatan->waktu_dicatat_security)->format('H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="label">Alasan Terlambat</td>
                <td class="value">"{{ $keterlambatan->alasan_siswa }}"</td>
            </tr>
            @if ($keterlambatan->tindak_lanjut_piket)
            <tr>
                <td class="label">Tindak Lanjut</td>
                <td class="value">{{ $keterlambatan->tindak_lanjut_piket }}</td>
            </tr>
            @endif
            @if ($keterlambatan->jadwalPelajaran)
            <tr>
                <td class="label">Jadwal Pelajaran</td>
                <td class="value">
                    <strong>{{ $keterlambatan->jadwalPelajaran->mataPelajaran->nama_mapel }}</strong> <br>
                    <span style="font-size: 9pt; color: #666;">
                        Jam Ke-{{ $keterlambatan->jadwalPelajaran->jam_ke }} • Pengajar: {{ $keterlambatan->jadwalPelajaran->guru->nama_lengkap }}
                    </span>
                </td>
            </tr>
            @else
            <tr>
                <td class="label">Jadwal Pelajaran</td>
                <td class="value" style="color: #888; font-style: italic;">Saat ini tidak ada jadwal pelajaran aktif.</td>
            </tr>
            @endif
        </table>

        <table class="footer-table">
            <tr>
                <td class="qr-cell">
                    <div class="qr-wrapper">
                        <img src="{{ $guruKelasQrCode }}" class="qr-img" alt="QR Guru Kelas">
                        <div class="qr-caption">Pindai oleh<br>Guru Kelas</div>
                    </div>
                    <div class="qr-wrapper">
                        <img src="{{ $publicQrCode }}" class="qr-img" alt="QR Keabsahan">
                        <div class="qr-caption">Verifikasi<br>Keabsahan</div>
                    </div>
                </td>
                <td class="sig-cell">
                    <div class="date-line">Bandar Lampung, {{ now()->isoFormat('D MMMM YYYY') }}</div>
                    <div class="role-line">Guru Piket</div>
                    <div class="name-line">{{ $keterlambatan->guruPiket->name }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
