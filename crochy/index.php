<?php
require "partials/bootstrap.php";
$pageTitle = "Beranda";
$halamanAktif = "beranda";
require "partials/head.php";
?>

<!-- ═══ HERO ═══ -->
<section id="beranda">
  <div class="yarn-ring"></div>
  <div class="yarn-ring"></div>
  <div class="yarn-ring"></div>
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>

  <div class="hero-inner">
    <div class="hero-left">
      <h1 class="hero-headline">
        "Crochet is a great way<br>to <em>relax</em> and be <em>creative</em>"
      </h1>
      <p class="hero-sub">Temukan berbagai karakter unik dalam bentuk boneka dan gantungan kunci rajut yang dirancang untuk membawa senyum dan keceriaan bagi pemiliknya.</p>
      <div class="hero-cta">
        <div class="hero-btns">
          <a href="produk.php" class="btn-fill">Lihat Produk</a>
          <a href="cara-pesan.php" class="btn-ghost">Cara Pesan</a>
        </div>
      </div>
    </div>
    <div class="hero-right">
      <div class="hero-glow"></div>
      <div class="hero-photo-stage">
        <?php foreach (['kiri_atas','kanan_atas','kiri_bawah','kanan_bawah','utama'] as $posisi):
          $foto = $heroFoto[$posisi] ?? null;
          $src = $foto ? "assets/hero/" . htmlspecialchars($foto['gambar']) : "https://placehold.co/300x300/8B4513/F8E8D8?text=Crochy";
          $kelasPosisi = 'hero-photo-' . str_replace('_', '-', $posisi);
        ?>
        <div class="hero-photo <?= $kelasPosisi ?>">
          <img src="<?= $src ?>" alt="Foto Crochy">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- STRIP -->
<div class="strip">
  <div class="strip-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
    100% Handmade
  </div>
  <div class="strip-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
    Bahan Premium
  </div>
  <div class="strip-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
    Custom Order
  </div>
  <div class="strip-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    Pengiriman ke Seluruh Indonesia
  </div>
</div>

<!-- ═══ PRODUK UNGGULAN (PREVIEW) ═══ -->
<section id="produk">
  <div class="section-header">
    <p class="section-eyebrow">Koleksi Kami</p>
    <h2 class="section-title">Produk <em>Unggulan</em></h2>
    <div class="section-rule"></div>
  </div>
  <div class="products-grid" id="katalog-produk">
    <?php
      $produkUnggulan = array_slice($semuaProduk, 0, 4);
      if (!empty($produkUnggulan)):
        foreach ($produkUnggulan as $produk):
    ?>
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
    <?php
        endforeach;
      else:
        echo "<p class='teks-kosong'>Belum ada produk.</p>";
      endif;
    ?>
  </div>
  <div style="text-align:center;margin-top:3rem">
    <a href="produk.php" class="btn-fill">Lihat Semua Produk</a>
  </div>
</section>

<?php require "partials/footer.php"; ?>
