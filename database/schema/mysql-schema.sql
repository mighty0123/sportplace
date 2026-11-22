/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `aktor_role` varchar(50) NOT NULL DEFAULT 'mitra',
  `entitas_type` varchar(100) NOT NULL,
  `entitas_id` bigint(20) unsigned NOT NULL,
  `aksi` varchar(100) NOT NULL,
  `status_lama` varchar(50) DEFAULT NULL,
  `status_baru` varchar(50) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `booking_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `header_id` bigint(20) unsigned NOT NULL,
  `field_id` bigint(20) unsigned NOT NULL,
  `tanggal_sewa` datetime NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `harga_slot` double NOT NULL,
  `status_item` enum('ongoing','completed','no_show') NOT NULL DEFAULT 'ongoing',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_details_header_id_foreign` (`header_id`),
  KEY `booking_details_field_date_index` (`field_id`,`tanggal_sewa`),
  CONSTRAINT `booking_details_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`),
  CONSTRAINT `booking_details_header_id_foreign` FOREIGN KEY (`header_id`) REFERENCES `booking_headers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `booking_headers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_headers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_booking` varchar(12) DEFAULT NULL,
  `penyewa_id` bigint(20) unsigned NOT NULL,
  `total_tagihan` double NOT NULL,
  `status` enum('pending','payment','canceled','expired') NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_headers_kode_booking_unique` (`kode_booking`),
  KEY `booking_headers_penyewa_id_foreign` (`penyewa_id`),
  KEY `booking_headers_created_index` (`created_at`),
  KEY `booking_headers_status_created_index` (`status`,`created_at`),
  KEY `booking_headers_status_expires_index` (`status`,`expires_at`),
  CONSTRAINT `booking_headers_penyewa_id_foreign` FOREIGN KEY (`penyewa_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `field_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `field_blocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `alasan_pemblokiran` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `field_blocks_field_id_foreign` (`field_id`),
  CONSTRAINT `field_blocks_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `field_facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `field_facilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `facilities_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field_facilities_field_id_foreign` (`field_id`),
  KEY `field_facilities_facilities_id_foreign` (`facilities_id`),
  CONSTRAINT `field_facilities_facilities_id_foreign` FOREIGN KEY (`facilities_id`) REFERENCES `master_facilities` (`id`),
  CONSTRAINT `field_facilities_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `field_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `field_photo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `foto_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `field_photo_field_id_foreign` (`field_id`),
  CONSTRAINT `field_photo_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `field_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `field_schedule` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `hari` tinyint(4) NOT NULL,
  `jam_buka` time DEFAULT NULL,
  `jam_tutup` time DEFAULT NULL,
  `durasi` time DEFAULT NULL,
  `status_hari` enum('buka','tutup') NOT NULL DEFAULT 'buka',
  PRIMARY KEY (`id`),
  KEY `field_schedule_field_id_foreign` (`field_id`),
  CONSTRAINT `field_schedule_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fields` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `venue_id` bigint(20) unsigned NOT NULL,
  `sport_id` bigint(20) unsigned NOT NULL,
  `nama_lapangan` varchar(100) NOT NULL,
  `harga_perjam` double NOT NULL,
  `meta_harga` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_harga`)),
  `status_lapangan` enum('draft','pending_review','aktif','nonaktif','rejected') NOT NULL DEFAULT 'draft',
  `alasan_penolakan` varchar(255) DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fields_venue_id_foreign` (`venue_id`),
  KEY `fields_reviewed_by_foreign` (`reviewed_by`),
  KEY `fields_status_submitted_index` (`status_lapangan`,`submitted_at`),
  KEY `fields_sport_id_foreign` (`sport_id`),
  CONSTRAINT `fields_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fields_sport_id_foreign` FOREIGN KEY (`sport_id`) REFERENCES `master_sports` (`id`),
  CONSTRAINT `fields_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_cities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kota` varchar(50) NOT NULL,
  `provinsi` varchar(50) NOT NULL,
  `status_kota` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_cities_nama_kota_provinsi_unique` (`nama_kota`,`provinsi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_facilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `kategori` enum('peralatan','keamanan','umum') NOT NULL,
  `icon_url` varchar(255) NOT NULL,
  `status_fasilitas` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_facilities_nama_unique` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_sports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_sports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_olahraga` varchar(30) NOT NULL,
  `slug` varchar(40) NOT NULL,
  `icon_url` varchar(255) NOT NULL,
  `status_olahraga` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_sports_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mitra_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mitra_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `nama_usaha` varchar(150) DEFAULT NULL,
  `bentuk_badan_usaha` varchar(50) DEFAULT NULL,
  `kota_operasional` varchar(100) DEFAULT NULL,
  `deskripsi_usaha` text DEFAULT NULL,
  `bank_nama` varchar(50) DEFAULT NULL,
  `nomor_rekening` varchar(50) DEFAULT NULL,
  `nama_rekening` varchar(100) DEFAULT NULL,
  `no_ktp` varchar(20) NOT NULL,
  `npwp` varchar(24) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `alasan_penolakan` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_checklist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`review_checklist`)),
  `file_ktp` varchar(255) DEFAULT NULL,
  `file_npwp` varchar(255) DEFAULT NULL,
  `file_nib` varchar(255) DEFAULT NULL,
  `file_sewa_lahan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mitra_profiles_user_id_unique` (`user_id`),
  KEY `mitra_profiles_reviewed_by_foreign` (`reviewed_by`),
  KEY `mitra_profiles_status_submitted_at_index` (`status`,`submitted_at`),
  CONSTRAINT `mitra_profiles_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `mitra_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `otp_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otp_codes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `kode` varchar(255) NOT NULL,
  `tujuan` enum('email','hp') NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otp_codes_user_id_tujuan_index` (`user_id`,`tujuan`),
  CONSTRAINT `otp_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_webhook_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_webhook_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(30) NOT NULL,
  `event_id` varchar(120) NOT NULL,
  `pg_transaction_id` varchar(100) DEFAULT NULL,
  `event_type` varchar(60) DEFAULT NULL,
  `payment_id` bigint(20) unsigned DEFAULT NULL,
  `signature_valid` tinyint(1) NOT NULL DEFAULT 0,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `process_status` varchar(20) NOT NULL DEFAULT 'received',
  `error` text DEFAULT NULL,
  `received_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_webhook_events_provider_event_id_unique` (`provider`,`event_id`),
  KEY `payment_webhook_events_payment_id_foreign` (`payment_id`),
  KEY `payment_webhook_events_process_status_received_at_index` (`process_status`,`received_at`),
  KEY `payment_webhook_events_pg_transaction_id_index` (`pg_transaction_id`),
  CONSTRAINT `payment_webhook_events_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `header_id` bigint(20) unsigned NOT NULL,
  `pg_transaction_id` varchar(255) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `nominal_bayar` double NOT NULL,
  `status_pembayaran` enum('pending','succes','failed') NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_pg_transaction_id_unique` (`pg_transaction_id`),
  KEY `payments_status_paid_index` (`status_pembayaran`,`paid_at`),
  KEY `payments_header_status_index` (`header_id`,`status_pembayaran`),
  CONSTRAINT `payments_header_id_foreign` FOREIGN KEY (`header_id`) REFERENCES `booking_headers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refunds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `header_id` bigint(20) unsigned NOT NULL,
  `detail_id` bigint(20) unsigned NOT NULL,
  `payment_id` bigint(20) unsigned NOT NULL,
  `alasan_refund` text NOT NULL,
  `alasan_penolakan` varchar(255) DEFAULT NULL,
  `nominal_refund` double NOT NULL,
  `metode_refund` varchar(50) NOT NULL,
  `gateway_refund_id` varchar(100) DEFAULT NULL,
  `rekening_tujuan` varchar(200) NOT NULL,
  `status_refund` enum('pending','processing','completed','rejected') NOT NULL DEFAULT 'pending',
  `diproses_oleh` bigint(20) unsigned DEFAULT NULL,
  `complated_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `refunds_gateway_refund_id_unique` (`gateway_refund_id`),
  KEY `refunds_detail_id_foreign` (`detail_id`),
  KEY `refunds_payment_id_foreign` (`payment_id`),
  KEY `refunds_diproses_oleh_foreign` (`diproses_oleh`),
  KEY `refunds_header_status_index` (`header_id`,`status_refund`),
  KEY `refunds_status_created_index` (`status_refund`,`created_at`),
  CONSTRAINT `refunds_detail_id_foreign` FOREIGN KEY (`detail_id`) REFERENCES `booking_details` (`id`),
  CONSTRAINT `refunds_diproses_oleh_foreign` FOREIGN KEY (`diproses_oleh`) REFERENCES `users` (`id`),
  CONSTRAINT `refunds_header_id_foreign` FOREIGN KEY (`header_id`) REFERENCES `booking_headers` (`id`),
  CONSTRAINT `refunds_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','mitra','customer') NOT NULL DEFAULT 'customer',
  `status_akun` enum('aktif','suspend','banned') NOT NULL DEFAULT 'aktif',
  `foto_profil` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `venues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `venues` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mitra_id` bigint(20) unsigned NOT NULL,
  `city_id` bigint(20) unsigned NOT NULL,
  `nama_venue` varchar(200) DEFAULT NULL,
  `alamat_lengkap` text NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `map_url` varchar(255) DEFAULT NULL,
  `jam_buka` time NOT NULL,
  `jam_tutup` time NOT NULL,
  `status_vanue` enum('draft','pending_review','aktif','nonaktif','rejected') NOT NULL DEFAULT 'draft',
  `alasan_penolakan` varchar(255) DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `venues_mitra_id_foreign` (`mitra_id`),
  KEY `venues_reviewed_by_foreign` (`reviewed_by`),
  KEY `venues_status_submitted_index` (`status_vanue`,`submitted_at`),
  KEY `venues_city_id_foreign` (`city_id`),
  CONSTRAINT `venues_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `master_cities` (`id`),
  CONSTRAINT `venues_mitra_id_foreign` FOREIGN KEY (`mitra_id`) REFERENCES `mitra_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `venues_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wallet_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wallet_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wallet_id` bigint(20) unsigned NOT NULL,
  `jenis_mutasi` enum('kredit','debit') NOT NULL,
  `kategori_transaksi` enum('booking_revenue','platform_fee_deduction','refund_deduction','withdrawal') NOT NULL,
  `nominal` double NOT NULL,
  `referensi_tipe` enum('booking_detail','withdrawal','refund') NOT NULL,
  `referensi_id` int(11) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wallet_transactions_wallet_id_foreign` (`wallet_id`),
  CONSTRAINT `wallet_transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wallets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wallets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `saldo_aktif` double NOT NULL DEFAULT 0,
  `saldo_tertahan` double NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wallets_user_id_foreign` (`user_id`),
  CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `withdrawals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `withdrawals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `bank_kode` varchar(20) NOT NULL,
  `nomor_rekening` varchar(200) NOT NULL,
  `nama_pemilik_rek` varchar(255) NOT NULL,
  `nominal_tarik` double NOT NULL,
  `biaya_admin_bank` double NOT NULL,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `pg_disbursement_id` varchar(255) NOT NULL,
  `alasan_gagal` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `complated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `withdrawals_pg_disbursement_id_unique` (`pg_disbursement_id`),
  KEY `withdrawals_user_id_foreign` (`user_id`),
  CONSTRAINT `withdrawals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_10_19_032853_create_master_cities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_10_19_032854_create_master_sports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_10_19_032856_create_master_facilities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_10_19_032857_create_mitra_profiles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_10_19_032859_create_venues_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_10_19_032900_create_fields_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_10_19_032902_create_field_facilities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_10_19_032903_create_field_photo_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_10_19_032904_create_field_schedule_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2026_10_19_032906_create_field_blocks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2026_10_19_032908_create_booking_headers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_10_19_032910_create_booking_details_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_10_19_032911_create_payments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_10_19_032912_create_refunds_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_10_19_032914_create_wallets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_10_19_032916_create_withdrawals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_10_19_032917_create_wallet_transactions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2026_10_24_102200_add_gap_fields_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2026_10_24_102203_create_otp_codes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2026_10_25_000001_create_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2026_10_25_000002_add_gap_fields_to_mitra_profiles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2026_10_25_000003_create_audit_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2026_10_30_052357_add_timestamps_to_venues_and_fields',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2026_10_26_000001_add_gap_fields_to_venues_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_10_31_215727_add_review_columns_to_mitra_profiles_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_11_01_093322_harden_listing_review_master_unique_and_delete_rules',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_11_07_225412_add_transaction_monitoring_indexes',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_11_10_155542_add_kode_booking_and_expires_at_to_booking_headers',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_11_10_155543_add_review_columns_to_refunds_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_11_10_155544_create_payment_webhook_events_table',6);
