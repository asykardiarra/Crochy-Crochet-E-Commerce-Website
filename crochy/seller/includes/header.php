<?php
// Variabel $activePage dan $pageTitle diset di masing-masing halaman sebelum include ini.
$activePage = $activePage ?? '';
$pageTitle  = $pageTitle ?? 'Seller Centre';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($pageTitle) ?> - Seller Centre Crochy</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../css/style.css"/>
</head>
<body>
<div class="admin-shell">
  <aside class="admin-sidebar">
    <a href="dashboard.php" class="admin-logo">Crochy Seller Centre</a>
    <nav>
      <a href="dashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">📊 Dashboard</a>
      <a href="pesanan.php" class="<?= $activePage === 'pesanan' ? 'active' : '' ?>">📦 Pesanan</a>
      <a href="laporan.php" class="<?= $activePage === 'laporan' ? 'active' : '' ?>">📈 Laporan</a>
      <a href="produk.php" class="<?= $activePage === 'produk' ? 'active' : '' ?>">🧶 Produk</a>
      <a href="diskon.php" class="<?= $activePage === 'diskon' ? 'active' : '' ?>">🏷️ Diskon</a>
      <a href="hero.php" class="<?= $activePage === 'hero' ? 'active' : '' ?>">🖼️ Foto Hero</a>
      <a href="profil.php" class="<?= $activePage === 'profil' ? 'active' : '' ?>">👤 Profil Toko</a>
    </nav>
    <a href="../index.php" class="admin-logout" style="border-top:none;padding-top:.5rem">🛍️ Lihat Toko</a>
    <a href="../auth/logout.php" class="admin-logout">🚪 Logout<br><small><?= htmlspecialchars($_SESSION['user_nama'] ?? '') ?></small></a>
  </aside>
  <main class="admin-main">
