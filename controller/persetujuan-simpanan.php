<?php
require_once(__DIR__ . "/ketentuan-rat.php");

function bolehReviewSimpanan($role)
{
  return in_array(strtolower(trim($role)), ['ketua koperasi', 'kepala koperasi'], true);
}

function validasiDataSimpanan($data)
{
  return in_array($data['jenis_simpanan'] ?? '', ['Simpanan Pokok', 'Simpanan Wajib', 'Simpanan Sukarela'], true)
    && is_numeric($data['jumlah'] ?? null) && (float) $data['jumlah'] > 0
    && (float) $data['jumlah'] <= 9999999999999.99
    && !empty($data['tgl_simpanan']) && strtotime($data['tgl_simpanan']) !== false;
}

function reviewSimpanan($conn, $id, $keputusan, $catatan, $reviewer, $role)
{
  return prosesReviewPengajuan($conn, 'simpanan', $id, $keputusan, $catatan, $reviewer, $role);
}
