<?php

require_once("../config/Base.php");
require_once("../config/Auth.php");
require_once("../config/Alert.php");

$is_anggota_login = in_array(strtolower($role), ['anggota', 'member']);
$filter_dashboard_anggota = $is_anggota_login ? " WHERE anggota.id_user = '$id_user'" : "";
$and_filter_dashboard_anggota = $is_anggota_login ? " AND anggota.id_user = '$id_user'" : "";

$dashboard_count = function ($query) use ($conn) {
  $result = mysqli_query($conn, $query);
  $data = $result ? mysqli_fetch_assoc($result) : null;
  return (int) ($data['total'] ?? 0);
};

$dashboard_sum_count = function ($query) use ($conn) {
  $result = mysqli_query($conn, $query);
  $data = $result ? mysqli_fetch_assoc($result) : null;
  return [
    'total' => (float) ($data['total'] ?? 0),
    'jumlah_data' => (int) ($data['jumlah_data'] ?? 0)
  ];
};

$dashboard_simpanan = $dashboard_sum_count("SELECT COALESCE(SUM(simpanan.jumlah), 0) AS total, COUNT(*) AS jumlah_data
FROM simpanan
LEFT JOIN anggota ON simpanan.id_anggota=anggota.id_anggota
$filter_dashboard_anggota");

$dashboard_pinjaman = $dashboard_sum_count("SELECT COALESCE(SUM(pinjaman.jumlah_pinjaman), 0) AS total, COUNT(*) AS jumlah_data
FROM pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
$filter_dashboard_anggota");

$dashboard_angsuran = $dashboard_sum_count("SELECT COALESCE(SUM(angsuran.jumlah_bayar), 0) AS total, COUNT(*) AS jumlah_data
FROM angsuran
LEFT JOIN pinjaman ON angsuran.id_pinjaman=pinjaman.id_pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
$filter_dashboard_anggota");

$dashboard_total_anggota = $dashboard_count("SELECT COUNT(*) AS total FROM anggota");
$dashboard_total_pengguna = $dashboard_count("SELECT COUNT(*) AS total FROM users");
$dashboard_pinjaman_pengajuan = $dashboard_count("SELECT COUNT(*) AS total
FROM pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
WHERE LOWER(pinjaman.status_pinjam)='pengajuan' $and_filter_dashboard_anggota");
$dashboard_pinjaman_disetujui = $dashboard_count("SELECT COUNT(*) AS total
FROM pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
WHERE LOWER(pinjaman.status_pinjam)='disetujui' $and_filter_dashboard_anggota");

$dashboard_sisa_pinjaman_query = mysqli_query($conn, "SELECT COALESCE(SUM(sisa_terakhir), 0) AS total
FROM (
  SELECT
    pinjaman.id_pinjaman,
    COALESCE(
      (SELECT angsuran.sisa_pinjaman FROM angsuran WHERE angsuran.id_pinjaman=pinjaman.id_pinjaman ORDER BY angsuran.id_angsuran DESC LIMIT 1),
      (pinjaman.jumlah_pinjaman + (pinjaman.jumlah_pinjaman * (pinjaman.bunga_persen / 100) * pinjaman.tenor_bulan))
    ) AS sisa_terakhir
  FROM pinjaman
  LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
  WHERE LOWER(pinjaman.status_pinjam) != 'ditolak' $and_filter_dashboard_anggota
) sisa_pinjaman");
$dashboard_sisa_pinjaman_data = $dashboard_sisa_pinjaman_query ? mysqli_fetch_assoc($dashboard_sisa_pinjaman_query) : null;
$dashboard_sisa_pinjaman = (float) ($dashboard_sisa_pinjaman_data['total'] ?? 0);

$dashboard_chart_bulan = [];
$dashboard_chart_simpanan = [];
$dashboard_chart_pinjaman = [];
$dashboard_chart_angsuran = [];
for ($month = 1; $month <= 12; $month++) {
  $key = date('Y') . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
  $dashboard_chart_bulan[$key] = date('M', mktime(0, 0, 0, $month, 1));
  $dashboard_chart_simpanan[$key] = 0;
  $dashboard_chart_pinjaman[$key] = 0;
  $dashboard_chart_angsuran[$key] = 0;
}

$dashboard_chart_query = mysqli_query($conn, "
  SELECT 'Simpanan' AS sumber, simpanan.jumlah AS nominal, simpanan.tgl_simpanan AS tanggal
  FROM simpanan
  LEFT JOIN anggota ON simpanan.id_anggota=anggota.id_anggota
  $filter_dashboard_anggota

  UNION ALL

  SELECT 'Pinjaman' AS sumber, pinjaman.jumlah_pinjaman AS nominal, pinjaman.tanggal_pengajuan AS tanggal
  FROM pinjaman
  LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
  $filter_dashboard_anggota

  UNION ALL

  SELECT 'Angsuran' AS sumber, angsuran.jumlah_bayar AS nominal, angsuran.tgl_bayar AS tanggal
  FROM angsuran
  LEFT JOIN pinjaman ON angsuran.id_pinjaman=pinjaman.id_pinjaman
  LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
  $filter_dashboard_anggota
");

if ($dashboard_chart_query) {
  foreach ($dashboard_chart_query as $chart_row) {
    if (!$chart_row['tanggal']) {
      continue;
    }
    $month_key = date('Y-m', strtotime($chart_row['tanggal']));
    if (!isset($dashboard_chart_bulan[$month_key])) {
      continue;
    }
    if ($chart_row['sumber'] == 'Simpanan') {
      $dashboard_chart_simpanan[$month_key] += (float) $chart_row['nominal'];
    } else if ($chart_row['sumber'] == 'Pinjaman') {
      $dashboard_chart_pinjaman[$month_key] += (float) $chart_row['nominal'];
    } else if ($chart_row['sumber'] == 'Angsuran') {
      $dashboard_chart_angsuran[$month_key] += (float) $chart_row['nominal'];
    }
  }
}

$dashboard_status_query = mysqli_query($conn, "SELECT pinjaman.status_pinjam, COUNT(*) AS total
FROM pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
WHERE 1=1 $and_filter_dashboard_anggota
GROUP BY pinjaman.status_pinjam
ORDER BY total DESC");
$dashboard_status_labels = [];
$dashboard_status_data = [];
if ($dashboard_status_query) {
  foreach ($dashboard_status_query as $status) {
    $dashboard_status_labels[] = $status['status_pinjam'] ?: 'Tanpa Status';
    $dashboard_status_data[] = (int) $status['total'];
  }
}

$dashboard_recent_query = mysqli_query($conn, "
  SELECT 'Simpanan' AS kategori, simpanan.tgl_simpanan AS tanggal, anggota.nama_lengkap AS anggota, simpanan.jumlah AS nominal, simpanan.jenis_simpanan AS detail
  FROM simpanan
  LEFT JOIN anggota ON simpanan.id_anggota=anggota.id_anggota
  $filter_dashboard_anggota

  UNION ALL

  SELECT 'Pinjaman' AS kategori, pinjaman.tanggal_pengajuan AS tanggal, anggota.nama_lengkap AS anggota, pinjaman.jumlah_pinjaman AS nominal, pinjaman.status_pinjam AS detail
  FROM pinjaman
  LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
  $filter_dashboard_anggota

  UNION ALL

  SELECT 'Angsuran' AS kategori, angsuran.tgl_bayar AS tanggal, anggota.nama_lengkap AS anggota, angsuran.jumlah_bayar AS nominal, CONCAT('Angsuran ke-', angsuran.angsuran_ke) AS detail
  FROM angsuran
  LEFT JOIN pinjaman ON angsuran.id_pinjaman=pinjaman.id_pinjaman
  LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
  $filter_dashboard_anggota
  ORDER BY tanggal DESC
  LIMIT 8
");
$dashboard_recent = [];
if ($dashboard_recent_query) {
  foreach ($dashboard_recent_query as $recent) {
    $dashboard_recent[] = $recent;
  }
}

$dashboard_info_query = mysqli_query($conn, "
  SELECT 'Berita' AS tipe, judul, tgl_buat AS tanggal FROM berita
  UNION ALL
  SELECT 'Pengumuman' AS tipe, judul, tgl_posting AS tanggal FROM pengumuman
  UNION ALL
  SELECT 'Kebijakan' AS tipe, judul, tgl_ubah AS tanggal FROM kebijakan_koperasi
  ORDER BY tanggal DESC
  LIMIT 5
");
$dashboard_info = [];
if ($dashboard_info_query) {
  foreach ($dashboard_info_query as $info) {
    $dashboard_info[] = $info;
  }
}
