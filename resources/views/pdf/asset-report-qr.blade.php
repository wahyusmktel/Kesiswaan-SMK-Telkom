<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>QR Laporan Aset</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #0f172a; }
        .poster { position: relative; width: 210mm; height: 296.5mm; overflow: hidden; page-break-after: always; background: #fff; }
        .poster:last-child { page-break-after: auto; }
        .top { position: relative; height: 60mm; padding: 18mm 18mm 12mm; background: #b91c1c; color: white; }
        .top-copy { width: 130mm; }
        .brand-logo { position: absolute; top: 9mm; right: 18mm; width: 32mm; height: auto; }
        .eyebrow { font-size: 10pt; font-weight: bold; letter-spacing: 1.8px; text-transform: uppercase; color: #fecaca; }
        h1 { margin: 5mm 0 2mm; font-size: 28pt; line-height: 1.08; }
        .sub { margin: 0; font-size: 11pt; color: #fee2e2; }
        .content { padding: 12mm 18mm 10mm; text-align: center; }
        .location { margin: 0; font-size: 22pt; font-weight: bold; }
        .building { margin: 2mm 0 7mm; font-size: 11pt; color: #64748b; }
        .qr-box { display: inline-block; padding: 5mm; border: 1.5mm solid #0f172a; border-radius: 5mm; }
        .qr { display: block; width: 90mm; height: 90mm; }
        .scan { margin: 7mm 0 2mm; font-size: 18pt; font-weight: bold; color: #b91c1c; }
        .words { width: 155mm; margin: 0 auto; font-size: 11pt; line-height: 1.55; color: #475569; }
        .footer { position: absolute; bottom: 0; left: 0; right: 0; height: 18mm; padding: 6mm 18mm; background: #0f172a; color: #fff; font-size: 9pt; }
        .code { float: right; color: #cbd5e1; }
    </style>
</head>
<body>
@foreach($locations as $location)
    <section class="poster">
        <header class="top">
            <img class="brand-logo" src="{{ $brandLogo }}" alt="Logo SMK Telkom Lampung">
            <div class="top-copy">
                <div class="eyebrow">SMK Telkom Lampung · Peduli Fasilitas</div>
                <h1>Lihat kerusakan?<br>Jangan dibiarkan.</h1>
                <p class="sub">Satu laporan kecil bisa membuat sekolah lebih aman dan nyaman.</p>
            </div>
        </header>
        <main class="content">
            <h2 class="location">{{ $location->name }}</h2>
            <p class="building">{{ $location->building->name }}{{ $location->floor ? ' · '.$location->floor : '' }}</p>
            <div class="qr-box"><img class="qr" src="{{ $qrCodes[$location->id] }}" alt="QR Code"></div>
            <p class="scan">SCAN UNTUK LAPOR ASET</p>
            <p class="words">Laporkan fasilitas rusak, hilang, kotor, atau berisiko. Sertakan kondisi yang kamu lihat agar petugas dapat menindaklanjuti dengan tepat.</p>
        </main>
        <footer class="footer">Terima kasih sudah ikut menjaga fasilitas sekolah.<span class="code">Kode Lokasi: {{ $location->code }}</span></footer>
    </section>
@endforeach
</body>
</html>
