<!DOCTYPE html>
<html>
<head>
    <title>Lembar Konseling & Kontrak Perilaku Siswa</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; color: #2563eb; text-transform: uppercase; }
        .header p { margin: 3px 0 0; font-size: 9px; color: #666; }
        
        .section-title { font-weight: bold; background-color: #f1f5f9; padding: 5px 10px; margin-top: 12px; border-left: 4px solid #2563eb; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .info-table td { padding: 4px; vertical-align: top; }
        .info-table .label { width: 25%; font-weight: bold; color: #475569; }
        
        .history-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .history-table th, .history-table td { border: 1px solid #e2e8f0; padding: 6px; text-align: left; }
        .history-table th { background-color: #f8fafc; font-weight: bold; font-size: 10px; color: #64748b; }

        .form-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .form-table th, .form-table td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; }
        .form-table th { background-color: #f8fafc; font-weight: bold; width: 35%; color: #475569; }

        .badge-list { margin: 0; padding: 0; list-style: none; }
        .badge-item { display: inline-block; padding: 2px 8px; background: #e0f2fe; color: #0369a1; border-radius: 4px; margin-right: 5px; border: 1px solid #bae6fd; font-size: 9px; }

        .contract-box { border: 2px solid #2563eb; padding: 15px; margin-top: 15px; background-color: #fff; }
        .contract-title { text-align: center; font-weight: bold; font-size: 13px; text-decoration: underline; margin-bottom: 10px; }
        
        .consequence-text { margin-top: 10px; font-weight: bold; color: #dc2626; border-top: 1px dashed #dc2626; padding-top: 10px; }
        
        .signature-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .signature-table td { width: 33.33%; text-align: center; }
        .signature-space { height: 50px; }
        .signature-name { font-weight: bold; text-decoration: underline; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lembar Konseling & Kontrak Perilaku Siswa</h1>
        <p>SMK TELKOM LAMPUNG — Layanan Bimbingan & Konseling (BK)</p>
    </div>

    <div class="section-title">I. IDENTITAS & REKAM JEJAK</div>
    <table class="info-table">
        <tr>
            <td class="label">Nama Siswa</td>
            <td>: {{ $keterlambatan->siswa->nama_lengkap }}</td>
            <td class="label">Konselor (BK)</td>
            <td>: {{ $bk_teacher }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td>: {{ $keterlambatan->siswa->rombels->first()?->kelas->nama_kelas ?? '-' }}</td>
            <td class="label">Wali Kelas</td>
            <td>: {{ $wali_kelas }}</td>
        </tr>
    </table>

    <div style="margin-top: 10px; font-weight: bold; color: #475569;">Riwayat Terlambat:</div>
    <table class="history-table">
        <thead>
            <tr>
                <th style="width: 25%">Tanggal</th>
                <th style="width: 75%">Alasan Terlambat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($history as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->waktu_dicatat_security)->translatedFormat('d F Y') }}</td>
                <td>{{ $item->alasan_siswa }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">II. IDENTIFIKASI AKAR MASALAH (DEEP DIVE)</div>
    <table class="form-table">
        <tr>
            <th>Evaluasi Sebelumnya: Mengapa rencana aksi Wali Kelas belum berhasil?</th>
            <td>{{ $keterlambatan->bkCoaching->evaluasi_sebelumnya }}</td>
        </tr>
        <tr>
            <th>Faktor Penghambat: Kendala teknis atau masalah psikologis?</th>
            <td>{{ $keterlambatan->bkCoaching->faktor_penghambat }}</td>
        </tr>
        <tr>
            <th>Analisis Dampak: Kerugian terbesar yang dirasakan siswa?</th>
            <td>{{ $keterlambatan->bkCoaching->analisis_dampak }}</td>
        </tr>
    </table>

    <div class="section-title">III. INTERVENSI PERILAKU (THE SOLUTION BRIDGE)</div>
    <div style="margin: 10px 0; border: 1px solid #e2e8f0; padding: 10px; background: #fafafa;">
        <table style="width: 100%">
            <tr>
                <td><strong>Jam Bangun:</strong> {{ substr($keterlambatan->bkCoaching->jam_bangun, 0, 5) }}</td>
                <td><strong>Jam Berangkat:</strong> {{ substr($keterlambatan->bkCoaching->jam_berangkat, 0, 5) }}</td>
                <td><strong>Estimasi Perjalanan:</strong> {{ $keterlambatan->bkCoaching->durasi_perjalanan }} Menit</td>
            </tr>
        </table>
        <div style="margin-top: 10px;">
            <strong>Strategi Pendukung:</strong><br>
            <div style="margin-top: 5px;">
                @foreach($keterlambatan->bkCoaching->strategi_pendukung as $strat)
                    <span class="badge-item">
                        @switch($strat)
                            @case('alarm') Alarm Ganda (jarak 5 menit) @break
                            @case('prep') Menyiapkan seragam & tas H-1 malam @break
                            @case('hp') Membatasi HP maks jam {{ substr($keterlambatan->bkCoaching->hp_limit_time, 0, 5) }} @break
                            @case('help') Bantuan pihak ketiga untuk membangunkan @break
                        @endswitch
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <div class="contract-box">
        <div class="contract-title">KONTRAK PERILAKU FORMAL</div>
        <p>
            Saya yang bertanda tangan di bawah ini menyadari sepenuhnya bahwa saya telah melewati batas toleransi keterlambatan sekolah (3 kali). 
            Saya berjanji akan melaksanakan strategi pendukung di atas dengan sungguh-sungguh.
        </p>
        <p>
            Jika saya terlambat untuk yang <strong>ke-4 kalinya</strong>, maka saya bersedia menerima sanksi tegas sesuai poin pelanggaran sekolah, yaitu:
        </p>
        <div class="consequence-text">
            "{{ $keterlambatan->bkCoaching->sanksi_disepakati }}"
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
                Guru BK,<br>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $bk_teacher }}</div>
            </td>
            <td>
                Wali Kelas,<br>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $wali_kelas }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem BK SMK Telkom Lampung pada {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
