# Persetujuan izin Pegawai Tetap

Deploy dengan `php artisan migrate --force`, lalu `php artisan optimize:clear`.

Status kepegawaian dibaca dari Dapodik saat tahap sebelumnya menyetujui izin. Pegawai Tetap diteruskan ke menu **Persetujuan Izin Pegawai** pada Kepala Sekolah. Kategori luar melewati Piket, Kurikulum, SDM, lalu Kepala Sekolah. Kategori terlambat melewati SDM lalu Kepala Sekolah. Kategori lingkungan sekolah mengikuti jalur Piket lalu Kepala Sekolah yang mempertahankan mekanisme pelompatan tahap kategori tersebut.

Keputusan menyimpan pemberi keputusan, waktu, dan alasan penolakan. Izin yang masih menunggu Kepala Sekolah belum sah untuk pengecualian fingerprint, sinkronisasi absensi mengajar, atau cetak surat izin. Riwayat keputusan tersedia melalui filter Disetujui/Ditolak.

Migrasi mempertahankan izin yang telah disetujui sebelum pembaruan sebagai keputusan final lama (`tidak_diperlukan`). Izin lama yang belum selesai diproses akan mengikuti aturan baru saat disetujui SDM/Piket.
