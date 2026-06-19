<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 0; color: #111827; }
        .page { border: 12px solid #b91c1c; height: 94%; margin: 24px; padding: 42px; text-align:center; }
        .label { color:#b91c1c; text-transform:uppercase; letter-spacing:4px; font-weight:bold; font-size:13px; }
        h1 { font-size: 42px; margin: 18px 0 8px; }
        h2 { font-size: 30px; margin: 18px 0; color:#b91c1c; }
        .muted { color:#6b7280; font-size:14px; }
        .score { display:inline-block; margin-top:16px; border:2px solid #b91c1c; padding:10px 24px; border-radius:8px; font-weight:bold; font-size:20px; }
        .sign { margin-top:50px; width: 260px; margin-left:auto; text-align:center; font-size:12px; }
        .verify { margin-top:8px; color:#6b7280; font-size:10px; }
    </style>
</head>
<body>
    <div class="page">
        <div class="label">Sertifikat Penilaian Semester</div>
        <h1>Piagam Penghargaan</h1>
        <p class="muted">Diberikan kepada</p>
        <h2>{{ $target->nama_lengkap ?? $target->name }}</h2>
        <p>sebagai <strong>Peringkat {{ $rank }}</strong> {{ $targetKind === 'student' ? 'Siswa Terbaik' : 'Guru Terbaik' }}</p>
        <p class="muted">{{ $period->title }} - {{ $period->tahunPelajaran?->tahun }} Semester {{ $period->semester }}</p>
        <div class="score">Skor {{ $score }}/100</div>

        <div class="sign">
            <p>KAUR SDM</p>
            @if($signature)
                <p style="margin-top:45px;font-weight:bold;">{{ $signature->signer_name }}</p>
                <p class="verify">Dokumen digital: {{ $signature->token }}</p>
            @else
                <p style="margin-top:55px;font-weight:bold;">Belum ditandatangani digital</p>
            @endif
        </div>
    </div>
</body>
</html>
