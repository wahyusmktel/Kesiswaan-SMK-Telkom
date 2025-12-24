<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Panggilan Orang Tua</title>
    <style>
        body { font-family: 'Times New Roman', serif; line-height: 1.6; color: #333; margin: 40px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 20px; margin-bottom: 30px; }
        .school-name { font-size: 20pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .school-address { font-size: 10pt; margin: 5px 0; }
        .letter-meta { margin-bottom: 30px; }
        .letter-meta table { width: 100%; border-collapse: collapse; }
        .content { margin-bottom: 30px; }
        .footer { float: right; width: 250px; text-align: center; margin-top: 50px; }
        .signature-space { height: 80px; }
        .bold { font-weight: bold; }
        .student-box { border: 1px solid #ddd; padding: 15px; background: #f9f9f9; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="school-name">{{ config('app.name', 'SMK TELKOM') }}</h1>
        <p class="school-address">Jl. Pahlawan No. 123, Purwokerto, Jawa Tengah</p>
        <p class="school-address">Email: info@smktelkom-pwt.sch.id | Telp: (0281) 123456</p>
    </div>

    <div class="letter-meta">
        <table>
            <tr>
                <td width="100">Nomor</td>
                <td>: {{ $panggilan->nomor_surat }}</td>
                <td align="right">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>: -</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td class="bold">: {{ $panggilan->perihal }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <p>Kepada Yth.<br>Orang Tua / Wali Murid dari:</p>
        
        <div class="student-box">
            <p style="margin: 0;">Nama: <span class="bold">{{ $panggilan->siswa->nama_lengkap }}</span></p>
            <p style="margin: 5px 0;">NIS: {{ $panggilan->siswa->nis }}</p>
            <p style="margin: 0;">Kelas: {{ $panggilan->siswa->rombels->first()?->kelas->nama_kelas }}</p>
        </div>

        <p>Dengan hormat,</p>
        <p>Sehubungan dengan perkembangan pembinaan kedisiplinan putra/putri Bapak/Ibu di sekolah, di mana saat ini poin pelanggaran siswa tersebut telah mencapai ambang batas yang memerlukan perhatian khusus, maka kami mengharap kehadiran Bapak/Ibu pada:</p>

        <table style="margin-left: 30px; margin-top: 20px;">
            <tr>
                <td width="100">Hari / Tanggal</td>
                <td>: {{ \Carbon\Carbon::parse($panggilan->tanggal_panggilan)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>: Pukul {{ date('H:i', strtotime($panggilan->jam_panggilan)) }} WIB</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td>: {{ $panggilan->tempat_panggilan }}</td>
            </tr>
            <tr>
                <td>Agenda</td>
                <td>: Konsultasi perkembangan kedisiplinan siswa</td>
            </tr>
        </table>

        <p style="margin-top: 30px;">Mengingat pentingnya acara ini demi masa depan pendidikan putra/putri Bapak/Ibu, kami sangat mengharap kehadiran Bapak/Ibu tepat pada waktunya.</p>
        <p>Demikian surat ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.</p>
    </div>

    <div class="footer">
        <p>Waka Kesiswaan</p>
        <div class="signature-space"></div>
        <p class="bold">{{ $panggilan->creator->name }}</p>
        <p>NIP. ...........................</p>
    </div>
</body>
</html>
