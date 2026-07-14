@php
    $popupTitle = $appSetting?->landing_popup_title ?: 'Selamat Datang, Siswa Baru!';
    $popupDescription = $appSetting?->landing_popup_description
        ?: 'Registrasikan data siswa baru melalui layanan SISFO SMK Telkom Lampung. Prosesnya cepat, aman, dan dapat dipantau secara daring.';
    $popupCtaText = $appSetting?->landing_popup_cta_text ?: 'Registrasi Sekarang';
    $popupCtaUrl = $appSetting?->landing_popup_cta_url ?: '/registrasi-siswa-baru';
    $popupFrequency = $appSetting?->landing_popup_frequency ?: 'daily';
    $popupVersion = sha1(implode('|', [$popupTitle, $popupDescription, $popupCtaText, $popupCtaUrl, $popupFrequency]));
@endphp

<div x-data="landingAnnouncementPopup(@js($popupFrequency), @js($popupVersion))" x-init="init()"
    @keydown.escape.window="closePopup()" x-cloak>
    <div x-show="showPopup"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="closePopup()"
        class="fixed inset-0 z-[9998] bg-slate-950/75 backdrop-blur-sm"></div>

    <div x-show="showPopup"
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0 translate-y-6 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-6 scale-95"
        class="pointer-events-none fixed inset-0 z-[9999] flex items-center justify-center p-4"
        role="dialog" aria-modal="true" aria-labelledby="registration-popup-title">
        <div class="pointer-events-auto relative w-full max-w-lg overflow-hidden rounded-lg border border-white/10 bg-slate-950 shadow-2xl shadow-black/60">
            <div class="h-1.5 bg-gradient-to-r from-red-700 via-red-500 to-amber-400"></div>

            <button type="button" @click="closePopup()" aria-label="Tutup popup"
                class="absolute right-4 top-5 flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-slate-400 transition hover:bg-white/10 hover:text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="px-6 pb-7 pt-8 text-center sm:px-9 sm:pb-9">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-lg border border-red-400/30 bg-red-600 text-white shadow-xl shadow-red-950/50">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m7-10a4 4 0 100-8 4 4 0 000 8zm8-4v6m3-3h-6" />
                    </svg>
                </div>

                <p class="mt-6 text-xs font-black uppercase tracking-widest text-red-400">Registrasi Sisfo Siswa Baru</p>
                <h3 style="color: white;" id="registration-popup-title" class="mt-2 font-outfit text-2xl font-black text-white sm:text-3xl">
                    {{ $popupTitle }}
                </h3>
                <p class="mx-auto mt-3 max-w-md text-sm font-medium leading-6 text-slate-400">
                    {{ $popupDescription }}
                </p>

                <div class="mt-7 grid gap-3 sm:grid-cols-[1fr_auto]">
                    <a href="{{ $popupCtaUrl }}" @click="markSeen()"
                        class="btn-primary inline-flex min-h-12 items-center justify-center gap-2 rounded-lg px-6 py-3 text-sm font-black text-white shadow-lg shadow-red-950/40">
                        {{ $popupCtaText }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <button type="button" @click="closePopup()"
                        class="min-h-12 rounded-lg border border-white/10 bg-white/5 px-5 py-3 text-sm font-bold text-slate-300 transition hover:bg-white/10 hover:text-white">
                        Nanti Saja
                    </button>
                </div>

                <p class="mt-5 flex items-center justify-center gap-2 text-[11px] font-semibold text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Data tersimpan aman di sistem resmi sekolah
                </p>
            </div>
        </div>
    </div>
</div>
