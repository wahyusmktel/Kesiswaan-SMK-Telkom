<!DOCTYPE html>
<html>
<head>
    <title>Lembar Coaching Kedisiplinan Siswa</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ed1c24; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #ed1c24; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 10px; color: #666; }
        
        .section-title { font-weight: bold; background-color: #f4f4f4; padding: 5px 10px; margin-top: 15px; border-left: 4px solid #ed1c24; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .info-table td { padding: 5px; vertical-align: top; }
        .info-table .label { width: 30%; font-weight: bold; color: #555; }
        
        .grow-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .grow-table th, .grow-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .grow-table th { background-color: #f9f9f9; font-weight: bold; font-size: 11px; text-transform: uppercase; color: #444; }
        .grow-table .tahap { width: 15%; font-weight: bold; vertical-align: middle; }
        .grow-table .pertanyaan { width: 35%; font-style: italic; font-size: 11px; color: #666; }
        .grow-table .respon { width: 50%; }
        
        .commitment-box { border: 1px solid #ddd; padding: 15px; margin-top: 10px; background-color: #fff; }
        .commitment-text { white-space: pre-wrap; margin-bottom: 15px; }
        
        .consequence-box { margin-top: 10px; padding: 10px; border: 1px dashed #ed1c24; background-color: #fff9f9; }
        
        .signature-table { width: 100%; margin-top: 40px; border-collapse: collapse; }
        .signature-table td { width: 50%; text-align: center; }
        .signature-space { height: 60px; }
        .signature-name { font-weight: bold; text-decoration: underline; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lembar Coaching Kedisiplinan Siswa</h1>
        <p>SMK TELKOM LAMPUNG — Sistem Management Kedisiplinan & Perizinan</p>
    </div>

    <div class="section-title">I. DATA UMUM</div>
    <table class="info-table">
        <tr>
            <td class="label">Nama Siswa</td>
            <td>: {{ $keterlambatan->siswa->nama_lengkap }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td>: {{ $keterlambatan->siswa->rombels->first()?->kelas->nama_kelas ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Coaching</td>
            <td>: {{ \Carbon\Carbon::parse($keterlambatan->coaching->tanggal_coaching)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Lokasi Coaching</td>
            <td>: {{ ucfirst($keterlambatan->coaching->lokasi) }}</td>
        </tr>
        <tr>
            <td class="label">Nama Wali Kelas</td>
            <td>: {{ $wali_kelas }}</td>
        </tr>
        <tr>
            <td class="label">Frekuensi Terlambat</td>
            <td>: {{ $frekuensiBulanIni }} Kali (Bulan Ini)</td>
        </tr>
    </table>

    <div class="section-title">II. TAHAP COACHING (MODEL GROW)</div>
    <table class="grow-table">
        <thead>
            <tr>
                <th>Tahap</th>
                <th>Pertanyaan Panduan</th>
                <th>Respon / Catatan Siswa</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tahap">Goal (Tujuan)</td>
                <td class="pertanyaan">Apa tujuanmu datang tepat waktu ke sekolah? Apa dampaknya bagimu jika masuk tepat waktu?</td>
                <td class="respon">{{ $keterlambatan->coaching->goal_response }}</td>
            </tr>
            <tr>
                <td class="tahap">Reality (Kenyataan)</td>
                <td class="pertanyaan">Ceritakan apa yang biasanya terjadi di pagi hari sehingga kamu terlambat? Apa kendala utamanya?</td>
                <td class="respon">{{ $keterlambatan->coaching->reality_response }}</td>
            </tr>
            <tr>
                <td class="tahap">Options (Pilihan)</td>
                <td class="pertanyaan">Apa saja hal yang bisa kamu ubah agar tidak terlambat lagi? (Misal: tidur lebih awal, dsb)</td>
                <td class="respon">{{ $keterlambatan->coaching->options_response }}</td>
            </tr>
            <tr>
                <td class="tahap">Will (Tindakan)</td>
                <td class="pertanyaan">Dari pilihan tadi, mana yang akan kamu lakukan mulai besok? Siapa yang bisa membantumu?</td>
                <td class="respon">{{ $keterlambatan->coaching->will_response }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">III. RENCANA AKSI & KOMITMEN</div>
    <div class="commitment-box">
        <p>Saya, <strong>{{ $keterlambatan->siswa->nama_lengkap }}</strong>, berkomitmen untuk melakukan perubahan sebagai berikut:</p>
        <div class="commitment-text">{{ $keterlambatan->coaching->rencana_aksi }}</div>
        
        <div class="consequence-box">
            <strong>Konsekuensi Logis:</strong><br>
            "Jika saya terlambat lagi, saya bersedia untuk: {{ $keterlambatan->coaching->konsekuensi_logis }}"
        </div>
    </div>

    <table class="signature-table">
        <tr>
            <td>
                Siswa,<br>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $keterlambatan->siswa->nama_lengkap }}</div>
            </td>
            <td>
                Wali Kelas,<br>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $wali_kelas }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Aplikasi Izin SMK Telkom Lampung pada {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
