-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Jun 2025 pada 14.36
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
-- Database: `ateka`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `atk`
--

CREATE TABLE `atk` (
  `id_atk` int(11) NOT NULL,
  `nama_atk` varchar(100) NOT NULL,
  `id_jenis_atk` int(11) NOT NULL,
  `satuan` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `atk`
--

INSERT INTO `atk` (`id_atk`, `nama_atk`, `id_jenis_atk`, `satuan`, `stok`) VALUES
(1, 'Pulpen Pilot Biru Snowman', 1, 'pcs', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pengadaan`
--

CREATE TABLE `detail_pengadaan` (
  `id_detail_pengadaan` int(11) NOT NULL,
  `id_pengadaan` int(11) NOT NULL,
  `id_jenis_atk` int(11) NOT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `status` enum('menunggu','ditawarkan','dipilih','ditolak semua','selesai') NOT NULL DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_pengadaan`
--

INSERT INTO `detail_pengadaan` (`id_detail_pengadaan`, `id_pengadaan`, `id_jenis_atk`, `id_supplier`, `jumlah`, `status`) VALUES
(1, 1, 1, 1, 10, 'menunggu'),
(4, 4, 14, NULL, 500, 'dipilih');

--
-- Trigger `detail_pengadaan`
--
DELIMITER $$
CREATE TRIGGER `after_detail_pengadaan_dipilih` AFTER UPDATE ON `detail_pengadaan` FOR EACH ROW BEGIN
  DECLARE total INT;
  DECLARE jumlah_dipilih INT;

  IF NEW.status = 'dipilih' THEN
    SELECT COUNT(*) INTO total
    FROM detail_pengadaan
    WHERE id_pengadaan = NEW.id_pengadaan;

    SELECT COUNT(*) INTO jumlah_dipilih
    FROM detail_pengadaan
    WHERE id_pengadaan = NEW.id_pengadaan AND status = 'dipilih';

    IF total = jumlah_dipilih THEN
      UPDATE pengadaan
      SET status = 'dipesan'
      WHERE id_pengadaan = NEW.id_pengadaan;
    END IF;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_detail_pengadaan_selesai` AFTER UPDATE ON `detail_pengadaan` FOR EACH ROW BEGIN
  DECLARE total INT;
  DECLARE jumlah_selesai INT;

  IF NEW.status = 'selesai' THEN
    SELECT COUNT(*) INTO total
    FROM detail_pengadaan
    WHERE id_pengadaan = NEW.id_pengadaan;

    SELECT COUNT(*) INTO jumlah_selesai
    FROM detail_pengadaan
    WHERE id_pengadaan = NEW.id_pengadaan AND status = 'selesai';

    IF total = jumlah_selesai THEN
      UPDATE pengadaan
      SET status = 'selesai'
      WHERE id_pengadaan = NEW.id_pengadaan;
    END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pengambilan`
--

CREATE TABLE `detail_pengambilan` (
  `id_detail_pengambilan` int(11) NOT NULL,
  `id_pengambilan` int(11) NOT NULL,
  `id_atk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_pengambilan`
--

INSERT INTO `detail_pengambilan` (`id_detail_pengambilan`, `id_pengambilan`, `id_atk`, `jumlah`) VALUES
(1, 1, 1, 10);

--
-- Trigger `detail_pengambilan`
--
DELIMITER $$
CREATE TRIGGER `pengambilan_jika_stok_tidak_cukup` BEFORE INSERT ON `detail_pengambilan` FOR EACH ROW BEGIN
    DECLARE current_stock INT;

    
    SELECT stok INTO current_stock 
    FROM atk 
    WHERE id_atk = NEW.id_atk;

    
    IF NEW.jumlah > current_stock THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stok ATK tidak mencukupi';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_request_pegawai`
--

CREATE TABLE `detail_request_pegawai` (
  `id_detail_request_pegawai` int(11) NOT NULL,
  `id_request` int(11) NOT NULL,
  `id_jenis_atk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_request_pegawai`
--

INSERT INTO `detail_request_pegawai` (`id_detail_request_pegawai`, `id_request`, `id_jenis_atk`, `jumlah`) VALUES
(1, 1, 1, 10),
(2, 2, 14, 500);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_atk`
--

CREATE TABLE `jenis_atk` (
  `id_jenis_atk` int(11) NOT NULL,
  `jenis_atk` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jenis_atk`
--

INSERT INTO `jenis_atk` (`id_jenis_atk`, `jenis_atk`) VALUES
(1, 'Pulpen Biru'),
(2, 'Pulpen Hitam'),
(3, 'Pulpen Merah'),
(4, 'Pensil 2B'),
(5, 'Pensil HB'),
(6, 'Penghapus'),
(7, 'Rautan'),
(8, 'Spidol Permanent Hitam'),
(9, 'Spidol Whiteboard Hitam'),
(10, 'Spidol Warna (Isi 12)'),
(11, 'Stabilo Kuning'),
(12, 'Stabilo Hijau'),
(13, 'Kertas A4 70gsm'),
(14, 'Kertas A4 80gsm'),
(15, 'Kertas F4 70gsm'),
(16, 'Kertas F4 80gsm'),
(17, 'Kertas HVS Warna (Isi 5)'),
(18, 'Map Plastik Transparan'),
(19, 'Map Kancing A4'),
(20, 'Ordner A4'),
(21, 'Stopmap Kertas'),
(22, 'Binder Clip Kecil'),
(23, 'Binder Clip Besar'),
(24, 'Paper Clip'),
(25, 'Penggaris 30cm'),
(26, 'Stempel Tinta'),
(27, 'Sticky Note Kuning'),
(28, 'Tipe-X'),
(29, 'Lem Kertas'),
(30, 'Gunting Kertas'),
(31, 'Double Tape'),
(32, 'Lakban Bening');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pegawai`
--

CREATE TABLE `pegawai` (
  `id_pegawai` int(11) NOT NULL,
  `nama_pegawai` varchar(100) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pegawai`
--

INSERT INTO `pegawai` (`id_pegawai`, `nama_pegawai`, `id_user`) VALUES
(1, 'Lukman', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengadaan`
--

CREATE TABLE `pengadaan` (
  `id_pengadaan` int(11) NOT NULL,
  `tanggal_pengadaan` date NOT NULL,
  `status` enum('diproses','selesai','dipesan') NOT NULL DEFAULT 'diproses'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengadaan`
--

INSERT INTO `pengadaan` (`id_pengadaan`, `tanggal_pengadaan`, `status`) VALUES
(1, '2025-05-27', 'selesai'),
(4, '2025-06-02', 'dipesan');

--
-- Trigger `pengadaan`
--
DELIMITER $$
CREATE TRIGGER `after_update_pengadaan_status` AFTER UPDATE ON `pengadaan` FOR EACH ROW BEGIN
    
    DECLARE done INT DEFAULT FALSE;
    DECLARE atk_id INT;
    DECLARE qty INT;
    DECLARE cur CURSOR FOR 
        SELECT id_atk, jumlah 
        FROM detail_pengadaan 
        WHERE id_pengadaan = NEW.id_pengadaan;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    
    IF NEW.status = 'selesai' AND OLD.status <> 'selesai' THEN
        OPEN cur;
        read_loop: LOOP
            FETCH cur INTO atk_id, qty;
            IF done THEN
                LEAVE read_loop;
            END IF;

            UPDATE atk SET stok = stok + qty WHERE id_atk = atk_id;
        END LOOP;
        CLOSE cur;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengambilan`
--

CREATE TABLE `pengambilan` (
  `id_pengambilan` int(11) NOT NULL,
  `id_pegawai` int(11) NOT NULL,
  `tanggal_pengambilan` date NOT NULL,
  `status` enum('diminta','disetujui','ditolak','diambil') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengambilan`
--

INSERT INTO `pengambilan` (`id_pengambilan`, `id_pegawai`, `tanggal_pengambilan`, `status`) VALUES
(1, 1, '2025-05-15', 'disetujui');

--
-- Trigger `pengambilan`
--
DELIMITER $$
CREATE TRIGGER `after_pengambilan_disetujui` AFTER UPDATE ON `pengambilan` FOR EACH ROW BEGIN
    
    DECLARE done INT DEFAULT 0;
    DECLARE v_id_atk INT;
    DECLARE v_jumlah INT;

    
    DECLARE cur CURSOR FOR
        SELECT id_atk, jumlah
        FROM detail_pengambilan
        WHERE id_pengambilan = NEW.id_pengambilan;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    
    IF NEW.status = 'diambil' THEN
        OPEN cur;

        read_loop: LOOP
            FETCH cur INTO v_id_atk, v_jumlah;
            IF done THEN
                LEAVE read_loop;
            END IF;

            UPDATE atk
            SET stok = stok - v_jumlah
            WHERE id_atk = v_id_atk;
        END LOOP;

        CLOSE cur;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `request_pegawai`
--

CREATE TABLE `request_pegawai` (
  `id_request` int(11) NOT NULL,
  `id_pegawai` int(11) NOT NULL,
  `tanggal_request` date NOT NULL,
  `keterangan` text NOT NULL,
  `status` enum('menunggu','disetujui','ditolak','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `request_pegawai`
--

INSERT INTO `request_pegawai` (`id_request`, `id_pegawai`, `tanggal_request`, `keterangan`, `status`) VALUES
(1, 1, '2025-05-08', 'Keperluan Seminar', 'menunggu'),
(2, 1, '2025-06-25', 'Seminar', 'disetujui');

--
-- Trigger `request_pegawai`
--
DELIMITER $$
CREATE TRIGGER `request_disetujui` AFTER UPDATE ON `request_pegawai` FOR EACH ROW BEGIN
  DECLARE idPengadaanBaru INT;

  
  IF NEW.status = 'disetujui' AND OLD.status <> 'disetujui' THEN

      INSERT INTO pengadaan (tanggal_pengadaan, status)
    VALUES (NOW(), 'diajukan');

    
    SET idPengadaanBaru = LAST_INSERT_ID();

    INSERT INTO detail_pengadaan (id_pengadaan, id_jenis_atk, jumlah, status)
    SELECT idPengadaanBaru, id_jenis_atk, jumlah, 'menunggu'
    FROM detail_request_pegawai
    WHERE id_request = NEW.id_request;

  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplier`
--

CREATE TABLE `supplier` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `no_telepon` varchar(100) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `no_telepon`, `alamat`, `id_user`) VALUES
(1, 'Asep Spidol', '08565645454343', 'Jl. Jend. Sudirman', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplier_katalog`
--

CREATE TABLE `supplier_katalog` (
  `id_katalog` int(11) NOT NULL,
  `nama_katalog_atk` varchar(100) NOT NULL,
  `id_jenis_atk` int(11) NOT NULL,
  `harga` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `supplier_katalog`
--

INSERT INTO `supplier_katalog` (`id_katalog`, `nama_katalog_atk`, `id_jenis_atk`, `harga`, `id_supplier`) VALUES
(1, 'Spidol Snowman Bisa Hapus', 9, 5000, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tawaran_supplier`
--

CREATE TABLE `tawaran_supplier` (
  `id_tawaran` int(11) NOT NULL,
  `id_detail_pengadaan` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `harga_tawaran` decimal(10,0) NOT NULL,
  `status_tawaran` enum('menunggu','disetujui','ditolak','') NOT NULL DEFAULT 'menunggu',
  `tanggal_tawaran` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Trigger `tawaran_supplier`
--
DELIMITER $$
CREATE TRIGGER `after_tawaran_diterima` AFTER UPDATE ON `tawaran_supplier` FOR EACH ROW BEGIN
    
    IF NEW.status_tawaran = 'disetujui' THEN
     
        UPDATE tawaran_supplier
        SET status_tawaran = 'ditolak'
        WHERE id_detail_pengadaan = NEW.id_detail_pengadaan
          AND id_tawaran != NEW.id_tawaran;
          
             UPDATE detail_pengadaan
        SET id_supplier = NEW.id_supplier
        WHERE id_detail_pengadaan = NEW.id_detail_pengadaan;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `cek_semua_tawaran_ditolak` AFTER UPDATE ON `tawaran_supplier` FOR EACH ROW BEGIN
  DECLARE jumlah_total INT DEFAULT 0;
  DECLARE jumlah_ditolak INT DEFAULT 0;

  SELECT COUNT(*) 
  INTO jumlah_total
  FROM tawaran_supplier
  WHERE id_detail_pengadaan = NEW.id_detail_pengadaan;

  SELECT COUNT(*) 
  INTO jumlah_ditolak
  FROM tawaran_supplier
  WHERE id_detail_pengadaan = NEW.id_detail_pengadaan 
    AND status_tawaran = 'ditolak';

  IF jumlah_total > 0 AND jumlah_total = jumlah_ditolak THEN
    UPDATE detail_pengadaan
    SET status = 'ditolak semua'
    WHERE id_detail_pengadaan = NEW.id_detail_pengadaan;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `pilih_tawaran_supplier` AFTER UPDATE ON `tawaran_supplier` FOR EACH ROW BEGIN
  IF NEW.status_tawaran = 'dipilih' THEN
    UPDATE detail_pengadaan
    SET id_supplier = NEW.id_supplier,
        status = 'dipilih'
    WHERE id_detail_pengadaan = NEW.id_detail_pengadaan;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `ubah_status_detail_pengadaan_setelah_tawaran` AFTER INSERT ON `tawaran_supplier` FOR EACH ROW BEGIN
  UPDATE detail_pengadaan
  SET status = 'ditawarkan'
  WHERE id_detail_pengadaan = NEW.id_detail_pengadaan
    AND status = 'menunggu';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','pegawai','supplier','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`) VALUES
(1, 'Lukman', 'lukmankuy', 'pegawai'),
(3, 'Asep Spidol', 'Spidol Merah', 'supplier');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `atk`
--
ALTER TABLE `atk`
  ADD PRIMARY KEY (`id_atk`),
  ADD KEY `id_jenis_atk` (`id_jenis_atk`);

--
-- Indeks untuk tabel `detail_pengadaan`
--
ALTER TABLE `detail_pengadaan`
  ADD PRIMARY KEY (`id_detail_pengadaan`),
  ADD KEY `detail_pengadaan_ibfk_1` (`id_pengadaan`),
  ADD KEY `detail_pengadaan_ibfk_2` (`id_jenis_atk`),
  ADD KEY `id_supplier` (`id_supplier`);

--
-- Indeks untuk tabel `detail_pengambilan`
--
ALTER TABLE `detail_pengambilan`
  ADD PRIMARY KEY (`id_detail_pengambilan`),
  ADD KEY `detail_pengambilan_ibfk_1` (`id_pengambilan`),
  ADD KEY `detail_pengambilan_ibfk_2` (`id_atk`);

--
-- Indeks untuk tabel `detail_request_pegawai`
--
ALTER TABLE `detail_request_pegawai`
  ADD PRIMARY KEY (`id_detail_request_pegawai`),
  ADD KEY `id_request` (`id_request`),
  ADD KEY `detail_request_pegawai_ibfk_2` (`id_jenis_atk`);

--
-- Indeks untuk tabel `jenis_atk`
--
ALTER TABLE `jenis_atk`
  ADD PRIMARY KEY (`id_jenis_atk`);

--
-- Indeks untuk tabel `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id_pegawai`),
  ADD KEY `pegawai_ibfk_1` (`id_user`);

--
-- Indeks untuk tabel `pengadaan`
--
ALTER TABLE `pengadaan`
  ADD PRIMARY KEY (`id_pengadaan`);

--
-- Indeks untuk tabel `pengambilan`
--
ALTER TABLE `pengambilan`
  ADD PRIMARY KEY (`id_pengambilan`),
  ADD KEY `pengambilan_ibfk_1` (`id_pegawai`);

--
-- Indeks untuk tabel `request_pegawai`
--
ALTER TABLE `request_pegawai`
  ADD PRIMARY KEY (`id_request`),
  ADD KEY `id_pegawai` (`id_pegawai`);

--
-- Indeks untuk tabel `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `supplier_katalog`
--
ALTER TABLE `supplier_katalog`
  ADD PRIMARY KEY (`id_katalog`),
  ADD KEY `jenis_atk` (`id_jenis_atk`),
  ADD KEY `id_supplier` (`id_supplier`);

--
-- Indeks untuk tabel `tawaran_supplier`
--
ALTER TABLE `tawaran_supplier`
  ADD PRIMARY KEY (`id_tawaran`),
  ADD KEY `id_detail_pengadaan` (`id_detail_pengadaan`),
  ADD KEY `id_supplier` (`id_supplier`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `atk`
--
ALTER TABLE `atk`
  MODIFY `id_atk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `detail_pengadaan`
--
ALTER TABLE `detail_pengadaan`
  MODIFY `id_detail_pengadaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `detail_pengambilan`
--
ALTER TABLE `detail_pengambilan`
  MODIFY `id_detail_pengambilan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `detail_request_pegawai`
--
ALTER TABLE `detail_request_pegawai`
  MODIFY `id_detail_request_pegawai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `jenis_atk`
--
ALTER TABLE `jenis_atk`
  MODIFY `id_jenis_atk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id_pegawai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pengadaan`
--
ALTER TABLE `pengadaan`
  MODIFY `id_pengadaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pengambilan`
--
ALTER TABLE `pengambilan`
  MODIFY `id_pengambilan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `request_pegawai`
--
ALTER TABLE `request_pegawai`
  MODIFY `id_request` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `supplier_katalog`
--
ALTER TABLE `supplier_katalog`
  MODIFY `id_katalog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tawaran_supplier`
--
ALTER TABLE `tawaran_supplier`
  MODIFY `id_tawaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `atk`
--
ALTER TABLE `atk`
  ADD CONSTRAINT `atk_ibfk_1` FOREIGN KEY (`id_jenis_atk`) REFERENCES `jenis_atk` (`id_jenis_atk`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_pengadaan`
--
ALTER TABLE `detail_pengadaan`
  ADD CONSTRAINT `detail_pengadaan_ibfk_1` FOREIGN KEY (`id_pengadaan`) REFERENCES `pengadaan` (`id_pengadaan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pengadaan_ibfk_2` FOREIGN KEY (`id_jenis_atk`) REFERENCES `jenis_atk` (`id_jenis_atk`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pengadaan_ibfk_3` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_pengambilan`
--
ALTER TABLE `detail_pengambilan`
  ADD CONSTRAINT `detail_pengambilan_ibfk_1` FOREIGN KEY (`id_pengambilan`) REFERENCES `pengambilan` (`id_pengambilan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pengambilan_ibfk_2` FOREIGN KEY (`id_atk`) REFERENCES `atk` (`id_atk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_request_pegawai`
--
ALTER TABLE `detail_request_pegawai`
  ADD CONSTRAINT `detail_request_pegawai_ibfk_1` FOREIGN KEY (`id_request`) REFERENCES `request_pegawai` (`id_request`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_request_pegawai_ibfk_2` FOREIGN KEY (`id_jenis_atk`) REFERENCES `jenis_atk` (`id_jenis_atk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pegawai`
--
ALTER TABLE `pegawai`
  ADD CONSTRAINT `pegawai_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengambilan`
--
ALTER TABLE `pengambilan`
  ADD CONSTRAINT `pengambilan_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `request_pegawai`
--
ALTER TABLE `request_pegawai`
  ADD CONSTRAINT `request_pegawai_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `supplier`
--
ALTER TABLE `supplier`
  ADD CONSTRAINT `supplier_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `supplier_katalog`
--
ALTER TABLE `supplier_katalog`
  ADD CONSTRAINT `supplier_katalog_ibfk_1` FOREIGN KEY (`id_jenis_atk`) REFERENCES `jenis_atk` (`id_jenis_atk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_katalog_ibfk_2` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tawaran_supplier`
--
ALTER TABLE `tawaran_supplier`
  ADD CONSTRAINT `tawaran_supplier_ibfk_1` FOREIGN KEY (`id_detail_pengadaan`) REFERENCES `detail_pengadaan` (`id_detail_pengadaan`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `tawaran_supplier_ibfk_2` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
