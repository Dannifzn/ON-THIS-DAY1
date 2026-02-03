-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2026 at 04:58 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u510831173_danni`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_logs`
--

CREATE TABLE `access_logs` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `access_time` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `access_logs`
--

INSERT INTO `access_logs` (`id`, `ip_address`, `access_time`) VALUES
(35, '104.28.194.248', '2026-02-03 06:40:18'),
(36, '104.28.194.248', '2026-02-03 06:41:28'),
(37, '2a09:bac5:3a5d:25d7::3c5:3b', '2026-02-03 06:45:10'),
(38, '2a09:bac5:3a5d:25d7::3c5:3b', '2026-02-03 06:45:11'),
(39, '2a09:bac5:3a5d:25d7::3c5:3b', '2026-02-03 06:45:21'),
(40, '2a09:bac5:3a5d:25d7::3c5:3b', '2026-02-03 06:46:53'),
(41, '202.67.44.12', '2026-02-03 07:10:42'),
(42, '202.67.44.12', '2026-02-03 07:16:10'),
(43, '202.67.44.12', '2026-02-03 07:16:13'),
(44, '202.67.44.12', '2026-02-03 07:16:16'),
(45, '202.67.44.12', '2026-02-03 07:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`) VALUES
(2, 'admin', '$2y$10$C5rpf8hsYf0tcvqx3pZzLuzg2QoqgQuXCghg8q0B3yyjXw6xfx0d2');

-- --------------------------------------------------------

--
-- Table structure for table `api_clients`
--

CREATE TABLE `api_clients` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `api_key` varchar(64) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_clients`
--

INSERT INTO `api_clients` (`id`, `client_name`, `api_key`, `created_at`) VALUES
(9, 'Said', 'Rahasia', '2026-01-20 09:29:54'),
(10, 'Admin', 'Bellzee', '2026-01-25 11:37:07'),
(12, 'BUDI', '2a93e8e2ce817c0524da27103ecd3374', '2026-02-03 07:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `api_requests`
--

CREATE TABLE `api_requests` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_requests`
--

INSERT INTO `api_requests` (`id`, `client_name`, `whatsapp`, `created_at`) VALUES
(2, 'Said', '085363979960', '2026-01-20 09:28:19'),
(3, 'Tono', '088271004763', '2026-01-21 19:51:38'),
(4, 'Boy', '088271004763', '2026-01-25 12:11:45'),
(5, 'BUDI', '085363979960', '2026-02-03 07:16:41');

-- --------------------------------------------------------

--
-- Table structure for table `custom_events`
--

CREATE TABLE `custom_events` (
  `id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` varchar(10) NOT NULL,
  `content` text NOT NULL,
  `source` varchar(50) DEFAULT 'Admin',
  `link` text NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `custom_events`
--

INSERT INTO `custom_events` (`id`, `day`, `month`, `year`, `content`, `source`, `link`) VALUES
(1, 1, 1, '2026', 'Project On This Day Selesai Dikerjakan oleh Kelompok Kami.', 'Admin', ''),
(3, 13, 1, '2026', 'Iran Escalates Deadly Crackdown on Mass Protests as Trump Threatens to Launch Military Attack', 'Admin', 'https://www.democracynow.org/2026/1/12/iran_protests'),
(4, 14, 1, '2026', 'Pegawai Pajak Kena OTT KPK, Cem Mana Tuah Purbaya?', 'Admin', 'https://www.cnnindonesia.com/ekonomi/20260114075009-532-1316819/pegawai-pajak-kena-ott-kpk-cem-mana-tuah-purbaya'),
(5, 20, 1, '2026', 'Seorang pekerja kafe, Sandi Hidayat ditemukan tewas usai gantung diri di Jalan Thamrin, Pekanbaru dengan seutas pesan haru terkait Judi online.', 'Admin', 'https://www.instagram.com/p/DTsR5HsDyiP/'),
(6, 21, 1, '2026', 'Gudang Penyimpanan Barang Rumah Tangga di Jalan Teuku Umar Pekanbaru Ludes Terbakar', 'Admin', 'https://riaupos.jawapos.com/pekanbaru/2257094980/gudang-penyimpanan-barang-rumah-tangga-di-jalan-teuku-umar-pekanbaru-ludes-terbakar'),
(7, 21, 1, '2026', 'Bahu Jalan Siak II Amblas Bahayakan Pengendara', 'Admin', 'https://riaupos.jawapos.com/pekanbaru/2257093444/bahu-jalan-siak-ii-amblas-bahayakan-pengendara'),
(8, 20, 1, '2026', 'Kasus Kematian Remaja di Jalan S Parman, Polisi Masih Dalami Dugaan Pengeroyokan, Ini Penjelasannya', 'Admin', 'https://riaupos.jawapos.com/pekanbaru/2257090256/kasus-kematian-remaja-di-jalan-s-parman-polisi-masih-dalami-dugaan-pengeroyokan-ini-penjelasannya'),
(9, 2, 2, '2026', 'Kapal Pembawa Getah Karet Tenggelam di Perairan Kepulauan Meranti', 'Admin', 'https://www.goriau.com/berita/baca/kapal-pembawa-getah-karet-tenggelam-di-perairan-kepulauan-meranti.html'),
(10, 2, 2, '2026', 'Gabung Board of Peace, Indonesia Dukung Kemerdekaan Palestina', 'Admin', 'https://news.republika.co.id/berita/t9try5484/gabung-board-of-peace-indonesia-dukung-kemerdekaan-palestina-part2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_logs`
--
ALTER TABLE `access_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_clients`
--
ALTER TABLE `api_clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_key` (`api_key`);

--
-- Indexes for table `api_requests`
--
ALTER TABLE `api_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_events`
--
ALTER TABLE `custom_events`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_logs`
--
ALTER TABLE `access_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `api_clients`
--
ALTER TABLE `api_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `api_requests`
--
ALTER TABLE `api_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `custom_events`
--
ALTER TABLE `custom_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
