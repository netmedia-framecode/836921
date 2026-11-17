-- Active: 1734576880718@@127.0.0.1@3306@ksu_st_karolus

/* =======================================================
BAGIAN 1: TEMPLATE (JANGAN DIUBAH / DO NOT CHANGE)
Sesuai script asli pengguna (English)
======================================================= */
CREATE TABLE
  utilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    logo VARCHAR(50),
    name_web VARCHAR(75),
    keyword TEXT,
    description TEXT,
    author VARCHAR(50)
  );

CREATE TABLE
  auth (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(50),
    bg VARCHAR(35),
    model INT DEFAULT 2
  );

CREATE TABLE
  user_role (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    role VARCHAR(35)
  );

INSERT INTO
  user_role (role)
VALUES
  ('Administrator'),
  ('Ketua Koperasi'),
  ('Bendahara'),
  ('Anggota');

CREATE TABLE
  user_status (
    id_status INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(35)
  );

INSERT INTO
  user_status (status)
VALUES
  ('Active'),
  ('No Active');

CREATE TABLE
  users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    id_role INT,
    id_active INT,
    en_user VARCHAR(75),
    token CHAR(6),
    name VARCHAR(100),
    image VARCHAR(100),
    email VARCHAR(75),
    password VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_role) REFERENCES user_role (id_role) ON UPDATE NO ACTION ON DELETE NO ACTION,
    FOREIGN KEY (id_active) REFERENCES user_status (id_status) ON UPDATE NO ACTION ON DELETE NO ACTION
  );

CREATE TABLE
  user_menu (
    id_menu INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50),
    menu VARCHAR(50)
  );

CREATE TABLE
  user_sub_menu (
    id_sub_menu INT AUTO_INCREMENT PRIMARY KEY,
    id_menu INT,
    id_active INT,
    title VARCHAR(50),
    url VARCHAR(50),
    FOREIGN KEY (id_menu) REFERENCES user_menu (id_menu) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_active) REFERENCES user_status (id_status) ON UPDATE NO ACTION ON DELETE NO ACTION
  );

CREATE TABLE
  permissions (
    id_permission INT AUTO_INCREMENT PRIMARY KEY,
    id_role INT NOT NULL,
    id_menu INT NULL,
    id_sub_menu INT NULL,
    `view` TINYINT (1) DEFAULT 0,
    `create` TINYINT (1) DEFAULT 0,
    `edit` TINYINT (1) DEFAULT 0,
    `delete` TINYINT (1) DEFAULT 0,
    FOREIGN KEY (id_role) REFERENCES user_role (id_role) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_menu) REFERENCES user_menu (id_menu) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_sub_menu) REFERENCES user_sub_menu (id_sub_menu) ON UPDATE CASCADE ON DELETE CASCADE
  );


/* =======================================================
BAGIAN 2: HASIL EKSTRAKSI DIAGRAM RELASI TABEL KOPERASI
Diurutkan berdasarkan dependensi Foreign Key
======================================================= */

-- 1. Tabel Berita
CREATE TABLE berita (
    id_berita INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi_berita TEXT NOT NULL,
    tgl_buat DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_admin INT,
    FOREIGN KEY (id_admin) REFERENCES users (id_user) ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- 2. Tabel Pengumuman
CREATE TABLE pengumuman (
    id_pengumuman INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi_pengumuman TEXT NOT NULL,
    tgl_posting DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_admin INT,
    FOREIGN KEY (id_admin) REFERENCES users (id_user) ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- 3. Tabel Kebijakan Koperasi
CREATE TABLE kebijakan_koperasi (
    id_kebijakan INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi_kebijakan TEXT NOT NULL,
    tgl_ubah DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_admin INT,
    FOREIGN KEY (id_admin) REFERENCES users (id_user) ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- 4. Tabel Keuangan
CREATE TABLE keuangan (
    id_keuangan INT AUTO_INCREMENT PRIMARY KEY,
    id_bendahara INT,
    jenis_transaksi VARCHAR(50) NOT NULL,
    nominal DECIMAL(15, 2) NOT NULL,
    keterangan TEXT,
    tgl_transaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_bendahara) REFERENCES users (id_user) ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- 5. Tabel Anggota
CREATE TABLE anggota (
    id_anggota INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    nik VARCHAR(20) NULL UNIQUE,
    nama_lengkap VARCHAR(150) NOT NULL,
    jenis_anggota VARCHAR(50),
    no_telp VARCHAR(20),
    alamat TEXT,
    tgl_gabung DATE,
    FOREIGN KEY (id_user) REFERENCES users (id_user) ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- 6. Tabel Simpanan (Note: Koreksi nama PK dari id_pinjaman menjadi id_simpanan)
CREATE TABLE simpanan (
    id_simpanan INT AUTO_INCREMENT PRIMARY KEY, 
    id_anggota INT,
    id_penerima INT,
    jenis_simpanan VARCHAR(50) NOT NULL,
    status_simpanan VARCHAR(20) NOT NULL DEFAULT 'Pengajuan',
    id_reviewer INT NULL,
    waktu_review DATETIME NULL,
    catatan_review TEXT NULL,
    FOREIGN KEY (id_reviewer) REFERENCES users(id_user),
    jumlah DECIMAL(15, 2) NOT NULL,
    tgl_simpanan DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_anggota) REFERENCES anggota (id_anggota) ON UPDATE NO ACTION ON DELETE NO ACTION,
    FOREIGN KEY (id_penerima) REFERENCES users (id_user) ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- 7. Tabel Pinjaman
CREATE TABLE pinjaman (
    id_pinjaman INT AUTO_INCREMENT PRIMARY KEY,
    id_anggota INT,
    id_validator INT,
    jumlah_pinjaman DECIMAL(15, 2) NOT NULL,
    tenor_bulan INT NOT NULL,
    bunga_persen DECIMAL(5, 2) NOT NULL,
    status_pinjam VARCHAR(35) NOT NULL,
    tanggal_pengajuan DATE,
    tanggal_disetujui DATE,
    FOREIGN KEY (id_anggota) REFERENCES anggota (id_anggota) ON UPDATE NO ACTION ON DELETE NO ACTION,
    FOREIGN KEY (id_validator) REFERENCES users (id_user) ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- 8. Tabel Angsuran
CREATE TABLE angsuran (
    id_angsuran INT AUTO_INCREMENT PRIMARY KEY,
    id_pinjaman INT,
    id_penerima INT,
    angsuran_ke INT NOT NULL,
    jumlah_bayar DECIMAL(15, 2) NOT NULL,
    sisa_pinjaman DECIMAL(15, 2) NOT NULL,
    tgl_bayar DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pinjaman) REFERENCES pinjaman (id_pinjaman) ON UPDATE NO ACTION ON DELETE NO ACTION,
    FOREIGN KEY (id_penerima) REFERENCES users (id_user) ON UPDATE NO ACTION ON DELETE NO ACTION
);

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
