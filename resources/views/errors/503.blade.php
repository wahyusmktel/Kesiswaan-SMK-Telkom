<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#b91c1c">
    <title>SISFO Sedang Maintenance — SMK Telkom Lampung</title>
    <style>
        :root { color-scheme:light; --red:#c91c24; --red-dark:#97151b; --ink:#172033; --muted:#64748b; --line:#e8edf4; }
        * { box-sizing:border-box; }
        html,body { min-height:100%; }
        body { margin:0; background:#f5f7fb; color:var(--ink); font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif; }
        body::before { content:""; position:fixed; inset:0; pointer-events:none; background:radial-gradient(circle at 12% 15%,rgba(201,28,36,.1),transparent 29rem),radial-gradient(circle at 88% 84%,rgba(15,23,42,.08),transparent 32rem); }
        .page { position:relative; min-height:100vh; display:grid; grid-template-rows:auto 1fr auto; }
        .topbar { height:7px; background:linear-gradient(90deg,var(--red-dark),#ed1b2f 58%,#ff5967); }
        main { width:min(1120px,calc(100% - 32px)); margin:auto; padding:38px 0; }
        .shell { display:grid; grid-template-columns:minmax(0,1.08fr) minmax(320px,.92fr); overflow:hidden; border:1px solid rgba(226,232,240,.9); border-radius:30px; background:rgba(255,255,255,.94); box-shadow:0 30px 80px rgba(15,23,42,.12); }
        .content { padding:clamp(34px,6vw,72px); }
        .brand { display:flex; align-items:center; gap:15px; margin-bottom:54px; }
        .brand-mark { display:grid; width:58px; height:58px; place-items:center; border-radius:17px; background:linear-gradient(145deg,#d9202b,var(--red-dark)); box-shadow:0 12px 25px rgba(185,28,28,.24); }
        .brand-mark img { width:47px; height:47px; object-fit:contain; }
        .brand-name { font-size:14px; font-weight:850; letter-spacing:.08em; text-transform:uppercase; }
        .brand-sub { margin-top:4px; color:var(--muted); font-size:12px; font-weight:600; }
        .eyebrow { display:inline-flex; align-items:center; gap:9px; margin-bottom:18px; border:1px solid #fecdd3; border-radius:999px; padding:7px 12px; background:#fff1f2; color:#a4111b; font-size:12px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; }
        .pulse { width:8px; height:8px; border-radius:50%; background:#dc2626; box-shadow:0 0 0 5px rgba(220,38,38,.12); animation:pulse 2s infinite; }
        h1 { max-width:620px; margin:0; font-size:clamp(38px,5.2vw,68px); line-height:1.04; letter-spacing:-.045em; }
        h1 span { color:var(--red); }
        .lead { max-width:620px; margin:24px 0 30px; color:var(--muted); font-size:clamp(15px,1.7vw,18px); line-height:1.75; }
        .notice { display:flex; align-items:flex-start; gap:13px; max-width:620px; padding:16px 18px; border:1px solid var(--line); border-radius:16px; background:#f8fafc; }
        .notice svg { flex:0 0 auto; color:var(--red); }
        .notice strong { display:block; margin-bottom:3px; font-size:13px; }
        .notice p { margin:0; color:var(--muted); font-size:12px; line-height:1.55; }
        .actions { display:flex; flex-wrap:wrap; align-items:center; gap:15px; margin-top:28px; }
        button { display:inline-flex; align-items:center; gap:9px; border:0; border-radius:13px; padding:13px 19px; background:var(--red); color:#fff; font:inherit; font-size:14px; font-weight:800; cursor:pointer; box-shadow:0 10px 20px rgba(185,28,28,.2); transition:.2s ease; }
        button:hover { transform:translateY(-2px); background:var(--red-dark); }
        button svg { width:17px; }
        .retry { color:var(--muted); font-size:12px; }
        .visual { position:relative; display:grid; min-height:600px; place-items:center; overflow:hidden; background:linear-gradient(145deg,#c91c24,#8f1118); }
        .visual::before,.visual::after { content:""; position:absolute; border:1px solid rgba(255,255,255,.13); border-radius:50%; }
        .visual::before { width:470px; height:470px; right:-180px; top:-145px; }
        .visual::after { width:330px; height:330px; left:-130px; bottom:-120px; }
        .art { position:relative; z-index:1; width:min(360px,78%); color:#fff; text-align:center; }
        .code { color:rgba(255,255,255,.18); font-size:clamp(110px,17vw,190px); font-weight:950; line-height:.8; letter-spacing:-.09em; }
        .gearbox { position:relative; width:190px; height:150px; margin:-18px auto 28px; }
        .gear { position:absolute; display:grid; place-items:center; border:4px dashed rgba(255,255,255,.88); border-radius:50%; animation:spin 12s linear infinite; }
        .gear::after { content:""; width:31%; height:31%; border:4px solid rgba(255,255,255,.88); border-radius:50%; }
        .gear.one { width:112px; height:112px; left:20px; bottom:0; }
        .gear.two { width:78px; height:78px; right:14px; top:5px; animation-direction:reverse; animation-duration:8s; }
        .visual h2 { margin:0 0 10px; font-size:24px; }
        .visual p { margin:0; color:rgba(255,255,255,.76); font-size:13px; line-height:1.65; }
        footer { padding:19px 24px 25px; color:#94a3b8; font-size:11px; text-align:center; }
        @keyframes spin { to { transform:rotate(360deg); } }
        @keyframes pulse { 50% { box-shadow:0 0 0 9px rgba(220,38,38,0); } }
        @media (prefers-reduced-motion:reduce) { *,*::before,*::after { animation:none!important; transition:none!important; } }
        @media (max-width:820px) { main { padding:24px 0; } .shell { grid-template-columns:1fr; border-radius:24px; } .brand { margin-bottom:38px; } .visual { min-height:330px; grid-row:1; } .code { font-size:115px; } .gearbox { width:150px; height:110px; margin:-15px auto 20px; } .gear.one { width:86px;height:86px; } .gear.two { width:58px;height:58px; } }
        @media (max-width:480px) { main { width:min(100% - 20px,1120px); } .content { padding:30px 24px 34px; } .visual { min-height:285px; } .brand-mark { width:52px;height:52px; } .brand-mark img { width:42px;height:42px; } h1 { font-size:38px; } }
    </style>
</head>
<body>
    <div class="page">
        <div class="topbar"></div>
        <main>
            <section class="shell" aria-labelledby="maintenance-title">
                <div class="content">
                    <div class="brand">
                        <div class="brand-mark"><img src="/images/asset-report/smk-telkom-lampung-white.png" alt="Logo SMK Telkom Lampung"></div>
                        <div><div class="brand-name">SISFO</div><div class="brand-sub">SMK Telkom Lampung</div></div>
                    </div>
                    <div class="eyebrow"><span class="pulse"></span> Pemeliharaan sistem</div>
                    <h1 id="maintenance-title">Kami sedang membuat SISFO <span>lebih baik.</span></h1>
                    <p class="lead">SISFO sedang menjalani pembaruan terjadwal. Selama proses ini beberapa layanan tidak dapat diakses untuk sementara. Data Anda tetap aman dan layanan akan segera kembali.</p>
                    <div class="notice">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z"/><path d="M12 8v4m0 4h.01"/></svg>
                        <div><strong>Tidak perlu melakukan tindakan apa pun</strong><p>Tunggu beberapa saat, lalu muat ulang halaman. Jika pemeliharaan selesai, Anda akan dapat masuk kembali seperti biasa.</p></div>
                    </div>
                    <div class="actions">
                        <button type="button" onclick="window.location.reload()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 11a8.1 8.1 0 0 0-15.5-2M4 4v5h5m-5 4a8.1 8.1 0 0 0 15.5 2M20 20v-5h-5"/></svg>
                            Coba lagi
                        </button>
                        <span class="retry">Pemeriksaan otomatis dalam <strong id="countdown">30</strong> detik</span>
                    </div>
                </div>
                <div class="visual" aria-hidden="true">
                    <div class="art">
                        <div class="code">503</div>
                        <div class="gearbox"><div class="gear one"></div><div class="gear two"></div></div>
                        <h2>Sedang dalam pemeliharaan</h2>
                        <p>Tim teknis sedang memastikan setiap layanan kembali berjalan dengan stabil dan aman.</p>
                    </div>
                </div>
            </section>
        </main>
        <footer>&copy; {{ date('Y') }} SMK Telkom Lampung · Sistem Informasi Sekolah</footer>
    </div>
    <script>
        (() => {
            let remaining = 30;
            const countdown = document.getElementById('countdown');
            window.setInterval(() => {
                remaining -= 1;
                countdown.textContent = remaining;
                if (remaining <= 0) window.location.reload();
            }, 1000);
        })();
    </script>
</body>
</html>
