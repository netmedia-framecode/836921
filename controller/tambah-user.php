<?php

function buatUserDenganKonfirmasi($conn, $pembuat_id, array $data)
{
  // Identity always comes from the authenticated session, never from the form.
  $stmt = mysqli_prepare($conn, 'SELECT u.password,u.id_role,r.role,s.status FROM users u JOIN user_role r ON r.id_role=u.id_role JOIN user_status s ON s.id_status=u.id_active WHERE u.id_user=?');
  mysqli_stmt_bind_param($stmt, 'i', $pembuat_id);
  mysqli_stmt_execute($stmt); $actor = mysqli_stmt_get_result($stmt)->fetch_assoc(); mysqli_stmt_close($stmt);
  if (!$actor || strtolower($actor['status']) !== 'active') throw new RuntimeException('Akun pembuat tidak aktif.');
  $admin = (int)$actor['id_role'] === 1 || strtolower($actor['role']) === 'administrator';
  if (!$admin) {
    $stmt = mysqli_prepare($conn, "SELECT 1 FROM permissions p JOIN user_sub_menu m ON m.id_sub_menu=p.id_sub_menu WHERE p.id_role=? AND p.`create`=1 AND m.url='manajemen-pengguna/users' LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $actor['id_role']); mysqli_stmt_execute($stmt);
    $allowed = mysqli_stmt_get_result($stmt)->num_rows > 0; mysqli_stmt_close($stmt);
    if (!$allowed) throw new RuntimeException('Anda tidak memiliki hak untuk menambah user.');
  }
  if (!is_string($data['password_pembuat'] ?? null) || !password_verify($data['password_pembuat'], $actor['password'])) {
    throw new DomainException('Password akun Anda salah. User baru belum dibuat.');
  }
  foreach (['name'=>100,'email'=>75] as $field=>$max) {
    if (!is_string($data[$field] ?? null) || trim($data[$field]) === '' || mb_strlen(trim($data[$field])) > $max) throw new InvalidArgumentException('Nama dan email wajib diisi dengan benar.');
    $data[$field] = trim($data[$field]);
  }
  if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Alamat email tidak valid.');
  if (!is_string($data['password'] ?? null) || strlen($data['password']) < 6 || strlen($data['password']) > 100 || $data['password'] !== ($data['re_password'] ?? null)) throw new InvalidArgumentException('Password user baru minimal 6 karakter dan konfirmasinya harus sama.');
  $role_id = filter_var($data['id_role'] ?? '', FILTER_VALIDATE_INT);
  $status_id = filter_var($data['id_active'] ?? '', FILTER_VALIDATE_INT);
  if (!$role_id || !$status_id) throw new InvalidArgumentException('Pilih role dan status yang valid.');
  $stmt = mysqli_prepare($conn, "SELECT 1 FROM user_role WHERE id_role=? AND LOWER(role) IN ('developer','bendahara','administrator','ketua koperasi')");
  mysqli_stmt_bind_param($stmt, 'i', $role_id); mysqli_stmt_execute($stmt);
  $valid_role = mysqli_stmt_get_result($stmt)->num_rows > 0; mysqli_stmt_close($stmt);
  if (!$valid_role) throw new InvalidArgumentException('Pilih role pengurus yang tersedia. Untuk anggota, gunakan menu Anggota.');
  $stmt = mysqli_prepare($conn, 'SELECT 1 FROM user_status WHERE id_status=?');
  mysqli_stmt_bind_param($stmt, 'i', $status_id); mysqli_stmt_execute($stmt);
  $valid_status = mysqli_stmt_get_result($stmt)->num_rows > 0; mysqli_stmt_close($stmt);
  if (!$valid_status) throw new InvalidArgumentException('Status akun tidak valid.');
  $stmt = mysqli_prepare($conn, 'SELECT 1 FROM users WHERE email=? LIMIT 1');
  mysqli_stmt_bind_param($stmt, 's', $data['email']); mysqli_stmt_execute($stmt);
  $duplicate = mysqli_stmt_get_result($stmt)->num_rows > 0; mysqli_stmt_close($stmt);
  if ($duplicate) throw new InvalidArgumentException('Email sudah digunakan oleh akun lain.');
  // Match the escaped-text convention used by existing views.
  $nama = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
  if (mb_strlen($nama) > 100) throw new InvalidArgumentException('Nama terlalu panjang.');
  $hash = password_hash($data['password'], PASSWORD_DEFAULT);
  $en_user = bin2hex(random_bytes(24));
  $stmt = mysqli_prepare($conn, "INSERT INTO users(id_role,id_active,en_user,name,email,password,image) VALUES(?,?,?,?,?,?,'default.svg')");
  mysqli_stmt_bind_param($stmt, 'iissss', $role_id, $status_id, $en_user, $nama, $data['email'], $hash);
  mysqli_stmt_execute($stmt); $created = mysqli_insert_id($conn); mysqli_stmt_close($stmt);
  return $created;
}
