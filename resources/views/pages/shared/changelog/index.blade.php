<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-red-100 text-red-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    Riwayat Pembaruan & Changelog SISFO
                </h2>
                <p class="text-xs text-gray-500 mt-1 font-medium">Log pengembangan software resmi, histori fitur, perbaikan bug, dan audit kepatuhan sistem.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('changelog.index', ['download' => 1]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 text-xs font-bold rounded-xl border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh CHANGELOG.md
                </a>
                <button type="button" onclick="copyMarkdownChangelog()" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-black transition shadow-sm">
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    Salin Markdown
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Release Header Banner --}}
            <div class="bg-gradient-to-r from-red-700 via-rose-700 to-indigo-900 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-2xl"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="bg-white/20 text-white text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-widest border border-white/30">
                                Latest Production Release
                            </span>
                            <span class="text-red-100 text-xs font-semibold">
                                SMK Telkom Lampung
                            </span>
                        </div>
                        <h3 class="text-4xl font-black tracking-tight">Version {{ $latestVersion }}</h3>
                        <p class="mt-2 text-rose-100 font-medium text-sm md:text-base max-w-2xl leading-relaxed">
                            "Mewujudkan Tata Kelola Kesiswaan Digital yang Aman, Terstruktur, Terintegrasi, dan Berorientasi Keunggulan Sekolah"
                        </p>
                    </div>

                    <div class="bg-black/30 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-xs space-y-2 min-w-[220px]">
                        <div class="flex justify-between items-center text-gray-300">
                            <span>Status Audit:</span>
                            <span class="text-emerald-400 font-bold flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Verified OK
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-gray-300">
                            <span>PHP Target:</span>
                            <span class="font-mono font-bold text-white">PHP 8.3.36+</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-300">
                            <span>Framework:</span>
                            <span class="font-mono font-bold text-white">Laravel 12</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-300 border-t border-white/10 pt-2">
                            <span>Total Riwayat:</span>
                            <span class="font-bold text-amber-300">{{ count($releases) }} Rilis Utama</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Version Quick Navigator Pills --}}
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm flex items-center gap-3 overflow-x-auto">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap pl-2">Lompat Versi:</span>
                <div class="flex items-center gap-2">
                    @foreach($releases as $rel)
                        <a href="#version-{{ str_replace('.', '-', $rel['version']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $loop->first ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            v{{ $rel['version'] }}
                            <span class="text-[10px] opacity-60 ml-1 font-normal">({{ \Carbon\Carbon::parse($rel['date'])->format('d M') }})</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Timeline Container --}}
            <div class="space-y-8">

                {{-- Version 2.4.0 --}}
                <div id="version-2-4-0" class="bg-white rounded-3xl p-8 border border-gray-200 shadow-sm relative scroll-mt-24">
                    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-gray-100 gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-red-600 text-white shadow-sm">
                                    Version 2.4.0
                                </span>
                                <span class="text-xs font-black text-emerald-600 uppercase tracking-wider bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200">
                                    Current Release
                                </span>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 mt-2">Penyempurnaan Rekap Keterlambatan, Audit Changelog & Debug Inspector</h3>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-gray-900 block">24 Oktober 2026</span>
                            <span class="text-xs text-gray-400">Pembaruan Sistem & Fitur Audit</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-6">
                        <div class="space-y-5">
                            <h4 class="text-xs font-black uppercase text-indigo-700 tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                                Fitur Baru & Peningkatan
                            </h4>
                            <div class="space-y-4">
                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                    <h5 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                        <span>📝</span> Halaman & File CHANGELOG.md Terintegrasi
                                    </h5>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                        Pencatatan riwayat pengembangan software secara terstruktur dan terstandar pada file `CHANGELOG.md` yang otomatis tampil pada halaman `/changelog` untuk keperluan audit, transparansi, dan dokumentasi berkala.
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                    <h5 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                        <span>📊</span> Redesain Modern Excel Rekap Keterlambatan
                                    </h5>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                        Pembaruan ekspor Excel dengan kop resmi SMK Telkom Lampung, header Telkom Red, pewarnaan otomatis status pembinaan (*Soft Emerald*, *Soft Amber*, *Soft Purple*, *Soft Rose*), sticky header (*Freeze Pane*), auto-filter interaktif, serta ringkasan footer.
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                    <h5 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                        <span>📄</span> Redesain PDF Rekap Keterlambatan (Landscape)
                                    </h5>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                        Konversi dokumen PDF ke orientasi A4 Landscape lega dengan kop resmi Yayasan Pendidikan Telkom, 4 kartu KPI ringkasan eksekutif, zebra striping, dan 3 kolom tanda tangan pejabat sekolah.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <h4 class="text-xs font-black uppercase text-emerald-700 tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                Perbaikan Bug & Debugging
                            </h4>
                            <div class="space-y-4">
                                <div class="bg-emerald-50/50 rounded-2xl p-4 border border-emerald-100">
                                    <h5 class="text-sm font-bold text-emerald-950 flex items-center gap-2">
                                        <span>🛡️</span> Fix Null Siswa & Soft-Deleted Export
                                    </h5>
                                    <p class="text-xs text-emerald-800/80 mt-1 leading-relaxed">
                                        Menambahkan relasi `->withTrashed()` pada model Keterlambatan serta nullsafe operator `?->` pada `KeterlambatanExport` dan Blade template, mengatasi error `Attempt to read property 'nama_lengkap' on null` ketika siswa terhapus atau query filter kosong.
                                    </p>
                                </div>
                                <div class="bg-emerald-50/50 rounded-2xl p-4 border border-emerald-100">
                                    <h5 class="text-sm font-bold text-emerald-950 flex items-center gap-2">
                                        <span>💻</span> Redesain Debug Error Inspector
                                    </h5>
                                    <p class="text-xs text-emerald-800/80 mt-1 leading-relaxed">
                                        Upgrade halaman error internal (`debug-simple.blade.php`) dengan Dark IDE Theme, tombol salin ringkasan siap AI/chat, tombol salin full trace, dan filter pintar pemisah kode aplikasi vs kode vendor.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Version 2.3.0 --}}
                <div id="version-2-3-0" class="bg-white rounded-3xl p-8 border border-gray-200 shadow-sm relative scroll-mt-24">
                    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-gray-100 gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-600 text-white shadow-sm">
                                    Version 2.3.0
                                </span>
                                <span class="text-xs font-black text-rose-700 uppercase tracking-wider bg-rose-50 px-2.5 py-0.5 rounded-md border border-rose-200">
                                    Security Update
                                </span>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 mt-2">Patch Keamanan Kritis PHP Global & Redesain Login Bento Modern</h3>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-gray-900 block">22 Oktober 2026</span>
                            <span class="text-xs text-gray-400">Infrastruktur & UI Authentication</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-6">
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-rose-700 tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                                Patching Keamanan Server (CVE-2026-109618 & 109619)
                            </h4>
                            <ul class="space-y-3 text-xs text-gray-600 leading-relaxed">
                                <li class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                    <strong class="text-gray-900 block text-sm mb-0.5">Upgrade PHP 8.3.36 & 8.4.27</strong>
                                    Eksekusi upgrade core PHP pada ke-3 server produksi (`sisfo-server`, `studi-lanjut`, `literasia`) untuk menutup celah memory corruption.
                                </li>
                                <li class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                    <strong class="text-gray-900 block text-sm mb-0.5">Penonaktifan session.upload_progress</strong>
                                    Mitigasi permanen dengan mengatur `session.upload_progress.enabled = Off` pada konfigurasi CLI, FPM, dan drop-in conf.
                                </li>
                                <li class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                    <strong class="text-gray-900 block text-sm mb-0.5">Hardening Nginx Storage</strong>
                                    Blokir eksekusi file script `.php` pada direktori upload statis (`/storage/`, `/uploads/`) dengan status 404/403.
                                </li>
                            </ul>
                        </div>

                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-indigo-700 tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                                Modern Bento Login Redesign
                            </h4>
                            <ul class="space-y-3 text-xs text-gray-600 leading-relaxed">
                                <li class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                    <strong class="text-gray-900 block text-sm mb-0.5">Desain Terinspirasi TelkomGPT KMS</strong>
                                    Pembaruan layout halaman login dengan showcase bento grid dinamis, tipografi elegan, dan responsive mobile layout.
                                </li>
                                <li class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                    <strong class="text-gray-900 block text-sm mb-0.5">Template Switcher di Super Admin</strong>
                                    Super Admin dapat berganti antara template login klasik dan modern sewaktu-waktu melalui pengaturan aplikasi.
                                </li>
                                <li class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                    <strong class="text-gray-900 block text-sm mb-0.5">Multi Authentication Options</strong>
                                    Dukungan Google OAuth, Stella Access RFID Card, Face ID Webcam AI, dan registrasi siswa baru PPDB tetap utuh.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Version 2.2.0 --}}
                <div id="version-2-2-0" class="bg-white rounded-3xl p-8 border border-gray-200 shadow-sm relative scroll-mt-24">
                    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-gray-100 gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-gray-900 text-white shadow-sm">
                                    Version 2.2.0
                                </span>
                                <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-md">
                                    Feature Update
                                </span>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 mt-2">Manajemen Hierarki Menu WordPress, Notifikasi Pengumuman & OKR Unit</h3>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-gray-900 block">18 Oktober 2026</span>
                            <span class="text-xs text-gray-400">Navigasi & Tata Kelola OKR</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <h5 class="text-sm font-bold text-gray-900">Hierarki Menu Dinamis</h5>
                            <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                Pengaturan urutan menu drag-and-drop, grouping parent header yang dapat dikustomisasi, tombol maximize/fullscreen, dan isolasi visibilitas menu sesuai role yang sedang aktif.
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <h5 class="text-sm font-bold text-gray-900">Pengaturan Modal Pengumuman</h5>
                            <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                Kontrol penuh bagi Super Admin untuk mengaktifkan atau menyembunyikan popup notifikasi pengumuman sistem pada landing page dan navigasi.
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <h5 class="text-sm font-bold text-gray-900">Hierarki OKR Kurikulum</h5>
                            <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                Kaprodi PPLGIM dan TJKT dapat menginduk pada OKR Tahunan Kurikulum untuk turunan komitmen bulanan dan mingguan secara terintegrasi.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Previous Releases (Collapsible / Summary) --}}
                <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-sm">
                    <h4 class="text-sm font-black uppercase text-gray-400 tracking-wider mb-4">Riwayat Rilis Sebelumnya:</h4>
                    <div class="divide-y divide-gray-100 text-xs">
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <strong class="text-gray-900 font-bold">Version 2.1.0</strong> (September 2026) — Modul RAB & Realisasi Keuangan, Izin Sakit Guru Otomatis, dan Integrasi Bot Telegram.
                            </div>
                            <span class="text-gray-400 font-mono">v2.1.0</span>
                        </div>
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <strong class="text-gray-900 font-bold">Version 2.0.0</strong> (Agustus 2026) — SISFO OAuth 2.0 Single Sign-On (IdP), CCTV Streaming MediaMTX, dan Notifikasi WhatsApp Gateway.
                            </div>
                            <span class="text-gray-400 font-mono">v2.0.0</span>
                        </div>
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <strong class="text-gray-900 font-bold">Version 1.5.0</strong> (Juli 2026) — Modul Ajar AI Generation (Stella AI), Registrasi Siswa Baru (PPDB), dan Kenaikan Kelas Otomatis.
                            </div>
                            <span class="text-gray-400 font-mono">v1.5.0</span>
                        </div>
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <strong class="text-gray-900 font-bold">Version 1.4.0</strong> (Juli 2026) — Digital Credentials, Tanda Tangan Digital Canvas, dan Arsip Transkrip Nilai.
                            </div>
                            <span class="text-gray-400 font-mono">v1.4.0</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Hidden Raw Markdown for Quick Copy --}}
    <textarea id="raw-markdown-changelog" class="hidden">{{ $rawContent }}</textarea>

    <script>
        function copyMarkdownChangelog() {
            const raw = document.getElementById('raw-markdown-changelog').value;
            navigator.clipboard.writeText(raw).then(() => {
                alert('Salinan berkas CHANGELOG.md berhasil disalin ke clipboard!');
            }).catch(err => {
                console.error('Gagal menyalin:', err);
            });
        }
    </script>
</x-app-layout>
