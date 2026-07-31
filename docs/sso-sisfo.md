# SSO SISFO

SISFO bertindak sebagai OAuth 2.0 Authorization Server. Aplikasi lain menggunakan Authorization Code Grant dengan PKCE dan mengambil profil pengguna melalui access token.

## Endpoint Produksi

| Fungsi | URL |
| --- | --- |
| Login dan persetujuan | `https://sso.smktelkom-lpg.id/oauth/authorize` |
| Penukaran token | `https://sso.smktelkom-lpg.id/oauth/token` |
| Profil pengguna | `https://sso.smktelkom-lpg.id/api/sso/user` |
| Login khusus SSO | `https://sso.smktelkom-lpg.id/masuk` |

Scope yang tersedia adalah `profile:read`. Endpoint profil mengembalikan `sub`, `name`, `email`, `email_verified`, `picture`, dan `roles`.

## Persiapan Produksi SISFO

Jalankan dari direktori proyek setelah deployment:

```bash
cd /var/www/Kesiswaan-SMK-Telkom
composer install --no-dev --optimize-autoloader
php artisan migrate --force
test -f storage/oauth-private.key || php artisan passport:keys
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl reload php8.3-fpm
```

Kunci `storage/oauth-private.key` wajib hanya dapat dibaca user web server. Jangan masukkan kedua kunci Passport ke Git:

Jangan menjalankan `passport:keys --force` pada deployment rutin karena rotasi kunci akan membuat seluruh access token yang masih aktif tidak dapat diverifikasi.

```bash
sudo chown www-data:www-data storage/oauth-private.key storage/oauth-public.key
sudo chmod 600 storage/oauth-private.key
sudo chmod 644 storage/oauth-public.key
```

Tambahkan ke `.env` produksi:

```dotenv
SSO_URL=https://sso.smktelkom-lpg.id
SSO_DOMAIN=sso.smktelkom-lpg.id
SSO_ENFORCE_DOMAIN=true
SSO_GOOGLE_REDIRECT_URL=https://sso.smktelkom-lpg.id/auth/google/callback
SSO_GOOGLE_WORKSPACE_DOMAIN=smktelkom-lpg.sch.id
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

Biarkan `SESSION_DOMAIN=null` agar cookie login utama dan cookie SSO terisolasi per host. Ubah domain Google Workspace sesuai domain email resmi sekolah bila berbeda.

## Cloudflare Tunnel

Subdomain SSO dapat memakai tunnel dan origin Nginx yang sama dengan SISFO.

1. Buka Cloudflare Zero Trust, pilih **Networks > Tunnels**, lalu buka tunnel server SISFO.
2. Tambahkan **Published application route / Public hostname**.
3. Isi subdomain `sso`, domain `smktelkom-lpg.id`, tipe service `HTTP`, dan URL origin yang sama dengan SISFO, misalnya `http://127.0.0.1:80`.
4. Jangan pasang Cloudflare Access di depan hostname SSO karena endpoint OAuth harus dapat dijangkau aplikasi klien.
5. Pastikan virtual host Nginx menerima kedua hostname.

Contoh Nginx:

```nginx
server {
    listen 80;
    server_name sisfo.smktelkom-lpg.id sso.smktelkom-lpg.id;

    root /var/www/Kesiswaan-SMK-Telkom/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
}
```

Validasi lalu reload:

```bash
sudo nginx -t
sudo systemctl reload nginx
curl -I https://sso.smktelkom-lpg.id/masuk
```

Pada Google Cloud Console, tambahkan `https://sso.smktelkom-lpg.id/auth/google/callback` ke **Authorized redirect URIs** pada OAuth Client yang dipakai SISFO.

## Mendaftarkan Aplikasi

Masuk sebagai Super Admin, buka **Aplikasi SSO**, lalu isi nama aplikasi dan Redirect URI. Pilih:

- `Public + PKCE` untuk Flutter, mobile, SPA, dan aplikasi baru. Tidak ada client secret.
- `Confidential` hanya untuk backend server yang dapat menjaga client secret.

Redirect URI harus sama persis dengan callback yang dikirim aplikasi. HTTPS wajib kecuali `localhost` saat pengembangan.

## Alur PKCE Aplikasi Klien

1. Buat `code_verifier` acak sepanjang 43-128 karakter.
2. Hitung `code_challenge = BASE64URL(SHA256(code_verifier))`.
3. Arahkan browser ke endpoint authorize dengan `client_id`, `redirect_uri`, `response_type=code`, `scope=profile:read`, `state`, `code_challenge`, dan `code_challenge_method=S256`.
4. Setelah callback, validasi `state` lalu tukar `code` di endpoint token dengan `code_verifier` yang asli.
5. Kirim access token sebagai `Authorization: Bearer TOKEN` ke endpoint profil.

Contoh request authorize:

```text
https://sso.smktelkom-lpg.id/oauth/authorize?client_id=CLIENT_ID&redirect_uri=https%3A%2F%2Fapp.example.id%2Fauth%2Fcallback&response_type=code&scope=profile%3Aread&state=RANDOM_STATE&code_challenge=CHALLENGE&code_challenge_method=S256
```

Contoh penukaran token:

```bash
curl -X POST https://sso.smktelkom-lpg.id/oauth/token \
  -H 'Accept: application/json' \
  -d 'grant_type=authorization_code' \
  -d 'client_id=CLIENT_ID' \
  -d 'redirect_uri=https://app.example.id/auth/callback' \
  -d 'code=AUTHORIZATION_CODE' \
  -d 'code_verifier=ORIGINAL_CODE_VERIFIER'
```

Contoh profil:

```bash
curl https://sso.smktelkom-lpg.id/api/sso/user \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer ACCESS_TOKEN'
```

Access token berlaku satu jam dan refresh token berlaku 30 hari. Jangan simpan token di log, URL, atau penyimpanan browser yang dapat dibaca skrip pihak ketiga.
