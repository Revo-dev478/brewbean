-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Jan 2026 pada 20.10
-- Versi server: 10.1.38-MariaDB
-- Versi PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_brewbeans`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `checkout`
--

CREATE TABLE `checkout` (
  `id_checkout` int(11) NOT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `total_harga` int(11) DEFAULT NULL,
  `status_checkout` varchar(20) DEFAULT 'pending',
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `checkout`
--

INSERT INTO `checkout` (`id_checkout`, `order_id`, `id_user`, `total_harga`, `status_checkout`, `metode_pembayaran`, `created_at`) VALUES
(1, 'ORDER-1768330621-620', 8, 780000, 'pending', 'midtrans', '2026-01-14 01:57:01'),
(2, 'ORDER-1768332920-686', 8, 2735000, 'pending', 'midtrans', '2026-01-14 02:35:20'),
(3, 'ORDER-1768333341-545', 8, 860000, 'pending', 'midtrans', '2026-01-14 02:42:21'),
(4, 'ORDER-1768334229-757', 8, 2985000, 'pending', 'midtrans', '2026-01-14 02:57:09'),
(5, 'ORDER-1768335400-711', 9, 2691000, 'pending', 'midtrans', '2026-01-14 03:16:40'),
(6, 'ORDER-1768408560-245', 8, 2833000, 'pending', 'midtrans', '2026-01-14 23:36:00'),
(7, 'ORDER-1768408830-774', 8, 631000, 'pending', 'midtrans', '2026-01-14 23:40:30'),
(8, 'ORDER-1768409053-258', 8, 2783000, 'pending', 'midtrans', '2026-01-14 23:44:13'),
(9, 'ORDER-1768409596-347', 8, 756000, 'pending', 'midtrans', '2026-01-14 23:53:16'),
(10, 'ORDER-1768410119-640', 8, 674500, 'pending', 'midtrans', '2026-01-15 00:01:59'),
(11, 'ORDER-1768410267-125', 8, 679000, 'pending', 'midtrans', '2026-01-15 00:04:27'),
(12, 'ORDER-1768417182-473', 8, 2971000, 'pending', 'midtrans', '2026-01-15 01:59:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tabel_kategori`
--

CREATE TABLE `tabel_kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tabel_kategori`
--

INSERT INTO `tabel_kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Robusta'),
(2, 'Arabika'),
(3, 'Tea'),
(4, 'Robusta'),
(5, 'Arabika'),
(6, 'Tea'),
(7, 'Robusta'),
(8, 'Arabika'),
(9, 'Tea');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tabel_keranjang`
--

CREATE TABLE `tabel_keranjang` (
  `id_keranjang` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  `Tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tabel_keranjang`
--

INSERT INTO `tabel_keranjang` (`id_keranjang`, `id_user`, `id_product`, `qty`, `harga`, `subtotal`, `Tanggal`) VALUES
(3, 7, 2, 2, 48000, 96000, '2025-12-04'),
(5, 7, 12, 2, 8000, 16000, '2025-12-16'),
(6, 7, 8, 2, 32000, 64000, '2025-12-16'),
(7, 7, 4, 1, 65000, 65000, '2025-12-16'),
(10, 8, 2, 9, 48000, 432000, '2026-01-06'),
(11, 8, 1, 5, 45000, 225000, '2026-01-06'),
(12, 8, 8, 2, 32000, 64000, '2026-01-06'),
(13, 9, 3, 2, 50000, 100000, '2026-01-06'),
(14, 9, 1, 1, 45000, 45000, '2026-01-06'),
(15, 9, 2, 2, 48000, 96000, '2026-01-06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tabel_penjual`
--

CREATE TABLE `tabel_penjual` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `tempat` varchar(100) NOT NULL,
  `umur` int(3) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tabel_penjual`
--

INSERT INTO `tabel_penjual` (`id`, `nama`, `password`, `tempat`, `umur`, `tanggal`) VALUES
(2, 'revo', '1234', 'bandung', 12, '2005-05-01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tabel_product`
--

CREATE TABLE `tabel_product` (
  `id_product` int(11) NOT NULL,
  `nama_product` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` int(11) NOT NULL,
  `qty` int(11) DEFAULT NULL,
  `id_kategori` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tabel_product`
--

INSERT INTO `tabel_product` (`id_product`, `nama_product`, `deskripsi`, `harga`, `qty`, `id_kategori`, `gambar`) VALUES
(1, 'Robusta Sumatera', 'Kopi robusta khas Lampung dengan aroma kuat dan aftertaste cokelat.', 45000, 100, 1, 'Robusta-1.jpeg'),
(2, 'Robusta Bali', 'Kopi robusta dengan cita rasa pahit seimbang dan body tebal.', 48000, 100, 1, 'robusta-2.jpeg'),
(3, 'Robusta Toraja', 'Robusta Toraja dengan karakter earthy dan aroma yang khas.', 50000, 100, 1, 'robusta-3.jpeg'),
(4, 'Arabika Gayo', 'Kopi Arabika Gayo dengan aroma lembut dan tingkat keasaman seimbang.', 65000, 100, 2, 'arabika-1.jpeg'),
(5, 'Arabika Flores', 'Arabika Flores dengan cita rasa floral dan body ringan.', 68000, 100, 2, 'arabika-2.jpeg'),
(6, 'Arabika Toraja', 'Arabika khas Toraja dengan aroma rempah dan karakter kompleks.', 70000, 100, 2, 'arabika-3.jpeg'),
(7, 'Lemon Tea Premium', 'Teh lemon premium dengan rasa segar dan aroma alami.', 35000, 100, 3, 'lemon-tea.jpeg'),
(8, 'Matcha Tea Classic Padang', 'Teh hijau matcha dengan cita rasa lembut dan kaya antioksidan.', 32000, 100, 3, 'matcha-tea.jpeg'),
(9, 'Green Tea Premium', 'Green tea pilihan dengan rasa ringan dan aroma harum.', 38000, 100, 3, 'green-tea.jpeg'),
(11, 'Robusta jakarta', 'enak', 50000, 1, 2, '90c4fdb8-81b8-47c2-be12-0fe279b5d992.jpeg'),
(12, 'Robusta aseli', 'mANTAP', 8000, 1, 2, 'arabika-2.jpeg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tabel_user`
--

CREATE TABLE `tabel_user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tabel_user`
--

INSERT INTO `tabel_user` (`id_user`, `username`, `email`, `password`, `phone`) VALUES
(1, 'admin', 'rvandausy@gmail.com', 'admin123', 2147483647),
(2, 'admin1', 'rvandausy1@gmail.com', '$2y$10$nq85DPyGteelMNWwcrHF.uZVI1Gj5B0Lnh8qVHI21oM5hvP3oULhu', 2147483647),
(3, 'revo', 'revony2407@gmail.com', '$2y$10$/3lTF/ZN6K8AzKmrIuonruhV5A9mcwxLfgpO54ad/dSb1ldtfqfIa', 2147483647),
(4, 'yanti', 'febriyantii1102@gmail.com', '$2y$10$3PpQMcjVtIoqpy/KvEXvtex/ZMDX9.or0WWNDt35UG9a1iuw8WMBS', 2147483647),
(6, 'asu', 'taktauiya@gmail.com', '$2y$10$JVf0sRPMC36xZ6fCTvOEA.0SwcNbgwHwxPVPe0aZZSPPphD3YLrVS', 2147483647),
(7, 'adit', 'revovndausy@gmail.com', '$2y$10$IAUN/.rb7UM8K9WMkUnwZ.xZAUOoy8QusPINe/MWLZcWPXW1hvfxm', 2147483647),
(8, 'bisdig', 'revo123@gmail.com', '$2y$10$5xK/90Zbj3slp.lc0XRGB.PN7bfNwv0HKdprZmvCTCgn5eJgf/4Xi', 2147483647),
(9, 'RevvMc123', 'revoricardo0@gmail.com', '$2y$10$nFEjS2pZHncLv6cutKgPS.oMpb/Y36oge.hs41OQKNitSyWDY9epe', 2147483647);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_midtrans`
--

CREATE TABLE `transaksi_midtrans` (
  `id_transaksi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `order_id` varchar(100) NOT NULL,
  `gross_amount` decimal(12,2) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `transaction_status` varchar(50) NOT NULL,
  `transaction_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `settlement_time` datetime DEFAULT NULL,
  `fraud_status` varchar(50) DEFAULT NULL,
  `signature_key` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `checkout`
--
ALTER TABLE `checkout`
  ADD PRIMARY KEY (`id_checkout`);

--
-- Indeks untuk tabel `tabel_kategori`
--
ALTER TABLE `tabel_kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `tabel_keranjang`
--
ALTER TABLE `tabel_keranjang`
  ADD PRIMARY KEY (`id_keranjang`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_product` (`id_product`);

--
-- Indeks untuk tabel `tabel_penjual`
--
ALTER TABLE `tabel_penjual`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tabel_product`
--
ALTER TABLE `tabel_product`
  ADD PRIMARY KEY (`id_product`);

--
-- Indeks untuk tabel `tabel_user`
--
ALTER TABLE `tabel_user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indeks untuk tabel `transaksi_midtrans`
--
ALTER TABLE `transaksi_midtrans`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_user` (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `checkout`
--
ALTER TABLE `checkout`
  MODIFY `id_checkout` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `tabel_kategori`
--
ALTER TABLE `tabel_kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `tabel_keranjang`
--
ALTER TABLE `tabel_keranjang`
  MODIFY `id_keranjang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `tabel_penjual`
--
ALTER TABLE `tabel_penjual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tabel_product`
--
ALTER TABLE `tabel_product`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `tabel_user`
--
ALTER TABLE `tabel_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `transaksi_midtrans`
--
ALTER TABLE `transaksi_midtrans`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `transaksi_midtrans`
--
ALTER TABLE `transaksi_midtrans`
  ADD CONSTRAINT `transaksi_midtrans_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tabel_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
