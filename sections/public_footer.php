<?php $publicWebName = ($data_utilities['name_web'] ?? '') ?: $name_website; ?>
<footer class="border-t border-slate-200 bg-white">
  <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 text-sm text-slate-500 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
    <div class="font-semibold"><?= htmlspecialchars($publicWebName) ?></div>
    <div>&copy; <?= date('Y') ?> <?= htmlspecialchars($publicWebName) ?>. Semua hak dilindungi.</div>
  </div>
</footer>
</body>

</html>
