<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kehadiran Bulan {{ $month->translatedFormat('F Y') }}</title>
    @include('pdf.partials.fingerprint-report-styles')
</head>
<body>
    <div class="school-mark">{{ mb_strtoupper($schoolName) }} ({{ $npsn }})</div>
    <div class="report-title">
        <h1>Laporan Kehadiran Bulan {{ $month->translatedFormat('F Y') }}</h1>
        <div class="generated">Dikeluarkan dari Aplikasi SISFO pada tanggal: {{ $generatedAt->translatedFormat('l, d F Y H : i : s') }}</div>
    </div>

    @include('pdf.partials.fingerprint-school-identity')

    <h3 class="recap-title">Rekapitulasi Kehadiran</h3>
    <table class="report-table">
        <thead>
            <tr>
                <th class="no">No.</th>
                <th class="name">Nama</th>
                <th class="nip">NIP</th>
                <th class="nuptk">NUPTK</th>
                <th class="attendance-count">Jumlah Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summaryRows as $row)
                <tr>
                    <td class="no">{{ $loop->iteration }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['nip'] }}</td>
                    <td>{{ $row['nuptk'] }}</td>
                    <td class="attendance-count">{{ $row['attendance_count'] ?: '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($attachments->isNotEmpty())
        <div class="appendix">
            <table class="report-table">
                <thead>
                    <tr>
                        <th colspan="7" class="appendix-title-cell">Lampiran Daftar Kehadiran {{ mb_strtoupper($schoolName) }}</th>
                    </tr>
                    <tr>
                        <th class="no" rowspan="2">No.</th>
                        <th class="name" rowspan="2">Nama</th>
                        <th class="nip" rowspan="2">NIP</th>
                        <th class="nuptk" rowspan="2">NUPTK</th>
                        <th class="type" rowspan="2">Jenis GTK</th>
                        <th colspan="2">Kehadiran</th>
                    </tr>
                    <tr><th class="time">Masuk</th><th class="time">Pulang</th></tr>
                </thead>
                <tbody>
                    @foreach($attachments as $day)
                        <tr class="date-row"><td colspan="7">{{ $day['date']->locale('id')->translatedFormat('l, d F Y') }}</td></tr>
                        @foreach($day['rows'] as $row)
                        <tr>
                            <td class="no">{{ $loop->iteration }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['nip'] }}</td>
                            <td>{{ $row['nuptk'] }}</td>
                            <td>{{ $row['employee_type'] }}</td>
                            <td class="time">{{ $row['check_in'] ?: '-' }}</td>
                            <td class="time">{{ $row['check_out'] ?: '-' }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>
