<?php require_once("../../controller/pusat-informasi.php");
if (!isset($_GET["p"])) {
  header("Location: pengumuman");
  exit();
} else {
  $id = valid($conn, $_GET["p"]);
  $pull_data = "SELECT pengumuman.*, users.name AS nama_admin
FROM pengumuman
LEFT JOIN users ON pengumuman.id_admin=users.id_user
WHERE pengumuman.id_pengumuman = '$id'";
  $store_data = mysqli_query($conn, $pull_data);
  $view_data = mysqli_fetch_assoc($store_data);

  if (!$view_data) {
    header("Location: pengumuman");
    exit();
  }

  $_SESSION["project_ksu_st_karolus"]["name_page"] = "Ubah Pengumuman";
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
                <input type="hidden" name="id_pengumuman" value="<?= $view_data['id_pengumuman'] ?>">
                <div class="mb-3">
                  <label for="judul" class="form-label">Judul Pengumuman</label>
                  <input type="text" name="judul" class="form-control" id="judul" value="<?= $view_data['judul'] ?>" required>
                </div>
                <div class="mb-3">
                  <label for="isi_pengumuman" class="form-label">Isi Pengumuman</label>
                  <textarea name="isi_pengumuman" class="form-control" id="isi_pengumuman" rows="8" required><?= $view_data['isi_pengumuman'] ?></textarea>
                </div>
                <div class="mb-3">
                  <label for="tgl_posting" class="form-label">Tanggal Posting</label>
                  <input type="datetime-local" name="tgl_posting" class="form-control" id="tgl_posting" value="<?= date('Y-m-d\TH:i', strtotime($view_data['tgl_posting'])) ?>" required>
                </div>
                <div class="mb-3 hstack gap-2 justify-content-left">
                  <a href="pengumuman" class="btn btn-success">Kembali</a>
                  <?php if (canAction('edit')) { ?>
                    <button type="submit" name="edit_pengumuman" class="btn btn-warning">Ubah</button>
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
