-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2026 at 03:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `19juta_pendidikan`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `email`, `password`) VALUES
(1, 'budi', 'budi@gmail.com', '55555555');

-- --------------------------------------------------------

--
-- Table structure for table `beasiswa`
--

CREATE TABLE `beasiswa` (
  `id_beasiswa` int(11) NOT NULL,
  `nama_beasiswa` varchar(200) NOT NULL,
  `penyelenggara` varchar(150) NOT NULL,
  `jenjang` varchar(50) NOT NULL,
  `tingkat_beasiswa` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `poster` varchar(255) NOT NULL,
  `deadline` date DEFAULT NULL,
  `tipe_pendanaan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beasiswa`
--

INSERT INTO `beasiswa` (`id_beasiswa`, `nama_beasiswa`, `penyelenggara`, `jenjang`, `tingkat_beasiswa`, `deskripsi`, `poster`, `deadline`, `tipe_pendanaan`) VALUES
(2, '111', 'UEFA', 'D3/D4/S1', 'Instansi', '222', 'poster_1779815904_451.PNG', '2026-11-11', 'Fully Funded'),
(3, '111', ' The Football Association', 'Umum', 'Instansi', 'uuuu', 'poster_1779816203_317.PNG', '2027-03-22', 'Partial Funded');

-- --------------------------------------------------------

--
-- Table structure for table `dikelola`
--

CREATE TABLE `dikelola` (
  `id_pengelolaan_lomba` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `id_lomba` int(11) NOT NULL,
  `status_kelola` enum('tambah','ubah','hapus','verifikasi','arsip') DEFAULT 'tambah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `iklan_beasiswa`
--

CREATE TABLE `iklan_beasiswa` (
  `id_iklan` int(11) NOT NULL,
  `id_beasiswa` int(11) NOT NULL,
  `id_pembayaran` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `judul_iklan` varchar(200) DEFAULT NULL,
  `status_verifikasi` enum('menunggu','disetujui','ditolak') DEFAULT 'menunggu',
  `paket_langganan` varchar(50) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `iklan_beasiswa`
--

INSERT INTO `iklan_beasiswa` (`id_iklan`, `id_beasiswa`, `id_pembayaran`, `id_user`, `judul_iklan`, `status_verifikasi`, `paket_langganan`, `is_read`) VALUES
(2, 2, 16, 1, 'ghgjty m ', 'disetujui', 'Paket Per Beasiswa', 0),
(3, 3, 17, 1, 'Coding 2026', 'disetujui', 'Paket Langganan Tahunan', 0);

-- --------------------------------------------------------

--
-- Table structure for table `iklan_lomba`
--

CREATE TABLE `iklan_lomba` (
  `id_iklan` int(11) NOT NULL,
  `id_lomba` int(11) NOT NULL,
  `id_pembayaran` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `judul_iklan` varchar(200) DEFAULT NULL,
  `status_verifikasi` enum('menunggu','disetujui','ditolak') DEFAULT 'menunggu',
  `paket_langganan` varchar(50) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `iklan_lomba`
--

INSERT INTO `iklan_lomba` (`id_iklan`, `id_lomba`, `id_pembayaran`, `id_user`, `judul_iklan`, `status_verifikasi`, `paket_langganan`, `is_read`) VALUES
(2, 4, 4, 1, 'ghgjty m ', 'disetujui', 'Paket Langganan Tahunan', 0),
(3, 5, 5, 1, 'FA Cup', 'disetujui', 'Paket Per Lomba', 0),
(5, 7, 7, 1, 'SIC', 'disetujui', 'Paket Per Lomba', 0),
(7, 9, 9, 1, 'gggg', 'ditolak', 'Paket Langganan Tahunan', 0),
(8, 10, 10, 1, 'gggg', 'ditolak', 'Paket Langganan Tahunan', 0);

-- --------------------------------------------------------

--
-- Table structure for table `lomba`
--

CREATE TABLE `lomba` (
  `id_lomba` int(11) NOT NULL,
  `judul_lomba` varchar(200) NOT NULL,
  `penyelenggara` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `tingkat_lomba` varchar(50) NOT NULL,
  `deskripsi` varchar(200) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `biaya` int(11) DEFAULT 0,
  `deadline` date DEFAULT NULL,
  `tipe_biaya` enum('Gratis','Berbayar') NOT NULL,
  `status_publish` enum('aktif','nonaktif','pending') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lomba`
--

INSERT INTO `lomba` (`id_lomba`, `judul_lomba`, `penyelenggara`, `kategori`, `tingkat_lomba`, `deskripsi`, `poster`, `biaya`, `deadline`, `tipe_biaya`, `status_publish`) VALUES
(4, 'UCL', 'UEFA', 'Akademik', 'Internasional', 'UCL adalah singkatan dari UEFA Champions League (atau dalam bahasa Indonesia dikenal sebagai Liga Champions UEFA). Ini adalah kompetisi sepak bola antarklub paling bergengsi di Eropa yang diselenggara', '1779276165_6a0d99858d201.jpg', 0, '2026-06-23', 'Gratis', 'pending'),
(5, 'FA cup', ' The Football Association', 'Akademik', 'Internasional', 'Piala FA (secara resmi disebut The Football Association Challenge Cup) adalah kompetisi sepak bola sistem gugur tahunan tertua di dunia yang diselenggarakan oleh Asosiasi Sepak Bola Inggris (FA). Turn', 'poster_1779243807_449.jpg', 2000000, '2026-10-23', 'Berbayar', 'pending'),
(7, 'Smart IT Competition', 'Emailkomp', 'Akademik', 'Nasional', 'Smart IT Competition adalah serangkaian ajang perlombaan teknologi tahunan yang diselenggarakan oleh Program Studi D3 Teknik Informatika / Sekolah Vokasi Universitas Sebelas Maret (UNS) yang ditujukan', 'poster_1779335445_513.jpg', 300000, '2026-05-31', 'Berbayar', 'pending'),
(9, 'FA cup', 'UEFA', 'Desain', 'Sekolah', '44444', 'poster_1779456913_655.jpg', 333333, '2222-02-22', 'Berbayar', 'pending'),
(10, 'rrr', 'UEFA', 'Desain', 'Nasional', 'nnnn', 'poster_1779457268_872.jpg', 0, '9999-11-11', 'Gratis', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `mencari`
--

CREATE TABLE `mencari` (
  `id_pencarian` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_tempat` int(11) DEFAULT NULL,
  `nama_tempat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mendaftar`
--

CREATE TABLE `mendaftar` (
  `id_pendaftaran` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_lomba` int(11) NOT NULL,
  `tanggal_daftar` datetime DEFAULT current_timestamp(),
  `status` enum('berhasil','menunggu','batal') DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mengakses`
--

CREATE TABLE `mengakses` (
  `id_user` int(11) NOT NULL,
  `id_beasiswa` int(11) NOT NULL,
  `waktu_akses` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mengelola_beasiswa`
--

CREATE TABLE `mengelola_beasiswa` (
  `id_pengelolaan_beasiswa` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `id_beasiswa` int(11) NOT NULL,
  `status_kelola` enum('tambah','ubah','hapus') DEFAULT 'tambah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mengelola_tempat`
--

CREATE TABLE `mengelola_tempat` (
  `id_pengelolaan_tempat` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `id_tempat` int(11) NOT NULL,
  `status_kelola` enum('tambah','ubah','hapus') DEFAULT 'tambah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `jumlah` int(20) NOT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) NOT NULL,
  `status_pembayaran` enum('pending','sukses','gagal') DEFAULT 'pending',
  `tanggal_bayar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_user`, `id_admin`, `jumlah`, `metode_pembayaran`, `bukti_pembayaran`, `status_pembayaran`, `tanggal_bayar`) VALUES
(1, 1, NULL, 499000, 'Transfer Bank BCA', 'bukti_1779218538_542.png', 'pending', '2026-05-20 02:22:18'),
(2, 1, NULL, 499000, 'Transfer Bank BCA', 'bukti_1779218619_322.png', 'pending', '2026-05-20 02:23:39'),
(3, 1, NULL, 499000, 'Transfer Bank BCA', 'bukti_1779218824_437.png', 'pending', '2026-05-20 02:27:04'),
(4, 1, NULL, 499000, 'Transfer Bank BCA', 'bukti_1779219083_983.png', 'pending', '2026-05-20 02:31:23'),
(5, 1, NULL, 50000, 'Transfer Bank Mandiri', 'bukti_1779243807_555.png', 'pending', '2026-05-20 09:23:27'),
(6, 1, NULL, 50000, 'Transfer Bank BCA', 'bukti_1779264132_536.jpg', 'pending', '2026-05-20 15:02:12'),
(7, 1, NULL, 50000, 'Transfer Bank Mandiri', 'bukti_1779335445_911.jpg', 'pending', '2026-05-21 10:50:45'),
(8, 1, NULL, 50000, 'E-Wallet Dana', 'bukti_1779456574_437.jpg', 'pending', '2026-05-22 20:29:34'),
(9, 1, NULL, 499000, 'Transfer Bank BCA', 'bukti_1779456913_400.jpg', 'pending', '2026-05-22 20:35:13'),
(10, 1, NULL, 499000, 'Transfer Bank Mandiri', 'bukti_1779457268_809.jpg', 'pending', '2026-05-22 20:41:08'),
(11, 1, NULL, 50000, 'Transfer Bank Mandiri', 'bukti_1779677966_249.jpg', 'pending', '2026-05-25 09:59:26'),
(12, 1, NULL, 50000, 'Transfer Bank BCA', 'bukti_1779802603_395.png', 'pending', '2026-05-26 20:36:43'),
(13, 1, NULL, 499000, 'E-Wallet Dana', 'bukti_1779808698_510.PNG', 'pending', '2026-05-26 22:18:18'),
(14, 1, NULL, 50000, 'E-Wallet Dana', 'bukti_1779808799_471.PNG', 'pending', '2026-05-26 22:19:59'),
(15, 1, NULL, 50000, 'Transfer Bank BCA', 'bukti_1779808918_268.PNG', 'pending', '2026-05-26 22:21:58'),
(16, 1, NULL, 50000, 'Transfer Bank Mandiri', 'bukti_1779815904_133.PNG', 'pending', '2026-05-27 00:18:24'),
(17, 1, NULL, 499000, 'E-Wallet Dana', 'bukti_1779816203_835.PNG', 'pending', '2026-05-27 00:23:23');

-- --------------------------------------------------------

--
-- Table structure for table `tempat_edukatif`
--

CREATE TABLE `tempat_edukatif` (
  `id_tempat` int(11) NOT NULL,
  `nama_tempat_edukatif` varchar(100) NOT NULL,
  `kategori` varchar(30) NOT NULL,
  `alamat_maps` varchar(100) NOT NULL,
  `jam_operasional` varchar(100) DEFAULT NULL,
  `prasarana` varchar(200) DEFAULT NULL,
  `sosial_media` varchar(100) DEFAULT NULL,
  `rating` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tempat_edukatif`
--

INSERT INTO `tempat_edukatif` (`id_tempat`, `nama_tempat_edukatif`, `kategori`, `alamat_maps`, `jam_operasional`, `prasarana`, `sosial_media`, `rating`, `foto`) VALUES
(1, 'Perpustakaan UNS', 'perpustakaan', 'https://maps.app.goo.gl/q3KXS21cBveGYQKd7', 'Senin-Jumat 07:30-17:30 | Sabtu-Minggu Tutup', 'Wifi, Meja, Kursi, Buku, Mushola, Ac, Kamar Mandi, Tempat Rapat', '@unslibrary', '4.5', '1780504937_6a2059696877e.jpeg'),
(2, 'Kopi Djati', 'kafe-belajar', 'https://maps.app.goo.gl/yerEvMsbepj4dmi38', 'Senin-Minggu Buka: 10.00-00.00', 'Wifi, Meja, Kursi, Stop Kontak, Ac, Musola, Kamar Mandi', '@kopidjati.id', '4', '1780505237_6a205a950b77f.png'),
(3, 'Solo Technopark ', 'teknologi', 'https://maps.app.goo.gl/jiBrhgSurtWsF8SX9', 'Senin-Jumat 07:30-16:00 | Sabtu-Minggu Tutup', 'Wifi, Meja, Kursi, Ac, Ruang Rapat, Stop Kontak, Air Putih', '@solotechnopark_official', '4', '1780505594_6a205bfad0a36.jpeg'),
(4, 'Monumen Pers Nasional', 'museum', 'https://maps.app.goo.gl/AxZaN6HFQNK1kk6U9', 'Senin-Minggu Buka: 09.00-15.00', 'Wifi, Ac, Meja, Kursi, Stop Kontak', '@monoumenpers', '4', '1780505944_6a205d5836d0a.webp'),
(5, 'Burjo Point Uns', 'tempat-makan', 'https://maps.app.goo.gl/BjqJ2ESN4cPg3kh76', 'Senin-Minggu Buka 24 Jam', 'Meja, Kursi', '@burjopoint.uns', '2.5', '1780506192_6a205e5053f1b.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(5) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `foto_profil` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `password`, `foto_profil`) VALUES
(1, 'primahadiramadhan03', 'primahad@gmail.com', '$2y$10$YIvLWiVVGbC2ZzILYnFBru231Zu4Jsdwth6Ju99mrPlkZzCWFBf9i', ''),
(2, 'arif tukimin', 'pepep@gmail.com', '$2y$10$.Y/mnQG.2MpHnxkCY0wJEeifCSJ8CvYmyjYZXRVUoal4jORnroWoW', ''),
(3, 'el gasing', 'contoh@email', '$2y$10$BP3FH6GjWmJimaKgm65zm./KwB/940idd4G8Uzy5Fv7ztltdx2pX2', ''),
(4, 'risti', 'risti@gmail.com', '$2y$10$LFEHaxNwi1vE69DwJuekNeUSUECy4acFkQstrs8KhffDzOwog6d8W', '6a200aba5344b_download (74).jpg');

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
-- Indexes for table `beasiswa`
--
ALTER TABLE `beasiswa`
  ADD PRIMARY KEY (`id_beasiswa`);

--
-- Indexes for table `dikelola`
--
ALTER TABLE `dikelola`
  ADD PRIMARY KEY (`id_pengelolaan_lomba`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_lomba` (`id_lomba`);

--
-- Indexes for table `iklan_beasiswa`
--
ALTER TABLE `iklan_beasiswa`
  ADD PRIMARY KEY (`id_iklan`),
  ADD KEY `fk_iklan_beasiswa_utama` (`id_beasiswa`),
  ADD KEY `fk_iklan_beasiswa_pembayaran` (`id_pembayaran`),
  ADD KEY `fk_iklan_beasiswa_user` (`id_user`);

--
-- Indexes for table `iklan_lomba`
--
ALTER TABLE `iklan_lomba`
  ADD PRIMARY KEY (`id_iklan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `fk_iklan_lomba` (`id_lomba`),
  ADD KEY `fk_iklan_pembayaran` (`id_pembayaran`);

--
-- Indexes for table `lomba`
--
ALTER TABLE `lomba`
  ADD PRIMARY KEY (`id_lomba`);

--
-- Indexes for table `mencari`
--
ALTER TABLE `mencari`
  ADD PRIMARY KEY (`id_pencarian`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_tempat` (`id_tempat`);

--
-- Indexes for table `mendaftar`
--
ALTER TABLE `mendaftar`
  ADD PRIMARY KEY (`id_pendaftaran`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_lomba` (`id_lomba`);

--
-- Indexes for table `mengakses`
--
ALTER TABLE `mengakses`
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_beasiswa` (`id_beasiswa`);

--
-- Indexes for table `mengelola_beasiswa`
--
ALTER TABLE `mengelola_beasiswa`
  ADD PRIMARY KEY (`id_pengelolaan_beasiswa`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_beasiswa` (`id_beasiswa`);

--
-- Indexes for table `mengelola_tempat`
--
ALTER TABLE `mengelola_tempat`
  ADD PRIMARY KEY (`id_pengelolaan_tempat`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_tempat` (`id_tempat`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `tempat_edukatif`
--
ALTER TABLE `tempat_edukatif`
  ADD PRIMARY KEY (`id_tempat`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `beasiswa`
--
ALTER TABLE `beasiswa`
  MODIFY `id_beasiswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dikelola`
--
ALTER TABLE `dikelola`
  MODIFY `id_pengelolaan_lomba` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `iklan_beasiswa`
--
ALTER TABLE `iklan_beasiswa`
  MODIFY `id_iklan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `iklan_lomba`
--
ALTER TABLE `iklan_lomba`
  MODIFY `id_iklan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lomba`
--
ALTER TABLE `lomba`
  MODIFY `id_lomba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `mencari`
--
ALTER TABLE `mencari`
  MODIFY `id_pencarian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mendaftar`
--
ALTER TABLE `mendaftar`
  MODIFY `id_pendaftaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mengelola_beasiswa`
--
ALTER TABLE `mengelola_beasiswa`
  MODIFY `id_pengelolaan_beasiswa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mengelola_tempat`
--
ALTER TABLE `mengelola_tempat`
  MODIFY `id_pengelolaan_tempat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tempat_edukatif`
--
ALTER TABLE `tempat_edukatif`
  MODIFY `id_tempat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dikelola`
--
ALTER TABLE `dikelola`
  ADD CONSTRAINT `dikelola_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE CASCADE,
  ADD CONSTRAINT `dikelola_ibfk_2` FOREIGN KEY (`id_lomba`) REFERENCES `lomba` (`id_lomba`) ON DELETE CASCADE;

--
-- Constraints for table `iklan_beasiswa`
--
ALTER TABLE `iklan_beasiswa`
  ADD CONSTRAINT `fk_iklan_beasiswa_pembayaran` FOREIGN KEY (`id_pembayaran`) REFERENCES `pembayaran` (`id_pembayaran`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_iklan_beasiswa_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_iklan_beasiswa_utama` FOREIGN KEY (`id_beasiswa`) REFERENCES `beasiswa` (`id_beasiswa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `iklan_lomba`
--
ALTER TABLE `iklan_lomba`
  ADD CONSTRAINT `fk_iklan_lomba` FOREIGN KEY (`id_lomba`) REFERENCES `lomba` (`id_lomba`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_iklan_pembayaran` FOREIGN KEY (`id_pembayaran`) REFERENCES `pembayaran` (`id_pembayaran`) ON DELETE CASCADE,
  ADD CONSTRAINT `iklan_lomba_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `mencari`
--
ALTER TABLE `mencari`
  ADD CONSTRAINT `mencari_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `mencari_ibfk_2` FOREIGN KEY (`id_tempat`) REFERENCES `tempat_edukatif` (`id_tempat`) ON DELETE SET NULL;

--
-- Constraints for table `mendaftar`
--
ALTER TABLE `mendaftar`
  ADD CONSTRAINT `mendaftar_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `mendaftar_ibfk_2` FOREIGN KEY (`id_lomba`) REFERENCES `lomba` (`id_lomba`) ON DELETE CASCADE;

--
-- Constraints for table `mengakses`
--
ALTER TABLE `mengakses`
  ADD CONSTRAINT `mengakses_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `mengakses_ibfk_2` FOREIGN KEY (`id_beasiswa`) REFERENCES `beasiswa` (`id_beasiswa`) ON DELETE CASCADE;

--
-- Constraints for table `mengelola_beasiswa`
--
ALTER TABLE `mengelola_beasiswa`
  ADD CONSTRAINT `mengelola_beasiswa_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE CASCADE,
  ADD CONSTRAINT `mengelola_beasiswa_ibfk_2` FOREIGN KEY (`id_beasiswa`) REFERENCES `beasiswa` (`id_beasiswa`) ON DELETE CASCADE;

--
-- Constraints for table `mengelola_tempat`
--
ALTER TABLE `mengelola_tempat`
  ADD CONSTRAINT `mengelola_tempat_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE CASCADE,
  ADD CONSTRAINT `mengelola_tempat_ibfk_2` FOREIGN KEY (`id_tempat`) REFERENCES `tempat_edukatif` (`id_tempat`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
