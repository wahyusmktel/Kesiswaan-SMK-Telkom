<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
            width: 80mm; /* Standard thermal printer width */
            line-height: 1.4;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .divider { border-bottom: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .py-1 { padding: 3px 0; }
        .header-title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        @media print {
            body { width: 100%; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="text-center">
        <div class="header-title">{{ $order->kantin->name ?? 'KANTIN SMK TELKOM' }}</div>
        <div>SMK Telkom Lampung</div>
        <div class="divider"></div>
    </div>

    <table>
        <tr>
            <td width="30%">Order ID</td>
            <td width="5%">:</td>
            <td class="font-bold">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Pemesan</td>
            <td>:</td>
            <td>{{ strtoupper($order->student->name) }}</td>
        </tr>
        <tr>
            <td>Pembayaran</td>
            <td>:</td>
            <td class="font-bold text-left uppercase">{{ $order->payment_method }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table>
        @foreach($order->items as $item)
        <tr>
            <td colspan="3" class="font-bold">{{ $item->menu_name }}</td>
        </tr>
        <tr>
            <td width="15%">{{ $item->quantity }}x</td>
            <td width="40%">{{ number_format($item->price, 0, ',', '.') }}</td>
            <td width="45%" class="text-right font-bold">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td class="font-bold">TOTAL BAYAR</td>
            <td class="text-right font-bold" style="font-size: 14px;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($order->notes)
    <div class="divider"></div>
    <div>
        <span class="font-bold">Catatan:</span><br>
        {{ $order->notes }}
    </div>
    @endif

    <div class="divider"></div>
    <div class="text-center">
        <div>Terima Kasih Atas Pesanan Anda!</div>
        <div style="font-size: 10px; margin-top: 5px;">Powered by Kesiswaan Apps</div>
    </div>

</body>
</html>
