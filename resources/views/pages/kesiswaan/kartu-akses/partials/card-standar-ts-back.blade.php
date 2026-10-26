<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,700&display=swap');

    .card-standar-ts-back {
        position: relative;
        width: 85.6mm;
        height: 54mm;
        background: #ffffff;
        box-sizing: border-box;
        overflow: hidden;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
        display: flex;
        flex-direction: column;
        border: 1px solid #e2e8f0;
    }

    .card-standar-ts-back * {
        box-sizing: border-box;
    }
</style>

<div class="card-standar-ts-back">

    {{-- BAGIAN ATAS: Background Globe Melengkung & Identitas Sekolah --}}
    <div style="position: relative; height: 35.5mm; width: 100%; background: #ffffff; overflow: hidden; display: flex; flex-direction: column; align-items: center; justify-content: space-between; padding: 2.2mm 4mm 1.8mm 4mm;">
        
        {{-- Gambar Globe Melengkung Sebagai Background --}}
        <img src="{{ asset('images/kartu-akses/globe-bg.png') }}" alt="Globe Background"
            style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 78mm; height: auto; opacity: 0.82; pointer-events: none; z-index: 1;">

        {{-- Logo SMK Telkom Lampung Berwarna --}}
        <div style="position: relative; z-index: 5; margin-top: 0.2mm;">
            <img src="{{ asset('images/kartu-akses/smk-telkom-lampung-color.png') }}" alt="SMK Telkom Lampung"
                style="height: 11mm; width: auto; object-fit: contain; display: block;"
                onerror="this.src='{{ asset('images/teaching-module/smk-telkom-lampung.png') }}'">
        </div>

        {{-- Grid Sosial Media: Facebook, Instagram, YouTube, TikTok --}}
        <div style="position: relative; z-index: 5; display: grid; grid-template-columns: auto auto; column-gap: 5mm; row-gap: 1.2mm; align-items: center; justify-content: center; margin-top: 0.4mm;">
            {{-- Facebook --}}
            <div style="display: flex; align-items: center; gap: 1.2mm;">
                <div style="width: 3.4mm; height: 3.4mm; border-radius: 50%; background: #dc2626; display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0;">
                    <svg style="width: 2mm; height: 2mm; fill: currentColor;" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </div>
                <span style="font-size: 4.4pt; font-weight: 700; color: #1e293b; letter-spacing: 0.1px; white-space: nowrap;">
                    SMK Telkom Lampung
                </span>
            </div>

            {{-- Instagram --}}
            <div style="display: flex; align-items: center; gap: 1.2mm;">
                <div style="width: 3.4mm; height: 3.4mm; border-radius: 50%; background: #dc2626; display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0;">
                    <svg style="width: 2mm; height: 2mm; fill: currentColor;" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </div>
                <span style="font-size: 4.4pt; font-weight: 700; color: #1e293b; letter-spacing: 0.1px; white-space: nowrap;">
                    smktelkom.lpg
                </span>
            </div>

            {{-- YouTube --}}
            <div style="display: flex; align-items: center; gap: 1.2mm;">
                <div style="width: 3.4mm; height: 3.4mm; border-radius: 50%; background: #dc2626; display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0;">
                    <svg style="width: 2mm; height: 2mm; fill: currentColor;" viewBox="0 0 24 24">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                </div>
                <span style="font-size: 4.4pt; font-weight: 700; color: #1e293b; letter-spacing: 0.1px; white-space: nowrap;">
                    SMK Telkom Lampung
                </span>
            </div>

            {{-- TikTok --}}
            <div style="display: flex; align-items: center; gap: 1.2mm;">
                <div style="width: 3.4mm; height: 3.4mm; border-radius: 50%; background: #dc2626; display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0;">
                    <svg style="width: 1.9mm; height: 1.9mm; fill: currentColor;" viewBox="0 0 24 24">
                        <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.298-.002.595.042.88.13V9.4a6.33 6.33 0 0 0-1-.08A6.34 6.34 0 0 0 3 15.66a6.34 6.34 0 0 0 10.82 4.49 6.3 6.3 0 0 0 1.91-4.49V8.84a8.21 8.21 0 0 0 4.86 1.58V6.99a4.85 4.85 0 0 1-1-.3z"/>
                    </svg>
                </div>
                <span style="font-size: 4.4pt; font-weight: 700; color: #1e293b; letter-spacing: 0.1px; white-space: nowrap;">
                    smktelkom.lpg
                </span>
            </div>
        </div>

        {{-- Website Pill Badge (Tengah) --}}
        <div style="position: relative; z-index: 5; width: 100%; display: flex; align-items: center; justify-content: center; margin-top: 0.8mm;">
            <div style="background: #dc2626; color: #ffffff; font-size: 5pt; font-weight: 800; padding: 0.8mm 5mm; border-radius: 2.5mm; letter-spacing: 0.4px; box-shadow: 0 1px 3px rgba(220, 38, 38, 0.25);">
                www.smktelkom-lpg.sch.id
            </div>
        </div>

    </div>

    {{-- GARIS PEMISAH MERAH --}}
    <div style="height: 1.2mm; width: 100%; background: #dc2626;"></div>

    {{-- BAGIAN BAWAH: Panel Gelap Berisi Ketentuan & Alamat Pengembalian --}}
    <div style="flex: 1; width: 100%; background: #171717; padding: 2mm 3.5mm 1.5mm 3.5mm; display: flex; gap: 3.5mm; color: #ffffff;">
        
        {{-- Kolom Kiri: Ketentuan Penggunaan Kartu --}}
        <div style="flex: 1.4; display: flex; flex-direction: column; justify-content: center; font-size: 3.8pt; line-height: 1.28; color: #e5e7eb;">
            <div style="display: flex; gap: 1mm; margin-bottom: 0.6mm;">
                <span style="color: #ffffff; font-size: 5pt; line-height: 0.9;">&bull;</span>
                <span>Kartu pelajar ini untuk dibawa setiap mengikuti kegiatan akademik dan kesiswaan.</span>
            </div>
            <div style="display: flex; gap: 1mm; margin-bottom: 0.6mm;">
                <span style="color: #ffffff; font-size: 5pt; line-height: 0.9;">&bull;</span>
                <span>Berlaku selama yang bersangkutan aktif / terdaftar sebagai Siswa/i Telkom School SMK Telkom Lampung.</span>
            </div>
            <div style="display: flex; gap: 1mm;">
                <span style="color: #ffffff; font-size: 5pt; line-height: 0.9;">&bull;</span>
                <span>Apabila kartu ini hilang harap menghubungi bagian kesiswaan.</span>
            </div>
        </div>

        {{-- Kolom Kanan: Alamat Pengembalian Kartu --}}
        <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; font-size: 3.8pt; line-height: 1.25; color: #e5e7eb; border-left: 0.5px solid #374151; padding-left: 3mm;">
            <div style="color: #d1d5db; margin-bottom: 0.2mm;">
                Bagi yang menemukan kartu ini harap mengembalikan ke :
            </div>
            <div style="font-weight: 800; color: #ffffff; font-size: 4.2pt; text-transform: uppercase;">
                SMK Telkom Lampung
            </div>
            <div style="color: #9ca3af;">
                Jl. Raya Gadingrejo, Kec. Gadingrejo<br>
                Kab. Pringsewu - 35372<br>
                <span style="font-size: 3.3pt; color: #cbd5e1;">WA: 0811-7970-1215</span>
            </div>
        </div>

    </div>

</div>
