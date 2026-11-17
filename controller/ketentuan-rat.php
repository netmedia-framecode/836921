<?php
function kepalaKoperasi($role) {
  return in_array(strtolower(trim($role)), ['ketua koperasi', 'kepala koperasi'], true);
}
function pemeriksaKoperasi($role) {
  return in_array(strtolower(trim($role)), ['administrator', 'admin', 'bendahara'], true);
}
function ratAktif($conn) {
  return mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM ketentuan_rat ORDER BY id_rat DESC LIMIT 1'));
}
function totalTagihanPinjaman($pokok, $bunga, $tenor, $basis = 'bulanan') {
  return round((float)$pokok * (1 + (float)$bunga / 100 * ($basis === 'total' ? 1 : (int)$tenor)), 2);
}
function sqlTotalTagihanPinjaman() {
  return "(pinjaman.jumlah_pinjaman * (1 + pinjaman.bunga_persen / 100 * CASE WHEN pinjaman.basis_bunga='total' THEN 1 ELSE pinjaman.tenor_bulan END))";
}
function prosesReviewPengajuan($conn, $jenis, $id, $keputusan, $catatan, $petugas, $role) {
  if (!in_array($jenis, ['simpanan','pinjaman'], true) || $id <= 0 || $petugas <= 0 || strlen($catatan) > 1000) return false;
  $verifikasi = $keputusan === 'Diverifikasi';
  if ($verifikasi ? !pemeriksaKoperasi($role) : (!kepalaKoperasi($role) || !in_array($keputusan, ['Disetujui','Ditolak'], true))) return false;
  if ($keputusan === 'Ditolak' && trim($catatan) === '') return false;
  $status = $jenis === 'simpanan' ? 'status_simpanan' : 'status_pinjam';
  $sebelum = $verifikasi ? 'Pengajuan' : 'Diverifikasi';
  if ($verifikasi) {
    $set = 'id_pemeriksa=?, catatan_pemeriksaan=?, waktu_pemeriksaan=NOW()';
  } else {
    $set = ($jenis === 'simpanan' ? 'id_reviewer' : 'id_validator') . '=?, catatan_review=?, waktu_review=NOW()';
    if ($keputusan === 'Disetujui') {
      // Penerima simpanan adalah petugas pemeriksa, ACC oleh kepala dicatat terpisah.
      $set .= $jenis === 'simpanan' ? ', id_penerima=id_pemeriksa' : ', tanggal_disetujui=CURDATE()';
    }
  }
  $stmt = mysqli_prepare($conn, "UPDATE $jenis SET $status=?, $set WHERE id_$jenis=? AND $status=?");
  mysqli_stmt_bind_param($stmt, 'sisis', $keputusan, $petugas, $catatan, $id, $sebelum);
  mysqli_stmt_execute($stmt);
  $ok = mysqli_stmt_affected_rows($stmt) === 1;
  mysqli_stmt_close($stmt);
  return $ok;
}
