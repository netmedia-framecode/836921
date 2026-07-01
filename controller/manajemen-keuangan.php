<?php

require_once("../../config/Base.php");
require_once("../../config/Auth.php");
require_once("../../config/Alert.php");
require_once("../../views/manajemen-keuangan/redirect.php");

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

$select_simpanan = "SELECT 
  simpanan.*,
  anggota.nik,
  anggota.nama_lengkap,
  users.name AS nama_penerima
FROM simpanan
LEFT JOIN anggota ON simpanan.id_anggota=anggota.id_anggota
LEFT JOIN users ON simpanan.id_penerima=users.id_user
WHERE 1=1 $filter_anggota_login
ORDER BY simpanan.id_simpanan DESC";
$views_simpanan = mysqli_query($conn, $select_simpanan);

$select_pinjaman = "SELECT 
  pinjaman.*,
  anggota.nik,
  anggota.nama_lengkap,
  users.name AS nama_validator
FROM pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
LEFT JOIN users ON pinjaman.id_validator=users.id_user
WHERE 1=1 $filter_anggota_login
ORDER BY pinjaman.id_pinjaman DESC";
$views_pinjaman = mysqli_query($conn, $select_pinjaman);

$select_pinjaman_angsuran = "SELECT
  pinjaman.id_pinjaman,
  pinjaman.jumlah_pinjaman,
  pinjaman.tenor_bulan,
  pinjaman.bunga_persen,
  pinjaman.status_pinjam,
  pinjaman.tanggal_pengajuan,
  anggota.nik,
  anggota.nama_lengkap,
  COALESCE(
    (SELECT a.sisa_pinjaman FROM angsuran a WHERE a.id_pinjaman=pinjaman.id_pinjaman ORDER BY a.id_angsuran DESC LIMIT 1),
    (pinjaman.jumlah_pinjaman + (pinjaman.jumlah_pinjaman * (pinjaman.bunga_persen / 100) * pinjaman.tenor_bulan))
  ) AS sisa_pinjaman_terakhir,
  COALESCE(
    (SELECT MAX(a.angsuran_ke) FROM angsuran a WHERE a.id_pinjaman=pinjaman.id_pinjaman),
    0
  ) AS angsuran_terakhir
FROM pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
WHERE LOWER(pinjaman.status_pinjam) != 'lunas' $filter_anggota_login
HAVING sisa_pinjaman_terakhir > 0
ORDER BY pinjaman.tanggal_pengajuan ASC, pinjaman.id_pinjaman ASC";
$views_pinjaman_angsuran = mysqli_query($conn, $select_pinjaman_angsuran);

$select_angsuran = "SELECT 
  angsuran.*,
  pinjaman.jumlah_pinjaman,
  pinjaman.tenor_bulan,
  pinjaman.bunga_persen,
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
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (!$can_access_keuangan('id_anggota', $validated_post['id_anggota'])) {
    $deny_akses_keuangan('data-simpanan');
  }
  $validated_post['id_penerima'] = $id_user;
  if (simpanan($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Data simpanan berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: data-simpanan");
    exit();
  }
}

if (isset($_POST["add_pinjaman"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (!$can_access_keuangan('id_anggota', $validated_post['id_anggota'])) {
    $deny_akses_keuangan('data-pinjaman');
  }
  if ($is_anggota_login) {
    $allowed_tenor = ['3', '6', '9', '12', '18', '24'];
    if (!in_array($validated_post['tenor_bulan'], $allowed_tenor)) {
      $deny_akses_keuangan('data-pinjaman');
    }
    $validated_post['id_validator'] = '';
    $validated_post['bunga_persen'] = 0;
    $validated_post['status_pinjam'] = 'Pengajuan';
    $validated_post['tanggal_pengajuan'] = date('Y-m-d');
    $validated_post['tanggal_disetujui'] = '';
  } else {
    $validated_post['id_validator'] = $id_user;
  }
  if (pinjaman($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Data pinjaman berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: data-pinjaman");
    exit();
  }
}

if (isset($_POST["edit_pinjaman"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (!$can_access_keuangan('id_pinjaman', $validated_post['id_pinjaman']) || !$can_access_keuangan('id_anggota', $validated_post['id_anggota'])) {
    $deny_akses_keuangan('data-pinjaman');
  }
  if ($is_anggota_login) {
    $allowed_tenor = ['3', '6', '9', '12', '18', '24'];
    if (!in_array($validated_post['tenor_bulan'], $allowed_tenor)) {
      $deny_akses_keuangan('data-pinjaman');
    }
    $old_pinjaman_query = mysqli_query($conn, "SELECT bunga_persen, status_pinjam, tanggal_pengajuan, tanggal_disetujui, id_validator FROM pinjaman WHERE id_pinjaman='$validated_post[id_pinjaman]'");
    $old_pinjaman = mysqli_fetch_assoc($old_pinjaman_query);
    if ($old_pinjaman && strtolower($old_pinjaman['status_pinjam']) == 'disetujui') {
      alert("Pinjaman yang sudah disetujui tidak dapat diubah.", "warning");
      header("Location: data-pinjaman");
      exit();
    }
    $validated_post['id_validator'] = $old_pinjaman['id_validator'] ?? '';
    $validated_post['bunga_persen'] = $old_pinjaman['bunga_persen'] ?? 0;
    $validated_post['status_pinjam'] = $old_pinjaman['status_pinjam'] ?? 'Pengajuan';
    $validated_post['tanggal_pengajuan'] = $old_pinjaman['tanggal_pengajuan'] ?? date('Y-m-d');
    $validated_post['tanggal_disetujui'] = $old_pinjaman['tanggal_disetujui'] ?? '';
  } else {
    $validated_post['id_validator'] = $id_user;
  }
  if (pinjaman($conn, $validated_post, $action = 'update') > 0) {
    $message = "Data pinjaman berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: data-pinjaman");
    exit();
  }
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
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (!$can_access_keuangan('id_simpanan', $validated_post['id_simpanan']) || !$can_access_keuangan('id_anggota', $validated_post['id_anggota'])) {
    $deny_akses_keuangan('data-simpanan');
  }
  $validated_post['id_penerima'] = $id_user;
  if (simpanan($conn, $validated_post, $action = 'update') > 0) {
    $message = "Data simpanan berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: data-simpanan");
    exit();
  }
}

if (isset($_POST["delete_simpanan"])) {
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
