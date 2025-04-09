-- Table structure for table `tb_tema`
CREATE TABLE `tb_tema` (
  `kode_tema` varchar(16) NOT NULL,
  `nama_tema` varchar(255) NOT NULL,
  `deskripsi` text,
  PRIMARY KEY (`kode_tema`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Add tema_id to tb_kriteria
ALTER TABLE `tb_kriteria` 
ADD COLUMN `kode_tema` varchar(16) DEFAULT NULL,
ADD FOREIGN KEY (`kode_tema`) REFERENCES `tb_tema`(`kode_tema`) ON DELETE SET NULL;

-- Add tema_id to tb_alternatif 
ALTER TABLE `tb_alternatif`
ADD COLUMN `kode_tema` varchar(16) DEFAULT NULL,
ADD FOREIGN KEY (`kode_tema`) REFERENCES `tb_tema`(`kode_tema`) ON DELETE SET NULL;

-- Sample data for tb_tema
INSERT INTO `tb_tema` (`kode_tema`, `nama_tema`, `deskripsi`) VALUES
('T1', 'Mahasiswa Berprestasi', 'Sistem pendukung keputusan untuk pemilihan mahasiswa berprestasi'),
('T2', 'Beasiswa', 'Sistem pendukung keputusan untuk seleksi penerima beasiswa');