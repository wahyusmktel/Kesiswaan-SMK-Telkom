<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .header { background: #991b1b; color: white; padding: 18px; border-radius: 8px; }
        .title { font-size: 22px; font-weight: 900; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .label { width: 185px; font-weight: 800; color: #374151; }
        .alert { margin-top: 18px; background: #fef2f2; border: 1px solid #fecaca; padding: 12px; border-radius: 8px; color: #7f1d1d; }
        .sign { margin-top: 32px; width: 100%; }
        .sign td { border: none; text-align: center; width: 50%; }
        .qr { width: 75px; height: 75px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">SURAT RUJUKAN UKS</div>
        <div>SMK Telkom Lampung</div>
    </div>
    <p>Mohon pemeriksaan dan penanganan lanjutan untuk siswa berikut:</p>
    <table>
        <tr><td class="label">Nama Siswa</td><td><strong>{{ $record->student->nama_lengkap }}</strong></td></tr>
        <tr><td class="label">NIS / Kelas</td><td>{{ $record->student->nis }} / {{ $record->student->rombels->first()?->kelas?->nama_kelas ?? '-' }}</td></tr>
        <tr><td class="label">Waktu Kunjungan</td><td>{{ $record->visited_at->translatedFormat('l, d F Y H:i') }}</td></tr>
        <tr><td class="label">Tujuan Rujukan</td><td>{{ $record->referral_facility_type }} {{ $record->referral_facility_name }}</td></tr>
        <tr><td class="label">Keluhan</td><td>{{ $record->complaint }}</td></tr>
        <tr><td class="label">Gejala</td><td>{{ implode(', ', $record->symptoms ?? []) ?: '-' }}</td></tr>
        <tr><td class="label">Diagnosis / Analisa UKS</td><td>{{ $record->diagnosis ?: '-' }}</td></tr>
        <tr><td class="label">Tanda Vital</td><td>Suhu {{ $record->temperature ?: '-' }} C, Tensi {{ $record->blood_pressure ?: '-' }}, Nadi {{ $record->pulse ?: '-' }}, O2 {{ $record->oxygen_saturation ?: '-' }}%</td></tr>
    </table>
    <div class="alert"><strong>Alasan Rujukan:</strong><br>{{ $record->referral_reason ?: 'Memerlukan pemeriksaan lanjutan.' }}</div>
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
