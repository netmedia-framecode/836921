CREATE TABLE ketentuan_rat (
 id_rat INT AUTO_INCREMENT PRIMARY KEY,
 tahun SMALLINT NOT NULL,
 bunga_persen DECIMAL(5,2) NOT NULL,
 tenor_bulan INT NOT NULL,
 simpanan_pokok DECIMAL(15,2) NULL,
 dibuat_oleh INT NULL,
 dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (dibuat_oleh) REFERENCES users(id_user)
);
INSERT INTO ketentuan_rat(tahun,bunga_persen,tenor_bulan,simpanan_pokok) VALUES(2025,6,12,NULL);
ALTER TABLE simpanan
 ADD COLUMN id_rat INT NULL,
 ADD COLUMN id_pemeriksa INT NULL,
 ADD COLUMN waktu_pemeriksaan DATETIME NULL,
 ADD COLUMN catatan_pemeriksaan TEXT NULL,
 ADD FOREIGN KEY (id_rat) REFERENCES ketentuan_rat(id_rat),
 ADD FOREIGN KEY (id_pemeriksa) REFERENCES users(id_user);
ALTER TABLE pinjaman
 ADD COLUMN id_rat INT NULL,
 ADD COLUMN basis_bunga VARCHAR(15) NOT NULL DEFAULT 'bulanan',
 ADD COLUMN id_pemeriksa INT NULL,
 ADD COLUMN waktu_pemeriksaan DATETIME NULL,
 ADD COLUMN catatan_pemeriksaan TEXT NULL,
 ADD COLUMN catatan_review TEXT NULL,
 ADD COLUMN waktu_review DATETIME NULL,
 ADD FOREIGN KEY (id_rat) REFERENCES ketentuan_rat(id_rat),
 ADD FOREIGN KEY (id_pemeriksa) REFERENCES users(id_user);
-- Pinjaman lama tetap memakai basis bulanan yang berlaku pada kode sebelumnya.
