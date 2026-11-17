<?php
$status_pengajuan = $jenis_pengajuan === 'simpanan' ? $pengajuan['status_simpanan'] : $pengajuan['status_pinjam'];
$verifikasi_pengajuan = $can_verify_pengajuan && $status_pengajuan === 'Pengajuan';
$acc_pengajuan = $can_review_simpanan && $status_pengajuan === 'Diverifikasi';
$review_id = 'review-'.$jenis_pengajuan.'-'.(int)$pengajuan['id_'.$jenis_pengajuan];
?>
<?php if ($review_mode === 'status') { ?>
  <?php if (!empty($pengajuan['waktu_pemeriksaan'])) { ?>
    <small class="d-block">Diperiksa: <?= htmlspecialchars($pengajuan['nama_pemeriksa'] ?? '-') ?> · <?= htmlspecialchars($pengajuan['waktu_pemeriksaan']) ?></small>
    <small class="d-block"><?= htmlspecialchars($pengajuan['catatan_pemeriksaan'] ?? '') ?></small>
  <?php } ?>
  <?php if (!empty($pengajuan['waktu_review'])) { ?>
    <small class="d-block">Keputusan kepala: <?= htmlspecialchars($pengajuan['nama_reviewer'] ?? $pengajuan['nama_validator'] ?? '-') ?> · <?= htmlspecialchars($pengajuan['waktu_review']) ?></small>
    <small class="d-block"><?= htmlspecialchars($pengajuan['catatan_review'] ?? '') ?></small>
  <?php } ?>
<?php } elseif ($review_mode === 'button' && ($verifikasi_pengajuan || $acc_pengajuan)) { ?>
  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#<?= $review_id ?>"><?= $verifikasi_pengajuan ? 'Periksa' : 'ACC / Tolak' ?></button>
<?php } elseif ($review_mode === 'modal' && ($verifikasi_pengajuan || $acc_pengajuan)) { ?>
<div class="modal fade" id="<?= $review_id ?>" tabindex="-1" aria-labelledby="<?= $review_id ?>-title" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="<?= $review_id ?>-title"><?= $verifikasi_pengajuan ? 'Pemeriksaan Kelengkapan' : 'Keputusan Kepala Koperasi' ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
    <form method="post">
      <div class="modal-body">
        <p><?= htmlspecialchars($pengajuan['nama_lengkap'] ?? '') ?> · <?= ucfirst($jenis_pengajuan) ?> #<?= (int)$pengajuan['id_'.$jenis_pengajuan] ?></p>
        <p>Rp <?= number_format($pengajuan[$jenis_pengajuan === 'simpanan' ? 'jumlah' : 'jumlah_pinjaman'], 0, ',', '.') ?></p>
        <input type="hidden" name="csrf_simpanan" value="<?= htmlspecialchars($csrf_simpanan) ?>">
        <input type="hidden" name="id_<?= $jenis_pengajuan ?>" value="<?= (int)$pengajuan['id_'.$jenis_pengajuan] ?>">
        <input type="hidden" name="review_<?= $jenis_pengajuan ?>" value="1">
        <label for="<?= $review_id ?>-catatan" class="form-label"><?= $verifikasi_pengajuan ? 'Catatan pemeriksaan kelengkapan' : 'Catatan kepala (wajib untuk penolakan)' ?></label>
        <textarea id="<?= $review_id ?>-catatan" name="catatan_review" class="form-control" maxlength="1000" rows="3"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <?php if ($verifikasi_pengajuan) { ?>
          <button name="keputusan" value="Diverifikasi" class="btn btn-primary">Kelengkapan Sesuai</button>
        <?php } else { ?>
          <button name="keputusan" value="Disetujui" class="btn btn-success">ACC Kepala</button>
          <button name="keputusan" value="Ditolak" class="btn btn-outline-danger">Tolak</button>
        <?php } ?>
      </div>
    </form>
  </div></div>
</div>
<?php } ?>
