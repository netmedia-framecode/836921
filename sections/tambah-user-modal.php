<?php if ($can_create_user) { ?>
<div class="modal fade" id="tambahUserModal" tabindex="-1" aria-labelledby="tambahUserTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="tambahUserTitle">Tambah User</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
    <form method="post" action="users" id="tambahUserForm">
      <div class="modal-body">
        <?php if ($add_user_error) { ?><div class="alert alert-danger" role="alert"><?= htmlspecialchars($add_user_error) ?></div><?php } ?>
        <input type="hidden" name="csrf_tambah_user" value="<?= htmlspecialchars($_SESSION['csrf_tambah_user']) ?>">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label" for="newUserName">Nama User</label><input class="form-control" id="newUserName" name="name" maxlength="100" autocomplete="off" value="<?= htmlspecialchars($add_user_values['name']) ?>" required></div>
          <div class="col-md-6"><label class="form-label" for="newUserEmail">Email User</label><input class="form-control" type="email" id="newUserEmail" name="email" maxlength="75" autocomplete="off" value="<?= htmlspecialchars($add_user_values['email']) ?>" required></div>
          <div class="col-md-6"><label class="form-label" for="newUserRole">Role</label><select class="form-control" id="newUserRole" name="id_role" required><option value="">Pilih Role</option>
            <?php foreach ($add_user_roles as $option) { ?><option value="<?= (int)$option['id_role'] ?>" <?= (string)$option['id_role'] === $add_user_values['id_role'] ? 'selected' : '' ?>><?= htmlspecialchars($option['role']) ?></option><?php } ?>
          </select><small class="text-muted">Pendaftaran anggota tersedia di menu Anggota.</small></div>
          <div class="col-md-6"><label class="form-label" for="newUserStatus">Status</label><select class="form-control" id="newUserStatus" name="id_active" required><option value="">Pilih Status</option>
            <?php foreach ($views_user_status as $option) { ?><option value="<?= (int)$option['id_status'] ?>" <?= (string)$option['id_status'] === $add_user_values['id_active'] ? 'selected' : '' ?>><?= htmlspecialchars($option['status']) ?></option><?php } ?>
          </select></div>
          <div class="col-md-6"><label class="form-label" for="newUserPassword">Password User Baru</label><input class="form-control" type="password" id="newUserPassword" name="password" minlength="6" maxlength="100" autocomplete="new-password" required></div>
          <div class="col-md-6"><label class="form-label" for="newUserPasswordAgain">Konfirmasi Password User Baru</label><input class="form-control" type="password" id="newUserPasswordAgain" name="re_password" minlength="6" maxlength="100" autocomplete="new-password" required></div>
        </div>
        <hr>
        <h6>Konfirmasi Keamanan</h6>
        <p class="text-muted">Masukkan password akun Anda yang sedang login untuk mengonfirmasi pembuatan user baru.</p>
        <label class="form-label" for="creatorPassword">Password Akun Anda</label>
        <input class="form-control" type="password" id="creatorPassword" name="password_pembuat" autocomplete="current-password" maxlength="100" required>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" name="add_user" value="1" class="btn btn-primary">Konfirmasi &amp; Tambah User</button></div>
    </form>
  </div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('tambahUserModal');
  modal.addEventListener('hidden.bs.modal', function () {
    modal.querySelectorAll('input[type="password"]').forEach(function (input) { input.value = ''; });
  });
  <?php if ($add_user_error) { ?>bootstrap.Modal.getOrCreateInstance(modal).show();<?php } ?>
});
</script>
<?php } ?>
