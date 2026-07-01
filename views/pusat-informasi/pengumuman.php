<?php require_once("../../controller/pusat-informasi.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Pengumuman";
require_once("../../templates/views_top.php"); ?>

<style>
  .pengumuman-table {
    table-layout: fixed !important;
    width: 100% !important;
    min-width: 900px;
  }

  #dataTable.pengumuman-table th,
  #dataTable.pengumuman-table td {
    white-space: normal !important;
    overflow-wrap: break-word !important;
    word-break: break-word !important;
    vertical-align: middle;
    overflow: hidden;
  }

  .pengumuman-title {
    display: block;
    line-height: 1.45;
    white-space: normal !important;
    overflow-wrap: break-word !important;
    word-break: break-word !important;
  }

  .pengumuman-excerpt {
    display: inline;
    line-height: 1.45;
    white-space: normal !important;
    overflow-wrap: break-word !important;
    word-break: break-word !important;
  }

  .pengumuman-date {
    white-space: nowrap !important;
  }

  .pengumuman-more {
    border: 0;
    background: transparent;
    color: #3454d1;
    font-weight: 700;
    padding: 0 2px;
    line-height: 1;
  }

  .pengumuman-more:hover {
    text-decoration: underline;
  }
</style>

<div class="nxl-content" style="height: 100vh;">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Pusat Informasi</li>
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
            <a href="add-pengumuman" class="btn btn-primary">
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
              <table class="table table-hover pengumuman-table" id="dataTable">
                <colgroup>
                  <col style="width: 5%;">
                  <col style="width: 24%;">
                  <col style="width: 35%;">
                  <col style="width: 15%;">
                  <col style="width: 11%;">
                  <col style="width: 10%;">
                </colgroup>
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Judul</th>
                    <th class="text-center">Isi Pengumuman</th>
                    <th class="text-center">Tanggal Posting</th>
                    <th class="text-center">Author</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($views_pengumuman as $key => $data) {
                    $isi_pengumuman = strip_tags(html_entity_decode($data['isi_pengumuman']));
                    $ringkasan = strlen($isi_pengumuman) > 85 ? substr($isi_pengumuman, 0, 85) : $isi_pengumuman; ?>
                    <tr class="single-item">
                      <td class="text-center"><?= $key + 1 ?></td>
                      <td><span class="pengumuman-title"><?= $data['judul'] ?></span></td>
                      <td>
                        <span class="pengumuman-excerpt"><?= $ringkasan ?></span>
                        <?php if (strlen($isi_pengumuman) > 85) { ?>
                          <button type="button" class="pengumuman-more" data-bs-toggle="modal" data-bs-target="#detailPengumuman<?= $data['id_pengumuman'] ?>">...</button>
                        <?php } ?>
                      </td>
                      <td class="text-center pengumuman-date"><?= $data['tgl_posting'] ? date('d-m-Y H:i', strtotime($data['tgl_posting'])) : '-' ?></td>
                      <td><?= $data['nama_admin'] ?: '-' ?></td>
                      <td>
                        <div class="hstack gap-2 justify-content-center">
                          <?php if (canAction('edit')) { ?>
                            <a href="edit-pengumuman?p=<?= $data['id_pengumuman'] ?>" class="btn btn-warning btn-sm">
                              <i class="bi bi-pencil-square"></i>
                            </a>
                          <?php } ?>
                          <?php if (canAction('delete')) { ?>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deletePengumuman<?= $data['id_pengumuman'] ?>">
                              <i class="bi bi-trash"></i>
                            </button>
                          <?php } ?>
                        </div>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
              <?php if (mysqli_num_rows($views_pengumuman) > 0) {
                mysqli_data_seek($views_pengumuman, 0);
              } ?>
              <?php foreach ($views_pengumuman as $data) { ?>
                <div class="modal fade" id="detailPengumuman<?= $data['id_pengumuman'] ?>" tabindex="-1" aria-labelledby="detailPengumumanLabel<?= $data['id_pengumuman'] ?>" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="detailPengumumanLabel<?= $data['id_pengumuman'] ?>"><?= $data['judul'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <p class="mb-0" style="white-space: pre-wrap;"><?= strip_tags(html_entity_decode($data['isi_pengumuman'])) ?></p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal fade" id="deletePengumuman<?= $data['id_pengumuman'] ?>" tabindex="-1" aria-labelledby="deletePengumumanLabel<?= $data['id_pengumuman'] ?>" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="deletePengumumanLabel<?= $data['id_pengumuman'] ?>">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form action="" method="post">
                        <div class="modal-body">
                          <input type="hidden" name="id_pengumuman" value="<?= $data['id_pengumuman'] ?>">
                          <input type="hidden" name="judul" value="<?= $data['judul'] ?>">
                          Apakah anda yakin ingin menghapus pengumuman <strong><?= $data['judul'] ?></strong>?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <?php if (canAction('delete')) { ?>
                            <button type="submit" name="delete_pengumuman" class="btn btn-danger">Hapus</button>
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
