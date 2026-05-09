<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Diperbaiki — SMK Telkom</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-deep:    #050b1a;
            --bg-mid:     #0a1628;
            --indigo:     #6366f1;
            --indigo-light: #818cf8;
            --cyan:       #22d3ee;
            --emerald:    #10b981;
            --amber:      #f59e0b;
            --text-main:  #f1f5f9;
            --text-muted: #94a3b8;
            --glass-bg:   rgba(255,255,255,0.05);
            --glass-border: rgba(255,255,255,0.1);
        }

        html, body {
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-deep);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* ─── Starfield background ─── */
        .stars-wrap {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .star {
            position: absolute;
            border-radius: 50%;
            background: white;
            animation: twinkle var(--dur, 3s) ease-in-out infinite;
            animation-delay: var(--delay, 0s);
            opacity: 0.4;
        }
        @keyframes twinkle {
            0%, 100% { opacity: 0.15; transform: scale(1); }
            50%       { opacity: 0.8;  transform: scale(1.4); }
        }

        /* ─── Gradient orbs ─── */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.18;
            pointer-events: none;
            z-index: 0;
            animation: drift 12s ease-in-out infinite alternate;
        }
        .orb-1 { width: 600px; height: 600px; background: var(--indigo); top: -200px; left: -150px; animation-duration: 14s; }
        .orb-2 { width: 500px; height: 500px; background: var(--cyan);   bottom: -150px; right: -100px; animation-duration: 11s; animation-delay: -3s; }
        .orb-3 { width: 350px; height: 350px; background: var(--amber);  top: 40%; left: 55%; animation-duration: 9s; animation-delay: -6s; }
        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, 20px) scale(1.05); }
        }

        /* ─── Layout ─── */
        .page {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            gap: 0;
        }

        /* ─── Header badge ─── */
        .school-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(12px);
            border-radius: 999px;
            padding: 8px 20px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--indigo-light);
            margin-bottom: 2.5rem;
        }
        .school-badge .dot {
            width: 8px; height: 8px;
            background: var(--emerald);
            border-radius: 50%;
            animation: pulse-dot 1.8s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.7); }
            50%       { box-shadow: 0 0 0 6px rgba(16,185,129,0); }
        }

        /* ─── Gear animation ─── */
        .gear-wrap {
            position: relative;
            width: 160px;
            height: 160px;
            margin-bottom: 2.5rem;
        }
        .gear-outer {
            position: absolute;
            inset: 0;
            animation: spin-slow 8s linear infinite;
        }
        .gear-inner {
            position: absolute;
            inset: 30px;
            animation: spin-slow 5s linear infinite reverse;
        }
        .gear-center {
            position: absolute;
            inset: 60px;
            background: var(--glass-bg);
            border: 2px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
        }
        .gear-center svg { width: 28px; height: 28px; color: var(--amber); }
        @keyframes spin-slow { to { transform: rotate(360deg); } }

        .gear-ring {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px dashed;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .gear-ring-1 { border-color: rgba(99,102,241,0.5); }
        .gear-ring-2 { border-color: rgba(34,211,238,0.4); }

        /* Gear teeth */
        .gear-svg { width: 100%; height: 100%; }

        /* ─── Status code ─── */
        .status-code {
            font-size: clamp(5rem, 15vw, 9rem);
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, var(--indigo-light) 0%, var(--cyan) 50%, var(--indigo) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.04em;
            margin-bottom: 0.5rem;
            filter: drop-shadow(0 0 40px rgba(99,102,241,0.4));
        }

        /* ─── Main card ─── */
        .main-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(20px);
            border-radius: 2rem;
            padding: 2.5rem 2rem;
            max-width: 580px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.1);
        }

        .main-title {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            background: linear-gradient(to right, var(--text-main), var(--indigo-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .main-desc {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        /* ─── Progress bar ─── */
        .progress-wrap {
            margin-bottom: 2rem;
        }
        .progress-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }
        .progress-label .pct {
            color: var(--cyan);
            font-weight: 800;
        }
        .progress-track {
            height: 6px;
            background: rgba(255,255,255,0.08);
            border-radius: 999px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--indigo), var(--cyan));
            border-radius: 999px;
            width: 0%;
            animation: fill-bar 4s ease-out 0.5s forwards;
            position: relative;
        }
        .progress-fill::after {
            content: '';
            position: absolute;
            right: 0; top: 0; bottom: 0;
            width: 20px;
            background: white;
            filter: blur(4px);
            opacity: 0.7;
            border-radius: 999px;
        }
        @keyframes fill-bar {
            to { width: 73%; }
        }

        /* ─── Pantun card ─── */
        .pantun-card {
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(34,211,238,0.08));
            border: 1px solid rgba(99,102,241,0.25);
            border-radius: 1.25rem;
            padding: 1.5rem;
            margin-bottom: 1.75rem;
            position: relative;
            overflow: hidden;
            min-height: 130px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .pantun-card::before {
            content: '"';
            position: absolute;
            top: -10px; left: 12px;
            font-size: 6rem;
            font-family: Georgia, serif;
            color: var(--indigo);
            opacity: 0.2;
            line-height: 1;
        }
        .pantun-label {
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--indigo-light);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .pantun-label::before, .pantun-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(99,102,241,0.3);
        }
        .pantun-text {
            font-size: 0.88rem;
            line-height: 1.9;
            color: var(--text-main);
            font-style: italic;
            text-align: center;
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        .pantun-text.fade-out {
            opacity: 0;
            transform: translateY(6px);
        }
        .pantun-text.fade-in {
            opacity: 1;
            transform: translateY(0);
        }
        .pantun-dots {
            display: flex;
            gap: 5px;
            margin-top: 1rem;
        }
        .pantun-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: rgba(99,102,241,0.3);
            cursor: pointer;
            transition: all 0.3s;
        }
        .pantun-dot.active {
            background: var(--indigo-light);
            width: 18px;
            border-radius: 3px;
        }

        /* ─── Info row ─── */
        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.75rem;
        }
        .info-item {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            padding: 0.875rem 1rem;
            text-align: center;
        }
        .info-item .info-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.3rem;
        }
        .info-item .info-val {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-main);
        }
        .info-item .info-val.green  { color: var(--emerald); }
        .info-item .info-val.yellow { color: var(--amber); }

        /* ─── Steps ─── */
        .steps {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.75rem;
            text-align: left;
        }
        .step-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.82rem;
            color: var(--text-muted);
            padding: 0.5rem 0.75rem;
            border-radius: 0.625rem;
            background: rgba(255,255,255,0.02);
        }
        .step-icon {
            width: 22px; height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            flex-shrink: 0;
        }
        .step-icon.done  { background: rgba(16,185,129,0.2); color: var(--emerald); }
        .step-icon.doing { background: rgba(245,158,11,0.2);  color: var(--amber); }
        .step-icon.todo  { background: rgba(255,255,255,0.05); color: var(--text-muted); }
        .step-text.done  { color: var(--emerald); font-weight: 600; }
        .step-text.doing { color: var(--amber);   font-weight: 600; }

        /* ─── Refresh button ─── */
        .btn-refresh {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, var(--indigo), #4f46e5);
            color: white;
            font-size: 0.875rem;
            font-weight: 700;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 8px 24px rgba(99,102,241,0.35);
        }
        .btn-refresh:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(99,102,241,0.5);
        }
        .btn-refresh:active { transform: translateY(0); }
        .btn-refresh svg { transition: transform 0.6s; }
        .btn-refresh:hover svg { transform: rotate(180deg); }

        /* ─── Footer ─── */
        .footer {
            margin-top: 2rem;
            font-size: 0.72rem;
            color: rgba(148,163,184,0.5);
            text-align: center;
        }
        .footer a { color: var(--indigo-light); text-decoration: none; }
        .footer a:hover { text-decoration: underline; }

        /* ─── Live clock ─── */
        #live-clock {
            font-variant-numeric: tabular-nums;
        }

        /* ─── Responsive ─── */
        @media (max-width: 480px) {
            .main-card { padding: 1.75rem 1.25rem; border-radius: 1.5rem; }
            .status-code { font-size: 5.5rem; }
            .info-row { grid-template-columns: 1fr; }
            .gear-wrap { width: 120px; height: 120px; }
        }
    </style>
</head>
<body>

    <!-- Starfield -->
    <div class="stars-wrap" id="stars"></div>

    <!-- Gradient orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page">

        <!-- School badge -->
        <div class="school-badge">
            <span class="dot"></span>
            SMK Telkom — Sistem Kesiswaan
        </div>

        <!-- Animated gears -->
        <div class="gear-wrap">
            <div class="gear-outer">
                <div class="gear-ring gear-ring-1">
                    <svg class="gear-svg" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="38" stroke="rgba(99,102,241,0.35)" stroke-width="2" stroke-dasharray="6 4"/>
                        <!-- Teeth -->
                        <g fill="rgba(99,102,241,0.6)">
                            <rect x="46" y="4"  width="8" height="14" rx="2"/>
                            <rect x="46" y="82" width="8" height="14" rx="2"/>
                            <rect x="4"  y="46" width="14" height="8" rx="2"/>
                            <rect x="82" y="46" width="14" height="8" rx="2"/>
                            <rect x="18" y="14" width="8" height="14" rx="2" transform="rotate(45 22 21)"/>
                            <rect x="62" y="70" width="8" height="14" rx="2" transform="rotate(45 66 77)"/>
                            <rect x="14" y="62" width="14" height="8" rx="2" transform="rotate(45 21 66)"/>
                            <rect x="70" y="18" width="14" height="8" rx="2" transform="rotate(45 77 22)"/>
                        </g>
                    </svg>
                </div>
            </div>
            <div class="gear-inner">
                <div class="gear-ring gear-ring-2">
                    <svg class="gear-svg" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="35" stroke="rgba(34,211,238,0.3)" stroke-width="2" stroke-dasharray="4 3"/>
                        <g fill="rgba(34,211,238,0.5)">
                            <rect x="46" y="6"  width="8" height="12" rx="2"/>
                            <rect x="46" y="82" width="8" height="12" rx="2"/>
                            <rect x="6"  y="46" width="12" height="8" rx="2"/>
                            <rect x="82" y="46" width="12" height="8" rx="2"/>
                            <rect x="20" y="16" width="8" height="12" rx="2" transform="rotate(45 24 22)"/>
                            <rect x="60" y="68" width="8" height="12" rx="2" transform="rotate(45 64 74)"/>
                            <rect x="16" y="60" width="12" height="8" rx="2" transform="rotate(45 22 64)"/>
                            <rect x="68" y="20" width="12" height="8" rx="2" transform="rotate(45 74 24)"/>
                        </g>
                    </svg>
                </div>
            </div>
            <div class="gear-center">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>

        <!-- Status code -->
        <div class="status-code">503</div>

        <!-- Main card -->
        <div class="main-card">

            <h1 class="main-title">Sistem Sedang Dalam Perbaikan</h1>
            <p class="main-desc">
                Tim teknis kami sedang bekerja keras memperbarui sistem agar lebih
                cepat, aman, dan nyaman untuk Anda. Mohon bersabar sebentar ya! 🚀
            </p>

            <!-- Progress -->
            <div class="progress-wrap">
                <div class="progress-label">
                    <span>Progress Perbaikan</span>
                    <span class="pct" id="pct-display">0%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
            </div>

            <!-- Pantun carousel -->
            <div class="pantun-card">
                <div class="pantun-label">✦ Pantun Sabar Menunggu ✦</div>
                <p class="pantun-text fade-in" id="pantun-text"></p>
                <div class="pantun-dots" id="pantun-dots"></div>
            </div>

            <!-- Info row -->
            <div class="info-row">
                <div class="info-item">
                    <div class="info-label">Status Tim</div>
                    <div class="info-val green">● Aktif Bekerja</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Waktu Sekarang</div>
                    <div class="info-val yellow" id="live-clock">--:--:--</div>
                </div>
            </div>

            <!-- Steps -->
            <div class="steps">
                <div class="step-item">
                    <div class="step-icon done">✓</div>
                    <span class="step-text done">Backup data selesai</span>
                </div>
                <div class="step-item">
                    <div class="step-icon done">✓</div>
                    <span class="step-text done">Update dependensi selesai</span>
                </div>
                <div class="step-item">
                    <div class="step-icon doing">⟳</div>
                    <span class="step-text doing">Pengujian sistem (sedang berjalan...)</span>
                </div>
                <div class="step-item">
                    <div class="step-icon todo">○</div>
                    <span class="step-text">Deployment ke production</span>
                </div>
            </div>

            <!-- Refresh -->
            <button class="btn-refresh" onclick="window.location.reload()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Coba Lagi Sekarang
            </button>

        </div><!-- /.main-card -->

        <div class="footer">
            &copy; {{ date('Y') }} SMK Telkom &mdash; Sistem Informasi Kesiswaan &nbsp;|&nbsp;
            Butuh bantuan? Hubungi <a href="mailto:admin@smktelkom.sch.id">admin@smktelkom.sch.id</a>
        </div>

    </div><!-- /.page -->

    <script>
    // ─── Starfield ───────────────────────────────────────────────────
    (function () {
        const wrap = document.getElementById('stars');
        for (let i = 0; i < 120; i++) {
            const s = document.createElement('div');
            s.className = 'star';
            const size = Math.random() * 2.5 + 0.5;
            s.style.cssText = `
                width:${size}px; height:${size}px;
                top:${Math.random()*100}%;
                left:${Math.random()*100}%;
                --dur:${(Math.random()*4+2).toFixed(1)}s;
                --delay:-${(Math.random()*6).toFixed(1)}s;
            `;
            wrap.appendChild(s);
        }
    })();

    // ─── Live clock ───────────────────────────────────────────────────
    function updateClock() {
        const now  = new Date();
        const pad  = n => String(n).padStart(2, '0');
        document.getElementById('live-clock').textContent =
            pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ─── Progress animation ───────────────────────────────────────────
    (function () {
        const fill    = document.getElementById('progress-fill');
        const display = document.getElementById('pct-display');
        let current   = 0;
        const target  = 73;
        const step    = () => {
            if (current >= target) return;
            current = Math.min(current + 0.5, target);
            display.textContent = Math.round(current) + '%';
            requestAnimationFrame(step);
        };
        setTimeout(step, 500);
    })();

    // ─── Pantun carousel ─────────────────────────────────────────────
    (function () {
        const pantunList = [
            [
                "Pergi ke pasar membeli mangga,",
                "Jangan lupa beli pisang kepok.",
                "Sistem kami sedang diperbaiki,",
                "Sebentar lagi siap untuk dipakai, kok! 😄"
            ],
            [
                "Bunga melati harum semerbak,",
                "Ditanam indah di kebun belakang.",
                "Tim kami kerja tanpa lelah,",
                "Sistem terbaik segera kami hadirkan! 💪"
            ],
            [
                "Ke pasar beli tempe tahu,",
                "Pulang ke rumah naik becak.",
                "Terima kasih sudah mau menunggu,",
                "Ini tanda kamu setia dan tidak galak! 🙏"
            ],
            [
                "Kalau ada sumur di ladang,",
                "Boleh kita menumpang mandi.",
                "Kalau sistem masih lagi berdendang,",
                "Boleh kita nonton TikTok dulu, kali! 📱"
            ],
            [
                "Menanam padi di sawah ladang,",
                "Tak lupa pupuk agar subur.",
                "Kami kerja keras tiada gelisah,",
                "Sistem baru sebentar lagi hadir dan subur! 🌾"
            ],
            [
                "Hujan deras membasahi bumi,",
                "Pelangi indah muncul kemudian.",
                "Sabar sebentar, percayai kami,",
                "Sistem lebih keren dari sebelumnya, dijamin! 🌈"
            ],
            [
                "Berlayar jauh ke pulau seberang,",
                "Angin sepoi-sepoi terasa segar.",
                "Jangan khawatir, kami tidak hilang,",
                "Lagi upgrade biar makin canggih dan kekar! 🚀"
            ],
        ];

        const textEl = document.getElementById('pantun-text');
        const dotsEl = document.getElementById('pantun-dots');
        let current  = 0;

        // Build dots
        pantunList.forEach((_, i) => {
            const d = document.createElement('div');
            d.className = 'pantun-dot' + (i === 0 ? ' active' : '');
            d.addEventListener('click', () => goTo(i));
            dotsEl.appendChild(d);
        });

        function renderPantun(idx) {
            return pantunList[idx].join('<br>');
        }

        function updateDots(idx) {
            dotsEl.querySelectorAll('.pantun-dot').forEach((d, i) => {
                d.classList.toggle('active', i === idx);
            });
        }

        function goTo(idx) {
            textEl.classList.add('fade-out');
            setTimeout(() => {
                current = idx;
                textEl.innerHTML = renderPantun(current);
                updateDots(current);
                textEl.classList.remove('fade-out');
            }, 500);
        }

        // Initial render
        textEl.innerHTML = renderPantun(0);

        // Auto-rotate every 5 seconds
        setInterval(() => {
            const next = (current + 1) % pantunList.length;
            goTo(next);
        }, 5000);
    })();
    </script>
</body>
</html>
