<?php require_once("../../controller/pusat-informasi.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Tambah Pengumuman";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Pengumuman</li>
        <li class="breadcrumb-item"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></li>
      </ul>
    </div>
  </div>
  <!-- [ page-header ] end -->

  <!-- [ Main Content ] start -->
  <div class="main-content">
    <div class="row">
      <div class="col-lg-8">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <form action="" method="post">
              <div class="mb-3">
                <label for="judul" class="form-label">Judul Pengumuman</label>
                <input type="text" name="judul" class="form-control" id="judul" required>
              </div>
              <div class="mb-3">
                <label for="isi_pengumuman" class="form-label">Isi Pengumuman</label>
                <textarea name="isi_pengumuman" class="form-control" id="isi_pengumuman" rows="8" required></textarea>
              </div>
              <div class="mb-3">
                <label for="tgl_posting" class="form-label">Tanggal Posting</label>
                <input type="datetime-local" name="tgl_posting" class="form-control" id="tgl_posting" value="<?= date('Y-m-d\TH:i') ?>" required>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="pengumuman" class="btn btn-success">Kembali</a>
                <?php if (canAction('create')) { ?>
                  <button type="submit" name="add_pengumuman" class="btn btn-primary">Tambah</button>
                <?php } ?>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<?php require_once("../../templates/views_bottom.php") ?>
