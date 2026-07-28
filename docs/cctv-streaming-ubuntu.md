# CCTV Internal dengan MediaMTX

## Arsitektur

Browser tidak membuka RTSP secara langsung. Alur yang dipakai aplikasi:

```text
CCTV (RTSP/H.264) -> MediaMTX (HLS) -> hls.js di SISFO
                           |
                           +-> Laravel HTTP Auth -> token + hak akses pengguna
```

URL RTSP disimpan terenkripsi di database. Browser hanya menerima URL HLS dan token
playback berumur pendek.

## 1. Persiapan kamera

1. Pastikan server Ubuntu dapat menjangkau IP kamera.
2. Buat akun kamera khusus baca, bukan akun administrator kamera.
3. Aktifkan sub-stream H.264. Hindari H.265 untuk kompatibilitas browser.
4. Uji dari server:

```bash
ffprobe -rtsp_transport tcp "rtsp://USER:PASSWORD@IP-KAMERA:554/PATH"
```

Jangan membuka port RTSP kamera ke internet.

## 2. Instal MediaMTX

Contoh berikut memakai MediaMTX `v1.19.3` pada Ubuntu `amd64`. Periksa rilis terbaru
sebelum instalasi jika server dipasang pada waktu berbeda.

```bash
cd /tmp
wget https://github.com/bluenviron/mediamtx/releases/download/v1.19.3/mediamtx_v1.19.3_linux_amd64.tar.gz
tar -xzf mediamtx_v1.19.3_linux_amd64.tar.gz
sudo install -m 0755 mediamtx /usr/local/bin/mediamtx
sudo useradd --system --home /var/lib/mediamtx --shell /usr/sbin/nologin mediamtx || true
sudo install -d -o mediamtx -g mediamtx /var/lib/mediamtx
```

Generate dua secret:

```bash
openssl rand -hex 32
openssl rand -hex 32
```

Secret pertama dipakai sebagai `CCTV_GATEWAY_AUTH_KEY`, secret kedua sebagai
`CCTV_PLAYBACK_TOKEN_SECRET`.

## 3. Konfigurasi MediaMTX

Buat `/etc/mediamtx.yml`:

```yaml
logLevel: info
logDestinations: [stdout]

authMethod: http
authHTTPAddress: http://127.0.0.1/api/cctv/gateway/auth?key=GANTI_DENGAN_GATEWAY_AUTH_KEY
authHTTPExclude:
  - action: api
  - action: metrics
  - action: pprof

api: true
apiAddress: 127.0.0.1:9997

rtsp: false
rtmp: false
webrtc: false
srt: false
moq: false
playback: false

hls: true
hlsAddress: 127.0.0.1:8888
hlsAllowOrigins: ["https://sisfo.smktelkom-lpg.id"]
hlsAlwaysRemux: false
hlsVariant: lowLatency
hlsSegmentCount: 7
hlsSegmentDuration: 1s
hlsPartDuration: 200ms

pathDefaults:
  sourceOnDemand: true
  sourceOnDemandStartTimeout: 15s
  sourceOnDemandCloseAfter: 30s
  rtspTransport: tcp

paths: {}
```

Jika request lokal ke `http://127.0.0.1/api/...` tidak masuk ke virtual host Laravel,
gunakan `http://127.0.0.1:PORT/api/...` sesuai listener Nginx lokal, atau buat server
block default yang menunjuk ke SISFO.

Buat `/etc/systemd/system/mediamtx.service`:

```ini
[Unit]
Description=MediaMTX CCTV Gateway
After=network-online.target
Wants=network-online.target

[Service]
Type=simple
User=mediamtx
Group=mediamtx
WorkingDirectory=/var/lib/mediamtx
ExecStart=/usr/local/bin/mediamtx /etc/mediamtx.yml
Restart=always
RestartSec=3
NoNewPrivileges=true
PrivateTmp=true
ProtectSystem=strict
ProtectHome=true
ReadWritePaths=/var/lib/mediamtx

[Install]
WantedBy=multi-user.target
```

Aktifkan:

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now mediamtx
sudo systemctl status mediamtx --no-pager
curl http://127.0.0.1:9997/v3/info
```

## 4. Environment Laravel

Tambahkan ke `.env` produksi:

```dotenv
CCTV_MEDIAMTX_API_URL=http://127.0.0.1:9997
CCTV_MEDIAMTX_API_USER=
CCTV_MEDIAMTX_API_PASSWORD=
CCTV_HLS_BASE_URL=https://cctv-media.smktelkom-lpg.id
CCTV_GATEWAY_AUTH_KEY=SECRET_PERTAMA
CCTV_PLAYBACK_TOKEN_SECRET=SECRET_KEDUA
CCTV_PLAYBACK_TOKEN_TTL=900
```

Jalankan:

```bash
cd /var/www/Kesiswaan-SMK-Telkom
php artisan migrate --force
php artisan optimize:clear
php artisan cctv:sync
npm ci
npm run build
```

Scheduler Laravel harus aktif karena `cctv:sync` dijalankan setiap lima menit:

```cron
* * * * * cd /var/www/Kesiswaan-SMK-Telkom && php artisan schedule:run >> /dev/null 2>&1
```

## 5. Endpoint media

### Opsi A: jaringan sekolah atau VPN (direkomendasikan)

Publikasikan port HLS hanya ke VLAN pengguna atau VPN. Gunakan HTTPS yang dipercaya
browser, lalu isi `CCTV_HLS_BASE_URL` dengan endpoint tersebut. Firewall harus menolak
akses dari jaringan lain.

### Opsi B: Cloudflare Tunnel

Secara teknis HLS dapat diteruskan oleh tunnel:

```yaml
ingress:
  - hostname: cctv-media.smktelkom-lpg.id
    service: http://127.0.0.1:8888
  - service: http_status:404
```

Setelah mengubah konfigurasi:

```bash
sudo systemctl restart cloudflared
cloudflared tunnel ingress validate
```

Cloudflare membatasi penyajian video melalui layanan CDN standar pada paket tertentu.
Untuk tayangan internet dalam skala besar, gunakan Cloudflare Stream atau layanan media
yang memang diizinkan. Untuk CCTV internal, LAN/VPN lebih tepat dan mengurangi paparan
data pribadi.

## 6. Operasional di SISFO

1. Masuk sebagai Super Admin.
2. Buka **Manajemen CCTV**.
3. Tambahkan nama, lokasi, dan URL RTSP kamera.
4. Pilih pengguna non-siswa yang mendapat akses.
5. Klik **Sinkronkan Semua** setelah MediaMTX dipasang atau di-restart.
6. Pengguna terpilih akan melihat menu **CCTV Live**.

Jika video tidak tampil, periksa:

```bash
sudo journalctl -u mediamtx -n 200 --no-pager
php artisan cctv:sync
curl http://127.0.0.1:9997/v3/config/paths/list
ffprobe -rtsp_transport tcp "rtsp://USER:PASSWORD@IP-KAMERA:554/PATH"
```

Penyebab umum adalah URL RTSP salah, server tidak satu rute dengan VLAN kamera, codec
H.265, keyframe terlalu jarang, atau endpoint HLS diblokir firewall/CORS.

## Referensi

- [MediaMTX configuration reference](https://mediamtx.org/docs/references/configuration-file)
- [MediaMTX authentication](https://mediamtx.org/docs/features/authentication)
- [MediaMTX browser/HLS embedding](https://mediamtx.org/docs/read/web-browsers)
- [Cloudflare video delivery policy](https://developers.cloudflare.com/fundamentals/reference/policies-compliances/delivering-videos-with-cloudflare/)
