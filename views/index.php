<?php require_once("../controller/dashboard.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Dashboard";
require_once("../templates/views_top.php");

$formatRupiah = function ($value) {
  return 'Rp ' . number_format((float) $value, 0, ',', '.');
};

$badgeClass = function ($value) {
  $value = strtolower((string) $value);
  if ($value == 'disetujui' || $value == 'lunas') {
    return 'bg-success';
  }
  if ($value == 'ditolak') {
    return 'bg-danger';
  }
  if ($value == 'pengajuan') {
    return 'bg-warning text-dark';
  }
  return 'bg-primary';
};
?>

<style>
  .dashboard-stat,
  .dashboard-panel {
    border: 1px solid #edf0f5;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
  }

  .dashboard-icon {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    color: #fff;
    flex: 0 0 42px;
  }

  .dashboard-list {
    display: grid;
    gap: 14px;
  }

  .dashboard-list-item {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    padding-bottom: 14px;
    border-bottom: 1px solid #edf0f5;
  }

  .dashboard-list-item:last-child {
    padding-bottom: 0;
    border-bottom: 0;
  }

  .dashboard-empty {
    min-height: 220px;
    display: grid;
    place-items: center;
    text-align: center;
    color: #8a94a6;
  }
</style>

<div class="nxl-content">
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10">Dashboard</h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item">Dashboard</li>
      </ul>
    </div>
    <div class="page-header-right ms-auto">
      <div class="page-header-right-items">
        <div class="d-flex d-md-none">
          <a href="javascript:void(0)" class="page-header-right-close-toggle">
            <i class="feather-arrow-left me-2"></i>
            <span>Back</span>
          </a>
        </div>
        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
          <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-person-badge me-2"></i><?= htmlspecialchars($role) ?>
          </span>
          <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-calendar3 me-2"></i><?= date('Y') ?>
          </span>
        </div>
      </div>
      <div class="d-md-none d-flex align-items-center">
        <a href="javascript:void(0)" class="page-header-right-open-toggle">
          <i class="feather-align-right fs-20"></i>
        </a>
      </div>
    </div>
  </div>

  <div class="main-content">
    <?php if ($is_anggota_login) { require_once(__DIR__ . "/../sections/persyaratan.php"); } ?>
    <div class="row g-4">
      <?php if ($is_anggota_login) { ?>
        <div class="col-xxl-3 col-md-6">
          <div class="card dashboard-stat stretch stretch-full">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="dashboard-icon bg-primary"><i class="bi bi-wallet2"></i></div>
              <div>
                <span class="text-muted d-block">Simpanan Saya</span>
                <h5 class="mb-0"><?= $formatRupiah($dashboard_simpanan['total']) ?></h5>
                <small><?= number_format($dashboard_simpanan['jumlah_data'], 0, ',', '.') ?> transaksi</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6">
          <div class="card dashboard-stat stretch stretch-full">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="dashboard-icon bg-warning"><i class="bi bi-cash-stack"></i></div>
              <div>
                <span class="text-muted d-block">Sisa Pinjaman</span>
                <h5 class="mb-0"><?= $formatRupiah($dashboard_sisa_pinjaman) ?></h5>
                <small><?= number_format($dashboard_pinjaman_disetujui, 0, ',', '.') ?> pinjaman disetujui</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6">
          <div class="card dashboard-stat stretch stretch-full">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="dashboard-icon bg-info"><i class="bi bi-hourglass-split"></i></div>
              <div>
                <span class="text-muted d-block">Pengajuan Pinjaman</span>
                <h5 class="mb-0"><?= number_format($dashboard_pinjaman_pengajuan, 0, ',', '.') ?></h5>
                <small>menunggu validasi</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6">
          <div class="card dashboard-stat stretch stretch-full">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="dashboard-icon bg-success"><i class="bi bi-arrow-repeat"></i></div>
              <div>
                <span class="text-muted d-block">Angsuran Dibayar</span>
                <h5 class="mb-0"><?= $formatRupiah($dashboard_angsuran['total']) ?></h5>
                <small><?= number_format($dashboard_angsuran['jumlah_data'], 0, ',', '.') ?> pembayaran</small>
              </div>
            </div>
          </div>
        </div>
      <?php } else { ?>
        <div class="col-xxl-3 col-md-6">
          <div class="card dashboard-stat stretch stretch-full">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="dashboard-icon bg-info"><i class="bi bi-people"></i></div>
              <div>
                <span class="text-muted d-block">Total Anggota</span>
                <h5 class="mb-0"><?= number_format($dashboard_total_anggota, 0, ',', '.') ?></h5>
                <small><?= number_format($dashboard_total_pengguna, 0, ',', '.') ?> akun pengguna</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6">
          <div class="card dashboard-stat stretch stretch-full">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="dashboard-icon bg-primary"><i class="bi bi-wallet2"></i></div>
              <div>
                <span class="text-muted d-block">Total Simpanan</span>
                <h5 class="mb-0"><?= $formatRupiah($dashboard_simpanan['total']) ?></h5>
                <small><?= number_format($dashboard_simpanan['jumlah_data'], 0, ',', '.') ?> transaksi</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6">
          <div class="card dashboard-stat stretch stretch-full">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="dashboard-icon bg-warning"><i class="bi bi-cash-stack"></i></div>
              <div>
                <span class="text-muted d-block">Total Pinjaman</span>
                <h5 class="mb-0"><?= $formatRupiah($dashboard_pinjaman['total']) ?></h5>
                <small><?= number_format($dashboard_pinjaman_pengajuan, 0, ',', '.') ?> pengajuan aktif</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6">
          <div class="card dashboard-stat stretch stretch-full">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="dashboard-icon bg-success"><i class="bi bi-arrow-repeat"></i></div>
              <div>
                <span class="text-muted d-block">Total Angsuran</span>
                <h5 class="mb-0"><?= $formatRupiah($dashboard_angsuran['total']) ?></h5>
                <small><?= number_format($dashboard_angsuran['jumlah_data'], 0, ',', '.') ?> pembayaran</small>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-xl-8">
        <div class="card dashboard-panel stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
              <div>
                <h5 class="mb-1">Aktivitas Keuangan Bulanan</h5>
                <span class="text-muted"><?= $is_anggota_login ? 'Transaksi pribadi tahun ini' : 'Akumulasi transaksi koperasi tahun ini' ?></span>
              </div>
            </div>
            <div id="dashboardMonthlyChart"></div>
          </div>
        </div>
      </div>
      <div class="col-xl-4">
        <div class="card dashboard-panel stretch stretch-full">
          <div class="card-body">
            <h5 class="mb-1">Status Pinjaman</h5>
            <span class="text-muted d-block mb-3"><?= $is_anggota_login ? 'Status pinjaman saya' : 'Komposisi pinjaman anggota' ?></span>
            <?php if (!empty($dashboard_status_data)) { ?>
              <div id="dashboardLoanStatusChart"></div>
            <?php } else { ?>
              <div class="dashboard-empty">Belum ada data pinjaman.</div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="<?= $is_anggota_login ? 'col-xl-7' : 'col-12' ?>">
        <div class="card dashboard-panel stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
              <div>
                <h5 class="mb-1">Aktivitas Terbaru</h5>
                <span class="text-muted"><?= $is_anggota_login ? 'Riwayat transaksi saya' : 'Transaksi terbaru koperasi' ?></span>
              </div>
              <a href="laporan/transaksi" class="btn btn-light btn-sm">Laporan</a>
            </div>
            <?php if (!empty($dashboard_recent)) { ?>
              <div class="dashboard-list">
                <?php foreach ($dashboard_recent as $recent) { ?>
                  <div class="dashboard-list-item">
                    <div>
                      <div class="fw-semibold"><?= htmlspecialchars($recent['kategori']) ?> - <?= htmlspecialchars($recent['anggota'] ?: '-') ?></div>
                      <small class="text-muted"><?= htmlspecialchars($recent['detail'] ?: '-') ?> · <?= $recent['tanggal'] ? date('d-m-Y H:i', strtotime($recent['tanggal'])) : '-' ?></small>
                    </div>
                    <div class="text-end">
                      <div class="fw-semibold"><?= $formatRupiah($recent['nominal']) ?></div>
                      <span class="badge <?= $badgeClass($recent['detail']) ?>"><?= htmlspecialchars($recent['kategori']) ?></span>
                    </div>
                  </div>
                <?php } ?>
              </div>
            <?php } else { ?>
              <div class="dashboard-empty">Belum ada aktivitas transaksi.</div>
            <?php } ?>
          </div>
        </div>
      </div>
      <?php if ($is_anggota_login) { ?>
      <div class="col-xl-5">
        <div class="card dashboard-panel stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
              <div>
                <h5 class="mb-1">Informasi Terbaru</h5>
                <span class="text-muted">Pengumuman dan kebijakan koperasi</span>
              </div>
              <a href="pusat-informasi/kebijakan-koperasi" class="btn btn-light btn-sm">Pusat Info</a>
            </div>
            <?php if (!empty($dashboard_info)) { ?>
              <div class="dashboard-list">
                <?php foreach ($dashboard_info as $info) { ?>
                  <div class="dashboard-list-item">
                    <div>
                      <span class="badge bg-light text-dark border mb-2"><?= htmlspecialchars($info['tipe']) ?></span>
                      <div class="fw-semibold"><?= htmlspecialchars($info['judul']) ?></div>
                    </div>
                    <small class="text-muted text-nowrap"><?= $info['tanggal'] ? date('d-m-Y', strtotime($info['tanggal'])) : '-' ?></small>
                  </div>
                <?php } ?>
              </div>
            <?php } else { ?>
              <div class="dashboard-empty">Belum ada informasi terbaru.</div>
            <?php } ?>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>

<div class="d-none">
  <div id="payment-records-chart"></div>
  <div id="total-sales-color-graph"></div>
  <div id="task-completed-area-chart"></div>
  <div id="new-tasks-area-chart"></div>
  <div id="project-done-area-chart"></div>
  <div id="leads-overview-donut"></div>
</div>

<script>
  window.addEventListener('load', function() {
    const chartMonths = <?= json_encode(array_values($dashboard_chart_bulan)) ?>;
    const simpananData = <?= json_encode(array_values($dashboard_chart_simpanan)) ?>;
    const pinjamanData = <?= json_encode(array_values($dashboard_chart_pinjaman)) ?>;
    const angsuranData = <?= json_encode(array_values($dashboard_chart_angsuran)) ?>;
    const statusLabels = <?= json_encode($dashboard_status_labels) ?>;
    const statusSeries = <?= json_encode($dashboard_status_data) ?>;

    const formatNumber = value => new Intl.NumberFormat('id-ID').format(Number(value || 0));
    const formatRupiah = value => new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(Number(value || 0));

    new ApexCharts(document.querySelector('#dashboardMonthlyChart'), {
      chart: {
        type: 'area',
        height: 340,
        toolbar: {
          show: false
        }
      },
      series: [{
          name: 'Simpanan',
          data: simpananData
        },
        {
          name: 'Pinjaman',
          data: pinjamanData
        },
        {
          name: 'Angsuran',
          data: angsuranData
        }
      ],
      colors: ['#3454d1', '#ffa21d', '#25b865'],
      xaxis: {
        categories: chartMonths
      },
      yaxis: {
        labels: {
          formatter: value => 'Rp ' + formatNumber(value)
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 2
      },
      tooltip: {
        y: {
          formatter: value => formatRupiah(value)
        }
      },
      legend: {
        position: 'top'
      }
    }).render();

    if (statusSeries.length > 0) {
      new ApexCharts(document.querySelector('#dashboardLoanStatusChart'), {
        chart: {
          type: 'donut',
          height: 310
        },
        labels: statusLabels,
        series: statusSeries,
        colors: ['#ffa21d', '#25b865', '#ea4d4d', '#3454d1', '#41b2c4'],
        dataLabels: {
          enabled: true
        },
        legend: {
          position: 'bottom'
        },
        tooltip: {
          y: {
            formatter: value => formatNumber(value) + ' pinjaman'
          }
        }
      }).render();
    }
  });
</script>

<?php require_once("../templates/views_bottom.php") ?>
