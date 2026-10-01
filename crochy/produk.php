<?php
require "partials/bootstrap.php";
$pageTitle = "Produk";
$halamanAktif = "produk";
require "partials/head.php";
?>

<!-- ═══ PRODUK ═══ -->
<section id="produk">
  <div class="section-header">
    <p class="section-eyebrow">Koleksi Kami</p>
    <h2 class="section-title">Semua <em>Produk</em></h2>
    <div class="section-rule"></div>
  </div>
  <div class="products-grid" id="katalog-produk">
    <?php if (!empty($semuaProduk)): ?>
      <?php foreach ($semuaProduk as $produk): ?>
      <div class="product-card">
        <div class="product-img">
          <img src="assets/produk/<?= htmlspecialchars($produk['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama']) ?>">
        </div>
        <div class="product-body">
          <div class="product-name"><?= htmlspecialchars($produk['nama']) ?></div>
          <div class="product-desc"><?= htmlspecialchars($produk['deskripsi']) ?></div>
          <div class="product-footer">
            <div class="product-price"><span class="lbl">Harga</span>Rp <?= number_format($produk['harga'], 0, ',', '.') ?></div>
            <button class="btn-pesan" onclick="tambahKeKeranjang(<?= (int)$produk['id'] ?>, this)">Tambah</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="teks-kosong">Belum ada produk.</p>
    <?php endif; ?>
  </div>
</section>

<?php require "partials/footer.php"; ?>
