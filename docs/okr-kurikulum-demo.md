# Panduan Demo OKR dan Rapat Pekanan Kurikulum

## Menyiapkan data contoh

Setelah kode terbaru terpasang di server, jalankan:

```bash
php artisan migrate --force
php artisan db:seed --class=CurriculumOkrDemoSeeder --force
```

Seeder akan menyinkronkan Matriks OKR Sekolah, memasukkan 16 Objektif Unit dan 41 Key Result Unit Kurikulum, lalu membuat contoh laporan pekan 7–11 September 2026 dengan status **Menunggu Tinjauan**. Seeder aman dijalankan ulang: data tidak digandakan, progres OKR yang sudah diisi tidak dikembalikan ke nol, dan laporan yang sudah direview tidak dibuka kembali.

## Alur yang dijelaskan saat presentasi

1. **Arah sekolah ditetapkan oleh Kepala Sekolah.** Objective dan Key Result Sekolah menjadi sasaran induk bersama.
2. **Unit menerjemahkan arah sekolah menjadi target unit.** Pada contoh Kurikulum, Objective Unit menjadi rencana tahunan dan Key Result Unit menjadi target terukur turunannya. Target, tingkat kesulitan, strategi, PIC, unit terkait, waktu, serta catatan berasal dari matriks OKR Kurikulum.
3. **Senin: unit membuat komitmen pekanan.** Kepala Unit memilih fokus OKR, menetapkan maksimal tiga komitmen, target terukur, ketergantungan lintas unit, dan kebutuhan persetujuan.
4. **Selama pekan: pelaksanaan dan bukti dikumpulkan.** Setiap komitmen dapat dihubungkan dengan target OKR yang relevan sehingga pekerjaan operasional tetap memiliki hubungan dengan sasaran sekolah.
5. **Jumat: unit melakukan evaluasi.** Unit mengisi capaian aktual, persentase penyelesaian, status, kendala, dan tindak lanjut untuk pekan berikutnya, lalu mengirim laporan.
6. **Kepala Sekolah memonitor seluruh unit.** Dashboard menampilkan kepatuhan pelaporan, status pencapaian, tren beberapa pekan, dan rincian komitmen tiap unit.
7. **Kepala Sekolah memberi review.** Setelah direview, laporan terkunci sehingga menjadi rekam jejak akuntabilitas rapat, bukan file yang dapat berubah tanpa jejak.

## Contoh narasi singkat

> Sebelumnya, rencana Senin dan evaluasi Jumat tersimpan pada lembar Excel terpisah. Sekarang arah sekolah diturunkan ke OKR Unit, kemudian diterjemahkan menjadi tiga komitmen pekanan. Pada Jumat, capaian aktual dibandingkan langsung dengan janji Senin. Kepala Sekolah tidak perlu menggabungkan file dari setiap unit karena status pelaporan, capaian, kendala, dan tindak lanjut sudah diringkas oleh SISFO dalam satu dashboard.

## Urutan demonstrasi di SISFO

1. Masuk sebagai **Kurikulum**, buka **Manajemen OKR**, lalu pilih Unit Kurikulum untuk menunjukkan 16 Objective dan 41 Key Result.
2. Tekan **Rapat Pekanan**, pilih pekan 7 September 2026, lalu tunjukkan hubungan antara Program Melanjutkan dan target OKR terkait.
3. Tunjukkan tiga komitmen Senin, capaian aktual Jumat, kendala, persentase, serta tindak lanjut.
4. Masuk sebagai **Kepala Sekolah**, buka **Rapat Pekanan**, lalu tunjukkan ringkasan seluruh unit dan detail Kurikulum.
5. Isi catatan review dan selesaikan tinjauan untuk memperlihatkan proses penguncian laporan.

## Catatan penggunaan berikutnya

- Data pekan 7–11 September 2026 adalah contoh demonstrasi yang berasal dari laporan manual Kurikulum.
- Untuk pekan berikutnya, unit mengisi langsung di SISFO; tidak perlu membuat salinan Excel baru.
- Persentase pada laporan pekanan menggambarkan penyelesaian komitmen pekan tersebut. Progres utama OKR tetap dicatat melalui pembaruan progres OKR agar setiap perubahan disertai nilai, evaluasi, tanggal, dan bukti yang jelas.
