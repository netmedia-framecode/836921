<?php
require_once('config/Base.php');
$publicTitle = 'Pengumuman KSU St. Karolus';
$publicActive = 'pengumuman';
$pengumuman = mysqli_query($conn, 'SELECT judul, isi_pengumuman, tgl_posting FROM pengumuman ORDER BY tgl_posting DESC, id_pengumuman DESC');
require_once('sections/public_head.php');
require_once('sections/public_navbar.php'); ?>
<main class="mx-auto min-h-screen max-w-7xl px-4 pb-20 pt-32 sm:px-6 lg:px-8">
  <h1 class="text-4xl font-extrabold text-ink">Pengumuman Koperasi</h1>
  <p class="mt-4 text-slate-600">Informasi resmi untuk anggota KSU St. Karolus.</p>
  <div class="mt-10 grid gap-6 md:grid-cols-2">
    <?php foreach ($pengumuman as $item) { ?>
    <article class="rounded-3xl border border-slate-200 bg-white p-7">
      <p class="text-sm text-slate-500"><?= htmlspecialchars($item['tgl_posting']) ?></p>
      <h2 class="mt-3 text-xl font-bold"><?= htmlspecialchars($item['judul']) ?></h2>
      <div class="mt-4 whitespace-pre-line leading-7 text-slate-600"><?= htmlspecialchars(strip_tags(html_entity_decode($item['isi_pengumuman']))) ?></div>
    </article>
    <?php } ?>
    <?php if (!mysqli_num_rows($pengumuman)) { ?><p>Belum ada pengumuman.</p><?php } ?>
  </div>
</main>
<?php require_once('sections/public_footer.php'); ?>
