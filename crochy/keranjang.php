<?php
require "partials/bootstrap.php";
$pageTitle = "Keranjang";
$halamanAktif = "keranjang";
require "partials/head.php";
?>

<section id="keranjang">
  <div class="section-header">
    <p class="section-eyebrow">Pesanan Anda</p>
    <h2 class="section-title">Keranjang <em>Belanja</em></h2>
    <div class="section-rule"></div>
  </div>

  <div class="checkout-steps">
    <div class="checkout-step active"><span class="checkout-step-num">1</span> Keranjang &amp; Promo</div>
    <div class="checkout-step"><span class="checkout-step-num">2</span> Data Pengiriman</div>
    <div class="checkout-step"><span class="checkout-step-num">3</span> Pembayaran</div>
  </div>

  <div class="form-pengiriman">

    <div class="form-group" id="promo-form-group">
      <label for="kode-promo-input">Kode Promo (opsional)</label>
      <div id="promo-input-row" class="promo-input-row">
        <input type="text" id="kode-promo-input" placeholder="Masukkan kode promo, contoh: AKHIRBULAN" onkeydown="if(event.key==='Enter'){event.preventDefault();terapkanKodePromo();}">
        <button type="button" onclick="terapkanKodePromo()" class="btn-ghost btn-terapkan-promo">Terapkan</button>
      </div>
      <div id="promo-chip-wrap" class="promo-chip-wrap"></div>
      <small id="status-promo" class="status-promo"></small>
    </div>

  </div>

  <div id="isi-keranjang" class="keranjang-isi-wrap">
    <p class="teks-kosong">Keranjang masih kosong 🧶</p>
  </div>

  <div class="keranjang-summary">
    <div id="rincian-diskon" class="rincian-diskon">
      <div>Subtotal: <span id="subtotal-harga">Rp 0</span></div>
      <div>Potongan Diskon: <span id="potongan-harga" class="potongan-harga">-Rp 0</span></div>
    </div>
    <h3 class="total-harga-heading">
      Total: <span id="total-harga" class="total-harga-value">Rp 0</span>
    </h3>
    <button onclick="lanjutKeCheckout()" class="btn-pesan btn-checkout">
      CHECKOUT
    </button>
  </div>

</section>

<?php require "partials/footer.php"; ?>
