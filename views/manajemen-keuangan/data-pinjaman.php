<?php require_once("../../controller/manajemen-keuangan.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Data Pinjaman";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Manajemen Keuangan</li>
        <li class="breadcrumb-item"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></li>
      </ul>
    </div>
    <div class="page-header-right ms-auto">
      <div class="page-header-right-items">
        <div class="d-flex d-md-none">
          <a href="javascript:void(0)" class="page-header-right-close-toggle">
            <i class="feather-arrow-left me-2"></i>
            <span>Back</span>
          </a>
        </div>
        <?php if (canAction('create')) { ?>
          <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
            <a href="add-data-pinjaman" class="btn btn-primary">
              <i class="feather-plus me-2"></i>
              <span>Tambah</span>
            </a>
          </div>
        <?php } ?>
      </div>
      <div class="d-md-none d-flex align-items-center">
        <a href="javascript:void(0)" class="page-header-right-open-toggle">
          <i class="feather-align-right fs-20"></i>
        </a>
      </div>
    </div>
  </div>
  <!-- [ page-header ] end -->

          <!-- [ Main Content ] start -->
          <div class="main-content">
            <div class="row">
              <div class="col-lg-12">
                <div class="card stretch stretch-full">
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-hover" id="dataTable">
                        <thead>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">NIK</th>
                            <th class="text-center">Nama Anggota</th>
                            <th class="text-center">Jumlah Pinjaman</th>
                            <th class="text-center">Tenor</th>
                            <th class="text-center">Bunga</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Tanggal Pengajuan</th>
                            <th class="text-center">Tanggal Disetujui</th>
                            <th class="text-center">Validator</th>
                            <th class="text-center" data-orderable="false" data-searchable="false">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($views_pinjaman as $key => $data) { ?>
                            <?php $can_edit_pinjaman = canAction('edit') && $data['status_pinjam'] === 'Pengajuan'; ?>
                            <tr class="single-item">
                              <td class="text-center"><?= $key + 1 ?></td>
                              <td><?= $data['nik'] ?></td>
                              <td><?= $data['nama_lengkap'] ?></td>
                              <td class="text-end">Rp <?= number_format($data['jumlah_pinjaman'], 0, ',', '.') ?></td>
                              <td class="text-center"><?= $data['tenor_bulan'] ?> Bulan</td>
                              <td class="text-center"><?= $data['bunga_persen'] ?>% <?= $data['basis_bunga'] === 'total' ? 'total' : '/ bulan (lama)' ?></td>
                              <td class="review-status"><?= htmlspecialchars($data['status_pinjam']) ?>
                                <?php $jenis_pengajuan = 'pinjaman'; $pengajuan = $data; $review_mode = 'status'; require(__DIR__ . '/../../sections/review-pengajuan.php'); ?>
                              </td>
                              <td class="text-center"><?= $data['tanggal_pengajuan'] ? date('d-m-Y', strtotime($data['tanggal_pengajuan'])) : '-' ?></td>
                              <td class="text-center"><?= $data['tanggal_disetujui'] ? date('d-m-Y', strtotime($data['tanggal_disetujui'])) : '-' ?></td>
                              <td><?= $data['nama_validator'] ?></td>
                              <td>
                                <div class="d-flex flex-wrap gap-2 justify-content-center financial-actions">
                          <?php $review_mode = 'button'; require(__DIR__ . '/../../sections/review-pengajuan.php'); ?>
                          <?php if (!$verifikasi_pengajuan && !$acc_pengajuan && !($data['status_pinjam'] === 'Pengajuan' && (canAction('edit') || canAction('delete')))) { ?><span class="text-muted">—</span><?php } ?>
                                  <?php if ($can_edit_pinjaman) { ?>
                                    <a href="edit-data-pinjaman?p=<?= $data['id_pinjaman'] ?>" class="btn btn-warning btn-sm">
                                      <i class="bi bi-pencil-square" aria-hidden="true"></i> Ubah
                                    </a>
                                  <?php } ?>
                                  <?php if (canAction('delete') && $data['status_pinjam'] === 'Pengajuan') { ?>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deletePinjaman<?= $data['id_pinjaman'] ?>">
                                      <i class="bi bi-trash" aria-hidden="true"></i> Hapus
                                    </button>
                                  <?php } ?>
                                </div>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                      <?php if (mysqli_num_rows($views_pinjaman) > 0) {
                        mysqli_data_seek($views_pinjaman, 0);
                      } ?>
                      <?php foreach ($views_pinjaman as $data) { ?>
                        <div class="modal fade" id="deletePinjaman<?= $data['id_pinjaman'] ?>" tabindex="-1" aria-labelledby="deletePinjamanLabel<?= $data['id_pinjaman'] ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="deletePinjamanLabel<?= $data['id_pinjaman'] ?>">Konfirmasi Hapus</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <form action="" method="post">
 <input type="hidden" name="csrf_simpanan" value="<?= htmlspecialchars($csrf_simpanan) ?>">
                                <div class="modal-body">
                                  <input type="hidden" name="id_pinjaman" value="<?= $data['id_pinjaman'] ?>">
                                  <input type="hidden" name="nama_lengkap" value="<?= $data['nama_lengkap'] ?>">
                                  Apakah anda yakin ingin menghapus data pinjaman milik <strong><?= $data['nama_lengkap'] ?></strong> sebesar <strong>Rp <?= number_format($data['jumlah_pinjaman'], 0, ',', '.') ?></strong>?
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                  <?php if (canAction('delete') && $data['status_pinjam'] === 'Pengajuan') { ?>
                                    <button type="submit" name="delete_pinjaman" class="btn btn-danger">Hapus</button>
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

<?php
if (mysqli_num_rows($views_pinjaman)) mysqli_data_seek($views_pinjaman, 0);
foreach ($views_pinjaman as $pengajuan) {
  $jenis_pengajuan = 'pinjaman'; $review_mode = 'modal';
  require(__DIR__ . '/../../sections/review-pengajuan.php');
}
?>
<?php require_once("../../templates/views_bottom.php") ?>
