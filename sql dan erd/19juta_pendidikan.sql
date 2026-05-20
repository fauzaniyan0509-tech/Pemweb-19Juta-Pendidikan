-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Bulan Mei 2026 pada 13.47
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `email`, `password`) VALUES
(1, 'budi', 'budi@gmail.com', '55555555');

-- --------------------------------------------------------

--
-- Struktur dari tabel `beasiswa`
--

CREATE TABLE `beasiswa` (
  `id_beasiswa` int(11) NOT NULL,
  `nama_beasiswa` varchar(200) NOT NULL,
  `penyelenggara` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `deadline` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dikelola`
--

CREATE TABLE `dikelola` (
  `id_pengelolaan_lomba` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `id_lomba` int(11) NOT NULL,
  `status_kelola` enum('tambah','ubah','hapus','verifikasi','arsip') DEFAULT 'tambah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `iklan_lomba`
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
-- Dumping data untuk tabel `iklan_lomba`
--

INSERT INTO `iklan_lomba` (`id_iklan`, `id_lomba`, `id_pembayaran`, `id_user`, `judul_iklan`, `status_verifikasi`, `paket_langganan`, `is_read`) VALUES
(1, 3, 3, 1, 'ghgjty m ', 'disetujui', 'Paket Langganan Tahunan', 0),
(2, 4, 4, 1, 'ghgjty m ', 'disetujui', 'Paket Langganan Tahunan', 0),
(3, 5, 5, 1, 'FA Cup', 'disetujui', 'Paket Per Lomba', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `lomba`
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
-- Dumping data untuk tabel `lomba`
--

INSERT INTO `lomba` (`id_lomba`, `judul_lomba`, `penyelenggara`, `kategori`, `tingkat_lomba`, `deskripsi`, `poster`, `biaya`, `deadline`, `tipe_biaya`, `status_publish`) VALUES
(2, 'rrr', 'rrr', 'Desain', 'Regional', 'nmgchkhu', NULL, 0, '2222-02-22', 'Gratis', 'pending'),
(3, 'rrr', 'rrr', 'Desain', 'Regional', 'nmgchkhu', NULL, 0, '2222-02-22', 'Gratis', 'pending'),
(4, 'UCL', 'UEFA', 'Akademik', 'Internasional', 'UCL adalah singkatan dari UEFA Champions League (atau dalam bahasa Indonesia dikenal sebagai Liga Champions UEFA). Ini adalah kompetisi sepak bola antarklub paling bergengsi di Eropa yang diselenggara', '1779276165_6a0d99858d201.jpg', 50000000, '2026-06-23', 'Berbayar', 'pending'),
(5, 'FA cup', ' The Football Association', 'Akademik', 'Internasional', 'Piala FA (secara resmi disebut The Football Association Challenge Cup) adalah kompetisi sepak bola sistem gugur tahunan tertua di dunia yang diselenggarakan oleh Asosiasi Sepak Bola Inggris (FA). Turn', 'poster_1779243807_449.jpg', 2000000, '2026-10-23', 'Berbayar', 'pending');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mencari`
--

CREATE TABLE `mencari` (
  `id_pencarian` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_tempat` int(11) DEFAULT NULL,
  `nama_tempat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mendaftar`
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
-- Struktur dari tabel `mengakses`
--

CREATE TABLE `mengakses` (
  `id_user` int(11) NOT NULL,
  `id_beasiswa` int(11) NOT NULL,
  `waktu_akses` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mengelola_beasiswa`
--

CREATE TABLE `mengelola_beasiswa` (
  `id_pengelolaan_beasiswa` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `id_beasiswa` int(11) NOT NULL,
  `status_kelola` enum('tambah','ubah','hapus') DEFAULT 'tambah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mengelola_tempat`
--

CREATE TABLE `mengelola_tempat` (
  `id_pengelolaan_tempat` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `id_tempat` int(11) NOT NULL,
  `status_kelola` enum('tambah','ubah','hapus') DEFAULT 'tambah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
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
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_user`, `id_admin`, `jumlah`, `metode_pembayaran`, `bukti_pembayaran`, `status_pembayaran`, `tanggal_bayar`) VALUES
(1, 1, NULL, 499000, 'Transfer Bank BCA', 'bukti_1779218538_542.png', 'pending', '2026-05-20 02:22:18'),
(2, 1, NULL, 499000, 'Transfer Bank BCA', 'bukti_1779218619_322.png', 'pending', '2026-05-20 02:23:39'),
(3, 1, NULL, 499000, 'Transfer Bank BCA', 'bukti_1779218824_437.png', 'pending', '2026-05-20 02:27:04'),
(4, 1, NULL, 499000, 'Transfer Bank BCA', 'bukti_1779219083_983.png', 'pending', '2026-05-20 02:31:23'),
(5, 1, NULL, 50000, 'Transfer Bank Mandiri', 'bukti_1779243807_555.png', 'pending', '2026-05-20 09:23:27'),
(6, 1, NULL, 50000, 'Transfer Bank BCA', 'bukti_1779264132_536.jpg', 'pending', '2026-05-20 15:02:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tempat_edukatif`
--

CREATE TABLE `tempat_edukatif` (
  `id_tempat` int(11) NOT NULL,
  `nama_tempat_edukatif` varchar(100) NOT NULL,
  `kategori` varchar(30) NOT NULL,
  `alamat_maps` varchar(100) NOT NULL,
  `jam_operasional` varchar(100) DEFAULT NULL,
  `prasarana` varchar(200) DEFAULT NULL,
  `sosial_media` varchar(100) DEFAULT NULL,
  `rating` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(5) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `foto_profil` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `password`, `foto_profil`) VALUES
(1, 'primahadiramadhan03', 'primahad@gmail.com', '$2y$10$YIvLWiVVGbC2ZzILYnFBru231Zu4Jsdwth6Ju99mrPlkZzCWFBf9i', ''),
(2, 'arif tukimin', 'pepep@gmail.com', '$2y$10$.Y/mnQG.2MpHnxkCY0wJEeifCSJ8CvYmyjYZXRVUoal4jORnroWoW', ''),
(3, 'el gasing', 'contoh@email', '$2y$10$BP3FH6GjWmJimaKgm65zm./KwB/940idd4G8Uzy5Fv7ztltdx2pX2', '');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `beasiswa`
--
ALTER TABLE `beasiswa`
  ADD PRIMARY KEY (`id_beasiswa`);

--
-- Indeks untuk tabel `dikelola`
--
ALTER TABLE `dikelola`
  ADD PRIMARY KEY (`id_pengelolaan_lomba`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_lomba` (`id_lomba`);

--
-- Indeks untuk tabel `iklan_lomba`
--
ALTER TABLE `iklan_lomba`
  ADD PRIMARY KEY (`id_iklan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `fk_iklan_lomba` (`id_lomba`),
  ADD KEY `fk_iklan_pembayaran` (`id_pembayaran`);

--
-- Indeks untuk tabel `lomba`
--
ALTER TABLE `lomba`
  ADD PRIMARY KEY (`id_lomba`);

--
-- Indeks untuk tabel `mencari`
--
ALTER TABLE `mencari`
  ADD PRIMARY KEY (`id_pencarian`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_tempat` (`id_tempat`);

--
-- Indeks untuk tabel `mendaftar`
--
ALTER TABLE `mendaftar`
  ADD PRIMARY KEY (`id_pendaftaran`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_lomba` (`id_lomba`);

--
-- Indeks untuk tabel `mengakses`
--
ALTER TABLE `mengakses`
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_beasiswa` (`id_beasiswa`);

--
-- Indeks untuk tabel `mengelola_beasiswa`
--
ALTER TABLE `mengelola_beasiswa`
  ADD PRIMARY KEY (`id_pengelolaan_beasiswa`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_beasiswa` (`id_beasiswa`);

--
-- Indeks untuk tabel `mengelola_tempat`
--
ALTER TABLE `mengelola_tempat`
  ADD PRIMARY KEY (`id_pengelolaan_tempat`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_tempat` (`id_tempat`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indeks untuk tabel `tempat_edukatif`
--
ALTER TABLE `tempat_edukatif`
  ADD PRIMARY KEY (`id_tempat`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `beasiswa`
--
ALTER TABLE `beasiswa`
  MODIFY `id_beasiswa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dikelola`
--
ALTER TABLE `dikelola`
  MODIFY `id_pengelolaan_lomba` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `iklan_lomba`
--
ALTER TABLE `iklan_lomba`
  MODIFY `id_iklan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `lomba`
--
ALTER TABLE `lomba`
  MODIFY `id_lomba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `mencari`
--
ALTER TABLE `mencari`
  MODIFY `id_pencarian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `mendaftar`
--
ALTER TABLE `mendaftar`
  MODIFY `id_pendaftaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `mengelola_beasiswa`
--
ALTER TABLE `mengelola_beasiswa`
  MODIFY `id_pengelolaan_beasiswa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `mengelola_tempat`
--
ALTER TABLE `mengelola_tempat`
  MODIFY `id_pengelolaan_tempat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tempat_edukatif`
--
ALTER TABLE `tempat_edukatif`
  MODIFY `id_tempat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dikelola`
--
ALTER TABLE `dikelola`
  ADD CONSTRAINT `dikelola_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE CASCADE,
  ADD CONSTRAINT `dikelola_ibfk_2` FOREIGN KEY (`id_lomba`) REFERENCES `lomba` (`id_lomba`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `iklan_lomba`
--
ALTER TABLE `iklan_lomba`
  ADD CONSTRAINT `fk_iklan_lomba` FOREIGN KEY (`id_lomba`) REFERENCES `lomba` (`id_lomba`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_iklan_pembayaran` FOREIGN KEY (`id_pembayaran`) REFERENCES `pembayaran` (`id_pembayaran`) ON DELETE CASCADE,
  ADD CONSTRAINT `iklan_lomba_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mencari`
--
ALTER TABLE `mencari`
  ADD CONSTRAINT `mencari_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `mencari_ibfk_2` FOREIGN KEY (`id_tempat`) REFERENCES `tempat_edukatif` (`id_tempat`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `mendaftar`
--
ALTER TABLE `mendaftar`
  ADD CONSTRAINT `mendaftar_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `mendaftar_ibfk_2` FOREIGN KEY (`id_lomba`) REFERENCES `lomba` (`id_lomba`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mengakses`
--
ALTER TABLE `mengakses`
  ADD CONSTRAINT `mengakses_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `mengakses_ibfk_2` FOREIGN KEY (`id_beasiswa`) REFERENCES `beasiswa` (`id_beasiswa`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mengelola_beasiswa`
--
ALTER TABLE `mengelola_beasiswa`
  ADD CONSTRAINT `mengelola_beasiswa_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE CASCADE,
  ADD CONSTRAINT `mengelola_beasiswa_ibfk_2` FOREIGN KEY (`id_beasiswa`) REFERENCES `beasiswa` (`id_beasiswa`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mengelola_tempat`
--
ALTER TABLE `mengelola_tempat`
  ADD CONSTRAINT `mengelola_tempat_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE CASCADE,
  ADD CONSTRAINT `mengelola_tempat_ibfk_2` FOREIGN KEY (`id_tempat`) REFERENCES `tempat_edukatif` (`id_tempat`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
