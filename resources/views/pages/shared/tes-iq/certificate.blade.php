<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Tes IQ - {{ $result->user->name }}</title>
    <style>
        @page { margin: 0px; size: a4 landscape; }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            margin: 0; 
            padding: 0;
            background-color: #ffffff;
            color: #333333;
        }
        .certificate-container {
            width: 100%;
            height: 100%;
            min-height: 790px;
            border: 20px solid #4f46e5;
            box-sizing: border-box;
            background-image: url('data:image/svg+xml;utf8,<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="%23f3f4f6" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)" /></svg>');
            position: relative;
            margin: 0 auto;
        }
        .inner-border {
            border: 2px solid #c7d2fe;
            margin: 15px;
            height: 710px;
            padding: 30px;
            box-sizing: border-box;
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-top: 10px;
        }
        .header h1 {
            color: #4f46e5;
            font-size: 50px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 4px;
            font-weight: 800;
        }
        .header h3 {
            color: #6b7280;
            font-size: 16px;
            margin: 8px 0;
            font-weight: 400;
            letter-spacing: 2px;
        }
        .content {
            text-align: center;
            margin-top: 30px;
        }
        .content p {
            font-size: 18px;
            color: #4b5563;
        }
        .name {
            font-size: 42px;
            font-weight: bold;
            color: #1f2937;
            margin: 20px 0;
            text-decoration: underline;
            text-decoration-color: #e5e7eb;
            text-decoration-thickness: 3px;
        }
        .score-box {
            margin: 30px auto;
            width: 240px;
            padding: 20px;
            background-color: #eef2ff;
            border-radius: 12px;
            border: 2px solid #c7d2fe;
        }
        .score-box p { margin: 0; font-size: 15px; font-weight: bold; color: #4338ca; }
        .score-box h1 { margin: 10px 0 0 0; font-size: 60px; color: #4f46e5; line-height: 1;}
        .footer {
            position: absolute;
            bottom: 30px;
            left: 30px;
            right: 30px;
            width: calc(100% - 60px);
            margin-top: 50px;
        }
        .qr-code {
            float: left;
            text-align: left;
            width: 150px;
        }
        .date {
            float: left;
            text-align: center;
            width: 500px;
            padding-top: 30px;
            color: #6b7280;
            font-size: 14px;
            margin-left: 20px;
        }
        .signature {
            float: right;
            text-align: right;
            width: 250px;
            padding-top: 20px;
        }
        .signature-line {
            border-bottom: 2px solid #1f2937;
            width: 200px;
            margin-left: auto;
            margin-bottom: 5px;
        }
        .signature p { margin: 0; font-size: 14px; font-weight: bold; }
        .seal {
            position: absolute;
            top: 30px;
            right: 30px;
            width: 76px;
            height: 76px;
            background-color: #f59e0b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 10;
        }
        .seal-inner {
            border: 2px dashed rgba(255,255,255,0.8);
            width: 64px;
            height: 64px;
            border-radius: 50%;
            margin: 4px auto;
        }
        .seal-text {
            display: block;
            margin-top: 18px;
            line-height: 1.2;
        }
        .cert-id {
            position: absolute;
            top: 30px;
            left: 30px;
            color: #9ca3af;
            font-size: 12px;
            letter-spacing: 1px;
            font-family: monospace;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="inner-border">
            
            <div class="cert-id">NO: {{ $result->certificate_code }}</div>

            <div class="seal">
                <div class="seal-inner">
                    <span class="seal-text">VALID<br>A.S.S</span>
                </div>
            </div>

            <div class="header">
                <h1>Sertifikat Tes IQ</h1>
                <h3>INTERNASIONAL STANDAR INTELEGENSI</h3>
            </div>

            <div class="content">
                <p>Dengan bangga diberikan kepada:</p>
                <div class="name">{{ strtoupper($result->user->name) }}</div>
                <p>Telah menyelesaikan evaluasi penalaran kognitif Logika & Pola secara mandiri.</p>

                <div class="score-box">
                    <p>Skor Inteligensi Quotient</p>
                    <h1>{{ $result->iq_score }}</h1>
                </div>
                
                <p style="font-weight:bold; color: #4b5563; font-size: 18px;">
                    Klasifikasi Predikat: 
                    @if($result->iq_score >= 130) SANGAT SUPERIOR
                    @elseif($result->iq_score >= 120) SUPERIOR
                    @elseif($result->iq_score >= 110) DI ATAS RATA-RATA
                    @elseif($result->iq_score >= 90) RATA-RATA (NORMAL)
                    @else DI BAWAH RATA-RATA
                    @endif
                </p>
            </div>

            <div class="footer clearfix">
                <div class="qr-code">
                    <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" width="90" height="90" alt="QR Code">
                    <p style="font-size:10px; color:#6b7280; margin-top:5px; font-family:monospace;">Scan to Verify</p>
                </div>
                <div class="date">
                    Diterbitkan tanggal:<br>
                    <strong>{{ $result->created_at->format('d F Y') }}</strong>
                </div>
                <div class="signature">
                    <div class="signature-line"></div>
                    <p>SUPERVISOR PENGUJI</p>
                    <span style="font-size:12px; color:#6b7280;">Sistem Stella Terpadu</span>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
