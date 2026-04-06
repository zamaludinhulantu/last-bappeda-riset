-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 09, 2026 at 01:49 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bappeda_riset`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` bigint UNSIGNED NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cuplikan` text COLLATE utf8mb4_unicode_ci,
  `ringkasan` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci,
  `berkas_sampul` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dipublikasikan_pada` timestamp NULL DEFAULT NULL,
  `penulis_id` bigint UNSIGNED DEFAULT NULL,
  `dibuat_pada` timestamp NULL DEFAULT NULL,
  `diubah_pada` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bidang`
--

CREATE TABLE `bidang` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dibuat_pada` timestamp NULL DEFAULT NULL,
  `diubah_pada` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `informasi_kontak`
--

CREATE TABLE `informasi_kontak` (
  `id` bigint UNSIGNED NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subjudul` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `surel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_layanan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dibuat_pada` timestamp NULL DEFAULT NULL,
  `diubah_pada` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `institusi`
--

CREATE TABLE `institusi` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kota` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dibuat_pada` timestamp NULL DEFAULT NULL,
  `diubah_pada` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kelompok_pekerjaan`
--

CREATE TABLE `kelompok_pekerjaan` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kunci_tembolok`
--

CREATE TABLE `kunci_tembolok` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_20_032011_create_institutions_table', 1),
(5, '2025_10_20_032125_create_fields_table', 1),
(6, '2025_10_20_032550_add_institution_id_to_users_table', 1),
(7, '2025_10_20_032627_create_researches_table', 1),
(8, '2025_10_20_032651_create_research_reviews_table', 1),
(9, '2025_10_26_020412_add_role_to_users_table', 1),
(10, '2025_10_28_214220_add_indexes_to_researches_table', 1),
(11, '2025_11_12_000000_update_researches_kesbang_flow', 1),
(12, '2025_11_12_000100_add_researcher_phone_to_researches', 1),
(13, '2025_11_12_000200_add_rejection_message_to_researches', 1),
(14, '2025_11_12_000300_add_nik_to_users_table', 1),
(15, '2025_11_12_000400_drop_nik_from_users_table', 1),
(16, '2025_11_21_000001_add_kesbang_letter_to_researches', 1),
(17, '2025_11_22_000300_update_status_enum_for_kesbang', 1),
(18, '2025_12_02_230543_add_decision_note_to_researches', 1),
(19, '2025_12_02_234244_add_resubmitted_after_reject_to_researches', 1),
(20, '2025_12_09_000000_create_news_table', 1),
(21, '2025_12_10_055541_add_results_path_to_researches_table', 1),
(22, '2025_12_13_000100_update_excerpt_column_in_news_table', 1),
(23, '2025_12_15_000200_add_location_to_researches_table', 1),
(24, '2025_12_18_000000_add_kesbang_letter_meta_to_researches', 1),
(25, '2025_12_21_000300_add_campus_letter_to_researches', 1),
(26, '2026_01_10_000000_create_contact_infos_table', 1),
(27, '2026_01_11_000000_drop_research_reviews_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `pekerjaan`
--

CREATE TABLE `pekerjaan` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pekerjaan_gagal`
--

CREATE TABLE `pekerjaan_gagal` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penelitian`
--

CREATE TABLE `penelitian` (
  `id` bigint UNSIGNED NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `penulis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik_peneliti` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon_peneliti` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `institusi_id` bigint UNSIGNED NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bidang_id` bigint UNSIGNED NOT NULL,
  `tahun` year NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `abstrak` text COLLATE utf8mb4_unicode_ci,
  `kata_kunci` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berkas_pdf` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `berkas_surat_kampus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berkas_hasil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berkas_surat_kesbang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_surat_kesbang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_surat_kesbang` date DEFAULT NULL,
  `status` enum('draft','submitted','kesbang_verified','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `pengunggah_id` bigint UNSIGNED NOT NULL,
  `diajukan_pada` timestamp NULL DEFAULT NULL,
  `diverifikasi_kesbang_pada` timestamp NULL DEFAULT NULL,
  `diverifikasi_kesbang_oleh` bigint UNSIGNED DEFAULT NULL,
  `disetujui_pada` timestamp NULL DEFAULT NULL,
  `hasil_diunggah_pada` timestamp NULL DEFAULT NULL,
  `ditolak_pada` timestamp NULL DEFAULT NULL,
  `alasan_penolakan` text COLLATE utf8mb4_unicode_ci,
  `catatan_keputusan` text COLLATE utf8mb4_unicode_ci,
  `diajukan_ulang_pada` timestamp NULL DEFAULT NULL,
  `disetujui_oleh` bigint UNSIGNED DEFAULT NULL,
  `ditolak_oleh` bigint UNSIGNED DEFAULT NULL,
  `dibuat_pada` timestamp NULL DEFAULT NULL,
  `diubah_pada` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surel_terverifikasi_pada` timestamp NULL DEFAULT NULL,
  `kata_sandi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_ingat` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dibuat_pada` timestamp NULL DEFAULT NULL,
  `diubah_pada` timestamp NULL DEFAULT NULL,
  `institusi_id` bigint UNSIGNED DEFAULT NULL,
  `peran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama`, `surel`, `surel_terverifikasi_pada`, `kata_sandi`, `token_ingat`, `dibuat_pada`, `diubah_pada`, `institusi_id`, `peran`) VALUES
(1, 'Test User', 'test@example.com', '2026-01-09 05:22:05', '$2y$12$lcTuC9GhJpTw3ZARdorWQOUpbDG84IOB452WZWaXL3Qnzy3GUyoP6', 'bo0azVGBUv', '2026-01-09 05:22:05', '2026-01-09 05:22:05', NULL, 'user'),
(2, 'Super Admin', 'superadmin@bapppeda.local', '2026-01-09 05:22:05', '$2y$12$yUGazj6FFAWRRMhxhJH8Pu.VZG8JhiDtgufFmdomwSbtHI/vRHf.a', NULL, '2026-01-09 05:22:05', '2026-01-09 05:22:05', NULL, 'superadmin'),
(3, 'Kesbangpol', 'kesbang123@gmail.com', '2026-01-09 05:22:05', '$2y$12$7a8PsBwK2XQsWzVQ1BL7I.84XMfquTeecpm5VIg/V//P7ldfLV8h6', NULL, '2026-01-09 05:22:05', '2026-01-09 05:22:05', NULL, 'kesbangpol'),
(4, 'Super Admin 2', 'superadmin123@gmail.com', '2026-01-09 05:22:05', '$2y$12$YmZrUSdqm4IBCK5jij0Lh.CB42z7sZAQDYUQ8LeTAW2my7GXR9p2y', NULL, '2026-01-09 05:22:05', '2026-01-09 05:22:05', NULL, 'superadmin');

-- --------------------------------------------------------

--
-- Table structure for table `sesi`
--

CREATE TABLE `sesi` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tembolok`
--

CREATE TABLE `tembolok` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `token_reset_sandi`
--

CREATE TABLE `token_reset_sandi` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `berita_slug_unique` (`slug`),
  ADD KEY `berita_penulis_id_foreign` (`penulis_id`);

--
-- Indexes for table `bidang`
--
ALTER TABLE `bidang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bidang_nama_unique` (`nama`);

--
-- Indexes for table `informasi_kontak`
--
ALTER TABLE `informasi_kontak`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `institusi`
--
ALTER TABLE `institusi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `institusi_nama_unique` (`nama`);

--
-- Indexes for table `kelompok_pekerjaan`
--
ALTER TABLE `kelompok_pekerjaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kunci_tembolok`
--
ALTER TABLE `kunci_tembolok`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pekerjaan`
--
ALTER TABLE `pekerjaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pekerjaan_queue_index` (`queue`);

--
-- Indexes for table `pekerjaan_gagal`
--
ALTER TABLE `pekerjaan_gagal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pekerjaan_gagal_uuid_unique` (`uuid`);

--
-- Indexes for table `penelitian`
--
ALTER TABLE `penelitian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penelitian_institusi_id_foreign` (`institusi_id`),
  ADD KEY `penelitian_bidang_id_foreign` (`bidang_id`),
  ADD KEY `penelitian_pengunggah_id_foreign` (`pengunggah_id`),
  ADD KEY `penelitian_disetujui_oleh_foreign` (`disetujui_oleh`),
  ADD KEY `penelitian_ditolak_oleh_foreign` (`ditolak_oleh`),
  ADD KEY `penelitian_status_index` (`status`),
  ADD KEY `penelitian_disetujui_pada_index` (`disetujui_pada`),
  ADD KEY `penelitian_tahun_index` (`tahun`),
  ADD KEY `penelitian_dibuat_pada_index` (`dibuat_pada`),
  ADD KEY `penelitian_diverifikasi_kesbang_oleh_foreign` (`diverifikasi_kesbang_oleh`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengguna_surel_unique` (`surel`),
  ADD KEY `pengguna_institusi_id_foreign` (`institusi_id`);

--
-- Indexes for table `sesi`
--
ALTER TABLE `sesi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sesi_user_id_index` (`user_id`),
  ADD KEY `sesi_last_activity_index` (`last_activity`);

--
-- Indexes for table `tembolok`
--
ALTER TABLE `tembolok`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `token_reset_sandi`
--
ALTER TABLE `token_reset_sandi`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bidang`
--
ALTER TABLE `bidang`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `informasi_kontak`
--
ALTER TABLE `informasi_kontak`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `institusi`
--
ALTER TABLE `institusi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `pekerjaan`
--
ALTER TABLE `pekerjaan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pekerjaan_gagal`
--
ALTER TABLE `pekerjaan_gagal`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penelitian`
--
ALTER TABLE `penelitian`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `berita`
--
ALTER TABLE `berita`
  ADD CONSTRAINT `berita_penulis_id_foreign` FOREIGN KEY (`penulis_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `penelitian`
--
ALTER TABLE `penelitian`
  ADD CONSTRAINT `penelitian_bidang_id_foreign` FOREIGN KEY (`bidang_id`) REFERENCES `bidang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penelitian_disetujui_oleh_foreign` FOREIGN KEY (`disetujui_oleh`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penelitian_ditolak_oleh_foreign` FOREIGN KEY (`ditolak_oleh`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penelitian_diverifikasi_kesbang_oleh_foreign` FOREIGN KEY (`diverifikasi_kesbang_oleh`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penelitian_institusi_id_foreign` FOREIGN KEY (`institusi_id`) REFERENCES `institusi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penelitian_pengunggah_id_foreign` FOREIGN KEY (`pengunggah_id`) REFERENCES `pengguna` (`id`);

--
-- Constraints for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD CONSTRAINT `pengguna_institusi_id_foreign` FOREIGN KEY (`institusi_id`) REFERENCES `institusi` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
