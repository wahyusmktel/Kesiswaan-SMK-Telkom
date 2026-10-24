<style>
    body.login-owl { min-height: 100svh; background: #f5f5f7; color: #24272b; }
    .login-owl .login-backdrop { position: fixed; inset: 0; width: 100%; height: 100%; object-fit: contain; object-position: left center; pointer-events: none; }
    .login-owl .login-shell { position: relative; min-height: 100svh; height: auto; justify-content: flex-end; background: linear-gradient(90deg, transparent 38%, rgba(245,245,247,.55) 58%, rgba(245,245,247,.96) 80%); }
    .login-owl .login-form-panel { width: 48%; min-height: 100svh; padding: 38px clamp(24px, 4vw, 76px); }
    .login-owl .login-card { max-width: 450px; padding: 30px 34px; border: 1px solid rgba(255,255,255,.95); border-radius: 28px; background: rgba(255,255,255,.89); box-shadow: 0 24px 80px -24px rgba(76,30,34,.18), 0 1px 2px rgba(0,0,0,.04); backdrop-filter: blur(20px); }
    .login-owl .login-brand { display: flex; justify-content: center; margin: -12px auto 4px; }
    .login-owl .login-brand img { width: 128px; height: 128px; object-fit: contain; }
    .login-owl .login-eyebrow { margin: 4px 0 12px; color: #bd1c2c; font-size: 9px; font-weight: 800; letter-spacing: .22em; text-align: center; }
    .login-owl .login-heading { margin-bottom: 24px; }
    .login-owl .login-heading h2 { font-size: 27px; line-height: 1.2; letter-spacing: -.04em; }
    .login-owl .login-heading p { font-size: 12px; line-height: 1.7; max-width: 300px; margin: 0 auto; color: #71717a; }
    .login-owl .input-label { font-size: 12px; font-weight: 700; color: #3f3f46; }
    .login-owl .form-input { border: 1px solid #e4e4e7; border-radius: 12px; padding-top: 12px; padding-bottom: 12px; font-size: 14px; box-shadow: none; }
    .login-owl .form-input:focus { border-color: #dc2638; outline: 3px solid rgba(220,38,56,.1); }
    .login-owl form.space-y-6 > :not([hidden]) ~ :not([hidden]) { margin-top: 18px; }
    .login-owl .btn-primary { border-radius: 12px; min-height: 46px; background: linear-gradient(115deg,#ee2437,#bf1125); box-shadow: 0 7px 16px -8px rgba(207,20,43,.6); }
    .login-owl .login-card > .my-6 { margin-top: 18px; margin-bottom: 18px; }
    .login-owl .login-card > a:not(.login-brand), .login-owl #btn-face-login { min-height: 42px; margin-bottom: 9px; padding: 10px 12px; border: 1px solid #e4e4e7; border-radius: 12px; background: #fff; color: #41414a; font-size: 12px; font-weight: 600; box-shadow: none; transform: none; }
    .login-owl .login-card > a:not(.login-brand):hover, .login-owl #btn-face-login:hover { border-color: #f3a7af; background: #fff5f6; color: #b51b2b; }
    .login-owl .login-card > a:last-child { margin-top: 16px; border: 0; border-top: 1px solid #f0f0f2; border-radius: 0; padding-bottom: 0; color: #bd1c2c; }
    .login-owl .login-footer { margin-top: 22px; font-size: 10px; line-height: 1.9; text-align: center; color: #73737e; }
    .login-owl .login-footer span { color: #92929b; }
    @media (max-width: 1023px) { .login-owl .login-form-panel { width: 56%; padding: 32px 22px; } .login-owl .login-card { padding: 26px; } }
    @media (max-width: 700px) {
        .login-owl .login-backdrop { object-fit: cover; object-position: center; opacity: .35; }
        .login-owl .login-shell { background: rgba(245,245,247,.34); }
        .login-owl .login-form-panel { width: 100%; padding: 24px 18px; }
        .login-owl .login-card { padding: 24px; max-width: 420px; background: rgba(255,255,255,.94); border-radius: 24px; }
        .login-owl .login-brand img { height: 110px; width: 110px; }
        .login-owl .login-heading h2 { font-size: 25px; }
    }
    @media (prefers-reduced-motion: reduce) { .login-owl .animate-fade-in-up { animation: none; } }
</style>
