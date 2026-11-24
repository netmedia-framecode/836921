<?php if ($is_profile_anggota) { ?>
<h5>Biodata Anggota</h5>
<?php if ($profile_anggota_error) { ?><div class="alert alert-danger" role="alert"><?= htmlspecialchars($profile_anggota_error) ?></div><?php } ?>
<form method="post" class="mb-4">
  <input type="hidden" name="csrf_profil_anggota" value="<?= htmlspecialchars($_SESSION['csrf_profil_anggota']) ?>">
  <?php foreach (['nama_lengkap'=>'Nama Lengkap', 'nik'=>'NIK', 'no_telp'=>'No. Telepon', 'alamat'=>'Alamat'] as $field=>$label) {
    $value = $profile_anggota_error && is_string($_POST[$field] ?? null) ? $_POST[$field] : html_entity_decode((string)($profil_anggota[$field] ?? ''), ENT_QUOTES, 'UTF-8');
  ?>
    <div class="mb-3">
      <label class="form-label" for="anggota-<?= $field ?>"><?= $label ?></label>
      <?php if ($field === 'alamat') { ?>
        <textarea class="form-control" id="anggota-alamat" name="alamat" rows="3" maxlength="2000" autocomplete="street-address" required><?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?></textarea>
      <?php } else { ?>
        <input class="form-control" id="anggota-<?= $field ?>" name="<?= $field ?>" value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" required
          <?php if ($field === 'nik') { ?>type="text" inputmode="numeric" pattern="[0-9]{16,20}" minlength="16" maxlength="20" title="NIK harus berisi 16–20 digit angka"<?php }
          elseif ($field === 'no_telp') { ?>type="tel" maxlength="20" autocomplete="tel"<?php }
          else { ?>type="text" maxlength="150" autocomplete="name"<?php } ?>>
      <?php } ?>
    </div>
  <?php } ?>
  <p class="text-muted">Jenis anggota dan tanggal bergabung mengikuti data koperasi.</p>
  <button class="btn btn-primary" type="submit" name="edit_profil_anggota" value="1">Simpan Biodata Anggota</button>
</form>
<hr>
<h5>Data Akun</h5>
<?php } ?>
