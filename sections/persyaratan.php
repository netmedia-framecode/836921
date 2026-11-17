<?php require_once(__DIR__ . '/../config/Koperasi.php'); ?>
<div class="row mb-4">
  <?php foreach (['simpanan' => 'Simpanan', 'pinjaman' => 'Pinjaman'] as $jenis => $judul) { ?>
  <div class="col-md-6">
    <div class="card h-100"><div class="card-body">
      <h5>Persyaratan <?= $judul ?></h5>
      <?php if ($koperasi['persyaratan_' . $jenis]) { ?>
        <ul><?php foreach ($koperasi['persyaratan_' . $jenis] as $syarat) { ?><li><?= htmlspecialchars($syarat) ?></li><?php } ?></ul>
      <?php } else { ?>
        <p class="text-muted">Persyaratan resmi sedang dikonfirmasi kepada pengurus. Silakan menghubungi kantor sebelum mengajukan <?= $jenis ?>.</p>
      <?php } ?>
      <a href="<?= htmlspecialchars($baseURL) ?>kontak" class="btn btn-outline-primary btn-sm">Kontak Koperasi</a>
    </div></div>
  </div>
  <?php } ?>
</div>
