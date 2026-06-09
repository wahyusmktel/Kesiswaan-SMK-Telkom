<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; line-height: 1.45; }
        .header { background: #111827; color: #ffffff; padding: 18px; border-radius: 10px; }
        .title { font-size: 21px; font-weight: 900; margin-bottom: 4px; }
        .subtitle { color: #d1d5db; }
        .summary { margin-top: 18px; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .label { width: 190px; font-weight: 800; color: #374151; background: #f9fafb; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 999px; font-size: 11px; font-weight: 900; background: #ecfdf5; color: #047857; }
        .badge.warn { background: #fffbeb; color: #b45309; }
        .badge.danger { background: #fef2f2; color: #b91c1c; }
        .box { margin-top: 14px; border: 1px solid #e5e7eb; background: #f9fafb; padding: 12px; border-radius: 8px; }
        .box h4 { margin: 0 0 6px; font-size: 12px; color: #374151; text-transform: uppercase; letter-spacing: .04em; }
        .sign { margin-top: 30px; width: 100%; }
        .sign td { border: none; text-align: center; width: 50%; }
        .qr { width: 75px; height: 75px; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">RESUME TES BUTA WARNA & KESEHATAN MATA</div>
        <div class="subtitle">Unit Kesehatan Sekolah - SMK Telkom Lampung</div>
    </div>

    <p>Resume ini menerangkan hasil pemeriksaan buta warna dan kesehatan mata dasar yang dilakukan oleh petugas UKS.</p>

    <div class="summary">
        <table>
            <tr><td class="label">Nama Peserta</td><td><strong>{{ $eyeExam->examinee_name }}</strong></td></tr>
            <tr><td class="label">Jenis / Identitas</td><td>{{ ucfirst($eyeExam->examinee_type) }} / {{ $eyeExam->examinee_identity }}</td></tr>
            <tr><td class="label">Waktu Pemeriksaan</td><td>{{ $eyeExam->examined_at->translatedFormat('l, d F Y H:i') }}</td></tr>
            <tr>
                <td class="label">Hasil Tes Buta Warna</td>
                <td>
                    <span class="badge {{ $eyeExam->color_blind_result === 'normal' ? '' : ($eyeExam->color_blind_result === 'inconclusive' ? 'warn' : 'danger') }}">
                        {{ $eyeExam->color_blind_label }}
                    </span>
                </td>
            </tr>
            <tr><td class="label">Ketajaman Mata Kanan</td><td>{{ $eyeExam->visual_acuity_right ?: '-' }}</td></tr>
            <tr><td class="label">Ketajaman Mata Kiri</td><td>{{ $eyeExam->visual_acuity_left ?: '-' }}</td></tr>
            <tr><td class="label">Kesimpulan UKS</td><td><strong>{{ $eyeExam->conclusion_label }}</strong></td></tr>
        </table>
    </div>

    <div class="box">
        <h4>Catatan Tes Buta Warna</h4>
        <div>{{ $eyeExam->color_blind_notes ?: '-' }}</div>
    </div>

    <div class="box">
        <h4>Temuan Kesehatan Mata</h4>
        <div>{{ $eyeExam->eye_health_findings ?: '-' }}</div>
    </div>

    <div class="box">
        <h4>Rekomendasi</h4>
        <div>{{ $eyeExam->recommendation ?: 'Tidak ada rekomendasi khusus.' }}</div>
    </div>

    <p class="muted">Catatan: Pemeriksaan UKS adalah pemeriksaan awal di lingkungan sekolah. Bila terdapat keluhan berlanjut atau indikasi gangguan penglihatan, pemeriksaan lanjutan oleh tenaga kesehatan/fasilitas kesehatan tetap dianjurkan.</p>

    <table class="sign">
        <tr>
            <td></td>
            <td>
                Lampung, {{ now()->translatedFormat('d F Y') }}<br>
                Petugas UKS<br><br>
                @if($qrBase64)
                    <img src="{{ $qrBase64 }}" class="qr"><br>
                    <small>TTD Digital Terverifikasi</small><br>
                @else
                    <br><br><br>
                @endif
                <strong>{{ $document?->signer_name ?? $eyeExam->handler?->name ?? 'Petugas UKS' }}</strong>
            </td>
        </tr>
    </table>
</body>
</html>
