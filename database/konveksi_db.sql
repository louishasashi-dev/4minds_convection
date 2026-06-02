-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 02, 2026 at 10:26 AM
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
(18, 16, 4, 15, '78000.00', '1170000.00');

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
(4, 15, 2, 17, 'invoice', NULL, '2026-06-02 11:31:51'),
(5, 15, 2, 17, 'nota', NULL, '2026-06-02 11:32:02');

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
(5, 1, 'penjualan', '2026-06-01', '2026-06-02', '[{\"id_transaksi\":\"7\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"batal\",\"tanggal_transaksi\":\"2026-06-02 08:58:16\"},{\"id_transaksi\":\"8\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"371250.00\",\"diskon_total\":\"123750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:02:01\"},{\"id_transaksi\":\"9\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"jahit_satuan\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:07:08\"},{\"id_transaksi\":\"10\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:07:59\"},{\"id_transaksi\":\"11\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:18:45\"},{\"id_transaksi\":\"12\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:21:55\"},{\"id_transaksi\":\"13\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"74250.00\",\"diskon_total\":\"24750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:22:21\"},{\"id_transaksi\":\"14\",\"pelanggan\":\"louis hasashi halim\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"371250.00\",\"diskon_total\":\"123750.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 09:44:41\"},{\"id_transaksi\":\"15\",\"pelanggan\":\"fachrur hannan williyan\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"742500.00\",\"diskon_total\":\"247500.00\",\"status\":\"selesai\",\"tanggal_transaksi\":\"2026-06-02 11:27:24\"},{\"id_transaksi\":\"16\",\"pelanggan\":\"bunaya ardik saputra\",\"jenis_transaksi\":\"pakaian_jadi\",\"total_harga\":\"877500.00\",\"diskon_total\":\"292500.00\",\"status\":\"batal\",\"tanggal_transaksi\":\"2026-06-02 11:34:06\"}]', '2026-06-02 17:01:06');

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
(3, 'bunaya ardik saputra', '1224160079@global.ac.id', '$2y$10$Mzrkdh9/rTmp3RcQA8ArI.e1fGCQSTBYzcjoDPOzbZ9IfRyxBz7Ou', 'pasar kemis', '0895637394487', '2026-06-02 02:21:56');

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
(18, 16, 1, '2026-06-02 11:38:38', 'transfer', 877500, 'bukti_1780375118.png', 'ditolak');

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
(1, 13, 1, 'JNT', '0011', '2026-06-02', '2026-06-02', '2026-06-04', 'sampai');

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
(3, NULL, 'kostum halloween JOKER.', 'Kostum', '99000.00', 50, 'pakaian_jadi', 'Regular', '', 'produk_1780364134.png', '2026-06-02 08:35:34'),
(4, NULL, 'Kaos sablon \"BROOKLYN\"', 'Kaos', '55000.00', 50, 'pakaian_jadi', 'Regular', '', 'produk_1780388690.png', '2026-06-02 11:33:16'),
(5, NULL, 'jaket coklat dan topi', 'jaket', '78000.00', 87, 'pakaian_jadi', 'Regular', 'beli jaket gratis topi', 'produk_1780375424.jpg', '2026-06-02 11:43:44'),
(6, NULL, 'jaket', 'jaket', '85000.00', 150, 'konveksi', 'Regular', 'produksi jaket 85ribu perlusin.', 'produk_1780388652.png', '2026-06-02 11:51:37'),
(7, NULL, 'jersey timnas indonesia', 'jersey', '120000.00', 35, 'pakaian_jadi', 'Regular', '', 'produk_1780388472.png', '2026-06-02 15:21:12'),
(8, NULL, 'The Seoul Syndicate Set', 'Bundle', '1750000.00', 12, 'pakaian_jadi', 'Regular', '\"Power isn\'t given. It\'s taken. command the room before you even speak.\"\r\n\r\nHadirkan aura intimidatif yang elegan dan berwibawa lewat The Seoul Syndicate Set. Terinspirasi dari gaya K-Noir modern, setelan ini dirancang khusus untuk pria yang tidak hanya mementingkan penampilan, tapi juga ingin memancarkan kekuatan, kemewahan, dan karisma mutlak seorang pemimpin.\r\n\r\nPotongan siluet yang tegas berpadu sempurna dengan layering berlapis khas bos mafia, menciptakan ilusi tubuh yang lebih tegap, gagah, dan proporsional.', 'produk_1780389656.png', '2026-06-02 15:39:19'),
(9, NULL, 'Noir Gentleman Suit', 'Bundle', '650000.00', 19, 'pakaian_jadi', 'Regular', '', 'produk_1780390154.png', '2026-06-02 15:49:14'),
(10, NULL, 'The Consigliere Black Suit', 'Bundle', '850000.00', 9, 'pakaian_jadi', 'Regular', 'The Consigliere Black Suit menghadirkan perpaduan sempurna antara kemewahan, ketegasan, dan profesionalisme. Dirancang dengan siluet modern fit yang mengikuti bentuk tubuh, setelan ini memberikan tampilan berkelas layaknya eksekutif muda, pengusaha sukses, maupun tokoh utama dalam drama bisnis modern.\r\n\r\nMengusung desain 3-piece suit yang terdiri dari jas, rompi, dan celana formal, outfit ini cocok digunakan untuk acara resmi, pertemuan bisnis, pesta pernikahan, wisuda, hingga sesi foto profesional.', 'produk_1780390605.png', '2026-06-02 15:56:45'),
(11, NULL, 'Seoul Street Cargo Set', 'Kaos', '249000.00', 23, 'pakaian_jadi', 'Regular', 'Seoul Street Cargo Set menghadirkan gaya streetwear Korea yang santai namun tetap stylish untuk aktivitas sehari-hari. Perpaduan oversized t-shirt dengan cargo pants modern menciptakan tampilan kasual yang sedang tren di kalangan Gen Z.\r\n\r\nPotongan longgar memberikan kenyamanan maksimal, sementara desain cargo menambahkan kesan urban dan fashionable. Cocok digunakan untuk nongkrong, kuliah, jalan-jalan, maupun konten media sosial.', 'produk_1780391627.png', '2026-06-02 16:13:47'),
(12, NULL, 'Blazer Semi-Formal Motif Plaid Klasik', 'Blazer/Jas', '245000.00', 21, 'jahit_satuan', 'Regular', 'Produk pakaian jadi ini menghadirkan kombinasi setelan blazer bermotif kotak-kotak (plaid) cokelat tua yang elegan dengan kemeja putih polos berkerah terbuka sebagai pasangannya. Dibuat menggunakan bahan katun wol premium yang semi-tebal namun tetap adem, blazer ini dirancang dengan potongan regular fit yang memberikan kesan gagah, profesional, sekaligus tetap santai. Sangat cocok dipasarkan untuk kebutuhan seragam kerja kantoran, pakaian acara formal, maupun sebagai koleksi smart-casual siap pakai bagi pelaku usaha fashion retail.', 'produk_1780394883.png', '2026-06-02 17:08:03'),
(13, NULL, 'Executive Corporate Suit', 'Blazer/Jas', '1850000.00', 8, 'pakaian_jadi', 'Premium', 'Setelan jas eksklusif ini dirancang khusus untuk kebutuhan profesional tingkat tinggi, mengusung material high-quality wool blend yang memberikan struktur tegas namun tetap fleksibel untuk menunjang mobilitas kerja. Paket ini terdiri dari jas single-breasted dengan potongan modern fit, celana bahan senada, serta opsi kemeja premium yang dijahit dengan presisi tinggi oleh tenaga ahli konveksi kami. Sangat cocok dijadikan sebagai seragam identitas perusahaan atau pakaian formal bagi para eksekutif yang menginginkan tampilan tajam, berwibawa, dan berkelas dalam setiap pertemuan bisnis.', 'produk_1780395654.png', '2026-06-02 17:20:54');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_pelanggan` int NOT NULL,
  `tanggal_transaksi` datetime DEFAULT CURRENT_TIMESTAMP,
  `jenis_transaksi` enum('jahit_satuan','pakaian_jadi','konveksi') NOT NULL,
  `total_harga` decimal(15,2) DEFAULT '0.00',
  `diskon_total` decimal(15,2) DEFAULT '0.00',
  `jenis_pembayaran` enum('dp','lunas','cod') NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('pending','diproses','selesai','dikirim','lunas','batal') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pelanggan`, `tanggal_transaksi`, `jenis_transaksi`, `total_harga`, `diskon_total`, `jenis_pembayaran`, `tanggal_selesai`, `status`) VALUES
(7, 1, '2026-06-02 08:58:16', 'pakaian_jadi', '74250.00', '24750.00', 'lunas', '2026-06-02', 'batal'),
(8, 2, '2026-06-02 09:02:01', 'pakaian_jadi', '371250.00', '123750.00', 'lunas', '2026-06-02', 'selesai'),
(9, 2, '2026-06-02 09:07:08', 'jahit_satuan', '74250.00', '24750.00', 'lunas', '2026-06-02', 'selesai'),
(10, 2, '2026-06-02 09:07:59', 'pakaian_jadi', '74250.00', '24750.00', 'lunas', '2026-06-02', 'selesai'),
(11, 2, '2026-06-02 09:18:45', 'pakaian_jadi', '74250.00', '24750.00', 'lunas', '2026-06-02', 'selesai'),
(12, 1, '2026-06-02 09:21:55', 'pakaian_jadi', '74250.00', '24750.00', 'lunas', '2026-06-06', 'selesai'),
(13, 1, '2026-06-02 09:22:21', 'pakaian_jadi', '74250.00', '24750.00', 'dp', '2026-06-07', 'selesai'),
(14, 1, '2026-06-02 09:44:41', 'pakaian_jadi', '371250.00', '123750.00', 'lunas', '2026-06-06', 'selesai'),
(15, 2, '2026-06-02 11:27:24', 'pakaian_jadi', '742500.00', '247500.00', 'lunas', '2026-06-02', 'selesai'),
(16, 3, '2026-06-02 11:34:06', 'pakaian_jadi', '877500.00', '292500.00', 'lunas', '2026-06-03', 'batal');

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
(2, 'Jas', 'XXXL', '', '2026-06-02 15:37:42');

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
(1, 3, 'L', 'Lingkar Dada 90cm', '2026-06-02 02:28:51'),
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
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `diskon`
--
ALTER TABLE `diskon`
  MODIFY `id_diskon` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dokumen_transaksi`
--
ALTER TABLE `dokumen_transaksi`
  MODIFY `id_dokumen` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pengiriman`
--
ALTER TABLE `pengiriman`
  MODIFY `id_pengiriman` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `ukuran_model`
--
ALTER TABLE `ukuran_model`
  MODIFY `id_ukuran_model` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
