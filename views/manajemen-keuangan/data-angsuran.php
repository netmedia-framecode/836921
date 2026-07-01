<?php require_once("../../controller/manajemen-keuangan.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Data Angsuran";
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
            <a href="add-data-angsuran" class="btn btn-primary">
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
                    <th class="text-center">Nama Anggota</th>
                    <th class="text-center">Angsuran Ke</th>
                    <th class="text-center">Jumlah Bayar</th>
                    <th class="text-center">Sisa Pinjaman</th>
                    <th class="text-center">Tanggal Bayar</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($views_angsuran as $key => $data) { ?>
                    <tr class="single-item">
                      <td class="text-center"><?= $key + 1 ?></td>
                      <td><?= $data['nama_lengkap'] ?></td>
                      <td class="text-center"><?= $data['angsuran_ke'] ?></td>
                      <td class="text-end">Rp <?= number_format($data['jumlah_bayar'], 0, ',', '.') ?></td>
                      <td class="text-end">Rp <?= number_format($data['sisa_pinjaman'], 0, ',', '.') ?></td>
                      <td class="text-center"><?= $data['tgl_bayar'] ? date('d-m-Y H:i', strtotime($data['tgl_bayar'])) : '-' ?></td>
                      <td class="text-center">
                        <?php if ($data['sisa_pinjaman'] <= 0) { ?>
                          <span class="badge bg-success">Lunas</span>
                        <?php } else { ?>
                          <span class="badge bg-warning text-dark">Berjalan</span>
                        <?php } ?>
                      </td>
                      <td>
                        <div class="hstack gap-2 justify-content-center">
                          <a href="detail-angsuran?p=<?= $data['id_angsuran'] ?>" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<?php require_once("../../templates/views_bottom.php") ?>
