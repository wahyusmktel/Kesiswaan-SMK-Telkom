/*M!999999\- enable the sandbox mode */ 
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;
DROP TABLE IF EXISTS `absensi_guru`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `absensi_guru` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `jadwal_pelajaran_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('hadir','tidak_hadir','terlambat','izin') NOT NULL DEFAULT 'tidak_hadir',
  `waktu_absen` time DEFAULT NULL,
  `dicatat_oleh` bigint(20) unsigned DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `absensi_guru_dicatat_oleh_foreign` (`dicatat_oleh`),
  KEY `absensi_guru_tanggal_status_index` (`tanggal`,`status`),
  KEY `absensi_guru_jadwal_pelajaran_id_index` (`jadwal_pelajaran_id`),
  CONSTRAINT `absensi_guru_dicatat_oleh_foreign` FOREIGN KEY (`dicatat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `absensi_guru_jadwal_pelajaran_id_foreign` FOREIGN KEY (`jadwal_pelajaran_id`) REFERENCES `jadwal_pelajarans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `app_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_name` varchar(255) NOT NULL DEFAULT 'SMK Telkom',
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `allow_registration` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bk_chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bk_chat_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chat_room_id` bigint(20) unsigned NOT NULL,
  `sender_id` bigint(20) unsigned NOT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text' COMMENT 'text, image, video, file',
  `file_path` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bk_chat_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bk_chat_rooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `siswa_user_id` bigint(20) unsigned NOT NULL,
  `guru_bk_user_id` bigint(20) unsigned NOT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bk_chat_rooms_siswa_user_id_guru_bk_user_id_unique` (`siswa_user_id`,`guru_bk_user_id`),
  KEY `bk_chat_rooms_guru_bk_user_id_foreign` (`guru_bk_user_id`),
  CONSTRAINT `bk_chat_rooms_guru_bk_user_id_foreign` FOREIGN KEY (`guru_bk_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bk_chat_rooms_siswa_user_id_foreign` FOREIGN KEY (`siswa_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bk_konsultasi_jadwals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bk_konsultasi_jadwals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_siswa_id` bigint(20) unsigned NOT NULL,
  `guru_bk_id` bigint(20) unsigned DEFAULT NULL,
  `perihal` varchar(255) NOT NULL,
  `tanggal_rencana` date NOT NULL,
  `jam_rencana` time NOT NULL,
  `tempat` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `catatan_bk` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bk_konsultasi_jadwals_master_siswa_id_foreign` (`master_siswa_id`),
  KEY `bk_konsultasi_jadwals_guru_bk_id_foreign` (`guru_bk_id`),
  CONSTRAINT `bk_konsultasi_jadwals_guru_bk_id_foreign` FOREIGN KEY (`guru_bk_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bk_konsultasi_jadwals_master_siswa_id_foreign` FOREIGN KEY (`master_siswa_id`) REFERENCES `master_siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bk_pembinaan_rutins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bk_pembinaan_rutins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dapodik_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dapodik_siswa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_siswa_id` bigint(20) unsigned NOT NULL,
  `nipd` varchar(255) DEFAULT NULL,
  `nisn` varchar(255) DEFAULT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `tempat_lahir` varchar(255) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `agama` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `rt` varchar(5) DEFAULT NULL,
  `rw` varchar(5) DEFAULT NULL,
  `dusun` varchar(255) DEFAULT NULL,
  `kelurahan` varchar(255) DEFAULT NULL,
  `kecamatan` varchar(255) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  `jenis_tinggal` varchar(255) DEFAULT NULL,
  `alat_transportasi` varchar(255) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `hp` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `skhun` varchar(255) DEFAULT NULL,
  `no_peserta_ujian_nasional` varchar(255) DEFAULT NULL,
  `no_seri_ijazah` varchar(255) DEFAULT NULL,
  `no_registrasi_akta_lahir` varchar(255) DEFAULT NULL,
  `no_kk` varchar(20) DEFAULT NULL,
  `penerima_kps` varchar(255) DEFAULT NULL,
  `no_kps` varchar(255) DEFAULT NULL,
  `penerima_kip` varchar(255) DEFAULT NULL,
  `nomor_kip` varchar(255) DEFAULT NULL,
  `nama_di_kip` varchar(255) DEFAULT NULL,
  `nomor_kks` varchar(255) DEFAULT NULL,
  `layak_pip` varchar(255) DEFAULT NULL,
  `alasan_layak_pip` text DEFAULT NULL,
  `bank` varchar(255) DEFAULT NULL,
  `nomor_rekening_bank` varchar(255) DEFAULT NULL,
  `rekening_atas_nama` varchar(255) DEFAULT NULL,
  `nama_ayah` varchar(255) DEFAULT NULL,
  `tahun_lahir_ayah` varchar(4) DEFAULT NULL,
  `jenjang_pendidikan_ayah` varchar(255) DEFAULT NULL,
  `pekerjaan_ayah` varchar(255) DEFAULT NULL,
  `penghasilan_ayah` varchar(255) DEFAULT NULL,
  `nik_ayah` varchar(20) DEFAULT NULL,
  `nama_ibu` varchar(255) DEFAULT NULL,
  `tahun_lahir_ibu` varchar(4) DEFAULT NULL,
  `jenjang_pendidikan_ibu` varchar(255) DEFAULT NULL,
  `pekerjaan_ibu` varchar(255) DEFAULT NULL,
  `penghasilan_ibu` varchar(255) DEFAULT NULL,
  `nik_ibu` varchar(20) DEFAULT NULL,
  `nama_wali` varchar(255) DEFAULT NULL,
  `tahun_lahir_wali` varchar(4) DEFAULT NULL,
  `jenjang_pendidikan_wali` varchar(255) DEFAULT NULL,
  `pekerjaan_wali` varchar(255) DEFAULT NULL,
  `penghasilan_wali` varchar(255) DEFAULT NULL,
  `nik_wali` varchar(20) DEFAULT NULL,
  `rombel_saat_ini` varchar(255) DEFAULT NULL,
  `kebutuhan_khusus` varchar(255) DEFAULT NULL,
  `sekolah_asal` varchar(255) DEFAULT NULL,
  `anak_ke_berapa` int(11) DEFAULT NULL,
  `lintang` varchar(20) DEFAULT NULL,
  `bujur` varchar(20) DEFAULT NULL,
  `berat_badan` int(11) DEFAULT NULL,
  `tinggi_badan` int(11) DEFAULT NULL,
  `lingkar_kepala` int(11) DEFAULT NULL,
  `jumlah_saudara_kandung` int(11) DEFAULT NULL,
  `jarak_rumah_ke_sekolah` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dapodik_siswa_master_siswa_id_foreign` (`master_siswa_id`),
  CONSTRAINT `dapodik_siswa_master_siswa_id_foreign` FOREIGN KEY (`master_siswa_id`) REFERENCES `master_siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dapodik_sync_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dapodik_sync_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` enum('import','sync') NOT NULL DEFAULT 'sync',
  `total_records` int(11) NOT NULL DEFAULT 0,
  `inserted_count` int(11) NOT NULL DEFAULT 0,
  `updated_count` int(11) NOT NULL DEFAULT 0,
  `failed_count` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dapodik_sync_history_user_id_foreign` (`user_id`),
  CONSTRAINT `dapodik_sync_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dispensasi_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispensasi_siswa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dispensasi_id` bigint(20) unsigned NOT NULL,
  `master_siswa_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dispensasi_siswa_dispensasi_id_foreign` (`dispensasi_id`),
  KEY `dispensasi_siswa_master_siswa_id_foreign` (`master_siswa_id`),
  CONSTRAINT `dispensasi_siswa_dispensasi_id_foreign` FOREIGN KEY (`dispensasi_id`) REFERENCES `dispensasis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dispensasi_siswa_master_siswa_id_foreign` FOREIGN KEY (`master_siswa_id`) REFERENCES `master_siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dispensasis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispensasis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kegiatan` varchar(255) NOT NULL,
  `keterangan` text NOT NULL,
  `waktu_mulai` timestamp NULL DEFAULT NULL,
  `waktu_selesai` timestamp NULL DEFAULT NULL,
  `status` enum('diajukan','disetujui','ditolak') NOT NULL DEFAULT 'diajukan',
  `diajukan_oleh_id` bigint(20) unsigned NOT NULL,
  `disetujui_oleh_id` bigint(20) unsigned DEFAULT NULL,
  `alasan_penolakan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dispensasis_diajukan_oleh_id_foreign` (`diajukan_oleh_id`),
  KEY `dispensasis_disetujui_oleh_id_foreign` (`disetujui_oleh_id`),
  CONSTRAINT `dispensasis_diajukan_oleh_id_foreign` FOREIGN KEY (`diajukan_oleh_id`) REFERENCES `users` (`id`),
  CONSTRAINT `dispensasis_disetujui_oleh_id_foreign` FOREIGN KEY (`disetujui_oleh_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `guru_izin_jadwal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `guru_izin_jadwal` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `guru_izin_id` bigint(20) unsigned NOT NULL,
  `jadwal_pelajaran_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guru_izin_jadwal_guru_izin_id_foreign` (`guru_izin_id`),
  KEY `guru_izin_jadwal_jadwal_pelajaran_id_foreign` (`jadwal_pelajaran_id`),
  CONSTRAINT `guru_izin_jadwal_guru_izin_id_foreign` FOREIGN KEY (`guru_izin_id`) REFERENCES `guru_izins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `guru_izin_jadwal_jadwal_pelajaran_id_foreign` FOREIGN KEY (`jadwal_pelajaran_id`) REFERENCES `jadwal_pelajarans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `guru_izins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `guru_izins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_guru_id` bigint(20) unsigned NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `jenis_izin` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `dokumen_pdf` varchar(255) DEFAULT NULL,
  `status_piket` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `status_kurikulum` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `status_sdm` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `piket_id` bigint(20) unsigned DEFAULT NULL,
  `kurikulum_id` bigint(20) unsigned DEFAULT NULL,
  `sdm_id` bigint(20) unsigned DEFAULT NULL,
  `piket_at` timestamp NULL DEFAULT NULL,
  `kurikulum_at` timestamp NULL DEFAULT NULL,
  `sdm_at` timestamp NULL DEFAULT NULL,
  `catatan_piket` text DEFAULT NULL,
  `catatan_kurikulum` text DEFAULT NULL,
  `catatan_sdm` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guru_izins_master_guru_id_foreign` (`master_guru_id`),
  KEY `guru_izins_piket_id_foreign` (`piket_id`),
  KEY `guru_izins_kurikulum_id_foreign` (`kurikulum_id`),
  KEY `guru_izins_sdm_id_foreign` (`sdm_id`),
  CONSTRAINT `guru_izins_kurikulum_id_foreign` FOREIGN KEY (`kurikulum_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `guru_izins_master_guru_id_foreign` FOREIGN KEY (`master_guru_id`) REFERENCES `master_gurus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `guru_izins_piket_id_foreign` FOREIGN KEY (`piket_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `guru_izins_sdm_id_foreign` FOREIGN KEY (`sdm_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `izin_meninggalkan_kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `izin_meninggalkan_kelas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `rombel_id` bigint(20) unsigned NOT NULL,
  `jadwal_pelajaran_id` bigint(20) unsigned DEFAULT NULL,
  `tujuan` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `estimasi_kembali` timestamp NULL DEFAULT NULL,
  `waktu_keluar_sebenarnya` timestamp NULL DEFAULT NULL,
  `waktu_kembali_sebenarnya` timestamp NULL DEFAULT NULL,
  `status` enum('diajukan','disetujui_guru_kelas','disetujui_guru_piket','diverifikasi_security','selesai','ditolak','terlambat') NOT NULL DEFAULT 'diajukan',
  `guru_kelas_approval_id` bigint(20) unsigned DEFAULT NULL,
  `guru_kelas_approved_at` timestamp NULL DEFAULT NULL,
  `guru_piket_approval_id` bigint(20) unsigned DEFAULT NULL,
  `guru_piket_approved_at` timestamp NULL DEFAULT NULL,
  `security_verification_id` bigint(20) unsigned DEFAULT NULL,
  `security_verified_at` timestamp NULL DEFAULT NULL,
  `alasan_penolakan` text DEFAULT NULL,
  `ditolak_oleh` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `izin_meninggalkan_kelas_uuid_unique` (`uuid`),
  KEY `izin_meninggalkan_kelas_user_id_foreign` (`user_id`),
  KEY `izin_meninggalkan_kelas_rombel_id_foreign` (`rombel_id`),
  KEY `izin_meninggalkan_kelas_guru_kelas_approval_id_foreign` (`guru_kelas_approval_id`),
  KEY `izin_meninggalkan_kelas_guru_piket_approval_id_foreign` (`guru_piket_approval_id`),
  KEY `izin_meninggalkan_kelas_security_verification_id_foreign` (`security_verification_id`),
  KEY `izin_meninggalkan_kelas_ditolak_oleh_foreign` (`ditolak_oleh`),
  KEY `izin_meninggalkan_kelas_jadwal_pelajaran_id_foreign` (`jadwal_pelajaran_id`),
  CONSTRAINT `izin_meninggalkan_kelas_ditolak_oleh_foreign` FOREIGN KEY (`ditolak_oleh`) REFERENCES `users` (`id`),
  CONSTRAINT `izin_meninggalkan_kelas_guru_kelas_approval_id_foreign` FOREIGN KEY (`guru_kelas_approval_id`) REFERENCES `users` (`id`),
  CONSTRAINT `izin_meninggalkan_kelas_guru_piket_approval_id_foreign` FOREIGN KEY (`guru_piket_approval_id`) REFERENCES `users` (`id`),
  CONSTRAINT `izin_meninggalkan_kelas_jadwal_pelajaran_id_foreign` FOREIGN KEY (`jadwal_pelajaran_id`) REFERENCES `jadwal_pelajarans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `izin_meninggalkan_kelas_rombel_id_foreign` FOREIGN KEY (`rombel_id`) REFERENCES `rombels` (`id`),
  CONSTRAINT `izin_meninggalkan_kelas_security_verification_id_foreign` FOREIGN KEY (`security_verification_id`) REFERENCES `users` (`id`),
  CONSTRAINT `izin_meninggalkan_kelas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jadwal_pelajarans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_pelajarans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rombel_id` bigint(20) unsigned NOT NULL,
  `mata_pelajaran_id` bigint(20) unsigned NOT NULL,
  `master_guru_id` bigint(20) unsigned NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
  `jam_ke` tinyint(3) unsigned NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jadwal_pelajarans_rombel_id_hari_jam_ke_unique` (`rombel_id`,`hari`,`jam_ke`),
  KEY `jadwal_pelajarans_mata_pelajaran_id_foreign` (`mata_pelajaran_id`),
  KEY `jadwal_pelajarans_master_guru_id_foreign` (`master_guru_id`),
  CONSTRAINT `jadwal_pelajarans_master_guru_id_foreign` FOREIGN KEY (`master_guru_id`) REFERENCES `master_gurus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jadwal_pelajarans_mata_pelajaran_id_foreign` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajarans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jadwal_pelajarans_rombel_id_foreign` FOREIGN KEY (`rombel_id`) REFERENCES `rombels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jam_pelajarans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jam_pelajarans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `jam_ke` tinyint(3) unsigned NOT NULL,
  `hari` varchar(255) DEFAULT NULL COMMENT 'Senin, Selasa, ..., Sabtu. Null berarti semua hari.',
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `tipe_kegiatan` varchar(255) DEFAULT NULL COMMENT 'istirahat, sholawat_pagi, upacara, ishoma, kegiatan_4r',
  `keterangan` varchar(255) DEFAULT NULL COMMENT 'Contoh: Istirahat, Sholat',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(255) NOT NULL,
  `jurusan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `keterlambatans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `keterlambatans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `master_siswa_id` bigint(20) unsigned NOT NULL,
  `dicatat_oleh_security_id` bigint(20) unsigned NOT NULL,
  `waktu_dicatat_security` timestamp NOT NULL,
  `alasan_siswa` text NOT NULL,
  `diverifikasi_oleh_piket_id` bigint(20) unsigned DEFAULT NULL,
  `waktu_verifikasi_piket` timestamp NULL DEFAULT NULL,
  `tindak_lanjut_piket` text DEFAULT NULL,
  `jadwal_pelajaran_id` bigint(20) unsigned DEFAULT NULL,
  `verifikasi_oleh_guru_kelas_id` bigint(20) unsigned DEFAULT NULL,
  `waktu_verifikasi_guru_kelas` timestamp NULL DEFAULT NULL,
  `status` enum('dicatat_security','diverifikasi_piket','selesai','terlambat') NOT NULL DEFAULT 'dicatat_security',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keterlambatans_uuid_unique` (`uuid`),
  KEY `keterlambatans_master_siswa_id_foreign` (`master_siswa_id`),
  KEY `keterlambatans_dicatat_oleh_security_id_foreign` (`dicatat_oleh_security_id`),
  KEY `keterlambatans_diverifikasi_oleh_piket_id_foreign` (`diverifikasi_oleh_piket_id`),
  KEY `keterlambatans_jadwal_pelajaran_id_foreign` (`jadwal_pelajaran_id`),
  KEY `keterlambatans_verifikasi_oleh_guru_kelas_id_foreign` (`verifikasi_oleh_guru_kelas_id`),
  CONSTRAINT `keterlambatans_dicatat_oleh_security_id_foreign` FOREIGN KEY (`dicatat_oleh_security_id`) REFERENCES `users` (`id`),
  CONSTRAINT `keterlambatans_diverifikasi_oleh_piket_id_foreign` FOREIGN KEY (`diverifikasi_oleh_piket_id`) REFERENCES `users` (`id`),
  CONSTRAINT `keterlambatans_jadwal_pelajaran_id_foreign` FOREIGN KEY (`jadwal_pelajaran_id`) REFERENCES `jadwal_pelajarans` (`id`),
  CONSTRAINT `keterlambatans_master_siswa_id_foreign` FOREIGN KEY (`master_siswa_id`) REFERENCES `master_siswa` (`id`),
  CONSTRAINT `keterlambatans_verifikasi_oleh_guru_kelas_id_foreign` FOREIGN KEY (`verifikasi_oleh_guru_kelas_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_gurus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_gurus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nuptk` varchar(255) DEFAULT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_gurus_nuptk_unique` (`nuptk`),
  UNIQUE KEY `master_gurus_user_id_unique` (`user_id`),
  CONSTRAINT `master_gurus_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_siswa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nis` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `tempat_lahir` varchar(255) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` text DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_siswa_nis_unique` (`nis`),
  UNIQUE KEY `master_siswa_user_id_unique` (`user_id`),
  CONSTRAINT `master_siswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mata_pelajarans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mata_pelajarans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_mapel` varchar(255) NOT NULL,
  `nama_mapel` varchar(255) NOT NULL,
  `jumlah_jam` int(11) NOT NULL DEFAULT 0,
  `kelas_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mata_pelajarans_kode_mapel_unique` (`kode_mapel`),
  KEY `mata_pelajarans_kelas_id_foreign` (`kelas_id`),
  CONSTRAINT `mata_pelajarans_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `nde_ref_jenis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nde_ref_jenis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `kode` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nde_ref_jenis_kode_unique` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `nota_dinas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_dinas` (
  `id` char(36) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `nomor_nota` varchar(255) NOT NULL,
  `jenis_id` bigint(20) unsigned NOT NULL,
  `perihal` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `tanggal` date NOT NULL,
  `lampiran` varchar(255) DEFAULT NULL,
  `status` enum('draft','dikirim') NOT NULL DEFAULT 'dikirim',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nota_dinas_nomor_nota_unique` (`nomor_nota`),
  KEY `nota_dinas_user_id_foreign` (`user_id`),
  KEY `nota_dinas_jenis_id_foreign` (`jenis_id`),
  CONSTRAINT `nota_dinas_jenis_id_foreign` FOREIGN KEY (`jenis_id`) REFERENCES `nde_ref_jenis` (`id`),
  CONSTRAINT `nota_dinas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `nota_dinas_penerima`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_dinas_penerima` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nota_dinas_id` char(36) NOT NULL,
  `penerima_user_id` bigint(20) unsigned NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nota_dinas_penerima_nota_dinas_id_foreign` (`nota_dinas_id`),
  KEY `nota_dinas_penerima_penerima_user_id_foreign` (`penerima_user_id`),
  CONSTRAINT `nota_dinas_penerima_nota_dinas_id_foreign` FOREIGN KEY (`nota_dinas_id`) REFERENCES `nota_dinas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nota_dinas_penerima_penerima_user_id_foreign` FOREIGN KEY (`penerima_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengaduans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengaduans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_pelapor` varchar(255) NOT NULL,
  `hubungan` varchar(255) NOT NULL,
  `nomor_wa` varchar(255) NOT NULL,
  `nama_siswa` varchar(255) NOT NULL,
  `kelas_siswa` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `isi_pengaduan` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `catatan_petugas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `perizinan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `perizinan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `tanggal_izin` date NOT NULL,
  `jenis_izin` enum('sakit','izin','dispen') NOT NULL,
  `keterangan` text NOT NULL,
  `dokumen_pendukung` varchar(255) DEFAULT NULL,
  `status` enum('diajukan','disetujui','ditolak') NOT NULL DEFAULT 'diajukan',
  `alasan_penolakan` text DEFAULT NULL,
  `disetujui_oleh` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `perizinan_user_id_foreign` (`user_id`),
  KEY `perizinan_disetujui_oleh_foreign` (`disetujui_oleh`),
  CONSTRAINT `perizinan_disetujui_oleh_foreign` FOREIGN KEY (`disetujui_oleh`) REFERENCES `users` (`id`),
  CONSTRAINT `perizinan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `poin_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `poin_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `poin_peraturans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `poin_peraturans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `poin_category_id` bigint(20) unsigned NOT NULL,
  `pasal` varchar(255) NOT NULL,
  `ayat` varchar(255) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `bobot_poin` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `poin_peraturans_poin_category_id_foreign` (`poin_category_id`),
  CONSTRAINT `poin_peraturans_poin_category_id_foreign` FOREIGN KEY (`poin_category_id`) REFERENCES `poin_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `prakerin_industris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `prakerin_industris` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_industri` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `kota` varchar(255) NOT NULL,
  `telepon` varchar(255) DEFAULT NULL,
  `email_pic` varchar(255) DEFAULT NULL,
  `nama_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `prakerin_jurnals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `prakerin_jurnals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `prakerin_penempatan_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `kegiatan_dilakukan` text NOT NULL,
  `kompetensi_yang_didapat` text NOT NULL,
  `foto_kegiatan` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('menunggu','disetujui','revisi') NOT NULL DEFAULT 'menunggu',
  `catatan_pembimbing` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prakerin_jurnals_prakerin_penempatan_id_foreign` (`prakerin_penempatan_id`),
  CONSTRAINT `prakerin_jurnals_prakerin_penempatan_id_foreign` FOREIGN KEY (`prakerin_penempatan_id`) REFERENCES `prakerin_penempatans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `prakerin_penempatans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `prakerin_penempatans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_siswa_id` bigint(20) unsigned NOT NULL,
  `prakerin_industri_id` bigint(20) unsigned NOT NULL,
  `master_guru_id` bigint(20) unsigned NOT NULL,
  `nama_pembimbing_industri` varchar(255) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` enum('aktif','selesai','dibatalkan') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prakerin_penempatans_master_siswa_id_tanggal_mulai_unique` (`master_siswa_id`,`tanggal_mulai`),
  KEY `prakerin_penempatans_prakerin_industri_id_foreign` (`prakerin_industri_id`),
  KEY `prakerin_penempatans_master_guru_id_foreign` (`master_guru_id`),
  CONSTRAINT `prakerin_penempatans_master_guru_id_foreign` FOREIGN KEY (`master_guru_id`) REFERENCES `master_gurus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `prakerin_penempatans_master_siswa_id_foreign` FOREIGN KEY (`master_siswa_id`) REFERENCES `master_siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `prakerin_penempatans_prakerin_industri_id_foreign` FOREIGN KEY (`prakerin_industri_id`) REFERENCES `prakerin_industris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rombel_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rombel_siswa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rombel_id` bigint(20) unsigned NOT NULL,
  `master_siswa_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rombel_siswa_rombel_id_master_siswa_id_unique` (`rombel_id`,`master_siswa_id`),
  KEY `rombel_siswa_master_siswa_id_foreign` (`master_siswa_id`),
  CONSTRAINT `rombel_siswa_master_siswa_id_foreign` FOREIGN KEY (`master_siswa_id`) REFERENCES `master_siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rombel_siswa_rombel_id_foreign` FOREIGN KEY (`rombel_id`) REFERENCES `rombels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rombels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rombels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tahun_pelajaran_id` bigint(20) unsigned DEFAULT NULL,
  `kelas_id` bigint(20) unsigned NOT NULL,
  `wali_kelas_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rombels_kelas_id_foreign` (`kelas_id`),
  KEY `rombels_wali_kelas_id_foreign` (`wali_kelas_id`),
  KEY `rombels_tahun_pelajaran_id_foreign` (`tahun_pelajaran_id`),
  CONSTRAINT `rombels_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`),
  CONSTRAINT `rombels_tahun_pelajaran_id_foreign` FOREIGN KEY (`tahun_pelajaran_id`) REFERENCES `tahun_pelajaran` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rombels_wali_kelas_id_foreign` FOREIGN KEY (`wali_kelas_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `siswa_panggilans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswa_panggilans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_siswa_id` bigint(20) unsigned NOT NULL,
  `nomor_surat` varchar(255) NOT NULL,
  `tanggal_panggilan` date NOT NULL,
  `jam_panggilan` time NOT NULL,
  `tempat_panggilan` varchar(255) NOT NULL,
  `perihal` varchar(255) NOT NULL,
  `status` enum('diajukan','disetujui','ditolak','terkirim','hadir','tidak_hadir') NOT NULL DEFAULT 'diajukan',
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `disetujui_oleh` bigint(20) unsigned DEFAULT NULL,
  `catatan_waka` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswa_panggilans_nomor_surat_unique` (`nomor_surat`),
  KEY `siswa_panggilans_master_siswa_id_foreign` (`master_siswa_id`),
  KEY `siswa_panggilans_created_by_foreign` (`created_by`),
  KEY `siswa_panggilans_disetujui_oleh_foreign` (`disetujui_oleh`),
  CONSTRAINT `siswa_panggilans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `siswa_panggilans_disetujui_oleh_foreign` FOREIGN KEY (`disetujui_oleh`) REFERENCES `users` (`id`),
  CONSTRAINT `siswa_panggilans_master_siswa_id_foreign` FOREIGN KEY (`master_siswa_id`) REFERENCES `master_siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `siswa_pelanggarans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswa_pelanggarans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_siswa_id` bigint(20) unsigned NOT NULL,
  `poin_peraturan_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `pelapor_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `siswa_pelanggarans_master_siswa_id_foreign` (`master_siswa_id`),
  KEY `siswa_pelanggarans_poin_peraturan_id_foreign` (`poin_peraturan_id`),
  KEY `siswa_pelanggarans_pelapor_id_foreign` (`pelapor_id`),
  CONSTRAINT `siswa_pelanggarans_master_siswa_id_foreign` FOREIGN KEY (`master_siswa_id`) REFERENCES `master_siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `siswa_pelanggarans_pelapor_id_foreign` FOREIGN KEY (`pelapor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `siswa_pelanggarans_poin_peraturan_id_foreign` FOREIGN KEY (`poin_peraturan_id`) REFERENCES `poin_peraturans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `siswa_pemutihans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswa_pemutihans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_siswa_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `poin_dikurangi` int(11) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `status` enum('diajukan','disetujui','ditolak') NOT NULL DEFAULT 'disetujui',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `diajukan_oleh` bigint(20) unsigned DEFAULT NULL,
  `disetujui_oleh` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `siswa_pemutihans_master_siswa_id_foreign` (`master_siswa_id`),
  KEY `siswa_pemutihans_diajukan_oleh_foreign` (`diajukan_oleh`),
  KEY `siswa_pemutihans_disetujui_oleh_foreign` (`disetujui_oleh`),
  CONSTRAINT `siswa_pemutihans_diajukan_oleh_foreign` FOREIGN KEY (`diajukan_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswa_pemutihans_disetujui_oleh_foreign` FOREIGN KEY (`disetujui_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswa_pemutihans_master_siswa_id_foreign` FOREIGN KEY (`master_siswa_id`) REFERENCES `master_siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `siswa_prestasis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswa_prestasis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_siswa_id` bigint(20) unsigned NOT NULL,
  `nama_prestasi` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `poin_bonus` int(11) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `siswa_prestasis_master_siswa_id_foreign` (`master_siswa_id`),
  CONSTRAINT `siswa_prestasis_master_siswa_id_foreign` FOREIGN KEY (`master_siswa_id`) REFERENCES `master_siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tahun_pelajaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tahun_pelajaran` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tahun` varchar(9) NOT NULL,
  `semester` enum('Ganjil','Genap') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `test_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_table` (
  `id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

/*M!999999\- enable the sandbox mode */ 
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_07_03_054635_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_07_03_072022_create_perizinans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_07_03_084905_add_wali_kelas_id_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_07_03_085513_add_alasan_penolakan_to_perizinan_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_07_03_090626_remove_wali_kelas_id_from_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_07_03_090652_create_master_siswas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_07_03_090704_create_kelas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_07_03_090719_create_rombels_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_07_03_090730_create_rombel_siswa_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_07_04_015829_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_07_04_153115_create_mata_pelajarans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_07_04_153950_create_master_gurus_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_07_04_155521_add_jumlah_jam_to_mata_pelajarans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_07_04_160215_create_jadwal_pelajarans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_07_05_020056_create_izin_meninggalkan_kelas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_07_05_202358_add_jadwal_id_to_izin_meninggalkan_kelas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_07_05_203403_create_jam_pelajarans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_07_06_170812_create_keterlambatans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_07_06_175430_change_status_column_in_keterlambatans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_07_07_081728_add_guru_kelas_verification_to_keterlambatans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_07_07_091744_create_dispensasis_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_07_07_091745_create_dispensasi_siswa_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_07_07_133833_create_prakerin_industris_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_07_07_134743_create_prakerin_penempatans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_07_07_140518_create_prakerin_jurnals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_12_17_095859_add_kelas_id_to_mata_pelajarans_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_12_17_192538_create_tahun_pelajarans_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_12_17_205032_modify_rombels_table_add_relation',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_12_18_222730_create_pengaduans_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_12_19_084658_create_app_settings_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_12_19_085916_add_registration_open_to_app_settings_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_12_24_200328_create_poin_categories_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_12_24_200328_create_poin_peraturans_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_12_24_200329_create_siswa_pelanggarans_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_12_24_200329_create_siswa_prestasis_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_12_24_200330_create_siswa_pemutihans_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_12_24_202848_create_siswa_panggilans_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_12_24_204424_create_bk_tables',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_12_24_204437_create_bk_konsultasi_jadwals_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_12_24_204437_create_bk_chat_rooms_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2025_12_24_204424_create_bk_tables',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2025_12_24_204437_create_bk_konsultasi_jadwals_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_12_24_204437_create_bk_chat_rooms_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_12_24_204438_create_bk_chat_messages_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2025_12_24_211909_add_media_to_bk_chat_messages_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2025_12_24_213044_make_message_nullable_in_bk_chat_messages_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2025_12_24_221412_create_absensi_guru_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2025_12_25_114705_update_status_in_siswa_panggilans',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2025_12_25_121500_add_status_to_siswa_pemutihans_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2025_12_25_172137_create_guru_izins_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2025_12_25_172139_create_guru_izin_jadwal_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2025_12_25_180033_change_tanggal_to_datetime_in_guru_izins_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2025_12_26_074915_add_tipe_kegiatan_to_jam_pelajarans_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2025_12_26_075622_modify_jam_pelajarans_remove_unique_add_hari',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2025_12_26_231351_create_personal_access_tokens_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2025_12_28_095202_create_nota_dinas_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2025_12_28_095203_create_nota_dinas_penerima_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2025_12_28_214300_create_dapodik_siswa_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2025_12_28_231300_create_dapodik_sync_history_table',24);
