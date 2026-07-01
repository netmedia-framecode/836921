<?php
require_once("config/Base.php");

$publicWebName = (($data_utilities['name_web'] ?? '') ?: $name_website);
$publicTitle = "Berita " . $publicWebName;
$publicDescription = "Berita, pengumuman, dan informasi resmi terbaru dari " . $publicWebName . ".";
$publicActive = 'berita';

$keyword = trim($_GET['q'] ?? '');
$safeKeyword = mysqli_real_escape_string($conn, $keyword);
$whereBerita = $safeKeyword !== '' ? "WHERE berita.judul LIKE '%$safeKeyword%' OR berita.isi_berita LIKE '%$safeKeyword%'" : '';

$beritaQuery = mysqli_query($conn, "
  SELECT berita.*, users.name AS nama_admin
  FROM berita
  LEFT JOIN users ON berita.id_admin = users.id_user
  $whereBerita
  ORDER BY berita.tgl_buat DESC, berita.id_berita DESC
");

$beritaList = [];
if ($beritaQuery) {
  foreach ($beritaQuery as $row) {
    $beritaList[] = $row;
  }
}

$pengumumanQuery = mysqli_query($conn, "
  SELECT pengumuman.*, users.name AS nama_admin
  FROM pengumuman
  LEFT JOIN users ON pengumuman.id_admin = users.id_user
  ORDER BY pengumuman.tgl_posting DESC, pengumuman.id_pengumuman DESC
  LIMIT 4
");

$pengumumanList = [];
if ($pengumumanQuery) {
  foreach ($pengumumanQuery as $row) {
    $pengumumanList[] = $row;
  }
}

$kebijakanQuery = mysqli_query($conn, "
  SELECT kebijakan_koperasi.*, users.name AS nama_admin
  FROM kebijakan_koperasi
  LEFT JOIN users ON kebijakan_koperasi.id_admin = users.id_user
  ORDER BY kebijakan_koperasi.tgl_ubah DESC, kebijakan_koperasi.id_kebijakan DESC
  LIMIT 3
");

$kebijakanList = [];
if ($kebijakanQuery) {
  foreach ($kebijakanQuery as $row) {
    $kebijakanList[] = $row;
  }
}

$excerpt = function ($text, $limit = 170) {
  $cleanText = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($text))));
  if (strlen($cleanText) <= $limit) {
    return $cleanText;
  }
  return substr($cleanText, 0, $limit) . '...';
};

$formatDate = function ($date) {
  return $date ? date('d M Y', strtotime($date)) : '-';
};

$featuredBerita = $beritaList[0] ?? null;
$otherBerita = array_slice($beritaList, 1);

require_once("sections/public_head.php");
require_once("sections/public_navbar.php");
?>

<header class="relative overflow-hidden bg-slate-950 pt-28">
  <img src="assets/img/auth/auth.png" alt="Berita <?= htmlspecialchars($publicWebName) ?>" class="absolute inset-0 h-full w-full object-cover opacity-25">
  <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-950/90 to-blue-950/80"></div>
  <div class="relative mx-auto max-w-7xl px-4 py-20 text-white sm:px-6 lg:px-8">
    <div class="max-w-3xl">
      <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-bold backdrop-blur">Pusat Informasi</span>
      <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl">Berita dan informasi resmi koperasi.</h1>
      <p class="mt-6 text-lg leading-8 text-slate-200">
        Ikuti kabar terbaru, pengumuman, dan kebijakan koperasi yang dipublikasikan untuk anggota dan masyarakat.
      </p>
    </div>
  </div>
</header>

<main>
  <section class="py-20">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[1fr_22rem] lg:px-8">
      <div>
        <div class="mb-8 flex flex-col justify-between gap-5 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center">
          <div>
            <div class="text-sm font-extrabold uppercase tracking-widest text-primary">Daftar Berita</div>
            <h2 class="mt-2 text-2xl font-extrabold text-ink">Informasi terbaru dari koperasi</h2>
          </div>
          <form action="berita" method="GET" class="flex w-full gap-2 sm:max-w-sm">
            <input type="search" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari berita..." class="min-w-0 flex-1 rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-primary">
            <button type="submit" class="rounded-full bg-primary px-5 py-3 text-sm font-extrabold text-white transition hover:bg-blue-700">Cari</button>
          </form>
        </div>

        <?php if ($featuredBerita) { ?>
          <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">
            <div class="grid lg:grid-cols-[.95fr_1.05fr]">
              <div class="relative min-h-72 bg-slate-900">
                <img src="assets/img/auth/auth.png" alt="<?= htmlspecialchars($featuredBerita['judul']) ?>" class="absolute inset-0 h-full w-full object-cover opacity-50">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/80 to-slate-950/80"></div>
                <div class="absolute bottom-6 left-6 right-6">
                  <span class="inline-flex rounded-full bg-white px-4 py-2 text-xs font-extrabold text-primary">Berita Utama</span>
                </div>
              </div>
              <div class="p-7 sm:p-9">
                <div class="flex flex-wrap gap-3 text-sm font-bold text-slate-500">
                  <span><?= $formatDate($featuredBerita['tgl_buat']) ?></span>
                  <span>Author: <?= htmlspecialchars($featuredBerita['nama_admin'] ?: 'Admin') ?></span>
                </div>
                <h2 class="mt-5 text-3xl font-extrabold leading-tight text-ink"><?= htmlspecialchars($featuredBerita['judul']) ?></h2>
                <p class="mt-5 text-base leading-8 text-slate-600"><?= htmlspecialchars($excerpt($featuredBerita['isi_berita'], 240)) ?></p>
                <details class="mt-6 rounded-2xl bg-slate-50 p-5">
                  <summary class="cursor-pointer text-sm font-extrabold text-primary">Baca selengkapnya</summary>
                  <div class="mt-4 whitespace-pre-wrap text-sm leading-7 text-slate-600"><?= htmlspecialchars(trim(strip_tags(html_entity_decode($featuredBerita['isi_berita'])))) ?></div>
                </details>
              </div>
            </div>
          </article>

          <?php if (!empty($otherBerita)) { ?>
            <div class="mt-8 grid gap-6 md:grid-cols-2">
              <?php foreach ($otherBerita as $data) { ?>
                <article class="flex min-h-full flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/5">
                  <div class="mb-5 flex items-center justify-between gap-4 text-sm font-bold text-slate-500">
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-extrabold text-primary">Berita</span>
                    <span><?= $formatDate($data['tgl_buat']) ?></span>
                  </div>
                  <h3 class="text-xl font-extrabold leading-7 text-ink"><?= htmlspecialchars($data['judul']) ?></h3>
                  <p class="mt-4 flex-1 text-sm leading-7 text-slate-600"><?= htmlspecialchars($excerpt($data['isi_berita'])) ?></p>
                  <div class="mt-5 text-sm font-semibold text-slate-500">Author: <?= htmlspecialchars($data['nama_admin'] ?: 'Admin') ?></div>
                  <details class="mt-5 rounded-2xl bg-slate-50 p-4">
                    <summary class="cursor-pointer text-sm font-extrabold text-primary">Baca selengkapnya</summary>
                    <div class="mt-4 whitespace-pre-wrap text-sm leading-7 text-slate-600"><?= htmlspecialchars(trim(strip_tags(html_entity_decode($data['isi_berita'])))) ?></div>
                  </details>
                </article>
              <?php } ?>
            </div>
          <?php } ?>
        <?php } else { ?>
          <div class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm">
            <div class="mx-auto mb-5 h-16 w-16 rounded-2xl bg-blue-50"></div>
            <h2 class="text-2xl font-extrabold text-ink">Berita belum tersedia</h2>
            <p class="mt-3 text-slate-600">
              <?= $keyword !== '' ? 'Tidak ada berita yang cocok dengan kata kunci pencarian Anda.' : 'Informasi berita akan ditampilkan di halaman ini setelah dipublikasikan.' ?>
            </p>
            <?php if ($keyword !== '') { ?>
              <a href="berita" class="mt-6 inline-flex rounded-full bg-primary px-6 py-3 text-sm font-extrabold text-white">Lihat Semua Berita</a>
            <?php } ?>
          </div>
        <?php } ?>
      </div>

      <aside class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="mb-5">
            <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Pengumuman</span>
            <h2 class="mt-2 text-xl font-extrabold text-ink">Terbaru</h2>
          </div>
          <div class="space-y-4">
            <?php if (!empty($pengumumanList)) { ?>
              <?php foreach ($pengumumanList as $pengumuman) { ?>
                <div class="rounded-2xl bg-slate-50 p-4">
                  <div class="text-xs font-extrabold text-primary"><?= $formatDate($pengumuman['tgl_posting']) ?></div>
                  <h3 class="mt-2 text-sm font-extrabold leading-6 text-ink"><?= htmlspecialchars($pengumuman['judul']) ?></h3>
                  <p class="mt-2 text-sm leading-6 text-slate-600"><?= htmlspecialchars($excerpt($pengumuman['isi_pengumuman'], 95)) ?></p>
                </div>
              <?php } ?>
            <?php } else { ?>
              <p class="text-sm leading-7 text-slate-600">Belum ada pengumuman terbaru.</p>
            <?php } ?>
          </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="mb-5">
            <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Kebijakan</span>
            <h2 class="mt-2 text-xl font-extrabold text-ink">Koperasi</h2>
          </div>
          <div class="space-y-4">
            <?php if (!empty($kebijakanList)) { ?>
              <?php foreach ($kebijakanList as $kebijakan) { ?>
                <div class="border-b border-slate-100 pb-4 last:border-b-0 last:pb-0">
                  <h3 class="text-sm font-extrabold leading-6 text-ink"><?= htmlspecialchars($kebijakan['judul']) ?></h3>
                  <p class="mt-2 text-sm leading-6 text-slate-600"><?= htmlspecialchars($excerpt($kebijakan['isi_kebijakan'], 90)) ?></p>
                </div>
              <?php } ?>
            <?php } else { ?>
              <p class="text-sm leading-7 text-slate-600">Belum ada kebijakan yang dipublikasikan.</p>
            <?php } ?>
          </div>
        </div>

        <div class="rounded-3xl bg-primary p-6 text-white shadow-xl shadow-blue-700/20">
          <h2 class="text-xl font-extrabold">Akses informasi anggota</h2>
          <p class="mt-3 text-sm leading-7 text-blue-50">Masuk ke sistem untuk melihat data transaksi dan laporan sesuai hak akses Anda.</p>
          <a href="auth/" class="mt-5 inline-flex rounded-full bg-white px-5 py-3 text-sm font-extrabold text-primary transition hover:bg-blue-50">Masuk</a>
        </div>
      </aside>
    </div>
  </section>
</main>

<?php require_once("sections/public_footer.php"); ?>
