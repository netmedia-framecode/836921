<?php require_once("../../controller/laporan.php");
$_SESSION["project_ksu_st_karolus"]["name_page"] = "Monitoring Kinerja";
require_once("../../templates/views_top.php");

$kategoriBadgeClass = function ($kategori) {
  if ($kategori == 'Simpanan') {
    return 'bg-primary';
  }
  if ($kategori == 'Pinjaman') {
    return 'bg-warning text-dark';
  }
  if ($kategori == 'Angsuran') {
    return 'bg-success';
  }
  return 'bg-info';
};
?>

<style>
  .performance-stat {
    border: 1px solid #edf0f5;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
  }

  .performance-icon {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    color: #fff;
    flex: 0 0 42px;
  }

  .performance-chart-card,
  .performance-table-card,
  .activity-card {
    border: 1px solid #edf0f5;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
  }

  .performance-table {
    min-width: 1080px;
  }

  .activity-list {
    display: grid;
    gap: 14px;
  }

  .activity-item {
    display: grid;
    grid-template-columns: 36px 1fr;
    gap: 12px;
    align-items: flex-start;
  }

  .activity-dot {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    color: #fff;
  }

  .activity-item:not(:last-child) .activity-body {
    padding-bottom: 14px;
    border-bottom: 1px solid #edf0f5;
  }

  .activity-meta {
    color: #7d8797;
    font-size: 12px;
  }

  .report-empty {
    min-height: 260px;
    display: grid;
    place-items: center;
    color: #8a94a6;
    text-align: center;
  }
</style>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></li>
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
            <i class="bi bi-calendar3 me-2"></i>Periode <?= date('Y') ?>
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
  <!-- [ page-header ] end -->

  <!-- [ Main Content ] start -->
  <div class="main-content">
    <div class="row g-4">
      <div class="col-xxl-3 col-md-6">
        <div class="card performance-stat stretch stretch-full">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="performance-icon bg-primary"><i class="bi bi-person-check"></i></div>
            <div>
              <span class="text-muted d-block">Petugas Terlibat</span>
              <h5 class="mb-0"><?= number_format($total_petugas_aktif, 0, ',', '.') ?></h5>
              <small>petugas dengan aktivitas transaksi</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card performance-stat stretch stretch-full">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="performance-icon bg-success"><i class="bi bi-clipboard-data"></i></div>
            <div>
              <span class="text-muted d-block">Total Aktivitas</span>
              <h5 class="mb-0"><?= number_format($total_aktivitas_petugas, 0, ',', '.') ?></h5>
              <small>simpanan, pinjaman, dan angsuran</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card performance-stat stretch stretch-full">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="performance-icon bg-warning"><i class="bi bi-cash-coin"></i></div>
            <div>
              <span class="text-muted d-block">Nominal Dikelola</span>
              <h5 class="mb-0">Rp <?= number_format($total_nominal_petugas, 0, ',', '.') ?></h5>
              <small>akumulasi transaksi petugas</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card performance-stat stretch stretch-full">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="performance-icon bg-info"><i class="bi bi-people"></i></div>
            <div>
              <span class="text-muted d-block">Total Anggota</span>
              <h5 class="mb-0"><?= number_format($total_anggota, 0, ',', '.') ?></h5>
              <small>anggota terdaftar koperasi</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-xl-8">
        <div class="card performance-chart-card stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
              <div>
                <h5 class="mb-1">Aktivitas Petugas</h5>
                <span class="text-muted">Ranking berdasarkan jumlah transaksi yang ditangani</span>
              </div>
            </div>
            <div id="staffActivityChart"></div>
            <div id="staffActivityEmpty" class="report-empty d-none">Belum ada aktivitas petugas yang bisa ditampilkan.</div>
          </div>
        </div>
      </div>
      <div class="col-xl-4">
        <div class="card performance-chart-card stretch stretch-full">
          <div class="card-body">
            <h5 class="mb-1">Status Pinjaman</h5>
            <span class="text-muted d-block mb-3">Komposisi status pinjaman anggota</span>
            <div id="loanStatusChart"></div>
            <div id="loanStatusEmpty" class="report-empty d-none">Belum ada data status pinjaman.</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-xxl-8">
        <div class="card performance-table-card stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
              <div>
                <h5 class="mb-1">Rekap Kinerja Petugas</h5>
                <span class="text-muted">Ringkasan tugas pencatatan dan validasi transaksi</span>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-hover performance-table" id="dataTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Petugas</th>
                    <th>Role</th>
                    <th>Simpanan</th>
                    <th>Pinjaman</th>
                    <th>Angsuran</th>
                    <th>Total Aktivitas</th>
                    <th>Total Nominal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($monitoring_kinerja)) { ?>
                    <?php $no = 1;
                    foreach ($monitoring_kinerja as $kinerja) { ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td>
                          <div class="fw-semibold"><?= htmlspecialchars($kinerja['name'] ?: '-') ?></div>
                          <small class="text-muted"><?= htmlspecialchars($kinerja['email'] ?: '-') ?></small>
                        </td>
                        <td><?= htmlspecialchars($kinerja['role'] ?: '-') ?></td>
                        <td>
                          <div><?= number_format($kinerja['jumlah_simpanan'], 0, ',', '.') ?> data</div>
                          <small>Rp <?= number_format($kinerja['total_simpanan_staff'], 0, ',', '.') ?></small>
                        </td>
                        <td>
                          <div><?= number_format($kinerja['jumlah_pinjaman'], 0, ',', '.') ?> data</div>
                          <small>Rp <?= number_format($kinerja['total_pinjaman_staff'], 0, ',', '.') ?></small>
                        </td>
                        <td>
                          <div><?= number_format($kinerja['jumlah_angsuran'], 0, ',', '.') ?> data</div>
                          <small>Rp <?= number_format($kinerja['total_angsuran_staff'], 0, ',', '.') ?></small>
                        </td>
                        <td>
                          <span class="badge bg-primary"><?= number_format($kinerja['total_aktivitas'], 0, ',', '.') ?> aktivitas</span>
                        </td>
                        <td>Rp <?= number_format($kinerja['total_nominal'], 0, ',', '.') ?></td>
                      </tr>
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xxl-4">
        <div class="card activity-card stretch stretch-full">
          <div class="card-body">
            <h5 class="mb-1">Aktivitas Terbaru</h5>
            <span class="text-muted d-block mb-4">Transaksi terakhir dari manajemen keuangan</span>
            <?php if (!empty($aktivitas_terbaru)) { ?>
              <div class="activity-list">
                <?php foreach ($aktivitas_terbaru as $aktivitas) {
                  $kategori = $aktivitas['kategori'] ?: '-';
                  $icon = $kategori == 'Simpanan' ? 'bi-wallet2' : ($kategori == 'Pinjaman' ? 'bi-cash-stack' : 'bi-arrow-repeat');
                  $iconBg = $kategoriBadgeClass($kategori);
                ?>
                  <div class="activity-item">
                    <div class="activity-dot <?= $iconBg ?>">
                      <i class="bi <?= $icon ?>"></i>
                    </div>
                    <div class="activity-body">
                      <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                          <div class="fw-semibold"><?= htmlspecialchars($kategori) ?> - <?= htmlspecialchars($aktivitas['anggota'] ?: '-') ?></div>
                          <div class="activity-meta"><?= htmlspecialchars($aktivitas['detail'] ?: '-') ?> oleh <?= htmlspecialchars($aktivitas['petugas'] ?: '-') ?></div>
                        </div>
                        <span class="badge <?= $iconBg ?>"><?= htmlspecialchars($kategori) ?></span>
                      </div>
                      <div class="d-flex align-items-center justify-content-between gap-2 mt-2">
                        <span class="fw-semibold">Rp <?= number_format($aktivitas['nominal'], 0, ',', '.') ?></span>
                        <span class="activity-meta"><?= $aktivitas['tanggal'] ? date('d-m-Y H:i', strtotime($aktivitas['tanggal'])) : '-' ?></span>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            <?php } else { ?>
              <div class="report-empty">Belum ada aktivitas transaksi.</div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->
</div>

<script>
  window.addEventListener('load', function() {
    const staffLabels = <?= json_encode($monitoring_staff_labels) ?>;
    const staffActivities = <?= json_encode($monitoring_staff_aktivitas) ?>;
    const staffNominals = <?= json_encode($monitoring_staff_nominal) ?>;
    const statusLabels = <?= json_encode($status_pinjaman_labels) ?>;
    const statusSeries = <?= json_encode($status_pinjaman_data) ?>;

    const formatNumber = value => new Intl.NumberFormat('id-ID').format(Number(value || 0));
    const formatRupiah = value => new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(Number(value || 0));

    if (staffLabels.length > 0) {
      new ApexCharts(document.querySelector('#staffActivityChart'), {
        chart: {
          type: 'bar',
          height: 340,
          toolbar: {
            show: false
          }
        },
        series: [{
            name: 'Aktivitas',
            type: 'column',
            data: staffActivities
          },
          {
            name: 'Nominal',
            type: 'line',
            data: staffNominals
          }
        ],
        colors: ['#3454d1', '#25b865'],
        xaxis: {
          categories: staffLabels
        },
        yaxis: [{
            title: {
              text: 'Aktivitas'
            },
            labels: {
              formatter: value => formatNumber(value)
            }
          },
          {
            opposite: true,
            title: {
              text: 'Nominal'
            },
            labels: {
              formatter: value => 'Rp ' + formatNumber(value)
            }
          }
        ],
        dataLabels: {
          enabled: false
        },
        stroke: {
          width: [0, 3],
          curve: 'smooth'
        },
        plotOptions: {
          bar: {
            borderRadius: 4,
            columnWidth: '38%'
          }
        },
        tooltip: {
          y: {
            formatter: function(value, options) {
              return options.seriesIndex === 1 ? formatRupiah(value) : formatNumber(value) + ' aktivitas';
            }
          }
        },
        legend: {
          position: 'top'
        }
      }).render();
    } else {
      document.getElementById('staffActivityChart').classList.add('d-none');
      document.getElementById('staffActivityEmpty').classList.remove('d-none');
    }

    if (statusLabels.length > 0) {
      new ApexCharts(document.querySelector('#loanStatusChart'), {
        chart: {
          type: 'donut',
          height: 320
        },
        labels: statusLabels,
        series: statusSeries,
        colors: ['#3454d1', '#25b865', '#ffa21d', '#ea4d4d', '#41b2c4'],
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
    } else {
      document.getElementById('loanStatusChart').classList.add('d-none');
      document.getElementById('loanStatusEmpty').classList.remove('d-none');
    }
  });
</script>

<?php require_once("../../templates/views_bottom.php") ?>
