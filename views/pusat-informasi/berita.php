<?php require_once("../../controller/pusat-informasi.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Berita";
require_once("../../templates/views_top.php"); ?>

<style>
  .berita-table {
    table-layout: fixed !important;
    width: 100% !important;
    min-width: 900px;
  }

  #dataTable.berita-table th,
  #dataTable.berita-table td {
    white-space: normal !important;
    overflow-wrap: break-word !important;
    word-break: break-word !important;
    vertical-align: middle;
    overflow: hidden;
  }

  .berita-title {
    display: block;
    line-height: 1.45;
    white-space: normal !important;
    overflow-wrap: break-word !important;
    word-break: break-word !important;
  }

  .berita-excerpt {
    display: inline;
    line-height: 1.45;
    white-space: normal !important;
    overflow-wrap: break-word !important;
    word-break: break-word !important;
  }

  .berita-date {
    white-space: nowrap !important;
  }

  .berita-more {
    border: 0;
    background: transparent;
    color: #3454d1;
    font-weight: 700;
    padding: 0 2px;
    line-height: 1;
  }

  .berita-more:hover {
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
            <a href="add-berita" class="btn btn-primary">
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
              <table class="table table-hover berita-table" id="dataTable">
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
                    <th class="text-center">Isi Berita</th>
                    <th class="text-center">Tanggal Buat</th>
                    <th class="text-center">Author</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($views_berita as $key => $data) {
                    $isi_berita = strip_tags(html_entity_decode($data['isi_berita']));
                    $ringkasan = strlen($isi_berita) > 85 ? substr($isi_berita, 0, 85) : $isi_berita; ?>
                    <tr class="single-item">
                      <td class="text-center"><?= $key + 1 ?></td>
                      <td><span class="berita-title"><?= $data['judul'] ?></span></td>
                      <td>
                        <span class="berita-excerpt"><?= $ringkasan ?></span>
                        <?php if (strlen($isi_berita) > 85) { ?>
                          <button type="button" class="berita-more" data-bs-toggle="modal" data-bs-target="#detailBerita<?= $data['id_berita'] ?>">...</button>
                        <?php } ?>
                      </td>
                      <td class="text-center berita-date"><?= $data['tgl_buat'] ? date('d-m-Y H:i', strtotime($data['tgl_buat'])) : '-' ?></td>
                      <td><?= $data['nama_admin'] ?: '-' ?></td>
                      <td>
                        <div class="hstack gap-2 justify-content-center">
                          <?php if (canAction('edit')) { ?>
                            <a href="edit-berita?p=<?= $data['id_berita'] ?>" class="btn btn-warning btn-sm">
                              <i class="bi bi-pencil-square"></i>
                            </a>
                          <?php } ?>
                          <?php if (canAction('delete')) { ?>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteBerita<?= $data['id_berita'] ?>">
                              <i class="bi bi-trash"></i>
                            </button>
                          <?php } ?>
                        </div>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
              <?php if (mysqli_num_rows($views_berita) > 0) {
                mysqli_data_seek($views_berita, 0);
              } ?>
              <?php foreach ($views_berita as $data) { ?>
                <div class="modal fade" id="detailBerita<?= $data['id_berita'] ?>" tabindex="-1" aria-labelledby="detailBeritaLabel<?= $data['id_berita'] ?>" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="detailBeritaLabel<?= $data['id_berita'] ?>"><?= $data['judul'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <p class="mb-0" style="white-space: pre-wrap;"><?= strip_tags(html_entity_decode($data['isi_berita'])) ?></p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal fade" id="deleteBerita<?= $data['id_berita'] ?>" tabindex="-1" aria-labelledby="deleteBeritaLabel<?= $data['id_berita'] ?>" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="deleteBeritaLabel<?= $data['id_berita'] ?>">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form action="" method="post">
                        <div class="modal-body">
                          <input type="hidden" name="id_berita" value="<?= $data['id_berita'] ?>">
                          <input type="hidden" name="judul" value="<?= $data['judul'] ?>">
                          Apakah anda yakin ingin menghapus berita <strong><?= $data['judul'] ?></strong>?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <?php if (canAction('delete')) { ?>
                            <button type="submit" name="delete_berita" class="btn btn-danger">Hapus</button>
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
