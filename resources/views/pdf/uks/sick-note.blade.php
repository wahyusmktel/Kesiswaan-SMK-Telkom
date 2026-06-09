<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .header { text-align: center; border-bottom: 3px solid #dc2626; padding-bottom: 12px; }
        .title { font-size: 20px; font-weight: 900; margin: 10px 0 4px; }
        .subtitle { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .label { width: 185px; font-weight: 800; color: #374151; }
        .box { margin-top: 18px; border: 1px solid #e5e7eb; background: #f9fafb; padding: 12px; border-radius: 8px; }
        .sign { margin-top: 36px; width: 100%; }
        .sign td { border: none; text-align: center; width: 50%; }
        .qr { width: 75px; height: 75px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">SURAT KETERANGAN SAKIT UKS</div>
        <div class="subtitle">SMK Telkom Lampung</div>
    </div>
    <p>Yang bertanda tangan di bawah ini menerangkan bahwa siswa berikut telah mendapatkan pemeriksaan/penanganan di UKS:</p>
    <table>
        <tr><td class="label">Nama Siswa</td><td><strong>{{ $record->student->nama_lengkap }}</strong></td></tr>
        <tr><td class="label">NIS / Kelas</td><td>{{ $record->student->nis }} / {{ $record->student->rombels->first()?->kelas?->nama_kelas ?? '-' }}</td></tr>
        <tr><td class="label">Waktu Kunjungan</td><td>{{ $record->visited_at->translatedFormat('l, d F Y H:i') }}</td></tr>
        <tr><td class="label">Keluhan</td><td>{{ $record->complaint }}</td></tr>
        <tr><td class="label">Diagnosis / Analisa UKS</td><td>{{ $record->diagnosis ?: '-' }}</td></tr>
        <tr><td class="label">Tindakan</td><td>{{ $record->treatment ?: '-' }}</td></tr>
        <tr><td class="label">Tindak Lanjut</td><td>{{ $record->disposition_label }}{{ $record->rest_until ? ' sampai ' . $record->rest_until->format('H:i') : '' }}</td></tr>
    </table>
    <div class="box">Surat ini dikeluarkan oleh UKS sebagai keterangan kondisi siswa pada saat pemeriksaan awal di sekolah.</div>
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
                <strong>{{ $document?->signer_name ?? $record->handler?->name ?? 'Petugas UKS' }}</strong>
            </td>
        </tr>
    </table>
</body>
</html>
