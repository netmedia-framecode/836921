-- Sinkronkan anggota yang sudah mempunyai pengajuan simpanan atau pinjaman.
UPDATE anggota a SET a.jenis_anggota='Tetap'
WHERE EXISTS (SELECT 1 FROM simpanan s WHERE s.id_anggota=a.id_anggota)
   OR EXISTS (SELECT 1 FROM pinjaman p WHERE p.id_anggota=a.id_anggota);
-- Nonaktifkan menu berita; catatan lama tetap tersimpan.
UPDATE user_sub_menu SET id_active=2
WHERE url IN ('pusat-informasi/berita', 'pusat-informasi/berita.php');
