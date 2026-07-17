<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Induk - {{ $student->nama_lengkap }}</title>
    <style>
        @page { margin: 16mm 14mm 16mm 18mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111827; font-family: DejaVu Sans, sans-serif; font-size: 9pt; line-height: 1.35; }
        h1, h2, h3, p { margin: 0; }
        .cover { height: 225mm; text-align: center; position: relative; padding-top: 36mm; }
        .cover-mark { width: 72px; height: 72px; margin: 0 auto 18px; border: 3px solid #b91c1c; color: #b91c1c; font-size: 24pt; font-weight: bold; line-height: 66px; }
        .cover h1 { font-size: 22pt; letter-spacing: 0; }
        .cover h2 { margin-top: 8px; font-size: 14pt; }
        .cover .student-box { width: 78%; margin: 38mm auto 0; border: 1.5px solid #111827; padding: 14px; }
        .cover .school { position: absolute; bottom: 16mm; width: 100%; font-size: 11pt; font-weight: bold; }
        .page-break { page-break-before: always; }
        .section { margin-bottom: 16px; page-break-inside: avoid; }
        .document-header { border-bottom: 2px solid #991b1b; padding-bottom: 8px; margin-bottom: 14px; }
        .document-header table { width: 100%; border: 0; }
        .document-header td { border: 0; padding: 0; vertical-align: middle; }
        .document-header .school-name { font-size: 13pt; font-weight: bold; }
        .document-header .meta { color: #4b5563; font-size: 8pt; }
        .section-title { margin-bottom: 7px; padding: 6px 9px; background: #991b1b; color: white; font-size: 10pt; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #6b7280; padding: 5px 6px; vertical-align: top; }
        th { background: #f3f4f6; font-weight: bold; text-align: left; }
        .label { width: 31%; background: #f9fafb; font-weight: bold; }
        .number { width: 28px; text-align: center; }
        .score { width: 70px; text-align: center; }
        .muted { color: #6b7280; }
        .signature { margin-top: 22px; page-break-inside: avoid; }
        .signature td { width: 50%; border: 0; text-align: center; padding: 0 12px; }
        .signature-space { height: 56px; }
        .attachment-page { page-break-before: always; text-align: center; }
        .attachment-page h2 { margin-bottom: 8px; font-size: 12pt; }
        .attachment-page p { margin-bottom: 12px; color: #6b7280; }
        .attachment-page img { max-width: 100%; max-height: 235mm; }
        .footer-note { margin-top: 8px; text-align: right; color: #6b7280; font-size: 7pt; }
    </style>
</head>
<body>
    @php
        $dapodik = $student->dapodik;
        $formatDate = fn ($date) => $date ? $date->copy()->locale('id')->translatedFormat('d F Y') : '-';
        $additional = $book?->additional_data ?? [];
    @endphp

    <div class="cover">
        <div class="cover-mark">BI</div>
        <h1>BUKU INDUK PESERTA DIDIK</h1>
        <h2>SEKOLAH MENENGAH KEJURUAN</h2>
        <div class="student-box">
            <div class="muted">Nama Peserta Didik</div>
            <div style="font-size: 16pt; font-weight: bold; margin: 6px 0 12px;">{{ mb_strtoupper($student->nama_lengkap) }}</div>
            <table>
                <tr><td class="label">Nomor Induk</td><td>{{ $student->nis }}</td></tr>
                <tr><td class="label">NISN</td><td>{{ $dapodik?->nisn ?: '-' }}</td></tr>
                <tr><td class="label">Kelas</td><td>{{ $rombel->kelas?->nama_kelas ?: '-' }}</td></tr>
            </table>
        </div>
        <div class="school">{{ mb_strtoupper($schoolName) }}<br><span style="font-size: 9pt; font-weight: normal;">NPSN {{ $npsn ?: '-' }}</span></div>
    </div>

    <div class="page-break">
        @include('pdf.partials.student-master-book-header')
        <div class="section">
            <div class="section-title">A. IDENTITAS PESERTA DIDIK</div>
            <table>
                @foreach([
                    'Nama lengkap' => $student->nama_lengkap,
                    'NIS / NIPD' => $student->nis.' / '.($dapodik?->nipd ?: '-'),
                    'NISN' => $dapodik?->nisn,
                    'NIK' => $dapodik?->nik,
                    'Tempat dan tanggal lahir' => ($student->tempat_lahir ?: $dapodik?->tempat_lahir ?: '-').', '.$formatDate($student->tanggal_lahir ?: $dapodik?->tanggal_lahir),
                    'Jenis kelamin' => $student->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                    'Agama' => $dapodik?->agama,
                    'Kewarganegaraan' => data_get($additional, 'citizenship'),
                    'Status dalam keluarga' => data_get($additional, 'child_status'),
                    'Anak ke / jumlah saudara' => ($dapodik?->anak_ke_berapa ?: '-').' / '.($dapodik?->jumlah_saudara_kandung ?: '-'),
                    'Nomor akta kelahiran' => $dapodik?->no_registrasi_akta_lahir,
                    'Nomor Kartu Keluarga' => $dapodik?->no_kk,
                    'Alamat lengkap' => $dapodik?->alamat ?: $student->alamat,
                    'Jenis tinggal' => $dapodik?->jenis_tinggal,
                    'Telepon / HP' => $dapodik?->hp ?: $dapodik?->telepon,
                    'Email' => $dapodik?->email,
                    'Bahasa sehari-hari' => data_get($additional, 'daily_language'),
                    'Hobi / cita-cita' => trim((data_get($additional, 'hobby') ?: '-').' / '.(data_get($additional, 'aspiration') ?: '-')),
                ] as $label => $value)
                    <tr><td class="label">{{ $label }}</td><td>{{ filled($value) ? $value : '-' }}</td></tr>
                @endforeach
            </table>
        </div>
    </div>

    <div class="page-break">
        @include('pdf.partials.student-master-book-header')
        <div class="section">
            <div class="section-title">B. DATA ORANG TUA DAN WALI</div>
            <table>
                <thead><tr><th>Data</th><th>Ayah</th><th>Ibu</th><th>Wali</th></tr></thead>
                <tbody>
                    @foreach([
                        'Nama' => [$dapodik?->nama_ayah, $dapodik?->nama_ibu, $dapodik?->nama_wali],
                        'NIK' => [$dapodik?->nik_ayah, $dapodik?->nik_ibu, $dapodik?->nik_wali],
                        'Tahun lahir' => [$dapodik?->tahun_lahir_ayah, $dapodik?->tahun_lahir_ibu, $dapodik?->tahun_lahir_wali],
                        'Pendidikan' => [$dapodik?->jenjang_pendidikan_ayah, $dapodik?->jenjang_pendidikan_ibu, $dapodik?->jenjang_pendidikan_wali],
                        'Pekerjaan' => [$dapodik?->pekerjaan_ayah, $dapodik?->pekerjaan_ibu, $dapodik?->pekerjaan_wali],
                        'Penghasilan' => [$dapodik?->penghasilan_ayah, $dapodik?->penghasilan_ibu, $dapodik?->penghasilan_wali],
                    ] as $label => $values)
                        <tr><td class="label">{{ $label }}</td>@foreach($values as $value)<td>{{ $value ?: '-' }}</td>@endforeach</tr>
                    @endforeach
                    <tr><td class="label">Nomor yang dapat dihubungi</td><td colspan="3">{{ data_get($additional, 'guardian_phone') ?: '-' }}</td></tr>
                </tbody>
            </table>
        </div>
        <div class="section">
            <div class="section-title">C. PENERIMAAN DI SEKOLAH</div>
            <table>
                @foreach([
                    'Tanggal diterima' => $formatDate($book?->admission_date),
                    'Status penerimaan' => $book?->admission_status,
                    'Diterima di tingkat' => data_get($additional, 'accepted_grade'),
                    'Program/konsentrasi keahlian' => data_get($additional, 'accepted_program'),
                    'Sekolah asal' => $book?->previous_school ?: $dapodik?->sekolah_asal,
                    'Nomor ijazah sebelumnya' => $book?->previous_diploma_number,
                    'Tanggal ijazah sebelumnya' => $formatDate($book?->previous_diploma_date),
                ] as $label => $value)
                    <tr><td class="label">{{ $label }}</td><td>{{ $value ?: '-' }}</td></tr>
                @endforeach
            </table>
        </div>
        <div class="section">
            <div class="section-title">D. KESEHATAN DAN PERKEMBANGAN FISIK</div>
            <table>
                <tr><td class="label">Golongan darah</td><td>{{ $book?->blood_type ?: '-' }}</td><td class="label">Kebutuhan khusus</td><td>{{ $book?->special_needs_notes ?: $dapodik?->kebutuhan_khusus ?: '-' }}</td></tr>
                <tr><td class="label">Tinggi / berat badan</td><td>{{ $dapodik?->tinggi_badan ?: '-' }} cm / {{ $dapodik?->berat_badan ?: '-' }} kg</td><td class="label">Lingkar kepala</td><td>{{ $dapodik?->lingkar_kepala ?: '-' }} cm</td></tr>
                <tr><td class="label">Riwayat penyakit</td><td colspan="3">{{ $book?->medical_history ?: '-' }}</td></tr>
                <tr><td class="label">Catatan kesehatan</td><td colspan="3">{{ data_get($additional, 'health_notes') ?: '-' }}</td></tr>
            </table>
        </div>
    </div>

    @forelse($book?->periods ?? [] as $period)
        <div class="page-break">
            @include('pdf.partials.student-master-book-header')
            <div class="section">
                <div class="section-title">E. PERKEMBANGAN AKADEMIK - {{ $period->school_year }} / {{ mb_strtoupper($period->semester) }}</div>
                <table>
                    <thead><tr><th class="number">No.</th><th>Mata Pelajaran</th><th class="score">Nilai</th></tr></thead>
                    <tbody>
                        @forelse($period->grades ?? [] as $grade)
                            <tr><td class="number">{{ $loop->iteration }}</td><td>{{ $grade['subject'] ?? '-' }}</td><td class="score">{{ isset($grade['score']) ? number_format((float) $grade['score'], 2) : '-' }}</td></tr>
                        @empty
                            <tr><td colspan="3" style="text-align:center;">Belum ada nilai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="section">
                <div class="section-title">F. EKSTRAKURIKULER DAN KEHADIRAN</div>
                <table>
                    <thead><tr><th class="number">No.</th><th>Kegiatan</th><th>Predikat</th><th>Keterangan</th></tr></thead>
                    <tbody>
                        @forelse($period->extracurriculars ?? [] as $activity)
                            <tr><td class="number">{{ $loop->iteration }}</td><td>{{ $activity['name'] ?? '-' }}</td><td>{{ $activity['predicate'] ?? '-' }}</td><td>{{ $activity['description'] ?? '-' }}</td></tr>
                        @empty
                            <tr><td colspan="4" style="text-align:center;">Belum ada kegiatan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <table style="margin-top:8px;">
                    <tr><th>Sakit</th><td>{{ $period->sick_days }} hari</td><th>Izin</th><td>{{ $period->permitted_days }} hari</td><th>Tanpa Keterangan</th><td>{{ $period->absent_days }} hari</td></tr>
                    <tr><th>Sikap/Perilaku</th><td colspan="5">{{ $period->conduct ?: '-' }}</td></tr>
                    <tr><th>Catatan Perkembangan</th><td colspan="5">{{ $period->development_notes ?: '-' }}</td></tr>
                </table>
            </div>
        </div>
    @empty
        <div class="page-break">
            @include('pdf.partials.student-master-book-header')
            <div class="section-title">E. PERKEMBANGAN AKADEMIK</div>
            <p style="padding:30px; text-align:center; border:1px solid #9ca3af;">Riwayat semester belum diisi.</p>
        </div>
    @endforelse

    <div class="page-break">
        @include('pdf.partials.student-master-book-header')
        <div class="section">
            <div class="section-title">G. MUTASI, KELULUSAN, DAN CATATAN</div>
            <table>
                <tr><td class="label">Status peserta didik</td><td>{{ ucfirst($book?->student_status ?: 'aktif') }}</td></tr>
                <tr><td class="label">Tanggal pindah/keluar</td><td>{{ $formatDate($book?->transfer_date) }}</td></tr>
                <tr><td class="label">Sekolah tujuan</td><td>{{ $book?->transfer_destination ?: '-' }}</td></tr>
                <tr><td class="label">Alasan pindah/keluar</td><td>{{ $book?->transfer_reason ?: '-' }}</td></tr>
                <tr><td class="label">Tanggal kelulusan</td><td>{{ $formatDate($book?->graduation_date ?: $student->graduated_at) }}</td></tr>
                <tr><td class="label">Nomor ijazah</td><td>{{ $book?->graduation_certificate_number ?: $student->transcriptDiplomaNumber?->diploma_number ?: '-' }}</td></tr>
                <tr><td class="label">Catatan perkembangan pendidikan</td><td>{{ data_get($additional, 'education_notes') ?: '-' }}</td></tr>
                <tr><td class="label">Catatan wali kelas</td><td>{{ $book?->homeroom_notes ?: '-' }}</td></tr>
            </table>
        </div>
        <div class="section">
            <div class="section-title">H. DAFTAR LAMPIRAN</div>
            <table>
                <thead><tr><th class="number">No.</th><th>Jenis</th><th>Judul Dokumen</th><th>Nama Berkas</th></tr></thead>
                <tbody>
                    @forelse($book?->attachments ?? [] as $attachment)
                        <tr>
                            <td class="number">{{ $loop->iteration }}</td>
                            <td>{{ str($attachment->category)->replace('_', ' ')->title() }}</td>
                            <td>{{ $attachment->title }}</td>
                            <td>{{ $attachment->original_name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;">Tidak ada lampiran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <table class="signature">
            <tr>
                <td>Wali Kelas,<div class="signature-space"></div><strong><u>{{ $rombel->waliKelas?->name ?? auth()->user()?->name }}</u></strong></td>
                <td>Mengetahui,<br>Kepala Sekolah<div class="signature-space"></div><strong><u>{{ $principalName ?: '................................' }}</u></strong><br>NIP. {{ $principalNip ?: '-' }}</td>
            </tr>
        </table>
        <p class="footer-note">Dicetak dari SISFO pada {{ now()->locale('id')->translatedFormat('d F Y H:i') }}</p>
    </div>

    @foreach($imageAttachments as $attachment)
        <div class="attachment-page">
            <h2>LAMPIRAN: {{ mb_strtoupper($attachment->title) }}</h2>
            <p>{{ $attachment->original_name }}</p>
            <img src="{{ $attachment->data_uri }}" alt="{{ $attachment->title }}">
        </div>
    @endforeach
</body>
</html>
