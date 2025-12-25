<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Pemutihan Poin</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; line-height: 1.6; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; }
        .content { margin-bottom: 30px; }
        .content h3 { border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        table th { background-color: #f9f9f9; width: 30%; font-weight: bold; }
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 4px; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        .status-disetujui { background-color: #dcfce7; color: #166534; }
        .status-ditolak { background-color: #fee2e2; color: #991b1b; }
        .footer { margin-top: 50px; }
        .signature-table { width: 100%; border: none; }
        .signature-table td { border: none; padding: 0; width: 50%; vertical-align: top; }
        .signature-box { text-align: center; margin-top: 20px; }
        .signature-line { margin-top: 60px; border-top: 1px solid #333; width: 200px; margin-left: auto; margin-right: auto; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Berita Acara Pemutihan Poin</h2>
        <p>SMK Telkom Lampung</p>
    </div>

    <div class="content">
        <h3>Informasi Siswa</h3>
        <table>
            <tr>
                <th>Nama Lengkap</th>
                <td>{{ $pemutihan->siswa->nama_lengkap }}</td>
            </tr>
            <tr>
                <th>NIS</th>
                <td>{{ $pemutihan->siswa->nis }}</td>
            </tr>
        </table>

        <h3>Detail Pemutihan</h3>
        <table>
            <tr>
                <th>Tanggal</th>
                <td>{{ \Carbon\Carbon::parse($pemutihan->tanggal)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <th>Poin Dikurangi</th>
                <td>{{ $pemutihan->poin_dikurangi }} Poin</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $pemutihan->keterangan ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="status-badge status-{{ $pemutihan->status }}">
                        {{ strtoupper($pemutihan->status) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Demikian berita acara ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-box">
                        <p>Diajukan Oleh,</p>
                        <div class="signature-line"></div>
                        <p>{{ $pemutihan->pengaju->name ?? 'Guru BK' }}</p>
                    </div>
                </td>
                <td>
                    <div class="signature-box">
                        <p>Disetujui/Diketahui Oleh,</p>
                        <div class="signature-line"></div>
                        <p>{{ $pemutihan->penyetuju->name ?? 'Waka Kesiswaan' }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
