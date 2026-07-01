<?php require_once("../../controller/manajemen-keuangan.php");
if (!isset($_GET["p"])) {
  header("Location: data-angsuran");
  exit();
} else {
  $id = valid($conn, $_GET["p"]);
  $pull_data = "SELECT
    angsuran.*,
    pinjaman.jumlah_pinjaman,
    pinjaman.tenor_bulan,
    pinjaman.bunga_persen,
    pinjaman.status_pinjam,
    pinjaman.tanggal_pengajuan,
    pinjaman.tanggal_disetujui,
    anggota.nik,
    anggota.nama_lengkap,
    anggota.jenis_anggota,
    anggota.no_telp,
    anggota.alamat,
    anggota.tgl_gabung,
    users.name AS nama_penerima
  FROM angsuran
  LEFT JOIN pinjaman ON angsuran.id_pinjaman=pinjaman.id_pinjaman
  LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
  LEFT JOIN users ON angsuran.id_penerima=users.id_user
  WHERE angsuran.id_angsuran = '$id' $filter_anggota_login";
  $store_data = mysqli_query($conn, $pull_data);
  $view_data = mysqli_fetch_assoc($store_data);

  if (!$view_data) {
    header("Location: data-angsuran");
    exit();
  }

  $id_pinjaman = $view_data['id_pinjaman'];
  $select_history = "SELECT angsuran.*, users.name AS nama_penerima
  FROM angsuran
  LEFT JOIN users ON angsuran.id_penerima=users.id_user
  WHERE angsuran.id_pinjaman='$id_pinjaman'
  ORDER BY angsuran.angsuran_ke ASC, angsuran.tgl_bayar ASC";
  $views_history = mysqli_query($conn, $select_history);

  $angsuran_bulanan = $view_data['tenor_bulan'] > 0 ? ($view_data['jumlah_pinjaman'] / $view_data['tenor_bulan']) + ($view_data['jumlah_pinjaman'] * ($view_data['bunga_persen'] / 100)) : 0;
  $total_tagihan = $angsuran_bulanan * $view_data['tenor_bulan'];
  $total_bayar = 0;
  $angsuran_terakhir = 0;
  foreach ($views_history as $history_total) {
    $total_bayar += $history_total['jumlah_bayar'];
    $angsuran_terakhir = max($angsuran_terakhir, $history_total['angsuran_ke']);
  }
  $sisa_tagihan = max($total_tagihan - $total_bayar, 0);
  $progress = $total_tagihan > 0 ? min(($total_bayar / $total_tagihan) * 100, 100) : 0;
  if (mysqli_num_rows($views_history) > 0) {
    mysqli_data_seek($views_history, 0);
  }

  $_SESSION["project_ksu_st_karolus"]["name_page"] = "Detail Angsuran";
  require_once("../../templates/views_top.php"); ?>

  <style>
    .loan-summary-card {
      border: 1px solid #eef0f4;
      box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
    }

    .metric-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
    }

    .metric-box {
      border: 1px solid #eef0f4;
      border-radius: 8px;
      padding: 14px;
      background: #fbfcfe;
    }

    .metric-box small {
      color: #6c757d;
      display: block;
      margin-bottom: 6px;
    }

    .stair-timeline {
      position: relative;
      display: flex;
      flex-direction: column;
      gap: 16px;
      padding-left: 4px;
    }

    .stair-item {
      position: relative;
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      padding: 16px 18px;
      background: #fff;
      box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
      margin-left: calc(var(--step) * 22px);
    }

    .stair-item::before {
      content: "";
      position: absolute;
      left: -24px;
      top: 24px;
      width: 24px;
      height: 2px;
      background: #d7dce5;
    }

    .stair-item::after {
      content: "";
      position: absolute;
      left: -28px;
      top: 18px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #3454d1;
      box-shadow: 0 0 0 4px rgba(52, 84, 209, .14);
    }

    .stair-item.is-paid-off::after {
      background: #1cc88a;
      box-shadow: 0 0 0 4px rgba(28, 200, 138, .16);
    }

    @media (max-width: 767.98px) {
      .metric-grid {
        grid-template-columns: 1fr;
      }

      .stair-item {
        margin-left: 0;
      }

      .stair-item::before,
      .stair-item::after {
        display: none;
      }
    }
  </style>

  <div class="nxl-content" style="min-height: 100vh;">

    <!-- [ page-header ] start -->
    <div class="page-header">
      <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
          <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">Data Angsuran</li>
          <li class="breadcrumb-item"><?= $view_data['nama_lengkap'] ?></li>
        </ul>
      </div>
      <div class="page-header-right ms-auto">
        <a href="data-angsuran" class="btn btn-success">
          <i class="feather-arrow-left me-2"></i>
          <span>Kembali</span>
        </a>
      </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
      <div class="row g-4">
        <div class="col-xl-5 col-lg-6">
          <div class="card loan-summary-card stretch stretch-full">
            <div class="card-body">
              <div class="d-flex align-items-start justify-content-between mb-4">
                <div>
                  <h5 class="mb-1"><?= $view_data['nama_lengkap'] ?></h5>
                  <span class="text-muted"><?= $view_data['nik'] ?></span>
                </div>
                <?php if ($sisa_tagihan <= 0) { ?>
                  <span class="badge bg-success">Lunas</span>
                <?php } else { ?>
                  <span class="badge bg-warning text-dark">Berjalan</span>
                <?php } ?>
              </div>

              <div class="progress mb-4" style="height: 10px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $progress ?>%;" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>

              <div class="metric-grid mb-4">
                <div class="metric-box">
                  <small>Total Tagihan</small>
                  <strong>Rp <?= number_format($total_tagihan, 0, ',', '.') ?></strong>
                </div>
                <div class="metric-box">
                  <small>Total Bayar</small>
                  <strong>Rp <?= number_format($total_bayar, 0, ',', '.') ?></strong>
                </div>
                <div class="metric-box">
                  <small>Sisa Tagihan</small>
                  <strong>Rp <?= number_format($sisa_tagihan, 0, ',', '.') ?></strong>
                </div>
                <div class="metric-box">
                  <small>Angsuran</small>
                  <strong><?= $angsuran_terakhir ?> / <?= $view_data['tenor_bulan'] ?></strong>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <small class="text-muted d-block">Jumlah Pinjaman</small>
                  <strong>Rp <?= number_format($view_data['jumlah_pinjaman'], 0, ',', '.') ?></strong>
                </div>
                <div class="col-md-6">
                  <small class="text-muted d-block">Angsuran Bulanan</small>
                  <strong>Rp <?= number_format($angsuran_bulanan, 0, ',', '.') ?></strong>
                </div>
                <div class="col-md-6">
                  <small class="text-muted d-block">Tenor</small>
                  <strong><?= $view_data['tenor_bulan'] ?> Bulan</strong>
                </div>
                <div class="col-md-6">
                  <small class="text-muted d-block">Bunga</small>
                  <strong><?= $view_data['bunga_persen'] ?>%</strong>
                </div>
                <div class="col-md-6">
                  <small class="text-muted d-block">Tanggal Pengajuan</small>
                  <strong><?= $view_data['tanggal_pengajuan'] ? date('d-m-Y', strtotime($view_data['tanggal_pengajuan'])) : '-' ?></strong>
                </div>
                <div class="col-md-6">
                  <small class="text-muted d-block">Tanggal Disetujui</small>
                  <strong><?= $view_data['tanggal_disetujui'] ? date('d-m-Y', strtotime($view_data['tanggal_disetujui'])) : '-' ?></strong>
                </div>
                <div class="col-md-6">
                  <small class="text-muted d-block">Jenis Anggota</small>
                  <strong><?= $view_data['jenis_anggota'] ?: '-' ?></strong>
                </div>
                <div class="col-md-6">
                  <small class="text-muted d-block">No. Telp</small>
                  <strong><?= $view_data['no_telp'] ?: '-' ?></strong>
                </div>
                <div class="col-12">
                  <small class="text-muted d-block">Alamat</small>
                  <strong><?= $view_data['alamat'] ?: '-' ?></strong>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-7 col-lg-6">
          <div class="card loan-summary-card stretch stretch-full">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                  <h5 class="mb-1">History Angsuran</h5>
                  <span class="text-muted"><?= mysqli_num_rows($views_history) ?> transaksi pembayaran</span>
                </div>
              </div>

              <div class="stair-timeline">
                <?php foreach ($views_history as $index => $history) {
                  $step = $index % 5;
                  $is_paid_off = $history['sisa_pinjaman'] <= 0; ?>
                  <div class="stair-item <?= $is_paid_off ? 'is-paid-off' : '' ?>" style="--step: <?= $step ?>;">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                      <div>
                        <span class="badge <?= $is_paid_off ? 'bg-success' : 'bg-primary' ?> mb-2">Angsuran Ke-<?= $history['angsuran_ke'] ?></span>
                        <h6 class="mb-0"><?= $history['tgl_bayar'] ? date('d-m-Y H:i', strtotime($history['tgl_bayar'])) : '-' ?></h6>
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        <strong>Rp <?= number_format($history['jumlah_bayar'], 0, ',', '.') ?></strong>
                        <?php if (canAction('edit')) { ?>
                          <a href="edit-data-angsuran?p=<?= $history['id_angsuran'] ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i>
                          </a>
                        <?php } ?>
                        <?php if (canAction('delete')) { ?>
                          <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAngsuran<?= $history['id_angsuran'] ?>">
                            <i class="bi bi-trash"></i>
                          </button>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="row g-3">
                      <div class="col-md-6">
                        <small class="text-muted d-block">Sisa Pinjaman</small>
                        <strong>Rp <?= number_format($history['sisa_pinjaman'], 0, ',', '.') ?></strong>
                      </div>
                      <div class="col-md-6">
                        <small class="text-muted d-block">Penerima</small>
                        <strong><?= $history['nama_penerima'] ?: '-' ?></strong>
                      </div>
                    </div>
                  </div>
                  <div class="modal fade" id="deleteAngsuran<?= $history['id_angsuran'] ?>" tabindex="-1" aria-labelledby="deleteAngsuranLabel<?= $history['id_angsuran'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="deleteAngsuranLabel<?= $history['id_angsuran'] ?>">Konfirmasi Hapus</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="" method="post">
                          <div class="modal-body">
                            <input type="hidden" name="id_angsuran" value="<?= $history['id_angsuran'] ?>">
                            <input type="hidden" name="id_pinjaman" value="<?= $view_data['id_pinjaman'] ?>">
                            <input type="hidden" name="nama_lengkap" value="<?= $view_data['nama_lengkap'] ?>">
                            <input type="hidden" name="redirect_detail_angsuran" value="1">
                            Apakah anda yakin ingin menghapus data angsuran ke-<strong><?= $history['angsuran_ke'] ?></strong> milik <strong><?= $view_data['nama_lengkap'] ?></strong> sebesar <strong>Rp <?= number_format($history['jumlah_bayar'], 0, ',', '.') ?></strong>?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <?php if (canAction('delete')) { ?>
                              <button type="submit" name="delete_angsuran" class="btn btn-danger">Hapus</button>
                            <?php } ?>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- [ Main Content ] end -->

  </div>

<?php }
require_once("../../templates/views_bottom.php") ?>
