<?php require_once("../../controller/manajemen-keuangan.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Tambah Data Pinjaman";
require_once("../../templates/views_top.php");

$anggota_login_data = null;
if ($is_anggota_login && mysqli_num_rows($views_anggota_simpanan) > 0) {
  $anggota_login_data = mysqli_fetch_assoc($views_anggota_simpanan);
  mysqli_data_seek($views_anggota_simpanan, 0);
}
?>

<div class="nxl-content" style="height: 100vh;">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Data Pinjaman</li>
        <li class="breadcrumb-item"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></li>
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
              <?php if ($is_anggota_login) { ?>
                <input type="hidden" name="id_anggota" value="<?= $anggota_login_data['id_anggota'] ?? '' ?>">
              <?php } else { ?>
                <div class="mb-3">
                  <label for="id_anggota" class="form-label">Anggota</label>
                  <select name="id_anggota" class="form-control" id="id_anggota" data-select2-selector="default" required>
                    <option value="">Pilih Anggota</option>
                    <?php foreach ($views_anggota_simpanan as $data_anggota) { ?>
                      <option value="<?= $data_anggota['id_anggota'] ?>">
                        <?= $data_anggota['nik'] . ' - ' . $data_anggota['nama_lengkap'] ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              <?php } ?>
              <div class="mb-3">
                <label for="jumlah_pinjaman" class="form-label">Jumlah Pinjaman</label>
                <input type="number" name="jumlah_pinjaman" class="form-control" id="jumlah_pinjaman" min="0" step="0.01" required>
              </div>
              <div class="mb-3">
                <label for="tenor_bulan" class="form-label">Tenor Bulan</label>
                <?php if ($is_anggota_login) { ?>
                  <select name="tenor_bulan" class="form-control" id="tenor_bulan" required>
                    <option value="">Pilih Tenor</option>
                    <option value="3">3 Bulan</option>
                    <option value="6">6 Bulan</option>
                    <option value="9">9 Bulan</option>
                    <option value="12">12 Bulan</option>
                    <option value="18">18 Bulan</option>
                    <option value="24">24 Bulan</option>
                  </select>
                <?php } else { ?>
                  <input type="number" name="tenor_bulan" class="form-control" id="tenor_bulan" min="1" required>
                <?php } ?>
              </div>
              <?php if ($is_anggota_login) { ?>
                <input type="hidden" name="bunga_persen" value="0">
                <input type="hidden" name="status_pinjam" value="Pengajuan">
                <input type="hidden" name="tanggal_pengajuan" value="<?= date('Y-m-d') ?>">
                <input type="hidden" name="tanggal_disetujui" value="">
              <?php } else { ?>
                <div class="mb-3">
                  <label for="bunga_persen" class="form-label">Bunga Persen</label>
                  <input type="number" name="bunga_persen" class="form-control" id="bunga_persen" min="0" step="0.01" required>
                </div>
                <div class="mb-3">
                  <label for="status_pinjam" class="form-label">Status Pinjam</label>
                  <select name="status_pinjam" class="form-control" id="status_pinjam" required>
                    <option value="">Pilih Status</option>
                    <option value="Pengajuan">Pengajuan</option>
                    <option value="Disetujui">Disetujui</option>
                    <option value="Ditolak">Ditolak</option>
                    <option value="Lunas">Lunas</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="tanggal_pengajuan" class="form-label">Tanggal Pengajuan</label>
                  <input type="date" name="tanggal_pengajuan" class="form-control" id="tanggal_pengajuan" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                  <label for="tanggal_disetujui" class="form-label">Tanggal Disetujui</label>
                  <input type="date" name="tanggal_disetujui" class="form-control" id="tanggal_disetujui">
                </div>
              <?php } ?>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="data-pinjaman" class="btn btn-success">Kembali</a>
                <?php if (canAction('create')) { ?>
                  <button type="submit" name="add_pinjaman" class="btn btn-primary">Tambah</button>
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
