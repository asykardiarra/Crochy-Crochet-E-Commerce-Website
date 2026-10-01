<?php
require "partials/bootstrap.php";

if (!$userLogin) {
  header("Location: auth/login.php?redirect=checkout-alamat.php");
  exit;
}
if ($userLogin['role'] !== 'pembeli') {
  header("Location: index.php");
  exit;
}

$pageTitle = "Data Pengiriman";
$halamanAktif = "keranjang";
require "partials/head.php";
?>

<section id="checkout-alamat">
  <div class="section-header">
    <p class="section-eyebrow">Langkah 2 dari 3</p>
    <h2 class="section-title">Data <em>Pengiriman</em></h2>
    <div class="section-rule"></div>
  </div>

  <div class="checkout-steps">
    <div class="checkout-step done"><span class="checkout-step-num">✓</span> Keranjang &amp; Promo</div>
    <div class="checkout-step active"><span class="checkout-step-num">2</span> Data Pengiriman</div>
    <div class="checkout-step"><span class="checkout-step-num">3</span> Pembayaran</div>
  </div>

  <div class="form-pengiriman">
    <div class="form-group">
      <label for="nama-penerima">Nama Lengkap <span class="form-required">*</span></label>
      <input type="text" id="nama-penerima" placeholder="Contoh: Sarah Amelia" required maxlength="50" value="<?= htmlspecialchars($userLogin['nama'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label for="alamat-pengiriman">Alamat Pengiriman <span class="form-required">*</span></label>
      <textarea id="alamat-pengiriman" placeholder="Contoh: Jl. Rajut Bahagia No. 88, Bandung, Jawa Barat 40123" required maxlength="100"><?= htmlspecialchars($userLogin['alamat'] ?? '') ?></textarea>
    </div>
  </div>

  <div class="admin-card" style="max-width:800px;margin:0 auto 1.8rem">
    <h3 style="margin-top:0;color:var(--warm-900);font-family:'Cormorant Garamond',serif;font-size:1.15rem">Ringkasan Pesanan</h3>
    <div id="ringkasan-alamat"></div>
  </div>

  <div class="keranjang-summary" style="max-width:800px;margin:0 auto;text-align:center;border-top:none;padding-top:0">
    <a href="keranjang.php" class="btn-ghost">Kembali ke Keranjang</a>
    <button onclick="simpanAlamatLanjutBayar()" class="btn-pesan btn-checkout">Lanjut ke Pembayaran</button>
  </div>

</section>

<script>

  document.addEventListener("DOMContentLoaded", function () {
    if (keranjangBelanja.length === 0) {
      window.location.href = "keranjang.php";
      return;
    }
    renderRingkasanPesanan("ringkasan-alamat");
  });
</script>

<?php require "partials/footer.php"; ?>