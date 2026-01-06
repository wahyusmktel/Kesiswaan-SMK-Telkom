<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Izin Meninggalkan Kelas</title>
    <style>
        @page {
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
            padding: 25px 35px;
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
            margin-bottom: 30px;
        }
        table.data td {
            padding: 8px 4px;
            vertical-align: top;
            border-bottom: 1px solid #eee;
        }
        table.data tr:last-child td {
            border-bottom: none;
        }
        table.data td.label {
            width: 150px;
            font-weight: bold;
            color: #444;
        }
        table.data td.value {
            color: #000;
        }
        .highlight {
            font-weight: bold;
        }

        .signatures-section {
            width: 100%;
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }
        
        table.sig-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table.sig-table td {
            width: 33.33%;
            vertical-align: top;
            text-align: center;
            padding: 0 10px;
        }

        .sig-role {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 50px;
            text-transform: uppercase;
        }
        
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 10pt;
        }

        .qr-group {
            margin-bottom: 15px;
            text-align: center;
        }

        .qr-wrapper {
            display: inline-block;
            text-align: center;
            margin: 0 5px;
        }
        
        .qr-img {
            width: 65px;
            height: 65px;
            padding: 4px;
            border: 1px solid #ddd;
            background: #fff;
        }
        
        .qr-caption {
            font-size: 6pt;
            margin-top: 4px;
            color: #666;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .date-line {
            text-align: right;
            margin-bottom: 15px;
            font-size: 9pt;
            font-style: italic;
        }

        .info-note {
            margin-top: 20px;
            font-size: 8pt;
            color: #777;
            border-left: 3px solid #ddd;
            padding-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Surat Izin Meninggalkan Kelas</h1>
            <p>SMK Telkom Lampung</p>
        </div>

        <div class="intro">
            @if(($izin->jenis_izin ?? 'keluar_sekolah') === 'keluar_sekolah')
                Diberikan izin kepada siswa di bawah ini untuk meninggalkan lingkungan sekolah sesuai dengan keterangan berikut:
            @else
                Diberikan izin kepada siswa di bawah ini untuk meninggalkan KELAS (berada di lingkungan sekolah) sesuai dengan keterangan berikut:
            @endif
        </div>

        <table class="data">
            <tr>
                <td class="label">Nama Siswa</td>
                <td class="value highlight">{{ $izin->siswa->name }}</td>
            </tr>
            <tr>
                <td class="label">Kelas / Rombel</td>
                <td class="value">{{ $izin->siswa->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Tujuan Keperluan</td>
                <td class="value">"{{ $izin->tujuan }}"</td>
            </tr>
            @if ($izin->jadwalPelajaran)
            <tr>
                <td class="label">Mata Pelajaran</td>
                <td class="value">
                    <strong>{{ $izin->jadwalPelajaran->mataPelajaran->nama_mapel }}</strong><br>
                    <span style="font-size: 9pt; color: #666;">Guru Pengajar: {{ $izin->jadwalPelajaran->guru->nama_lengkap }}</span>
                </td>
            </tr>
            @endif
            <tr>
                <td class="label">Estimasi Kembali</td>
                <td class="value highlight" style="color: #b91c1c;">{{ \Carbon\Carbon::parse($izin->estimasi_kembali)->format('H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="label">Berlaku Tanggal</td>
                <td class="value">{{ \Carbon\Carbon::parse($izin->created_at)->isoFormat('dddd, D MMMM YYYY') }}</td>
            </tr>
        </table>

        <div class="date-line">
            Lampung, {{ \Carbon\Carbon::parse($izin->created_at)->isoFormat('D MMMM YYYY') }}
        </div>

        <div class="signatures-section">
            <table class="sig-table">
                <tr>
                    <td>
                        <div class="sig-role">Guru Kelas</div>
                        <div class="sig-name">{{ $izin->guruKelasApprover->name ?? '..........................' }}</div>
                    </td>
                    <td>
                        <div class="sig-role">Guru Piket</div>
                        <div class="sig-name">{{ $izin->guruPiketApprover->name ?? '..........................' }}</div>
                    </td>
                    <td>
                        <div class="sig-role">Security / Verifikator</div>
                        <div class="qr-group">
                            <div class="qr-wrapper">
                                <img src="{{ $securityQrCodeBase64 }}" class="qr-img">
                                <div class="qr-caption">Akses<br>Petugas</div>
                            </div>
                            <div class="qr-wrapper">
                                <img src="{{ $publicQrCodeBase64 }}" class="qr-img">
                                <div class="qr-caption">Cek<br>Validitas</div>
                            </div>
                        </div>
                        <div class="sig-name">{{ $izin->securityVerifier->name ?? '..........................' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="info-note">
            * Surat ini adalah dokumen resmi digital. Keabsahan dapat diverifikasi melalui pemindaian QR Code yang tersedia.
            Siswa wajib kembali tepat waktu sesuai dengan estimasi yang telah ditentukan.
        </div>
    </div>
</body>
</html>
