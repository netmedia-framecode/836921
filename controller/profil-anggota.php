<?php
require_once __DIR__ . '/pemilik-simpanan.php';

function simpanProfilAnggota($conn, int $id_user, array $data)
{
  foreach (['nama_lengkap'=>150, 'nik'=>20, 'no_telp'=>20, 'alamat'=>2000] as $key=>$max) {
    if (!is_string($data[$key] ?? null) || trim($data[$key]) === '' || mb_strlen(trim($data[$key])) > $max) {
      throw new InvalidArgumentException('Nama lengkap, NIK, nomor telepon, dan alamat wajib diisi dengan benar.');
    }
    $data[$key] = trim($data[$key]);
  }
  if (!preg_match('/^[0-9]{16,20}$/', $data['nik'])) throw new InvalidArgumentException('NIK harus berisi 16–20 digit angka.');
  if (!preg_match('/^\+?[0-9 ()-]{8,20}$/', $data['no_telp'])) throw new InvalidArgumentException('Nomor telepon tidak valid.');
  $conn->begin_transaction();
  try {
    // Resolves only the authenticated member; posted IDs and membership status are ignored.
    $member_id = resolvePemilikSimpanan($conn, '', true, $id_user);
    $stmt = $conn->prepare('SELECT id_anggota FROM anggota WHERE nik=? AND id_anggota<>? LIMIT 1');
    $stmt->bind_param('si', $data['nik'], $member_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows) throw new InvalidArgumentException('NIK sudah digunakan anggota lain.');
    $stmt->close();
    foreach (['nama_lengkap','alamat'] as $key) $data[$key] = htmlspecialchars($data[$key], ENT_QUOTES, 'UTF-8');
    if (mb_strlen($data['nama_lengkap']) > 150) throw new InvalidArgumentException('Nama lengkap terlalu panjang.');
    $stmt = $conn->prepare('UPDATE anggota SET nama_lengkap=?,nik=?,no_telp=?,alamat=? WHERE id_anggota=? AND id_user=?');
    $stmt->bind_param('ssssii', $data['nama_lengkap'], $data['nik'], $data['no_telp'], $data['alamat'], $member_id, $id_user);
    $stmt->execute();
    $stmt->close();
    $conn->commit();
  } catch (Throwable $e) {
    $conn->rollback();
    throw $e;
  }
}
