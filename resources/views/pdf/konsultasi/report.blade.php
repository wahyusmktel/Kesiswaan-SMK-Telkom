<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Konsultasi BK</title>
    <style>
        body { font-family: 'Times New Roman', serif; line-height: 1.6; color: #333; margin: 40px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 20px; margin-bottom: 30px; }
        .school-name { font-size: 18pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .school-address { font-size: 10pt; margin: 5px 0; }
        .doc-title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 30px; }
        .letter-meta { margin-bottom: 20px; }
        .content { margin-bottom: 30px; }
        .bold { font-weight: bold; }
        .data-table { width: 100%; margin: 20px 0; border-collapse: collapse; }
        .data-table td { padding: 8px 0; vertical-align: top; }
        .result-box { border: 1px solid #000; padding: 15px; margin-top: 10px; min-height: 150px; }
        .footer { margin-top: 50px; width: 100%; }
        .signature-box { float: left; width: 33%; text-align: center; font-size: 10pt; }
        .signature-space { height: 70px; }
        .iso-code { position: fixed; bottom: 0; left: 0; font-size: 8pt; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="school-name">{{ config('app.name', 'SMK TELKOM') }}</h1>
        <p class="school-address">Jl. Pahlawan No. 123, Purwokerto, Jawa Tengah</p>
        <p class="school-address">Email: info@smktelkom-pwt.sch.id | Telp: (0281) 123456</p>
    </div>

    <div class="doc-title">BERITA ACARA KONSULTASI INDIVIDUAL</div>

    <div class="content">
        <p>Pada hari ini <b>{{ \Carbon\Carbon::parse($jadwal->updated_at)->translatedFormat('l') }}</b> tanggal <b>{{ \Carbon\Carbon::parse($jadwal->updated_at)->translatedFormat('d F Y') }}</b>, telah dilaksanakan kegiatan layanan Bimbingan dan Konsultasi Individual terhadap:</p>

        <table class="data-table">
            <tr>
                <td width="150" class="bold">Nama Siswa</td>
                <td>: {{ $jadwal->siswa->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="bold">NIS / Kelas</td>
                <td>: {{ $jadwal->siswa->nis }} / {{ $jadwal->siswa->rombels->first()?->nama_rombel ?? $jadwal->siswa->rombels->first()?->kelas->nama_kelas }}</td>
            </tr>
            <tr>
                <td class="bold">Perihal / Masalah</td>
                <td>: {{ $jadwal->perihal }}</td>
            </tr>
            <tr>
                <td class="bold">Guru BK</td>
                <td>: {{ $jadwal->guruBK->name }}</td>
            </tr>
        </table>

        <p class="bold">Ringkasan Kegiatan & Hasil Konsultasi:</p>
        <div class="result-box">
            {{ $jadwal->catatan_bk ?? 'Siswa telah mengikuti sesi bimbingan dengan baik. Masalah yang dihadapi telah didiskusikan dan diberikan arahan/solusi sesuai dengan standar pelayanan bimbingan konseling.' }}
        </div>

        <p style="margin-top: 20px;">Tindak Lanjut:</p>
        <p>..........................................................................................................................................................................</p>
    </div>

    <div class="footer">
        <div class="signature-box">
            <p>Siswa,</p>
            <div class="signature-space"></div>
            <p class="bold"><u>{{ $jadwal->siswa->nama_lengkap }}</u></p>
        </div>
        <div class="signature-box">
            <p>Mengetahui,<br>Waka Kesiswaan</p>
            <div class="signature-space"></div>
            <p class="bold"><u>....................................</u></p>
        </div>
        <div class="signature-box">
            <p>Purwokerto, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Guru BK,</p>
            <div class="signature-space"></div>
            <p class="bold"><u>{{ $jadwal->guruBK->name }}</u></p>
        </div>
    </div>

    <div class="iso-code">Doc Code: ISO-BK-KONS-BA-2025</div>
</body>
</html>
