-- Jalankan satu kali. Catatan lama tetap diakui sebagai simpanan disetujui.
-- Review lama tidak diberi nama/tanggal buatan.
ALTER TABLE simpanan
  ADD COLUMN status_simpanan VARCHAR(20) NOT NULL DEFAULT 'Disetujui',
  ADD COLUMN id_reviewer INT NULL,
  ADD COLUMN waktu_review DATETIME NULL,
  ADD COLUMN catatan_review TEXT NULL,
  ADD CONSTRAINT fk_simpanan_reviewer FOREIGN KEY (id_reviewer) REFERENCES users(id_user);
ALTER TABLE simpanan ALTER COLUMN status_simpanan SET DEFAULT 'Pengajuan';
