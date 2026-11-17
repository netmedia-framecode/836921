<?php
require_once(__DIR__ . '/../config/Base.php');
if (!isset($_SESSION['project_ksu_st_karolus']['users'])) { header('Location: ../auth/'); exit(); }
require_once(__DIR__ . '/../config/Auth.php');
require_once(__DIR__ . '/../config/Alert.php');
if (!kepalaKoperasi($role) && !in_array(strtolower($role), ['administrator','admin'], true)) {
  http_response_code(403); exit('Hanya admin dan kepala koperasi yang dapat mengelola ketentuan RAT.');
}
if (empty($_SESSION['csrf_rat'])) $_SESSION['csrf_rat'] = bin2hex(random_bytes(32));
$rat_error = '';
if (isset($_POST['simpan_rat'])) {
  $token = $_POST['csrf_rat'] ?? '';
  if (!is_string($token) || !hash_equals($_SESSION['csrf_rat'], $token)) { http_response_code(403); exit('Permintaan tidak valid.'); }
  $tahun = filter_var($_POST['tahun'] ?? '', FILTER_VALIDATE_INT);
  $tenor = filter_var($_POST['tenor_bulan'] ?? '', FILTER_VALIDATE_INT);
  $bunga = filter_var($_POST['bunga_persen'] ?? '', FILTER_VALIDATE_FLOAT);
  $pokok = ($_POST['simpanan_pokok'] ?? '') === '' ? null : filter_var($_POST['simpanan_pokok'], FILTER_VALIDATE_FLOAT);
  if ($tahun === false || $tahun < 2000 || $tahun > 2100 || $tenor === false || $tenor < 1 || $tenor > 120 ||
      $bunga === false || $bunga < 0 || $bunga > 100 || ($pokok !== null && ($pokok === false || $pokok <= 0 || $pokok > 9999999999999.99))) {
    $rat_error = 'Periksa tahun, bunga total (0–100%), tenor (1–120 bulan), dan nominal simpanan pokok.';
  } else {
    $stmt = mysqli_prepare($conn, 'INSERT INTO ketentuan_rat(tahun,bunga_persen,tenor_bulan,simpanan_pokok,dibuat_oleh) VALUES(?,?,?,?,?)');
    mysqli_stmt_bind_param($stmt, 'ididi', $tahun, $bunga, $tenor, $pokok, $id_user);
    mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    alert('Ketentuan RAT tersimpan. Berlaku untuk pengajuan baru; pengajuan lama tetap menggunakan ketentuan semula.', 'success');
    header('Location: pengaturan-rat'); exit();
  }
}
$rat_aktif = ratAktif($conn);
$riwayat_rat = mysqli_query($conn, 'SELECT * FROM ketentuan_rat ORDER BY id_rat DESC');
