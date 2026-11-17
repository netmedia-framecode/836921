<?php

function buatAkunAnggota($conn, array $data)
{
  foreach (['name'=>100, 'nama_lengkap'=>150, 'email'=>75, 'nik'=>20, 'no_telp'=>20, 'alamat'=>2000] as $field=>$max) {
    if (!isset($data[$field]) || !is_string($data[$field]) || trim($data[$field]) === '' || mb_strlen(trim($data[$field])) > $max) {
      throw new InvalidArgumentException('Lengkapi data akun dan identitas anggota dengan benar.');
    }
    $data[$field] = trim($data[$field]);
  }
  if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9]{16,20}$/', $data['nik']) || !preg_match('/^\+?[0-9 ()-]{8,20}$/', $data['no_telp'])) {
    throw new InvalidArgumentException('Email, NIK (16–20 digit), atau nomor telepon tidak valid.');
  }
  if (!is_string($data['password'] ?? null) || strlen($data['password']) < 6 || strlen($data['password']) > 100 || $data['password'] !== ($data['re_password'] ?? null)) {
    throw new InvalidArgumentException('Password minimal 6 karakter dan konfirmasi password harus sama.');
  }
  $role = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_role FROM user_role WHERE LOWER(role) IN ('anggota','member') ORDER BY id_role LIMIT 1"));
  $inactive = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_status FROM user_status WHERE LOWER(status)='no active' LIMIT 1"));
  if (!$role || !$inactive) throw new RuntimeException('Konfigurasi akun anggota belum tersedia.');
  $stmt = mysqli_prepare($conn, 'SELECT id_user FROM users WHERE email=? LIMIT 1');
  mysqli_stmt_bind_param($stmt, 's', $data['email']); mysqli_stmt_execute($stmt);
  $exists = mysqli_stmt_get_result($stmt)->num_rows > 0; mysqli_stmt_close($stmt);
  if ($exists) throw new InvalidArgumentException('Email sudah terdaftar. Silakan masuk atau gunakan pemulihan password.');
  $stmt = mysqli_prepare($conn, 'SELECT id_anggota FROM anggota WHERE nik=? LIMIT 1');
  mysqli_stmt_bind_param($stmt, 's', $data['nik']); mysqli_stmt_execute($stmt);
  $exists = mysqli_stmt_get_result($stmt)->num_rows > 0; mysqli_stmt_close($stmt);
  if ($exists) throw new InvalidArgumentException('NIK sudah terdaftar. Hubungi pengurus untuk menghubungkan akun anggota.');
  // Existing member views store/display escaped text; match that convention.
  foreach (['name'=>100, 'nama_lengkap'=>150, 'alamat'=>2000] as $field=>$max) {
    $data[$field] = htmlspecialchars($data[$field], ENT_QUOTES, 'UTF-8');
    if (mb_strlen($data[$field]) > $max) throw new InvalidArgumentException('Teks identitas terlalu panjang.');
  }
  $token = (string)random_int(100000,999999);
  $en_user = bin2hex(random_bytes(24));
  $password = password_hash($data['password'], PASSWORD_DEFAULT);
  mysqli_begin_transaction($conn);
  try {
    $stmt = mysqli_prepare($conn, "INSERT INTO users(id_role,id_active,en_user,token,name,email,password,image) VALUES(?,?,?,?,?,?,?,'default.svg')");
    mysqli_stmt_bind_param($stmt, 'iisssss', $role['id_role'], $inactive['id_status'], $en_user, $token, $data['name'], $data['email'], $password);
    mysqli_stmt_execute($stmt); $id_user = mysqli_insert_id($conn); mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare($conn, "INSERT INTO anggota(id_user,nik,nama_lengkap,jenis_anggota,no_telp,alamat,tgl_gabung) VALUES(?,?,?,'Tidak Tetap',?,?,CURDATE())");
    mysqli_stmt_bind_param($stmt, 'issss', $id_user, $data['nik'], $data['nama_lengkap'], $data['no_telp'], $data['alamat']);
    mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    mysqli_commit($conn);
  } catch (Throwable $exception) {
    mysqli_rollback($conn); throw $exception;
  }
  return ['id_user'=>$id_user, 'en_user'=>$en_user, 'token'=>$token, 'email'=>$data['email']];
}
