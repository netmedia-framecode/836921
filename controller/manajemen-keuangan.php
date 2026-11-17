<?php

require_once("../../config/Base.php");
require_once("../../config/Auth.php");
require_once("../../config/Alert.php");
require_once("../../views/manajemen-keuangan/redirect.php");
require_once(__DIR__ . "/pemilik-simpanan.php");

require_once(__DIR__ . '/persetujuan-simpanan.php');
$can_review_simpanan = bolehReviewSimpanan($role);
$can_verify_pengajuan = pemeriksaKoperasi($role);
$rat_aktif = ratAktif($conn);
if (empty($_SESSION['csrf_simpanan'])) {
  $_SESSION['csrf_simpanan'] = bin2hex(random_bytes(32));
}
$csrf_simpanan = $_SESSION['csrf_simpanan'];
foreach (['add_simpanan' => 'create', 'edit_simpanan' => 'edit', 'delete_simpanan' => 'delete', 'review_simpanan' => 'edit'] as $post_action => $permission_action) {
  if (isset($_POST[$post_action])) {
    $token = $_POST['csrf_simpanan'] ?? '';
    $authorized = $post_action === 'review_simpanan' ? ($can_review_simpanan || $can_verify_pengajuan) : canAction($permission_action, 'manajemen-keuangan/data-simpanan');
    if (!is_string($token) || !hash_equals($csrf_simpanan, $token) || !$authorized) {
      http_response_code(403);
      exit('Permintaan tidak diizinkan. Muat ulang halaman dan coba kembali.');
    }
    if (in_array($post_action, ['add_simpanan', 'edit_simpanan'], true) && !validasiDataSimpanan($_POST)) {
      alert('Jenis, jumlah, atau tanggal simpanan tidak valid. Jumlah harus lebih dari nol.', 'danger');
      header('Location: data-simpanan');
      exit();
    }
  }
}
if (isset($_POST['review_simpanan'])) {
  $updated = reviewSimpanan($conn, (int) ($_POST['id_simpanan'] ?? 0),
    (string) ($_POST['keputusan'] ?? ''), trim((string) ($_POST['catatan_review'] ?? '')),
    (int) $id_user, $role);
  alert($updated ? 'Keputusan pengajuan simpanan berhasil disimpan.' : 'Review gagal. Periksa alasan penolakan atau status pengajuan yang mungkin sudah berubah.', $updated ? 'success' : 'danger');
  header('Location: data-simpanan');
  exit();
}

foreach (['add_pinjaman'=>'create', 'edit_pinjaman'=>'edit', 'delete_pinjaman'=>'delete', 'review_pinjaman'=>'edit'] as $post_action=>$permission_action) {
  if (isset($_POST[$post_action])) {
    $token = $_POST['csrf_simpanan'] ?? '';
    $authorized = $post_action === 'review_pinjaman' ? ($can_review_simpanan || $can_verify_pengajuan) : canAction($permission_action, 'manajemen-keuangan/data-pinjaman');
    if (!is_string($token) || !hash_equals($csrf_simpanan, $token) || !$authorized) {
      http_response_code(403); exit('Permintaan tidak diizinkan.');
    }
    if (in_array($post_action, ['add_pinjaman','edit_pinjaman'], true) &&
        (!is_numeric($_POST['jumlah_pinjaman'] ?? null) || (float)$_POST['jumlah_pinjaman'] <= 0 || (float)$_POST['jumlah_pinjaman'] > 9999999999999.99)) {
      alert('Jumlah pinjaman harus lebih dari nol dan dalam batas nominal.', 'danger');
      header('Location: data-pinjaman'); exit();
    }
  }
}
if (isset($_POST['review_pinjaman'])) {
  $ok = prosesReviewPengajuan($conn, 'pinjaman', (int)($_POST['id_pinjaman'] ?? 0), (string)($_POST['keputusan'] ?? ''), trim((string)($_POST['catatan_review'] ?? '')), (int)$id_user, $role);
  alert($ok ? 'Status pengajuan pinjaman diperbarui.' : 'Gagal memproses pengajuan. Periksa status dan catatan penolakan.', $ok ? 'success' : 'danger');
  header('Location: data-pinjaman'); exit();
}

$is_anggota_login = in_array(strtolower($role), ['anggota', 'member']);
$filter_anggota_login = $is_anggota_login ? " AND anggota.id_user = '$id_user'" : "";
$deny_akses_keuangan = function ($redirect) {
  alert("Anda tidak memiliki akses ke data tersebut.", "danger");
  header("Location: $redirect");
  exit();
};
$can_access_keuangan = function ($type, $value) use ($conn, $is_anggota_login, $id_user) {
  if (!$is_anggota_login) {
    return true;
  }

  $value = valid($conn, $value);
  if ($type == 'id_anggota') {
    $query = "SELECT id_anggota FROM anggota WHERE id_anggota='$value' AND id_user='$id_user'";
  } else if ($type == 'id_simpanan') {
    $query = "SELECT simpanan.id_simpanan FROM simpanan JOIN anggota ON simpanan.id_anggota=anggota.id_anggota WHERE simpanan.id_simpanan='$value' AND anggota.id_user='$id_user'";
  } else if ($type == 'id_pinjaman') {
    $query = "SELECT pinjaman.id_pinjaman FROM pinjaman JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota WHERE pinjaman.id_pinjaman='$value' AND anggota.id_user='$id_user'";
  } else if ($type == 'id_angsuran') {
    $query = "SELECT angsuran.id_angsuran FROM angsuran JOIN pinjaman ON angsuran.id_pinjaman=pinjaman.id_pinjaman JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota WHERE angsuran.id_angsuran='$value' AND anggota.id_user='$id_user'";
  } else {
    return false;
  }

  $result = mysqli_query($conn, $query);
  return $result && mysqli_num_rows($result) > 0;
};

$select_anggota_simpanan = "SELECT id_anggota, nik, nama_lengkap FROM anggota WHERE 1=1 $filter_anggota_login ORDER BY nama_lengkap ASC";
$views_anggota_simpanan = mysqli_query($conn, $select_anggota_simpanan);
$views_pemilik_simpanan = [];
if (!$is_anggota_login && in_array(basename($_SERVER['SCRIPT_NAME'], '.php'), ['add-data-simpanan', 'add-data-pinjaman'], true)) {
  $views_pemilik_simpanan = mysqli_query($conn, "SELECT CAST(anggota.id_anggota AS CHAR) AS pilihan,
      CONCAT_WS(' - ', NULLIF(anggota.nik, ''), anggota.nama_lengkap) AS label
    FROM anggota
    UNION ALL
    SELECT CONCAT('user:', users.id_user) AS pilihan,
      CONCAT_WS(' - ', users.name, users.email) AS label
    FROM users
    JOIN user_role ON users.id_role=user_role.id_role
    WHERE LOWER(user_role.role) IN ('anggota', 'member')
      AND NOT EXISTS (SELECT 1 FROM anggota WHERE anggota.id_user=users.id_user)
    ORDER BY label ASC");
}

$select_simpanan = "SELECT 
  simpanan.*,
  anggota.nik,
  anggota.nama_lengkap,
  users.name AS nama_penerima, reviewer.name AS nama_reviewer, pemeriksa.name AS nama_pemeriksa
FROM simpanan
LEFT JOIN anggota ON simpanan.id_anggota=anggota.id_anggota
LEFT JOIN users ON simpanan.id_penerima=users.id_user
LEFT JOIN users reviewer ON simpanan.id_reviewer=reviewer.id_user
LEFT JOIN users pemeriksa ON simpanan.id_pemeriksa=pemeriksa.id_user
WHERE 1=1 $filter_anggota_login
ORDER BY simpanan.id_simpanan DESC";
$views_simpanan = mysqli_query($conn, $select_simpanan);

$select_pinjaman = "SELECT 
  pinjaman.*,
  anggota.nik,
  anggota.nama_lengkap,
  users.name AS nama_validator, pemeriksa.name AS nama_pemeriksa
FROM pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
LEFT JOIN users ON pinjaman.id_validator=users.id_user
LEFT JOIN users pemeriksa ON pinjaman.id_pemeriksa=pemeriksa.id_user
WHERE 1=1 $filter_anggota_login
ORDER BY pinjaman.id_pinjaman DESC";
$views_pinjaman = mysqli_query($conn, $select_pinjaman);

$select_pinjaman_angsuran = "SELECT
  pinjaman.id_pinjaman,
  pinjaman.jumlah_pinjaman,
  pinjaman.tenor_bulan,
  pinjaman.bunga_persen,
    pinjaman.basis_bunga,
  pinjaman.status_pinjam,
  pinjaman.tanggal_pengajuan,
  anggota.nik,
  anggota.nama_lengkap,
  COALESCE(
    (SELECT a.sisa_pinjaman FROM angsuran a WHERE a.id_pinjaman=pinjaman.id_pinjaman ORDER BY a.id_angsuran DESC LIMIT 1),
    (pinjaman.jumlah_pinjaman * (1 + pinjaman.bunga_persen / 100 * CASE WHEN pinjaman.basis_bunga='total' THEN 1 ELSE pinjaman.tenor_bulan END))
  ) AS sisa_pinjaman_terakhir,
  COALESCE(
    (SELECT MAX(a.angsuran_ke) FROM angsuran a WHERE a.id_pinjaman=pinjaman.id_pinjaman),
    0
  ) AS angsuran_terakhir
FROM pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
WHERE LOWER(pinjaman.status_pinjam) = 'disetujui' $filter_anggota_login
HAVING sisa_pinjaman_terakhir > 0
ORDER BY pinjaman.tanggal_pengajuan ASC, pinjaman.id_pinjaman ASC";
$views_pinjaman_angsuran = mysqli_query($conn, $select_pinjaman_angsuran);

$select_angsuran = "SELECT 
  angsuran.*,
  pinjaman.jumlah_pinjaman,
  pinjaman.tenor_bulan,
  pinjaman.bunga_persen,
    pinjaman.basis_bunga,
  pinjaman.status_pinjam,
  pinjaman.tanggal_pengajuan,
  anggota.nik,
  anggota.nama_lengkap,
  users.name AS nama_penerima
FROM angsuran
LEFT JOIN pinjaman ON angsuran.id_pinjaman=pinjaman.id_pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
LEFT JOIN users ON angsuran.id_penerima=users.id_user
WHERE angsuran.id_angsuran IN (
  SELECT MAX(a.id_angsuran)
  FROM angsuran a
  GROUP BY a.id_pinjaman
) $filter_anggota_login
ORDER BY CASE WHEN angsuran.sisa_pinjaman > 0 THEN 0 ELSE 1 END ASC, pinjaman.tanggal_pengajuan ASC, angsuran.tgl_bayar DESC";
$views_angsuran = mysqli_query($conn, $select_angsuran);

if (isset($_POST["add_simpanan"])) {
  if ((int)($_POST['rat_ditampilkan'] ?? 0) !== (int)$rat_aktif['id_rat']) {
    alert('Ketentuan RAT telah diperbarui. Periksa kembali ketentuan pada formulir sebelum mengajukan.', 'warning');
    header('Location: add-data-simpanan'); exit();
  }

  if ($_POST['jenis_simpanan'] === 'Simpanan Pokok' && $rat_aktif['simpanan_pokok'] !== null && round((float)$_POST['jumlah'], 2) != (float)$rat_aktif['simpanan_pokok']) {
    alert('Nominal simpanan pokok harus sesuai ketentuan RAT yang berlaku.', 'danger');
    header('Location: add-data-simpanan'); exit();
  }

  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  try {
    mysqli_begin_transaction($conn);
    // For members, ignore any owner supplied by the browser.
    $validated_post['id_anggota'] = resolvePemilikSimpanan(
      $conn, (string) ($_POST['id_anggota'] ?? ''), $is_anggota_login, $id_user
    );
    $validated_post['id_penerima'] = $id_user;
    $validated_post['id_rat'] = $rat_aktif['id_rat'];
    if (simpanan($conn, $validated_post, $action = 'insert') <= 0) {
      throw new RuntimeException('Data simpanan gagal ditambahkan.');
    }
    tandaiAnggotaTetap($conn, (int)$validated_post['id_anggota']);
    mysqli_commit($conn);
    $message = "Pengajuan simpanan tersimpan dan menunggu persetujuan kepala koperasi.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: data-simpanan");
    exit();
  } catch (Throwable $exception) {
    mysqli_rollback($conn);
    error_log("Tambah simpanan: " . $exception->getMessage());
    alert("Data simpanan gagal ditambahkan. Pastikan data anggota valid dan coba kembali.", "danger");
    header("Location: add-data-simpanan");
    exit();
  }
}

if (isset($_POST["add_pinjaman"])) {
  if ((int)($_POST['rat_ditampilkan'] ?? 0) !== (int)$rat_aktif['id_rat']) {
    alert('Ketentuan RAT telah diperbarui. Periksa kembali ketentuan pada formulir sebelum mengajukan.', 'warning');
    header('Location: add-data-pinjaman'); exit();
  }

  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  try {
    mysqli_begin_transaction($conn);
    $validated_post['id_anggota'] = resolvePemilikSimpanan($conn, (string)($_POST['id_anggota'] ?? ''), $is_anggota_login, $id_user);
    $validated_post['id_validator'] = '';
    $validated_post['bunga_persen'] = $rat_aktif['bunga_persen'];
    $validated_post['tenor_bulan'] = $rat_aktif['tenor_bulan'];
    $validated_post['id_rat'] = $rat_aktif['id_rat'];
    $validated_post['basis_bunga'] = 'total';
    $validated_post['status_pinjam'] = 'Pengajuan';
    $validated_post['tanggal_pengajuan'] = date('Y-m-d');
    $validated_post['tanggal_disetujui'] = '';
    if (pinjaman($conn, $validated_post, 'insert') <= 0) throw new RuntimeException('Pinjaman gagal disimpan.');
    tandaiAnggotaTetap($conn, (int)$validated_post['id_anggota']);
    mysqli_commit($conn);
    alert('Pengajuan pinjaman berhasil disimpan.', 'success');
    header('Location: data-pinjaman'); exit();
  } catch (Throwable $exception) {
    mysqli_rollback($conn);
    error_log('Tambah pinjaman: '.$exception->getMessage());
    alert('Data pinjaman gagal ditambahkan. Pastikan data anggota valid dan coba kembali.', 'danger');
    header('Location: add-data-pinjaman'); exit();
  }
}

if (isset($_POST["edit_pinjaman"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (!$can_access_keuangan('id_pinjaman', $validated_post['id_pinjaman']) || !$can_access_keuangan('id_anggota', $validated_post['id_anggota'])) {
    $deny_akses_keuangan('data-pinjaman');
  }
  $old_id = (int)$validated_post['id_pinjaman'];
  $old_pinjaman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pinjaman WHERE id_pinjaman=$old_id"));
  if (!$old_pinjaman || $old_pinjaman['status_pinjam'] !== 'Pengajuan') {
    alert('Pengajuan yang sudah diperiksa tidak dapat diubah.', 'danger');
    header('Location: data-pinjaman'); exit();
  }
  foreach (['id_validator','bunga_persen','tenor_bulan','id_rat','basis_bunga','status_pinjam','tanggal_pengajuan','tanggal_disetujui'] as $field) {
    $validated_post[$field] = $old_pinjaman[$field];
  }
  try {
    mysqli_begin_transaction($conn);
    $application_id = (int)$validated_post['id_pinjaman'];
    $locked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status_pinjam FROM pinjaman WHERE id_pinjaman=$application_id FOR UPDATE"));
    if (!$locked || $locked['status_pinjam'] !== 'Pengajuan') throw new RuntimeException('Status pengajuan sudah berubah.');
    if (pinjaman($conn, $validated_post, 'update') === false) throw new RuntimeException('Perubahan gagal disimpan.');
    tandaiAnggotaTetap($conn, (int)$validated_post['id_anggota']);
    mysqli_commit($conn);
    alert('Data pinjaman berhasil diubah.', 'success');
  } catch (Throwable $exception) {
    mysqli_rollback($conn);
    error_log('Ubah pinjaman: '.$exception->getMessage());
    alert('Perubahan gagal disimpan. Periksa status dan data anggota.', 'danger');
  }
  header('Location: data-pinjaman'); exit();
}

if (isset($_POST["delete_pinjaman"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (!$can_access_keuangan('id_pinjaman', $validated_post['id_pinjaman'])) {
    $deny_akses_keuangan('data-pinjaman');
  }
  if (pinjaman($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Data pinjaman " . $_POST['nama_lengkap'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: data-pinjaman");
    exit();
  }
}

if (isset($_POST["add_angsuran"])) {
  $loan_id = (int)($_POST['id_pinjaman'] ?? 0);
  $approved = mysqli_query($conn, "SELECT id_pinjaman FROM pinjaman WHERE id_pinjaman=$loan_id AND status_pinjam='Disetujui'");
  if (!mysqli_num_rows($approved)) {
    alert('Angsuran hanya dapat dicatat setelah pinjaman disetujui kepala koperasi.', 'danger');
    header('Location: data-angsuran'); exit();
  }

  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (!$can_access_keuangan('id_pinjaman', $validated_post['id_pinjaman'])) {
    $deny_akses_keuangan('data-angsuran');
  }
  $validated_post['id_penerima'] = $id_user;
  if (angsuran($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Data angsuran berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: data-angsuran");
    exit();
  }
}

if (isset($_POST["edit_angsuran"])) {
  $loan_id = (int)($_POST['id_pinjaman'] ?? 0);
  $approved = mysqli_query($conn, "SELECT id_pinjaman FROM pinjaman WHERE id_pinjaman=$loan_id AND status_pinjam='Disetujui'");
  if (!mysqli_num_rows($approved)) {
    alert('Angsuran hanya dapat dicatat setelah pinjaman disetujui kepala koperasi.', 'danger');
    header('Location: data-angsuran'); exit();
  }

  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (!$can_access_keuangan('id_angsuran', $validated_post['id_angsuran']) || !$can_access_keuangan('id_pinjaman', $validated_post['id_pinjaman'])) {
    $deny_akses_keuangan('data-angsuran');
  }
  $validated_post['id_penerima'] = $id_user;
  if (angsuran($conn, $validated_post, $action = 'update') > 0) {
    $message = "Data angsuran berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: data-angsuran");
    exit();
  }
}

if (isset($_POST["delete_angsuran"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (!$can_access_keuangan('id_angsuran', $validated_post['id_angsuran'])) {
    $deny_akses_keuangan('data-angsuran');
  }
  if (angsuran($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Data angsuran " . $_POST['nama_lengkap'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    if (isset($_POST['redirect_detail_angsuran'])) {
      $id_pinjaman = valid($conn, $_POST['id_pinjaman']);
      $select_latest_angsuran = mysqli_query($conn, "SELECT id_angsuran FROM angsuran WHERE id_pinjaman='$id_pinjaman' ORDER BY id_angsuran DESC LIMIT 1");
      if ($select_latest_angsuran && mysqli_num_rows($select_latest_angsuran) > 0) {
        $latest_angsuran = mysqli_fetch_assoc($select_latest_angsuran);
        header("Location: detail-angsuran?p=" . $latest_angsuran['id_angsuran']);
        exit();
      }
    }
    header("Location: data-angsuran");
    exit();
  }
}

if (isset($_POST["edit_simpanan"])) {
  $edit_id = (int)($_POST['id_simpanan'] ?? 0);
  $old_rat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ketentuan_rat.simpanan_pokok FROM simpanan LEFT JOIN ketentuan_rat ON simpanan.id_rat=ketentuan_rat.id_rat WHERE id_simpanan=$edit_id"));
  if ($_POST['jenis_simpanan'] === 'Simpanan Pokok' && isset($old_rat['simpanan_pokok']) && round((float)$_POST['jumlah'], 2) != (float)$old_rat['simpanan_pokok']) {
    alert('Nominal simpanan pokok harus sesuai RAT pengajuan.', 'danger');
    header('Location: data-simpanan'); exit();
  }

  $simpanan_id = (int) ($_POST['id_simpanan'] ?? 0);
  $pending = mysqli_query($conn, "SELECT id_simpanan FROM simpanan WHERE id_simpanan=$simpanan_id AND status_simpanan='Pengajuan'");
  if (!mysqli_num_rows($pending)) {
    alert('Simpanan yang telah direview tidak dapat diubah atau dihapus.', 'danger');
    header('Location: data-simpanan');
    exit();
  }

  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (!$can_access_keuangan('id_simpanan', $validated_post['id_simpanan']) || !$can_access_keuangan('id_anggota', $validated_post['id_anggota'])) {
    $deny_akses_keuangan('data-simpanan');
  }
  $validated_post['id_penerima'] = $id_user;
  try {
    mysqli_begin_transaction($conn);
    $application_id = (int)$validated_post['id_simpanan'];
    $locked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status_simpanan FROM simpanan WHERE id_simpanan=$application_id FOR UPDATE"));
    if (!$locked || $locked['status_simpanan'] !== 'Pengajuan') throw new RuntimeException('Status pengajuan sudah berubah.');
    if (simpanan($conn, $validated_post, 'update') === false) throw new RuntimeException('Perubahan gagal disimpan.');
    tandaiAnggotaTetap($conn, (int)$validated_post['id_anggota']);
    mysqli_commit($conn);
    alert('Data simpanan berhasil diubah.', 'success');
  } catch (Throwable $exception) {
    mysqli_rollback($conn);
    error_log('Ubah simpanan: '.$exception->getMessage());
    alert('Perubahan gagal disimpan. Periksa status dan data anggota.', 'danger');
  }
  header('Location: data-simpanan'); exit();
}

if (isset($_POST["delete_simpanan"])) {
  $simpanan_id = (int) ($_POST['id_simpanan'] ?? 0);
  $pending = mysqli_query($conn, "SELECT id_simpanan FROM simpanan WHERE id_simpanan=$simpanan_id AND status_simpanan='Pengajuan'");
  if (!mysqli_num_rows($pending)) {
    alert('Simpanan yang telah direview tidak dapat diubah atau dihapus.', 'danger');
    header('Location: data-simpanan');
    exit();
  }

  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (!$can_access_keuangan('id_simpanan', $validated_post['id_simpanan'])) {
    $deny_akses_keuangan('data-simpanan');
  }
  if (simpanan($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Data simpanan " . $_POST['nama_lengkap'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: data-simpanan");
    exit();
  }
}
