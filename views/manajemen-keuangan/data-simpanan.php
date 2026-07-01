<?php require_once("../../controller/manajemen-keuangan.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Data Simpanan";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content" style="height: 100vh;">

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
            <a href="add-data-simpanan" class="btn btn-primary">
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
                    <th class="text-center">Jenis Simpanan</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Tanggal Simpanan</th>
                    <th class="text-center">Penerima</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($views_simpanan as $key => $data) { ?>
                    <tr class="single-item">
                      <td class="text-center"><?= $key + 1 ?></td>
                      <td><?= $data['nik'] ?></td>
                      <td><?= $data['nama_lengkap'] ?></td>
                      <td><?= $data['jenis_simpanan'] ?></td>
                      <td class="text-end">Rp <?= number_format($data['jumlah'], 0, ',', '.') ?></td>
                      <td class="text-center">
                        <?php $tgl_simpanan = date_create($data['tgl_simpanan']);
                        echo $tgl_simpanan ? date_format($tgl_simpanan, "d-m-Y H:i") : "-"; ?>
                      </td>
                      <td><?= $data['nama_penerima'] ?></td>
                      <td>
                        <div class="hstack gap-2 justify-content-center">
                          <?php if (canAction('edit')) { ?>
                            <a href="edit-data-simpanan?p=<?= $data['id_simpanan'] ?>" class="btn btn-warning btn-sm">
                              <i class="bi bi-pencil-square"></i>
                            </a>
                          <?php } ?>
                          <?php if (canAction('delete')) { ?>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSimpanan<?= $data['id_simpanan'] ?>">
                              <i class="bi bi-trash"></i>
                            </button>
                          <?php } ?>
                        </div>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
              <?php if (mysqli_num_rows($views_simpanan) > 0) {
                mysqli_data_seek($views_simpanan, 0);
              } ?>
              <?php foreach ($views_simpanan as $data) { ?>
                <div class="modal fade" id="deleteSimpanan<?= $data['id_simpanan'] ?>" tabindex="-1" aria-labelledby="deleteSimpananLabel<?= $data['id_simpanan'] ?>" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="deleteSimpananLabel<?= $data['id_simpanan'] ?>">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form action="" method="post">
                        <div class="modal-body">
                          <input type="hidden" name="id_simpanan" value="<?= $data['id_simpanan'] ?>">
                          <input type="hidden" name="nama_lengkap" value="<?= $data['nama_lengkap'] ?>">
                          Apakah anda yakin ingin menghapus data simpanan <strong><?= $data['jenis_simpanan'] ?></strong> milik <strong><?= $data['nama_lengkap'] ?></strong> sebesar <strong>Rp <?= number_format($data['jumlah'], 0, ',', '.') ?></strong>?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <?php if (canAction('delete')) { ?>
                            <button type="submit" name="delete_simpanan" class="btn btn-danger">Hapus</button>
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

<?php require_once("../../templates/views_bottom.php") ?>
