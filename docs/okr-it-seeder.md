# Seeder Program Kerja OKR Unit IT TP 2026/2027

Seeder ini memasukkan program kerja dari workbook **OKR Bulanan Dan Mingguan Program Kerja IT TP 2026-2027.xlsx** ke Unit IT pada periode OKR aktif.

## Menjalankan di production

```bash
php artisan db:seed --class=ItOkrProgramSeeder --force
php artisan optimize:clear
```

Seeder aman dijalankan ulang. Target yang sama tidak digandakan dan progres yang sudah diperbarui melalui SISFO tidak dikembalikan ke nilai awal.

## Data yang dibuat

- 11 fokus tahunan Unit IT.
- 22 program bulanan dari Juni 2026 sampai Juli 2027.
- 88 agenda mingguan sebagai turunan program bulanan.
- Progres dan catatan historis Juni–Agustus 2026 sesuai workbook sumber.

## Relasi ke Key Result Sekolah

| Fokus Unit IT | Key Result Sekolah | Dasar keselarasan |
| --- | --- | --- |
| KR-IT.1 Aplikasi inventaris | KR 3.2 Produk Kreatif Digital Siswa | Produk digital operasional sekolah |
| KR-IT.2 Pelabelan aset | KR 4.2 Lingkungan Aman dan Nyaman | Ketertiban dan keterlacakan aset |
| KR-IT.3 SOP aset | KR 4.2 Lingkungan Aman dan Nyaman | Penggunaan aset yang aman dan tertib |
| KR-IT.4 Pemerataan Wi-Fi | KR 1.2 Sertifikasi Kompetensi | Infrastruktur pembelajaran dan asesmen industri |
| KR-IT.5 Stabilitas jaringan | KR 1.2 Sertifikasi Kompetensi | Keberlangsungan pembelajaran dan sertifikasi |
| KR-IT.6 Keandalan CCTV | KR 4.2 Lingkungan Aman dan Nyaman | Dukungan keamanan sekolah |
| KR-IT.7 Roadmap infrastruktur | KR 1.3 Kemitraan dan Sinkronisasi Industri | Infrastruktur selaras kebutuhan industri |
| KR-IT.8 Keamanan dan backup | KR 4.2 Lingkungan Aman dan Nyaman | Perlindungan dan ketersediaan layanan digital |
| KR-IT.9 Helpdesk | KR 4.2 Lingkungan Aman dan Nyaman | Penyelesaian gangguan layanan sekolah |
| KR-IT.10 Optimalisasi SISFO | KR 3.2 Produk Kreatif Digital Siswa | Pengembangan dan adopsi produk digital sekolah |
| KR-IT.11 Website dan landing page SPMB | KR 3.2 Produk Kreatif Digital Siswa | Produk digital layanan informasi sekolah |

Target manual lama dengan nama fokus yang sama akan direkonsiliasi dan digunakan kembali. Contohnya, target **Perawatan dan keandalan jaringan CCTV** tidak dibuat ulang; relasinya diperbaiki ke KR 4.2 dan program bulanan serta mingguan ditambahkan di bawahnya.
