<?php

require_once("../../config/Base.php");
require_once("../../config/Auth.php");
require_once("../../config/Alert.php");
require_once("../../views/pusat-informasi/redirect.php");

if (isset($_POST['add_berita']) || isset($_POST['edit_berita']) || isset($_POST['delete_berita'])) { http_response_code(410); exit('Fitur berita sudah dihapus.'); }

$select_pengumuman = "SELECT
  pengumuman.*,
  users.name AS nama_admin
FROM pengumuman
LEFT JOIN users ON pengumuman.id_admin=users.id_user
ORDER BY pengumuman.tgl_posting DESC, pengumuman.id_pengumuman DESC";
$views_pengumuman = mysqli_query($conn, $select_pengumuman);

$select_kebijakan = "SELECT
  kebijakan_koperasi.*,
  users.name AS nama_admin
FROM kebijakan_koperasi
LEFT JOIN users ON kebijakan_koperasi.id_admin=users.id_user
ORDER BY kebijakan_koperasi.tgl_ubah DESC, kebijakan_koperasi.id_kebijakan DESC";
$views_kebijakan = mysqli_query($conn, $select_kebijakan);


if (isset($_POST["add_pengumuman"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  $validated_post['id_admin'] = $id_user;
  if (pengumuman($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Data pengumuman berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: pengumuman");
    exit();
  }
}

if (isset($_POST["add_kebijakan"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  $validated_post['id_admin'] = $id_user;
  if (kebijakan($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Data kebijakan berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: kebijakan-koperasi");
    exit();
  }
}

if (isset($_POST["delete_pengumuman"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (pengumuman($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Data pengumuman " . $_POST['judul'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: pengumuman");
    exit();
  }
}

if (isset($_POST["delete_kebijakan"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (kebijakan($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Data kebijakan " . $_POST['judul'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: kebijakan-koperasi");
    exit();
  }
}

if (isset($_POST["edit_pengumuman"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  $validated_post['id_admin'] = $id_user;
  if (pengumuman($conn, $validated_post, $action = 'update') > 0) {
    $message = "Data pengumuman berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: pengumuman");
    exit();
  }
}

if (isset($_POST["edit_kebijakan"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  $validated_post['id_admin'] = $id_user;
  if (kebijakan($conn, $validated_post, $action = 'update') > 0) {
    $message = "Data kebijakan berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: kebijakan-koperasi");
    exit();
  }
}
