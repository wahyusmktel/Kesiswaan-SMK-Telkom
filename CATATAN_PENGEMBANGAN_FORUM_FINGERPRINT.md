# Catatan Pengembangan Forum Stella & Fingerprint

Dokumen ini dibuat sebagai ringkasan pekerjaan supaya pengembangan bisa dilanjutkan di PC lain.

## Konteks Project

- Project: `Kesiswaan-SMK-Telkom`
- Framework: Laravel 12
- Lokasi workspace saat dikerjakan: `C:\Projects\Kesiswaan-SMK-Telkom`
- Package baru yang ditambahkan: `rats/zkteco`

## 1. Forum Stella

### Tujuan

Menambahkan menu dan halaman forum bernama **Forum Stella** yang dapat diakses oleh semua role aplikasi. Jika belum login, user melihat landing page dan CTA login. Jika sudah login, user masuk ke halaman forum.

### File Utama

- `app/Http/Controllers/ForumStellaController.php`
- `resources/views/public/forum-stella.blade.php`
- `routes/web.php`
- `resources/views/welcome.blade.php`
- `resources/views/layouts/navigation.blade.php`
- `app/Models/NottedPost.php`
- `database/migrations/2026_06_04_000001_add_forum_category_to_notted_posts_table.php`

### Route Forum Stella

- `GET /forum-stella`
- `GET /forum-stella/masuk`
- `POST /forum-stella/posts`

### Fitur Yang Sudah Dibuat

- Menu **Forum Stella** setelah **Galery Photo** di halaman welcome.
- Menu sidebar **Forum Stella** untuk user login.
- Landing page publik untuk user belum login.
- Halaman forum untuk user login.
- Buat thread forum.
- Detail diskusi dalam modal.
- Modal **Detail Diskusi** bisa maximize/restore.
- Modal **Detail Diskusi** bisa di-drag lewat header saat mode normal.
- Modal **Buat Thread Forum** bisa maximize/restore.
- Modal **Buat Thread Forum** bisa di-drag lewat header saat mode normal.
- Kategori thread:
  - Semua Diskusi
  - Pertanyaan
  - Pengumuman
  - Berbagi Materi
  - Ide Sekolah
- Sidebar kategori berfungsi sebagai filter.
- Embed gambar dari URL gambar langsung.
- Embed YouTube cukup dengan paste link YouTube/youtu.be/shorts.
- Hashtag `#contoh` dirender sebagai tag.
- Tombol **Beri Cendol** memakai mekanisme like dari NOTTED.

### Catatan Teknis Forum

- Forum Stella masih menggunakan tabel/model NOTTED:
  - `notted_posts`
  - `notted_comments`
  - `notted_likes`
- Kolom tambahan:
  - `notted_posts.forum_category`
- Saat ingin memisahkan data Forum Stella dari NOTTED sepenuhnya, buat tabel baru khusus forum.

## 2. Integrasi Fingerprint GF1600/ZKTeco

### Tujuan

Menambahkan halaman integrasi mesin fingerprint GF1600/ZKTeco dengan library `rats/zkteco`.

Detail mesin awal:

- Nama mesin: `GF1600`
- IP Address: `192.168.135.2`
- Port: `4370`
- Port sudah dites manual via PHP `fsockopen` dan terbuka.

### Package

Sudah dijalankan:

```bash
composer require rats/zkteco
```

### File Utama

- `app/Http/Controllers/FingerprintController.php`
- `app/Models/FingerprintDevice.php`
- `app/Models/FingerprintUser.php`
- `app/Models/FingerprintAttendance.php`
- `database/migrations/2026_06_05_000001_create_fingerprint_devices_table.php`
- `database/migrations/2026_06_05_000002_create_fingerprint_users_table.php`
- `database/migrations/2026_06_05_000003_create_fingerprint_attendances_table.php`
- `resources/views/pages/fingerprint/index.blade.php`
- `resources/views/pages/fingerprint/form.blade.php`
- `resources/views/pages/fingerprint/logs.blade.php`
- `resources/views/pages/fingerprint/partials/flash.blade.php`
- `resources/views/pages/fingerprint/partials/log-table.blade.php`
- `routes/web.php`
- `resources/views/layouts/navigation.blade.php`

### Route Fingerprint

- `GET /fingerprint`
- `GET /fingerprint/create`
- `POST /fingerprint`
- `GET /fingerprint/{fingerprint}/edit`
- `PUT /fingerprint/{fingerprint}`
- `DELETE /fingerprint/{fingerprint}`
- `GET /fingerprint/logs`
- `POST /fingerprint/{id}/test-connection`
- `POST /fingerprint/{id}/sync-users`
- `POST /fingerprint/{id}/sync-attendances`

### Fitur Fingerprint Yang Sudah Dibuat

- CRUD mesin fingerprint.
- Form tambah/edit field:
  - `name`
  - `ip_address`
  - `port`
  - `serial_number`
  - `location`
  - `is_active`
- Default form create:
  - `GF1600`
  - `192.168.135.2`
  - `4370`
- Tombol **Test Koneksi**.
- Tombol **Tarik User**.
- Tombol **Tarik Log**.
- Simpan user fingerprint ke `fingerprint_users`.
- Simpan log absensi ke `fingerprint_attendances`.
- Anti-duplikasi log dengan `updateOrCreate`.
- Unique key log:
  - `fingerprint_device_id`
  - `user_id`
  - `timestamp`
- Filter log:
  - tanggal awal
  - tanggal akhir
  - nama/user ID
  - mesin
- Sidebar menu **Fingerprint**:
  - Mesin Fingerprint
  - Log Absensi
- Akses menu/route dibatasi untuk:
  - `Super Admin`
  - `Operator`
  - `KAUR SDM`

### Integrasi Data Pegawai

Project sudah punya data pegawai lewat:

- `users`
- `master_gurus`

Mapping fingerprint ke pegawai lokal dilakukan ke kolom `app_user_id` dengan percobaan cocok berdasarkan:

- `users.id`
- `master_gurus.nik`
- `master_gurus.nuptk`
- `master_gurus.kode_guru`
- `users.name`
- `master_gurus.nama_lengkap`

Catatan:

- Kolom `user_id` di `fingerprint_users` dan `fingerprint_attendances` menyimpan ID user dari mesin fingerprint.
- Kolom `app_user_id` menyimpan relasi ke user lokal Laravel.

## Perintah Verifikasi Yang Sudah Berhasil

```bash
php artisan migrate --force
php -l app/Http/Controllers/FingerprintController.php
php -l app/Models/FingerprintDevice.php
php -l app/Models/FingerprintUser.php
php -l app/Models/FingerprintAttendance.php
php artisan route:list --name=fingerprint
php artisan view:cache
php -r "require 'vendor/autoload.php'; var_export(class_exists('Rats\\Zkteco\\Lib\\ZKTeco'));"
```

## Cara Lanjut Di PC Rumah

1. Pull/copy project terbaru.

2. Install dependency Composer.

```bash
composer install
```

Jika `rats/zkteco` belum ada:

```bash
composer require rats/zkteco
```

3. Jalankan migration.

```bash
php artisan migrate
```

4. Clear/cache ulang.

```bash
php artisan optimize:clear
php artisan view:cache
```

5. Jalankan server lokal.

```bash
php artisan serve
```

6. Login sebagai `Super Admin`, `Operator`, atau `KAUR SDM`.

7. Buka menu:

```text
Fingerprint -> Mesin Fingerprint
```

8. Tambahkan mesin:

```text
Nama: GF1600
IP Address: 192.168.135.2
Port: 4370
Lokasi: isi sesuai lokasi perangkat
Status: aktif
```

9. Klik:

- **Test Koneksi**
- **Tarik User**
- **Tarik Log**

## Catatan Penting Untuk Test Mesin

- Pastikan PC rumah berada di jaringan yang bisa menjangkau `192.168.135.2`.
- Pastikan firewall tidak memblok UDP/TCP ke port `4370`.
- Package `rats/zkteco` memakai socket UDP di library-nya.
- Jika test koneksi gagal tetapi `fsockopen` berhasil, cek koneksi UDP dari jaringan PC.
- Jangan jalankan sync log berkali-kali dengan khawatir data dobel; sudah memakai `updateOrCreate` dan unique key.

## Potensi Lanjutan

- Tambahkan halaman mapping manual user fingerprint ke pegawai jika nama/ID tidak cocok otomatis.
- Tambahkan export Excel log fingerprint.
- Tambahkan sinkronisasi otomatis via scheduler Laravel.
- Tambahkan tombol clear attendance log mesin jika dibutuhkan, tetapi hati-hati karena menghapus data di perangkat.
- Integrasikan log fingerprint ke modul `AbsensiPegawai` yang sudah ada, bila workflow SDM ingin menyatu.

