<?php require_once("../../controller/pusat-informasi.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Tambah Berita";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content" style="height: 100vh;">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Berita</li>
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
                <label for="judul" class="form-label">Judul Berita</label>
                <input type="text" name="judul" class="form-control" id="judul" required>
              </div>
              <div class="mb-3">
                <label for="isi_berita" class="form-label">Isi Berita</label>
                <textarea name="isi_berita" class="form-control" id="isi_berita" rows="8" required></textarea>
              </div>
              <div class="mb-3">
                <label for="tgl_buat" class="form-label">Tanggal Buat</label>
                <input type="datetime-local" name="tgl_buat" class="form-control" id="tgl_buat" value="<?= date('Y-m-d\TH:i') ?>" required>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="berita" class="btn btn-success">Kembali</a>
                <?php if (canAction('create')) { ?>
                  <button type="submit" name="add_berita" class="btn btn-primary">Tambah</button>
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
