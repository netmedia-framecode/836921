<?php require_once(__DIR__ . '/../controller/pengaturan-rat.php');
$_SESSION['project_ksu_st_karolus']['name_page'] = 'Ketentuan RAT';
require_once(__DIR__ . '/../templates/views_top.php'); ?>
<div class="nxl-content"><div class="page-header"><h5>Ketentuan Rapat Anggota Tahunan</h5></div>
<div class="main-content">
  <p>Perubahan berlaku untuk pengajuan baru. Bunga adalah bunga flat total selama seluruh tenor, bukan bunga per bulan. Pengajuan lama tetap menggunakan ketentuan saat diajukan.</p>
  <?php if ($rat_error) { ?><div class="alert alert-danger"><?= htmlspecialchars($rat_error) ?></div><?php } ?>
  <div class="card"><div class="card-body">
    <form method="post">
      <input type="hidden" name="csrf_rat" value="<?= htmlspecialchars($_SESSION['csrf_rat']) ?>">
      <div class="row g-3">
        <div class="col-md-3"><label for="tahun" class="form-label">Tahun RAT</label><input id="tahun" class="form-control" type="number" name="tahun" min="2000" max="2100" value="<?= (int)$rat_aktif['tahun'] ?>" required></div>
        <div class="col-md-3"><label for="bunga" class="form-label">Bunga total (%)</label><input id="bunga" class="form-control" type="number" name="bunga_persen" min="0" max="100" step="0.01" value="<?= htmlspecialchars($rat_aktif['bunga_persen']) ?>" required></div>
        <div class="col-md-3"><label for="tenor" class="form-label">Jangka waktu (bulan)</label><input id="tenor" class="form-control" type="number" name="tenor_bulan" min="1" max="120" value="<?= (int)$rat_aktif['tenor_bulan'] ?>" required></div>
        <div class="col-md-3"><label for="pokok" class="form-label">Simpanan pokok (Rp)</label><input id="pokok" class="form-control" type="number" name="simpanan_pokok" min="0.01" max="9999999999999.99" step="0.01" value="<?= htmlspecialchars($rat_aktif['simpanan_pokok'] ?? '') ?>"><small>Kosongkan jika nominal resmi belum ditetapkan.</small></div>
      </div>
      <button name="simpan_rat" class="btn btn-primary mt-3">Simpan Ketentuan Baru</button>
    </form>
  </div></div>
  <div class="card"><div class="card-body"><h5>Riwayat Ketentuan</h5><div class="table-responsive"><table class="table">
    <thead><tr><th>Tahun RAT</th><th>Bunga total</th><th>Tenor</th><th>Simpanan pokok</th><th>Disimpan</th><th>Status</th></tr></thead>
    <tbody><?php foreach ($riwayat_rat as $rat) { ?><tr>
      <td><?= (int)$rat['tahun'] ?></td><td><?= htmlspecialchars($rat['bunga_persen']) ?>%</td><td><?= (int)$rat['tenor_bulan'] ?> bulan</td>
      <td><?= $rat['simpanan_pokok'] === null ? 'Belum ditetapkan' : 'Rp '.number_format($rat['simpanan_pokok'], 0, ',', '.') ?></td>
      <td><?= htmlspecialchars($rat['dibuat_pada']) ?></td><td><?= $rat['id_rat'] === $rat_aktif['id_rat'] ? 'Aktif' : 'Arsip' ?></td>
    </tr><?php } ?></tbody>
  </table></div></div></div>
</div></div>
<?php require_once(__DIR__ . '/../templates/views_bottom.php'); ?>
