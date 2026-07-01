<?php
require_once("config/Base.php");

$infoQuery = mysqli_query($conn, "
  SELECT 'Berita' AS tipe, judul, tgl_buat AS tanggal FROM berita
  UNION ALL
  SELECT 'Pengumuman' AS tipe, judul, tgl_posting AS tanggal FROM pengumuman
  ORDER BY tanggal DESC
  LIMIT 3
");
$latestInfo = [];
if ($infoQuery) {
  foreach ($infoQuery as $info) {
    $latestInfo[] = $info;
  }
}
  
$publicTitle = (($data_utilities['name_web'] ?? '') ?: $name_website) . " - Koperasi Serba Usaha";
$publicDescription = ($data_utilities['description'] ?? '') ?: "KSU St. Karolus membantu anggota mengelola simpanan, pinjaman, angsuran, dan informasi koperasi secara transparan.";
$publicActive = 'beranda';
require_once("sections/public_head.php");
require_once("sections/public_navbar.php");
?>

  <header class="relative min-h-[88vh] overflow-hidden pt-20">
    <img src="assets/img/auth/auth.png" alt="Layanan digital koperasi KSU St. Karolus" class="absolute inset-0 h-full w-full object-cover">
    <div class="absolute inset-0 bg-slate-950/75"></div>
    <div class="absolute inset-x-0 bottom-0 h-36 bg-gradient-to-t from-slate-50 to-transparent"></div>

    <div class="relative mx-auto grid min-h-[calc(88vh-5rem)] max-w-7xl items-center px-4 py-16 sm:px-6 lg:px-8">
      <div class="max-w-3xl text-white">
        <div class="mb-5 inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur">
          Transparan, teratur, dan dekat dengan anggota
        </div>
        <h1 class="text-4xl font-extrabold leading-tight sm:text-6xl"><?= htmlspecialchars($publicWebName) ?></h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-100">
          Koperasi serba usaha yang membantu anggota mengelola simpanan, mengajukan pinjaman, memantau angsuran, dan mengikuti informasi koperasi dalam satu sistem yang mudah diakses.
        </p>
        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
          <a href="auth/" class="inline-flex items-center justify-center rounded-full bg-white px-7 py-3.5 text-sm font-extrabold text-primary shadow-xl shadow-black/20 transition hover:bg-blue-50">Masuk ke Sistem</a>
          <a href="#tentang" class="inline-flex items-center justify-center rounded-full border border-white/30 px-7 py-3.5 text-sm font-extrabold text-white transition hover:bg-white/10">Pelajari Koperasi</a>
        </div>
      </div>
    </div>
  </header>

  <main>
    <section class="relative -mt-14 px-4 sm:px-6 lg:px-8">
      <div class="mx-auto grid max-w-7xl gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
          <div class="text-sm font-semibold text-slate-500">Layanan Anggota</div>
          <div class="mt-3 text-xl font-extrabold text-ink">Simpanan Terpantau</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
          <div class="text-sm font-semibold text-slate-500">Pengajuan</div>
          <div class="mt-3 text-xl font-extrabold text-ink">Pinjaman Tervalidasi</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
          <div class="text-sm font-semibold text-slate-500">Pembayaran</div>
          <div class="mt-3 text-xl font-extrabold text-ink">Angsuran Tercatat</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
          <div class="text-sm font-semibold text-slate-500">Informasi</div>
          <div class="mt-3 text-xl font-extrabold text-ink">Berita dan Pengumuman</div>
        </div>
      </div>
    </section>

    <section id="tentang" class="py-24">
      <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-[1.05fr_.95fr] lg:px-8">
        <div>
          <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Tentang Koperasi</span>
          <h2 class="mt-4 text-3xl font-extrabold leading-tight text-ink sm:text-4xl">Ruang keuangan bersama untuk anggota yang ingin tumbuh lebih tenang.</h2>
          <p class="mt-6 text-base leading-8 text-slate-600">
            KSU St. Karolus menghadirkan layanan koperasi yang berfokus pada kepercayaan, pencatatan rapi, dan kemudahan akses. Anggota dapat melihat perkembangan simpanan, status pinjaman, dan pembayaran angsuran dengan lebih jelas.
          </p>
          <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
              <div class="text-lg font-extrabold text-ink">Transparan</div>
              <p class="mt-2 text-sm leading-6 text-slate-600">Data transaksi tersusun sehingga anggota dan pengurus dapat memantau aktivitas keuangan dengan mudah.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
              <div class="text-lg font-extrabold text-ink">Terarah</div>
              <p class="mt-2 text-sm leading-6 text-slate-600">Alur simpanan, pinjaman, angsuran, dan laporan dibuat jelas untuk mendukung keputusan koperasi.</p>
            </div>
          </div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
          <div class="rounded-2xl bg-slate-100 p-5">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-semibold text-slate-500">Ringkasan Anggota</div>
                <div class="mt-2 text-2xl font-extrabold text-ink">Layanan Digital</div>
              </div>
              <div class="rounded-2xl bg-primary px-4 py-3 text-sm font-extrabold text-white">KSU</div>
            </div>
            <div class="mt-6 space-y-4">
              <div class="rounded-2xl bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between text-sm font-bold">
                  <span>Simpanan</span><span class="text-primary">Tercatat</span>
                </div>
              </div>
              <div class="rounded-2xl bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between text-sm font-bold">
                  <span>Pinjaman</span><span class="text-amber-600">Tervalidasi</span>
                </div>
              </div>
              <div class="rounded-2xl bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between text-sm font-bold">
                  <span>Angsuran</span><span class="text-emerald-600">Terpantau</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="layanan" class="bg-white py-24">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
          <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Layanan</span>
          <h2 class="mt-4 text-3xl font-extrabold text-ink sm:text-4xl">Layanan utama koperasi untuk kebutuhan anggota.</h2>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
          <article class="rounded-3xl border border-slate-200 p-7 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/5">
            <div class="mb-6 inline-flex rounded-2xl bg-blue-50 p-4 text-primary">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7h18M5 7v12h14V7M8 11h8M8 15h5" /></svg>
            </div>
            <h3 class="text-xl font-extrabold text-ink">Simpanan Anggota</h3>
            <p class="mt-3 text-sm leading-7 text-slate-600">Pencatatan simpanan pokok, wajib, dan sukarela yang dapat dipantau secara berkala oleh anggota.</p>
          </article>
          <article class="rounded-3xl border border-slate-200 p-7 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/5">
            <div class="mb-6 inline-flex rounded-2xl bg-amber-50 p-4 text-amber-600">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-3 0-5 1.5-5 3.5S9 15 12 15s5-1.5 5-3.5S15 8 12 8Z" /><path d="M4 6h16v12H4z" /></svg>
            </div>
            <h3 class="text-xl font-extrabold text-ink">Pengajuan Pinjaman</h3>
            <p class="mt-3 text-sm leading-7 text-slate-600">Anggota dapat mengajukan pinjaman dan memantau status validasi dari pengurus koperasi.</p>
          </article>
          <article class="rounded-3xl border border-slate-200 p-7 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/5">
            <div class="mb-6 inline-flex rounded-2xl bg-emerald-50 p-4 text-emerald-600">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v6h6M20 20v-6h-6" /><path d="M20 9A8 8 0 0 0 6.3 5.7L4 8m0 7a8 8 0 0 0 13.7 3.3L20 16" /></svg>
            </div>
            <h3 class="text-xl font-extrabold text-ink">Angsuran dan Laporan</h3>
            <p class="mt-3 text-sm leading-7 text-slate-600">Riwayat angsuran dan laporan transaksi membantu koperasi menjaga keuangan tetap tertata.</p>
          </article>
        </div>
      </div>
    </section>

    <section class="py-24">
      <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[.9fr_1.1fr] lg:px-8">
        <div>
          <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Alur Anggota</span>
          <h2 class="mt-4 text-3xl font-extrabold text-ink sm:text-4xl">Mulai dari daftar, simpan, ajukan, lalu pantau.</h2>
          <p class="mt-5 text-base leading-8 text-slate-600">Setiap proses dibuat sederhana agar anggota dapat fokus pada kebutuhan utama tanpa kehilangan kontrol atas data keuangan.</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
          <?php
          $steps = [
            ['01', 'Daftar Anggota', 'Data anggota dicatat oleh pengurus koperasi.'],
            ['02', 'Kelola Simpanan', 'Simpanan dicatat dan dapat dipantau dari akun anggota.'],
            ['03', 'Ajukan Pinjaman', 'Pengajuan diproses dan divalidasi oleh bendahara.'],
            ['04', 'Pantau Angsuran', 'Setiap pembayaran tersimpan sebagai riwayat angsuran.']
          ];
          foreach ($steps as $step) { ?>
            <div class="rounded-3xl border border-slate-200 bg-white p-6">
              <div class="text-sm font-extrabold text-primary"><?= $step[0] ?></div>
              <h3 class="mt-3 text-lg font-extrabold text-ink"><?= $step[1] ?></h3>
              <p class="mt-2 text-sm leading-6 text-slate-600"><?= $step[2] ?></p>
            </div>
          <?php } ?>
        </div>
      </div>
    </section>

    <section class="bg-white py-24">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
          <div>
            <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Informasi</span>
            <h2 class="mt-4 text-3xl font-extrabold text-ink sm:text-4xl">Berita dan pengumuman terbaru.</h2>
          </div>
          <a href="berita" class="inline-flex rounded-full border border-slate-200 px-6 py-3 text-sm font-extrabold text-slate-700 transition hover:border-primary hover:text-primary">Lihat Semua</a>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
          <?php if (!empty($latestInfo)) { ?>
            <?php foreach ($latestInfo as $info) { ?>
              <article class="rounded-3xl border border-slate-200 p-6">
                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-extrabold text-primary"><?= htmlspecialchars($info['tipe']) ?></span>
                <h3 class="mt-5 text-lg font-extrabold leading-7 text-ink"><?= htmlspecialchars($info['judul']) ?></h3>
                <p class="mt-4 text-sm font-semibold text-slate-500"><?= $info['tanggal'] ? date('d M Y', strtotime($info['tanggal'])) : '-' ?></p>
              </article>
            <?php } ?>
          <?php } else { ?>
            <div class="rounded-3xl border border-slate-200 p-6 md:col-span-3">
              <p class="text-slate-600">Belum ada berita atau pengumuman terbaru.</p>
            </div>
          <?php } ?>
        </div>
      </div>
    </section>

    <section class="px-4 py-20 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-7xl overflow-hidden rounded-[2rem] bg-primary px-6 py-12 text-white shadow-2xl shadow-blue-800/20 sm:px-10 lg:flex lg:items-center lg:justify-between">
        <div class="max-w-2xl">
          <h2 class="text-3xl font-extrabold">Akses layanan koperasi dalam satu sistem.</h2>
          <p class="mt-4 text-blue-50">Masuk untuk melihat simpanan, pinjaman, angsuran, laporan transaksi, serta informasi koperasi terbaru.</p>
        </div>
        <a href="auth/" class="mt-8 inline-flex rounded-full bg-white px-7 py-3.5 text-sm font-extrabold text-primary shadow-lg transition hover:bg-blue-50 lg:mt-0">Masuk Sekarang</a>
      </div>
    </section>
  </main>

<?php require_once("sections/public_footer.php"); ?>
