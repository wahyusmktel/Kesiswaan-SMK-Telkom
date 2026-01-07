<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Masal - Stella Access Card - {{ $rombel->kelas->nama_kelas ?? 'Semua Kelas' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
        }

        .print-header {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            border-radius: 12px;
            color: white;
        }

        @media print {
            .print-header {
                display: none;
            }
        }

        .print-header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .print-header p {
            font-size: 12px;
            opacity: 0.9;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5mm;
            max-width: 190mm;
            margin: 0 auto;
        }

        .card {
            width: 85.6mm;
            height: 54mm;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #020617 100%);
            border-radius: 8px;
            overflow: hidden;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        @media print {
            .card {
                border-radius: 0;
                border: 0.5px solid #ccc;
            }
        }

        .card-header {
            background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
            padding: 6px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .logo-icon {
            width: 18px;
            height: 18px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg {
            width: 10px;
            height: 10px;
            fill: white;
        }

        .card-title {
            color: white;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 0.8px;
        }

        .card-subtitle {
            color: rgba(199, 210, 254, 0.8);
            font-size: 5px;
        }

        .security-badge {
            width: 12px;
            height: 12px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .card-body {
            padding: 6px 10px;
            display: flex;
            gap: 8px;
        }

        .photo-box {
            width: 40px;
            height: 48px;
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            border-radius: 6px;
            border: 1px solid #4b5563;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .photo-box svg {
            width: 20px;
            height: 20px;
            fill: #6b7280;
        }

        .info-section {
            flex: 1;
            min-width: 0;
        }

        .student-name {
            color: white;
            font-size: 7px;
            font-weight: 700;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 2px;
        }

        .info-label {
            color: #9ca3af;
            font-size: 5px;
            width: 22px;
            flex-shrink: 0;
        }

        .info-value {
            color: white;
            font-size: 6px;
            font-family: 'Consolas', 'Courier New', monospace;
        }

        .barcode-section {
            margin: 4px 10px 6px;
            background: white;
            border-radius: 4px;
            padding: 4px;
            text-align: center;
        }

        .barcode-section img {
            height: 16px;
            width: auto;
        }

        .barcode-number {
            font-size: 5px;
            color: #374151;
            font-family: 'Consolas', 'Courier New', monospace;
            margin-top: 1px;
        }

        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 24px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4);
            z-index: 100;
        }

        .print-btn:hover {
            transform: translateY(-2px);
        }

        @media print {
            .print-btn {
                display: none;
            }
        }

        .page-break {
            page-break-after: always;
            break-after: always;
        }

        .cut-guide {
            border: 1px dashed #ccc;
            margin: 2mm 0;
        }

        @media screen {
            .cut-guide {
                display: none;
            }
        }

        .info-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 12px;
            color: #64748b;
        }

        @media print {
            .info-bar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-header">
        <h1>🎫 Stella Access Card - Cetak Masal</h1>
        <p>Kelas: {{ $rombel->kelas->nama_kelas ?? 'N/A' }} | Total: {{ $siswaList->count() }} kartu</p>
    </div>

    <div class="info-bar">
        <span>📋 {{ $siswaList->count() }} kartu akan dicetak</span>
        <span>📄 {{ ceil($siswaList->count() / 8) }} halaman A4</span>
    </div>

    <div class="cards-grid">
        @foreach($siswaList as $index => $siswa)
            <div class="card">
                <div class="card-header">
                    <div class="logo-section">
                        <div class="logo-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="card-title">STELLA ACCESS CARD</div>
                            <div class="card-subtitle">SMK Telkom Lampung</div>
                        </div>
                    </div>
                    <div class="security-badge"></div>
                </div>

                <div class="card-body">
                    <div class="photo-box">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 4C14.21 4 16 5.79 16 8C16 10.21 14.21 12 12 12C9.79 12 8 10.21 8 8C8 5.79 9.79 4 12 4M12 14C16.42 14 20 15.79 20 18V20H4V18C4 15.79 7.58 14 12 14Z"/>
                        </svg>
                    </div>
                    <div class="info-section">
                        <div class="student-name">{{ strtoupper($siswa->nama_lengkap) }}</div>
                        <div class="info-row">
                            <span class="info-label">NIPD</span>
                            <span class="info-value">{{ $siswa->nis }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">KELAS</span>
                            <span class="info-value">{{ $siswa->rombels->first()?->kelas?->nama_kelas ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="barcode-section">
                    <img src="data:image/png;base64,{{ $barcodes[$siswa->id] }}" alt="Barcode">
                    <div class="barcode-number">{{ $siswa->nis }}</div>
                </div>
            </div>

            {{-- Page break after every 8 cards (2 columns x 4 rows) --}}
            @if(($index + 1) % 8 == 0 && $index + 1 < $siswaList->count())
                </div>
                <div class="page-break"></div>
                <div class="cards-grid">
            @endif
        @endforeach
    </div>

    <button class="print-btn" onclick="window.print()">🖨️ Cetak Semua Kartu</button>
</body>
</html>
