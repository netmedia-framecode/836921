<?php require_once("../../controller/manajemen-keuangan.php");
if (!isset($_GET["p"])) {
  header("Location: data-simpanan");
  exit();
} else {
  $id = valid($conn, $_GET["p"]);
  $pull_data = "SELECT simpanan.*, anggota.nik, anggota.nama_lengkap, users.name AS nama_penerima
FROM simpanan
LEFT JOIN anggota ON simpanan.id_anggota=anggota.id_anggota
LEFT JOIN users ON simpanan.id_penerima=users.id_user
WHERE simpanan.id_simpanan = '$id' $filter_anggota_login";
  $store_data = mysqli_query($conn, $pull_data);
  $view_data = mysqli_fetch_assoc($store_data);

  if (!$view_data || $view_data['status_simpanan'] !== 'Pengajuan' || !canAction('edit', 'manajemen-keuangan/data-simpanan')) {
    header("Location: data-simpanan");
    exit();
  }

  $_SESSION["project_ksu_st_karolus"]["name_page"] = "Ubah Data Simpanan";
  require_once("../../templates/views_top.php"); ?>

  <div class="nxl-content">

    <!-- [ page-header ] start -->
    <div class="page-header">
      <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
          <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">Data Simpanan</li>
          <li class="breadcrumb-item"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] . ' ' . $view_data["nama_lengkap"] ?></li>
        </ul>
      </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
      <div class="row">
        <div class="col-lg-6">
          <div class="card stretch stretch-full">
            <div class="card-body">
              <form action="" method="post">
 <input type="hidden" name="csrf_simpanan" value="<?= htmlspecialchars($csrf_simpanan) ?>">
                <input type="hidden" name="id_simpanan" value="<?= $view_data['id_simpanan'] ?>">
                <div class="mb-3">
                  <label for="id_anggota" class="form-label">Anggota</label>
                  <select name="id_anggota" class="form-control" id="id_anggota" data-select2-selector="default" required>
                    <option value="">Pilih Anggota</option>
                    <?php foreach ($views_anggota_simpanan as $data_anggota) {
                      $selected_anggota = ($data_anggota['id_anggota'] == $view_data['id_anggota']) ? 'selected' : ''; ?>
                      <option value="<?= $data_anggota['id_anggota'] ?>" <?= $selected_anggota ?>>
                        <?= $data_anggota['nik'] . ' - ' . $data_anggota['nama_lengkap'] ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="jenis_simpanan" class="form-label">Jenis Simpanan</label>
                  <select name="jenis_simpanan" class="form-control" id="jenis_simpanan" required>
                    <option value="">Pilih Jenis Simpanan</option>
                    <option value="Simpanan Pokok" <?= $view_data['jenis_simpanan'] == 'Simpanan Pokok' ? 'selected' : '' ?>>Simpanan Pokok</option>
                    <option value="Simpanan Wajib" <?= $view_data['jenis_simpanan'] == 'Simpanan Wajib' ? 'selected' : '' ?>>Simpanan Wajib</option>
                    <option value="Simpanan Sukarela" <?= $view_data['jenis_simpanan'] == 'Simpanan Sukarela' ? 'selected' : '' ?>>Simpanan Sukarela</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="jumlah" class="form-label">Jumlah</label>
                  <input type="number" name="jumlah" class="form-control" id="jumlah" value="<?= $view_data['jumlah'] ?>" min="0.01" step="0.01" required>
                </div>
                <div class="mb-3">
                  <label for="tgl_simpanan" class="form-label">Tanggal Simpanan</label>
                  <input type="datetime-local" name="tgl_simpanan" class="form-control" id="tgl_simpanan" value="<?= date('Y-m-d\TH:i', strtotime($view_data['tgl_simpanan'])) ?>" required>
                </div>
                <div class="mb-3 hstack gap-2 justify-content-left">
                  <a href="data-simpanan" class="btn btn-success">Kembali</a>
                  <?php if (canAction('edit')) { ?>
                    <button type="submit" name="edit_simpanan" class="btn btn-warning">Ubah</button>
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
