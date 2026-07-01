<?php
require_once("config/Base.php");

$publicWebName = (($data_utilities['name_web'] ?? '') ?: $name_website);
$publicTitle = "Kontak " . $publicWebName;
$publicDescription = "Hubungi " . $publicWebName . " untuk informasi layanan koperasi, simpanan, pinjaman, angsuran, dan pusat informasi anggota.";
$publicActive = 'kontak';
require_once("sections/public_head.php");
require_once("sections/public_navbar.php");
?>

<header class="relative overflow-hidden bg-slate-950 pt-28">
  <img src="assets/img/auth/auth.png" alt="Kontak <?= htmlspecialchars($publicWebName) ?>" class="absolute inset-0 h-full w-full object-cover opacity-25">
  <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-950/90 to-blue-950/80"></div>
  <div class="relative mx-auto max-w-7xl px-4 py-20 text-white sm:px-6 lg:px-8">
    <div class="max-w-3xl">
      <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-bold backdrop-blur">Kontak Koperasi</span>
      <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl">Kami siap membantu kebutuhan informasi Anda.</h1>
      <p class="mt-6 text-lg leading-8 text-slate-200">
        Gunakan halaman ini untuk mengetahui kanal komunikasi, jam layanan, dan akses cepat menuju sistem anggota <?= htmlspecialchars($publicWebName) ?>.
      </p>
    </div>
  </div>
</header>

<main>
  <section class="py-24">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[1.05fr_.95fr] lg:px-8">
      <div>
        <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Lokasi Koperasi</span>
        <h2 class="mt-4 text-3xl font-extrabold leading-tight text-ink sm:text-4xl">Temukan lokasi dan akses informasi kontak koperasi.</h2>
        <p class="mt-5 text-base leading-8 text-slate-600">
          Halaman kontak difokuskan untuk informasi lokasi dan arahan layanan. Untuk data transaksi dan informasi pribadi anggota, silakan masuk ke sistem menggunakan akun resmi.
        </p>

        <div class="mt-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">
          <div class="relative h-[28rem] w-full bg-slate-100">
            <iframe
              title="Lokasi <?= htmlspecialchars($publicWebName) ?>"
              src="https://www.google.com/maps?q=<?= urlencode($publicWebName) ?>&output=embed"
              class="absolute inset-0 h-full w-full border-0"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
          <div class="grid gap-4 p-6 sm:grid-cols-[1fr_auto] sm:items-center">
            <div>
              <h3 class="text-xl font-extrabold text-ink"><?= htmlspecialchars($publicWebName) ?></h3>
              <p class="mt-2 text-sm leading-7 text-slate-600">Gunakan peta untuk melihat lokasi koperasi atau buka langsung melalui Google Maps.</p>
            </div>
            <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($publicWebName) ?>" target="_blank" rel="noopener" class="inline-flex justify-center rounded-full bg-primary px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-700">Buka Maps</a>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="grid gap-5 sm:grid-cols-2">
          <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 inline-flex rounded-2xl bg-blue-50 p-4 text-primary">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 1 1 18 0Z" /><path d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /></svg>
            </div>
            <h3 class="text-lg font-extrabold text-ink">Kantor Koperasi</h3>
            <p class="mt-3 text-sm leading-7 text-slate-600">Informasi lokasi kantor dapat diperoleh melalui pengurus koperasi atau kanal resmi yang tersedia.</p>
          </div>

          <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 inline-flex rounded-2xl bg-emerald-50 p-4 text-emerald-600">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.77.63 2.6a2 2 0 0 1-.45 2.11L8 9.72a16 16 0 0 0 6.28 6.28l1.29-1.29a2 2 0 0 1 2.11-.45c.83.3 1.7.51 2.6.63A2 2 0 0 1 22 16.92Z" /></svg>
            </div>
            <h3 class="text-lg font-extrabold text-ink">Layanan Anggota</h3>
            <p class="mt-3 text-sm leading-7 text-slate-600">Pertanyaan terkait simpanan, pinjaman, dan angsuran sebaiknya dilakukan melalui akun anggota.</p>
          </div>

          <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 inline-flex rounded-2xl bg-amber-50 p-4 text-amber-600">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v5l3 2" /><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <h3 class="text-lg font-extrabold text-ink">Jam Layanan</h3>
            <p class="mt-3 text-sm leading-7 text-slate-600">Layanan administrasi mengikuti jadwal kerja koperasi dan ketentuan pengurus.</p>
          </div>

          <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 inline-flex rounded-2xl bg-violet-50 p-4 text-violet-600">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 5h16v14H4z" /><path d="m4 7 8 6 8-6" /></svg>
            </div>
            <h3 class="text-lg font-extrabold text-ink">Informasi Resmi</h3>
            <p class="mt-3 text-sm leading-7 text-slate-600">Berita, pengumuman, dan kebijakan koperasi tersedia pada halaman pusat informasi.</p>
          </div>
        </div>

        <div class="rounded-3xl bg-primary p-7 text-white shadow-xl shadow-blue-700/20">
          <h3 class="text-2xl font-extrabold">Butuh akses data pribadi?</h3>
          <p class="mt-3 text-sm leading-7 text-blue-50">Masuk ke sistem untuk melihat transaksi, laporan, dan informasi anggota sesuai hak akses akun Anda.</p>
          <a href="auth/" class="mt-6 inline-flex rounded-full bg-white px-6 py-3 text-sm font-extrabold text-primary transition hover:bg-blue-50">Masuk ke Sistem</a>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-white py-24">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[.9fr_1.1fr] lg:px-8">
      <div>
        <span class="text-sm font-extrabold uppercase tracking-widest text-primary">Pertanyaan Umum</span>
        <h2 class="mt-4 text-3xl font-extrabold leading-tight text-ink sm:text-4xl">Beberapa hal yang sering ditanyakan anggota.</h2>
        <p class="mt-5 text-base leading-8 text-slate-600">
          Informasi berikut membantu mengarahkan pengguna ke kanal yang tepat tanpa membuka data sensitif di halaman publik.
        </p>
      </div>

      <div class="space-y-4">
        <?php
        $faqs = [
          ['Bagaimana melihat data simpanan?', 'Anggota perlu masuk ke sistem menggunakan akun masing-masing untuk melihat data simpanan pribadi.'],
          ['Bagaimana mengajukan pinjaman?', 'Pengajuan pinjaman dapat dilakukan melalui menu pinjaman setelah anggota masuk ke sistem.'],
          ['Apakah laporan transaksi tampil di publik?', 'Tidak. Laporan transaksi hanya tersedia setelah login dan mengikuti hak akses pengguna.'],
          ['Di mana melihat pengumuman koperasi?', 'Pengumuman terbaru tersedia pada halaman berita dan pusat informasi publik.']
        ];
        foreach ($faqs as $faq) { ?>
          <details class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
            <summary class="cursor-pointer text-base font-extrabold text-ink"><?= $faq[0] ?></summary>
            <p class="mt-4 text-sm leading-7 text-slate-600"><?= $faq[1] ?></p>
          </details>
        <?php } ?>
      </div>
    </div>
  </section>
</main>

<?php require_once("sections/public_footer.php"); ?>
