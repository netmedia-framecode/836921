<?php require_once("../../controller/manajemen-keuangan.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Tambah Data Angsuran";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Data Angsuran</li>
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
              <input type="hidden" name="auto_hitung_angsuran" value="1">
              <div class="mb-3">
                <label for="id_pinjaman" class="form-label">Pinjaman Anggota</label>
                <select name="id_pinjaman" class="form-control" id="id_pinjaman" data-select2-selector="default" required>
                  <option value="">Pilih Pinjaman</option>
                  <?php foreach ($views_pinjaman_angsuran as $data_pinjaman) { ?>
                    <option value="<?= $data_pinjaman['id_pinjaman'] ?>" data-sisa="<?= $data_pinjaman['sisa_pinjaman_terakhir'] ?>" data-angsuran-terakhir="<?= $data_pinjaman['angsuran_terakhir'] ?>" data-jumlah-pinjaman="<?= $data_pinjaman['jumlah_pinjaman'] ?>" data-tenor="<?= $data_pinjaman['tenor_bulan'] ?>" data-bunga="<?= $data_pinjaman['bunga_persen'] ?>" data-basis="<?= htmlspecialchars($data_pinjaman['basis_bunga']) ?>">
                      <?= $data_pinjaman['nik'] . ' - ' . $data_pinjaman['nama_lengkap'] ?> | Tanggal <?= $data_pinjaman['tanggal_pengajuan'] ? date('d-m-Y', strtotime($data_pinjaman['tanggal_pengajuan'])) : '-' ?> | Tenor <?= $data_pinjaman['tenor_bulan'] ?> Bulan | Sisa Rp <?= number_format($data_pinjaman['sisa_pinjaman_terakhir'], 0, ',', '.') ?> | Angsuran berikutnya <?= $data_pinjaman['angsuran_terakhir'] + 1 ?> dari <?= $data_pinjaman['tenor_bulan'] ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="angsuran_ke" class="form-label">Angsuran Ke</label>
                <select name="angsuran_ke" class="form-control" id="angsuran_ke" required>
                  <option value="">Pilih Angsuran</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="jumlah_bayar" class="form-label">Jumlah Bayar</label>
                <input type="number" name="jumlah_bayar" class="form-control" id="jumlah_bayar" min="0" step="0.01" readonly required>
              </div>
              <div class="mb-3">
                <label for="sisa_pinjaman" class="form-label">Sisa Pinjaman</label>
                <input type="number" name="sisa_pinjaman" class="form-control" id="sisa_pinjaman" min="0" step="0.01" readonly required>
              </div>
              <div class="mb-3">
                <label for="tgl_bayar" class="form-label">Tanggal Bayar</label>
                <input type="datetime-local" name="tgl_bayar" class="form-control" id="tgl_bayar" value="<?= date('Y-m-d\TH:i') ?>" required>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="data-angsuran" class="btn btn-success">Kembali</a>
                <?php if (canAction('create')) { ?>
                  <button type="submit" name="add_angsuran" class="btn btn-primary">Tambah</button>
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

<script>
  window.addEventListener('load', function() {
    const pinjaman = document.getElementById('id_pinjaman');
    const angsuranKe = document.getElementById('angsuran_ke');
    const jumlahBayar = document.getElementById('jumlah_bayar');
    const sisaPinjaman = document.getElementById('sisa_pinjaman');

    if (!pinjaman || !angsuranKe || !jumlahBayar || !sisaPinjaman) {
      return;
    }

    function getSelectedPinjaman() {
      const selected = pinjaman.options[pinjaman.selectedIndex];
      return selected && selected.value ? selected : null;
    }

    function isiPilihanAngsuran() {
      const selected = getSelectedPinjaman();
      angsuranKe.innerHTML = '<option value="">Pilih Angsuran</option>';

      if (!selected) {
        jumlahBayar.value = '';
        sisaPinjaman.value = '';
        return;
      }

      const tenor = parseInt(selected.dataset.tenor || 0);
      const angsuranTerakhir = parseInt(selected.dataset.angsuranTerakhir || 0);
      const angsuranBerikutnya = angsuranTerakhir + 1;

      for (let i = angsuranBerikutnya; i <= tenor; i++) {
        const jumlahBulan = i - angsuranTerakhir;
        const option = document.createElement('option');
        option.value = i;
        option.textContent = 'Bayar sampai angsuran ke-' + i + ' (' + jumlahBulan + ' bulan)';
        angsuranKe.appendChild(option);
      }

      angsuranKe.value = angsuranBerikutnya <= tenor ? angsuranBerikutnya : '';
      hitungAngsuran();
    }

    function hitungAngsuran() {
      const selected = getSelectedPinjaman();
      if (!selected || !angsuranKe.value) {
        jumlahBayar.value = '';
        sisaPinjaman.value = '';
        return;
      }

      const sisaSebelum = parseFloat(selected.dataset.sisa || 0);
      const jumlahPinjaman = parseFloat(selected.dataset.jumlahPinjaman || 0);
      const tenor = parseInt(selected.dataset.tenor || 0);
      const bungaPersen = parseFloat(selected.dataset.bunga || 0);
      const angsuranTerakhir = parseInt(selected.dataset.angsuranTerakhir || 0);
      const angsuranDipilih = parseInt(angsuranKe.value || 0);
      const jumlahBulan = Math.max(angsuranDipilih - angsuranTerakhir, 1);
      const angsuranBulanan = tenor > 0 ? (jumlahPinjaman * (1 + bungaPersen / 100 * (selected.dataset.basis === 'total' ? 1 : tenor))) / tenor : 0;
      const bayar = angsuranDipilih >= tenor ? sisaSebelum : Math.min(Math.round(angsuranBulanan * 100) / 100 * jumlahBulan, sisaSebelum);

      jumlahBayar.value = bayar.toFixed(2);
      sisaPinjaman.value = Math.max(sisaSebelum - bayar, 0).toFixed(2);
    }

    pinjaman.addEventListener('change', isiPilihanAngsuran);
    angsuranKe.addEventListener('change', hitungAngsuran);
    if (window.jQuery) {
      window.jQuery(pinjaman).on('change select2:select', isiPilihanAngsuran);
    }

    isiPilihanAngsuran();
  });
</script>

<?php require_once("../../templates/views_bottom.php") ?>
