<?php require_once("../../controller/manajemen-keuangan.php");
if (!isset($_GET["p"])) {
  header("Location: data-angsuran");
  exit();
} else {
  $id = valid($conn, $_GET["p"]);
  $pull_data = "SELECT
    angsuran.*,
    pinjaman.jumlah_pinjaman,
    pinjaman.tenor_bulan,
    pinjaman.bunga_persen,
    pinjaman.basis_bunga,
    pinjaman.tanggal_pengajuan,
    anggota.nik,
    anggota.nama_lengkap
  FROM angsuran
  LEFT JOIN pinjaman ON angsuran.id_pinjaman=pinjaman.id_pinjaman
  LEFT JOIN anggota ON pinjaman.id_anggota=anggota.id_anggota
  WHERE angsuran.id_angsuran = '$id' $filter_anggota_login";
  $store_data = mysqli_query($conn, $pull_data);
  $view_data = mysqli_fetch_assoc($store_data);

  if (!$view_data) {
    header("Location: data-angsuran");
    exit();
  }

  $id_pinjaman = $view_data['id_pinjaman'];
  $history_before_query = mysqli_query($conn, "SELECT COALESCE(MAX(angsuran_ke), 0) AS angsuran_sebelumnya, COALESCE(SUM(jumlah_bayar), 0) AS total_bayar_sebelumnya FROM angsuran WHERE id_pinjaman='$id_pinjaman' AND id_angsuran != '$view_data[id_angsuran]'");
  $history_before = mysqli_fetch_assoc($history_before_query);
  $angsuran_sebelumnya = $history_before ? (int) $history_before['angsuran_sebelumnya'] : 0;
  $total_bayar_sebelumnya = $history_before ? (float) $history_before['total_bayar_sebelumnya'] : 0;
  $angsuran_bulanan = $view_data['tenor_bulan'] > 0 ? totalTagihanPinjaman($view_data['jumlah_pinjaman'], $view_data['bunga_persen'], $view_data['tenor_bulan'], $view_data['basis_bunga']) / $view_data['tenor_bulan'] : 0;
  $total_tagihan = $angsuran_bulanan * $view_data['tenor_bulan'];
  $sisa_sebelum_bayar = max($total_tagihan - $total_bayar_sebelumnya, 0);

  $_SESSION["project_ksu_st_karolus"]["name_page"] = "Ubah Data Angsuran";
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
          <li class="breadcrumb-item"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] . ' ' . $view_data["nama_lengkap"] ?></li>
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
                <input type="hidden" name="id_angsuran" value="<?= $view_data['id_angsuran'] ?>">
                <input type="hidden" name="auto_hitung_angsuran" value="1">
                <div class="mb-3">
                  <label for="id_pinjaman" class="form-label">Pinjaman Anggota</label>
                  <select name="id_pinjaman" class="form-control" id="id_pinjaman" data-select2-selector="default" required>
                    <option value="<?= $view_data['id_pinjaman'] ?>" data-sisa="<?= $sisa_sebelum_bayar ?>" data-angsuran-terakhir="<?= $angsuran_sebelumnya ?>" data-jumlah-pinjaman="<?= $view_data['jumlah_pinjaman'] ?>" data-tenor="<?= $view_data['tenor_bulan'] ?>" data-bunga="<?= $view_data['bunga_persen'] ?>" data-basis="<?= htmlspecialchars($view_data['basis_bunga']) ?>" selected>
                      <?= $view_data['nik'] . ' - ' . $view_data['nama_lengkap'] ?> | Tanggal <?= $view_data['tanggal_pengajuan'] ? date('d-m-Y', strtotime($view_data['tanggal_pengajuan'])) : '-' ?> | Tenor <?= $view_data['tenor_bulan'] ?> Bulan | Sisa Sebelum Bayar Rp <?= number_format($sisa_sebelum_bayar, 0, ',', '.') ?>
                    </option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="angsuran_ke" class="form-label">Angsuran Ke</label>
                  <select name="angsuran_ke" class="form-control" id="angsuran_ke" data-selected="<?= $view_data['angsuran_ke'] ?>" required>
                    <option value="">Pilih Angsuran</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="jumlah_bayar" class="form-label">Jumlah Bayar</label>
                  <input type="number" name="jumlah_bayar" class="form-control" id="jumlah_bayar" value="<?= $view_data['jumlah_bayar'] ?>" min="0" step="0.01" readonly required>
                </div>
                <div class="mb-3">
                  <label for="sisa_pinjaman" class="form-label">Sisa Pinjaman</label>
                  <input type="number" name="sisa_pinjaman" class="form-control" id="sisa_pinjaman" value="<?= $view_data['sisa_pinjaman'] ?>" min="0" step="0.01" readonly required>
                </div>
                <div class="mb-3">
                  <label for="tgl_bayar" class="form-label">Tanggal Bayar</label>
                  <input type="datetime-local" name="tgl_bayar" class="form-control" id="tgl_bayar" value="<?= date('Y-m-d\TH:i', strtotime($view_data['tgl_bayar'])) ?>" required>
                </div>
                <div class="mb-3 hstack gap-2 justify-content-left">
                  <a href="detail-angsuran?p=<?= $view_data['id_angsuran'] ?>" class="btn btn-success">Kembali</a>
                  <?php if (canAction('edit')) { ?>
                    <button type="submit" name="edit_angsuran" class="btn btn-warning">Ubah</button>
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
        const selectedAngsuran = parseInt(angsuranKe.dataset.selected || 0);
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

        angsuranKe.value = selectedAngsuran >= angsuranBerikutnya && selectedAngsuran <= tenor ? selectedAngsuran : angsuranBerikutnya;
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

<?php }
require_once("../../templates/views_bottom.php") ?>
