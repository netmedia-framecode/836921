<?php require_once("../../controller/pusat-informasi.php");
if (!isset($_GET["p"])) {
  header("Location: berita");
  exit();
} else {
  $id = valid($conn, $_GET["p"]);
  $pull_data = "SELECT berita.*, users.name AS nama_admin
FROM berita
LEFT JOIN users ON berita.id_admin=users.id_user
WHERE berita.id_berita = '$id'";
  $store_data = mysqli_query($conn, $pull_data);
  $view_data = mysqli_fetch_assoc($store_data);

  if (!$view_data) {
    header("Location: berita");
    exit();
  }

  $_SESSION["project_ksu_st_karolus"]["name_page"] = "Ubah Berita";
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
          <li class="breadcrumb-item"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] . ' ' . $view_data["judul"] ?></li>
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
                <input type="hidden" name="id_berita" value="<?= $view_data['id_berita'] ?>">
                <div class="mb-3">
                  <label for="judul" class="form-label">Judul Berita</label>
                  <input type="text" name="judul" class="form-control" id="judul" value="<?= $view_data['judul'] ?>" required>
                </div>
                <div class="mb-3">
                  <label for="isi_berita" class="form-label">Isi Berita</label>
                  <textarea name="isi_berita" class="form-control" id="isi_berita" rows="8" required><?= $view_data['isi_berita'] ?></textarea>
                </div>
                <div class="mb-3">
                  <label for="tgl_buat" class="form-label">Tanggal Buat</label>
                  <input type="datetime-local" name="tgl_buat" class="form-control" id="tgl_buat" value="<?= date('Y-m-d\TH:i', strtotime($view_data['tgl_buat'])) ?>" required>
                </div>
                <div class="mb-3 hstack gap-2 justify-content-left">
                  <a href="berita" class="btn btn-success">Kembali</a>
                  <?php if (canAction('edit')) { ?>
                    <button type="submit" name="edit_berita" class="btn btn-warning">Ubah</button>
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

<?php }
require_once("../../templates/views_bottom.php") ?>
