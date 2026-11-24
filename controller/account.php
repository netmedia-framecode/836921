<?php

require_once("../config/Base.php");
require_once("../views/redirect.php");
require_once("../config/Auth.php");
require_once("../config/Alert.php");


require_once __DIR__ . '/profil-anggota.php';
$is_profile_anggota = in_array(strtolower($role), ['anggota','member'], true);
$profile_anggota_error = '';
if (empty($_SESSION['csrf_profil_anggota'])) $_SESSION['csrf_profil_anggota'] = bin2hex(random_bytes(32));
if (isset($_POST['edit_profil_anggota'])) {
  if (!$is_profile_anggota || !is_string($_POST['csrf_profil_anggota'] ?? null) || !hash_equals($_SESSION['csrf_profil_anggota'], $_POST['csrf_profil_anggota'])) {
    http_response_code(403); exit('Permintaan tidak diizinkan.');
  }
  try {
    simpanProfilAnggota($conn, (int)$id_user, $_POST);
    alert('Biodata anggota berhasil diperbarui.', 'success');
    header('Location: profil'); exit;
  } catch (InvalidArgumentException $e) {
    $profile_anggota_error = $e->getMessage();
  } catch (Throwable $e) {
    error_log('Update profil anggota gagal: ' . $e->getMessage());
    $profile_anggota_error = 'Biodata gagal disimpan. Silakan coba kembali.';
  }
}
$profil_anggota = [];
if ($is_profile_anggota) {
  $stmt = $conn->prepare('SELECT * FROM anggota WHERE id_user=? ORDER BY id_anggota LIMIT 1');
  $profile_user_id = (int)$id_user;
  $stmt->bind_param('i', $profile_user_id); $stmt->execute();
  $profil_anggota = $stmt->get_result()->fetch_assoc() ?: [];
  $stmt->close();
}

$select_profile = "SELECT users.*, user_role.role, user_status.status 
FROM users
JOIN user_role ON users.id_role=user_role.id_role 
JOIN user_status ON users.id_active=user_status.id_status 
WHERE users.id_user='$id_user'
";
$view_profile = mysqli_query($conn, $select_profile);
if (isset($_POST["edit_profil"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (profil($conn, $validated_post, $action = 'update', $id_user) > 0) {
    $message = "Profil Anda berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: profil");
    exit();
  }
}

if (isset($_POST["setting"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (setting($conn, $validated_post, $action = 'update') > 0) {
    $message = "Setting pada system login berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: setting");
    exit();
  }
}
