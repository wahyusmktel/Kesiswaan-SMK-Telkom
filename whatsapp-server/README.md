# SISFO WhatsApp Gateway

Engine lokal WhatsApp berbasis Baileys. Laravel menggunakan engine ini untuk
membuat sesi, menerima QR pairing, memeriksa status, dan mengirim notifikasi.

## Arsitektur production

- Laravel/Nginx tetap melayani aplikasi publik.
- Engine Node hanya mendengarkan `127.0.0.1:3001`.
- Laravel dan engine Node memakai API key yang sama.
- Kredensial perangkat tersimpan di `whatsapp-server/sessions-auth` dan tidak
  boleh masuk Git.
- PM2 menjalankan satu instance dalam mode fork. Jangan memakai cluster karena
  sesi Baileys disimpan di memori dan filesystem.

## Instalasi Ubuntu

Jalankan dari root proyek:

```bash
cd /var/www/Kesiswaan-SMK-Telkom/whatsapp-server
npm ci --omit=dev
sudo npm install -g pm2
```

Buat API key:

```bash
openssl rand -hex 32
```

Tambahkan hasilnya ke `.env` Laravel:

```dotenv
WHATSAPP_GATEWAY_URL=http://127.0.0.1:3001
WHATSAPP_GATEWAY_API_KEY=HASIL_OPENSSL_YANG_SAMA
```

Muat ulang konfigurasi Laravel:

```bash
cd /var/www/Kesiswaan-SMK-Telkom
php artisan optimize:clear
php artisan config:cache
```

Jalankan engine dengan nilai API key yang sama:

```bash
cd /var/www/Kesiswaan-SMK-Telkom/whatsapp-server
export WHATSAPP_GATEWAY_API_KEY='HASIL_OPENSSL_YANG_SAMA'
export LARAVEL_BASE_URL='https://sisfo.smktelkom-lpg.id'
pm2 start ecosystem.config.cjs
pm2 save
pm2 startup systemd -u "$USER" --hp "$HOME"
```

Perintah `pm2 startup` menampilkan satu perintah `sudo env ...`. Jalankan
perintah tersebut, lalu jalankan `pm2 save` sekali lagi.

## Pemeriksaan

```bash
pm2 status
pm2 logs sisfo-whatsapp-gateway --lines 100
curl http://127.0.0.1:3001/health
```

Health check normal:

```json
{"success":true,"service":"sisfo-whatsapp-gateway","active_sessions":0}
```

Setelah engine aktif, buka **Super Admin > WhatsApp Gateway**, tambahkan
perangkat dengan provider **Node.js Baileys Local Engine**, gunakan server URL
`http://127.0.0.1:3001`, lalu klik **Hubungkan QR**.

## Deployment berikutnya

Setelah `git pull`:

```bash
cd /var/www/Kesiswaan-SMK-Telkom/whatsapp-server
npm ci --omit=dev
pm2 restart sisfo-whatsapp-gateway --update-env
pm2 save
```

Jangan menghapus `sessions-auth` saat deploy. Menghapus direktori tersebut
mengharuskan perangkat melakukan scan QR ulang.

## QR tidak muncul dan log menunjukkan kode 405

Versi engine saat ini mengambil versi WhatsApp Web terbaru saat membuka socket.
Jika sesi pairing lama sudah rusak, hentikan engine lalu hapus hanya direktori
sesi yang gagal:

```bash
pm2 stop sisfo-whatsapp-gateway
rm -rf sessions-auth/ID_SESI_YANG_GAGAL
pm2 start sisfo-whatsapp-gateway
pm2 logs sisfo-whatsapp-gateway --lines 100
```

Jangan menghapus direktori sesi lain yang masih berstatus terhubung. Setelah
engine hidup, klik **Hubungkan QR** kembali dari panel.
