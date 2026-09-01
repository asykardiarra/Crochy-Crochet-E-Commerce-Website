<?php
require "../config.php";
require "includes/auth_check.php";

$totalProduk    = $koneksi->query("SELECT COUNT(*) AS n FROM produk")->fetch_assoc()['n'];
$totalDiskon    = $koneksi->query("SELECT COUNT(*) AS n FROM diskon WHERE aktif = 1")->fetch_assoc()['n'];
$totalCustomer  = $koneksi->query("SELECT COUNT(*) AS n FROM users WHERE role = 'pembeli'")->fetch_assoc()['n'];
$totalPesananBaru = $koneksi->query("SELECT COUNT(*) AS n FROM pesanan WHERE status = 'menunggu_verifikasi'")->fetch_assoc()['n'];
$totalPendapatan  = $koneksi->query("SELECT COALESCE(SUM(total),0) AS n FROM pesanan WHERE status = 'selesai'")->fetch_assoc()['n'];

$activePage = 'dashboard';
$pageTitle  = 'Dashboard';
require "includes/header.php";
?>

<div class="admin-topbar">
  <div class="admin-title">Dashboard</div>
</div>

<div class="admin-form-row">
  <div class="admin-card" style="flex:1;min-width:200px;text-align:center">
    <div style="font-size:2.2rem;font-weight:700;color:var(--warm-700)"><?= (int)$totalProduk ?></div>
    <div style="color:var(--warm-800);font-size:.85rem">Total Produk</div>
  </div>
  <div class="admin-card" style="flex:1;min-width:200px;text-align:center">
    <div style="font-size:2.2rem;font-weight:700;color:var(--warm-700)"><?= (int)$totalDiskon ?></div>
    <div style="color:var(--warm-800);font-size:.85rem">Diskon Aktif</div>
  </div>
  <div class="admin-card" style="flex:1;min-width:200px;text-align:center">
    <div style="font-size:2.2rem;font-weight:700;color:var(--warm-700)"><?= (int)$totalCustomer ?></div>
    <div style="color:var(--warm-800);font-size:.85rem">Customer Terdaftar</div>
  </div>
  <div class="admin-card" style="flex:1;min-width:200px;text-align:center">
    <a href="pesanan.php?filter=menunggu_verifikasi" style="text-decoration:none">
      <div style="font-size:2.2rem;font-weight:700;color:var(--warm-700)"><?= (int)$totalPesananBaru ?></div>
      <div style="color:var(--warm-800);font-size:.85rem">Pesanan Perlu Verifikasi</div>
    </a>
  </div>
  <div class="admin-card" style="flex:1;min-width:200px;text-align:center">
    <div style="font-size:1.6rem;font-weight:700;color:var(--warm-700)">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></div>
    <div style="color:var(--warm-800);font-size:.85rem">Total Pendapatan (Selesai)</div>
  </div>
</div>

<div class="admin-card">
  <p style="color:var(--warm-800);font-size:.9rem;line-height:1.7">
    Selamat datang di Seller Centre Crochy 🧶 Gunakan menu di samping untuk mengelola
    pesanan masuk, melihat laporan penjualan, produk, diskon, foto hero, dan profil toko
    yang ditampilkan di halaman utama.
  </p>
</div>

<?php require "includes/footer.php"; ?>
