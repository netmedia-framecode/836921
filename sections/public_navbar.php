<?php
$publicWebName = ($data_utilities['name_web'] ?? '') ?: $name_website;
$publicLogo = 'logo-kop.png';
$publicLogoPath = preg_match('/^(https?:\/\/|assets\/|\/)/', $publicLogo) ? $publicLogo : 'assets/img/' . $publicLogo;
$publicActive = $publicActive ?? 'beranda';
?>
<nav class="fixed inset-x-0 top-0 z-50 border-b border-white/20 bg-white/90 backdrop-blur">
  <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
    <a href="./" class="flex items-center gap-3">
      <img src="<?= htmlspecialchars($publicLogoPath) ?>" alt="Logo <?= htmlspecialchars($publicWebName) ?>" class="h-11 w-11 rounded-xl object-contain">
      <div>
        <div class="text-lg font-extrabold leading-tight text-ink"><?= htmlspecialchars($publicWebName) ?></div>
      </div>
    </a>

    <div class="hidden items-center gap-7 text-sm font-semibold text-slate-600 md:flex">
      <a href="./" class="<?= $publicActive == 'beranda' ? 'text-primary' : 'transition hover:text-primary' ?>">Beranda</a>
      <a href="tentang" class="<?= $publicActive == 'tentang' ? 'text-primary' : 'transition hover:text-primary' ?>">Tentang</a>
      <a href="layanan" class="<?= $publicActive == 'layanan' ? 'text-primary' : 'transition hover:text-primary' ?>">Layanan</a>
      <a href="pengumuman" class="<?= $publicActive == 'pengumuman' ? 'text-primary' : 'transition hover:text-primary' ?>">Pengumuman</a>
      <a href="kontak" class="<?= $publicActive == 'kontak' ? 'text-primary' : 'transition hover:text-primary' ?>">Kontak</a>
    </div>

    <div class="flex items-center gap-3">
      <a href="auth/" class="rounded-full bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-700">Masuk</a>
    </div>
  </div>
</nav>
