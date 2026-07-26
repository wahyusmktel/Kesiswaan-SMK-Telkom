<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pakta Integritas Persetujuan Siswa Baru</title>
    <style>
        @page { margin: 28mm 20mm 22mm; }
        * { box-sizing: border-box; }
        body {
            color: #172033;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 3px solid #b91c1c;
            margin-bottom: 20px;
            padding-bottom: 12px;
            text-align: center;
        }
        .school { font-size: 15px; font-weight: 700; text-transform: uppercase; }
        .address { color: #596275; font-size: 8px; margin-top: 3px; }
        h1 { font-size: 16px; margin: 0 0 4px; text-align: center; text-transform: uppercase; }
        .document-number { color: #596275; font-size: 8px; text-align: center; }
        .intro { margin: 18px 0 12px; text-align: justify; }
        .identity {
            background: #f5f6f8;
            border: 1px solid #d9dde5;
            margin: 14px 0;
            padding: 10px 12px;
        }
        .identity table { border-collapse: collapse; width: 100%; }
        .identity td { padding: 2px 0; vertical-align: top; }
        .identity .label { color: #596275; width: 145px; }
        .statements { margin: 12px 0 18px; }
        .statement { margin-bottom: 8px; text-align: justify; }
        .check {
            border: 1px solid #172033;
            display: inline-block;
            font-size: 8px;
            font-weight: 700;
            height: 13px;
            line-height: 11px;
            margin-right: 7px;
            text-align: center;
            vertical-align: top;
            width: 13px;
        }
        .statement-text { display: inline-block; width: 92%; }
        h2 {
            border-left: 3px solid #b91c1c;
            font-size: 11px;
            margin: 18px 0 8px;
            padding-left: 7px;
            text-transform: uppercase;
        }
        .student-table { border-collapse: collapse; width: 100%; }
        .student-table thead { display: table-header-group; }
        .student-table tr { page-break-inside: avoid; }
        .student-table th {
            background: #e8eaee;
            border: 1px solid #aeb5c1;
            font-size: 8px;
            padding: 6px 5px;
            text-align: left;
            text-transform: uppercase;
        }
        .student-table td {
            border: 1px solid #c9ced7;
            font-size: 8px;
            padding: 5px;
            vertical-align: top;
        }
        .student-table .number { text-align: center; width: 28px; }
        .student-table .registration { width: 100px; }
        .student-table .nisn { width: 75px; }
        .closing { margin-top: 16px; text-align: justify; }
        .signature-table { margin-top: 20px; width: 100%; }
        .signature-table td { vertical-align: top; width: 50%; }
        .signature-box { margin-left: auto; width: 230px; }
        .signature-space { height: 54px; }
        .approver { font-weight: 700; text-decoration: underline; }
        .audit {
            border: 1px solid #d9dde5;
            color: #596275;
            font-size: 7px;
            margin-top: 20px;
            padding: 7px 9px;
        }
        .footer {
            bottom: -12mm;
            color: #7b8494;
            font-size: 7px;
            left: 0;
            position: fixed;
            right: 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="footer">
        Dokumen dibuat otomatis oleh {{ $settings?->school_name ?? config('app.name', 'Sistem Informasi Sekolah') }}
    </div>

    <div class="header">
        <div class="school">{{ $settings?->school_name ?? config('app.name', 'Sekolah') }}</div>
        @if ($settings?->address)
            <div class="address">{{ $settings->address }}</div>
        @endif
    </div>

    <h1>Pakta Integritas Persetujuan Siswa Baru</h1>
    <div class="document-number">Nomor dokumen: PISB/{{ $pact->signed_at->format('Ymd') }}/{{ strtoupper(substr($pact->uuid, 0, 8)) }}</div>

    <p class="intro">
        Saya yang bertanda tangan secara elektronik melalui akun aplikasi di bawah ini menyatakan telah melakukan
        verifikasi terhadap {{ $pact->approved_count }} data calon siswa sebelum menyetujui pembentukan data siswa
        sementara pada sistem.
    </p>

    <div class="identity">
        <table>
            <tr>
                <td class="label">Nama petugas</td>
                <td>: <strong>{{ $pact->approver_name ?: ($pact->approver?->name ?? '-') }}</strong></td>
            </tr>
            <tr>
                <td class="label">Email akun</td>
                <td>: {{ $pact->approver_email ?: ($pact->approver?->email ?? '-') }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal persetujuan</td>
                <td>: {{ $pact->signed_at->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="label">Jumlah data disetujui</td>
                <td>: {{ $pact->approved_count }} calon siswa</td>
            </tr>
        </table>
    </div>

    <h2>Pernyataan Integritas</h2>
    <div class="statements">
        @foreach ($pact->statements as $statement)
            <div class="statement">
                <span class="check">X</span><span class="statement-text">{{ $statement }}</span>
            </div>
        @endforeach
    </div>

    <h2>Daftar Data yang Disetujui</h2>
    <table class="student-table">
        <thead>
            <tr>
                <th class="number">No.</th>
                <th class="registration">Nomor Registrasi</th>
                <th>Nama Lengkap</th>
                <th class="nisn">NISN</th>
                <th>Sekolah Asal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pact->student_snapshots as $student)
                <tr>
                    <td class="number">{{ $loop->iteration }}</td>
                    <td>{{ $student['registration_number'] ?? '-' }}</td>
                    <td><strong>{{ $student['nama_lengkap'] ?? '-' }}</strong></td>
                    <td>{{ $student['nisn'] ?? '-' }}</td>
                    <td>{{ $student['sekolah_asal'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="closing">
        Pakta integritas ini dibuat dengan sebenar-benarnya sebagai bukti pertanggungjawaban proses persetujuan
        massal. Setiap perubahan lanjutan tetap wajib mengikuti prosedur pemetaan dan sinkronisasi data resmi Dapodik.
    </p>

    <table class="signature-table">
        <tr>
            <td></td>
            <td>
                <div class="signature-box">
                    {{ $pact->signed_at->translatedFormat('d F Y') }}<br>
                    Petugas yang menyetujui,
                    <div class="signature-space"></div>
                    <span class="approver">{{ $pact->approver_name ?: ($pact->approver?->name ?? '-') }}</span><br>
                    Ditandatangani melalui akun terautentikasi
                </div>
            </td>
        </tr>
    </table>

    <div class="audit">
        ID audit: {{ $pact->uuid }}<br>
        Dokumen ini bersumber dari rekam persetujuan pada aplikasi. Kode audit dan isi daftar siswa disimpan
        bersama waktu serta identitas akun petugas.
    </div>
</body>
</html>
