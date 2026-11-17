<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Hari Ini — SMK Telkom</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,700;1,900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #060c1e;
            --indigo:   #6366f1;
            --violet:   #7c3aed;
            --cyan:     #06b6d4;
            --emerald:  #10b981;
            --amber:    #f59e0b;
            --rose:     #f43f5e;
            --text:     #f1f5f9;
            --muted:    #64748b;
            --topbar-h: 72px;
            --ticker-h: 40px;
        }

        html, body {
            width: 100vw; height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow: hidden;
        }

        /* ══════════════════════════════
           ANIMATED BACKGROUND
        ══════════════════════════════ */
        #bg-canvas {
            position: fixed; inset: 0; z-index: 0;
            pointer-events: none;
        }

        /* ══════════════════════════════
           TOP BAR
        ══════════════════════════════ */
        .topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
            height: var(--topbar-h);
            display: flex; align-items: center;
            padding: 0 2.5rem;
            gap: 1.5rem;
            background: rgba(6,12,30,0.7);
            backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        /* ══════════════════════════════
           RUNNING TEXT TICKER
        ══════════════════════════════ */
        .ticker-bar {
            position: fixed;
            top: var(--topbar-h); left: 0; right: 0;
            height: var(--ticker-h);
            z-index: 49;
            display: flex; align-items: center; overflow: hidden;
            background: linear-gradient(90deg, rgba(20,14,60,0.95) 0%, rgba(10,16,40,0.95) 100%);
            border-bottom: 1px solid rgba(99,102,241,0.2);
        }
        .ticker-label {
            flex-shrink: 0;
            height: 100%; padding: 0 18px 0 20px;
            display: flex; align-items: center; gap: 7px;
            background: linear-gradient(135deg, var(--indigo), #4f46e5);
            font-size: 0.62rem; font-weight: 800;
            letter-spacing: 0.15em; text-transform: uppercase;
            color: white; white-space: nowrap;
            clip-path: polygon(0 0, calc(100% - 12px) 0, 100% 50%, calc(100% - 12px) 100%, 0 100%);
            padding-right: 28px;
        }
        .ticker-label svg { width: 12px; height: 12px; }
        .ticker-track { flex: 1; overflow: hidden; position: relative; }
        .ticker-inner {
            display: inline-flex; align-items: center; gap: 0;
            white-space: nowrap;
            animation: ticker-run 35s linear infinite;
            will-change: transform;
        }
        .ticker-inner:hover { animation-play-state: paused; }
        @keyframes ticker-run {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .ticker-item {
            display: inline-flex; align-items: center;
            font-size: 0.78rem; font-weight: 600;
            color: rgba(226,232,240,0.85);
            padding: 0 32px;
        }
        .ticker-sep {
            color: var(--indigo); opacity: 0.5;
            font-size: 0.9rem; padding: 0 4px;
        }
        .ticker-empty {
            font-size: 0.72rem; font-weight: 600; color: rgba(100,116,139,0.6);
            font-style: italic; padding: 0 20px;
        }

        .tb-logo {
            display: flex; align-items: center; gap: 12px; flex-shrink: 0;
        }
        .tb-logo-icon {
            width: 46px; height: 46px; border-radius: 13px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 0 1px rgba(99,102,241,0.4), 0 8px 20px rgba(99,102,241,0.3);
        }
        .tb-logo-icon svg { width: 24px; height: 24px; color: white; }
        .tb-title { font-size: 1.05rem; font-weight: 900; letter-spacing: -0.02em; }
        .tb-sub   { font-size: 0.65rem; color: var(--muted); font-weight: 600; }

        .tb-sep { width: 1px; height: 36px; background: rgba(255,255,255,0.07); }

        .tb-day-badge {
            background: rgba(6,182,212,0.15);
            border: 1px solid rgba(6,182,212,0.3);
            border-radius: 999px; padding: 5px 16px;
            font-size: 0.8rem; font-weight: 800; color: var(--cyan);
            letter-spacing: 0.04em;
        }

        .tb-tp {
            font-size: 0.72rem; color: var(--muted); font-weight: 600;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 999px; padding: 4px 14px;
        }

        .tb-right { margin-left: auto; display: flex; align-items: center; gap: 1.5rem; }

        .clock-wrap { text-align: right; }
        .clock-time {
            font-size: 2.2rem; font-weight: 900;
            letter-spacing: -0.05em;
            font-variant-numeric: tabular-nums;
            background: linear-gradient(to right, #f1f5f9, #818cf8);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            line-height: 1;
        }
        .clock-date { font-size: 0.65rem; color: var(--muted); font-weight: 600; margin-top: 1px; }

        /* ══════════════════════════════
           SLIDER WRAPPER
        ══════════════════════════════ */
        .slider-stage {
            position: fixed;
            top: calc(var(--topbar-h) + var(--ticker-h));
            bottom: 56px; left: 0; right: 0;
            z-index: 1;
            overflow: hidden;
        }

        .slide {
            position: absolute; inset: 0;
            display: flex; flex-direction: column;
            opacity: 0;
            transform: translateX(60px) scale(0.97);
            transition: opacity 0.7s cubic-bezier(.4,0,.2,1),
                        transform 0.7s cubic-bezier(.4,0,.2,1);
            pointer-events: none;
        }
        .slide.active {
            opacity: 1; transform: none; pointer-events: auto;
        }
        .slide.exit {
            opacity: 0; transform: translateX(-60px) scale(0.97);
        }

        /* ══════════════════════════════
           SLIDE INNER LAYOUT
        ══════════════════════════════ */
        .slide-inner {
            display: grid;
            grid-template-columns: 320px 1fr;
            height: 100%;
            gap: 0;
        }

        /* ─ LEFT PANEL ─ */
        .slide-left {
            position: relative;
            display: flex; flex-direction: column;
            justify-content: center;
            padding: 2rem 2.5rem;
            border-right: 1px solid rgba(255,255,255,0.06);
            overflow: hidden;
        }
        .slide-left-bg {
            position: absolute; inset: 0;
            background: linear-gradient(160deg, rgba(99,102,241,0.12) 0%, transparent 70%);
        }
        .slide-left-glow {
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.2;
            bottom: -80px; left: -80px;
        }

        .class-number {
            font-size: 0.6rem; font-weight: 800; letter-spacing: 0.2em;
            text-transform: uppercase; color: var(--indigo);
            margin-bottom: 0.75rem;
            display: flex; align-items: center; gap: 8px;
        }
        .class-number::before {
            content: '';
            display: inline-block; width: 24px; height: 2px;
            background: var(--indigo);
            border-radius: 2px;
        }

        .class-name {
            font-size: 2.8rem; font-weight: 900;
            letter-spacing: -0.04em; line-height: 1;
            margin-bottom: 1.2rem;
        }
        .class-name span {
            display: block;
            font-size: 1.1rem; font-weight: 700;
            background: linear-gradient(to right, var(--cyan), var(--indigo));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            letter-spacing: -0.01em; margin-bottom: 0.2rem;
        }

        .wali-card {
            display: flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 14px; padding: 12px 16px;
            margin-bottom: 1.5rem;
        }
        .wali-avatar {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--indigo), var(--violet));
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 0.9rem; color: white; flex-shrink: 0;
        }
        .wali-info p { font-size: 0.6rem; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; }
        .wali-info h4 { font-size: 0.82rem; font-weight: 800; color: var(--text); margin-top: 1px; }

        .stats-row {
            display: flex; gap: 10px;
        }
        .stat-box {
            flex: 1; background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 12px; padding: 10px 12px;
            text-align: center;
        }
        .stat-box .sv { font-size: 1.6rem; font-weight: 900; line-height: 1; }
        .stat-box .sk { font-size: 0.55rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; margin-top: 2px; }
        .sv-indigo  { color: var(--indigo); }
        .sv-cyan    { color: var(--cyan); }
        .sv-emerald { color: var(--emerald); }

        /* ─ RIGHT PANEL (timeline) ─ */
        .slide-right {
            display: flex; flex-direction: column;
            padding: 1.2rem 2rem 1rem;
            overflow: hidden;
        }

        .timeline-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1rem; flex-shrink: 0;
        }
        .timeline-title {
            font-size: 0.65rem; font-weight: 800; letter-spacing: 0.18em;
            text-transform: uppercase; color: var(--muted);
            display: flex; align-items: center; gap: 8px;
        }
        .timeline-title::before {
            content: ''; width: 3px; height: 14px;
            background: linear-gradient(to bottom, var(--indigo), var(--cyan));
            border-radius: 2px; display: inline-block;
        }
        .now-badge {
            display: flex; align-items: center; gap: 6px;
            background: rgba(16,185,129,0.15);
            border: 1px solid rgba(16,185,129,0.3);
            border-radius: 999px; padding: 4px 12px;
            font-size: 0.65rem; font-weight: 800; color: var(--emerald);
        }
        .now-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--emerald); animation: pulse-g 1.6s infinite; }
        @keyframes pulse-g { 0%,100%{box-shadow:0 0 0 0 rgba(16,185,129,.8)} 50%{box-shadow:0 0 0 5px rgba(16,185,129,0)} }

        .timeline-scroll {
            flex: 1; overflow-y: auto; overflow-x: hidden;
            padding-right: 4px;
            scrollbar-width: thin;
            scrollbar-color: rgba(99,102,241,0.3) transparent;
        }
        .timeline-scroll::-webkit-scrollbar { width: 4px; }
        .timeline-scroll::-webkit-scrollbar-track { background: transparent; }
        .timeline-scroll::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 2px; }

        /* Timeline item */
        .tl-row {
            display: flex; gap: 16px; align-items: stretch;
            margin-bottom: 10px; position: relative;
        }
        .tl-row:last-child .tl-line { display: none; }

        .tl-time-col {
            width: 68px; flex-shrink: 0;
            display: flex; flex-direction: column; align-items: flex-end;
            padding-top: 10px;
        }
        .tl-time {
            font-size: 0.72rem; font-weight: 800; color: var(--muted);
            font-variant-numeric: tabular-nums; line-height: 1;
            white-space: nowrap;
        }
        .tl-time.current { color: var(--cyan); }
        .tl-dash { font-size: 0.55rem; color: var(--muted); margin: 2px 0; }
        .tl-time-end { font-size: 0.65rem; font-weight: 600; color: var(--muted); opacity: 0.6; }

        .tl-dot-col {
            display: flex; flex-direction: column; align-items: center;
            flex-shrink: 0; width: 24px;
        }
        .tl-dot {
            width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0;
            border: 2px solid var(--muted); background: transparent;
            margin-top: 12px; position: relative; z-index: 2;
            transition: all 0.3s;
        }
        .tl-dot.done    { background: rgba(100,116,139,0.3); border-color: var(--muted); }
        .tl-dot.current { background: var(--cyan); border-color: var(--cyan); box-shadow: 0 0 0 4px rgba(6,182,212,0.25); }
        .tl-dot.upcoming{ background: transparent; border-color: rgba(255,255,255,0.2); }
        .tl-dot.activity { background: var(--amber); border-color: var(--amber); }

        .tl-line {
            flex: 1; width: 1px; background: rgba(255,255,255,0.07);
            margin-top: 4px; margin-bottom: -6px;
        }
        .tl-line.progress {
            background: linear-gradient(to bottom, var(--cyan), rgba(255,255,255,0.07));
        }

        .tl-card {
            flex: 1; border-radius: 14px; padding: 10px 14px;
            border: 1px solid rgba(255,255,255,0.07);
            background: rgba(255,255,255,0.03);
            transition: all 0.35s;
            position: relative; overflow: hidden;
            min-height: 56px; display: flex; align-items: center;
        }
        .tl-card.current {
            background: rgba(6,182,212,0.1);
            border-color: rgba(6,182,212,0.35);
            box-shadow: 0 4px 20px rgba(6,182,212,0.12);
        }
        .tl-card.done { opacity: 0.4; }
        .tl-card.activity {
            background: linear-gradient(135deg, rgba(245,158,11,0.1), rgba(245,158,11,0.05));
            border-color: rgba(245,158,11,0.25);
        }
        .tl-card.activity.current {
            background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(245,158,11,0.1));
            border-color: rgba(245,158,11,0.5);
            box-shadow: 0 4px 20px rgba(245,158,11,0.15);
        }
        .tl-card-glow {
            position: absolute; inset: 0; border-radius: inherit;
            background: linear-gradient(90deg, rgba(6,182,212,0.08), transparent);
            opacity: 0; transition: opacity 0.4s;
        }
        .tl-card.current .tl-card-glow { opacity: 1; }

        .tl-jam-badge {
            flex-shrink: 0; width: 28px; height: 28px;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 900; margin-right: 10px;
            background: rgba(99,102,241,0.15); color: #a5b4fc;
        }
        .tl-card.current .tl-jam-badge { background: rgba(6,182,212,0.2); color: var(--cyan); }
        .tl-card.activity .tl-jam-badge { background: rgba(245,158,11,0.2); color: var(--amber); }

        .tl-content { flex: 1; min-width: 0; }
        .tl-mapel {
            font-size: 0.9rem; font-weight: 800; line-height: 1.2;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .tl-guru {
            font-size: 0.68rem; font-weight: 600; color: var(--muted);
            margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .tl-card.current .tl-mapel { color: #e0f2fe; }
        .tl-card.current .tl-guru  { color: rgba(224,242,254,0.6); }
        .tl-card.activity .tl-mapel { color: #fef3c7; }

        .tl-now-tag {
            flex-shrink: 0; margin-left: 8px;
            background: var(--cyan); color: var(--bg);
            font-size: 0.55rem; font-weight: 900; letter-spacing: 0.1em;
            text-transform: uppercase; border-radius: 4px; padding: 2px 7px;
            display: none;
        }
        .tl-card.current .tl-now-tag { display: block; }

        .tl-empty {
            font-size: 0.75rem; color: var(--muted); font-style: italic;
        }

        /* Color coding per mapel (10 warna) */
        .mc0 .tl-jam-badge { background:rgba(99,102,241,.18); color:#a5b4fc; }
        .mc1 .tl-jam-badge { background:rgba(16,185,129,.18); color:#6ee7b7; }
        .mc2 .tl-jam-badge { background:rgba(245,158,11,.18); color:#fcd34d; }
        .mc3 .tl-jam-badge { background:rgba(244,63,94,.18);  color:#fda4af; }
        .mc4 .tl-jam-badge { background:rgba(6,182,212,.18);  color:#67e8f9; }
        .mc5 .tl-jam-badge { background:rgba(168,85,247,.18); color:#d8b4fe; }
        .mc6 .tl-jam-badge { background:rgba(251,146,60,.18); color:#fdba74; }
        .mc7 .tl-jam-badge { background:rgba(20,184,166,.18); color:#5eead4; }
        .mc8 .tl-jam-badge { background:rgba(236,72,153,.18); color:#f9a8d4; }
        .mc9 .tl-jam-badge { background:rgba(132,204,22,.18); color:#bef264; }
        .mc0 .tl-mapel,.mc0 .tl-guru{color:#c7d2fe;} .mc0 .tl-guru{color:rgba(199,210,254,.6)}
        .mc1 .tl-mapel{color:#a7f3d0;} .mc1 .tl-guru{color:rgba(167,243,208,.6)}
        .mc2 .tl-mapel{color:#fde68a;} .mc2 .tl-guru{color:rgba(253,230,138,.6)}
        .mc3 .tl-mapel{color:#fecdd3;} .mc3 .tl-guru{color:rgba(254,205,211,.6)}
        .mc4 .tl-mapel{color:#a5f3fc;} .mc4 .tl-guru{color:rgba(165,243,252,.6)}
        .mc5 .tl-mapel{color:#e9d5ff;} .mc5 .tl-guru{color:rgba(233,213,255,.6)}
        .mc6 .tl-mapel{color:#fed7aa;} .mc6 .tl-guru{color:rgba(254,215,170,.6)}
        .mc7 .tl-mapel{color:#99f6e4;} .mc7 .tl-guru{color:rgba(153,246,228,.6)}
        .mc8 .tl-mapel{color:#fbcfe8;} .mc8 .tl-guru{color:rgba(251,207,232,.6)}
        .mc9 .tl-mapel{color:#d9f99d;} .mc9 .tl-guru{color:rgba(217,249,157,.6)}

        /* Override: current state always cyan */
        .tl-card.current .tl-mapel { color:#e0f2fe !important; }
        .tl-card.current .tl-guru  { color:rgba(224,242,254,.6) !important; }
        .tl-card.current .tl-jam-badge { background:rgba(6,182,212,.2) !important; color:var(--cyan) !important; }

        /* ══════════════════════════════
           PROGRESS BAR
        ══════════════════════════════ */
        .slide-progress-track {
            position: fixed; bottom: 56px; left: 0; right: 0; z-index: 50;
            height: 3px; background: rgba(255,255,255,0.08);
        }
        .slide-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--indigo), var(--cyan));
            width: 0%; transition: none;
        }

        /* ══════════════════════════════
           BOTTOM BAR
        ══════════════════════════════ */
        .bottombar {
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 50;
            height: 56px;
            display: flex; align-items: center;
            padding: 0 2rem;
            gap: 1rem;
            background: rgba(6,12,30,0.8);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .bb-kelas-list {
            display: flex; gap: 6px; align-items: center; flex: 1; overflow: hidden;
        }
        .bb-kelas-dot {
            height: 6px; border-radius: 3px;
            background: rgba(255,255,255,0.15);
            transition: all 0.4s cubic-bezier(.4,0,.2,1);
            cursor: pointer; flex-shrink: 0;
            min-width: 6px;
        }
        .bb-kelas-dot.active { background: var(--indigo); width: 28px !important; }

        .bb-sep { width: 1px; height: 24px; background: rgba(255,255,255,0.07); }

        .bb-next {
            font-size: 0.65rem; color: var(--muted); font-weight: 600;
            white-space: nowrap; display: flex; align-items: center; gap: 6px;
        }
        .bb-next-name { color: var(--indigo); font-weight: 800; }

        .bb-right {
            margin-left: auto;
            font-size: 0.6rem; color: rgba(100,116,139,0.5);
            font-weight: 600; text-align: right; line-height: 1.4;
        }

        /* ══════════════════════════════
           SPECIAL ACTIVITY OVERLAY
        ══════════════════════════════ */
        #activity-overlay {
            position: fixed; inset: 0; z-index: 200;
            display: none; align-items: center; justify-content: center;
            flex-direction: column;
        }
        #activity-overlay.show { display: flex; }

        .overlay-bg {
            position: absolute; inset: 0;
            background: var(--overlay-color, rgba(6,12,30,0.97));
            transition: background 1s;
        }
        .overlay-particles {
            position: absolute; inset: 0; overflow: hidden;
        }
        .particle {
            position: absolute; border-radius: 50%;
            background: var(--p-color, rgba(245,158,11,0.3));
            animation: particle-float var(--dur, 6s) ease-in-out infinite alternate;
            animation-delay: var(--del, 0s);
        }
        @keyframes particle-float {
            from { transform: translateY(0) scale(1); opacity: 0.15; }
            to   { transform: translateY(-30px) scale(1.1); opacity: 0.4; }
        }

        .overlay-content {
            position: relative; z-index: 2;
            display: flex; flex-direction: column; align-items: center;
            text-align: center; gap: 1.5rem;
            padding: 3rem;
        }
        .overlay-icon-wrap {
            width: 160px; height: 160px; border-radius: 40px;
            display: flex; align-items: center; justify-content: center;
            background: var(--icon-bg, rgba(245,158,11,0.2));
            box-shadow: 0 0 0 1px var(--icon-border, rgba(245,158,11,0.3)),
                        0 0 80px var(--icon-glow, rgba(245,158,11,0.2));
            animation: icon-pulse 3s ease-in-out infinite;
        }
        @keyframes icon-pulse {
            0%,100% { transform: scale(1); box-shadow: 0 0 0 1px var(--icon-border), 0 0 80px var(--icon-glow); }
            50%      { transform: scale(1.04); box-shadow: 0 0 0 1px var(--icon-border), 0 0 120px var(--icon-glow); }
        }
        .overlay-icon-wrap svg { width: 80px; height: 80px; }

        .overlay-label {
            font-size: 0.75rem; font-weight: 800; letter-spacing: 0.25em;
            text-transform: uppercase; color: var(--label-color, var(--amber));
            display: flex; align-items: center; gap: 10px;
        }
        .overlay-label::before, .overlay-label::after {
            content: ''; flex: 0 0 40px; height: 1px;
            background: currentColor; opacity: 0.4;
        }

        .overlay-title {
            font-size: clamp(3rem, 7vw, 6rem);
            font-weight: 900; letter-spacing: -0.04em; line-height: 1;
            color: white;
        }
        .overlay-title-color { color: var(--title-color, var(--amber)); }

        .overlay-subtitle {
            font-size: 1.15rem; font-weight: 600;
            color: rgba(241,245,249,0.5); max-width: 600px; line-height: 1.6;
        }

        .overlay-time-row {
            display: flex; align-items: center; gap: 2rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px; padding: 1.2rem 2.5rem;
            margin-top: 0.5rem;
        }
        .overlay-time-item { text-align: center; }
        .overlay-time-item .otl { font-size: 0.6rem; font-weight: 800; letter-spacing: 0.15em; text-transform: uppercase; color: var(--muted); margin-bottom: 4px; }
        .overlay-time-item .otv { font-size: 2rem; font-weight: 900; letter-spacing: -0.04em; font-variant-numeric: tabular-nums; color: var(--title-color, var(--amber)); }
        .overlay-time-sep { font-size: 2rem; font-weight: 200; color: rgba(255,255,255,0.15); }

        .overlay-countdown {
            font-size: 1rem; font-weight: 700;
            color: rgba(241,245,249,0.4);
        }
        .overlay-countdown strong { color: var(--title-color, var(--amber)); }

        /* Top bar still visible on overlay */
        #activity-overlay .topbar-mini {
            position: absolute; top: 16px; left: 50%; transform: translateX(-50%);
            display: flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);
            border-radius: 999px; padding: 6px 20px;
            font-size: 0.68rem; font-weight: 700; color: rgba(241,245,249,0.5);
        }

        /* ══════════════════════════════
           CANVAS BG (particles)
        ══════════════════════════════ */
        @keyframes slide-in  { from { opacity:0; transform:translateX(60px) scale(.97); } to { opacity:1; transform:none; } }
        @keyframes slide-out { from { opacity:1; transform:none; } to { opacity:0; transform:translateX(-60px) scale(.97); } }

        /* ══════════════════════════════
           FULL SCREEN TOTAL OVERLAY (IKLAN PENUH TANPA FRAME / BILLBOARD)
        ══════════════════════════════ */
        #fullscreen-total-overlay {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            z-index: 99999;
            background: #000;
            display: none;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        #fullscreen-total-overlay.show {
            display: flex;
        }
        .fst-item {
            position: absolute;
            inset: 0;
            width: 100vw;
            height: 100vh;
            display: none;
            align-items: center;
            justify-content: center;
            background: #000;
        }
        .fst-item.active {
            display: flex;
        }
        .fst-content {
            width: 100vw;
            height: 100vh;
            max-width: 100vw;
            max-height: 100vh;
            object-fit: contain;
            border: none;
            box-shadow: none;
        }

        /* ══════════════════════════════
           EXCLUSIVE MEDIA OVERLAY (TAKEOVER LAYAR PENUH TANPA FRAME)
        ══════════════════════════════ */
        #exclusive-media-overlay {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            z-index: 99990;
            background: #000;
            display: none;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            animation: fadeInEx 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        #exclusive-media-overlay.show {
            display: flex;
        }
        @keyframes fadeInEx {
            from { opacity: 0; transform: scale(0.99); }
            to { opacity: 1; transform: scale(1); }
        }
        .ex-media-stage {
            position: absolute;
            inset: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
        }
        .ex-fullscreen-content {
            width: 100vw;
            height: 100vh;
            max-width: 100vw;
            max-height: 100vh;
            object-fit: contain;
            border: none;
            box-shadow: none;
        }
        .ex-floating-badge {
            position: absolute;
            top: 24px;
            right: 28px;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(14px);
            padding: 8px 18px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
            pointer-events: none;
        }
        .ex-floating-badge .badge-tag {
            font-size: 0.72rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 999px;
        }
        .badge-tag.qr_hadir { background: rgba(16,185,129,0.25); color: #34d399; border: 1px solid rgba(16,185,129,0.4); }
        .badge-tag.qr_pulang { background: rgba(244,63,94,0.25); color: #fb7185; border: 1px solid rgba(244,63,94,0.4); }
        .badge-tag.mading, .badge-tag.pengumuman, .badge-tag.lainnya { background: rgba(99,102,241,0.25); color: #a5b4fc; border: 1px solid rgba(99,102,241,0.4); }

        .ex-floating-badge .time-tag {
            font-size: 0.82rem;
            font-weight: 800;
            color: #f59e0b;
            font-family: monospace;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .ex-floating-tip {
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(14px);
            padding: 8px 24px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
            pointer-events: none;
        }

        /* ══════════════════════════════
           SLIDE KOMBINASI MEDIA (LAYAR PENUH RAKSASA TANPA FRAME)
        ══════════════════════════════ */
        .slide.slide-media {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            overflow: hidden;
        }
        .slide-media-full {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            overflow: hidden;
        }
        .media-fullscreen-content {
            width: 100%;
            height: 100%;
            max-width: 100vw;
            max-height: 100%;
            object-fit: contain;
            border: none;
            box-shadow: none;
        }
        .comb-floating-pill {
            position: absolute;
            bottom: 24px;
            left: 28px;
            z-index: 10;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(14px);
            padding: 8px 20px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
            pointer-events: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
        }
        .comb-floating-pill .pill-tag {
            color: #f43f5e;
            text-transform: uppercase;
            font-weight: 900;
        }

        /* Mode Layar Penuh Raksasa saat Slide Media Kombinasi Aktif */
        body.media-fullscreen-mode .topbar,
        body.media-fullscreen-mode .ticker-bar,
        body.media-fullscreen-mode .bottombar,
        body.media-fullscreen-mode .slide-progress-track {
            opacity: 0 !important;
            pointer-events: none !important;
            visibility: hidden !important;
            transition: opacity 0.4s ease;
        }
        body.media-fullscreen-mode .slider-stage {
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 999 !important;
            background: #000 !important;
        }
        body.media-fullscreen-mode .slide.slide-media.active {
            position: absolute !important;
            inset: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            max-width: 100vw !important;
            max-height: 100vh !important;
            background: #000 !important;
            transform: none !important;
        }
        body.media-fullscreen-mode .slide-media-full {
            position: absolute !important;
            inset: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            max-width: 100vw !important;
            max-height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #000 !important;
        }
        body.media-fullscreen-mode .media-fullscreen-content {
            width: 100vw !important;
            height: 100vh !important;
            max-width: 100vw !important;
            max-height: 100vh !important;
            object-fit: contain !important;
        }
        .media-fullscreen-progress {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(255,255,255,0.08);
            z-index: 10001;
            display: none;
        }
        .media-fullscreen-progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #ec4899, #8b5cf6, #06b6d4);
            box-shadow: 0 0 12px rgba(236,72,153,0.8);
        }
        body.media-fullscreen-mode .media-fullscreen-progress {
            display: block;
        }
    </style>
    {{-- PDF.js Library --}}
    <script src="{{ asset('vendor/pdfjs/pdf.min.js') }}"></script>
</head>
<body>

{{-- ══ TOP BAR ══ --}}
<header class="topbar">
    <div class="tb-logo">
        <div class="tb-logo-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div>
            <div class="tb-title">Jadwal Pelajaran</div>
            <div class="tb-sub">SMK Telkom — Sistem Informasi Kesiswaan</div>
        </div>
    </div>
    <div class="tb-sep"></div>
    <div class="tb-day-badge" id="today-label">{{ $todayName }}</div>
    <div class="tb-tp">{{ $tahunAktif->tahun ?? date('Y').'/'.date('Y',strtotime('+1 year')) }} • {{ $tahunAktif->semester ?? 'Semester Ganjil' }}</div>
    <div class="tb-right">
        <div class="clock-wrap">
            <div class="clock-time" id="clock">--:--:--</div>
            <div class="clock-date" id="clock-date">—</div>
        </div>
    </div>
</header>

{{-- ══ RUNNING TEXT TICKER ══ --}}
<div class="ticker-bar">
    <div class="ticker-label">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
        </svg>
        INFO
    </div>
    <div class="ticker-track">
        @if(count($tickers) > 0)
            <div class="ticker-inner">
                @foreach($tickers as $t)
                    <span class="ticker-item">{{ $t }}</span><span class="ticker-sep">✦</span>
                @endforeach
                {{-- Duplicate for seamless loop --}}
                @foreach($tickers as $t)
                    <span class="ticker-item">{{ $t }}</span><span class="ticker-sep">✦</span>
                @endforeach
            </div>
        @else
            <span class="ticker-empty">Belum ada informasi running text. Tambahkan melalui Dashboard Guru Piket.</span>
        @endif
    </div>
</div>

{{-- ══ SLIDES ══ --}}
<div class="slider-stage" id="slider-stage">
    @foreach($kelasData as $idx => $kelas)
        @php
            $initial = substr($kelas['kelas'], 0, 1);
            $glowColors = ['#6366f1','#7c3aed','#06b6d4','#10b981','#f59e0b','#f43f5e','#8b5cf6','#0ea5e9'];
            $glowColor  = $glowColors[$idx % count($glowColors)];

            $totalSlots   = count($kelas['slots']);
            $activitySlots= collect($kelas['slots'])->where('is_activity', true)->count();
            $pelajaranSlots = collect($kelas['slots'])->where('is_activity', false)->whereNotNull('mapel')->count();
        @endphp

        <div class="slide {{ $idx === 0 ? 'active' : '' }}" id="slide-{{ $idx }}" data-idx="{{ $idx }}">
            <div class="slide-inner">

                {{-- LEFT --}}
                <div class="slide-left">
                    <div class="slide-left-bg"></div>
                    <div class="slide-left-glow" style="background: {{ $glowColor }};"></div>

                    <div class="class-number" style="color:{{ $glowColor }};">
                        Kelas {{ $idx + 1 }} dari {{ count($kelasData) }}
                    </div>

                    <div class="class-name">
                        <span style="background: linear-gradient(to right, {{ $glowColor }}, #818cf8); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">
                            Hari {{ $todayName }}
                        </span>
                        {{ $kelas['kelas'] }}
                    </div>

                    <div class="wali-card">
                        <div class="wali-avatar" style="background: linear-gradient(135deg, {{ $glowColor }}, #4f46e5);">
                            {{ substr($kelas['wali_kelas'], 0, 1) }}
                        </div>
                        <div class="wali-info">
                            <p>Wali Kelas</p>
                            <h4>{{ $kelas['wali_kelas'] }}</h4>
                        </div>
                    </div>

                    <div class="stats-row">
                        <div class="stat-box">
                            <div class="sv sv-indigo">{{ $totalSlots }}</div>
                            <div class="sk">Total Slot</div>
                        </div>
                        <div class="stat-box">
                            <div class="sv sv-cyan">{{ $pelajaranSlots }}</div>
                            <div class="sk">Mapel</div>
                        </div>
                        <div class="stat-box">
                            <div class="sv sv-emerald">{{ $activitySlots }}</div>
                            <div class="sk">Kegiatan</div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Timeline --}}
                <div class="slide-right">
                    <div class="timeline-header">
                        <div class="timeline-title">Jadwal Hari Ini</div>
                        <div class="now-badge"><span class="now-dot"></span> Real-time</div>
                    </div>

                    <div class="timeline-scroll" id="timeline-{{ $idx }}">
                        @if(empty($kelas['slots']))
                            <div style="display:flex;align-items:center;justify-content:center;height:200px;flex-direction:column;gap:12px;opacity:.3;">
                                <svg style="width:48px;height:48px;color:#64748b" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p style="font-size:.9rem;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.1em;">Tidak Ada Jadwal</p>
                            </div>
                        @else
                            @foreach($kelas['slots'] as $slot)
                                @php
                                    $isActivity = $slot['is_activity'];
                                    $hasMapel   = !$isActivity && !empty($slot['mapel']);
                                    $colorIdx   = 0;
                                    if ($hasMapel) {
                                        $colorIdx = abs(crc32($slot['kode'] ?? $slot['mapel'])) % 10;
                                    }

                                    $actIcons = [
                                        'istirahat'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                        'ishoma'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                        'sholawat_pagi' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
                                        'upacara'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>',
                                        'kegiatan_4r'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
                                    ];
                                    $actIcon = $actIcons[$slot['tipe_kegiatan'] ?? ''] ?? $actIcons['istirahat'];
                                @endphp

                                <div class="tl-row"
                                     data-mulai="{{ $slot['jam_mulai'] }}"
                                     data-selesai="{{ $slot['jam_selesai'] }}">

                                    {{-- Waktu --}}
                                    <div class="tl-time-col">
                                        <span class="tl-time">{{ $slot['jam_mulai'] }}</span>
                                        <span class="tl-dash">│</span>
                                        <span class="tl-time-end">{{ $slot['jam_selesai'] }}</span>
                                    </div>

                                    {{-- Dot + line --}}
                                    <div class="tl-dot-col">
                                        <div class="tl-dot upcoming {{ $isActivity ? 'activity' : '' }}"></div>
                                        <div class="tl-line"></div>
                                    </div>

                                    {{-- Card --}}
                                    <div class="tl-card {{ $isActivity ? 'activity' : ($hasMapel ? 'mc'.$colorIdx : '') }}">
                                        <div class="tl-card-glow"></div>
                                        <div class="tl-jam-badge">{{ $slot['jam_ke'] }}</div>
                                        <div class="tl-content">
                                            @if($isActivity)
                                                <div style="display:flex;align-items:center;gap:8px;">
                                                    <svg style="width:14px;height:14px;flex-shrink:0;color:var(--amber)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        {!! $actIcon !!}
                                                    </svg>
                                                    <div class="tl-mapel" style="color:#fef3c7;">{{ $slot['activity_label'] }}</div>
                                                </div>
                                                @if($slot['keterangan'])
                                                    <div class="tl-guru" style="color:rgba(254,243,199,.5);margin-top:2px;">{{ $slot['keterangan'] }}</div>
                                                @endif
                                            @elseif($hasMapel)
                                                <div class="tl-mapel">{{ $slot['mapel'] }}</div>
                                                <div class="tl-guru">{{ $slot['guru'] }}</div>
                                            @else
                                                <div class="tl-empty">— Belum ada jadwal —</div>
                                            @endif
                                        </div>
                                        <div class="tl-now-tag">SEKARANG</div>
                                    </div>

                                </div>{{-- /.tl-row --}}
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>
        </div>{{-- /.slide --}}
    @endforeach

    {{-- Slide Media Terjadwal (Kombinasi / Mading Digital) --}}
    @if(isset($kombinasiMedia) && $kombinasiMedia->isNotEmpty())
        @php $startIdx = count($kelasData); @endphp
        @foreach($kombinasiMedia as $kmIdx => $media)
            @php $slideIdx = $startIdx + $kmIdx; @endphp
            <div class="slide slide-media {{ count($kelasData) === 0 && $kmIdx === 0 ? 'active' : '' }}"
                 id="slide-{{ $slideIdx }}"
                 data-idx="{{ $slideIdx }}"
                 data-duration="{{ ($media->durasi_detik ?: 20) * 1000 }}"
                 data-media-title="{{ $media->judul }}"
                 data-file-type="{{ $media->file_type }}"
                 data-file-url="{{ $media->file_url }}">
                {{-- TAMPILAN MEDIA KOMBINASI TANPA FRAME (EDGE-TO-EDGE) --}}
                <div class="slide-media-full" id="comb-viewport-{{ $media->id }}">
                    @if($media->file_type === 'image')
                        <img src="{{ $media->file_url }}" alt="{{ $media->judul }}" class="media-fullscreen-content">
                    @else
                        <canvas id="comb-pdf-canvas-{{ $media->id }}" class="media-fullscreen-content" data-pdf-url="{{ $media->file_url }}"></canvas>
                    @endif

                    <div class="comb-floating-pill">
                        <span class="pill-tag">{{ $media->tipe_label }}</span>
                        <span>•</span>
                        <span>{{ $media->judul }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

{{-- ══ PROGRESS BAR ══ --}}
<div class="slide-progress-track">
    <div class="slide-progress-fill" id="progress-fill"></div>
</div>

{{-- ══ MEDIA FULLSCREEN PROGRESS BAR (TAMPIL HANYA SAAT SLIDE MEDIA KOMBINASI RAKSASA AKTIF) ══ --}}
<div class="media-fullscreen-progress" id="media-fullscreen-progress">
    <div class="media-fullscreen-progress-fill" id="media-fullscreen-progress-fill"></div>
</div>

{{-- ══ BOTTOM BAR ══ --}}
<div class="bottombar">
    <div class="bb-kelas-list" id="bb-dots">
        @foreach($kelasData as $idx => $kelas)
            <div class="bb-kelas-dot {{ $idx === 0 ? 'active' : '' }}"
                 style="width:{{ $idx === 0 ? '28px' : '6px' }}"
                 data-idx="{{ $idx }}"
                 title="{{ $kelas['kelas'] }}"
                 onclick="goToSlide({{ $idx }}, true)"></div>
        @endforeach
        @if(isset($kombinasiMedia))
            @foreach($kombinasiMedia as $kmIdx => $media)
                @php $dotIdx = count($kelasData) + $kmIdx; @endphp
                <div class="bb-kelas-dot"
                     style="width: 6px; background: rgba(236,72,153,0.5);"
                     data-idx="{{ $dotIdx }}"
                     title="{{ $media->judul }}"
                     onclick="goToSlide({{ $dotIdx }}, true)"></div>
            @endforeach
        @endif
    </div>
    <div class="bb-sep"></div>
    <div class="bb-next">
        Berikutnya: <span class="bb-next-name" id="next-kelas">—</span>
    </div>
    <div class="bb-right">
        Auto-rotate Smart TV &nbsp;•&nbsp; Realtime Sync<br>
        © {{ date('Y') }} SMK Telkom — Kesiswaan &amp; Kurikulum
    </div>
</div>

{{-- ══ EXCLUSIVE MEDIA OVERLAY (TAKEOVER LAYAR PENUH TANPA FRAME) ══ --}}
<div id="exclusive-media-overlay" class="{{ $exclusiveMedia ? 'show' : '' }}">
    @if($exclusiveMedia)
        <div class="ex-media-stage" id="exclusive-viewport">
            @if($exclusiveMedia->file_type === 'image')
                <img src="{{ $exclusiveMedia->file_url }}" alt="{{ $exclusiveMedia->judul }}" class="ex-fullscreen-content">
            @else
                <canvas id="exclusive-pdf-canvas" class="ex-fullscreen-content" data-pdf-url="{{ $exclusiveMedia->file_url }}"></canvas>
            @endif

            {{-- Floating Badge Jam & Status di Pojok Kanan Atas --}}
            <div class="ex-floating-badge">
                <span class="badge-tag {{ $exclusiveMedia->tipe }}">{{ $exclusiveMedia->tipe_label }}</span>
                <span class="time-tag">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $exclusiveMedia->jam_mulai_formatted }} - {{ $exclusiveMedia->jam_selesai_formatted }} WIB</span>
                </span>
            </div>

            {{-- Floating Tip di Bawah --}}
            @if(str_contains($exclusiveMedia->tipe, 'qr'))
                <div class="ex-floating-tip">
                    <svg style="width:16px;height:16px;color:#38bdf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Arahkan kamera smartphone atau buka aplikasi Sisfo untuk memindai QR Code Presensi</span>
                </div>
            @endif
        </div>
    @endif
</div>

{{-- ══ FULL SCREEN TOTAL OVERLAY (IKLAN PENUH TANPA FRAME / BILLBOARD EDGE-TO-EDGE) ══ --}}
<div id="fullscreen-total-overlay" class="{{ (isset($fullscreenTotalMedia) && $fullscreenTotalMedia->isNotEmpty()) ? 'show' : '' }}">
    @if(isset($fullscreenTotalMedia) && $fullscreenTotalMedia->isNotEmpty())
        @foreach($fullscreenTotalMedia as $fstIdx => $fst)
            <div class="fst-item {{ $fstIdx === 0 ? 'active' : '' }}"
                 id="fst-item-{{ $fst->id }}"
                 data-idx="{{ $fstIdx }}"
                 data-duration="{{ ($fst->durasi_detik ?: 20) * 1000 }}"
                 data-file-type="{{ $fst->file_type }}"
                 data-file-url="{{ $fst->file_url }}">
                @if($fst->file_type === 'image')
                    <img src="{{ $fst->file_url }}" alt="{{ $fst->judul }}" class="fst-content">
                @else
                    <canvas id="fst-canvas-{{ $fst->id }}" class="fst-content" data-pdf-url="{{ $fst->file_url }}"></canvas>
                @endif
            </div>
        @endforeach
    @endif
</div>

{{-- ══ SPECIAL ACTIVITY OVERLAY ══ --}}
<div id="activity-overlay">
    <div class="overlay-bg" id="overlay-bg"></div>
    <div class="overlay-particles" id="overlay-particles"></div>
    <div class="topbar-mini" id="overlay-tb">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/>
        </svg>
        SMK Telkom &nbsp;•&nbsp; <span id="ov-clock-mini">--:--:--</span>
    </div>
    <div class="overlay-content">
        <div class="overlay-icon-wrap" id="ov-icon-wrap">
            <svg id="ov-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--amber)">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="overlay-label" id="ov-label">Kegiatan Sekolah</div>
        <div class="overlay-title">
            <span class="overlay-title-color" id="ov-title">Istirahat</span>
        </div>
        <div class="overlay-subtitle" id="ov-subtitle">Silakan beristirahat. Pembelajaran akan dilanjutkan setelah waktu selesai.</div>
        <div class="overlay-time-row">
            <div class="overlay-time-item">
                <div class="otl">Dimulai</div>
                <div class="otv" id="ov-mulai">--:--</div>
            </div>
            <div class="overlay-time-sep">→</div>
            <div class="overlay-time-item">
                <div class="otl">Selesai</div>
                <div class="otv" id="ov-selesai">--:--</div>
            </div>
            <div class="overlay-time-sep">|</div>
            <div class="overlay-time-item">
                <div class="otl">Sisa Waktu</div>
                <div class="otv" id="ov-countdown" style="font-size:1.6rem;">--:--</div>
            </div>
        </div>
    </div>
</div>

{{-- ══ DATA JSON ══ --}}
<script>
// ─── Data dari PHP ────────────────────────────────────────────────
const ACTIVITY_SLOTS = @json($activitySlots) ?? [];
const KELAS_DATA     = @json($kelasData) ?? [];
const TODAY_NAME     = @json($todayName);
const SLIDE_DURATION = 14000; // ms
const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

// ─── Activity config ──────────────────────────────────────────────
const ACTIVITY_CONFIG = {
    istirahat: {
        label:    'Waktu Istirahat',
        subtitle: 'Silakan beristirahat. Pembelajaran dilanjutkan setelah waktu ini selesai.',
        bg:       'linear-gradient(135deg, #1a0a00 0%, #060c1e 60%)',
        iconColor:'#f59e0b',
        iconBg:   'rgba(245,158,11,0.15)',
        iconBorder:'rgba(245,158,11,0.35)',
        iconGlow: 'rgba(245,158,11,0.2)',
        titleColor:'#fbbf24',
        labelColor:'#f59e0b',
        particleColor: 'rgba(245,158,11,0.3)',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    },
    ishoma: {
        label:    'Istirahat Sholat & Makan',
        subtitle: 'Waktunya sholat dan makan siang. Jaga kebersihan dan kerapian tempat ibadah.',
        bg:       'linear-gradient(135deg, #001a0a 0%, #060c1e 60%)',
        iconColor:'#10b981',
        iconBg:   'rgba(16,185,129,0.15)',
        iconBorder:'rgba(16,185,129,0.35)',
        iconGlow: 'rgba(16,185,129,0.2)',
        titleColor:'#34d399',
        labelColor:'#10b981',
        particleColor: 'rgba(16,185,129,0.3)',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    },
    sholawat_pagi: {
        label:    'Sholawat Pagi',
        subtitle: 'Saatnya bersholawat bersama. Mulai hari dengan hati yang tenang dan penuh berkah.',
        bg:       'linear-gradient(135deg, #0a0018 0%, #060c1e 60%)',
        iconColor:'#a78bfa',
        iconBg:   'rgba(167,139,250,0.15)',
        iconBorder:'rgba(167,139,250,0.35)',
        iconGlow: 'rgba(167,139,250,0.2)',
        titleColor:'#c4b5fd',
        labelColor:'#a78bfa',
        particleColor: 'rgba(167,139,250,0.3)',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
    },
    upacara: {
        label:    'Upacara Bendera',
        subtitle: 'Ayo berbaris dengan tertib dan khidmat. Hormati jasa para pahlawan bangsa.',
        bg:       'linear-gradient(135deg, #1a0000 0%, #060c1e 60%)',
        iconColor:'#f43f5e',
        iconBg:   'rgba(244,63,94,0.15)',
        iconBorder:'rgba(244,63,94,0.35)',
        iconGlow: 'rgba(244,63,94,0.2)',
        titleColor:'#fb7185',
        labelColor:'#f43f5e',
        particleColor: 'rgba(244,63,94,0.3)',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>',
    },
    kegiatan_4r: {
        label:    'Kegiatan 4R',
        subtitle: 'Ringkas, Rapi, Resik, Rawat. Mari jaga kebersihan dan kerapian lingkungan sekolah kita.',
        bg:       'linear-gradient(135deg, #000d1a 0%, #060c1e 60%)',
        iconColor:'#06b6d4',
        iconBg:   'rgba(6,182,212,0.15)',
        iconBorder:'rgba(6,182,212,0.35)',
        iconGlow: 'rgba(6,182,212,0.2)',
        titleColor:'#22d3ee',
        labelColor:'#06b6d4',
        particleColor: 'rgba(6,182,212,0.3)',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
    },
};

// ─── State ────────────────────────────────────────────────────────
let currentSlide = 0;
let autoTimer    = null;
let progressRaf  = null;
let progressStart= null;
let isUserPaused = false;
const slides = Array.from(document.querySelectorAll('.slide'));
const bbDots = Array.from(document.querySelectorAll('.bb-kelas-dot'));

// ─── Clock ────────────────────────────────────────────────────────
const pad = n => String(n).padStart(2,'0');
function updateClock() {
    const now = new Date();
    const t   = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
    document.getElementById('clock').textContent = t;
    document.getElementById('ov-clock-mini').textContent = t;
    const d = now.getDate(), m = MONTHS_ID[now.getMonth()], y = now.getFullYear();
    document.getElementById('clock-date').textContent = `${TODAY_NAME}, ${d} ${m} ${y}`;
}
updateClock();
setInterval(updateClock, 1000);

// ─── Timeline highlight ───────────────────────────────────────────
function timeToMin(str) {
    const [h,m] = str.split(':').map(Number); return h*60+m;
}
function updateTimelines() {
    const now = new Date();
    const nowMin = now.getHours()*60 + now.getMinutes();

    slides.forEach((slide, sIdx) => {
        const rows = slide.querySelectorAll('.tl-row');
        let scrollTarget = null;

        rows.forEach(row => {
            const mulai   = timeToMin(row.dataset.mulai   || '00:00');
            const selesai = timeToMin(row.dataset.selesai || '23:59');
            const dot  = row.querySelector('.tl-dot');
            const card = row.querySelector('.tl-card');
            const time = row.querySelector('.tl-time');
            const line = row.querySelector('.tl-line');

            card.classList.remove('current','done');
            dot.classList.remove('current','done');
            if(time) time.classList.remove('current');
            if(line) line.classList.remove('progress');

            if (nowMin >= mulai && nowMin < selesai) {
                card.classList.add('current');
                dot.classList.add('current');
                if(time) time.classList.add('current');
                if(line) line.classList.add('progress');
                if(sIdx === currentSlide) scrollTarget = row;
            } else if (nowMin >= selesai) {
                card.classList.add('done');
                dot.classList.add('done');
            }
        });

        // Auto-scroll to current lesson
        if(scrollTarget && sIdx === currentSlide) {
            scrollTarget.scrollIntoView({ behavior:'smooth', block:'center' });
        }
    });
}
updateTimelines();
setInterval(updateTimelines, 5000);

// ─── Special activity overlay ─────────────────────────────────────
const overlay      = document.getElementById('activity-overlay');
const ovBg         = document.getElementById('overlay-bg');
const ovParticles  = document.getElementById('overlay-particles');
let overlayActive  = false;

function buildParticles(color) {
    ovParticles.innerHTML = '';
    for (let i = 0; i < 20; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        const size = Math.random()*180+40;
        p.style.cssText = `
            width:${size}px;height:${size}px;
            top:${Math.random()*100}%;left:${Math.random()*100}%;
            --p-color:${color};--dur:${(Math.random()*6+4).toFixed(1)}s;
            --del:-${(Math.random()*6).toFixed(1)}s;
        `;
        ovParticles.appendChild(p);
    }
}

function showActivityOverlay(slot) {
    const tk  = slot.tipe_kegiatan || 'istirahat';
    const cfg = ACTIVITY_CONFIG[tk] || ACTIVITY_CONFIG.istirahat;

    // Apply styles via CSS vars
    const w = overlay;
    w.style.setProperty('--overlay-color', cfg.bg);
    ovBg.style.background = cfg.bg;

    overlay.querySelector('.overlay-icon-wrap').style.cssText =
        `--icon-bg:${cfg.iconBg};--icon-border:${cfg.iconBorder};--icon-glow:${cfg.iconGlow};background:${cfg.iconBg};box-shadow:0 0 0 1px ${cfg.iconBorder},0 0 80px ${cfg.iconGlow};`;

    const iconEl = document.getElementById('ov-icon');
    iconEl.innerHTML = cfg.icon;
    iconEl.style.color = cfg.iconColor;

    document.querySelector('.overlay-title-color').style.color = cfg.titleColor;
    document.querySelector('.overlay-label').style.color = cfg.labelColor;
    document.querySelector('.overlay-label').style.setProperty('--label-color', cfg.labelColor);
    document.querySelectorAll('.otv').forEach(el => el.style.color = cfg.titleColor);

    document.getElementById('ov-label').textContent  = cfg.label;
    document.getElementById('ov-title').textContent  = slot.activity_label || 'Istirahat';
    document.getElementById('ov-subtitle').textContent = cfg.subtitle;
    document.getElementById('ov-mulai').textContent   = slot.jam_mulai;
    document.getElementById('ov-selesai').textContent = slot.jam_selesai;

    buildParticles(cfg.particleColor);

    overlay.classList.add('show');
    overlayActive = true;
}

function hideActivityOverlay() {
    overlay.classList.remove('show');
    overlayActive = false;
}

// Countdown update for overlay
function updateOverlayCountdown() {
    if (!overlayActive) return;
    const selesaiEl = document.getElementById('ov-selesai');
    if (!selesaiEl) return;
    const selesaiStr = selesaiEl.textContent;
    const [sh, sm] = selesaiStr.split(':').map(Number);
    const now = new Date();
    const endMin  = sh*60+sm;
    const nowMin  = now.getHours()*60+now.getMinutes();
    const diffSec = Math.max(0, (endMin - nowMin)*60 - now.getSeconds());
    const mm = Math.floor(diffSec/60), ss = diffSec%60;
    document.getElementById('ov-countdown').textContent = `${pad(mm)}:${pad(ss)}`;
}

function checkSpecialActivity() {
    if (!Array.isArray(ACTIVITY_SLOTS) || ACTIVITY_SLOTS.length === 0) return;

    const now    = new Date();
    const nowMin = now.getHours()*60 + now.getMinutes();
    let found    = null;

    for (const slot of ACTIVITY_SLOTS) {
        const mulai   = timeToMin(slot.jam_mulai   || '00:00');
        const selesai = timeToMin(slot.jam_selesai || '23:59');
        if (nowMin >= mulai && nowMin < selesai) { found = slot; break; }
    }

    if (found && !overlayActive) showActivityOverlay(found);
    if (!found && overlayActive) hideActivityOverlay();
    if (overlayActive) updateOverlayCountdown();
}
checkSpecialActivity();
setInterval(checkSpecialActivity, 10000);

// ─── PDF.js Renderer Engine ──────────────────────────────────────
if (window.pdfjsLib) {
    pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('vendor/pdfjs/pdf.worker.min.js') }}";
}

async function renderPdfToCanvas(url, canvasEl) {
    if (!canvasEl || !window.pdfjsLib || !url) return;
    try {
        const loadingTask = pdfjsLib.getDocument(url);
        const pdf = await loadingTask.promise;
        const page = await pdf.getPage(1);
        
        const parent = canvasEl.parentElement;
        const availWidth = (parent && parent.clientWidth > 50) ? parent.clientWidth : window.innerWidth;
        const availHeight = (parent && parent.clientHeight > 50) ? parent.clientHeight : window.innerHeight;

        const unscaledViewport = page.getViewport({ scale: 1.0 });
        const scaleX = availWidth / unscaledViewport.width;
        const scaleY = availHeight / unscaledViewport.height;
        const fitScale = Math.min(scaleX, scaleY);
        const scale = Math.max(fitScale, 0.5);

        const viewport = page.getViewport({ scale: scale });
        const dpr = window.devicePixelRatio || 1;

        canvasEl.width = Math.floor(viewport.width * dpr);
        canvasEl.height = Math.floor(viewport.height * dpr);
        canvasEl.style.width = Math.floor(viewport.width) + "px";
        canvasEl.style.height = Math.floor(viewport.height) + "px";

        const ctx = canvasEl.getContext('2d');
        ctx.scale(dpr, dpr);

        await page.render({
            canvasContext: ctx,
            viewport: viewport
        }).promise;
    } catch (err) {
        console.error('Gagal render canvas PDF:', err);
    }
}

// Render PDF Eksklusif jika ada saat pertama dibuka
const exclusiveCanvas = document.getElementById('exclusive-pdf-canvas');
if (exclusiveCanvas && exclusiveCanvas.dataset.pdfUrl) {
    renderPdfToCanvas(exclusiveCanvas.dataset.pdfUrl, exclusiveCanvas);
}

// ─── Full Screen Total Rotator (Iklan Penuh Tanpa Frame) ─────────
const fstItems = document.querySelectorAll('#fullscreen-total-overlay .fst-item');
let currentFstIdx = 0;
let fstTimer = null;

function renderFstItem(itemEl) {
    if (!itemEl) return;
    if (itemEl.dataset.fileType === 'pdf') {
        const canvas = itemEl.querySelector('canvas');
        if (canvas && canvas.dataset.pdfUrl && !canvas.dataset.rendered) {
            renderPdfToCanvas(canvas.dataset.pdfUrl, canvas);
            canvas.dataset.rendered = "true";
        }
    }
}

function rotateFst() {
    if (fstItems.length <= 1) return;
    fstItems[currentFstIdx].classList.remove('active');
    currentFstIdx = (currentFstIdx + 1) % fstItems.length;
    const nextItem = fstItems[currentFstIdx];
    nextItem.classList.add('active');
    renderFstItem(nextItem);

    const dur = parseInt(nextItem.dataset.duration, 10) || 20000;
    fstTimer = setTimeout(rotateFst, dur);
}

if (fstItems.length > 0) {
    renderFstItem(fstItems[0]);
    if (fstItems.length > 1) {
        const dur0 = parseInt(fstItems[0].dataset.duration, 10) || 20000;
        fstTimer = setTimeout(rotateFst, dur0);
    }
}

// ─── Slider ───────────────────────────────────────────────────────
function getSlideDuration(slideEl) {
    if (!slideEl) return SLIDE_DURATION;
    const custom = parseInt(slideEl.dataset.duration, 10);
    return (!isNaN(custom) && custom > 0) ? custom : SLIDE_DURATION;
}

function goToSlide(idx, userTriggered=false) {
    if (idx === currentSlide && !userTriggered) return;
    if (slides.length <= 1) { startProgress(getSlideDuration(slides[0])); return; }

    // Exit current
    slides[currentSlide].classList.remove('active');
    slides[currentSlide].classList.add('exit');
    const exitIdx = currentSlide;
    setTimeout(() => slides[exitIdx]?.classList.remove('exit'), 800);

    // Activate next
    currentSlide = idx;
    const activeSlide = slides[currentSlide];
    activeSlide.classList.add('active');

    // Cek apakah slide aktif adalah slide media kombinasi (Layar Penuh Raksasa Tanpa Frame)
    const isMediaSlide = activeSlide.classList.contains('slide-media');
    if (isMediaSlide) {
        document.body.classList.add('media-fullscreen-mode');
    } else {
        document.body.classList.remove('media-fullscreen-mode');
    }

    // Jika slide media bertipe PDF, pastikan sudah dirender
    if (activeSlide.dataset.fileType === 'pdf') {
        const canvas = activeSlide.querySelector('canvas');
        if (canvas && canvas.dataset.pdfUrl && !canvas.dataset.rendered) {
            renderPdfToCanvas(canvas.dataset.pdfUrl, canvas);
            canvas.dataset.rendered = "true";
        }
    }

    // Update dots
    bbDots.forEach((d, i) => d.classList.toggle('active', i === idx));

    // Update next label
    const nextIdx  = (idx+1) % slides.length;
    const nextSlideEl = slides[nextIdx];
    let nextTitle = '—';
    if (nextSlideEl) {
        nextTitle = nextSlideEl.dataset.mediaTitle || (KELAS_DATA[nextIdx]?.kelas ?? 'Informasi');
    }
    document.getElementById('next-kelas').textContent = nextTitle;

    // Update timeline immediately
    updateTimelines();

    // Progress bar dengan durasi slide aktif
    const slideDur = getSlideDuration(activeSlide);
    startProgress(slideDur);

    if (userTriggered) {
        clearInterval(autoTimer);
        isUserPaused = true;
        setTimeout(() => {
            isUserPaused = false;
            scheduleAuto();
        }, 60000);
    } else {
        scheduleAuto(slideDur);
    }
}

function startProgress(duration) {
    cancelAnimationFrame(progressRaf);
    const dur = duration || SLIDE_DURATION;
    const fill = document.getElementById('progress-fill');
    const fstFill = document.getElementById('media-fullscreen-progress-fill');
    if (fill) {
        fill.style.transition = 'none';
        fill.style.width = '0%';
    }
    if (fstFill) {
        fstFill.style.transition = 'none';
        fstFill.style.width = '0%';
    }

    progressStart = performance.now();
    function tick(ts) {
        const elapsed = ts - progressStart;
        const pct     = Math.min((elapsed / dur)*100, 100);
        if (fill) fill.style.width = pct + '%';
        if (fstFill) fstFill.style.width = pct + '%';
        if (pct < 100) progressRaf = requestAnimationFrame(tick);
    }
    progressRaf = requestAnimationFrame(tick);
}

function scheduleAuto(currentDur) {
    clearInterval(autoTimer);
    const dur = currentDur || getSlideDuration(slides[currentSlide]);
    startProgress(dur);
    autoTimer = setInterval(() => {
        if (isUserPaused) return;
        const next = (currentSlide+1) % slides.length;
        goToSlide(next);
    }, dur);
}

// ─── Realtime Status Checker / Polling TV ─────────────────────────
let currentServerHash = null;
async function checkTvStatus() {
    try {
        const res = await fetch("{{ route('tv.jadwal.status') }}");
        if (!res.ok) return;
        const data = await res.json();

        if (!currentServerHash) {
            currentServerHash = data.hash;
            return;
        }

        // Jika hash berubah (pergantian jam tayang eksklusif, media baru diunggah waka, ticker baru)
        if (data.hash !== currentServerHash) {
            console.log('Perubahan jadwal tayang Smart TV terdeteksi dari server. Memperbarui tampilan...');
            location.reload();
        }
    } catch (e) {
        // Abaikan kendala jaringan sesaat
    }
}
setInterval(checkTvStatus, 20000); // Cek setiap 20 detik

// Init
if (slides[0] && slides[0].classList.contains('slide-media')) {
    document.body.classList.add('media-fullscreen-mode');
}
const nextIdx0 = 1 % slides.length;
const initNextEl = slides[nextIdx0];
document.getElementById('next-kelas').textContent = initNextEl?.dataset?.mediaTitle || (KELAS_DATA[nextIdx0]?.kelas ?? '—');
scheduleAuto();

// Render slide pertama jika PDF
if (slides[0] && slides[0].dataset.fileType === 'pdf') {
    const canvas = slides[0].querySelector('canvas');
    if (canvas && canvas.dataset.pdfUrl) {
        renderPdfToCanvas(canvas.dataset.pdfUrl, canvas);
        canvas.dataset.rendered = "true";
    }
}

// Auto page refresh cadangan setiap 10 menit
setTimeout(() => location.reload(), 10*60*1000);
</script>
</body>
</html>
