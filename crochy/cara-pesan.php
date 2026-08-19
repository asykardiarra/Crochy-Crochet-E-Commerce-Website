<?php
require "partials/bootstrap.php";
$pageTitle = "Cara Pesan";
$halamanAktif = "cara-pesan";
require "partials/head.php";
?>

<!-- ═══ CARA PESAN ═══ -->
<section id="cara-pesan">
  <div class="section-header">
    <p class="section-eyebrow">Mudah &amp; Cepat</p>
    <h2 class="section-title">Cara <em>Memesan</em></h2>
    <div class="section-rule"></div>
  </div>
  <div class="steps-wrap">
    <div class="step"><div class="step-num">1</div><div class="step-title">Pilih Produk</div><div class="step-desc">Lihat koleksi dan tambahkan produk yang kamu inginkan ke keranjang.</div></div>
    <div class="step"><div class="step-num">2</div><div class="step-title">Cek Keranjang &amp; Promo</div><div class="step-desc">Periksa produk di Keranjang. Punya kode promo? Cek halaman Diskon, salin kodenya, lalu masukkan dan klik Terapkan.</div></div>
    <div class="step"><div class="step-num">3</div><div class="step-title">Isi Data Pengiriman</div><div class="step-desc">Login/daftar akun, lalu masukkan nama dan alamat lengkap untuk pengiriman pesanan (otomatis terisi jika sudah login).</div></div>
    <div class="step"><div class="step-num">4</div><div class="step-title">Transfer &amp; Unggah Bukti</div><div class="step-desc">Transfer sesuai total tagihan ke rekening toko, lalu unggah screenshot bukti pembayaran.</div></div>
    <div class="step"><div class="step-num">5</div><div class="step-title">Pantau Status Pesanan</div><div class="step-desc">Cek halaman "Pesanan Saya" untuk melihat status: Menunggu Verifikasi → Diproses → Dikirim → Selesai.</div></div>
    <div class="step"><div class="step-num">6</div><div class="step-title">Terima Pesanan</div><div class="step-desc">Pesanan dikemas rapi dan dikirim ke alamatmu. Selamat menikmati!</div></div>
  </div>
  <div class="order-cta">
    <p>Siap memesan atau ada pertanyaan? Yuk mulai dari koleksi produk kami!</p>
    <a href="produk.php" class="btn-fill">Lihat Produk</a>
    <a href="diskon.php" class="btn-ghost">Lihat Promo Aktif</a>
  </div>
</section>

<?php require "partials/footer.php"; ?>
