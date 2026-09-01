# Repository Bahan Praktikum - Konfigurasi Production

Fitur repository menyimpan file di storage privat. Browser Super Admin mengirim file dalam potongan 8 MB, sedangkan siswa mengunduh melalui token UUID publik. Untuk unduhan massal, Nginx sebaiknya mengirim file langsung menggunakan `X-Accel-Redirect` agar worker PHP-FPM tetap tersedia untuk operasional SISFO.

## 1. Environment

Tambahkan atau sesuaikan nilai berikut di `.env` production:

```dotenv
REPOSITORY_LOCAL_URL=http://IP-LOKAL-SERVER-SISFO
REPOSITORY_PUBLIC_URL=https://sisfo.smktelkom-lpg.id
REPOSITORY_STORAGE_PATH=/var/www/sisfo/storage/app/repository
REPOSITORY_CHUNK_SIZE=8388608
REPOSITORY_MAX_FILE_SIZE=107374182400
REPOSITORY_UPLOAD_TTL_HOURS=24
REPOSITORY_DOWNLOAD_DRIVER=nginx
REPOSITORY_ACCEL_REDIRECT_PREFIX=/_protected_repository
```

`REPOSITORY_LOCAL_URL` juga dapat diubah dari halaman Super Admin > Repository. IP tersebut harus mengarah ke vhost SISFO yang sama dari jaringan laboratorium.

## 2. Direktori dan izin

Contoh untuk deployment dengan user PHP-FPM `www-data`:

```bash
sudo install -d -o www-data -g www-data -m 0750 /var/www/sisfo/storage/app/repository
sudo install -d -o www-data -g www-data -m 0750 /var/www/sisfo/storage/app/repository/files
```

Jika file besar disimpan pada disk atau mount terpisah, arahkan `REPOSITORY_STORAGE_PATH` ke mount tersebut dan pastikan user PHP-FPM memiliki izin baca/tulis.

## 3. Nginx

Salin konfigurasi dari `docs/repository-nginx.conf.example` ke dalam blok `server {}` domain publik dan vhost/IP LAN. Sesuaikan nilai `alias` dengan `REPOSITORY_STORAGE_PATH` dan pertahankan akhiran `/`.

Nilai penting:

```nginx
client_max_body_size 12m;

location /_protected_repository/ {
    internal;
    alias /var/www/sisfo/storage/app/repository/;
    sendfile on;
    tcp_nopush on;
    open_file_cache max=1000 inactive=60s;
    open_file_cache_valid 120s;
    open_file_cache_min_uses 2;
}
```

`client_max_body_size` hanya perlu sedikit lebih besar dari ukuran chunk, bukan sebesar ISO. Location wajib `internal` agar file tidak dapat diakses dengan menebak path storage.

Validasi dan reload:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

## 4. Deploy aplikasi

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
```

Pastikan scheduler Laravel aktif karena sesi upload yang gagal atau ditinggalkan dibersihkan setiap hari pukul 02:30 oleh perintah `repository:cleanup-uploads`.

## 5. Verifikasi

1. Buka Super Admin > Repository.
2. Simpan URL lokal dan publik.
3. Upload file uji berukuran kecil.
4. Uji link publik dari internet dan link lokal dari komputer laboratorium.
5. Periksa response header. Mode optimal harus mengandung `X-Accel-Redirect` dari Laravel, kemudian Nginx melayani file dengan dukungan Range request.

Jika blok Nginx belum dipasang, gunakan sementara `REPOSITORY_DOWNLOAD_DRIVER=laravel`. Mode tersebut hemat memori dan mendukung Range request, tetapi koneksi unduhan masih memakai worker aplikasi sehingga tidak direkomendasikan untuk unduhan massal jangka panjang.
