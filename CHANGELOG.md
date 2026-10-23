# Changelog SISFO SMK Telkom Lampung
Semua perubahan penting pada Sistem Informasi Sekolah (SISFO Stella) didokumentasikan dalam dokumen ini.
Format pencatatan berpedoman pada [Keep a Changelog](https://keepachangelog.com/id/1.0.0/) dan [Semantic Versioning](https://semver.org/).

Dokumen ini diperbarui secara berkelanjutan sebagai bukti riwayat pengembangan software untuk keperluan audit sistem, transparansi tim IT, dan pelaporan stakeholder.

---

## [2.4.0] - 2026-10-24
### Added
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
  - Kop surat resmi Yayasan Pendidikan Telkom & SMK Telkom Lampung.
  - 4 Kartu KPI Ringkasan Eksekutif (Total Catatan, Siswa Unik, Selesai, Menunggu/Proses BK).
  - Kolom tanda tangan resmi 3 pihak (Waka Kesiswaan, Koordinator Guru BK, dan Koordinator Guru Piket).

### Fixed
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
