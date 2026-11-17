<input type="hidden" name="csrf_register" value="<?= htmlspecialchars($_SESSION['csrf_register']) ?>">
<?php foreach (['name'=>['Nama Akun','text',100], 'email'=>['Email','email',75], 'nama_lengkap'=>['Nama Lengkap','text',150], 'nik'=>['NIK','text',20], 'no_telp'=>['No. Telepon','tel',20]] as $field=>$detail) { ?>
<div class="mb-3">
  <label for="<?= $field ?>" class="form-label"><?= $detail[0] ?></label>
  <input id="<?= $field ?>" name="<?= $field ?>" type="<?= $detail[1] ?>" class="form-control" maxlength="<?= $detail[2] ?>" value="<?= htmlspecialchars(is_string($_POST[$field] ?? null) ? $_POST[$field] : '') ?>" <?= $field === 'nik' ? 'pattern="[0-9]{16,20}" inputmode="numeric" minlength="16"' : '' ?> required>
</div>
<?php } ?>
<div class="mb-3"><label for="alamat" class="form-label">Alamat</label><textarea id="alamat" name="alamat" class="form-control" rows="3" maxlength="2000" required><?= htmlspecialchars(is_string($_POST['alamat'] ?? null) ? $_POST['alamat'] : '') ?></textarea></div>
<div class="mb-3"><label class="form-label" for="jenis_anggota">Jenis Anggota</label><input id="jenis_anggota" class="form-control" value="Tidak Tetap" readonly><small>Berubah menjadi Tetap setelah memiliki pengajuan simpanan atau pinjaman.</small></div>
<div class="mb-3"><label class="form-label" for="tgl_gabung">Tanggal Gabung</label><input id="tgl_gabung" type="date" class="form-control" value="<?= date('Y-m-d') ?>" readonly></div>
<div class="mb-3"><label class="form-label" for="password">Password</label><input id="password" type="password" name="password" class="form-control" minlength="6" maxlength="100" autocomplete="new-password" required></div>
<div class="mb-3"><label class="form-label" for="re_password">Konfirmasi Password</label><input id="re_password" type="password" name="re_password" class="form-control" minlength="6" maxlength="100" autocomplete="new-password" required></div>
<button type="submit" name="register" class="btn btn-lg btn-primary w-100">Buat Akun</button>
