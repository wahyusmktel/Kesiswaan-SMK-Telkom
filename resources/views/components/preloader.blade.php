{{-- ============================================================ --}}
{{-- PREMIUM TECH PRELOADER (Reusable Component)               --}}
{{-- Usage: @include('components.preloader')                    --}}
{{-- ============================================================ --}}

<style>
    /* ===== Preloader Core ===== */
    .preloader-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: #060918;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.6s;
        overflow: hidden;
    }

    .preloader-overlay.hide {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    /* ===== Grid Background ===== */
    .preloader-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
        background-size: 60px 60px;
        animation: gridPulse 4s ease-in-out infinite;
    }

    @keyframes gridPulse {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 0.7; }
    }

    /* ===== Scanning Line ===== */
    .scan-line {
        position: absolute;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
        animation: scanDown 2.5s ease-in-out infinite;
        z-index: 1;
    }

    @keyframes scanDown {
        0% { top: -2px; opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { top: 100%; opacity: 0; }
    }

    /* ===== Corner Tech Brackets ===== */
    .tech-corner {
        position: absolute;
        width: 20px;
        height: 20px;
        z-index: 2;
    }
    .tech-corner::before, .tech-corner::after {
        content: '';
        position: absolute;
        background: rgba(255, 255, 255, 0.2);
    }
    .tech-corner.tl { top: -30px; left: -30px; }
    .tech-corner.tl::before { top: 0; left: 0; width: 20px; height: 2px; }
    .tech-corner.tl::after { top: 0; left: 0; width: 2px; height: 20px; }
    .tech-corner.tr { top: -30px; right: -30px; }
    .tech-corner.tr::before { top: 0; right: 0; width: 20px; height: 2px; }
    .tech-corner.tr::after { top: 0; right: 0; width: 2px; height: 20px; }
    .tech-corner.bl { bottom: -30px; left: -30px; }
    .tech-corner.bl::before { bottom: 0; left: 0; width: 20px; height: 2px; }
    .tech-corner.bl::after { bottom: 0; left: 0; width: 2px; height: 20px; }
    .tech-corner.br { bottom: -30px; right: -30px; }
    .tech-corner.br::before { bottom: 0; right: 0; width: 20px; height: 2px; }
    .tech-corner.br::after { bottom: 0; right: 0; width: 2px; height: 20px; }

    /* ===== Logo Container ===== */
    .preloader-logo-wrap {
        position: relative;
        width: 140px;
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
    }

    /* Orbiting Rings */
    .orbit-ring {
        position: absolute;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 50%;
    }
    .orbit-ring-1 {
        width: 180px; height: 180px;
        animation: orbitSpin 8s linear infinite;
    }
    .orbit-ring-2 {
        width: 220px; height: 220px;
        animation: orbitSpin 12s linear infinite reverse;
        border-style: dashed;
        border-color: rgba(255, 255, 255, 0.04);
    }
    .orbit-ring-3 {
        width: 260px; height: 260px;
        animation: orbitSpin 16s linear infinite;
        border-color: rgba(255, 255, 255, 0.03);
    }

    /* Orbit Dots */
    .orbit-ring::after {
        content: '';
        position: absolute;
        width: 6px;
        height: 6px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        top: -3px;
        left: 50%;
        transform: translateX(-50%);
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
    }
    .orbit-ring-2::after {
        width: 4px;
        height: 4px;
        top: auto;
        bottom: -2px;
        background: rgba(255, 255, 255, 0.3);
    }
    .orbit-ring-3::after {
        width: 3px;
        height: 3px;
        left: -1.5px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.2);
    }

    @keyframes orbitSpin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Logo Glow */
    .logo-glow {
        position: absolute;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
        animation: glowPulse 2s ease-in-out infinite;
    }

    @keyframes glowPulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.6); opacity: 1; }
    }

    /* Logo Image (Pure White) */
    .preloader-logo-img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        filter: brightness(0) invert(1);
        position: relative;
        z-index: 3;
        animation: logoFloat 3s ease-in-out infinite;
    }

    @keyframes logoFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-6px); }
    }

    /* ===== Progress Section ===== */
    .preloader-progress-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        position: relative;
        z-index: 3;
    }

    /* Progress Bar */
    .progress-track {
        width: 200px;
        height: 3px;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }

    .progress-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.1));
        border-radius: 4px;
        transition: width 0.15s linear;
        position: relative;
    }

    .progress-fill::after {
        content: '';
        position: absolute;
        right: 0;
        top: -2px;
        width: 7px;
        height: 7px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 0 12px 3px rgba(255, 255, 255, 0.6);
    }

    /* Percentage Counter */
    .progress-counter {
        font-family: 'Outfit', 'Courier New', monospace;
        font-size: 0.7rem;
        font-weight: 800;
        color: rgba(255, 255, 255, 0.35);
        letter-spacing: 0.3em;
        text-transform: uppercase;
    }

    .progress-counter .pct-value {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.85rem;
        font-variant-numeric: tabular-nums;
    }

    /* Status Text */
    .preloader-status {
        font-family: 'Outfit', sans-serif;
        font-size: 0.65rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.2);
        letter-spacing: 0.25em;
        text-transform: uppercase;
        animation: statusBlink 1.5s ease-in-out infinite;
    }

    @keyframes statusBlink {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 0.8; }
    }

    /* ===== Floating Particles ===== */
    .preloader-particle {
        position: absolute;
        width: 2px;
        height: 2px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        animation: particleDrift linear infinite;
    }

    @keyframes particleDrift {
        0% { transform: translateY(100vh) translateX(0); opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { transform: translateY(-20px) translateX(30px); opacity: 0; }
    }

    /* ===== Data Stream Lines ===== */
    .data-stream {
        position: absolute;
        width: 1px;
        background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: streamFlow linear infinite;
    }

    @keyframes streamFlow {
        0% { transform: translateY(-100%); }
        100% { transform: translateY(100vh); }
    }

    /* ===== Hexagon Background Pattern ===== */
    .hex-pattern {
        position: absolute;
        inset: 0;
        opacity: 0.03;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='49' viewBox='0 0 28 49'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        animation: hexShift 20s linear infinite;
    }

    @keyframes hexShift {
        0% { transform: translateY(0); }
        100% { transform: translateY(49px); }
    }
</style>

{{-- Preloader HTML --}}
<div id="preloader" class="preloader-overlay">
    {{-- Background Effects --}}
    <div class="preloader-grid"></div>
    <div class="hex-pattern"></div>
    <div class="scan-line"></div>

    {{-- Floating Particles --}}
    @for ($i = 0; $i < 20; $i++)
        <div class="preloader-particle"
            style="left: {{ rand(5, 95) }}%;
                   animation-duration: {{ rand(40, 80) / 10 }}s;
                   animation-delay: {{ rand(0, 40) / 10 }}s;
                   width: {{ rand(1, 3) }}px;
                   height: {{ rand(1, 3) }}px;">
        </div>
    @endfor

    {{-- Data Stream Lines --}}
    @for ($i = 0; $i < 6; $i++)
        <div class="data-stream"
            style="left: {{ rand(5, 95) }}%;
                   height: {{ rand(60, 150) }}px;
                   animation-duration: {{ rand(30, 60) / 10 }}s;
                   animation-delay: {{ rand(0, 30) / 10 }}s;
                   opacity: {{ rand(2, 6) / 10 }};">
        </div>
    @endfor

    {{-- Logo Section --}}
    <div class="preloader-logo-wrap">
        {{-- Corner Brackets --}}
        <div class="tech-corner tl"></div>
        <div class="tech-corner tr"></div>
        <div class="tech-corner bl"></div>
        <div class="tech-corner br"></div>

        {{-- Orbiting Rings --}}
        <div class="orbit-ring orbit-ring-1"></div>
        <div class="orbit-ring orbit-ring-2"></div>
        <div class="orbit-ring orbit-ring-3"></div>

        {{-- Glow --}}
        <div class="logo-glow"></div>

        {{-- Logo Image --}}
        <img src="{{ asset('loader.png') }}" alt="Loading..." class="preloader-logo-img">
    </div>

    {{-- Progress --}}
    <div class="preloader-progress-section">
        <div class="progress-track">
            <div class="progress-fill" id="preloaderFill"></div>
        </div>
        <div class="progress-counter">
            <span class="pct-value" id="preloaderPct">0</span>%
        </div>
        <div class="preloader-status" id="preloaderStatus">Initializing System</div>
    </div>
</div>

<script>
    (function() {
        const fill = document.getElementById('preloaderFill');
        const pct = document.getElementById('preloaderPct');
        const status = document.getElementById('preloaderStatus');
        const overlay = document.getElementById('preloader');

        const statuses = [
            'Initializing System',
            'Loading Resources',
            'Connecting Modules',
            'Rendering Interface',
            'Preparing Experience',
            'Almost Ready'
        ];

        let progress = 0;
        let step = 0;
        const totalSteps = 100;
        const interval = 25; // ~2.5s total

        function tick() {
            if (progress >= totalSteps) {
                fill.style.width = '100%';
                pct.textContent = '100';
                status.textContent = 'Welcome';
                status.style.color = 'rgba(255,255,255,0.6)';
                status.style.animation = 'none';
                setTimeout(() => {
                    overlay.classList.add('hide');
                    document.body.style.overflow = '';
                    setTimeout(() => overlay.remove(), 700);
                }, 300);
                return;
            }

            // Non-linear speed: fast start, slow middle, fast end
            let increment;
            if (progress < 30) {
                increment = Math.random() * 4 + 2;
            } else if (progress < 70) {
                increment = Math.random() * 2 + 0.5;
            } else {
                increment = Math.random() * 5 + 3;
            }

            progress = Math.min(progress + increment, totalSteps);
            const rounded = Math.floor(progress);

            fill.style.width = rounded + '%';
            pct.textContent = rounded;

            // Update status text at thresholds
            const statusIdx = Math.min(Math.floor(rounded / 18), statuses.length - 1);
            status.textContent = statuses[statusIdx];

            setTimeout(tick, interval + Math.random() * 40);
        }

        // Prevent scroll during load
        document.body.style.overflow = 'hidden';
        tick();
    })();
</script>
