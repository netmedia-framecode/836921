<?php require_once("../../controller/manajemen-keuangan.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Tambah Data Pinjaman";
require_once("../../templates/views_top.php");

?>

<div class="nxl-content">

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
    <?php require_once(__DIR__ . "/../../sections/persyaratan.php"); ?>
    <div class="row">
      <div class="col-lg-6">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <form action="" method="post">
 <input type="hidden" name="rat_ditampilkan" value="<?= (int)$rat_aktif['id_rat'] ?>">
 <input type="hidden" name="csrf_simpanan" value="<?= htmlspecialchars($csrf_simpanan) ?>">
              <?php if (!$is_anggota_login) { ?>
                <div class="mb-3">
                  <label for="id_anggota" class="form-label">Anggota</label>
                  <select name="id_anggota" class="form-control" id="id_anggota" data-select2-selector="default" required>
                    <option value="">Pilih Anggota</option>
                    <?php foreach ($views_pemilik_simpanan as $pemilik) { ?>
                      <option value="<?= htmlspecialchars($pemilik['pilihan']) ?>"><?= htmlspecialchars($pemilik['label']) ?></option>
                    <?php } ?>
                  </select>
                </div>
              <?php } ?>
              <div class="mb-3">
                <label for="jumlah_pinjaman" class="form-label">Jumlah Pinjaman</label>
                <input type="number" name="jumlah_pinjaman" class="form-control" id="jumlah_pinjaman" min="0.01" step="0.01" required>
              </div>
              <div class="alert alert-info">
                Bunga: <strong><?= htmlspecialchars($rat_aktif['bunga_persen']) ?>% <?= 'total selama tenor' ?></strong>.<br>
                Jangka waktu: <strong><?= (int)$rat_aktif['tenor_bulan'] ?> bulan</strong>.<br>

              </div>
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
