<?php require_once("../../controller/manajemen-keuangan.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Tambah Data Simpanan";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content" style="height: 100vh;">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Data Simpanan</li>
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
              <div class="mb-3">
                <label for="jenis_simpanan" class="form-label">Jenis Simpanan</label>
                <select name="jenis_simpanan" class="form-control" id="jenis_simpanan" required>
                  <option value="">Pilih Jenis Simpanan</option>
                  <option value="Simpanan Pokok">Simpanan Pokok</option>
                  <option value="Simpanan Wajib">Simpanan Wajib</option>
                  <option value="Simpanan Sukarela">Simpanan Sukarela</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah</label>
                <input type="number" name="jumlah" class="form-control" id="jumlah" min="0" step="0.01" required>
              </div>
              <div class="mb-3">
                <label for="tgl_simpanan" class="form-label">Tanggal Simpanan</label>
                <input type="datetime-local" name="tgl_simpanan" class="form-control" id="tgl_simpanan" value="<?= date('Y-m-d\TH:i') ?>" required>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="data-simpanan" class="btn btn-success">Kembali</a>
                <?php if (canAction('create')) { ?>
                  <button type="submit" name="add_simpanan" class="btn btn-primary">Tambah</button>
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
