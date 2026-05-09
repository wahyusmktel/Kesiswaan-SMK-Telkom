<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran — SMK Telkom</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #05091a;
            --surface:   #0d1426;
            --border:    rgba(255,255,255,0.07);
            --indigo:    #6366f1;
            --indigo-2:  #818cf8;
            --cyan:      #22d3ee;
            --emerald:   #10b981;
            --amber:     #f59e0b;
            --rose:      #f43f5e;
            --text:      #f1f5f9;
            --muted:     #64748b;
            --header-h:  88px;
            --ticker-h:  48px;
        }

        html, body {
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow: hidden;
        }

        /* ══════════════════════════════════════
           BACKGROUND LAYERS
        ══════════════════════════════════════ */
        .bg-grid {
            position: fixed; inset: 0; z-index: 0;
            background-image:
                linear-gradient(rgba(99,102,241,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .bg-orb {
            position: fixed; border-radius: 50%; filter: blur(120px);
            opacity: 0.12; pointer-events: none; z-index: 0;
            animation: float 16s ease-in-out infinite alternate;
        }
        .orb-a { width:800px;height:800px;background:var(--indigo);top:-300px;left:-200px;animation-duration:18s; }
        .orb-b { width:600px;height:600px;background:var(--cyan);bottom:-200px;right:-100px;animation-duration:14s;animation-delay:-5s; }
        @keyframes float { to { transform: translate(40px,30px) scale(1.06); } }

        /* ══════════════════════════════════════
           HEADER
        ══════════════════════════════════════ */
        .tv-header {
            position: fixed; top: 0; left: 0; right: 0;
            height: var(--header-h);
            background: rgba(5,9,26,0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 2.5rem;
            z-index: 100;
            gap: 1.5rem;
        }
        .header-logo {
            display: flex; align-items: center; gap: 14px; flex-shrink: 0;
        }
        .logo-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, var(--indigo), #4f46e5);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 24px rgba(99,102,241,0.4);
        }
        .logo-icon svg { width: 28px; height: 28px; color: white; }
        .logo-text h1 {
            font-size: 1.3rem; font-weight: 900;
            background: linear-gradient(to right, #f1f5f9, var(--indigo-2));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            line-height: 1.1; letter-spacing: -0.02em;
        }
        .logo-text p { font-size: 0.72rem; color: var(--muted); font-weight: 600; margin-top: 2px; }

        .header-divider {
            width: 1px; height: 40px; background: var(--border); flex-shrink: 0;
        }

        .header-info {
            display: flex; align-items: center; gap: 1.5rem; flex: 1;
        }
        .header-badge {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(99,102,241,0.15);
            border: 1px solid rgba(99,102,241,0.3);
            border-radius: 999px; padding: 5px 14px;
            font-size: 0.75rem; font-weight: 700; color: var(--indigo-2);
        }
        .live-dot {
            width: 7px; height: 7px; border-radius: 50%; background: var(--emerald);
            animation: pulse-g 1.8s ease-in-out infinite;
        }
        @keyframes pulse-g {
            0%,100% { box-shadow: 0 0 0 0 rgba(16,185,129,.7); }
            50%      { box-shadow: 0 0 0 5px rgba(16,185,129,0); }
        }

        .header-day-pill {
            background: rgba(34,211,238,0.12);
            border: 1px solid rgba(34,211,238,0.25);
            border-radius: 999px; padding: 5px 16px;
            font-size: 0.8rem; font-weight: 800; color: var(--cyan);
        }

        .header-right {
            margin-left: auto; display: flex; align-items: center; gap: 1.5rem;
        }
        .clock-block { text-align: right; }
        .clock-time {
            font-size: 2rem; font-weight: 900; letter-spacing: -0.04em;
            font-variant-numeric: tabular-nums;
            background: linear-gradient(to right, var(--text), var(--indigo-2));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            line-height: 1;
        }
        .clock-date {
            font-size: 0.7rem; font-weight: 600; color: var(--muted);
            margin-top: 2px; text-align: right;
        }

        /* ══════════════════════════════════════
           TICKER (bawah header)
        ══════════════════════════════════════ */
        .ticker-bar {
            position: fixed; top: var(--header-h); left: 0; right: 0;
            height: var(--ticker-h);
            background: linear-gradient(90deg, #1e1b4b, #0f172a, #1e1b4b);
            border-bottom: 1px solid rgba(99,102,241,0.2);
            display: flex; align-items: center; overflow: hidden; z-index: 99;
        }
        .ticker-label {
            flex-shrink: 0;
            background: var(--indigo);
            height: 100%; padding: 0 20px;
            display: flex; align-items: center;
            font-size: 0.7rem; font-weight: 800; letter-spacing: 0.12em;
            text-transform: uppercase; color: white; white-space: nowrap;
            clip-path: polygon(0 0, calc(100% - 14px) 0, 100% 50%, calc(100% - 14px) 100%, 0 100%);
            padding-right: 30px;
        }
        .ticker-track { flex: 1; overflow: hidden; }
        .ticker-inner {
            display: flex; gap: 3rem; white-space: nowrap;
            animation: ticker-scroll 60s linear infinite;
        }
        .ticker-inner:hover { animation-play-state: paused; }
        @keyframes ticker-scroll {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .ticker-item {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 0.78rem; font-weight: 600; color: rgba(241,245,249,0.75);
        }
        .ticker-item .ti-class {
            color: var(--cyan); font-weight: 800;
        }
        .ticker-item .ti-sep {
            color: var(--indigo); opacity: 0.6;
        }

        /* ══════════════════════════════════════
           MAIN CONTENT
        ══════════════════════════════════════ */
        .tv-body {
            position: fixed;
            top: calc(var(--header-h) + var(--ticker-h));
            bottom: 0; left: 0; right: 0;
            display: flex; flex-direction: column;
            overflow: hidden; z-index: 1;
        }

        /* ── Tab navigation (kelas) ── */
        .class-tabs {
            display: flex; align-items: center; gap: 6px;
            padding: 14px 2rem 10px;
            flex-shrink: 0; overflow-x: auto;
            scrollbar-width: none;
        }
        .class-tabs::-webkit-scrollbar { display: none; }

        .class-tab {
            flex-shrink: 0; cursor: pointer;
            padding: 7px 20px; border-radius: 10px;
            font-size: 0.78rem; font-weight: 700;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            color: var(--muted);
            transition: all 0.25s;
            white-space: nowrap;
        }
        .class-tab:hover { background: rgba(99,102,241,0.15); color: var(--indigo-2); border-color: rgba(99,102,241,0.3); }
        .class-tab.active {
            background: linear-gradient(135deg, var(--indigo), #4f46e5);
            border-color: transparent; color: white;
            box-shadow: 0 4px 16px rgba(99,102,241,0.4);
        }

        /* ── Jadwal panels (satu per rombel) ── */
        .jadwal-panels { flex: 1; overflow: hidden; position: relative; }

        .jadwal-panel {
            position: absolute; inset: 0;
            display: none; flex-direction: column;
            padding: 0 2rem 1rem;
        }
        .jadwal-panel.active { display: flex; }

        /* ── Panel header (info kelas) ── */
        .panel-meta {
            display: flex; align-items: center; gap: 1rem;
            margin-bottom: 14px; flex-shrink: 0;
        }
        .meta-kelas-badge {
            display: flex; align-items: center; gap: 10px;
        }
        .meta-kelas-icon {
            width: 42px; height: 42px; border-radius: 10px;
            background: linear-gradient(135deg, var(--indigo), #4f46e5);
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 1.1rem; color: white;
            box-shadow: 0 4px 12px rgba(99,102,241,0.3);
        }
        .meta-kelas-name {
            font-size: 1.25rem; font-weight: 900; letter-spacing: -0.02em;
        }
        .meta-wali {
            display: flex; align-items: center; gap: 6px;
            font-size: 0.75rem; color: var(--muted); font-weight: 600;
            background: rgba(255,255,255,0.04); border: 1px solid var(--border);
            border-radius: 999px; padding: 4px 14px;
        }
        .meta-wali svg { width: 13px; height: 13px; }

        /* ── Jadwal table ── */
        .jadwal-table-wrap {
            flex: 1; overflow: hidden;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: rgba(13,20,38,0.7);
            backdrop-filter: blur(12px);
        }
        .jadwal-table {
            width: 100%; height: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* Header row */
        .jadwal-table thead th {
            background: rgba(99,102,241,0.12);
            border-bottom: 1px solid rgba(99,102,241,0.2);
            padding: 10px 8px;
            font-size: 0.72rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: var(--indigo-2); white-space: nowrap;
        }
        .jadwal-table thead th:first-child {
            width: 90px; color: var(--muted);
        }
        .jadwal-table thead th.today-col {
            background: rgba(34,211,238,0.12);
            color: var(--cyan);
        }

        /* Body rows */
        .jadwal-table tbody tr { transition: background 0.15s; }
        .jadwal-table tbody tr:hover { background: rgba(255,255,255,0.02); }
        .jadwal-table tbody tr:not(:last-child) td {
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }

        /* Jam column */
        .td-jam {
            text-align: center; padding: 6px;
            border-right: 1px solid var(--border);
            background: rgba(255,255,255,0.02);
        }
        .jam-ke {
            display: block; font-size: 1.4rem; font-weight: 900;
            color: var(--indigo-2); line-height: 1;
        }
        .jam-time {
            display: block; font-size: 0.58rem; font-weight: 600;
            color: var(--muted); margin-top: 2px; font-variant-numeric: tabular-nums;
        }

        /* Cell */
        .td-cell {
            padding: 5px 6px; vertical-align: middle; text-align: center;
            border-right: 1px solid rgba(255,255,255,0.04);
        }
        .td-cell:last-child { border-right: none; }
        .td-cell.today-col { background: rgba(34,211,238,0.04); }

        /* Empty cell */
        .cell-empty {
            color: rgba(255,255,255,0.08); font-size: 1rem;
        }

        /* Activity cell */
        .cell-activity {
            display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 2px;
            height: 100%;
        }
        .activity-icon {
            width: 28px; height: 28px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .activity-icon svg { width: 15px; height: 15px; }
        .activity-name {
            font-size: 0.62rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.06em;
        }

        /* Mapel cell */
        .cell-mapel {
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 3px; padding: 4px 2px; border-radius: 8px;
            height: 100%; min-height: 56px;
        }
        .mapel-name {
            font-size: 0.78rem; font-weight: 800;
            line-height: 1.2; text-align: center;
        }
        .mapel-guru {
            font-size: 0.6rem; font-weight: 600; opacity: 0.7;
            text-align: center; line-height: 1.2;
        }
        .mapel-code {
            font-size: 0.55rem; font-weight: 800; letter-spacing: 0.06em;
            background: rgba(255,255,255,0.1); border-radius: 4px;
            padding: 1px 6px; text-transform: uppercase;
        }

        /* Color palette for mapel cards */
        .c0 { background: rgba(99,102,241,0.2);  color: #a5b4fc; }
        .c1 { background: rgba(16,185,129,0.2);  color: #6ee7b7; }
        .c2 { background: rgba(245,158,11,0.2);  color: #fcd34d; }
        .c3 { background: rgba(244,63,94,0.2);   color: #fda4af; }
        .c4 { background: rgba(34,211,238,0.2);  color: #67e8f9; }
        .c5 { background: rgba(168,85,247,0.2);  color: #d8b4fe; }
        .c6 { background: rgba(251,146,60,0.2);  color: #fdba74; }
        .c7 { background: rgba(20,184,166,0.2);  color: #5eead4; }
        .c8 { background: rgba(236,72,153,0.2);  color: #f9a8d4; }
        .c9 { background: rgba(132,204,22,0.2);  color: #bef264; }

        /* Activity types */
        .act-istirahat   { background: rgba(245,158,11,0.15); color: #fcd34d; }
        .act-ishoma      { background: rgba(245,158,11,0.15); color: #fcd34d; }
        .act-sholawat    { background: rgba(16,185,129,0.15); color: #6ee7b7; }
        .act-upacara     { background: rgba(239,68,68,0.15);  color: #fca5a5; }
        .act-kegiatan_4r { background: rgba(168,85,247,0.15); color: #d8b4fe; }
        .act-default     { background: rgba(99,102,241,0.15); color: #a5b4fc; }

        /* ── Highlight today column ── */
        .jadwal-table thead th.highlight-today {
            background: linear-gradient(180deg, rgba(34,211,238,0.2), rgba(34,211,238,0.08));
            color: var(--cyan);
            position: relative;
        }
        .jadwal-table thead th.highlight-today::after {
            content: 'HARI INI';
            display: block; font-size: 0.5rem; font-weight: 900;
            letter-spacing: 0.15em; color: var(--cyan); opacity: 0.7;
            margin-top: 2px;
        }
        .td-cell.highlight-today {
            background: rgba(34,211,238,0.06);
            border-left: 1px solid rgba(34,211,238,0.15);
            border-right: 1px solid rgba(34,211,238,0.15);
        }

        /* ── Auto-scroll indicator ── */
        .auto-nav {
            position: fixed; bottom: 18px; right: 24px;
            display: flex; align-items: center; gap: 8px; z-index: 200;
        }
        .auto-nav-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: rgba(255,255,255,0.15); transition: all 0.35s;
        }
        .auto-nav-dot.active { background: var(--indigo); width: 22px; border-radius: 4px; }

        /* ── Current lesson highlight ── */
        @keyframes current-pulse {
            0%,100% { box-shadow: inset 0 0 0 2px rgba(16,185,129,0.6); }
            50%      { box-shadow: inset 0 0 0 2px rgba(16,185,129,0.2); }
        }
        .current-lesson { animation: current-pulse 2s ease-in-out infinite; }

        /* ══════════════════════════════════════
           FOOTER BAR
        ══════════════════════════════════════ */
        .tv-footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            height: 36px;
            background: rgba(5,9,26,0.9);
            border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem;
            font-size: 0.65rem; font-weight: 600; color: var(--muted);
            z-index: 100;
        }
        .footer-left { display: flex; align-items: center; gap: 14px; }
        .footer-pill {
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.2);
            border-radius: 999px; padding: 2px 10px;
            color: var(--indigo-2); font-size: 0.62rem;
        }
    </style>
</head>
<body>

<div class="bg-grid"></div>
<div class="bg-orb orb-a"></div>
<div class="bg-orb orb-b"></div>

{{-- ══ HEADER ══ --}}
<header class="tv-header">
    <div class="header-logo">
        <div class="logo-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div class="logo-text">
            <h1>Jadwal Pelajaran</h1>
            <p>SMK Telkom — {{ $tahunAktif->tahun ?? date('Y').'/'.(date('Y')+1) }} ({{ $tahunAktif->semester ?? 'Ganjil' }})</p>
        </div>
    </div>

    <div class="header-divider"></div>

    <div class="header-info">
        <div class="header-badge">
            <span class="live-dot"></span>
            LIVE DISPLAY
        </div>
        <div class="header-day-pill" id="today-label">—</div>
    </div>

    <div class="header-right">
        <div class="clock-block">
            <div class="clock-time" id="clock">--:--:--</div>
            <div class="clock-date" id="clock-date">—</div>
        </div>
    </div>
</header>

{{-- ══ TICKER ══ --}}
<div class="ticker-bar">
    <div class="ticker-label">📢 Info Kelas</div>
    <div class="ticker-track">
        <div class="ticker-inner" id="ticker-inner">
            @foreach($rombels as $rombel)
                @php
                    $jadwalRombel = $jadwalPerRombel[$rombel->id] ?? [];
                    $totalMapel   = collect($jadwalRombel)->count();
                @endphp
                <span class="ticker-item">
                    <span class="ti-class">{{ $rombel->kelas->nama_kelas }}</span>
                    <span>Wali Kelas: {{ $rombel->waliKelas->name ?? '-' }}</span>
                    <span class="ti-sep">•</span>
                    <span>{{ $totalMapel }} slot terjadwal</span>
                </span>
                <span class="ticker-item"><span class="ti-sep">✦</span></span>
            @endforeach
            {{-- duplikat untuk loop seamless --}}
            @foreach($rombels as $rombel)
                @php $jadwalRombel = $jadwalPerRombel[$rombel->id] ?? []; $totalMapel = collect($jadwalRombel)->count(); @endphp
                <span class="ticker-item">
                    <span class="ti-class">{{ $rombel->kelas->nama_kelas }}</span>
                    <span>Wali Kelas: {{ $rombel->waliKelas->name ?? '-' }}</span>
                    <span class="ti-sep">•</span>
                    <span>{{ $totalMapel }} slot terjadwal</span>
                </span>
                <span class="ticker-item"><span class="ti-sep">✦</span></span>
            @endforeach
        </div>
    </div>
</div>

{{-- ══ BODY ══ --}}
<main class="tv-body">

    {{-- Tab Kelas --}}
    <div class="class-tabs" id="class-tabs">
        @foreach($rombels as $idx => $rombel)
            <button class="class-tab {{ $idx === 0 ? 'active' : '' }}"
                    data-panel="{{ $rombel->id }}"
                    onclick="switchPanel({{ $rombel->id }}, this)">
                {{ $rombel->kelas->nama_kelas }}
            </button>
        @endforeach
    </div>

    {{-- Panel per Kelas --}}
    <div class="jadwal-panels">
        @foreach($rombels as $idx => $rombel)
            @php
                $jadwalKelas = $jadwalPerRombel[$rombel->id] ?? [];
                $namaKelas   = $rombel->kelas->nama_kelas;
                $initKelas   = substr($namaKelas, 0, 1);
            @endphp

            <div class="jadwal-panel {{ $idx === 0 ? 'active' : '' }}"
                 id="panel-{{ $rombel->id }}">

                {{-- Meta kelas --}}
                <div class="panel-meta">
                    <div class="meta-kelas-badge">
                        <div class="meta-kelas-icon">{{ $initKelas }}</div>
                        <span class="meta-kelas-name">{{ $namaKelas }}</span>
                    </div>
                    <div class="meta-wali">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Wali Kelas: <strong>{{ $rombel->waliKelas->name ?? 'Belum ditentukan' }}</strong>
                    </div>
                </div>

                {{-- Table jadwal --}}
                <div class="jadwal-table-wrap">
                    <table class="jadwal-table">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                @foreach($days as $day)
                                    <th class="day-header" data-day="{{ $day }}">{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jamKeList as $jamKe)
                                <tr>
                                    {{-- Kolom jam --}}
                                    <td class="td-jam">
                                        @php
                                            $refSlot = $jamLookup["{$jamKe}-Senin"]
                                                    ?? $jamLookup["{$jamKe}-Senin"]
                                                    ?? null;
                                        @endphp
                                        <span class="jam-ke">{{ $jamKe }}</span>
                                        @if($refSlot)
                                            <span class="jam-time">
                                                {{ \Carbon\Carbon::parse($refSlot->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($refSlot->jam_selesai)->format('H:i') }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kolom per hari --}}
                                    @foreach($days as $day)
                                        @php
                                            $slot         = $jamLookup["{$jamKe}-{$day}"] ?? null;
                                            $jadwalEntry  = $jadwalKelas["{$day}-{$jamKe}"] ?? null;

                                            $isActivity   = false;
                                            $actType      = '';
                                            $actName      = '';

                                            if ($slot && $slot->tipe_kegiatan) {
                                                $tk = $slot->tipe_kegiatan;
                                                if (in_array($tk, ['istirahat', 'sholawat_pagi', 'ishoma'])) {
                                                    $isActivity = true;
                                                } elseif ($tk === 'upacara' && $day === 'Senin') {
                                                    $isActivity = true;
                                                } elseif ($tk === 'kegiatan_4r' && $day === 'Jumat') {
                                                    $isActivity = true;
                                                }
                                                if ($isActivity) {
                                                    $actType = str_replace('_pagi', '', $tk);
                                                    $actName = ucwords(str_replace('_', ' ', $slot->tipe_kegiatan));
                                                    if ($slot->keterangan) $actName .= ' — '.$slot->keterangan;
                                                }
                                            }

                                            // Warna mapel berdasarkan nama mapel
                                            $colorIdx = 0;
                                            if ($jadwalEntry) {
                                                $colorIdx = crc32($jadwalEntry['kode']) % 10;
                                                if ($colorIdx < 0) $colorIdx += 10;
                                            }
                                        @endphp

                                        <td class="td-cell day-cell" data-day="{{ $day }}"
                                            @if($slot)
                                            data-mulai="{{ $slot->jam_mulai }}"
                                            data-selesai="{{ $slot->jam_selesai }}"
                                            @endif
                                        >
                                            @if(!$slot)
                                                {{-- Slot tidak ada --}}
                                                <span class="cell-empty">—</span>

                                            @elseif($isActivity)
                                                {{-- Kegiatan khusus --}}
                                                @php
                                                    $actCls = match($actType) {
                                                        'istirahat'  => 'act-istirahat',
                                                        'ishoma'     => 'act-ishoma',
                                                        'sholawat'   => 'act-sholawat',
                                                        'upacara'    => 'act-upacara',
                                                        'kegiatan_4r'=> 'act-kegiatan_4r',
                                                        default      => 'act-default',
                                                    };
                                                    $actIcon = match($actType) {
                                                        'istirahat','ishoma' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                                        'sholawat'           => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
                                                        'upacara'            => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>',
                                                        default              => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
                                                    };
                                                @endphp
                                                <div class="cell-activity {{ $actCls }} cell-mapel">
                                                    <div class="activity-icon {{ $actCls }}" style="background:transparent;">
                                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $actIcon !!}</svg>
                                                    </div>
                                                    <span class="activity-name">{{ $actName }}</span>
                                                </div>

                                            @elseif($jadwalEntry)
                                                {{-- Ada jadwal pelajaran --}}
                                                <div class="cell-mapel c{{ $colorIdx }}">
                                                    <span class="mapel-code">{{ $jadwalEntry['kode'] }}</span>
                                                    <span class="mapel-name">{{ $jadwalEntry['mapel'] }}</span>
                                                    <span class="mapel-guru">{{ $jadwalEntry['guru'] }}</span>
                                                </div>

                                            @else
                                                {{-- Slot kosong --}}
                                                <span class="cell-empty" style="font-size:1.2rem;opacity:.15;">·</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>{{-- /.jadwal-panel --}}
        @endforeach
    </div>

</main>

{{-- ══ AUTO-NAV DOTS ══ --}}
<div class="auto-nav" id="auto-nav">
    @foreach($rombels as $idx => $rombel)
        <div class="auto-nav-dot {{ $idx === 0 ? 'active' : '' }}" data-idx="{{ $idx }}"></div>
    @endforeach
</div>

{{-- ══ FOOTER ══ --}}
<footer class="tv-footer">
    <div class="footer-left">
        <span>© {{ date('Y') }} SMK Telkom — Sistem Informasi Kesiswaan</span>
        <span class="footer-pill">Auto-rotate 15 detik</span>
        <span class="footer-pill" id="next-class-label">Kelas berikutnya: —</span>
    </div>
    <span>Refresh otomatis setiap 5 menit • <em>Jadwal dapat berubah sewaktu-waktu</em></span>
</footer>

<script>
// ─────────────────────────────────────────────
// Data dari Blade
// ─────────────────────────────────────────────
const DAYS_ID = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

const panels = Array.from(document.querySelectorAll('.jadwal-panel'));
const tabs   = Array.from(document.querySelectorAll('.class-tab'));
const dots   = Array.from(document.querySelectorAll('.auto-nav-dot'));
let currentIdx = 0;

// ─────────────────────────────────────────────
// Clock
// ─────────────────────────────────────────────
function updateClock() {
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');

    document.getElementById('clock').textContent =
        pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());

    const day   = DAYS_ID[now.getDay()];
    const date  = now.getDate();
    const month = MONTHS_ID[now.getMonth()];
    const year  = now.getFullYear();
    document.getElementById('clock-date').textContent = `${day}, ${date} ${month} ${year}`;
    document.getElementById('today-label').textContent = day.toUpperCase();

    // Highlight today's column
    highlightToday(now.getDay());

    // Highlight current lesson
    highlightCurrentLesson(now);
}
updateClock();
setInterval(updateClock, 1000);

// ─────────────────────────────────────────────
// Highlight today column
// ─────────────────────────────────────────────
const todayDayName = () => DAYS_ID[new Date().getDay()];
function highlightToday(dayIndex) {
    const dayName = DAYS_ID[dayIndex];
    document.querySelectorAll('.day-header').forEach(th => {
        th.classList.toggle('highlight-today', th.dataset.day === dayName);
    });
    document.querySelectorAll('.day-cell').forEach(td => {
        td.classList.toggle('highlight-today', td.dataset.day === dayName);
    });
}
highlightToday(new Date().getDay());

// ─────────────────────────────────────────────
// Highlight current running lesson
// ─────────────────────────────────────────────
function highlightCurrentLesson(now) {
    const nowMin = now.getHours() * 60 + now.getMinutes();
    const dayName = DAYS_ID[now.getDay()];

    document.querySelectorAll('.day-cell').forEach(td => {
        td.classList.remove('current-lesson');
        if (td.dataset.day !== dayName) return;
        const mulai   = td.dataset.mulai;
        const selesai = td.dataset.selesai;
        if (!mulai || !selesai) return;
        const [mH, mM] = mulai.split(':').map(Number);
        const [sH, sM] = selesai.split(':').map(Number);
        const startMin = mH * 60 + mM;
        const endMin   = sH * 60 + sM;
        if (nowMin >= startMin && nowMin < endMin) {
            td.classList.add('current-lesson');
        }
    });
}

// ─────────────────────────────────────────────
// Panel switching
// ─────────────────────────────────────────────
function switchPanel(panelId, tabEl) {
    panels.forEach(p => p.classList.remove('active'));
    tabs.forEach(t => t.classList.remove('active'));
    dots.forEach((d, i) => {
        d.classList.toggle('active', tabs[i] === tabEl);
    });

    document.getElementById('panel-' + panelId)?.classList.add('active');
    if (tabEl) tabEl.classList.add('active');

    currentIdx = tabs.indexOf(tabEl);
    updateNextLabel();
}

// ─────────────────────────────────────────────
// Auto-rotate every 15s
// ─────────────────────────────────────────────
function updateNextLabel() {
    const nextIdx = (currentIdx + 1) % tabs.length;
    const nextTab = tabs[nextIdx];
    document.getElementById('next-class-label').textContent =
        'Berikutnya: ' + (nextTab?.textContent?.trim() ?? '—');
}
updateNextLabel();

let autoRotateTimer = setInterval(() => {
    const nextIdx = (currentIdx + 1) % tabs.length;
    const nextTab = tabs[nextIdx];
    const panelId = nextTab?.dataset.panel;
    if (panelId) switchPanel(panelId, nextTab);

    // Scroll tab into view
    nextTab?.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
}, 15000);

// Click tab pauses auto-rotate for 60s then resumes
tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        clearInterval(autoRotateTimer);
        setTimeout(() => {
            autoRotateTimer = setInterval(() => {
                const nextIdx = (currentIdx + 1) % tabs.length;
                const nextTab = tabs[nextIdx];
                if (nextTab?.dataset.panel) switchPanel(nextTab.dataset.panel, nextTab);
                nextTab?.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }, 15000);
        }, 60000);
    });
});

// ─────────────────────────────────────────────
// Auto-refresh halaman setiap 5 menit
// ─────────────────────────────────────────────
setTimeout(() => window.location.reload(), 5 * 60 * 1000);
</script>
</body>
</html>
