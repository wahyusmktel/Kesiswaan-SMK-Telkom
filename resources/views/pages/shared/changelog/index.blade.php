<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            📝 Change Log
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Release Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-2xl"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-white/20 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest border border-white/30">Stable Release</span>
                        <span class="text-blue-100 text-xs font-bold">{{ date('F d, Y') }}</span>
                    </div>
                    <h3 class="text-4xl font-black tracking-tight">Version {{ config('app.version') }}</h3>
                    <p class="mt-2 text-blue-100 font-medium text-lg italic">"Empowering School Administration, IoT Sync, and Digital Credentials"</p>
                </div>
            </div>

            {{-- Change Log Content --}}
            <div class="space-y-6">

                {{-- Latest Update 1.5.0 --}}
                <div class="bg-gradient-to-br from-blue-900 to-gray-900 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden border border-blue-700/50">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="bg-blue-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest">Latest Update</span>
                            <h3 class="text-3xl font-black tracking-tight mt-1 text-white">Version {{ config('app.version') }}</h3>
                            <p class="text-gray-400 text-sm font-medium mt-1 italic">"Modul Ajar & Student Onboarding"</p>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-400 text-xs font-bold">July 15, 2026</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-blue-400 tracking-widest border-l-2 border-blue-500 pl-3">Fitur Baru</h4>
                            <ul class="space-y-3">
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Modul Ajar (Teaching Module)</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Pengelolaan Modul Ajar terstruktur untuk Guru Kelas dengan metadata dinamis, form konten modular, dan ekspor PDF resmi berformat presisi.</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Pendaftaran Siswa Baru (PPDB)</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Portal registrasi mandiri siswa baru lengkap dengan validasi nomor telepon kustom serta sistem verifikasi status pendaftaran.</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Layanan Informasi Landing Popup</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Pengaturan popup pengumuman/penerimaan siswa baru pada beranda utama yang dapat diatur dinamis oleh Super Admin.</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Kenaikan Kelas & Kelulusan</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Sistem kenaikan kelas terautomasi (ClassPromotionService) untuk perpindahan rombel yang aman dan pencatatan status kelulusan alumni.</p>
                                </li>
                            </ul>
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-emerald-400 tracking-widest border-l-2 border-emerald-500 pl-3">Peningkatan & Perbaikan</h4>
                            <ul class="space-y-2">
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-emerald-400">🛡️</span>
                                    <span class="text-gray-300"><strong class="text-white">Active Student Validation:</strong> Validasi keaktifan siswa pada rombel belajar guna mencegah inkonsistensi data.</span>
                                </li>
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-indigo-400">⚡</span>
                                    <span class="text-gray-300"><strong class="text-white">Phone Validation:</strong> Peningkatan filter input nomor ponsel untuk keamanan data registrasi.</span>
                                </li>
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-amber-400">📋</span>
                                    <span class="text-gray-300"><strong class="text-white">Dapodik Active Statistics:</strong> Perhitungan statistik dashboard Dapodik hanya menampilkan siswa aktif.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Update 1.4.0 --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden border border-gray-700">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="bg-indigo-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest">Update</span>
                            <h3 class="text-3xl font-black tracking-tight mt-1 text-white">Version 1.4.0</h3>
                            <p class="text-gray-400 text-sm font-medium mt-1 italic">"Digital Credentials & Manual Signatures"</p>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-400 text-xs font-bold">July 08, 2026</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-blue-400 tracking-widest border-l-2 border-blue-500 pl-3">Fitur Baru</h4>
                            <ul class="space-y-3">
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Tanda Tangan Manual Digital</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Pengunggahan dokumen PDF dengan penempatan posisi QR Code secara interaktif via koordinat presisi dan normalisasi berkas PDF.</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Penerbitan Nomor Transkrip</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Sistem penomoran transkrip otomatis serta penguncian nilai untuk menjaga integritas data akademik siswa.</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Kustomisasi Transkrip PDF</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Pengaturan watermark transparansi, tanda tangan otomatis QR code, nomor ijazah, dan layout borderless pada cetak transkrip.</p>
                                </li>
                            </ul>
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-emerald-400 tracking-widest border-l-2 border-emerald-500 pl-3">Peningkatan & Perbaikan</h4>
                            <ul class="space-y-2">
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-emerald-400">⚙️</span>
                                    <span class="text-gray-300"><strong class="text-white">PDF Normalization:</strong> Perbaikan penanganan file PDF hasil scan/kompresi agar terbaca sempurna oleh pustaka internal.</span>
                                </li>
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-indigo-400">📦</span>
                                    <span class="text-gray-300"><strong class="text-white">pdfjs-dist Dependency:</strong> Integrasi pustaka Node.js untuk pembacaan metadata dan halaman PDF secara asinkron.</span>
                                </li>
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-amber-400">🎨</span>
                                    <span class="text-gray-300"><strong class="text-white">Theme "Ajaran Baru":</strong> Tema visual baru untuk menyesuaikan tampilan beranda saat pergantian tahun ajaran.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Update 1.3.0 --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden border border-gray-700">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="bg-indigo-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest">Update</span>
                            <h3 class="text-3xl font-black tracking-tight mt-1 text-white">Version 1.3.0</h3>
                            <p class="text-gray-400 text-sm font-medium mt-1 italic">"Industrial Practice & School Health Services"</p>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-400 text-xs font-bold">June 27, 2026</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-blue-400 tracking-widest border-l-2 border-blue-500 pl-3">Fitur Baru</h4>
                            <ul class="space-y-3">
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Sistem Prakerin (PKL)</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Pengelolaan penempatan industri, monitoring jurnal harian siswa, ekspor PDF agenda, bimbingan berkas, dan rubrik penilaian PKL dinamis.</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">UKS & Rekam Medis</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Pencatatan pemeriksaan kesehatan, pemeriksaan mata, rujukan rumah sakit, dan notifikasi pemulangan siswa mandiri.</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Google Drive Integrator</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Integrasi kredensial Google Drive API untuk mengunggah dokumen digital secara langsung ke penyimpanan awan cloud.</p>
                                </li>
                            </ul>
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-emerald-400 tracking-widest border-l-2 border-emerald-500 pl-3">Peningkatan & Perbaikan</h4>
                            <ul class="space-y-2">
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-emerald-400">👥</span>
                                    <span class="text-gray-300"><strong class="text-white">Role Petugas UKS:</strong> Hak akses khusus baru untuk pengelolaan Unit Kesehatan Sekolah tanpa akses administratif penuh.</span>
                                </li>
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-indigo-400">🏫</span>
                                    <span class="text-gray-300"><strong class="text-white">External Mentor Mapping:</strong> Pemetaan guru pembimbing luar sekolah dan integrasinya ke rombel PKL terkait.</span>
                                </li>
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-amber-400">🔗</span>
                                    <span class="text-gray-300"><strong class="text-white">SSO Integration:</strong> Integrasi masuk satu pintu (Single Sign-On / SSO) menggunakan protokol OAuth2 callback.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Update 1.2.0 --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden border border-gray-700">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="bg-indigo-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest">Update</span>
                            <h3 class="text-3xl font-black tracking-tight mt-1 text-white">Version 1.2.0</h3>
                            <p class="text-gray-400 text-sm font-medium mt-1 italic">"IoT Fingerprint Sync & Collaborative Forum Stella"</p>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-400 text-xs font-bold">June 08, 2026</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-blue-400 tracking-widest border-l-2 border-blue-500 pl-3">Fitur Baru</h4>
                            <ul class="space-y-3">
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Koneksi Mesin Absensi (IoT)</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Sinkronisasi log kehadiran real-time dan manajemen data user langsung dari perangkat fingerprint ZKTeco/GF1600 via UDP socket.</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Forum Stella</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Ruang diskusi terbuka sekolah dengan filter kategori, auto-embed video YouTube & tautan gambar langsung, dan modal detail interaktif (draggable/maximizable).</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Dynamic Role Switcher</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Sistem perpindahan role instan untuk pengguna dengan banyak akses (multi-role). Sidebar akan menyesuaikan secara otomatis sesuai role aktif.</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Jadwal Mengajar Interaktif</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Halaman "Jadwal Saya" untuk Guru dengan desain kartu modern, kategori per hari, dan visualisasi warna yang cerah.</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Notted Games (Uno & Scrabble)</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Modul mini game Uno multiplayer dan Scrabble sebagai media rekreasi dan pembelajaran interaktif siswa.</p>
                                </li>
                            </ul>
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-emerald-400 tracking-widest border-l-2 border-emerald-500 pl-3">Peningkatan & Perbaikan</h4>
                            <ul class="space-y-2">
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-emerald-400">🕒</span>
                                    <span class="text-gray-300"><strong class="text-white">Auto-Sync Fingerprint:</strong> Peningkatan sinkronisasi terjadwal otomatis di latar belakang (scheduler & queueing).</span>
                                </li>
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-indigo-400">📊</span>
                                    <span class="text-gray-300"><strong class="text-white">Attendance Analytics:</strong> Grafik visualisasi waktu check-in/check-out serta metrik penilaian kedisiplinan pegawai.</span>
                                </li>
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-amber-400">🎨</span>
                                    <span class="text-gray-300"><strong class="text-white">Tema Telkom Corporate:</strong> Opsi tema baru ("telkom-corporate", "campus-flow", "tech-red") untuk personalisasi visual dashboard.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Update 1.1.0 --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden border border-gray-700">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="bg-indigo-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest">Update</span>
                            <h3 class="text-3xl font-black tracking-tight mt-1 text-white">Version 1.1.0</h3>
                            <p class="text-gray-400 text-sm font-medium mt-1 italic">"Dapodik Integrity & System Syncing"</p>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-400 text-xs font-bold">May 15, 2026</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-blue-400 tracking-widest border-l-2 border-blue-500 pl-3">Fitur Baru</h4>
                            <ul class="space-y-3">
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Pengajuan Mandiri Dapodik</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Siswa dapat mengajukan perubahan data Dapodik secara mandiri dengan sistem kategori lampiran dokumen (KK, Ijazah, Akta, dll).</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Modern Operator Verification</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Dashboard verifikasi baru dengan perbandingan data lama vs baru secara side-by-side dan fitur tag ringkasan perubahan.</p>
                                </li>
                            </ul>
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-emerald-400 tracking-widest border-l-2 border-emerald-500 pl-3">Peningkatan & Perbaikan</h4>
                            <ul class="space-y-2">
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-emerald-400">🔹</span>
                                    <span class="text-gray-300"><strong class="text-white">Auto-Identity Sync:</strong> Sinkronisasi Nama Lengkap antara Dapodik, Master Siswa, hingga Akun Login Profil.</span>
                                </li>
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-indigo-400">🔹</span>
                                    <span class="text-gray-300"><strong class="text-white">Modal Doc Previewer:</strong> Preview dokumen (Gambar/PDF) langsung di halaman verifikasi tanpa pindah tab.</span>
                                </li>
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-amber-400">🔹</span>
                                    <span class="text-gray-300"><strong class="text-white">Timezone Date Fix:</strong> Perbaikan akurasi tanggal lahir dari database ke formulir (WIB Timezone alignment).</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Update 1.0.0 --}}
                <div class="bg-gradient-to-br from-gray-950 to-gray-900 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden border border-gray-800">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="bg-indigo-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest">Update</span>
                            <h3 class="text-3xl font-black tracking-tight mt-1 text-white">Version 1.0.0</h3>
                            <p class="text-gray-400 text-sm font-medium mt-1 italic">"Empowering School Management Core"</p>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-400 text-xs font-bold">April 17, 2026</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-blue-400 tracking-widest border-l-2 border-blue-500 pl-3">Fitur Utama</h4>
                            <ul class="space-y-3">
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Sistem Perizinan Terpadu</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Mendukung izin tidak masuk (sakit/izin) dan izin keluar kelas real-time dengan alur persetujuan berjenjang (Guru Kelas, Wali Kelas, Guru Piket).</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Manajemen Poin & Tata Tertib</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Sistem pencatatan pelanggaran, prestasi, dan pemutihan poin siswa yang terintegrasi secara global.</p>
                                </li>
                                <li class="text-sm">
                                    <span class="font-bold text-gray-200">Monitoring Absensi Guru</span>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Pelacakan kehadiran guru di kelas secara real-time oleh Waka Kurikulum dan Piket, lengkap dengan statistik performa.</p>
                                </li>
                            </ul>
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-emerald-400 tracking-widest border-l-2 border-emerald-500 pl-3">Peningkatan & Perbaikan</h4>
                            <ul class="space-y-2">
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-emerald-400">🚀</span>
                                    <span class="text-gray-300"><strong class="text-white">Dashboard Interaktif:</strong> Widget "Kegiatan Saat Ini" yang adaptif untuk Siswa, Guru, Piket, dan Kurikulum.</span>
                                </li>
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-indigo-400">📅</span>
                                    <span class="text-gray-300"><strong class="text-white">Flexible Lesson Hours:</strong> Mendukung jam pelajaran yang berbeda antar hari untuk mengakomodasi jadwal khusus.</span>
                                </li>
                                <li class="text-[13px] flex items-start gap-2">
                                    <span class="text-amber-400">📄</span>
                                    <span class="text-gray-300"><strong class="text-white">Eksport PDF Master Jadwal:</strong> Fitur unduh jadwal seluruh rombel dalam satu file PDF landscape yang rapi.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer Info --}}
            <div class="text-center pb-12">
                <p class="text-gray-400 text-sm font-medium">SMK Telkom Lampung - Version {{ config('app.version') }}-stable</p>
                <div class="flex justify-center gap-4 mt-2">
                    <span class="w-2 h-2 rounded-full bg-blue-200"></span>
                    <span class="w-2 h-2 rounded-full bg-indigo-200"></span>
                    <span class="w-2 h-2 rounded-full bg-purple-200"></span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>