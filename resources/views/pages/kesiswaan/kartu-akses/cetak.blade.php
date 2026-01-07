<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stella Access Card - {{ $siswa->nama_lengkap }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: 85.6mm 54mm;
            margin: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
        }

        .card {
            width: 85.6mm;
            height: 54mm;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #020617 100%);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
        }

        @media print {
            .card {
                box-shadow: none;
                border-radius: 0;
            }
        }

        .card-header {
            background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-icon {
            width: 24px;
            height: 24px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg {
            width: 14px;
            height: 14px;
            fill: white;
        }

        .card-title {
            color: white;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .card-subtitle {
            color: rgba(199, 210, 254, 0.8);
            font-size: 6px;
        }

        .security-badge {
            width: 16px;
            height: 16px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .card-body {
            padding: 10px 14px;
            display: flex;
            gap: 12px;
        }

        .photo-box {
            width: 55px;
            height: 65px;
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            border-radius: 8px;
            border: 1px solid #4b5563;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .photo-box svg {
            width: 28px;
            height: 28px;
            fill: #6b7280;
        }

        .info-section {
            flex: 1;
            min-width: 0;
        }

        .student-name {
            color: white;
            font-size: 9px;
            font-weight: 700;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 3px;
        }

        .info-label {
            color: #9ca3af;
            font-size: 6px;
            width: 28px;
            flex-shrink: 0;
        }

        .info-value {
            color: white;
            font-size: 7px;
            font-family: 'Consolas', 'Courier New', monospace;
        }

        .barcode-section {
            margin: 4px 14px 8px;
            background: white;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
        }

        .barcode-section img {
            height: 32px;
            width: auto;
            max-width: 100%;
        }

        .barcode-number {
            font-size: 8px;
            color: #374151;
            font-family: 'Consolas', 'Courier New', monospace;
            margin-top: 3px;
            letter-spacing: 1px;
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
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
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
            <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode">
            <div class="barcode-number">{{ $siswa->nis }}</div>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">🖨️ Cetak Kartu</button>
</body>
</html>
