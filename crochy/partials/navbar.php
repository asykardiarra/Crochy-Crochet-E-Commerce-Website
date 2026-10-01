<?php
$halamanAktif = $halamanAktif ?? '';
function navAktif($nama, $halamanAktif) {
  return $nama === $halamanAktif ? ' class="active"' : '';
}
?>
<nav>
  <a class="nav-logo" href="index.php">Crochy</a>
  <div class="nav-right">
    <ul class="nav-links" id="navLinks">
      <li><a href="index.php"<?= navAktif('beranda', $halamanAktif) ?>>Beranda</a></li>
      <li><a href="produk.php"<?= navAktif('produk', $halamanAktif) ?>>Produk</a></li>
      <li><a href="cara-pesan.php"<?= navAktif('cara-pesan', $halamanAktif) ?>>Cara Pesan</a></li>
      <li><a href="diskon.php"<?= navAktif('diskon', $halamanAktif) ?>>Diskon</a></li>
      <?php if ($userLogin && $userLogin['role'] === 'pembeli'): ?>
        <li><a href="pesanan-saya.php"<?= navAktif('pesanan-saya', $halamanAktif) ?>>Pesanan Saya</a></li>
      <?php endif; ?>
      <li><a href="profil.php"<?= navAktif('profil', $halamanAktif) ?>>Profil</a></li>
    </ul>
    <div class="nav-user">
      <?php if ($userLogin && $userLogin['role'] === 'penjual'): ?>
        <span class="nav-hi">Hai, <?= htmlspecialchars($userLogin['nama']) ?> (Penjual)</span>
        <a href="seller/dashboard.php">Seller Centre</a>
        <a href="auth/logout.php">Logout</a>
      <?php elseif ($userLogin): ?>
        <span class="nav-hi">Hai, <?= htmlspecialchars($userLogin['nama']) ?></span>
        <a href="auth/logout.php">Logout</a>
      <?php else: ?>
        <a href="auth/login.php">Login</a>
        <a href="auth/register.php">Daftar</a>
      <?php endif; ?>
    </div>
    <a href="keranjang.php" class="cart-badge" id="cartBadge">
      🛒
      <span class="badge-count" id="badgeCount">0</span>
    </a>
    <button class="hamburger" id="hamburger" onclick="toggleNav()" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>
