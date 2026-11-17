<?php

// Call inside a transaction: locking the user prevents duplicate member profiles.
function resolvePemilikSimpanan($conn, $pilihan, $is_anggota_login, $id_user)
{
  if ($is_anggota_login) {
    $pemilik_user = (int) $id_user;
  } elseif (preg_match('/^user:([1-9][0-9]*)$/', $pilihan, $matches)) {
    $pemilik_user = (int) $matches[1];
  } elseif (preg_match('/^[1-9][0-9]*$/', $pilihan)) {
    $id_anggota = (int) $pilihan;
    $result = mysqli_query($conn, "SELECT id_anggota FROM anggota WHERE id_anggota=$id_anggota");
    if ($row = mysqli_fetch_assoc($result)) {
      return (int) $row['id_anggota'];
    }
    throw new RuntimeException('Anggota tidak ditemukan.');
  } else {
    throw new RuntimeException('Pilih anggota yang valid.');
  }

  $result = mysqli_query($conn, "SELECT users.id_user, users.name, users.created_at
    FROM users JOIN user_role ON users.id_role=user_role.id_role
    WHERE users.id_user=$pemilik_user AND LOWER(user_role.role) IN ('anggota', 'member')
    FOR UPDATE");
  $user = mysqli_fetch_assoc($result);
  if (!$user) {
    throw new RuntimeException('Akun anggota tidak ditemukan.');
  }

  $result = mysqli_query($conn, "SELECT id_anggota FROM anggota WHERE id_user=$pemilik_user ORDER BY id_anggota LIMIT 1");
  if ($row = mysqli_fetch_assoc($result)) {
    return (int) $row['id_anggota'];
  }

  // Self-registration does not collect a NIK; leave it unknown, never fabricate one.
  $statement = mysqli_prepare($conn, "INSERT INTO anggota (id_user, nik, nama_lengkap, tgl_gabung) VALUES (?, NULL, ?, DATE(?))");
  mysqli_stmt_bind_param($statement, 'iss', $pemilik_user, $user['name'], $user['created_at']);
  if (!mysqli_stmt_execute($statement)) {
    throw new RuntimeException('Data anggota gagal dibuat.');
  }
  mysqli_stmt_close($statement);
  return mysqli_insert_id($conn);
}

// Call in the same transaction as the successful application insert.
function tandaiAnggotaTetap($conn, $id_anggota)
{
  $stmt = mysqli_prepare($conn, "UPDATE anggota SET jenis_anggota='Tetap' WHERE id_anggota=?");
  mysqli_stmt_bind_param($stmt, 'i', $id_anggota);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}
