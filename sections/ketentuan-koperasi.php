<?php $rat_info = ratAktif($conn); ?>
<div class="card mb-4"><div class="card-body">
  <h5>Ketentuan RAT <?= (int)$rat_info['tahun'] ?></h5>
  <p>Bunga pinjaman <strong><?= htmlspecialchars($rat_info['bunga_persen']) ?>% total</strong> selama <strong><?= (int)$rat_info['tenor_bulan'] ?> bulan</strong>. Ketentuan dapat diperbarui sesuai keputusan RAT berikutnya.</p>
  <ul>
    <li>Simpanan pokok: <?= $rat_info['simpanan_pokok'] === null ? 'nominal mengikuti keputusan RAT; nilai resmi belum ditetapkan di sistem.' : 'Rp '.number_format($rat_info['simpanan_pokok'], 0, ',', '.').' sesuai RAT.' ?></li>
    <li>Simpanan wajib.</li>
    <li>Simpanan sukarela.</li>
  </ul>
  <?php if (kepalaKoperasi($role) || in_array(strtolower($role), ['administrator','admin'], true)) { ?>
    <a href="<?= htmlspecialchars($baseURL) ?>views/pengaturan-rat" class="btn btn-outline-primary btn-sm">Kelola Ketentuan RAT</a>
  <?php } ?>
</div></div>
