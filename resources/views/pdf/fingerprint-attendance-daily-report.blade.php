<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kehadiran Harian</title>
    @include('pdf.partials.fingerprint-report-styles')
</head>
<body>
    <div class="school-mark">{{ mb_strtoupper($schoolName) }} ({{ $npsn }})</div>
    <div class="report-title">
        <h1>Laporan Kehadiran Harian</h1>
        <h2>{{ $date->translatedFormat('l, d F Y') }}</h2>
        <div class="generated">Dikeluarkan dari Aplikasi SISFO pada tanggal: {{ $generatedAt->translatedFormat('l, d F Y H : i : s') }}</div>
    </div>

    @include('pdf.partials.fingerprint-school-identity')

    <table class="report-table">
        <thead>
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
            @foreach($rows as $row)
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
        </tbody>
    </table>
</body>
</html>
