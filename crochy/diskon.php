<?php
require "partials/bootstrap.php";
$pageTitle    = "Diskon";
$halamanAktif = "diskon";
require "partials/head.php";
?>

<!-- ═══ DISKON / PROMO AKTIF ═══ -->
<section id="diskon">
  <div class="section-header">
    <p class="section-eyebrow">Promo Spesial</p>
    <h2 class="section-title">Diskon &amp; <em>Promo</em></h2>
    <div class="section-rule"></div>
  </div>

  <div class="diskon-wrap">
    <?php if (empty($daftarDiskon)): ?>
      <p class="teks-kosong">Belum ada promo aktif saat ini. Pantau terus ya! 🧶</p>
    <?php else: ?>
      <?php foreach ($daftarDiskon as $diskon): ?>
      <div class="diskon-card">
        <div class="diskon-persen"><?= (int)$diskon['persen'] ?>%<span>OFF</span></div>
        <div class="diskon-body">
          <h3 class="diskon-judul"><?= htmlspecialchars($diskon['judul']) ?></h3>
          <?php if (!empty($diskon['deskripsi'])): ?>
            <p class="diskon-desc"><?= htmlspecialchars($diskon['deskripsi']) ?></p>
          <?php endif; ?>
          <div class="diskon-footer">
            <div class="diskon-kode" onclick="salinKodePromo(this)" data-kode="<?= htmlspecialchars($diskon['kode_promo']) ?>">
              <span class="kode-label">Kode</span>
              <span class="kode-teks"><?= htmlspecialchars($diskon['kode_promo']) ?></span>
              <span class="kode-icon">📋</span>
            </div>
            <?php if (!empty($diskon['berlaku_sampai'])): ?>
            <div class="diskon-berlaku">Berlaku sampai <?= date('d M Y', strtotime($diskon['berlaku_sampai'])) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<?php require "partials/footer.php"; ?>
