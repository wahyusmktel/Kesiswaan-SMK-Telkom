<!DOCTYPE html>
<html>
<head>
    <title>Rekap Keterlambatan Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { bg-color: #f5f5f5; font-weight: bold; }
        .status { font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; }
        .filter-info { margin-bottom: 10px; font-style: italic; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rekap Keterlambatan Siswa</h2>
        <p>SMK Telkom Jakarta</p>
    </div>

    <div class="filter-info">
        Periode: {{ $filters['start_date'] ?? '-' }} s/d {{ $filters['end_date'] ?? '-' }} <br>
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Waktu Datang</th>
                <th>Alasan</th>
                <th>Status</th>
                <th>Pencatat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $late)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $late->siswa->user->name }}</strong><br>
                        NIS: {{ $late->siswa->nis }}
                    </td>
                    <td>{{ $late->siswa->rombels->first()?->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $late->waktu_dicatat_security->format('d/m/Y H:i') }}</td>
                    <td>{{ $late->alasan_siswa }}</td>
                    <td class="status">{{ str_replace('_', ' ', $late->status) }}</td>
                    <td>
                        Sec: {{ $late->security->name }}<br>
                        @if($late->guruPiket)
                            Pikt: {{ $late->guruPiket->name }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak secara otomatis oleh Sistem Kesiswaan</p>
    </div>
</body>
</html>
