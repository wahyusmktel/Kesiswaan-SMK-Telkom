<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daftar Pengguna Sistem</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.4;
        }

        .container {
            width: 100%;
            padding: 20px 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #dc2626;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 700;
            color: #dc2626;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header .subtitle {
            font-size: 10px;
            color: #6b7280;
        }

        .meta-info {
            margin-bottom: 20px;
            padding: 12px 15px;
            background-color: #fef2f2;
            border-radius: 6px;
            border-left: 4px solid #dc2626;
        }

        .meta-info table {
            width: 100%;
        }

        .meta-info td {
            padding: 3px 0;
            font-size: 10px;
        }

        .meta-info .label {
            font-weight: 600;
            color: #374151;
            width: 120px;
        }

        .meta-info .value {
            color: #6b7280;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table thead tr {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }

        .data-table th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #dc2626;
        }

        .data-table th.center {
            text-align: center;
        }

        .data-table td {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #fef2f2;
        }

        .data-table tbody tr:hover {
            background-color: #fee2e2;
        }

        .data-table .no-cell {
            text-align: center;
            font-weight: 600;
            width: 40px;
            color: #dc2626;
        }

        .data-table .name-cell {
            font-weight: 500;
            color: #111827;
        }

        .data-table .email-cell {
            color: #4b5563;
        }

        .data-table .role-cell {
            color: #991b1b;
        }

        .role-badge {
            display: inline-block;
            padding: 2px 8px;
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            font-size: 9px;
            color: #991b1b;
            margin: 1px 2px;
        }

        .date-cell {
            color: #6b7280;
            font-size: 9px;
        }

        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .footer-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }

        .summary-box {
            background-color: #f9fafb;
            padding: 10px 15px;
            border-radius: 6px;
            display: inline-block;
        }

        .summary-box .label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .summary-box .value {
            font-size: 20px;
            font-weight: 700;
            color: #dc2626;
        }

        .signature-area {
            text-align: center;
            padding: 10px;
        }

        .signature-area .date {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 40px;
        }

        .signature-area .name {
            font-weight: 600;
            font-size: 11px;
            border-top: 1px solid #374151;
            padding-top: 5px;
            display: inline-block;
            min-width: 150px;
        }

        .signature-area .title {
            font-size: 9px;
            color: #6b7280;
        }

        .page-number {
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            margin-top: 15px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Daftar Pengguna Sistem</h1>
            <p class="subtitle">SMK Telkom Lampung - Sistem Informasi Kesiswaan</p>
        </div>

        <div class="meta-info">
            <table>
                <tr>
                    <td class="label">Filter Role</td>
                    <td class="value">: {{ $role ?? 'Semua Role' }}</td>
                </tr>
                <tr>
                    <td class="label">Total Pengguna</td>
                    <td class="value">: {{ $users->count() }} orang</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Export</td>
                    <td class="value">: {{ now()->isoFormat('dddd, D MMMM Y') }} pukul {{ now()->format('H:i') }} WIB</td>
                </tr>
            </table>
        </div>

        @if($users->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="center" style="width: 40px;">No</th>
                        <th style="width: 25%;">Nama Lengkap</th>
                        <th style="width: 30%;">Email</th>
                        <th style="width: 25%;">Role (Peran)</th>
                        <th style="width: 15%;">Terdaftar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $index => $user)
                        <tr>
                            <td class="no-cell">{{ $index + 1 }}</td>
                            <td class="name-cell">{{ $user->name }}</td>
                            <td class="email-cell">{{ $user->email }}</td>
                            <td class="role-cell">
                                @forelse ($user->getRoleNames() as $roleName)
                                    <span class="role-badge">{{ $roleName }}</span>
                                @empty
                                    <span style="color: #9ca3af; font-style: italic;">Tidak ada peran</span>
                                @endforelse
                            </td>
                            <td class="date-cell">{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <p>Tidak ada data pengguna yang ditemukan.</p>
            </div>
        @endif

        <div class="footer">
            <div class="footer-left">
                <div class="summary-box">
                    <div class="label">Total Pengguna</div>
                    <div class="value">{{ $users->count() }}</div>
                </div>
            </div>
            <div class="footer-right">
                <div class="signature-area">
                    <div class="date">Bandar Lampung, {{ now()->isoFormat('D MMMM Y') }}</div>
                    <div class="name">Administrator</div>
                    <div class="title">Pengelola Sistem</div>
                </div>
            </div>
        </div>

        <div class="page-number">
            Dokumen ini digenarasi secara otomatis oleh sistem.
        </div>
    </div>
</body>

</html>
