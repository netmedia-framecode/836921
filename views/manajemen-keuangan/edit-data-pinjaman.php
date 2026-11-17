<?php require_once("../../controller/manajemen-keuangan.php");
if (!isset($_GET["p"])) {
  header("Location: data-pinjaman");
  exit();
} else {
  $id = valid($conn, $_GET["p"]);
  $pull_data = "SELECT pinjaman.*, anggota.nik, anggota.nama_lengkap, users.name AS nama_validator
FROM pinjaman
LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
LEFT JOIN users ON pinjaman.id_validator=users.id_user
WHERE pinjaman.id_pinjaman = '$id' $filter_anggota_login";
  $store_data = mysqli_query($conn, $pull_data);
  $view_data = mysqli_fetch_assoc($store_data);

  if (!$view_data) {
    header("Location: data-pinjaman");
    exit();
  }

  if ($view_data['status_pinjam'] !== 'Pengajuan' || !canAction('edit', 'manajemen-keuangan/data-pinjaman')) {
    alert("Pinjaman yang sudah disetujui tidak dapat diubah.", "warning");
    header("Location: data-pinjaman");
    exit();
  }

  $_SESSION["project_ksu_st_karolus"]["name_page"] = "Ubah Data Pinjaman";
  require_once("../../templates/views_top.php"); ?>

  <div class="nxl-content">

    <!-- [ page-header ] start -->
    <div class="page-header">
      <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
          <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">Data Pinjaman</li>
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
                <input type="hidden" name="id_pinjaman" value="<?= $view_data['id_pinjaman'] ?>">
                <?php if ($is_anggota_login) { ?>
                  <input type="hidden" name="id_anggota" value="<?= $view_data['id_anggota'] ?>">
                <?php } else { ?>
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
                <?php } ?>
                <div class="mb-3">
                  <label for="jumlah_pinjaman" class="form-label">Jumlah Pinjaman</label>
                  <input type="number" name="jumlah_pinjaman" class="form-control" id="jumlah_pinjaman" value="<?= $view_data['jumlah_pinjaman'] ?>" min="0.01" step="0.01" required>
                </div>
              <div class="alert alert-info">
                Bunga: <strong><?= htmlspecialchars($view_data['bunga_persen']) ?>% <?= ($view_data['basis_bunga'] === 'total' ? 'total selama tenor' : 'per bulan (ketentuan lama)') ?></strong>.<br>
                Jangka waktu: <strong><?= (int)$view_data['tenor_bulan'] ?> bulan</strong>.<br>

              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                  <a href="data-pinjaman" class="btn btn-success">Kembali</a>
                  <?php if (canAction('edit')) { ?>
                    <button type="submit" name="edit_pinjaman" class="btn btn-warning">Ubah</button>
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
