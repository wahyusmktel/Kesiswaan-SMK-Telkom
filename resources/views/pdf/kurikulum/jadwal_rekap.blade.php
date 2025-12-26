<!DOCTYPE html>
<html>
<head>
    <title>Master Jadwal Pelajaran</title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8px; /* Ukuran teks kecil agar muat banyak kolom */
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Memaksa lebar kolom merata */
        }
        th, td {
            border: 1px solid #999;
            padding: 4px 2px;
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
        }
        .bg-grey {
            background-color: #f9f9f9;
        }
        .hari-row {
            background-color: #e5e7eb;
            font-weight: bold;
            text-align: left;
            padding-left: 10px;
        }
        .jam-col {
            width: 30px;
        }
        .waktu-col {
            width: 70px;
        }
        .hari-label-col {
            width: 50px;
            font-weight: bold;
        }
        .activity-cell {
            background-color: #fffbeb;
            font-style: italic;
            color: #92400e;
        }
        .footer-note {
            margin-top: 20px;
            font-size: 9px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .lampiran {
            margin-top: 30px;
            page-break-before: auto;
        }
        .lampiran h2 {
            font-size: 14px;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
        .mapel-grid {
            width: 50%;
            float: left;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekapitulasi Jadwal Pelajaran</h1>
        <p>SMK Telkom Lampung - Tahun Pelajaran {{ date('Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="hari-label-col">HARI</th>
                <th class="jam-col">JAM</th>
                <th class="waktu-col">PUKUL</th>
                @foreach($rombels as $rombel)
                    <th>{{ $rombel->kelas->nama_kelas }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($days as $day)
                @php $firstInDay = true; @endphp
                @foreach($jamKeList as $jamKe)
                    @php 
                        $slot = $jamLookup["{$jamKe}-{$day}"] ?? null;
                    @endphp
                    @if($slot)
                        <tr>
                            @if($firstInDay)
                                <td rowspan="{{ count($jamKeList) }}" class="hari-label-col">
                                    {{ strtoupper($day) }}
                                </td>
                                @php $firstInDay = false; @endphp
                            @endif
                            <td class="jam-col">{{ $jamKe }}</td>
                            <td class="waktu-col">
                                {{ \Carbon\Carbon::parse($slot->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($slot->jam_selesai)->format('H:i') }}
                            </td>
                            @foreach($rombels as $rombel)
                                @php
                                    $data = $jadwalMatrix["{$day}-{$jamKe}-{$rombel->id}"] ?? null;
                                    $isActivity = false;
                                    if ($slot->tipe_kegiatan) {
                                        if (in_array($slot->tipe_kegiatan, ['istirahat', 'sholawat_pagi', 'ishoma'])) { $isActivity = true; }
                                        elseif ($slot->tipe_kegiatan == 'upacara' && $day == 'Senin') { $isActivity = true; }
                                        elseif ($slot->tipe_kegiatan == 'kegiatan_4r' && $day == 'Jumat') { $isActivity = true; }
                                    }
                                @endphp
                                @if($isActivity)
                                    <td class="activity-cell">
                                        {{ str_replace('_', ' ', strtoupper($slot->tipe_kegiatan)) }}
                                    </td>
                                @else
                                    <td>
                                        @if($data)
                                            <strong>{{ $data['kode'] }}</strong><br>
                                            <span style="font-size: 7px; color: #666;">{{ $data['guru'] }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endif
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="lampiran clearfix">
        <h2>Lampiran Kode Mata Pelajaran</h2>
        @php
            $chunks = $allMapels->chunk(ceil($allMapels->count() / 2));
        @endphp
        @foreach($chunks as $chunk)
            <div class="mapel-grid">
                <table style="width: 95%;">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Kode</th>
                            <th>Nama Mata Pelajaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chunk as $mapel)
                            <tr>
                                <td style="text-align: center; font-weight: bold;">{{ $mapel->kode_mapel }}</td>
                                <td style="text-align: left; padding-left: 5px;">{{ $mapel->nama_mapel }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    <div class="footer-note">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} | Sistem Informasi Izin Guru - SMK Telkom Lampung
    </div>
</body>
</html>
