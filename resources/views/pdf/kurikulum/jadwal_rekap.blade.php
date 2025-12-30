<!DOCTYPE html>
<html>
<head>
    <title>Master Jadwal Pelajaran</title>
    <style>
        @page {
            margin: 0.8cm;
            size: a4 landscape;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 7px;
            color: #1f2937;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #111827;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 3px 0 0;
            font-size: 10px;
            color: #4b5563;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 0.5pt solid #d1d5db;
            padding: 3px 1px;
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 6.5px;
        }
        .hari-label-col {
            width: 45px;
            background-color: #4f46e5 !important;
            color: white !important;
            font-weight: 800;
        }
        .jam-col {
            width: 25px;
            background-color: #f9fafb;
            font-weight: 700;
        }
        .waktu-col {
            width: 60px;
            background-color: #f9fafb;
            font-family: 'Courier', monospace;
            font-weight: 600;
        }
        .activity-cell {
            background-color: #fffbeb;
            color: #92400e;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 8px;
        }
        .mapel-text {
            font-weight: 700;
            color: #111827;
            display: block;
            margin-bottom: 1px;
        }
        .guru-text {
            font-size: 6px;
            color: #6b7280;
            font-style: italic;
        }
        .footer-note {
            margin-top: 15px;
            font-size: 7px;
            color: #9ca3af;
            text-align: right;
            border-top: 0.5pt solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Master Jadwal Pelajaran</h1>
        <p>SMK TELKOM LAMPUNG • TAHUN PELAJARAN {{ date('Y') }}/{{ date('Y')+1 }}</p>
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
                        @php
                            $isActivity = false;
                            if ($slot->tipe_kegiatan) {
                                if (in_array($slot->tipe_kegiatan, ['istirahat', 'sholawat_pagi', 'ishoma'])) { $isActivity = true; }
                                elseif ($slot->tipe_kegiatan == 'upacara' && $day == 'Senin') { $isActivity = true; }
                                elseif ($slot->tipe_kegiatan == 'kegiatan_4r' && $day == 'Jumat') { $isActivity = true; }
                            }
                        @endphp
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
                            
                            @if($isActivity)
                                <td colspan="{{ count($rombels) }}" class="activity-cell">
                                    {{ str_replace('_', ' ', strtoupper($slot->tipe_kegiatan)) }}
                                </td>
                            @else
                                @foreach($rombels as $rombel)
                                    @php
                                        $data = $jadwalMatrix["{$day}-{$jamKe}-{$rombel->id}"] ?? null;
                                    @endphp
                                    <td>
                                        @if($data)
                                            <span class="mapel-text">{{ $data['kode'] }}</span>
                                            <span class="guru-text">{{ $data['guru'] }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                    @endif
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer-note">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} | Sistem Informasi Manajemen Sekolah - SMK Telkom Lampung
    </div>
</body>
</html>
