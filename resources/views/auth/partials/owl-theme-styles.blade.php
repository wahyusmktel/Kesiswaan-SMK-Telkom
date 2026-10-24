<style>
    body.login-owl { min-height: 100svh; background: #f5f5f7; color: #24272b; }
    .login-owl .login-backdrop { position: fixed; inset: 0; width: 56%; height: 100%; object-fit: cover; object-position: center; pointer-events: none; }
    .login-owl .login-shell { position: relative; min-height: 100svh; height: auto; justify-content: flex-end; background: linear-gradient(90deg, transparent 38%, rgba(245,245,247,.55) 58%, rgba(245,245,247,.96) 80%); }
    .login-owl .login-form-panel { width: 46%; min-height: 100svh; padding: 22px clamp(20px, 3vw, 56px); }
    .login-owl .login-card { max-width: 430px; padding: 22px 28px; border: 1px solid rgba(255,255,255,.95); border-radius: 24px; background: rgba(255,255,255,.93); box-shadow: 0 24px 80px -24px rgba(76,30,34,.18), 0 1px 2px rgba(0,0,0,.04); backdrop-filter: blur(20px); }
    .login-owl .login-brand { display: flex; justify-content: center; margin: -6px auto 4px; }
    .login-owl .login-brand img { width: 76px; height: 76px; object-fit: contain; }
    .login-owl .login-eyebrow { margin: 0 0 10px; color: #bd1c2c; font-size: 8px; font-weight: 800; letter-spacing: .2em; text-align: center; }
    .login-owl .login-heading { margin-bottom: 16px; }
    .login-owl .login-heading h2 { font-size: 25px; line-height: 1.2; letter-spacing: -.04em; }
    .login-owl .login-heading p { font-size: 12px; line-height: 1.7; max-width: 300px; margin: 0 auto; color: #71717a; }
    .login-owl .input-label { font-size: 12px; font-weight: 700; color: #3f3f46; }
    .login-owl .form-input { border: 1px solid #e4e4e7; border-radius: 10px; padding-top: 10px; padding-bottom: 10px; font-size: 14px; box-shadow: none; }
    .login-owl .form-input:focus { border-color: #dc2638; outline: 3px solid rgba(220,38,56,.1); }
    .login-owl form.space-y-6 > :not([hidden]) ~ :not([hidden]) { margin-top: 12px; }
    .login-owl .btn-primary { border-radius: 12px; min-height: 46px; background: linear-gradient(115deg,#ee2437,#bf1125); box-shadow: 0 7px 16px -8px rgba(207,20,43,.6); }
    .login-owl .login-card > .my-6 { margin-top: 12px; margin-bottom: 12px; }
    .login-owl .login-methods { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
    .login-owl .login-card > a:not(.login-brand), .login-owl .login-methods > a, .login-owl #btn-face-login { min-height: 42px; margin: 0; gap: 6px; padding: 9px 6px; border: 1px solid #e4e4e7; border-radius: 10px; background: #fff; color: #41414a; font-size: 11px; font-weight: 600; box-shadow: none; transform: none; }
    .login-owl .login-methods svg { width: 16px; height: 16px; flex-shrink: 0; }
    .login-owl .login-methods > a:hover, .login-owl #btn-face-login:hover { border-color: #f3a7af; background: #fff5f6; color: #b51b2b; }
    .login-owl .login-card > a:last-child { min-height: 32px; margin-top: 12px; border: 0; border-top: 1px solid #f0f0f2; border-radius: 0; padding-bottom: 0; color: #bd1c2c; }
    .login-owl .login-footer { margin-top: 12px; font-size: 10px; line-height: 1.7; text-align: center; color: #73737e; }
    .login-owl .login-footer span { color: #92929b; }
    .login-owl-focus .login-backdrop, .login-owl-panorama .login-backdrop { width: 100%; object-fit: cover; object-position: center 52%; }
    .login-owl-focus .login-shell { justify-content: center; background: rgba(248,248,250,.48); }
    .login-owl-focus .login-form-panel { width: 100%; }
    .login-owl-focus .login-card { background: rgba(255,255,255,.93); box-shadow: 0 32px 100px -28px rgba(87,21,32,.32); }
    .login-owl-panorama .login-shell { justify-content: flex-start; background: linear-gradient(90deg,rgba(248,248,250,.95),rgba(248,248,250,.85) 32%,rgba(248,248,250,.12) 68%); }
    .login-owl-panorama .login-form-panel { width: 46%; }
    @media (max-width: 1023px) { .login-owl .login-form-panel { width: 56%; padding: 20px; } .login-owl .login-card { padding: 20px 24px; } .login-owl-focus .login-form-panel { width: 100%; } }
    @media (max-width: 700px) {
        .login-owl .login-backdrop { width: 100%; object-fit: cover; object-position: center; opacity: .55; }
        .login-owl .login-shell { background: rgba(245,245,247,.34); }
        .login-owl .login-form-panel { width: 100%; padding: 18px 14px; }
        .login-owl .login-card { padding: 20px; max-width: 420px; background: rgba(255,255,255,.95); border-radius: 20px; }
        .login-owl .login-heading h2 { font-size: 24px; }
    }
    @media (max-height: 740px) {
        .login-owl .login-form-panel { padding-top: 14px; padding-bottom: 14px; }
        .login-owl .login-card { padding-top: 12px; padding-bottom: 14px; }
        .login-owl .login-brand img { width: 52px; height: 52px; }
        .login-owl .login-eyebrow { display: none; }
        .login-owl .login-heading { margin-bottom: 8px; }
        .login-owl form.space-y-6 > :not([hidden]) ~ :not([hidden]) { margin-top: 10px; }
        .login-owl .login-card > .my-6 { margin-top: 8px; margin-bottom: 8px; }
        .login-owl .login-card > a:last-child { margin-top: 8px; }
        .login-owl .login-footer { margin-top: 8px; }
        .login-owl .login-footer span, .login-owl .login-footer br { display: none; }
    }
    @media (prefers-reduced-motion: reduce) { .login-owl .animate-fade-in-up { animation: none; } }
</style>
