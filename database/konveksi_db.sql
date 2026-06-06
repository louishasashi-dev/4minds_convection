-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 06, 2026 at 05:19 AM
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
-- Database: `konveksi_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `name`, `email`, `password`, `no_hp`, `created_at`) VALUES
(1, 'Administrator', 'admin@konveksi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', '2026-05-31 00:20:42'),
(2, 'Administrator 2', 'admin2@konveksi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567891', '2026-06-02 02:33:49');

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `id_produk` int DEFAULT NULL,
  `jumlah` int NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_detail`, `id_transaksi`, `id_produk`, `jumlah`, `harga_satuan`, `subtotal`) VALUES
(9, 7, 3, 1, '99000.00', '99000.00'),
(10, 8, 3, 5, '99000.00', '495000.00'),
(11, 9, 3, 1, '99000.00', '99000.00'),
(12, 10, 3, 1, '99000.00', '99000.00'),
(13, 11, 3, 1, '99000.00', '99000.00'),
(14, 12, 3, 1, '99000.00', '99000.00'),
(15, 13, 3, 1, '99000.00', '99000.00'),
(16, 14, 3, 5, '99000.00', '495000.00'),
(17, 15, 3, 10, '99000.00', '990000.00'),
(18, 16, 4, 15, '78000.00', '1170000.00'),
(19, 17, NULL, 10, '4000000.00', '4000000.00'),
(20, 18, NULL, 100, '1000000.00', '1000000.00'),
(21, 19, NULL, 1, '0.00', '0.00'),
(22, 20, NULL, 5, '50000.00', '250000.00'),
(23, 21, 6, 2, '85000.00', '170000.00'),
(24, 22, 12, 2, '245000.00', '490000.00'),
(25, 23, 6, 100, '85000.00', '8500000.00'),
(26, 24, NULL, 1, '112000.00', '112000.00'),
(27, 25, NULL, 1, '100000.00', '100000.00'),
(28, 26, 13, 1, '1850000.00', '1850000.00'),
(29, 26, 12, 1, '245000.00', '245000.00'),
(30, 26, 7, 1, '120000.00', '120000.00'),
(31, 26, 4, 1, '55000.00', '55000.00'),
(32, 26, 3, 1, '99000.00', '99000.00'),
(33, 26, 9, 1, '650000.00', '650000.00'),
(34, 27, 12, 1, '245000.00', '245000.00'),
(35, 28, 19, 25, '45000.00', '1125000.00'),
(36, 29, NULL, 1, '80000.00', '80000.00');

-- --------------------------------------------------------

--
-- Table structure for table `diskon`
--

CREATE TABLE `diskon` (
  `id_diskon` int NOT NULL,
  `id_admin` int NOT NULL,
  `nama_diskon` varchar(100) NOT NULL,
  `jenis_diskon` enum('loyalitas','dinamis') NOT NULL,
  `syarat` varchar(255) DEFAULT NULL,
  `persentase` decimal(5,2) NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `diskon`
--

INSERT INTO `diskon` (`id_diskon`, `id_admin`, `nama_diskon`, `jenis_diskon`, `syarat`, `persentase`, `status`, `created_at`) VALUES
(1, 1, 'Diskon Pelanggan baru', 'dinamis', 'order pertama', '10.00', 'aktif', '2026-06-02 00:44:23'),
(2, 1, 'Diskon Pelanggan Setia', 'loyalitas', 'telah melakukan lebih dari 1000 transaksi', '25.00', 'aktif', '2026-06-02 01:42:53');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_transaksi`
--

CREATE TABLE `dokumen_transaksi` (
  `id_dokumen` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `id_pelanggan` int NOT NULL,
  `id_pembayaran` int DEFAULT NULL,
  `jenis_dokumen` enum('kwitansi','invoice','nota') NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `tanggal_cetak` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dokumen_transaksi`
--

INSERT INTO `dokumen_transaksi` (`id_dokumen`, `id_transaksi`, `id_pelanggan`, `id_pembayaran`, `jenis_dokumen`, `file_path`, `tanggal_cetak`) VALUES
(1, 13, 1, 14, 'kwitansi', NULL, '2026-06-02 11:26:28'),
(2, 7, 1, NULL, 'kwitansi', NULL, '2026-06-02 11:31:14'),
(3, 15, 2, 17, 'kwitansi', NULL, '2026-06-02 11:31:40'),
(4, 15, 2, 17, 'invoice', NULL, '2026-06-03 12:52:41'),
(5, 15, 2, 17, 'nota', NULL, '2026-06-02 11:32:02'),
(6, 7, 1, NULL, 'invoice', NULL, '2026-06-03 12:52:14');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int NOT NULL,
  `id_admin` int NOT NULL,
  `jenis_laporan` enum('penjualan','keuangan','aktivitas') NOT NULL,
  `periode_awal` date DEFAULT NULL,
  `periode_akhir` date DEFAULT NULL,
  `data` text,
  `generated_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `id_admin`, `jenis_laporan`, `periode_awal`, `periode_akhir`, `data`, `generated_at`) VALUES
(1, 1, 'penjualan', '2026-06-01', '2026-06-01', '[]', '2026-06-02 01:41:31'),
(2, 1, 'penjualan', '2026-06-01', '2026-06-01', '[]', '2026-06-02 01:41:34'),
(3, 1, 'penjualan', '2026-06-01', '2026-06-01', '[]', '2026-06-02 01:41:37'),
(4, 1, 'penjualan', '2026-06-01', '2026-06-02', '[{\"id_transaksi\":\"7\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"batal\",\"tanggal_transaksi\":\"2026-06-02 08:58:16\"},{\"id_transaksi\":\"8\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"371250.00\",\"diskon_total\":\"123750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:02:01\"},{\"id_transaksi\":\"9\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"jahit_satuan\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:07:08\"},{\"id_transaksi\":\"10\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:07:59\"},{\"id_transaksi\":\"11\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:18:45\"},{\"id_transaksi\":\"12\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:21:55\"},{\"id_transaksi\":\"13\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:22:21\"},{\"id_transaksi\":\"14\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"371250.00\",\"diskon_total\":\"123750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:44:41\"},{\"id_transaksi\":\"15\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"742500.00\",\"diskon_total\":\"247500.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 11:27:24\"},{\"id_transaksi\":\"16\",\"pelanggan\":\"bunaya ardik saputra\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"877500.00\",\"diskon_total\":\"292500.00\",\"status\":\"batal\",\"tanggal_transaksi\":\"2026-06-02 11:34:06\"}]', '2026-06-02 17:01:02'),
(5, 1, 'penjualan', '2026-06-01', '2026-06-02', '[{\"id_transaksi\":\"7\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"batal\",\"tanggal_transaksi\":\"2026-06-02 08:58:16\"},{\"id_transaksi\":\"8\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"371250.00\",\"diskon_total\":\"123750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:02:01\"},{\"id_transaksi\":\"9\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"jahit_satuan\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:07:08\"},{\"id_transaksi\":\"10\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:07:59\"},{\"id_transaksi\":\"11\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:18:45\"},{\"id_transaksi\":\"12\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:21:55\"},{\"id_transaksi\":\"13\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:22:21\"},{\"id_transaksi\":\"14\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"371250.00\",\"diskon_total\":\"123750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:44:41\"},{\"id_transaksi\":\"15\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"742500.00\",\"diskon_total\":\"247500.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 11:27:24\"},{\"id_transaksi\":\"16\",\"pelanggan\":\"bunaya ardik saputra\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"877500.00\",\"diskon_total\":\"292500.00\",\"status\":\"batal\",\"tanggal_transaksi\":\"2026-06-02 11:34:06\"}]', '2026-06-02 17:01:06'),
(6, 1, 'penjualan', '2026-06-01', '2026-06-03', '[{\"id_transaksi\":\"7\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"batal\",\"tanggal_transaksi\":\"2026-06-02 08:58:16\"},{\"id_transaksi\":\"8\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"371250.00\",\"diskon_total\":\"123750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:02:01\"},{\"id_transaksi\":\"9\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"jahit_satuan\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:07:08\"},{\"id_transaksi\":\"10\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:07:59\"},{\"id_transaksi\":\"11\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:18:45\"},{\"id_transaksi\":\"12\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:21:55\"},{\"id_transaksi\":\"13\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:22:21\"},{\"id_transaksi\":\"14\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"371250.00\",\"diskon_total\":\"123750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:44:41\"},{\"id_transaksi\":\"15\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"742500.00\",\"diskon_total\":\"247500.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 11:27:24\"},{\"id_transaksi\":\"16\",\"pelanggan\":\"bunaya ardik saputra\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"877500.00\",\"diskon_total\":\"292500.00\",\"status\":\"batal\",\"tanggal_transaksi\":\"2026-06-02 11:34:06\"},{\"id_transaksi\":\"17\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"kustom\",\"total_harga\":\"4000000.00\",\"diskon_total\":\"0.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-03 12:16:04\"}]', '2026-06-03 12:43:29'),
(7, 1, 'penjualan', '2026-06-03', '2026-06-03', '[{\"id_transaksi\":\"17\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"kustom\",\"total_harga\":\"4000000.00\",\"diskon_total\":\"0.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-03 12:16:04\"}]', '2026-06-03 12:44:15'),
(8, 1, 'keuangan', '2026-06-03', '2026-06-03', '[]', '2026-06-03 12:44:30'),
(9, 1, 'aktivitas', '2026-06-03', '2026-06-03', '[]', '2026-06-03 12:44:33'),
(10, 1, 'aktivitas', '2026-06-01', '2026-06-03', '[{\"id_pengiriman\":\"1\",\"pelanggan\":\"louis hasashi halim\",\"kurir\":\"JNT\",\"no_resi\":\"0011\",\"status\":\"sampai\",\"tanggal_kirim\":\"2026-06-02\",\"tanggal_tiba\":\"2026-06-02\"}]', '2026-06-03 12:44:38'),
(11, 1, 'keuangan', '2026-06-01', '2026-06-03', '[{\"id_pembayaran\":\"9\",\"pelanggan\":\"louis hasashi halim\",\"metode\":\"transfer\",\"jumlah_bayar\":\"74250\",\"status\":\"ditolak\",\"tanggal_pembayaran\":\"2026-06-02 08:59:30\"},{\"id_pembayaran\":\"10\",\"pelanggan\":\"fachrur hannan williyan\",\"metode\":\"transfer\",\"jumlah_bayar\":\"371250\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:02:11\"},{\"id_pembayaran\":\"11\",\"pelanggan\":\"fachrur hannan williyan\",\"metode\":\"transfer\",\"jumlah_bayar\":\"74250\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:08:12\"},{\"id_pembayaran\":\"12\",\"pelanggan\":\"fachrur hannan williyan\",\"metode\":\"transfer\",\"jumlah_bayar\":\"74250\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:20:25\"},{\"id_pembayaran\":\"13\",\"pelanggan\":\"louis hasashi halim\",\"metode\":\"transfer\",\"jumlah_bayar\":\"37125\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:22:21\"},{\"id_pembayaran\":\"14\",\"pelanggan\":\"louis hasashi halim\",\"metode\":\"transfer\",\"jumlah_bayar\":\"74250\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:22:38\"},{\"id_pembayaran\":\"15\",\"pelanggan\":\"louis hasashi halim\",\"metode\":\"transfer\",\"jumlah_bayar\":\"74250\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:22:44\"},{\"id_pembayaran\":\"16\",\"pelanggan\":\"louis hasashi halim\",\"metode\":\"transfer\",\"jumlah_bayar\":\"371250\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:44:50\"},{\"id_pembayaran\":\"17\",\"pelanggan\":\"fachrur hannan williyan\",\"metode\":\"transfer\",\"jumlah_bayar\":\"742500\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 11:27:33\"},{\"id_pembayaran\":\"18\",\"pelanggan\":\"bunaya ardik saputra\",\"metode\":\"transfer\",\"jumlah_bayar\":\"877500\",\"status\":\"ditolak\",\"tanggal_pembayaran\":\"2026-06-02 11:38:38\"}]', '2026-06-03 12:44:45'),
(12, 1, 'penjualan', '2026-06-01', '2026-06-03', '[{\"id_transaksi\":\"7\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"batal\",\"tanggal_transaksi\":\"2026-06-02 08:58:16\"},{\"id_transaksi\":\"8\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"371250.00\",\"diskon_total\":\"123750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:02:01\"},{\"id_transaksi\":\"9\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"jahit_satuan\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:07:08\"},{\"id_transaksi\":\"10\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:07:59\"},{\"id_transaksi\":\"11\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:18:45\"},{\"id_transaksi\":\"12\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:21:55\"},{\"id_transaksi\":\"13\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:22:21\"},{\"id_transaksi\":\"14\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"371250.00\",\"diskon_total\":\"123750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:44:41\"},{\"id_transaksi\":\"15\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"742500.00\",\"diskon_total\":\"247500.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 11:27:24\"},{\"id_transaksi\":\"16\",\"pelanggan\":\"bunaya ardik saputra\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"877500.00\",\"diskon_total\":\"292500.00\",\"status\":\"batal\",\"tanggal_transaksi\":\"2026-06-02 11:34:06\"},{\"id_transaksi\":\"17\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"kustom\",\"total_harga\":\"4000000.00\",\"diskon_total\":\"0.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-03 12:16:04\"}]', '2026-06-03 12:44:57'),
(13, 1, 'keuangan', '2026-06-01', '2026-06-03', '[{\"id_pembayaran\":\"9\",\"pelanggan\":\"louis hasashi halim\",\"metode\":\"transfer\",\"jumlah_bayar\":\"74250\",\"status\":\"ditolak\",\"tanggal_pembayaran\":\"2026-06-02 08:59:30\"},{\"id_pembayaran\":\"10\",\"pelanggan\":\"fachrur hannan williyan\",\"metode\":\"transfer\",\"jumlah_bayar\":\"371250\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:02:11\"},{\"id_pembayaran\":\"11\",\"pelanggan\":\"fachrur hannan williyan\",\"metode\":\"transfer\",\"jumlah_bayar\":\"74250\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:08:12\"},{\"id_pembayaran\":\"12\",\"pelanggan\":\"fachrur hannan williyan\",\"metode\":\"transfer\",\"jumlah_bayar\":\"74250\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:20:25\"},{\"id_pembayaran\":\"13\",\"pelanggan\":\"louis hasashi halim\",\"metode\":\"transfer\",\"jumlah_bayar\":\"37125\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:22:21\"},{\"id_pembayaran\":\"14\",\"pelanggan\":\"louis hasashi halim\",\"metode\":\"transfer\",\"jumlah_bayar\":\"74250\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:22:38\"},{\"id_pembayaran\":\"15\",\"pelanggan\":\"louis hasashi halim\",\"metode\":\"transfer\",\"jumlah_bayar\":\"74250\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:22:44\"},{\"id_pembayaran\":\"16\",\"pelanggan\":\"louis hasashi halim\",\"metode\":\"transfer\",\"jumlah_bayar\":\"371250\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 09:44:50\"},{\"id_pembayaran\":\"17\",\"pelanggan\":\"fachrur hannan williyan\",\"metode\":\"transfer\",\"jumlah_bayar\":\"742500\",\"status\":\"terkonfirmasi\",\"tanggal_pembayaran\":\"2026-06-02 11:27:33\"},{\"id_pembayaran\":\"18\",\"pelanggan\":\"bunaya ardik saputra\",\"metode\":\"transfer\",\"jumlah_bayar\":\"877500\",\"status\":\"ditolak\",\"tanggal_pembayaran\":\"2026-06-02 11:38:38\"}]', '2026-06-03 12:45:18');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` text,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `name`, `email`, `password`, `alamat`, `no_hp`, `created_at`) VALUES
(1, 'louis hasashi halim', 'louishasashi@gmail.com', '$2y$10$C0XVOug2xgJ87wEMNHPezug5uOmc/YuMerxQ5fKTWC8TdWnLsgtEG', 'bugel', '083150323263', '2026-05-31 00:37:02'),
(2, 'fachrur hannan williyan', '1224160073@global.ac.id', '$2y$10$haADRtJUHAxuKEfzmRLJcO7C6syft.2KdiDezs02k4ROqLU546ke.', 'wisma harapan keluarga fachrur', '082164859936', '2026-06-01 19:32:34'),
(3, 'bunaya ardik saputra', '1224160079@global.ac.id', '$2y$10$Mzrkdh9/rTmp3RcQA8ArI.e1fGCQSTBYzcjoDPOzbZ9IfRyxBz7Ou', 'pasar kemis 1', '0895637394487', '2026-06-02 02:21:56'),
(6, 'reza', 'reza123@gmail.com', '$2y$10$hs2k1WtjhroC5Kq/zvxv2exGO43EGc7v0HPv6CXceJgMsm0fMuQnC', 'Piruk', '085772601430', '2026-06-03 13:41:32');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `id_admin` int DEFAULT NULL,
  `tanggal_pembayaran` datetime DEFAULT CURRENT_TIMESTAMP,
  `metode` enum('transfer','tunai','cod') NOT NULL,
  `jumlah_bayar` double NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `status` enum('menunggu','terkonfirmasi','ditolak') DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_transaksi`, `id_admin`, `tanggal_pembayaran`, `metode`, `jumlah_bayar`, `bukti_bayar`, `status`) VALUES
(9, 7, 1, '2026-06-02 08:59:30', 'transfer', 74250, 'bukti_1780365570.jpg', 'ditolak'),
(10, 8, 1, '2026-06-02 09:02:11', 'transfer', 371250, '', 'terkonfirmasi'),
(11, 9, 1, '2026-06-02 09:08:12', 'transfer', 74250, '', 'terkonfirmasi'),
(12, 11, 1, '2026-06-02 09:20:25', 'transfer', 74250, '', 'terkonfirmasi'),
(13, 13, 1, '2026-06-02 09:22:21', 'transfer', 37125, NULL, 'terkonfirmasi'),
(14, 13, 1, '2026-06-02 09:22:38', 'transfer', 74250, '', 'terkonfirmasi'),
(15, 12, 1, '2026-06-02 09:22:44', 'transfer', 74250, '', 'terkonfirmasi'),
(16, 14, 1, '2026-06-02 09:44:50', 'transfer', 371250, '', 'terkonfirmasi'),
(17, 15, 1, '2026-06-02 11:27:33', 'transfer', 742500, '', 'terkonfirmasi'),
(18, 16, 1, '2026-06-02 11:38:38', 'transfer', 877500, 'bukti_1780375118.png', 'ditolak'),
(19, 20, 1, '2026-06-03 13:26:33', 'transfer', 93750, NULL, 'terkonfirmasi'),
(20, 20, 1, '2026-06-03 13:27:46', 'transfer', 93750, 'bukti_1780468066.png', 'terkonfirmasi'),
(21, 21, 1, '2026-06-03 13:44:20', 'transfer', 127500, '', 'terkonfirmasi'),
(22, 22, 1, '2026-06-04 12:12:42', 'transfer', 183750, NULL, 'terkonfirmasi'),
(23, 22, 1, '2026-06-04 12:13:13', 'transfer', 183750, '', 'terkonfirmasi'),
(24, 23, 1, '2026-06-04 12:57:31', 'transfer', 3187500, NULL, 'terkonfirmasi'),
(25, 23, 1, '2026-06-04 12:58:20', 'transfer', 3187500, 'bukti_1780552700.jpg', 'terkonfirmasi'),
(26, 24, 1, '2026-06-04 18:33:53', 'transfer', 56000, NULL, 'terkonfirmasi'),
(27, 24, 1, '2026-06-04 18:34:37', 'transfer', 112000, '', 'ditolak'),
(28, 24, 1, '2026-06-04 18:35:12', 'tunai', 56000, '', 'ditolak'),
(29, 24, 1, '2026-06-04 18:36:32', 'transfer', 112000, '', 'ditolak'),
(30, 25, 1, '2026-06-04 18:40:14', 'transfer', 50000, NULL, 'terkonfirmasi'),
(31, 25, 1, '2026-06-04 18:40:57', 'transfer', 50000, 'bukti_1780573257.png', 'terkonfirmasi'),
(32, 27, 1, '2026-06-04 19:07:48', 'transfer', 91875, NULL, 'terkonfirmasi'),
(33, 27, 1, '2026-06-04 19:08:16', 'transfer', 91875, '', 'terkonfirmasi'),
(34, 28, 1, '2026-06-06 11:36:45', 'transfer', 421875, NULL, 'terkonfirmasi'),
(35, 28, 1, '2026-06-06 11:39:25', 'transfer', 421875, '', 'ditolak'),
(36, 28, 1, '2026-06-06 11:40:17', 'transfer', 421875, 'bukti_1780720817.jpg', 'terkonfirmasi'),
(37, 29, 1, '2026-06-06 11:55:02', 'transfer', 40000, NULL, 'terkonfirmasi'),
(38, 29, 1, '2026-06-06 12:00:54', 'transfer', 40000, 'bukti_1780722054.png', 'terkonfirmasi');

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman`
--

CREATE TABLE `pengiriman` (
  `id_pengiriman` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `id_admin` int DEFAULT NULL,
  `kurir` varchar(100) DEFAULT NULL,
  `no_resi` varchar(100) DEFAULT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `tanggal_tiba` date DEFAULT NULL,
  `estimasi_sampai` date DEFAULT NULL,
  `status` enum('belum_dikirim','dikirim','sampai') DEFAULT 'belum_dikirim'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengiriman`
--

INSERT INTO `pengiriman` (`id_pengiriman`, `id_transaksi`, `id_admin`, `kurir`, `no_resi`, `tanggal_kirim`, `tanggal_tiba`, `estimasi_sampai`, `status`) VALUES
(1, 13, 1, 'JNT', '0011', '2026-06-02', '2026-06-02', '2026-06-04', 'sampai'),
(2, 25, 1, 'JNT', '001199778766587656', '2026-06-04', '2026-06-04', '2026-06-09', 'sampai'),
(3, 12, 1, 'JNT', '001199778766587659', '2026-06-04', '2026-06-04', '2026-06-05', 'sampai'),
(4, 26, 1, 'JNE', '0011997787665876599', '2026-06-04', '2026-06-04', '2026-06-10', 'sampai'),
(5, 27, 1, 'JNE', '001199778766587659', '2026-06-04', '2026-06-04', '2026-06-10', 'sampai'),
(6, 28, 1, 'JNT', '001199778766587659', '2026-06-06', '2026-06-06', '2026-06-08', 'sampai'),
(7, 29, 1, 'JNT', '001199778766587659', '2026-06-06', '2026-06-06', '2026-06-07', 'sampai');

-- --------------------------------------------------------

--
-- Table structure for table `pesan_jahit`
--

CREATE TABLE `pesan_jahit` (
  `id_pesan` int NOT NULL,
  `id_pelanggan` int NOT NULL,
  `jenis_pakaian` varchar(100) NOT NULL,
  `ukuran` varchar(255) DEFAULT NULL,
  `jumlah` int DEFAULT '1',
  `catatan` text,
  `estimasi_selesai` date DEFAULT NULL,
  `file_desain` varchar(255) DEFAULT NULL,
  `status` enum('menunggu','disetujui','ditolak') DEFAULT 'menunggu',
  `harga_disetujui` decimal(12,2) DEFAULT '0.00',
  `jenis_pembayaran` enum('dp','lunas','cod') DEFAULT 'dp',
  `id_transaksi` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesan_jahit`
--

INSERT INTO `pesan_jahit` (`id_pesan`, `id_pelanggan`, `jenis_pakaian`, `ukuran`, `jumlah`, `catatan`, `estimasi_selesai`, `file_desain`, `status`, `harga_disetujui`, `jenis_pembayaran`, `id_transaksi`, `created_at`) VALUES
(1, 1, 'kaos', 'XXXL', 1, 'warna navy, bahan cotton combed 30s, ada logo di dada kiri...\ndengan teks \"BAYUR BETTER THAN BROOKLYN\".', '2026-06-05', NULL, 'disetujui', '112000.00', 'dp', 24, '2026-06-04 18:33:17'),
(2, 1, 'kaos', 'XL', 1, 'warna navy, bahan cotton, lengan panjang, tambahkan logo \"manchester united\" di bagian dada sebelah kiri.', '2026-06-30', NULL, 'disetujui', '100000.00', 'dp', 25, '2026-06-04 18:39:24'),
(3, 3, 'kaos', 'XL', 1, 'Warna Navy, dengan desain sisik ular, dengan teks dibagian depan \"SNAKE\".', '2026-06-07', NULL, 'disetujui', '80000.00', 'dp', 29, '2026-06-06 11:54:33');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int NOT NULL,
  `id_ukuran_model` int DEFAULT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `harga` decimal(15,2) NOT NULL,
  `stok` int DEFAULT '0',
  `jenis` enum('jahit_satuan','pakaian_jadi','konveksi') NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `deskripsi` text,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `id_ukuran_model`, `nama_produk`, `kategori`, `harga`, `stok`, `jenis`, `model`, `deskripsi`, `gambar`, `created_at`) VALUES
(3, NULL, 'kostum halloween JOKER.', 'Kostum', '99000.00', 49, 'pakaian_jadi', 'Regular', 'Tampil beda di berbagai acara kostum dengan Kostum Halloween JOKER yang siap pakai dan nyaman digunakan. Cocok untuk Halloween, cosplay, pesta kostum, maupun acara komunitas. Detail desain dibuat untuk menghadirkan karakter ikonik yang mudah dikenali dan tetap nyaman dipakai dalam waktu lama.', 'produk_1780364134.png', '2026-06-02 08:35:34'),
(4, NULL, 'Kaos sablon \"BROOKLYN\"', 'Kaos', '55000.00', 49, 'pakaian_jadi', 'Regular', 'Kaos sablon \"BROOKLYN\" dengan desain simpel dan modern yang cocok untuk dipakai sehari-hari. Menggunakan bahan yang nyaman dengan sablon yang menarik sehingga mudah dipadukan dengan berbagai outfit kasual. Cocok untuk nongkrong, jalan-jalan, maupun aktivitas harian.', 'produk_1780388690.png', '2026-06-02 11:33:16'),
(5, NULL, 'jaket coklat dan topi', 'jaket', '78000.00', 87, 'pakaian_jadi', 'Regular', 'beli jaket gratis topi', 'produk_1780375424.jpg', '2026-06-02 11:43:44'),
(6, NULL, 'jaket', 'jaket', '85000.00', 48, 'konveksi', 'Regular', 'Kami melayani pembuatan jaket custom untuk kebutuhan komunitas, organisasi, sekolah, kampus, perusahaan, maupun event. Bisa request warna, bahan, bordir, sablon, dan berbagai detail lainnya sesuai kebutuhan. Untuk desain yang diinginkan, cukup jelaskan pada deskripsi pemesanan atau kirim gambar referensi agar tim kami lebih mudah memahami konsep yang Anda inginkan.', 'produk_1780716935.png', '2026-06-02 11:51:37'),
(7, NULL, 'jersey timnas indonesia', 'jersey', '120000.00', 34, 'pakaian_jadi', 'Regular', 'Jersey Timnas Indonesia dengan desain sporty yang siap menunjang aktivitas olahraga maupun koleksi bagi para pecinta sepak bola Indonesia. Nyaman digunakan saat bermain, berolahraga, menonton pertandingan, atau sekadar tampil dengan gaya yang lebih sporty.', 'produk_1780388472.png', '2026-06-02 15:21:12'),
(8, NULL, 'The Seoul Syndicate Set', 'Bundle', '1750000.00', 12, 'pakaian_jadi', 'Regular', '\"Power isn\'t given. It\'s taken. command the room before you even speak.\"\r\n\r\nHadirkan aura intimidatif yang elegan dan berwibawa lewat The Seoul Syndicate Set. Terinspirasi dari gaya K-Noir modern, setelan ini dirancang khusus untuk pria yang tidak hanya mementingkan penampilan, tapi juga ingin memancarkan kekuatan, kemewahan, dan karisma mutlak seorang pemimpin.\r\n\r\nPotongan siluet yang tegas berpadu sempurna dengan layering berlapis khas bos mafia, menciptakan ilusi tubuh yang lebih tegap, gagah, dan proporsional.', 'produk_1780389656.png', '2026-06-02 15:39:19'),
(9, NULL, 'Noir Gentleman Suit', 'Bundle', '650000.00', 18, 'pakaian_jadi', 'Regular', 'Noir Gentleman Suit menghadirkan tampilan formal yang elegan, berkelas, dan penuh percaya diri. Dirancang untuk pria yang ingin tampil profesional di berbagai kesempatan seperti acara resmi, pesta, pernikahan, wisuda, meeting penting, maupun sesi foto eksklusif. Potongan modern dan detail yang rapi membuat outfit ini memberikan kesan premium tanpa terlihat berlebihan.', 'produk_1780390154.png', '2026-06-02 15:49:14'),
(10, NULL, 'The Consigliere Black Suit', 'Bundle', '850000.00', 9, 'pakaian_jadi', 'Regular', 'The Consigliere Black Suit menghadirkan perpaduan sempurna antara kemewahan, ketegasan, dan profesionalisme. Dirancang dengan siluet modern fit yang mengikuti bentuk tubuh, setelan ini memberikan tampilan berkelas layaknya eksekutif muda, pengusaha sukses, maupun tokoh utama dalam drama bisnis modern.\r\n\r\nMengusung desain 3-piece suit yang terdiri dari jas, rompi, dan celana formal, outfit ini cocok digunakan untuk acara resmi, pertemuan bisnis, pesta pernikahan, wisuda, hingga sesi foto profesional.', 'produk_1780390605.png', '2026-06-02 15:56:45'),
(11, NULL, 'Seoul Street Cargo Set', 'Kaos', '249000.00', 23, 'pakaian_jadi', 'Regular', 'Seoul Street Cargo Set menghadirkan gaya streetwear Korea yang santai namun tetap stylish untuk aktivitas sehari-hari. Perpaduan oversized t-shirt dengan cargo pants modern menciptakan tampilan kasual yang sedang tren di kalangan Gen Z.\r\n\r\nPotongan longgar memberikan kenyamanan maksimal, sementara desain cargo menambahkan kesan urban dan fashionable. Cocok digunakan untuk nongkrong, kuliah, jalan-jalan, maupun konten media sosial.', 'produk_1780391627.png', '2026-06-02 16:13:47'),
(12, NULL, 'Blazer Semi-Formal Motif Plaid Klasik', 'Blazer/Jas', '245000.00', 17, 'pakaian_jadi', 'Regular', 'Produk pakaian jadi ini menghadirkan kombinasi setelan blazer bermotif kotak-kotak (plaid) cokelat tua yang elegan dengan kemeja putih polos berkerah terbuka sebagai pasangannya. Dibuat menggunakan bahan katun wol premium yang semi-tebal namun tetap adem, blazer ini dirancang dengan potongan regular fit yang memberikan kesan gagah, profesional, sekaligus tetap santai. Sangat cocok dipasarkan untuk kebutuhan seragam kerja kantoran, pakaian acara formal, maupun sebagai koleksi smart-casual siap pakai bagi pelaku usaha fashion retail.', 'produk_1780394883.png', '2026-06-02 17:08:03'),
(13, NULL, 'Executive Corporate Suit', 'Blazer/Jas', '1850000.00', 7, 'pakaian_jadi', 'Premium', 'Setelan jas eksklusif ini dirancang khusus untuk kebutuhan profesional tingkat tinggi, mengusung material high-quality wool blend yang memberikan struktur tegas namun tetap fleksibel untuk menunjang mobilitas kerja. Paket ini terdiri dari jas single-breasted dengan potongan modern fit, celana bahan senada, serta opsi kemeja premium yang dijahit dengan presisi tinggi oleh tenaga ahli konveksi kami. Sangat cocok dijadikan sebagai seragam identitas perusahaan atau pakaian formal bagi para eksekutif yang menginginkan tampilan tajam, berwibawa, dan berkelas dalam setiap pertemuan bisnis.', 'produk_1780395654.png', '2026-06-02 17:20:54'),
(15, NULL, 'Urban Skate', 'Bundle', '600000.00', 6, 'pakaian_jadi', 'Regular', 'Outfit ini ngambil vibe urban skate yang santai tapi tetap rapi. Kombinasi kemeja flanel kotak-kotak oversized sebagai outer, kaos polos sebagai inner, dan beanie hitam bikin look-nya jadi kasual, maskulin, dan gampang di-mix buat nongkrong atau foto street. Warnanya earth-tone gelap jadi cocok buat semua warna kulit dan gak gampang kotor. Simple, nyaman, dan timeless buat gaya harian anak kota.', 'produk_1780577389.png', '2026-06-04 19:49:49'),
(16, NULL, 'Streetwear Hype', 'Bundle', '760000.00', 9, 'pakaian_jadi', 'Regular', 'Outfit ini full streetwear hype dengan nuansa techwear. Kaos oversized washed hitam dipadu celana cargo abu-abu bikin siluet boxy dan tegas. Sling bag, sneakers chunky, dan layer necklace jadi poin yang naikin level outfit ini dari basic ke “anak hype”. Warna monokrom gelap kasih kesan bold, misterius, dan mahal. Cocok banget buat foto di spot urban kayak parkiran atau nongkrong di event street culture.', 'produk_1780577496.png', '2026-06-04 19:51:36'),
(17, NULL, 'Kaos', 'Kaos', '65000.00', 999999, 'konveksi', 'Regular', 'Melayani produksi kaos custom dalam jumlah besar maupun kecil untuk komunitas, event, sekolah, kampus, perusahaan, hingga kebutuhan promosi. Bisa request warna, bahan, sablon, maupun bordir sesuai kebutuhan. Jelaskan konsep desain pada deskripsi pemesanan atau kirim gambar referensi agar hasil produksi sesuai dengan yang Anda inginkan.', 'produk_1780716530.png', '2026-06-06 10:28:50'),
(18, NULL, 'Polo Shirt', 'Kaos', '75000.00', 9999999, 'konveksi', 'Regular', 'Melayani pembuatan polo shirt custom yang cocok untuk seragam kantor, komunitas, organisasi, sekolah, maupun kebutuhan promosi perusahaan. Tersedia berbagai pilihan warna, bahan, serta opsi bordir logo. Untuk desain yang diinginkan, cukup jelaskan pada deskripsi pemesanan atau kirim gambar referensi.', 'produk_1780716761.png', '2026-06-06 10:32:27'),
(19, NULL, 'jersey', 'Jersey', '45000.00', 9999974, 'konveksi', 'Regular', 'Melayani produksi jersey custom untuk tim olahraga, komunitas, sekolah, turnamen, maupun event khusus. Bisa request nama, nomor punggung, warna tim, dan desain sesuai kebutuhan. Jelaskan detail desain pada deskripsi pemesanan atau kirim gambar agar proses pengerjaan lebih mudah dan akurat.', 'produk_1780717085.png', '2026-06-06 10:37:43'),
(20, NULL, 'Rompi', 'Perlengkapan', '78000.00', 9999999, 'konveksi', 'Regular', 'Kami melayani pembuatan rompi custom untuk event, panitia, organisasi, komunitas, maupun kebutuhan kerja lapangan. Bisa disesuaikan dengan warna, logo, tulisan, dan model yang dibutuhkan. Cukup jelaskan kebutuhan Anda pada deskripsi pemesanan atau kirim gambar desain sebagai referensi.', 'produk_1780717669.png', '2026-06-06 10:40:02'),
(21, NULL, 'Kemeja', 'Kemeja', '65000.00', 9999999, 'konveksi', 'Regular', 'Melayani produksi kemeja custom untuk seragam kantor, komunitas, organisasi, sekolah, maupun kebutuhan usaha. Tersedia berbagai pilihan bahan dan warna yang bisa disesuaikan dengan kebutuhan Anda. Untuk desain, logo, atau detail tambahan lainnya, jelaskan pada deskripsi pemesanan atau kirim gambar referensi.', 'produk_1780717363.png', '2026-06-06 10:42:43'),
(22, NULL, 'Office Uniform Shirt', 'Seragam', '70000.00', 9999999, 'konveksi', 'Regular', 'Kami melayani pembuatan seragam kantor custom dengan tampilan profesional dan nyaman digunakan sehari-hari. Cocok untuk perusahaan, instansi, maupun usaha yang ingin memiliki identitas seragam sendiri. Jelaskan kebutuhan desain, warna, logo, dan detail lainnya pada deskripsi pemesanan atau kirim gambar referensi.', 'produk_1780717628.png', '2026-06-06 10:47:08'),
(26, NULL, 'Work Uniform', 'Seragam', '85000.00', 9999999, 'konveksi', 'Regular', 'Melayani produksi seragam kerja lapangan yang dirancang untuk menunjang aktivitas operasional dengan nyaman dan rapi. Cocok untuk proyek, teknisi, logistik, maupun berbagai kebutuhan industri. Untuk desain, warna, logo, dan spesifikasi lainnya, cukup jelaskan pada deskripsi pemesanan atau kirim gambar referensi.', 'produk_1780717926.png', '2026-06-06 10:52:06'),
(27, NULL, 'Parka', 'Jaket/mantel', '150000.00', 9999999, 'konveksi', 'Premium', 'Kami melayani pembuatan parka custom untuk komunitas, organisasi, perusahaan, maupun kebutuhan outdoor. Bisa request warna, bahan, logo, dan detail desain sesuai kebutuhan. Jelaskan konsep yang Anda inginkan pada deskripsi pemesanan atau kirim gambar referensi agar hasil lebih sesuai ekspektasi.', 'produk_1780718180.png', '2026-06-06 10:56:20'),
(28, NULL, 'Windbreaker', 'Jaket/mantel', '80000.00', 9999999, 'konveksi', 'Regular', 'Melayani produksi windbreaker custom yang cocok untuk komunitas touring, event, organisasi, maupun kebutuhan promosi. Ringan, nyaman digunakan, dan dapat disesuaikan dengan desain yang Anda inginkan. Untuk detail desain, cukup jelaskan pada deskripsi pemesanan atau kirim gambar referensi.', 'produk_1780718351.png', '2026-06-06 10:59:11'),
(29, NULL, 'Seragam Sekolah', 'Seragam', '65000.00', 9999999, 'konveksi', 'Regular', 'Kami menyediakan produksi seragam sekolah untuk SD, SMP, SMA, hingga SMK dengan kualitas jahitan yang rapi dan nyaman digunakan. Cocok untuk kebutuhan sekolah dalam jumlah besar. Jika memiliki desain khusus, atribut tambahan, atau kebutuhan tertentu, silakan jelaskan pada deskripsi pemesanan atau kirim gambar referensi.', 'produk_1780718520.jpg', '2026-06-06 11:02:00'),
(30, NULL, 'Seragam Kerja Industri', 'Seragam', '79500.00', 9999999, 'konveksi', 'Regular', 'Melayani pembuatan seragam kerja industri untuk pabrik, manufaktur, gudang, maupun berbagai kebutuhan operasional perusahaan. Desain dapat disesuaikan dengan identitas perusahaan, termasuk logo, warna, dan atribut tambahan. Jelaskan kebutuhan Anda pada deskripsi pemesanan atau kirim gambar referensi agar proses produksi lebih tepat.', 'produk_1780718626.webp', '2026-06-06 11:03:46');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_pelanggan` int NOT NULL,
  `tanggal_transaksi` datetime DEFAULT CURRENT_TIMESTAMP,
  `jenis_transaksi` enum('jahit_satuan','pakaian_jadi','konveksi','kustom') NOT NULL,
  `total_harga` decimal(15,2) DEFAULT '0.00',
  `diskon_total` decimal(15,2) DEFAULT '0.00',
  `jenis_pembayaran` enum('dp','lunas','cod') NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `deskripsi` text,
  `file_desain` varchar(255) DEFAULT NULL,
  `catatan_kustom` text,
  `ukuran_kustom` varchar(100) DEFAULT NULL,
  `jenis_pakaian_kustom` varchar(50) DEFAULT NULL,
  `status` enum('pending','diproses','selesai','dikirim','lunas','batal') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pelanggan`, `tanggal_transaksi`, `jenis_transaksi`, `total_harga`, `diskon_total`, `jenis_pembayaran`, `tanggal_selesai`, `deskripsi`, `file_desain`, `catatan_kustom`, `ukuran_kustom`, `jenis_pakaian_kustom`, `status`) VALUES
(7, 1, '2026-06-02 08:58:16', 'pakaian_jadi', '74250.00', '24750.00', 'lunas', '2026-06-02', NULL, NULL, NULL, NULL, NULL, 'batal'),
(8, 2, '2026-06-02 09:02:01', 'pakaian_jadi', '371250.00', '123750.00', 'lunas', '2026-06-02', NULL, NULL, NULL, NULL, NULL, 'selesai'),
(9, 2, '2026-06-02 09:07:08', 'jahit_satuan', '74250.00', '24750.00', 'lunas', '2026-06-02', NULL, NULL, NULL, NULL, NULL, 'selesai'),
(10, 2, '2026-06-02 09:07:59', 'pakaian_jadi', '74250.00', '24750.00', 'lunas', '2026-06-02', NULL, NULL, NULL, NULL, NULL, 'selesai'),
(11, 2, '2026-06-02 09:18:45', 'pakaian_jadi', '74250.00', '24750.00', 'lunas', '2026-06-02', NULL, NULL, NULL, NULL, NULL, 'selesai'),
(12, 1, '2026-06-02 09:21:55', 'pakaian_jadi', '74250.00', '24750.00', 'lunas', '2026-06-06', NULL, NULL, NULL, NULL, NULL, 'dikirim'),
(13, 1, '2026-06-02 09:22:21', 'pakaian_jadi', '74250.00', '24750.00', 'dp', '2026-06-07', NULL, NULL, NULL, NULL, NULL, 'selesai'),
(14, 1, '2026-06-02 09:44:41', 'pakaian_jadi', '371250.00', '123750.00', 'lunas', '2026-06-06', NULL, NULL, NULL, NULL, NULL, 'selesai'),
(15, 2, '2026-06-02 11:27:24', 'pakaian_jadi', '742500.00', '247500.00', 'lunas', '2026-06-02', NULL, NULL, NULL, NULL, NULL, 'selesai'),
(16, 3, '2026-06-02 11:34:06', 'pakaian_jadi', '877500.00', '292500.00', 'lunas', '2026-06-03', NULL, NULL, NULL, NULL, NULL, 'batal'),
(17, 1, '2026-06-03 12:16:04', 'kustom', '4000000.00', '0.00', 'lunas', '2026-06-17', NULL, NULL, 'warna navy, dengan sablon \"idaman ibu mertua\"', '1', NULL, 'selesai'),
(18, 3, '2026-06-03 12:47:44', 'kustom', '1000000.00', '0.00', 'lunas', '2026-06-11', NULL, NULL, 'warna coklat teks \"kicau\"', '1', NULL, 'pending'),
(19, 3, '2026-06-03 12:58:36', 'kustom', '0.00', '0.00', 'dp', '2026-06-14', NULL, NULL, 'XL', 'custom', NULL, 'pending'),
(20, 1, '2026-06-03 13:26:33', 'jahit_satuan', '187500.00', '62500.00', 'dp', '2026-06-06', NULL, NULL, NULL, NULL, NULL, 'lunas'),
(21, 6, '2026-06-03 13:44:05', 'konveksi', '127500.00', '42500.00', 'lunas', '2026-06-06', NULL, NULL, NULL, NULL, NULL, 'lunas'),
(22, 6, '2026-06-04 12:12:42', 'pakaian_jadi', '367500.00', '122500.00', 'dp', '2026-06-04', NULL, NULL, NULL, NULL, NULL, 'lunas'),
(23, 6, '2026-06-04 12:57:31', 'konveksi', '6375000.00', '2125000.00', 'dp', '2026-06-19', 'tolong tambahkan teks \"KITA BISA\" pada bagian depan jaket, dan jaketnya berwarna merah muda.', NULL, NULL, NULL, NULL, 'lunas'),
(24, 1, '2026-06-04 18:33:53', 'jahit_satuan', '112000.00', '0.00', 'dp', '2026-06-05', NULL, NULL, 'warna navy, bahan cotton combed 30s, ada logo di dada kiri...\ndengan teks \"BAYUR BETTER THAN BROOKLYN\".', 'XXXL', NULL, 'batal'),
(25, 1, '2026-06-04 18:40:14', 'jahit_satuan', '100000.00', '0.00', 'dp', '2026-06-30', NULL, NULL, 'warna navy, bahan cotton, lengan panjang, tambahkan logo \"manchester united\" di bagian dada sebelah kiri.', 'XL', NULL, 'selesai'),
(26, 1, '2026-06-04 18:43:37', 'pakaian_jadi', '2264250.00', '754750.00', 'lunas', NULL, '', NULL, NULL, NULL, NULL, 'selesai'),
(27, 1, '2026-06-04 19:07:48', 'pakaian_jadi', '183750.00', '61250.00', 'dp', '2026-06-06', '', NULL, NULL, NULL, NULL, 'selesai'),
(28, 6, '2026-06-06 11:36:45', 'konveksi', '843750.00', '281250.00', 'dp', '2026-06-13', 'desain sesuai gambar yang saya upload.', 'desain_1780720605_6.png', NULL, NULL, NULL, 'selesai'),
(29, 3, '2026-06-06 11:55:02', 'jahit_satuan', '80000.00', '0.00', 'dp', '2026-06-07', NULL, NULL, 'Warna Navy, dengan desain sisik ular, dengan teks dibagian depan \"SNAKE\".', 'XL', 'kaos', 'selesai');

-- --------------------------------------------------------

--
-- Table structure for table `ukuran_model`
--

CREATE TABLE `ukuran_model` (
  `id_ukuran_model` int NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `ukuran` varchar(50) NOT NULL,
  `deskripsi` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ukuran_model`
--

INSERT INTO `ukuran_model` (`id_ukuran_model`, `jenis`, `ukuran`, `deskripsi`, `created_at`) VALUES
(1, 'Kaos', 'XXL', 'Lingkar Dada 88cm, Lingkar perut 68cm.', '2026-06-02 00:39:45'),
(2, 'Jas', 'XXXL', '', '2026-06-02 15:37:42'),
(3, 'Kaos', 'S', '', '2026-06-04 20:06:22'),
(4, 'Kaos', 'M', '', '2026-06-04 20:06:29'),
(5, 'Kaos', 'L', '', '2026-06-04 20:06:37'),
(6, 'Kaos', 'XL', '', '2026-06-04 20:06:43'),
(7, 'Kaos', 'XXXL', '', '2026-06-04 20:06:55'),
(8, 'Kaos', 'XXXXL', '', '2026-06-04 20:07:06'),
(9, 'Jas', 'XXL', '', '2026-06-04 20:07:17'),
(10, 'Jas', 'XL', '', '2026-06-04 20:07:23'),
(11, 'Jas', 'L', '', '2026-06-04 20:07:28'),
(12, 'Jas', 'M', '', '2026-06-04 20:07:35'),
(13, 'Jas', 'S', '', '2026-06-04 20:07:40'),
(14, 'Kemeja', 'S', '', '2026-06-04 20:10:00'),
(15, 'Kemeja', 'M', '', '2026-06-04 20:10:00'),
(16, 'Kemeja', 'L', '', '2026-06-04 20:10:00'),
(17, 'Kemeja', 'XL', '', '2026-06-04 20:10:00'),
(18, 'Kemeja', 'XXL', '', '2026-06-04 20:10:00'),
(19, 'Kemeja', 'XXXL', '', '2026-06-04 20:10:00'),
(20, 'Kemeja', 'XXXXL', '', '2026-06-04 20:10:00'),
(21, 'Jaket', 'S', '', '2026-06-04 20:10:00'),
(22, 'Jaket', 'M', '', '2026-06-04 20:10:00'),
(23, 'Jaket', 'L', '', '2026-06-04 20:10:00'),
(24, 'Jaket', 'XL', '', '2026-06-04 20:10:00'),
(25, 'Jaket', 'XXL', '', '2026-06-04 20:10:00'),
(26, 'Jaket', 'XXXL', '', '2026-06-04 20:10:00'),
(27, 'Jaket', 'XXXXL', '', '2026-06-04 20:10:00'),
(28, 'Celana', 'S', '', '2026-06-04 20:10:00'),
(29, 'Celana', 'M', '', '2026-06-04 20:10:00'),
(30, 'Celana', 'L', '', '2026-06-04 20:10:00'),
(31, 'Celana', 'XL', '', '2026-06-04 20:10:00'),
(32, 'Celana', 'XXL', '', '2026-06-04 20:10:00'),
(33, 'Celana', 'XXXL', '', '2026-06-04 20:10:00'),
(34, 'Celana', 'XXXXL', '', '2026-06-04 20:10:00'),
(35, 'Gamis', 'S', '', '2026-06-04 20:10:00'),
(36, 'Gamis', 'M', '', '2026-06-04 20:10:00'),
(37, 'Gamis', 'L', '', '2026-06-04 20:10:00'),
(38, 'Gamis', 'XL', '', '2026-06-04 20:10:00'),
(39, 'Gamis', 'XXL', '', '2026-06-04 20:10:00'),
(40, 'Gamis', 'XXXL', '', '2026-06-04 20:10:00'),
(41, 'Gamis', 'XXXXL', '', '2026-06-04 20:10:00'),
(42, 'Seragam', 'S', '', '2026-06-04 20:10:00'),
(43, 'Seragam', 'M', '', '2026-06-04 20:10:00'),
(44, 'Seragam', 'L', '', '2026-06-04 20:10:00'),
(45, 'Seragam', 'XL', '', '2026-06-04 20:10:00'),
(46, 'Seragam', 'XXL', '', '2026-06-04 20:10:00'),
(47, 'Seragam', 'XXXL', '', '2026-06-04 20:10:00'),
(48, 'Seragam', 'XXXXL', '', '2026-06-04 20:10:00'),
(49, 'Blouse', 'S', '', '2026-06-04 20:10:00'),
(50, 'Blouse', 'M', '', '2026-06-04 20:10:00'),
(51, 'Blouse', 'L', '', '2026-06-04 20:10:00'),
(52, 'Blouse', 'XL', '', '2026-06-04 20:10:00'),
(53, 'Blouse', 'XXL', '', '2026-06-04 20:10:00'),
(54, 'Blouse', 'XXXL', '', '2026-06-04 20:10:00'),
(55, 'Blouse', 'XXXXL', '', '2026-06-04 20:10:00'),
(56, 'Hoodie', 'S', '', '2026-06-04 20:10:00'),
(57, 'Hoodie', 'M', '', '2026-06-04 20:10:00'),
(58, 'Hoodie', 'L', '', '2026-06-04 20:10:00'),
(59, 'Hoodie', 'XL', '', '2026-06-04 20:10:00'),
(60, 'Hoodie', 'XXL', '', '2026-06-04 20:10:00'),
(61, 'Hoodie', 'XXXL', '', '2026-06-04 20:10:00'),
(62, 'Hoodie', 'XXXXL', '', '2026-06-04 20:10:00'),
(63, 'Sweater', 'S', '', '2026-06-04 20:10:00'),
(64, 'Sweater', 'M', '', '2026-06-04 20:10:00'),
(65, 'Sweater', 'L', '', '2026-06-04 20:10:00'),
(66, 'Sweater', 'XL', '', '2026-06-04 20:10:00'),
(67, 'Sweater', 'XXL', '', '2026-06-04 20:10:00'),
(68, 'Sweater', 'XXXL', '', '2026-06-04 20:10:00'),
(69, 'Sweater', 'XXXXL', '', '2026-06-04 20:10:00'),
(70, 'Vest', 'S', '', '2026-06-04 20:10:00'),
(71, 'Vest', 'M', '', '2026-06-04 20:10:00'),
(72, 'Vest', 'L', '', '2026-06-04 20:10:00'),
(73, 'Vest', 'XL', '', '2026-06-04 20:10:00'),
(74, 'Vest', 'XXL', '', '2026-06-04 20:10:00'),
(75, 'Vest', 'XXXL', '', '2026-06-04 20:10:00'),
(76, 'Vest', 'XXXXL', '', '2026-06-04 20:10:00'),
(77, 'Rompi', 'S', '', '2026-06-04 20:10:00'),
(78, 'Rompi', 'M', '', '2026-06-04 20:10:00'),
(79, 'Rompi', 'L', '', '2026-06-04 20:10:00'),
(80, 'Rompi', 'XL', '', '2026-06-04 20:10:00'),
(81, 'Rompi', 'XXL', '', '2026-06-04 20:10:00'),
(82, 'Rompi', 'XXXL', '', '2026-06-04 20:10:00'),
(83, 'Rompi', 'XXXXL', '', '2026-06-04 20:10:00'),
(84, 'Rok', 'S', '', '2026-06-04 20:10:00'),
(85, 'Rok', 'M', '', '2026-06-04 20:10:00'),
(86, 'Rok', 'L', '', '2026-06-04 20:10:00'),
(87, 'Rok', 'XL', '', '2026-06-04 20:10:00'),
(88, 'Rok', 'XXL', '', '2026-06-04 20:10:00'),
(89, 'Rok', 'XXXL', '', '2026-06-04 20:10:00'),
(90, 'Rok', 'XXXXL', '', '2026-06-04 20:10:00'),
(91, 'Legging', 'S', '', '2026-06-04 20:10:00'),
(92, 'Legging', 'M', '', '2026-06-04 20:10:00'),
(93, 'Legging', 'L', '', '2026-06-04 20:10:00'),
(94, 'Legging', 'XL', '', '2026-06-04 20:10:00'),
(95, 'Legging', 'XXL', '', '2026-06-04 20:10:00'),
(96, 'Legging', 'XXXL', '', '2026-06-04 20:10:00'),
(97, 'Legging', 'XXXXL', '', '2026-06-04 20:10:00'),
(98, 'Shorts', 'S', '', '2026-06-04 20:10:00'),
(99, 'Shorts', 'M', '', '2026-06-04 20:10:00'),
(100, 'Shorts', 'L', '', '2026-06-04 20:10:00'),
(101, 'Shorts', 'XL', '', '2026-06-04 20:10:00'),
(102, 'Shorts', 'XXL', '', '2026-06-04 20:10:00'),
(103, 'Shorts', 'XXXL', '', '2026-06-04 20:10:00'),
(104, 'Shorts', 'XXXXL', '', '2026-06-04 20:10:00'),
(105, 'Blazer', 'S', '', '2026-06-04 20:10:00'),
(106, 'Blazer', 'M', '', '2026-06-04 20:10:00'),
(107, 'Blazer', 'L', '', '2026-06-04 20:10:00'),
(108, 'Blazer', 'XL', '', '2026-06-04 20:10:00'),
(109, 'Blazer', 'XXL', '', '2026-06-04 20:10:00'),
(110, 'Blazer', 'XXXL', '', '2026-06-04 20:10:00'),
(111, 'Blazer', 'XXXXL', '', '2026-06-04 20:10:00'),
(112, 'Cardigan', 'S', '', '2026-06-04 20:10:00'),
(113, 'Cardigan', 'M', '', '2026-06-04 20:10:00'),
(114, 'Cardigan', 'L', '', '2026-06-04 20:10:00'),
(115, 'Cardigan', 'XL', '', '2026-06-04 20:10:00'),
(116, 'Cardigan', 'XXL', '', '2026-06-04 20:10:00'),
(117, 'Cardigan', 'XXXL', '', '2026-06-04 20:10:00'),
(118, 'Cardigan', 'XXXXL', '', '2026-06-04 20:10:00'),
(119, 'Parka', 'S', '', '2026-06-04 20:10:00'),
(120, 'Parka', 'M', '', '2026-06-04 20:10:00'),
(121, 'Parka', 'L', '', '2026-06-04 20:10:00'),
(122, 'Parka', 'XL', '', '2026-06-04 20:10:00'),
(123, 'Parka', 'XXL', '', '2026-06-04 20:10:00'),
(124, 'Parka', 'XXXL', '', '2026-06-04 20:10:00'),
(125, 'Parka', 'XXXXL', '', '2026-06-04 20:10:00'),
(126, 'Trencher', 'S', '', '2026-06-04 20:10:00'),
(127, 'Trencher', 'M', '', '2026-06-04 20:10:00'),
(128, 'Trencher', 'L', '', '2026-06-04 20:10:00'),
(129, 'Trencher', 'XL', '', '2026-06-04 20:10:00'),
(130, 'Trencher', 'XXL', '', '2026-06-04 20:10:00'),
(131, 'Trencher', 'XXXL', '', '2026-06-04 20:10:00'),
(132, 'Trencher', 'XXXXL', '', '2026-06-04 20:10:00'),
(133, 'Abaya', 'S', '', '2026-06-04 20:10:00'),
(134, 'Abaya', 'M', '', '2026-06-04 20:10:00'),
(135, 'Abaya', 'L', '', '2026-06-04 20:10:00'),
(136, 'Abaya', 'XL', '', '2026-06-04 20:10:00'),
(137, 'Abaya', 'XXL', '', '2026-06-04 20:10:00'),
(138, 'Abaya', 'XXXL', '', '2026-06-04 20:10:00'),
(139, 'Abaya', 'XXXXL', '', '2026-06-04 20:10:00'),
(140, 'Koko', 'S', '', '2026-06-04 20:10:00'),
(141, 'Koko', 'M', '', '2026-06-04 20:10:00'),
(142, 'Koko', 'L', '', '2026-06-04 20:10:00'),
(143, 'Koko', 'XL', '', '2026-06-04 20:10:00'),
(144, 'Koko', 'XXL', '', '2026-06-04 20:10:00'),
(145, 'Koko', 'XXXL', '', '2026-06-04 20:10:00'),
(146, 'Koko', 'XXXXL', '', '2026-06-04 20:10:00'),
(147, 'Sarung', 'S', '', '2026-06-04 20:10:00'),
(148, 'Sarung', 'M', '', '2026-06-04 20:10:00'),
(149, 'Sarung', 'L', '', '2026-06-04 20:10:00'),
(150, 'Sarung', 'XL', '', '2026-06-04 20:10:00'),
(151, 'Sarung', 'XXL', '', '2026-06-04 20:10:00'),
(152, 'Sarung', 'XXXL', '', '2026-06-04 20:10:00'),
(153, 'Sarung', 'XXXXL', '', '2026-06-04 20:10:00'),
(154, 'Hijab', 'S', '', '2026-06-04 20:10:00'),
(155, 'Hijab', 'M', '', '2026-06-04 20:10:00'),
(156, 'Hijab', 'L', '', '2026-06-04 20:10:00'),
(157, 'Hijab', 'XL', '', '2026-06-04 20:10:00'),
(158, 'Hijab', 'XXL', '', '2026-06-04 20:10:00'),
(159, 'Hijab', 'XXXL', '', '2026-06-04 20:10:00'),
(160, 'Hijab', 'XXXXL', '', '2026-06-04 20:10:00'),
(161, 'Baju Anak', 'S', '', '2026-06-04 20:10:00'),
(162, 'Baju Anak', 'M', '', '2026-06-04 20:10:00'),
(163, 'Baju Anak', 'L', '', '2026-06-04 20:10:00'),
(164, 'Baju Anak', 'XL', '', '2026-06-04 20:10:00'),
(165, 'Baju Anak', 'XXL', '', '2026-06-04 20:10:00'),
(166, 'Baju Anak', 'XXXL', '', '2026-06-04 20:10:00'),
(167, 'Baju Anak', 'XXXXL', '', '2026-06-04 20:10:00'),
(168, 'Baju Bayi', 'S', '', '2026-06-04 20:10:00'),
(169, 'Baju Bayi', 'M', '', '2026-06-04 20:10:00'),
(170, 'Baju Bayi', 'L', '', '2026-06-04 20:10:00'),
(171, 'Baju Bayi', 'XL', '', '2026-06-04 20:10:00'),
(172, 'Baju Bayi', 'XXL', '', '2026-06-04 20:10:00'),
(173, 'Baju Bayi', 'XXXL', '', '2026-06-04 20:10:00'),
(174, 'Baju Bayi', 'XXXXL', '', '2026-06-04 20:10:00'),
(175, 'Baju Olahraga', 'S', '', '2026-06-04 20:10:00'),
(176, 'Baju Olahraga', 'M', '', '2026-06-04 20:10:00'),
(177, 'Baju Olahraga', 'L', '', '2026-06-04 20:10:00'),
(178, 'Baju Olahraga', 'XL', '', '2026-06-04 20:10:00'),
(179, 'Baju Olahraga', 'XXL', '', '2026-06-04 20:10:00'),
(180, 'Baju Olahraga', 'XXXL', '', '2026-06-04 20:10:00'),
(181, 'Baju Olahraga', 'XXXXL', '', '2026-06-04 20:10:00'),
(182, 'Jersey', 'S', '', '2026-06-04 20:10:00'),
(183, 'Jersey', 'M', '', '2026-06-04 20:10:00'),
(184, 'Jersey', 'L', '', '2026-06-04 20:10:00'),
(185, 'Jersey', 'XL', '', '2026-06-04 20:10:00'),
(186, 'Jersey', 'XXL', '', '2026-06-04 20:10:00'),
(187, 'Jersey', 'XXXL', '', '2026-06-04 20:10:00'),
(188, 'Jersey', 'XXXXL', '', '2026-06-04 20:10:00'),
(189, 'Singlet Olahraga', 'S', '', '2026-06-04 20:10:00'),
(190, 'Singlet Olahraga', 'M', '', '2026-06-04 20:10:00'),
(191, 'Singlet Olahraga', 'L', '', '2026-06-04 20:10:00'),
(192, 'Singlet Olahraga', 'XL', '', '2026-06-04 20:10:00'),
(193, 'Singlet Olahraga', 'XXL', '', '2026-06-04 20:10:00'),
(194, 'Singlet Olahraga', 'XXXL', '', '2026-06-04 20:10:00'),
(195, 'Singlet Olahraga', 'XXXXL', '', '2026-06-04 20:10:00'),
(196, 'Celana Olahraga', 'S', '', '2026-06-04 20:10:00'),
(197, 'Celana Olahraga', 'M', '', '2026-06-04 20:10:00'),
(198, 'Celana Olahraga', 'L', '', '2026-06-04 20:10:00'),
(199, 'Celana Olahraga', 'XL', '', '2026-06-04 20:10:00'),
(200, 'Celana Olahraga', 'XXL', '', '2026-06-04 20:10:00'),
(201, 'Celana Olahraga', 'XXXL', '', '2026-06-04 20:10:00'),
(202, 'Celana Olahraga', 'XXXXL', '', '2026-06-04 20:10:00'),
(203, 'Jaket Olahraga', 'S', '', '2026-06-04 20:10:00'),
(204, 'Jaket Olahraga', 'M', '', '2026-06-04 20:10:00'),
(205, 'Jaket Olahraga', 'L', '', '2026-06-04 20:10:00'),
(206, 'Jaket Olahraga', 'XL', '', '2026-06-04 20:10:00'),
(207, 'Jaket Olahraga', 'XXL', '', '2026-06-04 20:10:00'),
(208, 'Jaket Olahraga', 'XXXL', '', '2026-06-04 20:10:00'),
(209, 'Jaket Olahraga', 'XXXXL', '', '2026-06-04 20:10:00'),
(210, 'Tracktop', 'S', '', '2026-06-04 20:10:00'),
(211, 'Tracktop', 'M', '', '2026-06-04 20:10:00'),
(212, 'Tracktop', 'L', '', '2026-06-04 20:10:00'),
(213, 'Tracktop', 'XL', '', '2026-06-04 20:10:00'),
(214, 'Tracktop', 'XXL', '', '2026-06-04 20:10:00'),
(215, 'Tracktop', 'XXXL', '', '2026-06-04 20:10:00'),
(216, 'Tracktop', 'XXXXL', '', '2026-06-04 20:10:00'),
(217, 'Baju Renang', 'S', '', '2026-06-04 20:10:00'),
(218, 'Baju Renang', 'M', '', '2026-06-04 20:10:00'),
(219, 'Baju Renang', 'L', '', '2026-06-04 20:10:00'),
(220, 'Baju Renang', 'XL', '', '2026-06-04 20:10:00'),
(221, 'Baju Renang', 'XXL', '', '2026-06-04 20:10:00'),
(222, 'Baju Renang', 'XXXL', '', '2026-06-04 20:10:00'),
(223, 'Baju Renang', 'XXXXL', '', '2026-06-04 20:10:00'),
(224, 'Apron', 'S', '', '2026-06-04 20:10:00'),
(225, 'Apron', 'M', '', '2026-06-04 20:10:00'),
(226, 'Apron', 'L', '', '2026-06-04 20:10:00'),
(227, 'Apron', 'XL', '', '2026-06-04 20:10:00'),
(228, 'Apron', 'XXL', '', '2026-06-04 20:10:00'),
(229, 'Apron', 'XXXL', '', '2026-06-04 20:10:00'),
(230, 'Apron', 'XXXXL', '', '2026-06-04 20:10:00'),
(231, 'Overall', 'S', '', '2026-06-04 20:10:00'),
(232, 'Overall', 'M', '', '2026-06-04 20:10:00'),
(233, 'Overall', 'L', '', '2026-06-04 20:10:00'),
(234, 'Overall', 'XL', '', '2026-06-04 20:10:00'),
(235, 'Overall', 'XXL', '', '2026-06-04 20:10:00'),
(236, 'Overall', 'XXXL', '', '2026-06-04 20:10:00'),
(237, 'Overall', 'XXXXL', '', '2026-06-04 20:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `ukuran_pelanggan`
--

CREATE TABLE `ukuran_pelanggan` (
  `id_ukuran` int NOT NULL,
  `id_pelanggan` int NOT NULL,
  `ukuran` varchar(50) NOT NULL,
  `catatan` text,
  `update_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ukuran_pelanggan`
--

INSERT INTO `ukuran_pelanggan` (`id_ukuran`, `id_pelanggan`, `ukuran`, `catatan`, `update_at`) VALUES
(1, 3, 'XL', 'Lingkar Dada 80cm', '2026-06-03 12:11:33'),
(2, 1, 'XXXL', 'lingkar dada 120cm', '2026-06-02 09:51:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `detail_transaksi_ibfk_2` (`id_produk`);

--
-- Indexes for table `diskon`
--
ALTER TABLE `diskon`
  ADD PRIMARY KEY (`id_diskon`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `dokumen_transaksi`
--
ALTER TABLE `dokumen_transaksi`
  ADD PRIMARY KEY (`id_dokumen`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_pembayaran` (`id_pembayaran`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`id_pengiriman`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `pesan_jahit`
--
ALTER TABLE `pesan_jahit`
  ADD PRIMARY KEY (`id_pesan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_ukuran_model` (`id_ukuran_model`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `ukuran_model`
--
ALTER TABLE `ukuran_model`
  ADD PRIMARY KEY (`id_ukuran_model`);

--
-- Indexes for table `ukuran_pelanggan`
--
ALTER TABLE `ukuran_pelanggan`
  ADD PRIMARY KEY (`id_ukuran`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `diskon`
--
ALTER TABLE `diskon`
  MODIFY `id_diskon` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dokumen_transaksi`
--
ALTER TABLE `dokumen_transaksi`
  MODIFY `id_dokumen` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `pengiriman`
--
ALTER TABLE `pengiriman`
  MODIFY `id_pengiriman` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pesan_jahit`
--
ALTER TABLE `pesan_jahit`
  MODIFY `id_pesan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `ukuran_model`
--
ALTER TABLE `ukuran_model`
  MODIFY `id_ukuran_model` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=238;

--
-- AUTO_INCREMENT for table `ukuran_pelanggan`
--
ALTER TABLE `ukuran_pelanggan`
  MODIFY `id_ukuran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE SET NULL;

--
-- Constraints for table `diskon`
--
ALTER TABLE `diskon`
  ADD CONSTRAINT `diskon_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`);

--
-- Constraints for table `dokumen_transaksi`
--
ALTER TABLE `dokumen_transaksi`
  ADD CONSTRAINT `dokumen_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `dokumen_transaksi_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`),
  ADD CONSTRAINT `dokumen_transaksi_ibfk_3` FOREIGN KEY (`id_pembayaran`) REFERENCES `pembayaran` (`id_pembayaran`) ON DELETE SET NULL;

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL;

--
-- Constraints for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD CONSTRAINT `pengiriman_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `pengiriman_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL;

--
-- Constraints for table `pesan_jahit`
--
ALTER TABLE `pesan_jahit`
  ADD CONSTRAINT `pesan_jahit_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`);

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_ukuran_model`) REFERENCES `ukuran_model` (`id_ukuran_model`) ON DELETE SET NULL;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`);

--
-- Constraints for table `ukuran_pelanggan`
--
ALTER TABLE `ukuran_pelanggan`
  ADD CONSTRAINT `ukuran_pelanggan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
