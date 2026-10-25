# Changelog SISFO SMK Telkom Lampung
Semua perubahan penting pada Sistem Informasi Sekolah (SISFO Stella) didokumentasikan dalam dokumen ini.
Format pencatatan berpedoman pada [Keep a Changelog](https://keepachangelog.com/id/1.0.0/) dan [Semantic Versioning](https://semver.org/).

Dokumen ini diperbarui secara berkelanjutan sebagai bukti riwayat pengembangan software untuk keperluan audit sistem, transparansi tim IT, dan pelaporan stakeholder.

---

## [2.5.0] - 2026-10-25
### Added
- **Modernisasi Halaman Monitoring Catatan Siswa (`/bk/monitoring-catatan`)**:
  - Menerapkan dropdown filter kelas interaktif dengan kolom pencarian *real-time* (*searchable select* berbasis Alpine.js).
  - Menambahkan kolom penomoran urutan `No` di sisi paling kiri tabel catatan kedisiplinan siswa dengan kalkulasi halaman aktif.
  - Menerapkan komponen paginasi ringkas bertema terang (*light theme*) dengan fitur *Jump to Page* dan tombol aksi bersudut kotak modern (`rounded-md`).
- **Identifikasi Wali Kelas pada Detail Kedisiplinan (`/bk/monitoring-catatan/{siswa}`)**:
  - Mengganti teks sub-judul generic *"Monitoring Kedisiplinan Siswa"* di bawah nama siswa dengan informasi nama **Wali Kelas** secara dinamis untuk mempermudah identifikasi dan koordinasi Guru BK.
- **Modernisasi Halaman Monitoring Izin Guru BK (`/bk/monitoring-izin`)**:
  - Menerapkan dropdown filter kelas interaktif dengan kolom pencarian *real-time* (*searchable select* berbasis Alpine.js).
  - Menambahkan kolom penomoran urutan `No` di sisi paling kiri tabel perizinan siswa dengan kalkulasi halaman paginasi aktif.
  - Menyatukan tombol aksi *Filter* dan *Reset* ke dalam *segmented button group* modern sedikit kotak (`rounded-md`) yang responsif di perangkat mobile.
  - Menerapkan sistem paginasi ringkas bertema terang (*light theme*, 1-4 digit window) lengkap dengan fitur *Jump to Page* via tombol Enter.
  - Memperbarui format tampilan pada kolom **Tanggal Izin** menjadi bahasa Indonesia resmi (contoh: `11 Agustus 2026`).
- **Searchable Dropdown Filter Kelas (Alpine.js)**:
  - Mengubah dropdown filter kelas pada halaman Monitoring Keterlambatan menjadi komponen interaktif yang dilengkapi kolom pencarian real-time untuk mempermudah pencarian nama kelas.
- **Kolom Nomor Urutan Data pada Tabel Keterlambatan**:
  - Menambahkan kolom `No` di sisi paling kiri tabel keterlambatan (sebelah kiri kolom Siswa) dengan perhitungan nomor urut berbasis paginasi aktif.
- **Paginasi Ringkas Bertema Light & Fitur Jump to Page**:
  - Tampilan navigasi paginasi baru bertema terang (*light theme*), modern, dan bersudut kotak tegas (`rounded-md`).
  - Menyingkat tampilan angka halaman menjadi maksimal 4 angka berurutan (*compact sliding window* 1, 2, 3, 4) dengan navigasi `Prev` dan `Next`.
  - Fitur **Lompat Halaman (*Jump to Page*)**: input angka halaman langsung yang melompat ketika ditekan tombol **Enter** dengan tetap mempertahankan seluruh parameter filter aktif.

### Changed
- **Penyatuan dan Responsivitas 4 Tombol Aksi (Filter, Reset, Excel, PDF)**:
  - Mengelompokkan keempat tombol aksi ke dalam *segmented button group* terpadu dengan border-radius modern sedikit kotak (`rounded-md`).
  - Desain responsif optimal untuk perangkat mobile, di mana tombol meregang secara proporsional (*flex-1*) tanpa berantakan.

### Fixed
- **Navigasi Jump to Page pada Paginasi**:
  - Memperbaiki error JavaScript `Uncaught TypeError: URL is not a constructor` pada input lompat halaman akibat penimpaan objek global `URL` oleh atribut string bawaan `document.URL` di *scope inline event handler*.
  - Mengisolasi pemrosesan navigasi ke dalam fungsi `window.jumpToPaginationPage` dengan *fallback* manipulasi parameter URL yang aman dan kompatibel.

---

## [2.4.0] - 2026-10-24
### Added
- **Integrasi Gambar Kop Surat Resmi (Full Sisi) pada PDF Keterlambatan**:
  - Mengintegrasikan banner gambar kop surat sekolah yang diunggah melalui menu konfigurasi aplikasi Super Admin (`kop_surat_ukk` pada `AppSetting`).
  - Menerapkan tata letak tanpa margin (*full sisi / edge-to-edge*) pada gambar banner kop surat di halaman pertama dokumen PDF sehingga terbentang penuh dari ujung ke ujung kertas landscape, dengan tetap mempertahankan margin aman untuk tabel dan halaman-halaman berikutnya.
- **Sistem Dokumentasi Changelog Terintegrasi**: Halaman `/changelog` kini membaca dan menampilkan riwayat perubahan langsung dari file `CHANGELOG.md` untuk keperluan audit dan transparansi tim IT.
- **Modern Redesigned Debug Error Inspector**:
  - Tampilan baru halaman error (`resources/views/errors/debug-simple.blade.php`) dengan Dark IDE Theme.
  - Tombol **Salin Ringkasan Error** (format pesan, lokasi file, request URL, user context, dan top trace siap paste ke AI/chat).
  - Tombol **Salin Full Trace** untuk analisis mendalam.
  - Filter interaktif **Hanya Kode Aplikasi (App)** untuk menyaring frame vendor/third-party.
  - Notifikasi toast saat teks disalin ke clipboard.
- **Format Excel Modern Rekap Keterlambatan**:
  - Penambahan kop banner dinamis (periode, kelas, status, waktu ekspor).
  - Header tabel bertema Telkom Red (`#991B1B`) dengan tinggi 28pt.
  - Pewarnaan otomatis status pembinaan (*Soft Emerald*, *Soft Amber*, *Soft Purple*, *Soft Rose*).
  - Zebra striping baris dan format teks eksplisit pada NIS untuk mencegah notasi ilmiah.
  - Fitur interaktif *Freeze Pane* (sticky header) dan *AutoFilter* native Excel pada seluruh kolom (A-J).
  - Baris footer ringkasan total catatan keterlambatan.
- **Format PDF Landscape Rekap Keterlambatan**:
  - Konfigurasi orientasi A4 Landscape (lebar 297 mm) untuk ruang tabel yang lega dan proporsional.
  - Perbaikan margin cetak halaman (`18mm 22mm`) agar dokumen memiliki padding aman sisi atas, bawah, kanan, dan kiri tanpa mentok kertas.
  - Redesain widget KPI ringkasan menjadi kotak datar modern (*clean flat border*) tanpa sudut lengkung (*zero border-radius*) dan tanpa border sisi kiri/kanan.
  - Kop surat resmi Yayasan Pendidikan Telkom & SMK Telkom Lampung.
  - 4 Kartu KPI Ringkasan Eksekutif (Total Catatan, Siswa Unik, Selesai, Menunggu/Proses BK).
  - Kolom tanda tangan resmi 3 pihak (Waka Kesiswaan, Koordinator Guru BK, dan Koordinator Guru Piket).

### Changed
- **Penyesuaian Kolom Tanda Tangan PDF Rekap Keterlambatan**:
  - Mengosongkan nama penandatangan (disediakan format titik-titik kosong `( .................................... )` untuk pengisian fisik/manual).
  - Menghapus sebutan kata "Koordinator" sehingga judul pihak penandatangan menjadi **Guru BK** dan **Guru Piket**, dengan jabatan **Wakil Kepala Sekolah Bid. Kesiswaan** tetap dipertahankan.
- **Penyederhanaan Tampilan Halaman Log / Changelog**:
  - Header diringkas secara minimalis hanya menyisakan judul **"Log"** (menghapus teks sub-deskripsi).
  - Tombol aksi **Unduh CHANGELOG.md** dan **Salin Markdown** dipindahkan dari area header ke area toolbar konten agar tata letak halaman lebih proporsional dan nyaman diakses di perangkat mobile maupun desktop.

### Fixed
- **Margin Halaman Ekspor PDF Keterlambatan**:
  - Menghapus deklarasi reset `margin: 0; padding: 0;` pada `html, body` di template PDF yang memicu engine Dompdf menimpa konfigurasi `@page margin`.
  - Menerapkan padding dan margin konsisten (14mm vertikal, 16mm horizontal) di seluruh sisi kertas (atas, bawah, kanan, kiri) sehingga dokumen tidak lagi menempel ke tepi kertas saat dicetak maupun disimpan sebagai PDF.
- **Penanganan Siswa Soft-Deleted pada Ekspor Keterlambatan**:
  - Menambahkan relasi `->withTrashed()` pada model `Keterlambatan` ke `MasterSiswa` sehingga data riwayat siswa yang diarsipkan/terhapus tetap terbaca utuh.
  - Menambahkan operator null-safe `?->` dan fallback `'Siswa Tidak Ditemukan'` pada `KeterlambatanExport.php` dan `rekap-keterlambatan.blade.php` untuk mencegah `ErrorException: Attempt to read property 'nama_lengkap' on null`.
  - Memperbaiki validasi filter tanggal kosong (`!empty(...)`) agar tidak menghasilkan kueri SQL tidak valid.
  - Mengamankan seluruh tampilan tabel index dan show monitoring keterlambatan dari potensi nilai null.

---

## [2.3.0] - 2026-10-22
### Security
- **Mitigasi & Patching Kerentanan Kritis Core PHP (CVE-2026-109618 & CVE-2026-109619)**:
  - Upgrade paket core PHP ke versi patch resmi rilis 21 Oktober 2026: **PHP 8.3.36** dan **PHP 8.4.27** pada seluruh 3 server produksi (`sisfo-server`, `studi-lanjut`, `literasia`).
  - Penonaktifan permanen fitur `session.upload_progress.enabled = Off` pada konfigurasi FPM & CLI beserta file override prioritas tinggi `99-hardening.ini`.
  - Hardening konfigurasi Nginx dengan memblokir eksekusi file script `.php` pada direktori upload statis (`/storage/`, `/uploads/`, `/images/`, `/files/`) dengan respons `404 / 403 Denied`.
  - Perbaikan konflik fungsi tema pada LMS Moodle (`lms_theme_ts`) yang sempat mengalami HTTP 500 menjadi kembali normal 200 OK.
  - Pelaporan status kepatuhan dan mitigasi server ke BOD YPT & Telkom.

### Added
- **Modern Bento Login Redesign (Terinspirasi TelkomGPT KMS)**:
  - Desain baru halaman login split-screen dengan showcase bento grid dinamis di panel kanan.
  - Template switcher pada pengaturan aplikasi (`classic` vs `telkom-modern`) yang dapat dikustomisasi Super Admin.
  - Dukungan lengkap seluruh opsi login: Google OAuth Workspace, Kartu RFID Stella Access, Face ID Webcam AI, serta tautan PPDB Siswa Baru.

---

## [2.2.0] - 2026-10-18
### Added
- **WordPress-style Hierarchical Role Menu Management**:
  - Antarmuka drag-and-drop interaktif untuk mengatur urutan dan hierarki submenu bertingkat per-role.
  - Fitur tombol *Maximize/Fullscreen* pada kotak struktur hierarki menu untuk kenyamanan penataan.
  - Pengelompokan dinamis (*Grouping Parent Header*) yang dapat diedit namanya sebagai accordion dekorasi.
  - Isolasi menu berdasarkan *Role Aktif* pengguna pada sistem multi-role.
- **Sistem Pengaturan Pengumuman Global**:
  - Tombol kontrol Super Admin untuk menampilkan atau menyembunyikan modal pengumuman sistem pada landing page dan navigasi utama.
- **Manajemen OKR Sekolah Bertingkat**:
  - Penyesuaian hierarki unit kerja: Kaprodi PPLGIM & TJKT dapat menginduk ke OKR Tahunan Kurikulum untuk penyusunan target bulanan dan mingguan.

---

## [2.1.0] - 2026-09-20
### Added
- **Modul Rencana Anggaran Biaya (RAB) & Keuangan**:
  - Alur pengajuan anggaran terstruktur, upload bukti realisasi, dan validasi persetujuan bertingkat.
- **Sistem Perizinan Guru & Integrasi Telegram Bot**:
  - Alur perizinan sakit guru dengan durasi otomatis 3 hari, unggah surat dokter/rawat inap, dan integrasi notifikasi via Telegram Bot.
  - Ekspor Excel status penautan akun Telegram pegawai sekolah (tertaut vs belum tertaut).
- **Modul Bimbingan Prakerin & Berita Acara**:
  - Pencatatan log bimbingan, revisi berita acara, dan verifikasi tanda tangan digital berbasis QR Code.

---

## [2.0.0] - 2026-08-20
### Added
- **SISFO OAuth 2.0 Single Sign-On (SSO) Provider**:
  - Menjadikan SISFO Stella sebagai Identity Provider (IdP) pusat bagi ekosistem aplikasi SMK Telkom Lampung.
- **Integrasi Streaming CCTV Kampus**:
  - Sinkronisasi protokol MediaMTX / RTSP / HLS CORS untuk pemantauan keamanan sekolah real-time.
- **Modul E-Rapor & Konfigurasi Kurikulum**:
  - Pengaturan periode kurikulum, mata pelajaran pilihan, dan penugasan guru mengajar.
- **WhatsApp Gateway Notification Engine**:
  - Otomatisasi pengiriman notifikasi keterlambatan siswa dan pengumuman PPDB langsung ke nomor WhatsApp orang tua.

---

## [1.5.0] - 2026-07-15
### Added
- **Pengelolaan Modul Ajar (Teaching Module)** dengan bantuan AI Content Generation (Stella AI).
- **Pendaftaran Siswa Baru (PPDB)** dengan validasi kontak dan alur approval berkas.
- **Sistem Kenaikan Kelas & Kelulusan (ClassPromotionService)** untuk mutasi rombel aman.

---

## [1.4.0] - 2026-07-08
### Added
- **Tanda Tangan Manual & Digital Credentials**:
  - Dukungan canvas tanda tangan digital untuk verifikasi dokumen resmi kesiswaan.
  - Arsip transkrip nilai dengan stempel digital.
