<?php

require_once("../../config/Base.php");
require_once("../../config/Auth.php");
require_once("../../config/Alert.php");
require_once("../../views/manajemen-pengguna/redirect.php");

$select_users = "SELECT users.*, user_role.role, user_status.status
FROM users
JOIN user_role ON users.id_role=user_role.id_role
JOIN user_status ON users.id_active=user_status.id_status
WHERE LOWER(user_role.role) != 'anggota' AND LOWER(user_role.role) != 'administrator'
";
$views_users = mysqli_query($conn, $select_users);
$select_user_role = "SELECT * FROM user_role";
$views_user_role = mysqli_query($conn, $select_user_role);
$select_user_status = "SELECT * FROM user_status";
$views_user_status = mysqli_query($conn, $select_user_status);
$select_member_role = "SELECT id_role FROM user_role WHERE LOWER(role) IN ('anggota', 'member') ORDER BY id_role ASC LIMIT 1";
$view_member_role = mysqli_query($conn, $select_member_role);
$data_member_role = mysqli_fetch_assoc($view_member_role);
$member_role_id = $data_member_role ? $data_member_role['id_role'] : 4;
if (isset($_POST["edit_users"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (users($conn, $validated_post, $action = 'update') > 0) {
    $message = "data users berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: users");
    exit();
  }
}
if (isset($_POST["add_anggota"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (anggota($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Data anggota baru berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: anggota");
    exit();
  }
}
if (isset($_POST["edit_anggota"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (anggota($conn, $validated_post, $action = 'update') > 0) {
    $message = "Data anggota berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: anggota");
    exit();
  }
}
if (isset($_POST["delete_anggota"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (anggota($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Data anggota " . $_POST['nama_lengkap'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: anggota");
    exit();
  }
}
if (isset($_POST["add_role"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (role($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Role baru berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: role");
    exit();
  }
}
if (isset($_POST["edit_role"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (role($conn, $validated_post, $action = 'update') > 0) {
    $message = "Role " . $_POST['roleOld'] . " berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: role");
    exit();
  }
}
if (isset($_POST["delete_role"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (role($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Role " . $_POST['role'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: role");
    exit();
  }
}

$select_anggota = "SELECT anggota.*, users.name, users.email, users.id_active, user_role.role, user_status.status
FROM anggota
JOIN users ON anggota.id_user=users.id_user
JOIN user_role ON users.id_role=user_role.id_role
JOIN user_status ON users.id_active=user_status.id_status
WHERE LOWER(user_role.role) IN ('anggota', 'member')
ORDER BY anggota.id_anggota DESC
";
$views_anggota = mysqli_query($conn, $select_anggota);
