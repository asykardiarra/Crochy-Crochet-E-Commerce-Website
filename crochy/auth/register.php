<?php
require "../config.php";

// ===== Redirect tujuan setelah daftar (dipakai alur checkout) =====
$redirectAman = ['checkout-alamat.php', 'checkout-bayar.php', 'pesanan-saya.php', 'keranjang.php'];
$redirect = $_GET['redirect'] ?? ($_POST['redirect'] ?? '');
if (!in_array($redirect, $redirectAman, true)) {
  $redirect = '';
}

// Kalau sudah login, langsung lempar sesuai role
if (!empty($_SESSION['user_id'])) {
  if ($_SESSION['user_role'] === 'penjual') {
    header("Location: ../seller/dashboard.php");
  } else {
    header("Location: " . ($redirect !== '' ? '../' . $redirect : '../index.php'));
  }
  exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama        = trim($_POST['nama'] ?? '');
  $email       = trim($_POST['email'] ?? '');
  $whatsapp    = trim($_POST['whatsapp'] ?? '');
  $alamat      = trim($_POST['alamat'] ?? '');
  $password    = trim($_POST['password'] ?? '');
  $konfirmasi  = trim($_POST['konfirmasi_password'] ?? '');

  if ($nama === '' || $email === '' || $password === '') {
    $error = "Nama, email, dan password wajib diisi.";
  } elseif (mb_strlen($nama) > 50) {
    $error = "Nama maksimal 50 karakter.";
  } elseif (mb_strlen($email) > 150) {
    $error = "Email maksimal 150 karakter.";
  } elseif ($whatsapp !== '' && mb_strlen($whatsapp) > 15) {
    $error = "Nomor WhatsApp maksimal 15 karakter.";
  } elseif ($alamat !== '' && mb_strlen($alamat) > 100) {
    $error = "Alamat maksimal 100 karakter.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Format email tidak valid.";
  } elseif (strlen($password) < 6) {
    $error = "Password minimal 6 karakter.";
  } elseif (strlen($password) > 72) {
    $error = "Password maksimal 72 karakter.";
  } elseif ($password !== $konfirmasi) {
    $error = "Konfirmasi password tidak sama.";
  } else {
    $cek = $koneksi->prepare("SELECT id FROM users WHERE email = ?");
    $cek->bind_param("s", $email);
    $cek->execute();
    if ($cek->get_result()->num_rows > 0) {
      $error = "Email sudah terdaftar. Coba login.";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      // Registrasi publik SELALU role pembeli. Akun penjual dibuat khusus lewat install_penjual.php.
      $stmt = $koneksi->prepare("INSERT INTO users (nama, email, password, role, whatsapp, alamat) VALUES (?, ?, ?, 'pembeli', ?, ?)");
      $stmt->bind_param("sssss", $nama, $email, $hash, $whatsapp, $alamat);
      if ($stmt->execute()) {
        $_SESSION['user_id']       = $stmt->insert_id;
        $_SESSION['user_nama']     = $nama;
        $_SESSION['user_role']     = 'pembeli';
        $_SESSION['user_whatsapp'] = $whatsapp;
        $_SESSION['user_alamat']   = $alamat;
        header("Location: " . ($redirect !== '' ? '../' . $redirect : '../index.php'));
        exit;
      } else {
        $error = "Gagal mendaftar: " . $koneksi->error;
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daftar Akun - Crochy</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../css/style.css"/>
</head>
<body>
<div class="auth-page">
  <div class="auth-box">
    <a href="../index.php" class="auth-logo">Crochy</a>
    <p class="auth-subtitle">Daftar untuk memesan lebih cepat</p>

    <?php if ($error): ?>
      <div class="auth-msg err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
      <div class="form-group">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" required maxlength="50" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required maxlength="150" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="whatsapp">No. WhatsApp</label>
        <input type="text" id="whatsapp" name="whatsapp" placeholder="Contoh: 6281234567890" maxlength="15" value="<?= htmlspecialchars($_POST['whatsapp'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="alamat">Alamat Pengiriman</label>
        <input type="text" id="alamat" name="alamat" placeholder="Opsional, bisa diisi nanti" maxlength="100" value="<?= htmlspecialchars($_POST['alamat'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="6" maxlength="72">
      </div>
      <div class="form-group">
        <label for="konfirmasi_password">Konfirmasi Password</label>
        <input type="password" id="konfirmasi_password" name="konfirmasi_password" required minlength="6" maxlength="72">
      </div>
      <button type="submit" class="auth-submit">Daftar</button>
    </form>

    <div class="auth-footer">Sudah punya akun? <a href="login.php<?= $redirect !== '' ? '?redirect=' . urlencode($redirect) : '' ?>">Login di sini</a></div>
    <a href="../index.php" class="auth-back" style="color:var(--warm-600)">Kembali ke Beranda</a>
  </div>
</div>
</body>
</html>