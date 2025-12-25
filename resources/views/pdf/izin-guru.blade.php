<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Izin Guru</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .kop-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 20px; margin-bottom: 30px; }
        .kop-surat h2 { margin: 0; font-size: 20px; }
        .kop-surat p { margin: 5px 0 0; font-size: 12px; }
        .judul-surat { text-align: center; margin-bottom: 30px; }
        .judul-surat h3 { text-decoration: underline; margin-bottom: 5px; }
        .judul-surat p { margin: 0; font-size: 12px; }
        .content { margin-bottom: 30px; }
        .content table { width: 100%; border-collapse: collapse; }
        .content table td { padding: 5px 0; vertical-align: top; }
        .content table td:first-child { width: 150px; }
        .jadwal-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .jadwal-table th, .jadwal-table td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        .jadwal-table th { bg-color: #f5f5f5; }
        .footer { margin-top: 50px; }
        .signature-table { width: 100%; text-align: center; }
        .signature-table td { width: 33.33%; padding-bottom: 80px; font-size: 12px; }
        .signature-name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="kop-surat">
        <h2>SMK TELKOM LAMPUNG</h2>
        <p>Jl. Raya Gading Rejo, Gading Rejo, Kec. Gading Rejo, Kabupaten Pringsewu, Lampung 35372</p>
        <p>Email: info@smktelkom-lpg.sch.id | Website: www.smktelkom-lpg.sch.id</p>
    </div>

    <div class="judul-surat">
        <h3>SURAT IZIN MENINGGALKAN TUGAS</h3>
        <p>Nomor: {{ $izin->id }}/IZIN-GURU/{{ date('m/Y') }}</p>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
        <table>
            <tr>
                <td>Nama</td>
                <td>: <strong>{{ $izin->guru->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <td>NIP/NUPTK</td>
                <td>: {{ $izin->guru->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td>Unit Kerja</td>
                <td>: Tenaga Pendidik</td>
            </tr>
            <tr>
                <td>Alasan Izin</td>
                <td>: {{ $izin->jenis_izin }} ({{ $izin->deskripsi }})</td>
            </tr>
            <tr>
                <td>Waktu Izin</td>
                <td>: 
                    <strong>
                        @if($izin->tanggal_mulai->isSameDay($izin->tanggal_selesai))
                            {{ $izin->tanggal_mulai->translatedFormat('d F Y') }} ({{ $izin->tanggal_mulai->format('H:i') }} - {{ $izin->tanggal_selesai->format('H:i') }})
                        @else
                            {{ $izin->tanggal_mulai->translatedFormat('d F Y, H:i') }} s/d {{ $izin->tanggal_selesai->translatedFormat('d F Y, H:i') }}
                        @endif
                    </strong>
                </td>
            </tr>
        </table>

        <p>Memohon izin untuk meninggalkan tugas pada jam pelajaran berikut:</p>
        <table class="jadwal-table">
            <thead>
                <tr>
                    <th>Jam Ke</th>
                    <th>Waktu</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($izin->jadwals as $jadwal)
                <tr>
                    <td>{{ $jadwal->jam_ke }}</td>
                    <td>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                    <td>{{ $jadwal->rombel->kelas->nama_kelas }}</td>
                    <td>{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p style="text-align: right; margin-bottom: 20px;">Pringsewu, {{ now()->translatedFormat('d F Y') }}</p>
        <table class="signature-table">
            <tr>
                <td>Guru Piket</td>
                <td>Waka Kurikulum</td>
                <td>KAUR SDM</td>
            </tr>
            <tr>
                <td style="height: 60px;"></td>
                <td style="height: 60px;"></td>
                <td style="height: 60px;"></td>
            </tr>
            <tr>
                <td class="signature-name">{{ $izin->piket->name ?? '..........................' }}</td>
                <td class="signature-name">{{ $izin->kurikulum->name ?? '..........................' }}</td>
                <td class="signature-name">{{ $izin->sdm->name ?? '..........................' }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
