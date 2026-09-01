<?php
require "../config.php";
require "includes/auth_check.php";

$rowPendapatan = $koneksi->query("SELECT COALESCE(SUM(total),0) AS total_pendapatan, COUNT(*) AS jumlah_selesai FROM pesanan WHERE status = 'selesai'")->fetch_assoc();
$totalPendapatan = (float)$rowPendapatan['total_pendapatan'];
$jumlahSelesai   = (int)$rowPendapatan['jumlah_selesai'];

$totalPesananMasuk = $koneksi->query("SELECT COUNT(*) AS n FROM pesanan WHERE status != 'ditolak'")->fetch_assoc()['n'];
$totalDitolak      = $koneksi->query("SELECT COUNT(*) AS n FROM pesanan WHERE status = 'ditolak'")->fetch_assoc()['n'];

$rataRataTransaksi = $jumlahSelesai > 0 ? $totalPendapatan / $jumlahSelesai : 0;

$sqlTerlaris = "SELECT pi.nama_produk, pi.produk_id,
                       SUM(pi.jumlah) AS total_jumlah,
                       SUM(pi.jumlah * pi.harga_satuan) AS total_pendapatan_produk,
                       pr.gambar
                FROM pesanan_item pi
                JOIN pesanan p ON p.id = pi.pesanan_id
                LEFT JOIN produk pr ON pr.id = pi.produk_id
                WHERE p.status = 'selesai'
                GROUP BY pi.produk_id, pi.nama_produk, pr.gambar
                ORDER BY total_jumlah DESC
                LIMIT 10";
$daftarTerlaris = $koneksi->query($sqlTerlaris)->fetch_all(MYSQLI_ASSOC);

$activePage = 'laporan';
$pageTitle  = 'Laporan Penjualan';
require "includes/header.php";
?>

<div class="admin-topbar">
  <div class="admin-title">Laporan Penjualan</div>
</div>

<div class="admin-form-row">
  <div class="admin-card" style="flex:1;min-width:200px;text-align:center">
    <div style="font-size:1.7rem;font-weight:700;color:var(--warm-700)">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></div>
    <div style="color:var(--warm-800);font-size:.85rem">Total Pendapatan (Selesai)</div>
  </div>
  <div class="admin-card" style="flex:1;min-width:200px;text-align:center">
    <div style="font-size:2.2rem;font-weight:700;color:var(--warm-700)"><?= $jumlahSelesai ?></div>
    <div style="color:var(--warm-800);font-size:.85rem">Transaksi Selesai</div>
  </div>
  <div class="admin-card" style="flex:1;min-width:200px;text-align:center">
    <div style="font-size:2.2rem;font-weight:700;color:var(--warm-700)"><?= (int)$totalPesananMasuk ?></div>
    <div style="color:var(--warm-800);font-size:.85rem">Total Pesanan Masuk</div>
  </div>
  <div class="admin-card" style="flex:1;min-width:200px;text-align:center">
    <div style="font-size:1.5rem;font-weight:700;color:var(--warm-700)">Rp <?= number_format($rataRataTransaksi, 0, ',', '.') ?></div>
    <div style="color:var(--warm-800);font-size:.85rem">Rata-rata / Transaksi</div>
  </div>
</div>

<div class="admin-card">
  <h3 style="margin-top:0;margin-bottom:.4rem;color:var(--warm-900);font-family:'Cormorant Garamond',serif">🏆 Produk Terlaris</h3>
  <p style="color:var(--warm-800);font-size:.85rem;margin-top:0;margin-bottom:1rem">Dihitung dari pesanan yang berstatus Selesai.</p>
  <table class="admin-table">
    <thead>
      <tr><th>#</th><th>Gambar</th><th>Nama Produk</th><th>Total Terjual</th><th>Total Pendapatan</th></tr>
    </thead>
    <tbody>
      <?php if (empty($daftarTerlaris)): ?>
        <tr><td colspan="5" class="teks-kosong">Belum ada penjualan selesai untuk ditampilkan.</td></tr>
      <?php else: $no = 1; foreach ($daftarTerlaris as $t): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?php if (!empty($t['gambar'])): ?><img src="../assets/produk/<?= htmlspecialchars($t['gambar']) ?>" alt=""><?php else: ?>—<?php endif; ?></td>
          <td><?= htmlspecialchars($t['nama_produk']) ?></td>
          <td><?= (int)$t['total_jumlah'] ?>x</td>
          <td>Rp <?= number_format($t['total_pendapatan_produk'], 0, ',', '.') ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<?php if ($totalDitolak > 0): ?>
<div class="admin-card">
  <p style="color:var(--warm-800);font-size:.85rem;margin:0">ℹ️ Ada <strong><?= (int)$totalDitolak ?></strong> pesanan yang ditolak/dibatalkan dan tidak dihitung dalam laporan ini.</p>
</div>
<?php endif; ?>

<?php require "includes/footer.php"; ?>
