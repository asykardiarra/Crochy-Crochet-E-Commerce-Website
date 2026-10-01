<footer>
  <div class="footer-logo">Crochy</div>
  <div class="footer-credit">Handmade by <a href="https://www.instagram.com/p1tsky.crochy/" target="_blank" rel="noopener">@p1tsky.crochy</a></div>
  <ul class="footer-links">
    <li><a href="index.php">Beranda</a></li>
    <li><a href="produk.php">Produk</a></li>
    <li><a href="cara-pesan.php">Cara Pesan</a></li>
    <li><a href="keranjang.php">Diskon</a></li>
    <li><a href="diskon.php">Pesanan Saya</a></li>
    <li><a href="profil.php">Profil</a></li>
  </ul>
  <hr class="footer-hr"/>
  <div class="footer-copy">© 2026</div>
</footer>

<button id="back-top" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Kembali ke atas">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 15l-6-6-6 6"/></svg>
</button>

<div id="toast-container"></div>

<script>
  const produkCrochy = <?php
    $dataProdukJs = [];
    foreach ($semuaProduk as $row) {
      $r = $row;
      $r['id'] = (int)$r['id'];
      $r['harga'] = (int)$r['harga'];
      $r['gambar'] = "assets/produk/" . $r['gambar'];
      $dataProdukJs[] = $r;
    }
    echo json_encode($dataProdukJs, JSON_UNESCAPED_SLASHES);
  ?>;

  const daftarDiskonAktif = <?php
    $dataDiskonJs = [];
    foreach ($daftarDiskon as $d) {
      $dataDiskonJs[] = [
        'kode'   => $d['kode_promo'],
        'persen' => (int)$d['persen']
      ];
    }
    echo json_encode($dataDiskonJs, JSON_UNESCAPED_SLASHES);
  ?>;

  const customerLogin = <?= $customerLogin ? json_encode($customerLogin, JSON_UNESCAPED_SLASHES) : 'null' ?>;
  const userLoginSaatIni = <?= $userLogin ? json_encode($userLogin, JSON_UNESCAPED_SLASHES) : 'null' ?>;
</script>
<script src="js/main.js"></script>
</body>
</html>
