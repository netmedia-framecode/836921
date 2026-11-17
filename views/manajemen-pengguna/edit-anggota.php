<?php require_once("../../controller/user-management.php");
if (!isset($_GET["p"])) {
  header("Location: anggota");
  exit();
} else {
  $id = valid($conn, $_GET["p"]);
  $pull_data = "SELECT anggota.*, users.name, users.email, users.id_role, users.id_active, user_role.role, user_status.status
FROM anggota
JOIN users ON anggota.id_user=users.id_user
JOIN user_role ON users.id_role=user_role.id_role
JOIN user_status ON users.id_active=user_status.id_status
WHERE anggota.id_anggota = '$id'";
  $store_data = mysqli_query($conn, $pull_data);
  $view_data = mysqli_fetch_assoc($store_data);

  if (!$view_data) {
    header("Location: anggota");
    exit();
  }

  $_SESSION["project_ksu_st_karolus"]["name_page"] = "Ubah Anggota";
  require_once("../../templates/views_top.php"); ?>

  <div class="nxl-content">

    <!-- [ page-header ] start -->
    <div class="page-header">
      <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
          <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">Anggota</li>
          <li class="breadcrumb-item"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] . ' ' . $view_data["nama_lengkap"] ?></li>
        </ul>
      </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
      <div class="row">
        <div class="col-lg-12">
          <div class="card stretch stretch-full">
            <div class="card-body">
              <form action="" method="post">
                <input type="hidden" name="id_anggota" value="<?= $view_data['id_anggota'] ?>">
                <input type="hidden" name="id_user" value="<?= $view_data['id_user'] ?>">
                <input type="hidden" name="id_role" value="<?= $member_role_id ?>">
                <div class="row">
                  <div class="col-lg-6">
                    <h6 class="mb-3">Data Akun</h6>
                    <div class="mb-3">
                      <label for="name" class="form-label">Nama Akun</label>
                      <input type="text" name="name" class="form-control" id="name" value="<?= $view_data['name'] ?>" minlength="3" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                      <label for="email" class="form-label">Email</label>
                      <input type="email" name="email" class="form-control" id="email" value="<?= $view_data['email'] ?>" maxlength="75" required>
                    </div>
                    <div class="mb-3">
                      <label for="password" class="form-label">Password Baru</label>
                      <input type="password" name="password" class="form-control" id="password" minlength="6" maxlength="100">
                    </div>
                    <div class="mb-3">
                      <label for="re_password" class="form-label">Konfirmasi Password Baru</label>
                      <input type="password" name="re_password" class="form-control" id="re_password" minlength="6" maxlength="100">
                    </div>
                    <div class="mb-3">
                      <label for="id_active" class="form-label">Status Akun</label>
                      <select name="id_active" class="form-control" id="id_active" required>
                        <option value="">Pilih Status</option>
                        <?php foreach ($views_user_status as $data_select_status) {
                          $selected_status = ($data_select_status['id_status'] == $view_data['id_active']) ? 'selected' : ''; ?>
                          <option value="<?= $data_select_status['id_status'] ?>" <?= $selected_status ?>>
                            <?= $data_select_status['status'] ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <h6 class="mb-3">Data Anggota</h6>
                    <div class="mb-3">
                      <label for="nik" class="form-label">NIK</label>
                      <input type="text" name="nik" class="form-control" id="nik" value="<?= $view_data['nik'] ?>" minlength="16" maxlength="20" required>
                    </div>
                    <div class="mb-3">
                      <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                      <input type="text" name="nama_lengkap" class="form-control" id="nama_lengkap" value="<?= $view_data['nama_lengkap'] ?>" minlength="3" maxlength="150" required>
                    </div>
                    <div class="mb-3">
                      <label for="jenis_anggota" class="form-label">Jenis Anggota</label>
                      <select name="jenis_anggota" class="form-control" id="jenis_anggota" required>
                        <option value="">Pilih Jenis Anggota</option>
                        <option value="Tetap" <?= $view_data['jenis_anggota'] == 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                        <option value="Tidak Tetap" <?= $view_data['jenis_anggota'] == 'Tidak Tetap' ? 'selected' : '' ?>>Tidak Tetap</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="no_telp" class="form-label">No. Telepon</label>
                      <input type="tel" name="no_telp" class="form-control" id="no_telp" value="<?= $view_data['no_telp'] ?>" maxlength="20">
                    </div>
                    <div class="mb-3">
                      <label for="tgl_gabung" class="form-label">Tanggal Gabung</label>
                      <input type="date" name="tgl_gabung" class="form-control" id="tgl_gabung" value="<?= $view_data['tgl_gabung'] ?>">
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="mb-3">
                      <label for="alamat" class="form-label">Alamat</label>
                      <textarea name="alamat" class="form-control" id="alamat" rows="3"><?= $view_data['alamat'] ?></textarea>
                    </div>
                  </div>
                </div>
                <div class="mb-3 hstack gap-2 justify-content-left">
                  <a href="anggota" class="btn btn-success">Kembali</a>
                  <?php if (canAction('edit')) { ?>
                    <button type="submit" name="edit_anggota" class="btn btn-warning">Ubah</button>
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
