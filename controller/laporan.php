<?php

require_once("../../config/Base.php");
require_once("../../config/Auth.php");
require_once("../../config/Alert.php");
require_once("../../views/laporan/redirect.php");

$is_anggota_login = in_array(strtolower($role), ['anggota', 'member']);
$filter_laporan_anggota = $is_anggota_login ? " WHERE anggota.id_user = '$id_user'" : "";

if (isset($_GET['export_transaksi_excel'])) {
  require_once(__DIR__ . "/../assets/vendor/autoload.php");

  $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

  $sheet_configs = [
    [
      'title' => 'Simpanan',
      'headers' => ['ID', 'NIK', 'Nama Anggota', 'Jenis Simpanan', 'Jumlah', 'Tanggal Simpanan', 'Penerima'],
      'query' => "SELECT simpanan.id_simpanan, anggota.nik, anggota.nama_lengkap, simpanan.jenis_simpanan, simpanan.jumlah, simpanan.tgl_simpanan, users.name AS penerima
        FROM simpanan
        LEFT JOIN anggota ON simpanan.id_anggota=anggota.id_anggota
        LEFT JOIN users ON simpanan.id_penerima=users.id_user
        $filter_laporan_anggota
        ORDER BY simpanan.tgl_simpanan DESC",
      'fields' => ['id_simpanan', 'nik', 'nama_lengkap', 'jenis_simpanan', 'jumlah', 'tgl_simpanan', 'penerima']
    ],
    [
      'title' => 'Pinjaman',
      'headers' => ['ID', 'NIK', 'Nama Anggota', 'Jumlah Pinjaman', 'Tenor Bulan', 'Bunga Persen', 'Status', 'Tanggal Pengajuan', 'Tanggal Disetujui', 'Validator'],
      'query' => "SELECT pinjaman.id_pinjaman, anggota.nik, anggota.nama_lengkap, pinjaman.jumlah_pinjaman, pinjaman.tenor_bulan, pinjaman.bunga_persen, pinjaman.status_pinjam, pinjaman.tanggal_pengajuan, pinjaman.tanggal_disetujui, users.name AS validator
        FROM pinjaman
        LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
        LEFT JOIN users ON pinjaman.id_validator=users.id_user
        $filter_laporan_anggota
        ORDER BY pinjaman.tanggal_pengajuan DESC",
      'fields' => ['id_pinjaman', 'nik', 'nama_lengkap', 'jumlah_pinjaman', 'tenor_bulan', 'bunga_persen', 'status_pinjam', 'tanggal_pengajuan', 'tanggal_disetujui', 'validator']
    ],
    [
      'title' => 'Angsuran',
      'headers' => ['ID', 'ID Pinjaman', 'NIK', 'Nama Anggota', 'Angsuran Ke', 'Jumlah Bayar', 'Sisa Pinjaman', 'Tanggal Bayar', 'Penerima'],
      'query' => "SELECT angsuran.id_angsuran, angsuran.id_pinjaman, anggota.nik, anggota.nama_lengkap, angsuran.angsuran_ke, angsuran.jumlah_bayar, angsuran.sisa_pinjaman, angsuran.tgl_bayar, users.name AS penerima
        FROM angsuran
        LEFT JOIN pinjaman ON angsuran.id_pinjaman=pinjaman.id_pinjaman
        LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
        LEFT JOIN users ON angsuran.id_penerima=users.id_user
        $filter_laporan_anggota
        ORDER BY angsuran.tgl_bayar DESC",
      'fields' => ['id_angsuran', 'id_pinjaman', 'nik', 'nama_lengkap', 'angsuran_ke', 'jumlah_bayar', 'sisa_pinjaman', 'tgl_bayar', 'penerima']
    ]
  ];

  foreach ($sheet_configs as $sheet_index => $config) {
    $sheet = $sheet_index === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
    $sheet->setTitle($config['title']);

    foreach ($config['headers'] as $column_index => $header) {
      $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column_index + 1);
      $sheet->setCellValue($column . '1', $header);
    }

    $result = mysqli_query($conn, $config['query']);
    $row_number = 2;
    foreach ($result as $row) {
      foreach ($config['fields'] as $column_index => $field) {
        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column_index + 1);
        $sheet->setCellValue($column . $row_number, $row[$field] ?: '-');
      }
      $row_number++;
    }

    $highest_column = $sheet->getHighestColumn();
    $sheet->getStyle("A1:{$highest_column}1")->getFont()->setBold(true);
    $sheet->getStyle("A1:{$highest_column}1")->getFill()
      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
      ->getStartColor()->setARGB('FFEFF3FF');
    foreach (range('A', $highest_column) as $column) {
      $sheet->getColumnDimension($column)->setAutoSize(true);
    }
  }

  $spreadsheet->setActiveSheetIndex(0);
  $filename = "laporan-transaksi-" . date('Ymd-His') . ".xlsx";

  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment; filename="' . $filename . '"');
  header('Cache-Control: max-age=0');

  $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
  $writer->save('php://output');
  exit();
}

$total_simpanan_query = mysqli_query($conn, "SELECT COALESCE(SUM(simpanan.jumlah), 0) AS total, COUNT(*) AS jumlah_data
FROM simpanan
LEFT JOIN anggota ON simpanan.id_anggota=anggota.id_anggota
$filter_laporan_anggota");
$total_simpanan_data = mysqli_fetch_assoc($total_simpanan_query);

$total_pinjaman_query = mysqli_query($conn, "SELECT COALESCE(SUM(pinjaman.jumlah_pinjaman), 0) AS total, COUNT(*) AS jumlah_data
FROM pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
$filter_laporan_anggota");
$total_pinjaman_data = mysqli_fetch_assoc($total_pinjaman_query);

$total_angsuran_query = mysqli_query($conn, "SELECT COALESCE(SUM(angsuran.jumlah_bayar), 0) AS total, COUNT(*) AS jumlah_data
FROM angsuran
LEFT JOIN pinjaman ON angsuran.id_pinjaman=pinjaman.id_pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
$filter_laporan_anggota");
$total_angsuran_data = mysqli_fetch_assoc($total_angsuran_query);

$total_simpanan = (float) $total_simpanan_data['total'];
$jumlah_simpanan = (int) $total_simpanan_data['jumlah_data'];
$total_pinjaman = (float) $total_pinjaman_data['total'];
$jumlah_pinjaman = (int) $total_pinjaman_data['jumlah_data'];
$total_angsuran = (float) $total_angsuran_data['total'];
$jumlah_angsuran = (int) $total_angsuran_data['jumlah_data'];
$total_transaksi_keuangan = $total_simpanan + $total_pinjaman + $total_angsuran;
$jumlah_transaksi_keuangan = $jumlah_simpanan + $jumlah_pinjaman + $jumlah_angsuran;

$select_laporan_transaksi = "
  SELECT
    simpanan.id_simpanan AS id_transaksi,
    'Simpanan' AS sumber,
    CONCAT('Simpanan ', simpanan.jenis_simpanan) AS jenis_transaksi,
    anggota.nik,
    anggota.nama_lengkap,
    simpanan.jumlah AS nominal,
    simpanan.tgl_simpanan AS tanggal,
    users.name AS petugas,
    '-' AS status,
    '-' AS keterangan
  FROM simpanan
  LEFT JOIN anggota ON simpanan.id_anggota=anggota.id_anggota
  LEFT JOIN users ON simpanan.id_penerima=users.id_user
  $filter_laporan_anggota

  UNION ALL

  SELECT
    pinjaman.id_pinjaman AS id_transaksi,
    'Pinjaman' AS sumber,
    'Pinjaman' AS jenis_transaksi,
    anggota.nik,
    anggota.nama_lengkap,
    pinjaman.jumlah_pinjaman AS nominal,
    pinjaman.tanggal_pengajuan AS tanggal,
    users.name AS petugas,
    pinjaman.status_pinjam AS status,
    CONCAT('Tenor ', pinjaman.tenor_bulan, ' bulan, bunga ', pinjaman.bunga_persen, '%') AS keterangan
  FROM pinjaman
  LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
  LEFT JOIN users ON pinjaman.id_validator=users.id_user
  $filter_laporan_anggota

  UNION ALL

  SELECT
    angsuran.id_angsuran AS id_transaksi,
    'Angsuran' AS sumber,
    CONCAT('Angsuran ke-', angsuran.angsuran_ke) AS jenis_transaksi,
    anggota.nik,
    anggota.nama_lengkap,
    angsuran.jumlah_bayar AS nominal,
    angsuran.tgl_bayar AS tanggal,
    users.name AS petugas,
    CASE WHEN angsuran.sisa_pinjaman <= 0 THEN 'Lunas' ELSE 'Berjalan' END AS status,
    CONCAT('Sisa pinjaman Rp ', FORMAT(angsuran.sisa_pinjaman, 0)) AS keterangan
  FROM angsuran
  LEFT JOIN pinjaman ON angsuran.id_pinjaman=pinjaman.id_pinjaman
  LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
  LEFT JOIN users ON angsuran.id_penerima=users.id_user
  $filter_laporan_anggota
  ORDER BY tanggal DESC";
$views_laporan_transaksi = mysqli_query($conn, $select_laporan_transaksi);

$laporan_transaksi = [];
$chart_bulan = [];
$chart_simpanan = [];
$chart_pinjaman = [];
$chart_angsuran = [];

for ($month = 1; $month <= 12; $month++) {
  $key = date('Y') . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
  $chart_bulan[$key] = date('M', mktime(0, 0, 0, $month, 1));
  $chart_simpanan[$key] = 0;
  $chart_pinjaman[$key] = 0;
  $chart_angsuran[$key] = 0;
}

foreach ($views_laporan_transaksi as $transaksi) {
  $tanggal = $transaksi['tanggal'] ? date('Y-m-d H:i', strtotime($transaksi['tanggal'])) : '-';
  $laporan_transaksi[] = [
    'id_transaksi' => $transaksi['id_transaksi'],
    'sumber' => $transaksi['sumber'],
    'jenis_transaksi' => $transaksi['jenis_transaksi'],
    'nik' => $transaksi['nik'] ?: '-',
    'nama_lengkap' => $transaksi['nama_lengkap'] ?: '-',
    'nominal' => (float) $transaksi['nominal'],
    'tanggal' => $tanggal,
    'petugas' => $transaksi['petugas'] ?: '-',
    'status' => $transaksi['status'] ?: '-',
    'keterangan' => $transaksi['keterangan'] ?: '-'
  ];

  if ($transaksi['tanggal']) {
    $month_key = date('Y-m', strtotime($transaksi['tanggal']));
    if (isset($chart_bulan[$month_key])) {
      if ($transaksi['sumber'] == 'Simpanan') {
        $chart_simpanan[$month_key] += (float) $transaksi['nominal'];
      } else if ($transaksi['sumber'] == 'Pinjaman') {
        $chart_pinjaman[$month_key] += (float) $transaksi['nominal'];
      } else if ($transaksi['sumber'] == 'Angsuran') {
        $chart_angsuran[$month_key] += (float) $transaksi['nominal'];
      }
    }
  }
}

$total_anggota_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM anggota");
$total_anggota_data = mysqli_fetch_assoc($total_anggota_query);
$total_anggota = (int) ($total_anggota_data['total'] ?? 0);

$monitoring_kinerja_query = mysqli_query($conn, "
  SELECT
    users.id_user,
    users.name,
    users.email,
    user_role.role,
    COALESCE(simpanan_staff.jumlah_simpanan, 0) AS jumlah_simpanan,
    COALESCE(simpanan_staff.total_simpanan, 0) AS total_simpanan_staff,
    COALESCE(pinjaman_staff.jumlah_pinjaman, 0) AS jumlah_pinjaman,
    COALESCE(pinjaman_staff.total_pinjaman, 0) AS total_pinjaman_staff,
    COALESCE(angsuran_staff.jumlah_angsuran, 0) AS jumlah_angsuran,
    COALESCE(angsuran_staff.total_angsuran, 0) AS total_angsuran_staff,
    (
      COALESCE(simpanan_staff.jumlah_simpanan, 0) +
      COALESCE(pinjaman_staff.jumlah_pinjaman, 0) +
      COALESCE(angsuran_staff.jumlah_angsuran, 0)
    ) AS total_aktivitas,
    (
      COALESCE(simpanan_staff.total_simpanan, 0) +
      COALESCE(pinjaman_staff.total_pinjaman, 0) +
      COALESCE(angsuran_staff.total_angsuran, 0)
    ) AS total_nominal
  FROM users
  LEFT JOIN user_role ON users.id_role=user_role.id_role
  LEFT JOIN (
    SELECT id_penerima, COUNT(*) AS jumlah_simpanan, COALESCE(SUM(jumlah), 0) AS total_simpanan
    FROM simpanan
    GROUP BY id_penerima
  ) simpanan_staff ON simpanan_staff.id_penerima=users.id_user
  LEFT JOIN (
    SELECT id_validator, COUNT(*) AS jumlah_pinjaman, COALESCE(SUM(jumlah_pinjaman), 0) AS total_pinjaman
    FROM pinjaman
    GROUP BY id_validator
  ) pinjaman_staff ON pinjaman_staff.id_validator=users.id_user
  LEFT JOIN (
    SELECT id_penerima, COUNT(*) AS jumlah_angsuran, COALESCE(SUM(jumlah_bayar), 0) AS total_angsuran
    FROM angsuran
    GROUP BY id_penerima
  ) angsuran_staff ON angsuran_staff.id_penerima=users.id_user
  HAVING total_aktivitas > 0
  ORDER BY total_aktivitas DESC, total_nominal DESC
");

$monitoring_kinerja = [];
$monitoring_staff_labels = [];
$monitoring_staff_aktivitas = [];
$monitoring_staff_nominal = [];
$total_petugas_aktif = 0;
$total_aktivitas_petugas = 0;
$total_nominal_petugas = 0;

if ($monitoring_kinerja_query) {
  foreach ($monitoring_kinerja_query as $kinerja) {
    $total_petugas_aktif++;
    $total_aktivitas_petugas += (int) $kinerja['total_aktivitas'];
    $total_nominal_petugas += (float) $kinerja['total_nominal'];
    $monitoring_kinerja[] = $kinerja;

    if (count($monitoring_staff_labels) < 8) {
      $monitoring_staff_labels[] = $kinerja['name'] ?: 'Tanpa Nama';
      $monitoring_staff_aktivitas[] = (int) $kinerja['total_aktivitas'];
      $monitoring_staff_nominal[] = (float) $kinerja['total_nominal'];
    }
  }
}

$status_pinjaman_query = mysqli_query($conn, "
  SELECT status_pinjam, COUNT(*) AS total
  FROM pinjaman
  GROUP BY status_pinjam
  ORDER BY total DESC
");
$status_pinjaman_labels = [];
$status_pinjaman_data = [];
if ($status_pinjaman_query) {
  foreach ($status_pinjaman_query as $status_pinjaman) {
    $status_pinjaman_labels[] = $status_pinjaman['status_pinjam'] ?: 'Tanpa Status';
    $status_pinjaman_data[] = (int) $status_pinjaman['total'];
  }
}

$aktivitas_terbaru_query = mysqli_query($conn, "
  SELECT
    'Simpanan' AS kategori,
    simpanan.tgl_simpanan AS tanggal,
    anggota.nama_lengkap AS anggota,
    users.name AS petugas,
    simpanan.jumlah AS nominal,
    simpanan.jenis_simpanan AS detail
  FROM simpanan
  LEFT JOIN anggota ON simpanan.id_anggota=anggota.id_anggota
  LEFT JOIN users ON simpanan.id_penerima=users.id_user

  UNION ALL

  SELECT
    'Pinjaman' AS kategori,
    pinjaman.tanggal_pengajuan AS tanggal,
    anggota.nama_lengkap AS anggota,
    users.name AS petugas,
    pinjaman.jumlah_pinjaman AS nominal,
    CONCAT(pinjaman.status_pinjam, ' - tenor ', pinjaman.tenor_bulan, ' bulan') AS detail
  FROM pinjaman
  LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
  LEFT JOIN users ON pinjaman.id_validator=users.id_user

  UNION ALL

  SELECT
    'Angsuran' AS kategori,
    angsuran.tgl_bayar AS tanggal,
    anggota.nama_lengkap AS anggota,
    users.name AS petugas,
    angsuran.jumlah_bayar AS nominal,
    CONCAT('Angsuran ke-', angsuran.angsuran_ke) AS detail
  FROM angsuran
  LEFT JOIN pinjaman ON angsuran.id_pinjaman=pinjaman.id_pinjaman
  LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
  LEFT JOIN users ON angsuran.id_penerima=users.id_user
  ORDER BY tanggal DESC
  LIMIT 10
");

$aktivitas_terbaru = [];
if ($aktivitas_terbaru_query) {
  foreach ($aktivitas_terbaru_query as $aktivitas) {
    $aktivitas_terbaru[] = $aktivitas;
  }
}
