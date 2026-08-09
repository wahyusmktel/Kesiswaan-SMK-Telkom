# API Pembiayaan SPMB

Fitur ini menyediakan pengelolaan rincian biaya SPMB khusus role **Super Admin** dan endpoint publik read-only untuk landing page PPDB.

## Endpoint

```text
GET /api/spmb/fees
```

Respons berisi daftar biaya aktif, tahun pelajaran, total biaya, dan waktu pembaruan terakhir. Endpoint dibatasi 120 request per menit dan memiliki cache publik selama lima menit.

## Deployment SISFO

Tambahkan origin landing page pada `.env` SISFO:

```env
SPMB_CORS_ALLOWED_ORIGINS=https://ppdb.smktelkom-lpg.sch.id
```

Kemudian jalankan:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
```

Menu **Pembiayaan SPMB** tersedia pada navigasi role aktif Super Admin. Migration otomatis membuat lima rincian biaya awal agar endpoint langsung memiliki data.

## Deployment landing page

Isi `.env` proyek landing page:

```env
PUBLIC_SISFO_FEES_API_URL=https://sisfo.smktelkom-lpg.sch.id/api/spmb/fees
```

Jalankan `npm run build`, lalu unggah ulang isi folder `dist`. Landing page mengambil data terbaru saat halaman dibuka dan mempertahankan data cadangan jika API tidak dapat dihubungi.
