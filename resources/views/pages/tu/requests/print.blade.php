<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            margin: 2cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.5;
            font-size: 12pt;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
        }

        .header h1 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 10pt;
        }

        .letter-info {
            margin-top: 1cm;
            margin-bottom: 1cm;
        }

        .letter-number {
            font-weight: bold;
        }

        .content {
            text-align: justify;
            min-height: 10cm;
        }

        .signature {
            margin-top: 2cm;
            float: right;
            width: 6cm;
            text-align: center;
        }

        .signature-name {
            margin-top: 2cm;
            font-weight: bold;
            text-decoration: underline;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>SMK TELKOM LAMPUNG</h1>
        <p>Jl. Raya Tegineneng, Pesawaran, Lampung</p>
        <p>Telp: (0721) 123456 | Email: info@smktelkom-lpg.sch.id</p>
    </div>

    <div class="letter-info">
        <table width="100%">
            <tr>
                <td width="15%">Nomor</td>
                <td width="5%">:</td>
                <td class="letter-number">{{ $letterRequest->outgoingLetter->full_number ?? 'MODUL.BELUM.TERBIT' }}</td>
                <td align="right">
                    {{ \Carbon\Carbon::parse($letterRequest->outgoingLetter->date ?? now())->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>-</td>
                <td></td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>:</td>
                <td style="font-weight: bold;">{{ $letterRequest->subject }}</td>
                <td></td>
            </tr>
        </table>
    </div>

    <div class="content">
        {!! nl2br(e($letterRequest->content)) !!}
    </div>

    <div class="signature">
        <p>Pesawaran,
            {{ \Carbon\Carbon::parse($letterRequest->outgoingLetter->date ?? now())->translatedFormat('d F Y') }}</p>
        <p>Mengetahui,</p>
        <div class="signature-name">
            (................................)
        </div>
        <p>Petugas Administrasi</p>
    </div>
    <div class="clear"></div>
</body>

</html>