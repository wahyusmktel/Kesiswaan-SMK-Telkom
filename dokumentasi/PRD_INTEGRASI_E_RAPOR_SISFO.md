# PRD Integrasi e-Rapor SMK ke SISFO

Status: Draft awal v0.1  
Basis analisis: e-Rapor SMK `8.0.3` (versi database `6.0.6`) dan kondisi SISFO per 25 Juli 2026.

## Status Implementasi

- [x] Fase 0: landing page, permission lihat, dan pemeriksaan kesiapan data SISFO.
- [x] Fondasi referensi: manifest versi/checksum, import JSON idempoten, katalog kurikulum/mapel, dan relasi kurikulum-mapel.
- [x] Fondasi akademik: pemetaan mapel SISFO dan penugasan permanen guru-mapel-rombel yang disinkronkan dari jadwal.
- [x] Konfigurasi kurikulum aktif per periode, kurikulum per rombel, dan gerbang kesiapan pembukaan penilaian.
- [ ] Rencana asesmen, input nilai, kalkulasi, validasi, dan penerbitan rapor.

## 1. Executive Summary

### Problem Statement

SISFO telah memiliki data guru, siswa, kelas, rombel, jadwal, tahun pelajaran, PKL, UKK, absensi, prestasi, dan akun pengguna, tetapi belum memiliki alur e-Rapor yang menyatukan perencanaan penilaian, input nilai, validasi wali/kurikulum, dan penerbitan rapor. Aplikasi e-Rapor SMK sumber masih berdiri sendiri, memakai PostgreSQL, Vue 3/Vuetify, serta sinkronisasi Dapodik/Synchronizer untuk mengisi data induknya.

### Proposed Solution

Bangun e-Rapor sebagai modul native di Laravel SISFO dengan autentikasi, role, UI, database MySQL, dan data induk SISFO sebagai sumber kebenaran tunggal. Aturan bisnis e-Rapor diadaptasi, referensi JSON yang relevan diimpor secara terkontrol dan berversi, sedangkan pengambilan data dari Dapodik dihilangkan dari alur operasional.

Tahap pembuka adalah landing page internal “e-Rapor SISFO” yang menampilkan kesiapan data, periode aktif, progres penilaian, dan pintasan sesuai peran. Implementasi berikutnya bergerak per alur kerja sampai satu rapor dapat diterbitkan tanpa membuat salinan guru atau siswa.

### Success Criteria

- 100% guru, siswa aktif, rombel, dan anggota rombel yang dipakai e-Rapor mengacu ke ID data induk SISFO.
- Tidak ada panggilan Dapodik/Synchronizer dalam alur normal e-Rapor SISFO.
- Minimal 99% pasangan guru–mapel–rombel aktif berhasil dipetakan atau memiliki alasan konflik yang dapat ditindaklanjuti.
- Pada MVP, satu kelas pilot dapat menyelesaikan alur penugasan, input nilai, validasi, dan cetak rapor PDF end-to-end.
- Nilai pada PDF yang diterbitkan sama dengan nilai hasil perhitungan tersimpan pada 100% sampel uji.

### Asumsi dan Keputusan yang Masih TBD

- Asumsi awal: modul mengikuti stack SISFO (Laravel 12, Blade/Alpine/Tailwind, MySQL), bukan menanam SPA Vue e-Rapor sebagai aplikasi kedua.
- TBD: prioritas tahap pertama hanya landing page + kesiapan data, atau langsung satu kelas pilot sampai cetak.
- TBD: kurikulum yang aktif di sekolah dan format rapor resmi yang harus didukung pertama.
- TBD: kebutuhan migrasi nilai historis dari instalasi e-Rapor lama, jika ada.

## 2. User Experience & Functionality

### User Personas

- **Kurikulum/Operator**: mengaktifkan periode, kurikulum, referensi, penugasan mengajar, aturan nilai, dan memonitor kelengkapan.
- **Guru Mata Pelajaran**: menyusun tujuan/rencana penilaian, menginput atau mengimpor nilai, dan mengirim nilai final.
- **Wali Kelas**: mengisi pelengkap rapor, memeriksa kelengkapan, dan mengajukan penerbitan.
- **Kepala Sekolah**: memantau dan mengesahkan rapor sesuai kewenangan.
- **Siswa/Orang Tua**: melihat atau mengunduh rapor yang sudah diterbitkan sesuai kebijakan sekolah.
- **Super Admin**: mengelola permission, referensi, audit, dan pemulihan kesalahan.

### User Flow Utama

1. Pengguna membuka landing page e-Rapor SISFO.
2. Sistem memeriksa periode aktif dan kesiapan data induk.
3. Kurikulum memetakan kurikulum, mapel, penugasan guru, rombel, serta KKM/KKTP.
4. Guru membuat rencana asesmen/tujuan pembelajaran dan mengisi nilai siswa.
5. Sistem menghitung nilai akhir dan deskripsi secara konsisten.
6. Guru mengunci serta mengirim nilai.
7. Wali kelas melengkapi absensi, ekskul, prestasi, catatan, kenaikan, PKL/P5 yang berlaku.
8. Kurikulum/wali memvalidasi kelengkapan dan menangani pengecualian.
9. Rapor diterbitkan sebagai snapshot, diberi nomor/versi, lalu dicetak atau diunduh.

### User Stories dan Acceptance Criteria

#### Story A — Landing Page e-Rapor

Sebagai pengguna, saya ingin melihat status e-Rapor sesuai peran agar saya mengetahui pekerjaan yang harus diselesaikan.

**Acceptance Criteria**

- Halaman tersedia di dalam autentikasi SISFO dan memakai layout SISFO.
- Menampilkan tahun pelajaran/semester aktif, status konfigurasi, jumlah konflik data, progres nilai, dan rapor diterbitkan.
- Tombol aksi hanya tampil jika pengguna memiliki permission terkait.
- Jika data dasar belum siap, halaman menunjukkan item masalah dan tautan perbaikannya, bukan gagal secara umum.

#### Story B — Kesiapan dan Pemetaan Data

Sebagai operator kurikulum, saya ingin memvalidasi data SISFO terhadap kebutuhan e-Rapor agar penilaian tidak memakai data ganda atau salah.

**Acceptance Criteria**

- Pemeriksaan mencakup guru, siswa aktif, anggota rombel, wali kelas, mapel, penugasan, periode, dan identitas sekolah.
- Sistem menyediakan status `siap`, `peringatan`, atau `blokir` beserta penyebab per record.
- Pemetaan dapat dijalankan ulang secara idempoten tanpa menduplikasi data.
- Perubahan data induk dicatat dan tidak mengubah rapor yang sudah diterbitkan.

#### Story C — Referensi Kurikulum

Sebagai kurikulum, saya ingin mengimpor dan memilih referensi yang relevan agar guru dapat memakai struktur kurikulum resmi tanpa memenuhi database dengan data yang tidak digunakan.

**Acceptance Criteria**

- Import menyimpan sumber, versi, waktu, checksum, jumlah record, hasil validasi, dan pengguna pelaksana.
- Import menggunakan upsert berdasarkan ID referensi sumber dan tidak menghapus data lokal secara otomatis.
- Referensi dapat diaktifkan/nonaktifkan per periode sekolah.
- Hanya subset SMK/kurikulum aktif yang masuk ke katalog operasional; arsip sumber tetap dapat dilacak.

#### Story D — Penugasan dan Input Nilai

Sebagai guru, saya ingin melihat mapel dan rombel yang benar serta mengisi nilai secara massal agar proses penilaian efisien.

**Acceptance Criteria**

- Hak input berasal dari penugasan guru–mapel–rombel–periode, bukan dari slot jadwal tunggal.
- Mendukung input tabel dan impor template dengan preview serta laporan baris gagal.
- Rentang, presisi, komponen, bobot, dan aturan nilai divalidasi server-side.
- Penyimpanan bersifat draft sampai guru mengirim; setiap perubahan memiliki audit actor dan timestamp.

#### Story E — Validasi dan Penerbitan

Sebagai wali kelas/kurikulum, saya ingin memvalidasi kelengkapan dan menerbitkan rapor agar dokumen final konsisten serta dapat diaudit.

**Acceptance Criteria**

- Rapor tidak dapat diterbitkan jika komponen wajib belum lengkap, kecuali override beralasan oleh permission khusus.
- Nilai final, deskripsi, identitas, struktur mapel, dan data pelengkap dibekukan sebagai snapshot penerbitan.
- Revisi menghasilkan versi baru; versi sebelumnya tetap tersimpan.
- PDF memiliki identitas sekolah, siswa, periode, nomor versi, waktu terbit, dan checksum/QR verifikasi bila diaktifkan.

### Non-Goals

- Mengoperasikan e-Rapor sumber sebagai aplikasi kedua, database kedua, atau iframe.
- Mengambil atau mengirim data langsung ke Dapodik pada MVP.
- Menyalin seluruh tampilan Vuexy/Vuetify secara pixel-perfect.
- Mengimpor semua referensi wilayah nasional bila tidak dibutuhkan rapor.
- Migrasi penuh seluruh riwayat e-Rapor lama pada tahap awal.
- Dukungan multi-sekolah sebelum kebutuhan SISFO berubah menjadi multi-tenant.

## 3. AI System Requirements (If Applicable)

Tidak ada AI yang diperlukan untuk MVP. Perhitungan nilai dan deskripsi wajib deterministik, dapat diuji, dan tidak bergantung pada model generatif.

Fitur AI untuk saran deskripsi rapor dapat dipertimbangkan setelah v2.0, dengan syarat guru selalu menyetujui hasil, data siswa diminimalkan, prompt/output diaudit, dan hasil AI tidak pernah mengubah nilai numerik.

## 4. Technical Specifications

### Hasil Analisis Aplikasi

| Area | e-Rapor SMK 8.0.3 | SISFO | Implikasi |
|---|---|---|---|
| Backend | Laravel 11, PHP 8.2 | Laravel 12, PHP 8.2 | Port aturan bisnis, bukan copy aplikasi penuh |
| Frontend | Vue 3, Vuetify, Pinia, SPA | Blade, Alpine, Tailwind, Vite | UI e-Rapor mengikuti design system SISFO |
| Database | PostgreSQL; schema `ref` dan `dapodik`; view SQL khusus PG | MySQL | Migrasi e-Rapor tidak dapat dijalankan langsung |
| Auth/RBAC | Sanctum + Laratrust + team semester | Sanctum + Spatie Permission | Gunakan akun dan role SISFO; tambah permission granular |
| PDF/Excel | mPDF, FastExcel, Laravel Excel | Dompdf/FPDF/FPDI, Laravel Excel | Pilih satu renderer melalui proof-of-concept format rapor |
| Data induk | Salinan dari Dapodik/Synchronizer | Sudah tersedia di SISFO | SISFO menjadi sumber kebenaran |

Audit menemukan 201 file migrasi e-Rapor dan sekitar 91 tabel termasuk tabel referensi. e-Rapor juga memakai PostgreSQL view untuk agregasi nilai dan pencarian `ILIKE`; logika tersebut perlu ditulis ulang sebagai service/query yang kompatibel dengan MySQL dan diuji dengan fixture perhitungan.

### Cakupan Domain e-Rapor yang Diadaptasi

- Referensi: kurikulum, jurusan, kelompok mapel, mapel nasional, pemetaan mapel-kurikulum, KD/CP/TP, teknik penilaian, sikap.
- Akademik: penugasan pembelajaran, rencana asesmen, nilai per komponen, nilai akhir, deskripsi capaian, remedial, PTS bila digunakan.
- Pelengkap: absensi, catatan wali, ekskul, prestasi, kenaikan kelas.
- Kekhasan SMK: PKL/prakerin, UKK, asesor, DUDI, sertifikat.
- Monitoring dan keluaran: progres kelengkapan, leger, cover, rapor akademik/pelengkap, rapor P5/PKL, audit penerbitan.

### Arsitektur Overview

```text
UI e-Rapor dalam SISFO
        |
Policy + Permission SISFO
        |
Application Services e-Rapor
   |         |          |
Master SISFO | Referensi berversi | Transaksi + snapshot rapor
   |         |          |
MySQL SISFO (satu database dan satu autentikasi)
        |
PDF / Excel / Audit Log
```

Prinsip arsitektur:

- Gunakan bounded module, misalnya namespace `App\Erapor` dan prefix tabel `erapor_`.
- Master SISFO dibaca lewat model/kontrak domain; jangan membuat tabel salinan `guru` atau `peserta_didik`.
- Semua transaksi rapor memiliki `tahun_pelajaran_id` dan konteks semester.
- Nilai disimpan sebagai decimal, bukan integer, agar aturan pembulatan eksplisit.
- Perhitungan ditempatkan pada service deterministik; hindari ketergantungan pada database view PostgreSQL.
- Penerbitan menggunakan snapshot immutable agar perubahan nama, kelas, atau mapel tidak mengubah dokumen lama.
- Pembuatan PDF besar dijalankan melalui queue dan aman untuk retry/idempoten.

### Data Ownership dan Mapping Awal

| Konsep e-Rapor | Sumber/Target SISFO | Keputusan |
|---|---|---|
| `users` | `users` | Pakai akun SISFO |
| `guru` / PTK | `master_gurus` + `users` | Pakai ID SISFO; NUPTK/NIK hanya kunci bantu |
| `peserta_didik` | `master_siswa` + `users` | Pakai ID SISFO; lengkapi NISN/NIK bila format rapor mewajibkan |
| `rombongan_belajar` | `kelas`, `rombels` | Pakai rombel per tahun pelajaran |
| `anggota_rombel` | `rombel_siswa` | Jadikan keanggotaan per periode eksplisit |
| `semester` | `tahun_pelajaran` | Normalisasi tahun + semester; hanya satu periode aktif |
| `pembelajaran` | entitas baru `erapor_teaching_assignments` | Tidak memakai `jadwal_pelajarans` langsung |
| `mata_pelajaran` | `mata_pelajarans` + mapping referensi | Mapel lokal tetap operasional; simpan pasangan ID referensi |
| nilai ujian semester | `nilai_ujian_semesters` | Dapat menjadi sumber komponen/final melalui aturan mapping, bukan dipindah |
| absensi | data absensi/perizinan SISFO + snapshot rapor | Agregasi otomatis dengan override beralasan |
| prestasi | `siswa_prestasis` | Reuse setelah validasi kategori/periode |
| PKL/DUDI | modul `Prakerin*` | Reuse penempatan/industri; tambah evaluasi rapor bila belum ada |
| UKK | modul `Ukk*` | Reuse data ujian/nilai; adapter ke keluaran rapor |
| nilai/deskripsi/terbit | tabel baru berprefix `erapor_` | Domain transaksi baru |

Catatan: skema awal SISFO belum selengkap profil Dapodik untuk data rapor, misalnya NISN, identitas orang tua, identitas sekolah, dan atribut administratif tertentu. Sebelum MVP cetak, buat matriks “field wajib format rapor → sumber SISFO → fallback → pemilik koreksi”.

### Strategi Referensi JSON

Folder `database/data` e-Rapor berisi 541 file JSON. Volume utama yang terukur:

- 6.042 mata pelajaran.
- 863 kurikulum.
- 997 jurusan.
- 84.155 kompetensi dasar dalam 169 file.
- 84.562 relasi mata pelajaran-kurikulum dalam 170 file.
- 91.098 wilayah level 4 dalam 183 file.

Referensi tidak boleh langsung disalin seluruhnya ke tabel operasional. Pipeline yang disarankan:

1. Simpan paket sumber di lokasi internal yang dapat dilacak.
2. Validasi schema, encoding, foreign key logis, duplikasi, dan tanggal kedaluwarsa.
3. Catat manifest import pada `erapor_reference_imports`.
4. Upsert ke tabel staging/reference berprefix `erapor_ref_`.
5. Kurikulum memilih subset aktif untuk sekolah/periode.
6. Buat laporan record diterima, dilewati, konflik, dan kedaluwarsa.

JSON e-Rapor tampak membawa data historis dan lintas jenjang; filter tidak boleh hanya berdasarkan nama. Gunakan ID, jenjang, kurikulum, tingkat, status aktif, dan masa berlaku. Status lisensi kode/aset/data sumber juga perlu dikonfirmasi karena repository yang dianalisis tidak memiliki file LICENSE di root.

### Model Data Minimum MVP

- `erapor_period_settings`: periode, kurikulum, status workflow, tanggal kunci.
- `erapor_reference_imports`: manifest dan hasil import.
- `erapor_subject_mappings`: mapel SISFO ↔ mapel referensi.
- `erapor_teaching_assignments`: periode, rombel, mapel, guru, kelompok, urutan, KKM/KKTP.
- `erapor_learning_outcomes`: CP/TP lokal atau hasil adopsi referensi.
- `erapor_assessment_plans`: komponen, bobot, teknik, tanggal, status.
- `erapor_assessment_scores`: rencana, siswa, skor, remedial, audit.
- `erapor_final_grades`: hasil kalkulasi, deskripsi, formula version.
- `erapor_completeness_items`: status kelengkapan per siswa/rombel.
- `erapor_publications`: versi, snapshot JSON, checksum, file, actor, waktu terbit.

Tabel pelengkap seperti ekskul, catatan wali, PKL, UKK, P5, dan kenaikan ditambahkan bertahap atau melalui adapter ke modul SISFO yang sudah ada.

### Integration Points

- **Auth**: session web SISFO; Sanctum hanya untuk endpoint yang memang diperlukan.
- **RBAC**: Spatie Permission dengan permission minimal `erapor.view`, `erapor.configure`, `erapor.score`, `erapor.submit`, `erapor.validate`, `erapor.publish`, `erapor.override`, dan `erapor.audit`.
- **Master data**: model `MasterGuru`, `MasterSiswa`, `Kelas`, `Rombel`, `MataPelajaran`, `TahunPelajaran`, dan relasi keanggotaan.
- **Existing modules**: jadwal sebagai kandidat pembentuk penugasan, ujian semester sebagai kandidat sumber nilai, serta PKL/UKK/absensi/prestasi sebagai sumber pelengkap.
- **Import/export**: Laravel Excel dengan preview, validasi, idempotency key, dan laporan error.
- **Documents**: renderer PDF dipilih setelah membandingkan kesetiaan layout, font, page break, QR, dan penggunaan memori pada satu rombel penuh.

### Security & Privacy

- Terapkan least privilege dan scope guru hanya ke penugasan miliknya.
- Siswa hanya dapat mengakses rapor miliknya yang sudah diterbitkan.
- Endpoint unduh memakai authorization, bukan URL file publik permanen.
- Audit create/update/submit/unlock/publish/download untuk nilai dan rapor.
- Override dan buka-kunci wajib menyimpan alasan serta actor.
- Snapshot rapor mengenkripsi atau membatasi field sensitif sesuai kebutuhan.
- Jangan menyalin konfigurasi `.env` atau contoh kredensial dari sumber; README e-Rapor memuat nilai SMTP yang menyerupai secret nyata dan harus dianggap telah terpapar/dirotasi bila masih digunakan.
- Backup dan restore diuji; jangan memakai fitur restore e-Rapor sumber secara langsung pada database SISFO.

### Testing

- Unit test formula nilai, bobot, remedial, pembulatan, predikat, dan deskripsi.
- Contract test adapter data SISFO agar perubahan master data terdeteksi.
- Import test untuk file valid, duplikat, referensi hilang, data kedaluwarsa, dan retry.
- Authorization test per persona dan upaya akses lintas rombel.
- Feature test workflow draft → submit → validate → publish → revise.
- Golden-file test membandingkan data kalkulasi dengan isi PDF.
- Pilot satu rombel menggunakan data salinan/staging sebelum produksi.

## 5. Risks & Roadmap

### Phased Rollout

#### Fase 0 — Discovery dan Landing Page

- Tambahkan landing page e-Rapor dalam SISFO.
- Buat pemeriksaan kesiapan data tanpa mengubah data.
- Finalkan format rapor prioritas, field wajib, formula, dan matriks role.
- Hasil: backlog implementasi tervalidasi dan dashboard kesiapan dapat dipakai.

#### MVP — Fondasi Data dan Satu Alur Akademik

- Buat periode, pemetaan mapel, penugasan, permission, dan import referensi terpilih.
- Implementasikan rencana asesmen, input/impor nilai, kalkulasi final, submit, validasi, dan cetak rapor akademik.
- Pilot satu tingkat/rombel dan satu kurikulum aktif.
- Exit criteria: seluruh KPI MVP pada Executive Summary terpenuhi.

#### v1.1 — Pelengkap Wali Kelas dan Monitoring

- Integrasikan absensi, ekskul, prestasi, catatan wali, kenaikan kelas, leger, serta monitoring per guru/rombel.
- Tambahkan revisi rapor, QR/checksum verifikasi, notifikasi pekerjaan tertunda, dan akses rapor siswa.

#### v2.0 — Kekhasan SMK dan Hardening

- Integrasikan PKL, UKK/sertifikat, P5/kokurikuler, DUDI/asesor, serta format khusus sekolah.
- Tambahkan migrasi historis terkontrol bila diperlukan.
- Lakukan uji beban, disaster recovery, observability, dan rollout seluruh tingkat.

### Technical Risks

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Menjalankan migrasi e-Rapor langsung pada MySQL SISFO | Gagal migrasi atau skema rusak | Tulis migrasi baru berprefix `erapor_`; jangan copy schema/view PG |
| Data SISFO belum memuat semua field rapor | Cetak terblokir atau tidak resmi | Data readiness matrix dan form koreksi dengan ownership |
| Jadwal dianggap sama dengan penugasan | Nilai terduplikasi per slot | Entitas `erapor_teaching_assignments` terpisah |
| Referensi JSON besar, historis, atau kedaluwarsa | Database bengkak dan pilihan salah | Staging, filter subset, versioning, checksum, laporan import |
| Formula berbeda antar kurikulum | Nilai akhir salah | Formula version, unit test, dan sampel pembanding |
| Duplikasi fitur SISFO (UKK/PKL/absensi) | Data ganda dan konflik | Adapter/reuse; tetapkan ownership per field sebelum coding |
| Perubahan master mengubah rapor lama | Dokumen historis tidak konsisten | Snapshot immutable dan versi penerbitan |
| Lisensi sumber/referensi belum jelas | Risiko distribusi | Review lisensi sebelum menyalin kode, aset, template, atau data |
| Secret dari source ikut tersalin | Akses sistem/email disalahgunakan | Secret scanning, rotasi kredensial, dan konfigurasi hanya dari environment |
| PDF massal berat | Timeout dan penggunaan memori tinggi | Queue, chunk per siswa/rombel, retry idempoten, penyimpanan privat |

### Keputusan yang Dibutuhkan Sebelum MVP

1. Kurikulum dan tingkat kelas pilot.
2. Contoh PDF rapor resmi yang saat ini digunakan sekolah.
3. Formula nilai, pembulatan, KKM/KKTP, dan aturan remedial yang disepakati.
4. Daftar field identitas wajib beserta sumber data SISFO.
5. Apakah nilai ujian semester menjadi komponen otomatis atau hanya opsi impor.
6. Apakah riwayat e-Rapor lama perlu dimigrasikan atau cukup diarsipkan.
