<?php
require "partials/bootstrap.php";

if (!$userLogin) {
  header("Location: auth/login.php?redirect=checkout-bayar.php");
  exit;
}
if ($userLogin['role'] !== 'pembeli') {
  header("Location: index.php");
  exit;
}

$error = "";
$folderUpload = "assets/bukti_bayar/";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $namaPenerima     = trim($_POST['nama_penerima'] ?? '');
  $alamatPengiriman = trim($_POST['alamat_pengiriman'] ?? '');
  $kodePromoInput   = strtoupper(trim($_POST['kode_promo'] ?? ''));
  $cartDataRaw      = $_POST['cart_data'] ?? '';

  $cartData = json_decode($cartDataRaw, true);

  if ($namaPenerima === '' || $alamatPengiriman === '') {
    $error = "Nama dan alamat pengiriman wajib diisi.";
  } elseif (mb_strlen($namaPenerima) > 50) {
    $error = "Nama penerima maksimal 50 karakter.";
  } elseif (mb_strlen($alamatPengiriman) > 100) {
    $error = "Alamat pengiriman maksimal 100 karakter.";
  } elseif (!is_array($cartData) || count($cartData) === 0) {
    $error = "Keranjang kosong atau data tidak valid. Silakan ulangi dari halaman Keranjang.";
  } elseif (empty($_FILES['bukti_bayar']['name'])) {
    $error = "Mohon unggah bukti transfer/pembayaran.";
  } else {

    $items = [];
    $subtotal = 0;
    foreach ($cartData as $row) {
      $idProduk = (int)($row['id'] ?? 0);
      $jumlah   = (int)($row['jumlah'] ?? 0);
      if ($idProduk <= 0 || $jumlah <= 0) continue;

      $stmtP = $koneksi->prepare("SELECT id, nama, harga FROM produk WHERE id = ?");
      $stmtP->bind_param("i", $idProduk);
      $stmtP->execute();
      $p = $stmtP->get_result()->fetch_assoc();
      if (!$p) continue;

      $items[] = [
        'produk_id' => $p['id'],
        'nama'      => $p['nama'],
        'harga'     => (float)$p['harga'],
        'jumlah'    => $jumlah,
      ];
      $subtotal += (float)$p['harga'] * $jumlah;
    }

    if (count($items) === 0) {
      $error = "Produk pada keranjang tidak ditemukan. Silakan ulangi dari halaman Keranjang.";
    } else {

      $persenDiskon = 0;
      $kodePromoValid = null;
      if ($kodePromoInput !== '') {
        $stmtD = $koneksi->prepare("SELECT persen, kode_promo FROM diskon WHERE kode_promo = ? AND aktif = 1 AND (berlaku_sampai IS NULL OR berlaku_sampai >= CURDATE())");
        $stmtD->bind_param("s", $kodePromoInput);
        $stmtD->execute();
        $d = $stmtD->get_result()->fetch_assoc();
        if ($d) {
          $persenDiskon = (int)$d['persen'];
          $kodePromoValid = $d['kode_promo'];
        }
      }
      $potongan = round($subtotal * $persenDiskon / 100);
      $total = $subtotal - $potongan;

      $ekstensiOk = ['jpg','jpeg','png','webp'];
      $ekstensi = strtolower(pathinfo($_FILES['bukti_bayar']['name'], PATHINFO_EXTENSION));
      if (!in_array($ekstensi, $ekstensiOk)) {
        $error = "Format bukti bayar harus jpg, jpeg, png, atau webp.";
      } elseif ($_FILES['bukti_bayar']['size'] > 3 * 1024 * 1024) {
        $error = "Ukuran bukti bayar maksimal 3MB.";
      } else {
        $namaBukti = "bukti-" . time() . "-" . rand(1000,9999) . "." . $ekstensi;
        if (!move_uploaded_file($_FILES['bukti_bayar']['tmp_name'], $folderUpload . $namaBukti)) {
          $error = "Gagal mengunggah bukti bayar. Coba lagi.";
        } else {
            
          $koneksi->begin_transaction();
          $gagal = false;

          $stmt = $koneksi->prepare("INSERT INTO pesanan (user_id, nama_penerima, alamat_pengiriman, kode_promo, persen_diskon, subtotal, potongan, total, bukti_bayar, status) VALUES (?,?,?,?,?,?,?,?,?, 'menunggu_verifikasi')");
          $stmt->bind_param("isssiddds", $userLogin['id'], $namaPenerima, $alamatPengiriman, $kodePromoValid, $persenDiskon, $subtotal, $potongan, $total, $namaBukti);
          if (!$stmt->execute()) {
            $gagal = true;
          } else {
            $pesananId = $stmt->insert_id;
            $stmtItem = $koneksi->prepare("INSERT INTO pesanan_item (pesanan_id, produk_id, nama_produk, harga_satuan, jumlah) VALUES (?,?,?,?,?)");
            foreach ($items as $it) {
              $stmtItem->bind_param("iisdi", $pesananId, $it['produk_id'], $it['nama'], $it['harga'], $it['jumlah']);
              if (!$stmtItem->execute()) { $gagal = true; break; }
            }
          }

          if ($gagal) {
            $koneksi->rollback();
            if (file_exists($folderUpload . $namaBukti)) @unlink($folderUpload . $namaBukti);
            $error = "Gagal menyimpan pesanan: " . $koneksi->error;
          } else {
            $koneksi->commit();
            header("Location: pesanan-saya.php?sukses=1");
            exit;
          }
        }
      }
    }
  }
}

$pageTitle = "Pembayaran";
$halamanAktif = "keranjang";
require "partials/head.php";
?>

<section id="checkout-bayar">
  <div class="section-header">
    <p class="section-eyebrow">Langkah 3 dari 3</p>
    <h2 class="section-title">Konfirmasi <em>Pembayaran</em></h2>
    <div class="section-rule"></div>
  </div>

  <div class="checkout-steps">
    <div class="checkout-step done"><span class="checkout-step-num">✓</span> Keranjang &amp; Promo</div>
    <div class="checkout-step done"><span class="checkout-step-num">✓</span> Data Pengiriman</div>
    <div class="checkout-step active"><span class="checkout-step-num">3</span> Pembayaran</div>
  </div>

  <?php if ($error): ?><div class="auth-msg err" style="max-width:800px;margin:0 auto 1.2rem"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <div class="admin-card" style="max-width:800px;margin:0 auto 1.5rem">
    <h3 style="margin-top:0;color:var(--warm-900);font-family:'Cormorant Garamond',serif;font-size:1.15rem">Ringkasan Pesanan</h3>
    <div id="ringkasan-bayar"></div>
  </div>

  <div class="admin-card rekening-box" style="max-width:800px;margin:0 auto 1.5rem">
    <h3 style="margin-top:0;color:var(--warm-900);font-family:'Cormorant Garamond',serif;font-size:1.15rem">💳 Transfer ke Rekening Berikut</h3>
    <?php if (!empty($profil['no_rekening'])): ?>
      <div class="rekening-item"><span class="label">Bank / E-Wallet</span><span class="value"><?= htmlspecialchars($profil['nama_bank'] ?? '') ?></span></div>
      <div class="rekening-item"><span class="label">No. Rekening</span><span class="value rekening-nomor"><?= htmlspecialchars($profil['no_rekening']) ?></span></div>
      <div class="rekening-item"><span class="label">Atas Nama</span><span class="value"><?= htmlspecialchars($profil['atas_nama'] ?? '') ?></span></div>
      <p class="rekening-catatan">Transfer sesuai nominal Total di atas, lalu unggah screenshot bukti transfer pada form di bawah ini.</p>
    <?php else: ?>
      <p class="rekening-catatan">Info rekening toko belum diatur oleh penjual. Silakan hubungi toko sebelum melakukan transfer.</p>
    <?php endif; ?>
  </div>

  <div class="admin-card" style="max-width:800px;margin:0 auto 1.8rem">
    <form method="post" enctype="multipart/form-data" id="form-bayar" onsubmit="return siapkanFormBayar(this)">
      <input type="hidden" name="nama_penerima">
      <input type="hidden" name="alamat_pengiriman">
      <input type="hidden" name="cart_data">
      <input type="hidden" name="kode_promo">

      <div class="form-group">
        <label for="bukti-bayar-input">Unggah Bukti Transfer / Screenshot Pembayaran <span class="form-required">*</span></label>
        <input type="file" id="bukti-bayar-input" name="bukti_bayar" accept=".jpg,.jpeg,.png,.webp" required>
      </div>

      <div class="keranjang-summary" style="text-align:center;border-top:none;padding-top:0">
        <a href="checkout-alamat.php" class="btn-ghost">Kembali</a>
        <button type="submit" class="btn-pesan btn-checkout">Konfirmasi Pembayaran</button>
      </div>
    </form>
  </div>

</section>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    if (keranjangBelanja.length === 0) {
      window.location.href = "keranjang.php";
      return;
    }
    if (!localStorage.getItem("crochy_checkout_alamat")) {
      window.location.href = "checkout-alamat.php";
      return;
    }
    renderRingkasanPesanan("ringkasan-bayar");
  });
</script>

<?php require "partials/footer.php"; ?>
