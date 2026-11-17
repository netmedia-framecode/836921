<?php
require_once(__DIR__ . "/../config/Koperasi.php");
$publicTitle = $publicTitle ?? ($data_utilities['name_web'] ?? $name_website);
$publicDescription = $publicDescription ?? ($data_utilities['description'] ?? 'Koperasi serba usaha yang membantu anggota mengelola simpanan, pinjaman, angsuran, dan informasi koperasi secara transparan.');
$publicKeywords = $publicKeywords ?? ($data_utilities['keyword'] ?? 'koperasi, simpanan, pinjaman, angsuran');
$publicAuthor = $data_utilities['author'] ?? $publicTitle;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <link rel="icon" href="assets/img/logo-kop.png" type="image/png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($publicDescription) ?>">
  <meta name="keywords" content="<?= htmlspecialchars($publicKeywords) ?>">
  <meta name="author" content="<?= htmlspecialchars($publicAuthor) ?>">
  <title><?= htmlspecialchars($publicTitle) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#1456d9',
            ink: '#172033'
          },
          fontFamily: {
            sans: ['Inter', 'ui-sans-serif', 'system-ui']
          }
        }
      }
    }
  </script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }
  </style>
</head>

<body class="bg-slate-50 text-slate-800">
