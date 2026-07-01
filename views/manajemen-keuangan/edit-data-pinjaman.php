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

  if ($is_anggota_login && strtolower($view_data['status_pinjam']) == 'disetujui') {
    alert("Pinjaman yang sudah disetujui tidak dapat diubah.", "warning");
    header("Location: data-pinjaman");
    exit();
  }

  $_SESSION["project_ksu_st_karolus"]["name_page"] = "Ubah Data Pinjaman";
  require_once("../../templates/views_top.php"); ?>

  <div class="nxl-content" style="height: 100vh;">

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
                  <input type="number" name="jumlah_pinjaman" class="form-control" id="jumlah_pinjaman" value="<?= $view_data['jumlah_pinjaman'] ?>" min="0" step="0.01" required>
                </div>
                <div class="mb-3">
                  <label for="tenor_bulan" class="form-label">Tenor Bulan</label>
                  <?php if ($is_anggota_login) { ?>
                    <select name="tenor_bulan" class="form-control" id="tenor_bulan" required>
                      <option value="">Pilih Tenor</option>
                      <?php foreach ([3, 6, 9, 12, 18, 24] as $tenor) { ?>
                        <option value="<?= $tenor ?>" <?= $view_data['tenor_bulan'] == $tenor ? 'selected' : '' ?>><?= $tenor ?> Bulan</option>
                      <?php } ?>
                    </select>
                  <?php } else { ?>
                    <input type="number" name="tenor_bulan" class="form-control" id="tenor_bulan" value="<?= $view_data['tenor_bulan'] ?>" min="1" required>
                  <?php } ?>
                </div>
                <?php if ($is_anggota_login) { ?>
                  <input type="hidden" name="bunga_persen" value="<?= $view_data['bunga_persen'] ?>">
                  <input type="hidden" name="status_pinjam" value="<?= $view_data['status_pinjam'] ?>">
                  <input type="hidden" name="tanggal_pengajuan" value="<?= $view_data['tanggal_pengajuan'] ?>">
                  <input type="hidden" name="tanggal_disetujui" value="<?= $view_data['tanggal_disetujui'] ?>">
                <?php } else { ?>
                  <div class="mb-3">
                    <label for="bunga_persen" class="form-label">Bunga Persen</label>
                    <input type="number" name="bunga_persen" class="form-control" id="bunga_persen" value="<?= $view_data['bunga_persen'] ?>" min="0" step="0.01" required>
                  </div>
                  <div class="mb-3">
                    <label for="status_pinjam" class="form-label">Status Pinjam</label>
                    <select name="status_pinjam" class="form-control" id="status_pinjam" required>
                      <option value="">Pilih Status</option>
                      <option value="Pengajuan" <?= $view_data['status_pinjam'] == 'Pengajuan' ? 'selected' : '' ?>>Pengajuan</option>
                      <option value="Disetujui" <?= $view_data['status_pinjam'] == 'Disetujui' ? 'selected' : '' ?>>Disetujui</option>
                      <option value="Ditolak" <?= $view_data['status_pinjam'] == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                      <option value="Lunas" <?= $view_data['status_pinjam'] == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="tanggal_pengajuan" class="form-label">Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_pengajuan" class="form-control" id="tanggal_pengajuan" value="<?= $view_data['tanggal_pengajuan'] ?>" required>
                  </div>
                  <div class="mb-3">
                    <label for="tanggal_disetujui" class="form-label">Tanggal Disetujui</label>
                    <input type="date" name="tanggal_disetujui" class="form-control" id="tanggal_disetujui" value="<?= $view_data['tanggal_disetujui'] ?>">
                  </div>
                <?php } ?>
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
