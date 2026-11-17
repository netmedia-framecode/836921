<?php
require_once("config/Base.php");

$publicWebName = (($data_utilities['name_web'] ?? '') ?: $name_website);
$publicTitle = "Tentang " . $publicWebName;
$publicDescription = "Kenali " . $publicWebName . " sebagai koperasi serba usaha yang mendukung transparansi simpanan, pinjaman, angsuran, dan informasi anggota.";
$publicActive = 'tentang';
require_once("sections/public_head.php");
require_once("sections/public_navbar.php");
?>

<header class="relative overflow-hidden bg-slate-950 pt-28">
  <img src="assets/img/kantor.jpeg" alt="Profil <?= htmlspecialchars($publicWebName) ?>" class="absolute inset-0 h-full w-full object-cover opacity-25">
  <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
    <div class="max-w-3xl text-white">
      <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-bold backdrop-blur">Tentang Koperasi</span>
      <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl">Membangun kepercayaan anggota melalui layanan koperasi yang tertata.</h1>
      <p class="mt-6 text-lg leading-8 text-slate-200">
        <?= htmlspecialchars($publicWebName) ?> hadir sebagai ruang ekonomi bersama yang mengutamakan asas kekeluargaan, pencatatan rapi, dan akses informasi yang mudah untuk seluruh anggota.
      </p>
    </div>
  </div>
</header>

<main>
  <section class="py-24">
    <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-[1.05fr_.95fr] lg:px-8">
      <div>
        <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Profil Singkat</span>
        <h2 class="mt-4 text-3xl font-extrabold leading-tight text-ink sm:text-4xl"><?= htmlspecialchars($publicWebName) ?> dan komitmen pelayanan anggota.</h2>
        <div class="mt-6 space-y-5 text-base leading-8 text-slate-600">
          <p>
            Koperasi Serba Usaha (KSU) St. Karolus berkantor di Kabupaten Malaka, Nusa Tenggara Timur. Identitas pada papan nama kantor mencantumkan badan hukum 008389/BH/M.KUKM.2/V/2018, tanggal 14 Mei 2018.
          </p>
          <p>
            Melalui website KSU St. Karolus, anggota dapat mengakses catatan simpanan, mengajukan pinjaman, melihat riwayat angsuran, serta membaca pengumuman, dan kebijakan koperasi. Kunjungi halaman Kontak untuk melihat titik lokasi kantor.
          </p>
        </div>
      </div>
      <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-900/5">
        <div class="rounded-2xl bg-blue-50 p-6">
          <div class="text-sm font-extrabold uppercase tracking-widest text-primary">Nilai Dasar</div>
          <div class="mt-6 space-y-4">
            <div class="rounded-2xl bg-white p-5">
              <h3 class="font-extrabold text-ink">Kekeluargaan</h3>
              <p class="mt-2 text-sm leading-6 text-slate-600">Pelayanan koperasi dibangun dari kepentingan bersama dan kedekatan antaranggota.</p>
            </div>
            <div class="rounded-2xl bg-white p-5">
              <h3 class="font-extrabold text-ink">Transparansi</h3>
              <p class="mt-2 text-sm leading-6 text-slate-600">Setiap transaksi dicatat agar anggota dan pengurus dapat melakukan pemantauan dengan jelas.</p>
            </div>
            <div class="rounded-2xl bg-white p-5">
              <h3 class="font-extrabold text-ink">Tanggung Jawab</h3>
              <p class="mt-2 text-sm leading-6 text-slate-600">Pengelolaan data dan laporan diarahkan untuk mendukung keputusan koperasi yang akurat.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-white py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 p-8">
          <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Pelayanan Anggota</span>
          <h2 class="mt-4 text-3xl font-extrabold text-ink">Menjadi koperasi yang terpercaya, tertib, dan bermanfaat bagi kesejahteraan anggota.</h2>
          <p class="mt-5 text-base leading-8 text-slate-600">Website ini membantu anggota mengakses catatan transaksi dan informasi layanan koperasi.</p>
        </div>
        <div class="rounded-3xl border border-slate-200 p-8">
          <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Layanan Website</span>
          <div class="mt-5 space-y-4">
            <?php
            $missions = [
              'Menyediakan layanan simpanan dan pinjaman yang terarah bagi anggota.',
              'Meningkatkan ketertiban pencatatan transaksi koperasi.',
              'Memperkuat transparansi laporan dan informasi bagi pengurus maupun anggota.',
              'Mendukung budaya gotong royong dalam pengembangan ekonomi anggota.'
            ];
            foreach ($missions as $mission) { ?>
              <div class="flex gap-3">
                <div class="mt-1 h-5 w-5 flex-none rounded-full bg-primary"></div>
                <p class="text-base leading-7 text-slate-600"><?= $mission ?></p>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl">
        <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Cara Kerja</span>
        <h2 class="mt-4 text-3xl font-extrabold text-ink sm:text-4xl">Alur layanan koperasi dibuat sederhana dan mudah dipantau.</h2>
      </div>
      <div class="mt-10 grid gap-5 md:grid-cols-4">
        <?php
        $steps = [
          ['01', 'Data Anggota', 'Identitas anggota dicatat sebagai dasar akses layanan koperasi.'],
          ['02', 'Transaksi', 'Simpanan, pinjaman, dan angsuran diproses sesuai kebutuhan anggota.'],
          ['03', 'Validasi', 'Admin/bendahara memeriksa kelengkapan; kepala koperasi memberikan persetujuan akhir.'],
          ['04', 'Laporan', 'Data transaksi disajikan menjadi laporan yang mudah dipantau.']
        ];
        foreach ($steps as $step) { ?>
          <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <div class="text-sm font-extrabold text-primary"><?= $step[0] ?></div>
            <h3 class="mt-4 text-lg font-extrabold text-ink"><?= $step[1] ?></h3>
            <p class="mt-3 text-sm leading-6 text-slate-600"><?= $step[2] ?></p>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>

  <section class="px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl rounded-[2rem] bg-primary px-6 py-12 text-white sm:px-10 lg:flex lg:items-center lg:justify-between">
      <div class="max-w-2xl">
        <h2 class="text-3xl font-extrabold">Ingin melihat layanan koperasi lebih lengkap?</h2>
        <p class="mt-4 text-blue-50">Masuk ke sistem untuk mengakses data transaksi, laporan, dan informasi terbaru sesuai hak akses Anda.</p>
      </div>
      <a href="auth/" class="mt-8 inline-flex rounded-full bg-white px-7 py-3.5 text-sm font-extrabold text-primary shadow-lg transition hover:bg-blue-50 lg:mt-0">Masuk ke Sistem</a>
    </div>
  </section>
</main>

<?php require_once("sections/public_footer.php"); ?>
