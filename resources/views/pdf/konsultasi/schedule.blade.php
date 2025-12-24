<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jadwal Konsultasi BK</title>
    <style>
        body { font-family: 'Times New Roman', serif; line-height: 1.6; color: #333; margin: 40px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 20px; margin-bottom: 30px; }
        .school-name { font-size: 18pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .school-address { font-size: 10pt; margin: 5px 0; }
        .doc-title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 30px; }
        .letter-meta { margin-bottom: 20px; }
        .letter-meta table { width: 100%; border-collapse: collapse; }
        .content { margin-bottom: 30px; }
        .bold { font-weight: bold; }
        .data-table { width: 100%; margin: 20px 0; border-collapse: collapse; }
        .data-table td { padding: 8px 0; vertical-align: top; }
        .footer { margin-top: 50px; width: 100%; }
        .signature-box { float: left; width: 45%; text-align: center; }
        .signature-box.right { float: right; }
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

    <div class="doc-title">JADWAL BIMBINGAN DAN KONSELING INDIVIDUAL</div>

    <div class="content">
        <p>Berdasarkan pengajuan dari siswa/permintaan Guru BK, telah dijadwalkan pertemuan konsultasi individual pada:</p>

        <table class="data-table">
            <tr>
                <td width="150" class="bold">Hari / Tanggal</td>
                <td>: {{ \Carbon\Carbon::parse($jadwal->tanggal_rencana)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td class="bold">Waktu</td>
                <td>: Pukul {{ date('H:i', strtotime($jadwal->jam_rencana)) }} WIB s.d Selesai</td>
            </tr>
            <tr>
                <td class="bold">Tempat</td>
                <td>: {{ $jadwal->tempat ?? 'Ruang Bimbingan & Konseling' }}</td>
            </tr>
            <tr>
                <td class="bold">Perihal / Masalah</td>
                <td>: {{ $jadwal->perihal }}</td>
            </tr>
        </table>

        <p>Identitas Peserta Konsultasi:</p>
        <table class="data-table">
            <tr>
                <td width="150" class="bold">Nama Siswa</td>
                <td>: {{ $jadwal->siswa->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="bold">NIS</td>
                <td>: {{ $jadwal->siswa->nis }}</td>
            </tr>
            <tr>
                <td class="bold">Kelas / Rombel</td>
                <td>: {{ $jadwal->siswa->rombels->first()?->nama_rombel ?? $jadwal->siswa->rombels->first()?->kelas->nama_kelas }}</td>
            </tr>
            <tr>
                <td class="bold">Guru BK</td>
                <td>: {{ $jadwal->guruBK->name }}</td>
            </tr>
        </table>

        <p style="margin-top: 20px;">Demikian jadwal ini dibuat untuk dipergunakan sebagaimana mestinya. Siswa harap hadir tepat waktu dengan membawa perlengkapan yang diperlukan.</p>
    </div>

    <div class="footer">
        <div class="signature-box">
            <p>Siswa,</p>
            <div class="signature-space"></div>
            <p class="bold">{{ $jadwal->siswa->nama_lengkap }}</p>
        </div>
        <div class="signature-box right">
            <p>Purwokerto, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Guru Pembimbing/BK,</p>
            <div class="signature-space"></div>
            <p class="bold">{{ $jadwal->guruBK->name }}</p>
        </div>
    </div>

    <div class="iso-code">Doc Code: ISO-BK-KONS-JWL-2025</div>
</body>
</html>
