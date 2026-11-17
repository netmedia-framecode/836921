<?php require_once("../../controller/pusat-informasi.php");
if (!isset($_GET["p"])) {
  header("Location: kebijakan-koperasi");
  exit();
} else {
  $id = valid($conn, $_GET["p"]);
  $pull_data = "SELECT kebijakan_koperasi.*, users.name AS nama_admin
FROM kebijakan_koperasi
LEFT JOIN users ON kebijakan_koperasi.id_admin=users.id_user
WHERE kebijakan_koperasi.id_kebijakan = '$id'";
  $store_data = mysqli_query($conn, $pull_data);
  $view_data = mysqli_fetch_assoc($store_data);

  if (!$view_data) {
    header("Location: kebijakan-koperasi");
    exit();
  }

  $_SESSION["project_ksu_st_karolus"]["name_page"] = "Ubah Kebijakan Koperasi";
  require_once("../../templates/views_top.php"); ?>

  <div class="nxl-content">

    <!-- [ page-header ] start -->
    <div class="page-header">
      <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
          <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">Kebijakan Koperasi</li>
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
                <input type="hidden" name="id_kebijakan" value="<?= $view_data['id_kebijakan'] ?>">
                <div class="mb-3">
                  <label for="judul" class="form-label">Judul Kebijakan</label>
                  <input type="text" name="judul" class="form-control" id="judul" value="<?= $view_data['judul'] ?>" required>
                </div>
                <div class="mb-3">
                  <label for="isi_kebijakan" class="form-label">Isi Kebijakan</label>
                  <textarea name="isi_kebijakan" class="form-control" id="isi_kebijakan" rows="8" required><?= $view_data['isi_kebijakan'] ?></textarea>
                </div>
                <div class="mb-3">
                  <label for="tgl_ubah" class="form-label">Tanggal Ubah</label>
                  <input type="datetime-local" name="tgl_ubah" class="form-control" id="tgl_ubah" value="<?= date('Y-m-d\TH:i', strtotime($view_data['tgl_ubah'])) ?>" required>
                </div>
                <div class="mb-3 hstack gap-2 justify-content-left">
                  <a href="kebijakan-koperasi" class="btn btn-success">Kembali</a>
                  <?php if (canAction('edit')) { ?>
                    <button type="submit" name="edit_kebijakan" class="btn btn-warning">Ubah</button>
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
