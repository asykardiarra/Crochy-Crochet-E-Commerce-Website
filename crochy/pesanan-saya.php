<?php
require "partials/bootstrap.php";

if (!$userLogin) {
  header("Location: auth/login.php?redirect=pesanan-saya.php");
  exit;
}
if ($userLogin['role'] !== 'pembeli') {
  header("Location: seller/pesanan.php");
  exit;
}

$sukses = !empty($_GET['sukses']);

$labelStatus = [
  'menunggu_verifikasi' => 'Menunggu Verifikasi',
  'diproses'            => 'Diproses',
  'dikirim'             => 'Dikirim',
  'selesai'             => 'Selesai',
  'ditolak'             => 'Ditolak/Dibatalkan',
];

$daftarPesanan = [];
$stmt = $koneksi->prepare("SELECT * FROM pesanan WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $userLogin['id']);
$stmt->execute();
$hasil = $stmt->get_result();
while ($p = $hasil->fetch_assoc()) {
  $stmtItem = $koneksi->prepare("SELECT * FROM pesanan_item WHERE pesanan_id = ?");
  $stmtItem->bind_param("i", $p['id']);
  $stmtItem->execute();
  $p['items'] = $stmtItem->get_result()->fetch_all(MYSQLI_ASSOC);
  $daftarPesanan[] = $p;
}

$pageTitle = "Pesanan Saya";
$halamanAktif = "pesanan-saya";
require "partials/head.php";
?>

<section id="pesanan-saya">
  <div class="section-header">
    <p class="section-eyebrow">Riwayat Transaksi</p>
    <h2 class="section-title">Pesanan <em>Saya</em></h2>
    <div class="section-rule"></div>
  </div>

  <?php if ($sukses): ?>
    <div class="auth-msg ok" style="max-width:800px;margin:0 auto 1.5rem">
      🎉 Pesanan berhasil dibuat! Kami akan memverifikasi bukti pembayaranmu secepatnya.
    </div>
  <?php endif; ?>

  <div class="pesanan-list">
    <?php if (empty($daftarPesanan)): ?>
      <p class="teks-kosong">Kamu belum pernah memesan. Yuk lihat koleksi produk kami! 🧶</p>
    <?php else: foreach ($daftarPesanan as $p): ?>
      <div class="admin-card pesanan-card">
        <div class="pesanan-card-head">
          <div>
            <strong>Pesanan #<?= (int)$p['id'] ?></strong>
            <small style="display:block;color:var(--warm-800)"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?> WIB</small>
          </div>
          <span class="status-badge status-<?= htmlspecialchars($p['status']) ?>"><?= htmlspecialchars($labelStatus[$p['status']] ?? $p['status']) ?></span>
        </div>

        <div class="pesanan-card-body">
          <?php foreach ($p['items'] as $it): ?>
            <div class="cart-item">
              <div class="cart-item-info">
                <h4><?= htmlspecialchars($it['nama_produk']) ?> (<?= (int)$it['jumlah'] ?>x)</h4>
                <small>Rp <?= number_format($it['harga_satuan'] * $it['jumlah'], 0, ',', '.') ?></small>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="pesanan-card-foot">
          <div class="pesanan-alamat">
            <span class="label">Dikirim ke:</span> <?= htmlspecialchars($p['nama_penerima']) ?> — <?= htmlspecialchars($p['alamat_pengiriman']) ?>
            <a href="assets/bukti_bayar/<?= htmlspecialchars($p['bukti_bayar']) ?>" target="_blank" class="btn-ghost btn-lihat-bukti">🧾 Lihat Bukti Bayar</a>
          </div>
          <div class="ringkasan-total-wrap" style="text-align:right">
            <div>Subtotal: <span>Rp <?= number_format($p['subtotal'], 0, ',', '.') ?></span></div>
            <?php if ($p['potongan'] > 0): ?>
              <div>Potongan<?= $p['kode_promo'] ? ' (' . htmlspecialchars($p['kode_promo']) . ')' : '' ?>: <span class="potongan-harga">-Rp <?= number_format($p['potongan'], 0, ',', '.') ?></span></div>
            <?php endif; ?>
            <div class="ringkasan-total-final">Total: <span>Rp <?= number_format($p['total'], 0, ',', '.') ?></span></div>
          </div>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
</section>

<?php if ($sukses): ?>
<script>
  document.addEventListener("DOMContentLoaded", function () { bersihkanDataCheckout(); });
</script>
<?php endif; ?>

<?php require "partials/footer.php"; ?>
