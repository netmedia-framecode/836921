<?php
require_once("config/Base.php");

$publicWebName = (($data_utilities['name_web'] ?? '') ?: $name_website);
$publicTitle = "Layanan " . $publicWebName;
$publicDescription = "Layanan " . $publicWebName . " meliputi simpanan anggota, pengajuan pinjaman, pembayaran angsuran, laporan transaksi, dan pusat informasi koperasi.";
$publicActive = 'layanan';
require_once("sections/public_head.php");
require_once("sections/public_navbar.php");
?>

<header class="relative overflow-hidden bg-slate-950 pt-28">
  <img src="assets/img/auth/auth.png" alt="Layanan <?= htmlspecialchars($publicWebName) ?>" class="absolute inset-0 h-full w-full object-cover opacity-25">
  <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-950/85 to-blue-950/80"></div>
  <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-[1.05fr_.95fr] lg:px-8">
    <div class="text-white">
      <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-bold backdrop-blur">Layanan Koperasi</span>
      <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl">Layanan anggota yang tertata, mudah dipantau, dan aman diakses.</h1>
      <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-200">
        <?= htmlspecialchars($publicWebName) ?> membantu anggota dan pengurus mengelola aktivitas koperasi mulai dari simpanan, pinjaman, angsuran, laporan, sampai informasi resmi koperasi.
      </p>
      <div class="mt-9 flex flex-col gap-3 sm:flex-row">
        <a href="auth/" class="inline-flex items-center justify-center rounded-full bg-white px-7 py-3.5 text-sm font-extrabold text-primary shadow-xl shadow-black/20 transition hover:bg-blue-50">Masuk ke Sistem</a>
        <a href="#layanan" class="inline-flex items-center justify-center rounded-full border border-white/30 px-7 py-3.5 text-sm font-extrabold text-white transition hover:bg-white/10">Lihat Layanan</a>
      </div>
    </div>

    <div class="rounded-3xl border border-white/15 bg-white/10 p-6 text-white shadow-2xl shadow-black/20 backdrop-blur">
      <div class="rounded-2xl bg-white p-5 text-ink">
        <div class="flex items-center justify-between gap-4">
          <div>
            <div class="text-sm font-bold text-slate-500">Akses Anggota</div>
            <div class="mt-2 text-2xl font-extrabold">Portal Koperasi</div>
          </div>
          <div class="rounded-2xl bg-primary px-4 py-3 text-sm font-extrabold text-white">KSU</div>
        </div>
        <div class="mt-6 space-y-3">
          <?php
          $heroItems = [
            ['Simpanan', 'Riwayat dan pencatatan anggota'],
            ['Pinjaman', 'Pengajuan dan validasi bendahara'],
            ['Angsuran', 'Pembayaran dan sisa pinjaman'],
            ['Informasi', 'Berita, pengumuman, dan kebijakan']
          ];
          foreach ($heroItems as $item) { ?>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <div class="font-extrabold"><?= $item[0] ?></div>
                  <p class="mt-1 text-sm text-slate-500"><?= $item[1] ?></p>
                </div>
                <div class="h-3 w-3 rounded-full bg-primary"></div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</header>

<main>
  <section id="layanan" class="py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-3xl text-center">
        <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Layanan Utama</span>
        <h2 class="mt-4 text-3xl font-extrabold leading-tight text-ink sm:text-4xl">Dibuat untuk kebutuhan koperasi dari pencatatan sampai pelaporan.</h2>
        <p class="mt-5 text-base leading-8 text-slate-600">
          Setiap layanan diarahkan agar proses koperasi lebih jelas, rapi, dan mudah digunakan sesuai hak akses masing-masing pengguna.
        </p>
      </div>

      <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php
        $services = [
          [
            'title' => 'Simpanan Anggota',
            'accent' => 'bg-blue-50 text-primary',
            'icon' => '<path d="M3 7h18M5 7v12h14V7M8 11h8M8 15h5" />',
            'desc' => 'Mencatat simpanan pokok, wajib, dan sukarela agar riwayat transaksi anggota tersusun dengan baik.',
            'points' => ['Pencatatan transaksi', 'Riwayat simpanan', 'Akses sesuai anggota']
          ],
          [
            'title' => 'Pengajuan Pinjaman',
            'accent' => 'bg-amber-50 text-amber-600',
            'icon' => '<path d="M4 6h16v12H4z" /><path d="M8 10h8M8 14h5" />',
            'desc' => 'Membantu anggota mengajukan pinjaman dan memudahkan bendahara melakukan pemeriksaan.',
            'points' => ['Pengajuan anggota', 'Status pinjaman', 'Validasi bendahara']
          ],
          [
            'title' => 'Pembayaran Angsuran',
            'accent' => 'bg-emerald-50 text-emerald-600',
            'icon' => '<path d="M4 4v6h6M20 20v-6h-6" /><path d="M20 9A8 8 0 0 0 6.3 5.7L4 8m0 7a8 8 0 0 0 13.7 3.3L20 16" />',
            'desc' => 'Mencatat pembayaran angsuran, menghitung sisa pinjaman, dan menampilkan riwayat pembayaran.',
            'points' => ['Riwayat angsuran', 'Sisa pinjaman', 'Detail pembayaran']
          ],
          [
            'title' => 'Laporan Transaksi',
            'accent' => 'bg-cyan-50 text-cyan-600',
            'icon' => '<path d="M4 19V5m0 14h16M8 15v-5M12 15V8M16 15v-7" />',
            'desc' => 'Menyajikan rekap transaksi koperasi untuk membantu pemantauan dan pengambilan keputusan.',
            'points' => ['Filter laporan', 'Rekap transaksi', 'Export data']
          ],
          [
            'title' => 'Pusat Informasi',
            'accent' => 'bg-violet-50 text-violet-600',
            'icon' => '<path d="M4 5h16v14H4z" /><path d="M8 9h8M8 13h5" />',
            'desc' => 'Mengelola berita, pengumuman, dan kebijakan koperasi agar informasi resmi mudah ditemukan.',
            'points' => ['Berita koperasi', 'Pengumuman', 'Kebijakan']
          ],
          [
            'title' => 'Manajemen Pengguna',
            'accent' => 'bg-rose-50 text-rose-600',
            'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><path d="M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" />',
            'desc' => 'Mengatur data anggota, akun pengguna, role, dan hak akses fitur sesuai kebutuhan koperasi.',
            'points' => ['Data anggota', 'Role pengguna', 'Hak akses']
          ]
        ];
        foreach ($services as $service) { ?>
          <article class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/5">
            <div class="mb-6 inline-flex rounded-2xl <?= $service['accent'] ?> p-4">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><?= $service['icon'] ?></svg>
            </div>
            <h3 class="text-xl font-extrabold text-ink"><?= $service['title'] ?></h3>
            <p class="mt-3 text-sm leading-7 text-slate-600"><?= $service['desc'] ?></p>
            <div class="mt-6 space-y-3">
              <?php foreach ($service['points'] as $point) { ?>
                <div class="flex items-center gap-3 text-sm font-semibold text-slate-600">
                  <span class="h-2.5 w-2.5 rounded-full bg-primary"></span>
                  <span><?= $point ?></span>
                </div>
              <?php } ?>
            </div>
          </article>
        <?php } ?>
      </div>
    </div>
  </section>

  <section class="bg-white py-24">
    <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-[.9fr_1.1fr] lg:px-8">
      <div>
        <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Alur Layanan</span>
        <h2 class="mt-4 text-3xl font-extrabold leading-tight text-ink sm:text-4xl">Proses sederhana untuk anggota dan pengurus.</h2>
        <p class="mt-5 text-base leading-8 text-slate-600">
          Sistem dirancang agar anggota dapat mengakses data pribadinya, sementara pengurus tetap memiliki ruang kerja untuk validasi, pencatatan, dan pelaporan.
        </p>
        <a href="auth/" class="mt-8 inline-flex rounded-full bg-primary px-7 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-700">Mulai Akses</a>
      </div>

      <div class="relative">
        <div class="absolute left-6 top-6 hidden h-[calc(100%-3rem)] w-px bg-slate-200 sm:block"></div>
        <div class="space-y-5">
          <?php
          $flows = [
            ['01', 'Login sesuai hak akses', 'Anggota, bendahara, dan pengurus masuk menggunakan akun masing-masing.'],
            ['02', 'Pilih layanan yang dibutuhkan', 'Pengguna dapat membuka layanan simpanan, pinjaman, angsuran, laporan, atau informasi.'],
            ['03', 'Data diproses dan divalidasi', 'Setiap transaksi dicatat dan dikelola oleh petugas sesuai tanggung jawabnya.'],
            ['04', 'Riwayat dan laporan tersedia', 'Data yang sudah tercatat dapat dipantau kembali melalui halaman laporan atau detail transaksi.']
          ];
          foreach ($flows as $flow) { ?>
            <div class="relative rounded-3xl border border-slate-200 bg-slate-50 p-6 sm:ml-14">
              <div class="absolute -left-14 top-6 hidden h-12 w-12 items-center justify-center rounded-2xl bg-primary text-sm font-extrabold text-white sm:flex"><?= $flow[0] ?></div>
              <div class="text-sm font-extrabold text-primary sm:hidden"><?= $flow[0] ?></div>
              <h3 class="mt-2 text-lg font-extrabold text-ink sm:mt-0"><?= $flow[1] ?></h3>
              <p class="mt-2 text-sm leading-7 text-slate-600"><?= $flow[2] ?></p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </section>

  <section class="py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl bg-primary p-8 text-white lg:col-span-1">
          <span class="text-sm font-extrabold uppercase tracking-widest text-blue-100">Prinsip Layanan</span>
          <h2 class="mt-4 text-3xl font-extrabold leading-tight">Privasi anggota tetap menjadi perhatian utama.</h2>
          <p class="mt-5 text-sm leading-7 text-blue-50">
            Halaman publik hanya menampilkan informasi umum. Data transaksi dan data anggota hanya dapat diakses melalui sistem sesuai hak akses.
          </p>
        </div>
        <div class="grid gap-6 sm:grid-cols-3 lg:col-span-2">
          <?php
          $principles = [
            ['Aman', 'Informasi sensitif tidak ditampilkan di halaman publik.'],
            ['Terarah', 'Setiap menu dibuat berdasarkan kebutuhan kerja koperasi.'],
            ['Transparan', 'Anggota dapat memantau data pribadinya setelah login.']
          ];
          foreach ($principles as $principle) { ?>
            <div class="rounded-3xl border border-slate-200 bg-white p-7">
              <div class="mb-5 h-12 w-12 rounded-2xl bg-blue-50"></div>
              <h3 class="text-xl font-extrabold text-ink"><?= $principle[0] ?></h3>
              <p class="mt-3 text-sm leading-7 text-slate-600"><?= $principle[1] ?></p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </section>

  <section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl overflow-hidden rounded-[2rem] bg-slate-950 px-6 py-12 text-white shadow-2xl shadow-slate-900/20 sm:px-10 lg:flex lg:items-center lg:justify-between">
      <div class="max-w-2xl">
        <h2 class="text-3xl font-extrabold">Gunakan layanan koperasi melalui akun resmi Anda.</h2>
        <p class="mt-4 text-slate-300">Masuk untuk melihat data transaksi, status pengajuan, riwayat angsuran, laporan, dan informasi koperasi sesuai hak akses.</p>
      </div>
      <a href="auth/" class="mt-8 inline-flex rounded-full bg-white px-7 py-3.5 text-sm font-extrabold text-slate-950 shadow-lg transition hover:bg-blue-50 lg:mt-0">Masuk ke Sistem</a>
    </div>
  </section>
</main>

<?php require_once("sections/public_footer.php"); ?>
